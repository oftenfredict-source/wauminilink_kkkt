<?php
// File: app/Http/Controllers/MemberController.php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Child;
use App\Models\DeletedMember;
use App\Models\User;
use App\Services\SmsService;
use App\Services\SettingsService;
use App\Services\ZKTecoService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function create()
    {
        // Check permission
        if (!auth()->user()->hasPermission('members.create') && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to add members.');
        }

        // Get campuses for selection - only active campuses
        $query = \App\Models\Campus::where('is_active', true);

        // If user has a campus, filter by it
        if (auth()->check() && auth()->user()->campus_id) {
            $userCampus = auth()->user()->campus;
            if ($userCampus) {
                if ($userCampus->is_main_campus) {
                    // Main campus can see itself and all sub campuses
                    $campusIds = [$userCampus->id];
                    $campusIds = array_merge($campusIds, $userCampus->subCampuses->pluck('id')->toArray());
                    $query->whereIn('id', $campusIds);
                } else {
                    // Sub campus can only see itself
                    $query->where('id', $userCampus->id);
                }
            }
        }

        $campuses = $query->orderBy('is_main_campus', 'desc')
            ->orderBy('name')
            ->get();

        return view('members.add-members', compact('campuses'));
    }

    public function nextId()
    {
        return response()->json(['next_id' => Member::generateMemberId()]);
    }

    public function store(Request $request)
    {
        // Debug: Log received data
        \Log::info('=== MEMBER STORE METHOD CALLED ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->url());
        \Log::info('Received member data:', $request->all());
        \Log::info('CSRF Token: ' . $request->input('_token'));
        \Log::info('Request headers:', $request->headers->all());
        
        // Also log to browser console if possible
        if ($request->wantsJson()) {
            \Log::info('JSON request received');
        } else {
            \Log::info('Form request received');
        }

        $childMaxAge = config('membership.child_max_age', 18);
        
        // Validation rules
        $rules = [
            'member_type' => ['nullable','required_if:membership_type,permanent', Rule::in(['father','mother','independent'])],
            'membership_type' => ['required', Rule::in(['permanent','temporary'])],

            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => ['required','string','max:20','regex:/^\+255[0-9]{9,15}$/', Rule::unique('members', 'phone_number')],
            'date_of_birth' => 'required|date|before:today',
            'gender' => ['required', Rule::in(['male','female'])],

            'education_level' => ['nullable', Rule::in(['primary','secondary','high_level','certificate','diploma','bachelor_degree','masters','phd','professor','not_studied'])],
            'profession' => 'required|string|max:100',

            // Guardian for temporary members and independent persons
            'guardian_name' => 'nullable|required_if:membership_type,temporary|string|max:255',
            'guardian_phone' => ['nullable','required_if:membership_type,temporary','string','max:20','regex:/^\+255[0-9]{9,15}$/'],
            'guardian_relationship' => 'nullable|required_if:membership_type,temporary|string|max:100',

            // Children
            'children_count' => 'nullable|integer|min:0|max:4',
            'children' => 'nullable|array',
            'children.*.full_name' => 'required_with:children|string|max:255',
            'children.*.gender' => ['required_with:children', Rule::in(['male','female'])],
            'children.*.date_of_birth' => 'required_with:children|date|before_or_equal:today',

            // Address fields - make required
            'nida_number' => 'nullable|string|max:20',
            'tribe' => 'required|string|max:100',
            'other_tribe' => 'nullable|required_if:tribe,Other|string|max:100',
            'region' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'address' => 'required|string',

            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Marital status and spouse info fields
            'marital_status' => ['nullable', Rule::in(['married','divorced','widowed','separated'])],
            'spouse_full_name' => 'nullable|required_if:marital_status,married|string|max:255',
            'spouse_date_of_birth' => 'nullable|required_if:marital_status,married|date|before:today',
            'spouse_education_level' => ['nullable','required_if:marital_status,married', Rule::in(['primary','secondary','high_level','certificate','diploma','bachelor_degree','masters','phd','professor','not_studied'])],
            'spouse_profession' => 'nullable|required_if:marital_status,married|string|max:100',
            'spouse_nida_number' => 'nullable|string|max:20',
            'spouse_email' => 'nullable|email|max:255',
            'spouse_phone_number' => ['nullable','required_if:marital_status,married','string','max:20','regex:/^\+255[0-9]{9,15}$/'],
            // spouse_gender is automatically determined based on member_type (father -> female, mother -> male)
            'spouse_tribe' => 'nullable|required_if:marital_status,married|string|max:100',
            'spouse_other_tribe' => 'nullable|required_if:spouse_tribe,Other|string|max:100',
            'spouse_church_member' => ['nullable','required_if:marital_status,married', Rule::in(['yes','no'])],
        ];
        // Custom validation for independent persons
        if ($request->member_type === 'independent' && $request->membership_type === 'permanent') {
            $rules['guardian_name'] = 'required|string|max:255';
            $rules['guardian_phone'] = ['required','string','max:20','regex:/^\+255[0-9]{9,15}$/'];
            $rules['guardian_relationship'] = 'required|string|max:100';
        }

        // Custom validation messages
        $messages = [
            'full_name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone_number.required' => 'Phone number is required.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.before' => 'Date of birth cannot be today or in the future.',
            'gender.required' => 'Gender is required.',
            'tribe.required' => 'Tribe is required.',
            'region.required' => 'Region is required.',
            'district.required' => 'District is required.',
            'ward.required' => 'Ward is required.',
            'street.required' => 'Street is required.',
            'address.required' => 'Address is required.',
            'other_tribe.required_if' => 'Please specify the tribe name.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file.',
            'avatar.max' => 'Avatar size must not exceed 2MB.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function($v) use ($request, $childMaxAge) {
            $count = (int) $request->input('children_count', 0);
            if ($request->input('membership_type') === 'permanent') {
                if ($count > 0) {
                    $children = $request->input('children', []);
                    for ($i = 0; $i < $count; $i++) {
                        if (!isset($children[$i]['full_name']) || trim($children[$i]['full_name']) === '') {
                            $v->errors()->add("children.$i.full_name", 'Child full name is required.');
                        }
                        if (!isset($children[$i]['gender']) || !in_array($children[$i]['gender'], ['male','female'])) {
                            $v->errors()->add("children.$i.gender", 'Child gender is required.');
                        }
                        if (!isset($children[$i]['date_of_birth']) || empty($children[$i]['date_of_birth'])) {
                            $v->errors()->add("children.$i.date_of_birth", 'Child date of birth is required.');
                        } else {
                            try {
                                $age = Carbon::parse($children[$i]['date_of_birth'])->age;
                                if ($age > $childMaxAge) {
                                    $v->errors()->add("children.$i.date_of_birth", "Child age exceeds maximum ({$childMaxAge}).");
                                }
                            } catch (\Exception $e) {
                                $v->errors()->add("children.$i.date_of_birth", 'Invalid child date of birth.');
                            }
                        }
                    }
                }
            }
        });

        if ($validator->fails()) {
            \Log::info('=== VALIDATION FAILED ===');
            \Log::info('Validation errors:', $validator->errors()->toArray());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }
        
        \Log::info('=== VALIDATION PASSED ===');

        try {
            // Handle profile picture upload
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                
                // Validate file type
                if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid file type. Please upload a JPG or PNG image.',
                        'errors' => ['profile_picture' => ['Invalid file type']]
                    ], 422);
                }
                
                // Validate file size (2MB max)
                if ($file->getSize() > 2 * 1024 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File too large. Please upload an image smaller than 2MB.',
                        'errors' => ['profile_picture' => ['File too large']]
                    ], 422);
                }
                
                // Save to storage/app/public/member/profile-pictures/ using Laravel Storage
                $profilePicturePath = $file->store('member/profile-pictures', 'public');
            }

            // Handle spouse profile picture upload
            $spouseProfilePicturePath = null;
            if ($request->hasFile('spouse_profile_picture')) {
                $file = $request->file('spouse_profile_picture');
                
                // Validate file type
                if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid file type for spouse photo. Please upload a JPG or PNG image.',
                        'errors' => ['spouse_profile_picture' => ['Invalid file type']]
                    ], 422);
                }
                
                // Validate file size (2MB max)
                if ($file->getSize() > 2 * 1024 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Spouse photo file too large. Please upload an image smaller than 2MB.',
                        'errors' => ['spouse_profile_picture' => ['File too large']]
                    ], 422);
                }
                
                // Save to storage/app/public/member/profile-pictures/ using Laravel Storage
                $spouseProfilePicturePath = $file->store('member/profile-pictures', 'public');
            }

            // Generate unique member ID
            $memberId = Member::generateMemberId();

            // Validate that the user is allowed to add to this campus
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus) {
                // If main campus, they can add to any sub-campus
                if ($userCampus->is_main_campus) {
                    $allowedIds = [$userCampus->id];
                    $allowedIds = array_merge($allowedIds, $userCampus->subCampuses->pluck('id')->toArray());
                    
                    if (!in_array($request->campus_id, $allowedIds)) {
                        return redirect()->back()->with('error', 'You are not authorized to add members to this branch.')->withInput();
                    }
                } 
                // If not main campus, can ONLY add to their own campus
                else if ($request->campus_id != $userCampus->id) {
                    return redirect()->back()->with('error', 'You can only add members to your assigned branch.')->withInput();
                }
            }
        }

        // Create member
            \Log::info('=== CREATING MEMBER ===');
            \Log::info('Member ID to be created: ' . $memberId);
            
            // Determine campus_id - CRITICAL: Ensure members go to correct branch
            $userCampus = auth()->user()->getCampus();
            $campusId = null;
            
            if ($userCampus && !$userCampus->is_main_campus) {
                // Branch user - MUST register to their branch only
                $campusId = $userCampus->id;
                // Override any campus_id from request to prevent wrong branch assignment
                \Log::info('Branch user registering member', [
                    'user_campus_id' => $userCampus->id,
                    'user_campus_name' => $userCampus->name,
                    'requested_campus_id' => $request->campus_id,
                    'final_campus_id' => $campusId
                ]);
            } elseif ($userCampus && $userCampus->is_main_campus && $request->filled('campus_id')) {
                // Usharika admin - can select branch
                $campusId = $request->campus_id;
            } elseif ($userCampus && $userCampus->is_main_campus) {
                // Usharika admin - default to main campus if not specified
                $campusId = $userCampus->id;
            } elseif (auth()->check() && auth()->user()->member && auth()->user()->member->campus_id) {
                // Fallback: Use member's campus
                $campusId = auth()->user()->member->campus_id;
            } else {
                // Final fallback: Get main campus
                $mainCampus = \App\Models\Campus::where('is_main_campus', true)->first();
                if ($mainCampus) {
                    $campusId = $mainCampus->id;
                }
            }
            
            // Validation: Ensure campus_id is set
            if (!$campusId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot determine branch. Please contact administrator.',
                    'errors' => ['campus_id' => ['Branch assignment failed']]
                ], 422);
            }

            $memberData = [
                'member_id' => $memberId,
                'campus_id' => $campusId,
                'community_id' => $request->community_id, // Community assignment
                // biometric_enroll_id will be filled after successful device registration
                'member_type' => $request->member_type,
                'membership_type' => $request->membership_type,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'education_level' => $request->education_level,
                'profession' => $request->profession,
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'guardian_relationship' => $request->guardian_relationship,
                'nida_number' => $request->nida_number,
                'tribe' => $request->tribe,
                'other_tribe' => $request->other_tribe,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'street' => $request->street,
                'address' => $request->address,
                'residence_region' => $request->residence_region,
                'residence_district' => $request->residence_district,
                'residence_ward' => $request->residence_ward,
                'residence_street' => $request->residence_street,
                'residence_road' => $request->residence_road,
                'residence_house_number' => $request->residence_house_number,
                'profile_picture' => $profilePicturePath,
                'marital_status' => $request->marital_status,
                'wedding_date' => $request->wedding_date,
                'spouse_full_name' => $request->spouse_full_name,
                'spouse_date_of_birth' => $request->spouse_date_of_birth,
                'spouse_education_level' => $request->spouse_education_level,
                'spouse_profession' => $request->spouse_profession,
                'spouse_nida_number' => $request->spouse_nida_number,
                'spouse_email' => $request->spouse_email,
                'spouse_phone_number' => $request->spouse_phone_number,
                'spouse_tribe' => $request->spouse_tribe,
                'spouse_other_tribe' => $request->spouse_other_tribe,
                // spouse_gender is automatically determined when creating spouse member
                'spouse_church_member' => $request->spouse_church_member,
            ];
            
            \Log::info('Member data to be saved:', $memberData);
            
            $member = Member::create($memberData);
            
            \Log::info('=== MEMBER CREATED SUCCESSFULLY ===');
            \Log::info('Created member ID: ' . $member->id);
            \Log::info('Created member data:', $member->toArray());

            // NOTE: Biometric registration will happen AFTER spouse and children are created
            // This ensures all family members are registered at once

            // Create User account for member
            try {
                // Extract lastname from full_name (assuming last word is lastname)
                $nameParts = explode(' ', trim($member->full_name));
                $lastname = !empty($nameParts) ? strtoupper(end($nameParts)) : 'MEMBER';
                
                // Create user account with member_id as username (email) and lastname as password
                $user = \App\Models\User::create([
                    'name' => $member->full_name,
                    'email' => $member->member_id, // Use member_id as username/email
                    'password' => \Hash::make($lastname), // Password is lastname in uppercase
                    'role' => 'member',
                    'member_id' => $member->id,
                    'phone_number' => $member->phone_number,
                    'campus_id' => $member->campus_id, // Assign same campus as member
                ]);
                
                \Log::info('User account created for member', [
                    'member_id' => $member->id,
                    'user_id' => $user->id,
                    'username' => $member->member_id,
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create user account for member', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue even if user creation fails - member is already created
            }

            // Children creation with age check
            $children = [];
            if ($request->filled('children')) {
                foreach ($request->children as $childData) {
                    $age = Carbon::parse($childData['date_of_birth'])->age;
                    if ($age > $childMaxAge) {
                        return response()->json([
                            'success' => false,
                            'message' => "Child age exceeds maximum ({$childMaxAge}).",
                            'errors' => ['children' => ["Child age exceeds maximum ({$childMaxAge})."]]
                        ], 422);
                    }
                    $children[] = new Child([
                        'full_name' => $childData['full_name'],
                        'gender' => $childData['gender'],
                        'date_of_birth' => $childData['date_of_birth'],
                    ]);
                }
                if (!empty($children)) {
                    $member->children()->saveMany($children);
                }
            }

            // Send welcome SMS (non-blocking best-effort) with diagnostic logging
            $smsSent = false;
            $smsError = null;
            try {
                $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                if (!$smsEnabled) {
                    $smsError = 'SMS notifications are disabled in system settings';
                    Log::info('Welcome SMS skipped: SMS notifications disabled', [
                        'member_id' => $member->id,
                        'phone' => $member->phone_number ?? 'N/A'
                    ]);
                } elseif (empty($member->phone_number)) {
                    $smsError = 'Member has no phone number';
                    Log::info('Welcome SMS skipped: No phone number', [
                        'member_id' => $member->id
                    ]);
                } else {
                    $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
                    
                    // Get username and password for SMS
                    $username = $member->member_id;
                    $nameParts = explode(' ', trim($member->full_name));
                    $password = !empty($nameParts) ? strtoupper(end($nameParts)) : 'MEMBER';
                    
                    $message = "Umesajiliwa kikamilifu kwenye mfumo wa Kanisa la {$churchName}.\n\n";
                    $message .= "Unaweza kuingia kwenye akaunti yako kwa kutumia:\n";
                    $message .= "Username: {$username}\n";
                    $message .= "Password: {$password}\n\n";
                    $message .= "Unaweza kupokea taarifa za ibada, matukio, na huduma kwa njia ya SMS. Karibu sana!";
                    
                    $smsService = app(SmsService::class);
                    $resp = $smsService->sendDebug($member->phone_number, $message);
                    $smsSent = $resp['ok'] ?? false;
                    $smsError = $resp['reason'] ?? ($resp['error'] ?? null);
                    
                    if ($smsSent) {
                        Log::info('Welcome SMS sent successfully', [
                            'member_id' => $member->id,
                            'phone' => $member->phone_number,
                            'response' => $resp
                        ]);
                    } else {
                        Log::warning('Welcome SMS failed', [
                            'member_id' => $member->id,
                            'phone' => $member->phone_number,
                            'error' => $smsError,
                            'response' => $resp
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                $smsError = $e->getMessage();
                Log::error('Welcome SMS exception', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Create spouse as separate member if they are a church member
            $spouseMember = null;
            if ($member->marital_status === 'married' && 
                $member->spouse_church_member === 'yes' && 
                !empty($member->spouse_full_name) && 
                !empty($member->spouse_phone_number)) {
                
                try {
                    // Determine spouse gender based on main member type
                    $spouseGender = ($member->member_type === 'father') ? 'female' : 'male';
                    
                    // Create spouse member data
                    $spouseMemberData = [
                        'member_id' => Member::generateMemberId(),
                        'member_type' => 'independent', // Spouse is independent member
                        'membership_type' => 'permanent',
                        'campus_id' => $member->campus_id, // Assign same campus as main member
                        'full_name' => $member->spouse_full_name,
                        'email' => $member->spouse_email,
                        'phone_number' => $member->spouse_phone_number,
                        'date_of_birth' => $member->spouse_date_of_birth,
                        'gender' => $spouseGender,
                        'education_level' => $member->spouse_education_level,
                        'profession' => $member->spouse_profession,
                        'nida_number' => $member->spouse_nida_number,
                        'tribe' => $member->spouse_tribe,
                        'other_tribe' => $member->spouse_other_tribe,
                        'region' => $member->region,
                        'district' => $member->district,
                        'ward' => $member->ward,
                        'street' => $member->street,
                        'address' => $member->address,
                        'residence_region' => $member->residence_region,
                        'residence_district' => $member->residence_district,
                        'residence_ward' => $member->residence_ward,
                        'residence_street' => $member->residence_street,
                        'residence_road' => $member->residence_road,
                        'residence_house_number' => $member->residence_house_number,
                        'profile_picture' => $spouseProfilePicturePath,
                        'marital_status' => 'married', // Spouse is also married
                        'wedding_date' => $member->wedding_date, // Sync wedding date
                        'spouse_member_id' => $member->id, // Link to main member
                    ];
                    
                    $spouseMember = Member::create($spouseMemberData);
                    
                    // Update main member to link to spouse member
                    $member->update(['spouse_member_id' => $spouseMember->id]);
                    
                    \Log::info('Spouse member created successfully', [
                        'main_member_id' => $member->id,
                        'spouse_member_id' => $spouseMember->id,
                        'spouse_name' => $spouseMember->full_name,
                        'spouse_phone' => $spouseMember->phone_number,
                    ]);
                    
                    // Create User account for spouse member
                    try {
                        // Extract lastname from full_name
                        $spouseNameParts = explode(' ', trim($spouseMember->full_name));
                        $spouseLastname = !empty($spouseNameParts) ? strtoupper(end($spouseNameParts)) : 'MEMBER';
                        
                        // Create user account for spouse
                        $spouseUser = \App\Models\User::create([
                            'name' => $spouseMember->full_name,
                            'email' => $spouseMember->member_id, // Use member_id as username/email
                            'password' => \Hash::make($spouseLastname), // Password is lastname in uppercase
                            'role' => 'member',
                            'member_id' => $spouseMember->id,
                            'phone_number' => $spouseMember->phone_number,
                            'campus_id' => $spouseMember->campus_id, // Assign same campus as spouse member
                        ]);
                        
                        \Log::info('User account created for spouse member', [
                            'spouse_member_id' => $spouseMember->id,
                            'user_id' => $spouseUser->id,
                            'username' => $spouseMember->member_id,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to create user account for spouse member', [
                            'spouse_member_id' => $spouseMember->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                    
                } catch (\Throwable $e) {
                    \Log::error('Failed to create spouse member', [
                        'error' => $e->getMessage(),
                        'main_member_id' => $member->id,
                        'spouse_name' => $member->spouse_full_name,
                    ]);
                }
            }

            // Send welcome SMS to spouse if they are a church member
            try {
                $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                if ($smsEnabled && 
                    $member->marital_status === 'married' && 
                    $member->spouse_church_member === 'yes' && 
                    !empty($member->spouse_phone_number) &&
                    $spouseMember) {
                    
                    $churchName = SettingsService::get('church_name', 'KKKT Ushirika wa Longuo');
                    
                    // Get username and password for spouse SMS
                    $spouseUsername = $spouseMember->member_id;
                    $spouseNameParts = explode(' ', trim($spouseMember->full_name));
                    $spousePassword = !empty($spouseNameParts) ? strtoupper(end($spouseNameParts)) : 'MEMBER';
                    
                    $spouseMessage = "Umesajiliwa kikamilifu kwenye mfumo wa Kanisa la {$churchName}.\n\n";
                    $spouseMessage .= "Unaweza kuingia kwenye akaunti yako kwa kutumia:\n";
                    $spouseMessage .= "Username: {$spouseUsername}\n";
                    $spouseMessage .= "Password: {$spousePassword}\n\n";
                    $spouseMessage .= "Unaweza kupokea taarifa za ibada, matukio, na huduma kwa njia ya SMS. Karibu sana!";
                    
                    $spouseResp = app(SmsService::class)->sendDebug($member->spouse_phone_number, $spouseMessage);
                    \Log::info('Spouse Welcome SMS provider response', [
                        'spouse_phone' => $member->spouse_phone_number,
                        'spouse_name' => $member->spouse_full_name,
                        'ok' => $spouseResp['ok'] ?? null,
                        'status' => $spouseResp['status'] ?? null,
                        'body' => $spouseResp['body'] ?? null,
                        'reason' => $spouseResp['reason'] ?? null,
                        'error' => $spouseResp['error'] ?? null,
                        'request' => $spouseResp['request'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Spouse Welcome SMS failed', ['error' => $e->getMessage()]);
            }

            // Automatically register member AND family (spouse + teenagers) to biometric device
            // This happens AFTER spouse and children are created to ensure all are registered at once
            try {
                // Check if device connection is configured
                $ip = config('zkteco.ip');
                $port = config('zkteco.port');
                $password = config('zkteco.password', 0);
                
                if ($ip && $port) {
                    // Generate enroll ID if not already set (should be auto-generated by model boot, but double-check)
                    if (empty($member->biometric_enroll_id)) {
                        $enrollId = Member::generateBiometricEnrollId();
                        $member->biometric_enroll_id = $enrollId;
                        $member->save();
                    }
                    
                    // Try to register to device (don't fail member creation if device is offline)
                    try {
                        $zktecoService = new ZKTecoService($ip, $port, $password);
                        
                        if ($zktecoService->connect()) {
                            $enrollId = $member->biometric_enroll_id;
                            
                            // Validate enroll ID is in valid range
                            $enrollIdInt = (int)$enrollId;
                            if ($enrollIdInt >= 10 && $enrollIdInt <= 999) {
                                \Log::info("=== REGISTERING FAMILY TO BIOMETRIC DEVICE ===");
                                \Log::info("Main Member: {$member->full_name} (ID: {$enrollId})");
                                
                                // Register main member
                                try {
                                    $registered = $zktecoService->registerUser(
                                        $enrollIdInt,
                                        (string)$enrollId,
                                        $member->full_name,
                                        '',
                                        0,
                                        0
                                    );
                                    
                                    if ($registered) {
                                        \Log::info("✅ Main member '{$member->full_name}' registered to device (ID: {$enrollId})");
                                    } else {
                                        \Log::warning("Main member registration returned false, but continuing with family registration");
                                    }
                                } catch (\Exception $mainError) {
                                    if (strpos($mainError->getMessage(), 'already exists') !== false) {
                                        \Log::info("✅ Main member '{$member->full_name}' already on device");
                                    } else {
                                        \Log::warning("Main member registration error: " . $mainError->getMessage() . " - Continuing with family");
                                    }
                                }
                                
                                // Register spouse if they are a church member
                                // Reload member to get spouse relationship (spouse was created after main member)
                                $member->refresh();
                                $member->load('spouseMember');
                                
                                if ($member->spouse_member_id) {
                                    $spouse = $member->spouseMember;
                                    if ($spouse) {
                                        \Log::info("Registering spouse: {$spouse->full_name}");
                                        
                                        // Generate enroll ID for spouse if needed
                                        if (!$spouse->biometric_enroll_id) {
                                            $spouseEnrollId = Member::generateBiometricEnrollId();
                                            $spouse->biometric_enroll_id = $spouseEnrollId;
                                            $spouse->save();
                                        } else {
                                            $spouseEnrollId = $spouse->biometric_enroll_id;
                                        }
                                        
                                        // Small delay
                                        usleep(500000);
                                        
                                        try {
                                            $spouseResult = $zktecoService->registerUser(
                                                (int)$spouseEnrollId,
                                                (string)$spouseEnrollId,
                                                $spouse->full_name,
                                                '',
                                                0,
                                                0
                                            );
                                            
                                            if ($spouseResult) {
                                                \Log::info("✅ Spouse '{$spouse->full_name}' registered to device (ID: {$spouseEnrollId})");
                                            }
                                        } catch (\Exception $spouseError) {
                                            if (strpos($spouseError->getMessage(), 'already exists') !== false) {
                                                \Log::info("✅ Spouse '{$spouse->full_name}' already on device");
                                            } else {
                                                \Log::warning("Spouse registration error: " . $spouseError->getMessage());
                                            }
                                        }
                                    }
                                }
                                
                                // Register teenager children (13-17)
                                $member->load('children');
                                $allChildren = $member->children()->whereNotNull('date_of_birth')->get();
                                $teenagers = $allChildren->filter(function($child) {
                                    return $child->shouldAttendMainService(); // Only teenagers (13-17)
                                });
                                
                                \Log::info("Found {$teenagers->count()} teenagers to register");
                                
                                foreach ($teenagers as $teenager) {
                                    \Log::info("Registering teenager: {$teenager->full_name} (age: {$teenager->getAge()})");
                                    
                                    // Generate enroll ID if needed
                                    if (!$teenager->biometric_enroll_id) {
                                        $teenEnrollId = \App\Models\Child::generateBiometricEnrollId();
                                        $teenager->biometric_enroll_id = $teenEnrollId;
                                        $teenager->save();
                                    } else {
                                        $teenEnrollId = $teenager->biometric_enroll_id;
                                    }
                                    
                                    // Small delay
                                    usleep(500000);
                                    
                                    try {
                                        $teenResult = $zktecoService->registerUser(
                                            (int)$teenEnrollId,
                                            (string)$teenEnrollId,
                                            $teenager->full_name,
                                            '',
                                            0,
                                            0
                                        );
                                        
                                        if ($teenResult) {
                                            \Log::info("✅ Teenager '{$teenager->full_name}' registered to device (ID: {$teenEnrollId})");
                                        }
                                    } catch (\Exception $teenError) {
                                        if (strpos($teenError->getMessage(), 'already exists') !== false) {
                                            \Log::info("✅ Teenager '{$teenager->full_name}' already on device");
                                        } else {
                                            \Log::warning("Teenager registration error: " . $teenError->getMessage());
                                        }
                                    }
                                }
                                
                                \Log::info("✅ Family registration complete for member '{$member->full_name}'");
                            } else {
                                \Log::warning("Member '{$member->full_name}' has invalid enroll ID: {$enrollId} (must be 10-999)");
                            }
                            
                            $zktecoService->disconnect();
                        } else {
                            \Log::warning("Could not connect to biometric device to register member '{$member->full_name}'. Device may be offline.");
                        }
                    } catch (\Exception $deviceError) {
                        // Don't fail member creation if device registration fails
                        \Log::warning("Failed to register member '{$member->full_name}' to biometric device: " . $deviceError->getMessage());
                    }
                } else {
                    \Log::info("Biometric device not configured. Member '{$member->full_name}' created without device registration.");
                }
            } catch (\Exception $e) {
                // Don't fail member creation if biometric registration fails
                \Log::warning("Biometric registration error for member '{$member->full_name}': " . $e->getMessage());
            }

            // Get updated total members count
            $totalMembers = Member::count();

            if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Member registered successfully! Member ID: {$memberId}",
                'member' => $member,
                'totalMembers' => $totalMembers
            ], 201);
            }


            // Flash session data for SweetAlert popup and redirect to view the member
            return redirect()->route('members.show', $member->id)->with([
                'success' => 'Member registered successfully!',
                'user_id' => $memberId,
                'name' => $member->full_name,
                'membership_type' => $member->membership_type,
                'member_id' => $member->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('=== MEMBER CREATION FAILED ===');
            \Log::error('Exception message: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while registering the member.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'An error occurred while registering the member.');
        }
    }

  public function index(Request $request)
{
    $query = Member::query();

    // Apply branch/campus filtering based on user's access
    if (auth()->check() && !auth()->user()->isAdmin()) {
        $userCampus = auth()->user()->getCampus();
        if ($userCampus) {
            if ($userCampus->is_main_campus) {
                // Usharika (main campus) can see all branches including itself
                $campusIds = [$userCampus->id];
                $campusIds = array_merge($campusIds, $userCampus->subCampuses->pluck('id')->toArray());
                $query->whereIn('campus_id', $campusIds);
            } else {
                // Branch users can only see their own branch members
                $query->where('campus_id', $userCampus->id);
            }
        } elseif (auth()->user()->campus_id) {
            // Fallback: if user has campus_id but getCampus() fails
            $query->where('campus_id', auth()->user()->campus_id);
        }
    }

    // Search across key fields
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%")
              ->orWhere('member_id', 'like', "%{$search}%");
        });
    }

    // Branch filter (for Usharika to filter by specific branch)
    if ($request->filled('campus_id') && auth()->check() && auth()->user()->getCampus() && auth()->user()->getCampus()->is_main_campus) {
        $query->where('campus_id', $request->campus_id);
    }

    // Membership type filter (permanent/temporary)
    if ($request->filled('membership_type')) {
        $query->where('membership_type', $request->membership_type);
    } else {
        // Default to permanent if no type specified
        if (!$request->filled('type') && !$request->filled('archived')) {
            $query->where('membership_type', 'permanent');
        }
    }

    // Filters
    if ($request->filled('region')) {
        $query->where('region', $request->region);
    }
    if ($request->filled('district')) {
        $query->where('district', $request->district);
    }
    if ($request->filled('ward')) {
        $query->where('ward', $request->ward);
    }
    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }
    if ($request->filled('tribe')) {
        $query->where('tribe', $request->tribe);
    }
    if ($request->filled('living_with_family')) {
        $query->where('living_with_family', $request->living_with_family);
    }

    // Get base query for distincts (respecting branch access)
    $baseQuery = clone $query;
    
    // Distincts for filter dropdowns (filtered by branch access)
    $regions = (clone $baseQuery)->distinct()->pluck('region')->filter()->sort()->values();
    $districts = (clone $baseQuery)->distinct()->pluck('district')->filter()->sort()->values();
    $wards = (clone $baseQuery)->distinct()->pluck('ward')->filter()->sort()->values();
    $tribes = (clone $baseQuery)->distinct()->pluck('tribe')->filter()->sort()->values();

    $members = $query->orderBy('created_at', 'desc')->paginate(10);
    $members->appends($request->query());

    // Fetch children (filtered by branch access through their parents)
    $childrenQuery = \App\Models\Child::with('member');
    if (auth()->check() && !auth()->user()->isAdmin()) {
        $userCampus = auth()->user()->getCampus();
        if ($userCampus && !$userCampus->is_main_campus) {
            $childrenQuery->whereHas('member', function($q) use ($userCampus) {
                $q->where('campus_id', $userCampus->id);
            });
        }
    }
    $children = $childrenQuery->orderBy('full_name', 'asc')->get();

    // Get campuses for branch filter dropdown (for Usharika)
    $campuses = null;
    if (auth()->check() && auth()->user()->getCampus() && auth()->user()->getCampus()->is_main_campus) {
        $campuses = \App\Models\Campus::where('is_active', true)
            ->orderBy('is_main_campus', 'desc')
            ->orderBy('name')
            ->get();
    }

    // Count members by type for tabs
    $baseCountQuery = Member::query();
    if (auth()->check() && !auth()->user()->isAdmin()) {
        $userCampus = auth()->user()->getCampus();
        if ($userCampus) {
            if ($userCampus->is_main_campus) {
                $campusIds = [$userCampus->id];
                $campusIds = array_merge($campusIds, $userCampus->subCampuses->pluck('id')->toArray());
                $baseCountQuery->whereIn('campus_id', $campusIds);
            } else {
                $baseCountQuery->where('campus_id', $userCampus->id);
            }
        } elseif (auth()->user()->campus_id) {
            $baseCountQuery->where('campus_id', auth()->user()->campus_id);
        }
    }
    
    $permanentCount = (clone $baseCountQuery)->where('membership_type', 'permanent')->count();
    $temporaryCount = (clone $baseCountQuery)->where('membership_type', 'temporary')->count();
    $childrenCount = $children->count();

    // Handle children view
    if ($request->filled('type') && $request->type === 'children') {
        $members = null; // Children will be shown instead
    }


    if ($request->wantsJson() || $request->has('wantsJson')) {
        // For dropdown/select purposes, return simple array of members
        $simpleMembers = Member::query();
        
        // Apply branch/campus filtering
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus) {
                if ($userCampus->is_main_campus) {
                    $campusIds = [$userCampus->id];
                    $campusIds = array_merge($campusIds, $userCampus->subCampuses->pluck('id')->toArray());
                    $simpleMembers->whereIn('campus_id', $campusIds);
                } else {
                    $simpleMembers->where('campus_id', $userCampus->id);
                }
            } elseif (auth()->user()->campus_id) {
                $simpleMembers->where('campus_id', auth()->user()->campus_id);
            }
        }
        
        // Only return permanent and temporary members (not children)
        // Load spouse relationship to show couples together
        $simpleMembers->whereIn('membership_type', ['permanent', 'temporary'])
            ->with(['spouseMember', 'mainMember'])
            ->orderBy('full_name', 'asc')
            ->select('id', 'full_name', 'member_id', 'phone_number', 'email', 'spouse_member_id', 'member_type');
        
        $members = $simpleMembers->get();
        
        // Format members to show couples together
        $formattedMembers = [];
        $processedIds = [];
        
        foreach ($members as $member) {
            // Skip if already processed (as a spouse)
            if (in_array($member->id, $processedIds)) {
                continue;
            }
            
            // Check if this member has a spouse who is also a church member
            $spouse = null;
            
            // Method 1: Check if member has spouse_member_id and load the spouse
            if ($member->spouse_member_id) {
                $spouse = $members->firstWhere('id', $member->spouse_member_id);
            }
            
            // Method 2: Check reverse - if another member has this member as spouse
            if (!$spouse) {
                $spouse = $members->firstWhere('spouse_member_id', $member->id);
            }
            
            // Method 3: Try using the relationship (in case it's loaded)
            if (!$spouse && $member->spouseMember) {
                $spouse = $member->spouseMember;
            }
            
            // If we found a spouse who is also a church member (not a child)
            if ($spouse && $spouse->membership_type !== 'child' && $member->membership_type !== 'child') {
                // Use the member with lower ID as main (for consistency)
                if ($member->id < $spouse->id) {
                    $mainMember = $member;
                    $spouseMember = $spouse;
                } else {
                    $mainMember = $spouse;
                    $spouseMember = $member;
                }
                
                // Show as a couple: "John Doe & Jane Doe"
                $formattedMembers[] = [
                    'id' => $mainMember->id, // Use main member's ID
                    'full_name' => $mainMember->full_name . ' & ' . $spouseMember->full_name,
                    'member_id' => $mainMember->member_id,
                    'phone_number' => $mainMember->phone_number,
                    'email' => $mainMember->email,
                    'is_couple' => true,
                    'spouse_id' => $spouseMember->id,
                    'spouse_name' => $spouseMember->full_name,
                    'spouse_member_id' => $spouseMember->member_id
                ];
                
                // Mark both as processed
                $processedIds[] = $member->id;
                $processedIds[] = $spouse->id;
            } 
            // Single member (not married or spouse not a church member)
            else {
                $formattedMembers[] = [
                    'id' => $member->id,
                    'full_name' => $member->full_name,
                    'member_id' => $member->member_id,
                    'phone_number' => $member->phone_number,
                    'email' => $member->email,
                    'is_couple' => false
                ];
                $processedIds[] = $member->id;
            }
        }
        
        return response()->json($formattedMembers);
    }

    return view('members.view', compact('members', 'regions', 'districts', 'wards', 'tribes', 'children', 'campuses', 'permanentCount', 'temporaryCount', 'childrenCount'));
}

public function view()
{
    return $this->index(request());
}

public function show($id)
{
    \Log::info('SHOW_MEMBER_ATTEMPT', ['id' => $id, 'type' => gettype($id)]);
    
    // First try to find in regular members
    $member = Member::find($id);
    if ($member) {
        \Log::info('SHOW_MEMBER_FOUND_REGULAR', ['id' => $id]);
        
        // Load the member with children and spouse relationship
        $member->load(['children', 'spouseMember', 'mainMember', 'campus', 'community']);
        
        // Get children - they belong to the main member (father/mother)
        // Children are linked to the member who created them (father or mother)
        // We need to show children on both father's and mother's pages
        
        $children = collect();
        
        // Collect children from multiple sources and merge them
        // This ensures children appear on both parents' pages when they are married
        
        // 1. Get children directly linked to this member
        if ($member->children->isNotEmpty()) {
            $children = $children->merge($member->children);
        }
        
        // 2. If this member is a spouse (has a mainMember), get children from the main member
        if ($member->mainMember) {
            $mainMember = $member->mainMember;
            $mainMember->load('children');
            if ($mainMember->children->isNotEmpty()) {
                $children = $children->merge($mainMember->children);
            }
        }
        
        // 3. If this member has a spouse who is a church member, get children from the spouse
        // (in case children were linked to spouse instead of main member)
        if ($member->spouseMember) {
            $spouse = $member->spouseMember;
            $spouse->load('children');
            if ($spouse->children->isNotEmpty()) {
                $children = $children->merge($spouse->children);
            }
        }
        
        // 4. If spouse has a mainMember (spouse is also a spouse), get children from spouse's mainMember
        if ($member->spouseMember) {
            $spouse = $member->spouseMember;
            if ($spouse->mainMember) {
                $spouse->mainMember->load('children');
                if ($spouse->mainMember->children->isNotEmpty()) {
                    $children = $children->merge($spouse->mainMember->children);
                }
            }
        }
        
        // Remove duplicates by ID (in case same child appears in multiple collections)
        $children = $children->unique('id')->values();
        
        // If this member has a spouse member, add spouse details
        if ($member->spouseMember) {
            $spouse = $member->spouseMember;
            $member->spouse_details = [
                'id' => $spouse->id,
                'member_id' => $spouse->member_id,
                'full_name' => $spouse->full_name,
                'email' => $spouse->email,
                'phone_number' => $spouse->phone_number,
                'date_of_birth' => $spouse->date_of_birth,
                'gender' => $spouse->gender,
                'education_level' => $spouse->education_level,
                'profession' => $spouse->profession,
                'nida_number' => $spouse->nida_number,
                'tribe' => $spouse->tribe,
                'other_tribe' => $spouse->other_tribe,
                'marital_status' => $spouse->marital_status,
            ];
        }
        
        // If this member is a spouse member, add main member details
        if ($member->mainMember) {
            $mainMember = $member->mainMember;
            $member->main_member_details = [
                'id' => $mainMember->id,
                'member_id' => $mainMember->member_id,
                'full_name' => $mainMember->full_name,
                'email' => $mainMember->email,
                'phone_number' => $mainMember->phone_number,
                'date_of_birth' => $mainMember->date_of_birth,
                'gender' => $mainMember->gender,
                'education_level' => $mainMember->education_level,
                'profession' => $mainMember->profession,
                'nida_number' => $mainMember->nida_number,
                'tribe' => $mainMember->tribe,
                'other_tribe' => $mainMember->other_tribe,
                'marital_status' => $mainMember->marital_status,
            ];
        }
        
        // Debug: Log children count for troubleshooting
        \Log::info('MEMBER_SHOW_CHILDREN', [
            'member_id' => $member->id,
            'member_name' => $member->full_name,
            'member_type' => $member->member_type,
            'has_spouse_member_id' => !empty($member->spouse_member_id),
            'has_main_member' => !empty($member->mainMember),
            'children_count' => $children->count(),
            'member_children_count' => $member->children->count(),
            'spouse_children_count' => $member->spouseMember ? $member->spouseMember->children->count() : 0,
            'main_member_children_count' => $member->mainMember ? $member->mainMember->children->count() : 0,
        ]);
        
        // If request wants JSON (AJAX request), return JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($member);
        }
        
        // Otherwise return view
        return view('members.view-member', compact('member', 'children'));
    }
    
    // If not found, return 404
    \Log::info('SHOW_MEMBER_NOT_FOUND', ['id' => $id]);
    
    if (request()->wantsJson() || request()->ajax()) {
        return response()->json(['error' => 'Member not found'], 404);
    }
    
    abort(404, 'Member not found');
}

public function edit($id)
{
    // Check permission
    if (!auth()->user()->hasPermission('members.edit') && !auth()->user()->isAdmin()) {
        abort(403, 'You do not have permission to edit members.');
    }

    $member = Member::findOrFail($id);
    $member->load(['children', 'campus', 'community']);

    // Get campuses for selection - only active campuses
    $query = \App\Models\Campus::where('is_active', true);

    // If user has a campus, filter by it
    if (auth()->check()) {
        $userCampus = auth()->user()->getCampus();
        if ($userCampus && !auth()->user()->isAdmin()) {
            if ($userCampus->is_main_campus) {
                // Main campus can see itself and all sub campuses
                $campusIds = [$userCampus->id];
                $campusIds = array_merge($campusIds, $userCampus->subCampuses->pluck('id')->toArray());
                $query->whereIn('id', $campusIds);
            } else {
                // Sub campus can only see itself
                $query->where('id', $userCampus->id);
            }
        }
    }

    $campuses = $query->orderBy('is_main_campus', 'desc')->orderBy('name', 'asc')->get();

    // Get communities for the member's campus
    $communities = \App\Models\Community::where('campus_id', $member->campus_id)->orderBy('name', 'asc')->get();

    return view('members.edit', compact('member', 'campuses', 'communities'));
}

public function update(Request $request, Member $member)
{
    // Check permission
    if (!auth()->user()->hasPermission('members.edit') && !auth()->user()->isAdmin()) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to edit members.'], 403);
        }
        abort(403, 'You do not have permission to edit members.');
    }

    $rules = [
        'full_name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone_number' => 'required|string|max:20',
        'membership_type' => 'required|in:permanent,temporary',
        'date_of_birth' => 'required|date|before:today',
        'gender' => 'required|in:male,female',
        'nida_number' => 'nullable|string|max:20',
        'tribe' => 'required|string|max:100',
        'other_tribe' => 'nullable|required_if:tribe,Other|string|max:100',
        'region' => 'required|string|max:100',
        'district' => 'required|string|max:100',
        'ward' => 'required|string|max:100',
        'street' => 'required|string|max:255',
        'address' => 'required|string',
        'campus_id' => 'required|exists:campuses,id',
        'community_id' => 'nullable|exists:communities,id',
        'marital_status' => 'nullable|in:single,married,divorced,widowed,separated',
        'wedding_date' => 'nullable|date|before:today',
    ];

    $validated = $request->validate($rules);
    
    // Check if wedding_date is being updated
    $oldWeddingDate = $member->wedding_date ? $member->wedding_date->format('Y-m-d') : null;
    $newWeddingDate = $validated['wedding_date'] ?? null;
    $weddingDateUpdated = $oldWeddingDate !== $newWeddingDate;
    
    $member->update($validated);
    
    // Sync wedding date to spouse if it was updated
    if ($weddingDateUpdated) {
        // If this member has a spouse (spouse is a church member)
        if ($member->spouse_member_id) {
            $spouseMember = Member::find($member->spouse_member_id);
            if ($spouseMember) {
                $spouseMember->update(['wedding_date' => $newWeddingDate]);
                \Log::info('Wedding date synced to spouse member', [
                    'member_id' => $member->id,
                    'spouse_member_id' => $spouseMember->id,
                    'wedding_date' => $newWeddingDate
                ]);
            }
        }
        
        // If this member is a spouse (has a main member)
        $mainMember = Member::where('spouse_member_id', $member->id)->first();
        if ($mainMember) {
            $mainMember->update(['wedding_date' => $newWeddingDate]);
            \Log::info('Wedding date synced to main member', [
                'member_id' => $member->id,
                'main_member_id' => $mainMember->id,
                'wedding_date' => $newWeddingDate
            ]);
        }
    }
    
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Member updated successfully', 'member' => $member]);
    }
    
    return redirect()->route('members.show', $member->id)
        ->with('success', 'Member updated successfully');
}

public function destroy(Member $member)
{
    // Check permission first
    if (!auth()->user()->hasPermission('members.delete') && !auth()->user()->isAdmin()) {
        \Log::warning('DELETE_MEMBER_PERMISSION_DENIED', [
            'user_id' => auth()->id(),
            'member_id' => $member->id
        ]);
        
        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete members.'
            ], 403);
        }
        abort(403, 'You do not have permission to delete members.');
    }
    
    try {
        // Store member info before deletion
        $memberId = $member->id;
        $memberIdString = $member->member_id;
        $fullName = $member->full_name;
        
        \Log::info('DELETE_MEMBER_ATTEMPT', [
            'member_id' => $memberId,
            'member_id_string' => $memberIdString,
            'full_name' => $fullName,
            'user_id' => auth()->id()
        ]);

        // Get the reason from request body
        $reason = request()->input('reason', 'Member deleted by user');
        
        // Delete the member directly (no archiving)
        $member->delete();

        \Log::info('DELETE_MEMBER_SUCCESS', [
            'member_id' => $memberId,
            'member_id_string' => $memberIdString,
            'full_name' => $fullName
        ]);

        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Member has been deleted successfully.',
                'member_id' => $memberId,
                'action' => 'deleted'
            ]);
        }
        
        return redirect()->route('members.index')
            ->with('success', 'Member has been deleted successfully.');

    } catch (\Exception $e) {
        \Log::error('DELETE_MEMBER_FAILED', [
            'member_id' => $member->id ?? 'unknown',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the member: ' . $e->getMessage(),
                'error_type' => 'exception'
            ], 500);
        }
        
        return redirect()->route('members.index')
            ->with('error', 'An error occurred while deleting the member: ' . $e->getMessage());
    }
}

