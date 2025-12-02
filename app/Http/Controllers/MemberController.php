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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class MemberController extends Controller
{
    public function create()
    {
        return view('members.add-members');
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
            'gender' => 'required|in:male,female',

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
                
                $profilePicturePath = $file->store('members/profile-pictures', 'public');
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
                
                $spouseProfilePicturePath = $file->store('members/profile-pictures', 'public');
            }

            // Generate unique member ID
            $memberId = Member::generateMemberId();

            // Create member
            \Log::info('=== CREATING MEMBER ===');
            \Log::info('Member ID to be created: ' . $memberId);
            
            $memberData = [
                'member_id' => $memberId,
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

            // Register member on biometric attendance device
            try {
                $biometricResponse = Http::timeout(5)->post(
                    'http://192.168.100.100:8000/api/v1/users/register',
                    [
                        // Use internal numeric ID as enroll_id basis; device will return actual enroll_id
                        'id' => (string) $member->id,
                        'name' => $member->full_name,
                    ]
                );

                if ($biometricResponse->successful()) {
                    $biometricJson = $biometricResponse->json();

                    \Log::info('Biometric registration response', [
                        'member_id' => $member->id,
                        'response' => $biometricJson,
                    ]);

                    if (!empty($biometricJson['data']['enroll_id'])) {
                        $member->biometric_enroll_id = (string) $biometricJson['data']['enroll_id'];
                        $member->save();
                    }
                } else {
                    \Log::warning('Biometric registration HTTP error', [
                        'member_id' => $member->id,
                        'status' => $biometricResponse->status(),
                        'body' => $biometricResponse->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error('Biometric registration failed', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue flow even if biometric registration fails
            }

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
            try {
                $smsEnabled = SettingsService::get('enable_sms_notifications', false);
                if ($smsEnabled && !empty($member->phone_number)) {
                    $churchName = SettingsService::get('church_name', 'AIC Moshi Kilimanjaro');
                    
                    // Get username and password for SMS
                    $username = $member->member_id;
                    $nameParts = explode(' ', trim($member->full_name));
                    $password = !empty($nameParts) ? strtoupper(end($nameParts)) : 'MEMBER';
                    
                    $message = "Umesajiliwa kikamilifu kwenye mfumo wa Kanisa la {$churchName}.\n\n";
                    $message .= "Unaweza kuingia kwenye akaunti yako kwa kutumia:\n";
                    $message .= "Username: {$username}\n";
                    $message .= "Password: {$password}\n\n";
                    $message .= "Unaweza kupokea taarifa za ibada, matukio, na huduma kwa njia ya SMS. Karibu sana!";
                    
                    $resp = app(SmsService::class)->sendDebug($member->phone_number, $message);
                    \Log::info('Welcome SMS provider response', [
                        'ok' => $resp['ok'] ?? null,
                        'status' => $resp['status'] ?? null,
                        'body' => $resp['body'] ?? null,
                        'reason' => $resp['reason'] ?? null,
                        'error' => $resp['error'] ?? null,
                        'request' => $resp['request'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Welcome SMS failed', ['error' => $e->getMessage()]);
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
                    
                    $churchName = SettingsService::get('church_name', 'AIC Moshi Kilimanjaro');
                    
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


            // Flash session data for SweetAlert popup
            return redirect()->route('members.add')->with([
                'success' => 'Member registered successfully!',
                'user_id' => $memberId,
                'name' => $member->full_name,
                'membership_type' => $member->membership_type,
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

    // Distincts for filter dropdowns
    $regions = Member::distinct()->pluck('region')->filter()->sort()->values();
    $districts = Member::distinct()->pluck('district')->filter()->sort()->values();
    $wards = Member::distinct()->pluck('ward')->filter()->sort()->values();
    $tribes = Member::distinct()->pluck('tribe')->filter()->sort()->values();

    $members = $query->orderBy('created_at', 'desc')->paginate(10);
    $members->appends($request->query());

    // Fetch archived members from DeletedMember
    $archivedMembers = \App\Models\DeletedMember::orderBy('deleted_at_actual', 'desc')->get();

    // Fetch all children
    $children = \App\Models\Child::with('member')
        ->orderBy('full_name', 'asc')
        ->get();

    if ($request->wantsJson()) {
        return response()->json($members);
    }

    return view('members.view', compact('members', 'regions', 'districts', 'wards', 'tribes', 'archivedMembers', 'children'));
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
        $member->load(['children', 'spouseMember', 'mainMember']);
        
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
        
        return response()->json($member);
    }
    
    // If not found, try to find in archived members
    $archivedMember = DeletedMember::where('member_id', (int)$id)->first();
    \Log::info('SHOW_ARCHIVED_SEARCH', [
        'id' => $id, 
        'archived_found' => $archivedMember ? true : false,
        'archived_id' => $archivedMember ? $archivedMember->id : null
    ]);
    
    if ($archivedMember) {
        // Return the archived member data with the snapshot
        $memberData = $archivedMember->member_snapshot;
        $memberData['archived'] = true;
        $memberData['archive_reason'] = $archivedMember->reason;
        $memberData['archived_at'] = $archivedMember->deleted_at_actual;
        \Log::info('SHOW_ARCHIVED_SUCCESS', ['id' => $id]);
        return response()->json($memberData);
    }
    
    // If not found in either table, return 404
    \Log::info('SHOW_MEMBER_NOT_FOUND', ['id' => $id]);
    return response()->json(['error' => 'Member not found'], 404);
}

public function update(Request $request, Member $member)
{
    $rules = [
        'full_name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|nullable|email|max:255',
        'phone_number' => 'sometimes|required|string|max:20',
        'membership_type' => 'sometimes|required|in:permanent,temporary',
        'date_of_birth' => 'sometimes|required|date|before:today',
        'gender' => 'sometimes|required|in:male,female',
        'nida_number' => 'nullable|string|max:20',
        'tribe' => 'sometimes|required|string|max:100',
        'other_tribe' => 'nullable|required_if:tribe,Other|string|max:100',
        'region' => 'sometimes|required|string|max:100',
        'district' => 'sometimes|required|string|max:100',
        'ward' => 'sometimes|required|string|max:100',
        'street' => 'sometimes|required|string|max:255',
        'address' => 'sometimes|required|string',
        'living_with_family' => 'sometimes|required|in:yes,no',
        'family_relationship' => 'nullable|required_if:living_with_family,yes|string|max:100',
    ];

    $validated = $request->validate($rules);
    $member->update($validated);
    return response()->json(['success' => true, 'message' => 'Member updated successfully', 'member' => $member]);
}

public function destroy(Member $member)
{
    try {
        \Log::info('ARCHIVE_MEMBER_ATTEMPT', [
            'member_id' => $member->id,
            'member_id_string' => $member->member_id,
            'full_name' => $member->full_name,
            'request_method' => request()->method(),
            'request_url' => request()->url(),
            'request_headers' => request()->headers->all(),
            'route_name' => request()->route()->getName(),
            'route_parameters' => request()->route()->parameters()
        ]);

        // Get the reason from request body
        $reason = request()->input('reason', 'Member archived via delete action - all financial records preserved');
        
        // Instead of deleting, archive the member to preserve all financial records
        DeletedMember::create([
            'member_id' => $member->id,
            'member_snapshot' => $member->toArray(),
            'reason' => $reason,
            'deleted_at_actual' => now(),
        ]);

        \Log::info('ARCHIVE_MEMBER_SUCCESS', [
            'member_id' => $member->id,
            'full_name' => $member->full_name
        ]);

        // Remove from active members (but keep all financial records intact)
        $member->delete();

        \Log::info('DELETE_MEMBER_SUCCESS', [
            'member_id' => $member->id,
            'member_id_string' => $member->member_id
        ]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Member has been moved to archived status. All financial records (tithes, offerings, donations, pledges) have been preserved and remain intact.',
                'member_id' => $member->id,
                'action' => 'archived'
            ]);
        }
        
        return redirect()->route('members.view')
            ->with('success', 'Member has been moved to archived status. All financial records have been preserved.');

    } catch (\Exception $e) {
        \Log::error('DELETE_MEMBER_FAILED', [
            'member_id' => $member->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the member: ' . $e->getMessage(),
                'error_type' => 'exception'
            ], 500);
        }
        
        return redirect()->route('members.view')
            ->with('error', 'An error occurred while deleting the member: ' . $e->getMessage());
    }
}

public function archive(Request $request, Member $member)
{
    \Log::info('ARCHIVE_ATTEMPT', [
        'member_id' => $member->id,
        'membership_type' => $member->membership_type,
        'full_name' => $member->full_name,
        'request_data' => $request->all()
    ]);

    $validated = $request->validate([
        'reason' => 'required|string|max:500',
    ]);

    DeletedMember::create([
        'member_id' => $member->id,
        'member_snapshot' => $member->toArray(),
        'reason' => $validated['reason'],
        'deleted_at_actual' => now(),
    ]);

    \Log::info('ARCHIVE_SUCCESS', [
        'member_id' => $member->id,
        'membership_type' => $member->membership_type
    ]);

    $member->delete();

    // If AJAX, return JSON; else redirect to archived tab
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Member archived successfully']);
    }
    return redirect()->route('members.view', ['tab' => 'archived'])
        ->with('success', 'Member archived successfully');
}

/**
 * Permanently delete an archived member
 */
public function destroyArchived(Request $request, $memberId)
{
    try {
        \Log::info('DELETE_ARCHIVED_MEMBER_ATTEMPT', [
            'member_id' => $memberId,
            'request_method' => request()->method(),
            'request_url' => request()->url(),
            'request_headers' => request()->headers->all()
        ]);

        // Find the archived member
        $archivedMember = DeletedMember::where('member_id', $memberId)->first();
        
        if (!$archivedMember) {
            return response()->json([
                'success' => false,
                'message' => 'Archived member not found'
            ], 404);
        }

        // Check if there are any related financial records
        $hasTithes = \App\Models\Tithe::where('member_id', $memberId)->count() > 0;
        $hasOfferings = \App\Models\Offering::where('member_id', $memberId)->count() > 0;
        $hasDonations = \App\Models\Donation::where('member_id', $memberId)->count() > 0;
        $hasPledges = \App\Models\Pledge::where('member_id', $memberId)->count() > 0;

        if ($hasTithes || $hasOfferings || $hasDonations || $hasPledges) {
            \Log::warning('DELETE_ARCHIVED_MEMBER_BLOCKED', [
                'member_id' => $memberId,
                'has_tithes' => $hasTithes,
                'has_offerings' => $hasOfferings,
                'has_donations' => $hasDonations,
                'has_pledges' => $hasPledges
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Cannot permanently delete archived member. Member has associated financial records (tithes, offerings, donations, or pledges).',
                'error_type' => 'has_related_data'
            ], 422);
        }

        // Delete the archived member record
        $archivedMember->delete();

        \Log::info('DELETE_ARCHIVED_MEMBER_SUCCESS', [
            'member_id' => $memberId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archived member permanently deleted successfully'
        ]);

    } catch (\Exception $e) {
        \Log::error('DELETE_ARCHIVED_MEMBER_FAILED', [
            'member_id' => $memberId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while deleting the archived member: ' . $e->getMessage(),
            'error_type' => 'exception'
        ], 500);
    }
}

/**
 * Restore an archived member back to active status
 */
public function restore(Request $request, $memberId)
{
    try {
        \Log::info('RESTORE_MEMBER_ATTEMPT', [
            'member_id' => $memberId,
            'request_method' => request()->method(),
            'request_url' => request()->url()
        ]);

        // Find the archived member
        $archivedMember = DeletedMember::where('member_id', $memberId)->first();
        
        if (!$archivedMember) {
            return response()->json([
                'success' => false,
                'message' => 'Archived member not found'
            ], 404);
        }

        // Check if member already exists (shouldn't happen, but safety check)
        // Check by database ID first, then by member_id string
        $existingById = Member::find($memberId);
        $memberIdString = $archivedMember->member_snapshot['member_id'] ?? null;
        $existingByMemberId = $memberIdString ? Member::where('member_id', $memberIdString)->first() : null;
        
        if ($existingById || $existingByMemberId) {
            return response()->json([
                'success' => false,
                'message' => 'Member already exists in active members list. Please refresh the page.'
            ], 422);
        }

        // Get the snapshot data
        $snapshot = $archivedMember->member_snapshot;
        
        // Remove fields that shouldn't be restored directly
        unset($snapshot['id'], $snapshot['created_at'], $snapshot['updated_at'], $snapshot['deleted_at']);
        
        // Create the member from snapshot
        $member = Member::create($snapshot);

        \Log::info('RESTORE_MEMBER_SUCCESS', [
            'member_id' => $memberId,
            'restored_id' => $member->id,
            'full_name' => $member->full_name
        ]);

        // Delete the archived member record
        $archivedMember->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Member has been restored successfully. All financial records remain intact.',
                'member_id' => $member->id,
                'member' => $member
            ]);
        }

        return redirect()->route('members.view')
            ->with('success', 'Member has been restored successfully.');

    } catch (\Exception $e) {
        \Log::error('RESTORE_MEMBER_FAILED', [
            'member_id' => $memberId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while restoring the member: ' . $e->getMessage(),
                'error_type' => 'exception'
            ], 500);
        }

        return redirect()->route('members.view')
            ->with('error', 'An error occurred while restoring the member: ' . $e->getMessage());
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
     * Reset member password - Admin only
     * Generates a new password and optionally sends it via SMS
     */
    public function resetPassword(Request $request, Member $member)
    {
        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators can reset member passwords.'
            ], 403);
        }

        try {
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
            $user->password = Hash::make($newPassword);
            $user->save();

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
            if (!empty($member->phone_number)) {
                $smsService = app(SmsService::class);
                $message = "Shalom {$member->full_name}, nenosiri lako jipya la akaunti yako ni: {$newPassword}. Tafadhali badilisha nenosiri baada ya kuingia. Mungu akubariki.";
                
                $smsSent = $smsService->send($member->phone_number, $message);
                
                if ($smsSent) {
                    Log::info('Password reset SMS sent', [
                        'member_id' => $member->id,
                        'phone' => $member->phone_number
                    ]);
                } else {
                    Log::warning('Password reset SMS failed', [
                        'member_id' => $member->id,
                        'phone' => $member->phone_number
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
                'password' => $newPassword, // Return password so admin can see/copy it
                'sms_sent' => $smsSent,
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