public function exportCsv(Request $request)
{
    $filename = 'members_export_'.now()->format('Ymd_His').'.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function() use ($request) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Member ID', 'Full Name', 'Phone', 'Email', 'Gender', 'Region', 'District', 'Ward']);

        $query = Member::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc')->chunk(200, function($rows) use ($handle) {
            foreach ($rows as $m) {
                fputcsv($handle, [
                    $m->member_id,
                    $m->full_name,
                    $m->phone_number,
                    $m->email,
                    $m->gender,
                    $m->region,
                    $m->district,
                    $m->ward,
                ]);
            }
        });
        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Generate member identity card
 */
public function identityCard(Member $member)
{
    $churchName = config('app.name', 'Waumini Link Church');
    
    return view('members.identity-card', compact('member', 'churchName'));
}

/**
 * Store a child (with member or non-member parent)
 */
public function storeChild(Request $request)
{
    $request->validate([
        'full_name' => 'required|string|max:255',
        'gender' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
        'member_id' => 'nullable|exists:members,id',
        'parent_name' => 'nullable|string|max:255',
        'parent_phone' => 'nullable|string|max:255',
        'parent_relationship' => 'nullable|string|max:255',
        'baptism_status' => 'nullable|in:baptized,not_baptized',
        'baptism_date' => 'nullable|date',
        'baptism_location' => 'nullable|string|max:255',
        'baptized_by' => 'nullable|string|max:255',
        'baptism_certificate_number' => 'nullable|string|max:255',
    ]);

    // Ensure either member_id or parent_name is provided
    if (!$request->filled('member_id') && !$request->filled('parent_name')) {
        return response()->json([
            'success' => false,
            'message' => 'Either select a member parent or provide non-member parent information.'
        ], 422);
    }

    // If member_id is provided, parent fields should not be
    if ($request->filled('member_id') && $request->filled('parent_name')) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot specify both member parent and non-member parent.'
        ], 422);
    }

    try {
        $childData = [
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ];

        // Add member_id if provided
        if ($request->filled('member_id')) {
            $childData['member_id'] = $request->member_id;
        } else {
            $childData['member_id'] = null;
        }

        // Add parent fields if provided (for non-member parents)
        if ($request->filled('parent_name')) {
            $childData['parent_name'] = $request->parent_name;
            $childData['parent_phone'] = $request->filled('parent_phone') ? $request->parent_phone : null;
            $childData['parent_relationship'] = $request->filled('parent_relationship') ? $request->parent_relationship : null;
        } else {
            $childData['parent_name'] = null;
            $childData['parent_phone'] = null;
            $childData['parent_relationship'] = null;
        }

        // Add baptism fields if provided
        if ($request->filled('baptism_status')) {
            $childData['baptism_status'] = $request->baptism_status;
            $childData['baptism_date'] = $request->filled('baptism_date') ? $request->baptism_date : null;
            $childData['baptism_location'] = $request->filled('baptism_location') ? $request->baptism_location : null;
            $childData['baptized_by'] = $request->filled('baptized_by') ? $request->baptized_by : null;
            $childData['baptism_certificate_number'] = $request->filled('baptism_certificate_number') ? $request->baptism_certificate_number : null;
        } else {
            $childData['baptism_status'] = null;
            $childData['baptism_date'] = null;
            $childData['baptism_location'] = null;
            $childData['baptized_by'] = null;
            $childData['baptism_certificate_number'] = null;
        }

        $child = Child::create($childData);

        return response()->json([
            'success' => true,
            'message' => 'Child added successfully.',
            'child' => $child
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error storing child: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to save child: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Show a single child (for editing)
 */
public function showChild(\App\Models\Child $child)
{
    return response()->json($child);
}

/**
 * Update a child
 */
public function updateChild(Request $request, \App\Models\Child $child)
{
    // Check permission
    if (!auth()->user()->hasPermission('members.edit') && !auth()->user()->isAdmin()) {
        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit children.'
            ], 403);
        }
        abort(403, 'You do not have permission to edit children.');
    }

    $request->validate([
        'full_name' => 'required|string|max:255',
        'gender' => 'required|in:male,female',
        'date_of_birth' => 'required|date',
        'baptism_status' => 'nullable|in:baptized,not_baptized',
        'baptism_date' => 'nullable|date',
        'baptism_location' => 'nullable|string|max:255',
        'baptized_by' => 'nullable|string|max:255',
        'baptism_certificate_number' => 'nullable|string|max:255',
    ]);

    try {
        $childData = [
            'full_name' => $request->full_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ];

        // Update baptism fields
        if ($request->filled('baptism_status')) {
            $childData['baptism_status'] = $request->baptism_status;
            $childData['baptism_date'] = $request->filled('baptism_date') ? $request->baptism_date : null;
            $childData['baptism_location'] = $request->filled('baptism_location') ? $request->baptism_location : null;
            $childData['baptized_by'] = $request->filled('baptized_by') ? $request->baptized_by : null;
            $childData['baptism_certificate_number'] = $request->filled('baptism_certificate_number') ? $request->baptism_certificate_number : null;
        } else {
            // If baptism status is not provided, clear all baptism fields
            $childData['baptism_status'] = null;
            $childData['baptism_date'] = null;
            $childData['baptism_location'] = null;
            $childData['baptized_by'] = null;
            $childData['baptism_certificate_number'] = null;
        }

        $child->update($childData);

        return response()->json([
            'success' => true,
            'message' => 'Child updated successfully.',
            'child' => $child->fresh()
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error updating child: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to update child: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Delete a child
 */
public function destroyChild(\App\Models\Child $child)
{
    // Check permission
    if (!auth()->user()->hasPermission('members.delete') && !auth()->user()->isAdmin()) {
        \Log::warning('DELETE_CHILD_PERMISSION_DENIED', [
            'user_id' => auth()->id(),
            'child_id' => $child->id
        ]);
        
        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete children.'
            ], 403);
        }
        abort(403, 'You do not have permission to delete children.');
    }
    
    try {
        // Store child info before deletion
        $childId = $child->id;
        $childName = $child->full_name;
        
        \Log::info('DELETE_CHILD_ATTEMPT', [
            'child_id' => $childId,
            'child_name' => $childName,
            'user_id' => auth()->id()
        ]);

        // Delete the child
        $child->delete();

        \Log::info('DELETE_CHILD_SUCCESS', [
            'child_id' => $childId,
            'child_name' => $childName
        ]);

        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Child has been deleted successfully.',
                'child_id' => $childId,
                'action' => 'deleted'
            ]);
        }
        
        return redirect()->route('members.index')
            ->with('success', 'Child has been deleted successfully.');

    } catch (\Exception $e) {
        \Log::error('DELETE_CHILD_FAILED', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        if (request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the child: ' . $e->getMessage(),
                'error_type' => 'exception'
            ], 500);
        }
        
        return redirect()->route('members.index')
            ->with('error', 'An error occurred while deleting the child: ' . $e->getMessage());
    }
}

    /**
     * Reset member password - Admin only
     * Generates a new password and optionally sends it via SMS
     */
    public function resetPassword(Request $request, $id)
    {
        // Log the request for debugging
        Log::info('Password reset request received', [
            'member_id' => $id,
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators can reset member passwords.'
            ], 403);
        }

        try {
            // Find the member - try by ID first, then by member_id
            $member = Member::find($id);
            if (!$member) {
                // Try finding by member_id if ID doesn't work
                $member = Member::where('member_id', $id)->first();
            }
            
            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found with ID: ' . $id
                ], 404);
            }
            
            // Find user account for this member
            $user = User::where('member_id', $member->id)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user account found for this member.'
                ], 404);
            }

            // Generate a secure random password (8 characters: uppercase, lowercase, numbers)
            $newPassword = $this->generateSecurePassword();
            
            // Update password
            // IMPORTANT: User model has 'password' => 'hashed' cast which can cause issues
            // Use DB::table() to directly update the password field, bypassing model casts
            $hashedPassword = Hash::make($newPassword);
            
            // Direct database update to bypass the 'hashed' cast
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => $hashedPassword]);
            
            // Refresh the model to get the latest data
            $user->refresh();
            
            // Verify the password was saved correctly by checking against database
            $dbPassword = DB::table('users')->where('id', $user->id)->value('password');
            $passwordVerified = Hash::check($newPassword, $dbPassword);
            
            if (!$passwordVerified) {
                Log::error('Password verification failed after reset', [
                    'user_id' => $user->id,
                    'member_id' => $member->id,
                    'email' => $user->email,
                    'db_password_length' => strlen($dbPassword ?? ''),
                    'new_password' => $newPassword
                ]);
                
                // Try one more time with a fresh hash
                $hashedPassword2 = Hash::make($newPassword);
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => $hashedPassword2]);
                
                $user->refresh();
                $dbPassword2 = DB::table('users')->where('id', $user->id)->value('password');
                $passwordVerified2 = Hash::check($newPassword, $dbPassword2);
                
                Log::info('Password reset retry', [
                    'user_id' => $user->id,
                    'password_verified' => $passwordVerified2
                ]);
            } else {
                Log::info('Password reset and verified successfully', [
                    'user_id' => $user->id,
                    'member_id' => $member->id,
                    'email' => $user->email,
                    'password_verified' => true
                ]);
            }

            // Log the password reset
            Log::info('Member password reset by admin', [
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'member_id' => $member->id,
                'member_name' => $member->full_name,
                'user_id' => $user->id
            ]);

            // Send SMS with new password if phone number exists
            $smsSent = false;
            $smsError = null;
            if (!empty($member->phone_number)) {
                try {
                    // Check if SMS notifications are enabled
                    $smsEnabled = \App\Services\SettingsService::get('enable_sms_notifications', false);
                    if (!$smsEnabled) {
                        $smsError = 'SMS notifications are disabled in system settings';
                        Log::info('Password reset SMS skipped: SMS notifications disabled', [
                            'member_id' => $member->id,
                            'phone' => $member->phone_number
                        ]);
                    } else {
                $smsService = app(SmsService::class);
                $message = "Shalom {$member->full_name}, nenosiri lako jipya la akaunti yako ni: {$newPassword}. Tafadhali badilisha nenosiri baada ya kuingia. Mungu akubariki.";
                
                        // Use sendDebug to get detailed response
                        $smsResult = $smsService->sendDebug($member->phone_number, $message);
                        $smsSent = $smsResult['ok'] ?? false;
                        $smsError = $smsResult['reason'] ?? ($smsResult['error'] ?? null);
                
                if ($smsSent) {
                            Log::info('Password reset SMS sent successfully', [
                        'member_id' => $member->id,
                                'phone' => $member->phone_number,
                                'response' => $smsResult
                    ]);
                } else {
                    Log::warning('Password reset SMS failed', [
                        'member_id' => $member->id,
                                'phone' => $member->phone_number,
                                'error' => $smsError,
                                'response' => $smsResult
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $smsError = $e->getMessage();
                    Log::error('Failed to send password reset SMS: ' . $e->getMessage(), [
                        'member_id' => $member->id,
                        'phone' => $member->phone_number,
                        'exception' => $e
                    ]);
                }
            } else {
                $smsError = 'No phone number found for this member';
                Log::info('Password reset SMS skipped: No phone number', [
                    'member_id' => $member->id
                ]);
            }

            // Build success message
            $message = 'Password reset successfully.';
            if ($smsSent) {
                $message .= ' New password has been sent via SMS.';
            } else {
                $message .= ' New password: ' . $newPassword . ' (SMS not sent';
                if ($smsError) {
                    $message .= ': ' . $smsError;
                }
                $message .= ').';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'password' => $newPassword, // Return password so admin can see/copy it
                'sms_sent' => $smsSent,
                'sms_error' => $smsError,
                'member_name' => $member->full_name,
                'phone_number' => $member->phone_number
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting member password: ' . $e->getMessage(), [
                'member_id' => $member->id,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a secure random password
     * 8 characters: uppercase, lowercase, numbers
     */
    private function generateSecurePassword($length = 8)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        
        // Ensure at least one character from each set
        $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        
        // Fill the rest randomly
        $all = $uppercase . $lowercase . $numbers;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Shuffle to randomize position
        return str_shuffle($password);
    }
}