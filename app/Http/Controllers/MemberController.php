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

    /**
     * Check if an envelope number is available within a community
     */
    public function checkEnvelopeAvailability(Request $request)
    {
        $envelope = $request->query('envelope');
        $communityId = $request->query('community_id');
        $excludeId = $request->query('exclude_id');

        if (!$envelope || !$communityId) {
            return response()->json(['available' => true]); // Default to true if not enough info
        }

        $query = Member::where('community_id', $communityId)
            ->where('envelope_number', $envelope);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This envelope number is already taken in this community.' : 'Available'
        ]);
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

        // Validation rules
        $rules = [
            'member_type' => ['nullable', 'required_if:membership_type,permanent', Rule::in(['father', 'mother', 'independent'])],
            'membership_type' => ['required', Rule::in(['permanent', 'temporary'])],
            'envelope_number' => 'nullable|string|max:100',

            // Temporary membership duration fields
            'membership_duration_value' => 'nullable|required_if:membership_type,temporary|integer|min:1|max:120',
            'membership_duration_unit' => ['nullable', 'required_if:membership_type,temporary', Rule::in(['months', 'years'])],

            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => ['required', 'string', 'max:20', 'regex:/^\+255[0-9]{9,15}$/', Rule::unique('members', 'phone_number')],
            'date_of_birth' => 'required|date|before:today',
            'gender' => ['required', Rule::in(['male', 'female'])],

            'education_level' => ['nullable', Rule::in(['primary', 'secondary', 'high_level', 'certificate', 'diploma', 'bachelor_degree', 'masters', 'phd', 'professor', 'not_studied'])],
            'profession' => 'required|string|max:100',

            // Guardian for temporary members and independent persons
            'guardian_name' => 'nullable|required_if:membership_type,temporary|string|max:255',
            'guardian_phone' => ['nullable', 'required_if:membership_type,temporary', 'string', 'max:20', 'regex:/^\+255[0-9]{9,15}$/'],
            'guardian_relationship' => 'nullable|required_if:membership_type,temporary|string|max:100',

            // Children
            'children_count' => 'nullable|integer|min:0',
            'children' => 'nullable|array',
            'children.*.full_name' => 'required_with:children|string|max:255',
            'children.*.gender' => ['required_with:children', Rule::in(['male', 'female'])],
            'children.*.date_of_birth' => 'required_with:children|date|before_or_equal:today',
            'children.*.is_church_member' => ['nullable', Rule::in(['yes', 'no'])],
            'children.*.campus_id' => 'nullable|exists:campuses,id',
            'children.*.community_id' => 'nullable|exists:communities,id',
            'children.*.region' => 'nullable|string|max:100',
            'children.*.district' => 'nullable|string|max:100',
            'children.*.city_town' => 'nullable|string|max:100',
            'children.*.current_church_attended' => 'nullable|string|max:255',
            'children.*.phone_number' => ['nullable', 'string', 'max:20', 'regex:/^\+255[0-9]{9,15}$/'],
            'children.*.lives_outside_main_area' => ['nullable', Rule::in(['yes', 'no'])],
            // Children social welfare fields
            'children.*.orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'children.*.disability_status' => 'nullable|boolean',
            'children.*.disability_type' => 'nullable|string|max:255',
            'children.*.vulnerable_status' => 'nullable|boolean',
            'children.*.vulnerable_type' => 'nullable|string|max:255',
            'children.*.envelope_number' => 'nullable|string|max:100',

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

            // Baptism fields for member
            'baptism_status' => ['nullable', Rule::in(['baptized', 'not_baptized'])],
            'baptism_date' => 'nullable|date|before:today',
            'baptism_location' => 'nullable|string|max:255',
            'baptized_by' => 'nullable|string|max:255',
            'baptism_certificate_number' => 'nullable|string|max:255',

            // Marital status and spouse info fields
            'marital_status' => ['nullable', Rule::in(['married', 'divorced', 'widowed', 'separated'])],
            'spouse_full_name' => 'nullable|required_if:marital_status,married|string|max:255',
            'spouse_date_of_birth' => 'nullable|required_if:marital_status,married|date|before:today',
            'spouse_education_level' => ['nullable', 'required_if:marital_status,married', Rule::in(['primary', 'secondary', 'high_level', 'certificate', 'diploma', 'bachelor_degree', 'masters', 'phd', 'professor', 'not_studied'])],
            'spouse_profession' => 'nullable|required_if:marital_status,married|string|max:100',
            'spouse_nida_number' => 'nullable|string|max:20',
            'spouse_email' => 'nullable|email|max:255',
            'spouse_phone_number' => ['nullable', 'required_if:marital_status,married', 'string', 'max:20', 'regex:/^\+255[0-9]{9,15}$/'],
            // spouse_gender is automatically determined based on member_type (father -> female, mother -> male)
            'spouse_tribe' => 'nullable|required_if:marital_status,married|string|max:100',
            'spouse_other_tribe' => 'nullable|required_if:spouse_tribe,Other|string|max:100',
            'spouse_church_member' => ['nullable', 'required_if:marital_status,married', Rule::in(['yes', 'no'])],
            'spouse_campus_id' => 'nullable|exists:campuses,id',
            'spouse_community_id' => 'nullable|exists:communities,id',
            'spouse_envelope_number' => 'nullable|string|max:100',

            // Spouse Welfare Fields
            'spouse_orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'spouse_disability_status' => 'nullable|boolean',
            'spouse_disability_type' => 'nullable|string|max:255',

            // Social Welfare Fields
            'orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'disability_status' => 'nullable|boolean',
            'disability_type' => 'nullable|string|max:255',
            'vulnerable_status' => 'nullable|boolean',
            'vulnerable_type' => 'nullable|string|max:255',
        ];
        // Custom validation for independent persons
        if ($request->member_type === 'independent' && $request->membership_type === 'permanent') {
            $rules['guardian_name'] = 'required|string|max:255';
            $rules['guardian_phone'] = ['required', 'string', 'max:20', 'regex:/^\+255[0-9]{9,15}$/'];
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

        $validator->after(function ($v) use ($request) {
            $count = (int) $request->input('children_count', 0);
            if ($request->input('membership_type') === 'permanent') {
                if ($count > 0) {
                    $children = $request->input('children', []);
                    for ($i = 0; $i < $count; $i++) {
                        if (!isset($children[$i]['full_name']) || trim($children[$i]['full_name']) === '') {
                            $v->errors()->add("children.$i.full_name", 'Child full name is required.');
                        }
                        if (!isset($children[$i]['gender']) || !in_array($children[$i]['gender'], ['male', 'female'])) {
                            $v->errors()->add("children.$i.gender", 'Child gender is required.');
                        }
                        if (!isset($children[$i]['date_of_birth']) || empty($children[$i]['date_of_birth'])) {
                            $v->errors()->add("children.$i.date_of_birth", 'Child date of birth is required.');
                        } else {
                            try {
                                // Validate date format only, no age restriction
                                Carbon::parse($children[$i]['date_of_birth']);
                            } catch (\Exception $e) {
                                $v->errors()->add("children.$i.date_of_birth", 'Invalid child date of birth.');
                            }
                        }

                        // Validate child campus and community if child is a church member
                        if (isset($children[$i]['is_church_member']) && $children[$i]['is_church_member'] === 'yes') {
                            if (empty($children[$i]['campus_id'])) {
                                $v->errors()->add("children.$i.campus_id", 'Campus is required for church member children.');
                            }
                            if (empty($children[$i]['community_id'])) {
                                $v->errors()->add("children.$i.community_id", 'Fellowship is required for church member children.');
                            }
                        }
                    }
                }
            }

            // Validate spouse campus and community if spouse is a church member
            if ($request->input('spouse_church_member') === 'yes') {
                if (empty($request->input('spouse_campus_id'))) {
                    $v->errors()->add('spouse_campus_id', 'Spouse campus is required when spouse is a church member.');
                }
                if (empty($request->input('spouse_community_id'))) {
                    $v->errors()->add('spouse_community_id', 'Spouse fellowship is required when spouse is a church member.');
                }
            }

            // Mandate spouse envelope number if married
            if ($request->input('marital_status') === 'married') {
                if (empty($request->input('spouse_envelope_number'))) {
                    $v->errors()->add('spouse_envelope_number', 'Spouse envelope number is required.');
                }
            }

            // Mandate envelope number for children aged 18+
            $count = (int) $request->input('children_count', 0);
            if ($count > 0) {
                $children = $request->input('children', []);
                for ($i = 0; $i < $count; $i++) {
                    if (isset($children[$i]['date_of_birth']) && !empty($children[$i]['date_of_birth'])) {
                        try {
                            $dob = Carbon::parse($children[$i]['date_of_birth']);
                            if ($dob->diffInYears(now()) >= 18) {
                                if (empty($children[$i]['envelope_number'])) {
                                    $v->errors()->add("children.$i.envelope_number", 'Envelope number is required for children aged 18 and above.');
                                }
                            }
                        } catch (\Exception $e) {
                            // Date validation already handled above
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
            } elseif ($request->filled('campus_id')) {
                // Usharika admin OR user with permission - can select branch
                // This covers the case where an admin selects a sub-campus
                $campusId = $request->campus_id;
            } elseif ($userCampus) {
                // Fallback to user's campus if not specified
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

            // Calculate temporary membership dates if temporary membership
            $membershipStartDate = null;
            $membershipEndDate = null;
            $membershipDurationMonths = null;

            if ($request->membership_type === 'temporary') {
                $membershipStartDate = now();
                $durationValue = (int) ($request->membership_duration_value ?? 3);
                $durationUnit = $request->membership_duration_unit ?? 'months';

                // Convert to months
                if ($durationUnit === 'years') {
                    $membershipDurationMonths = $durationValue * 12;
                } else {
                    $membershipDurationMonths = $durationValue;
                }

                $membershipEndDate = $membershipStartDate->copy()->addMonths((int) $membershipDurationMonths);
            }

            $memberData = [
                'member_id' => $memberId,
                'campus_id' => $campusId,
                'community_id' => $request->community_id, // Community assignment
                'biometric_enroll_id' => $request->biometric_enroll_id, // biometric_enroll_id will be filled after successful device registration
                'envelope_number' => $request->envelope_number,
                'member_type' => $request->member_type,
                'membership_type' => $request->membership_type,
                'membership_duration_months' => $membershipDurationMonths,
                'membership_start_date' => $membershipStartDate,
                'membership_end_date' => $membershipEndDate,
                'membership_status' => 'active',
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
                'baptism_status' => $request->baptism_status ?: null,
                'baptism_date' => $request->baptism_status === 'baptized' ? $request->baptism_date : null,
                'baptism_location' => $request->baptism_status === 'baptized' ? $request->baptism_location : null,
                'baptized_by' => $request->baptism_status === 'baptized' ? $request->baptized_by : null,
                'baptism_certificate_number' => $request->baptism_status === 'baptized' ? $request->baptism_certificate_number : null,
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
                'spouse_campus_id' => $request->spouse_campus_id,
                'spouse_community_id' => $request->spouse_community_id,
                'spouse_orphan_status' => $request->spouse_orphan_status ?? 'not_orphan',
                'spouse_disability_status' => $request->has('spouse_disability_status') || $request->spouse_disability_status == 1,
                'spouse_disability_type' => $request->spouse_disability_type,
                'orphan_status' => $request->orphan_status ?? 'not_orphan',
                'disability_status' => $request->has('disability_status') || $request->disability_status == 1,
                'disability_type' => $request->disability_type,
                'vulnerable_status' => $request->has('vulnerable_status') || $request->vulnerable_status == 1,
                'vulnerable_type' => $request->vulnerable_type,
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

            // Children creation (no age restriction)
            $children = [];
            if ($request->filled('children')) {
                foreach ($request->children as $childData) {
                    // Determine if child is a church member
                    $isChurchMember = isset($childData['is_church_member']) && $childData['is_church_member'] === 'yes';

                    $childFields = [
                        'full_name' => $childData['full_name'],
                        'gender' => $childData['gender'],
                        'date_of_birth' => $childData['date_of_birth'],
                        'is_church_member' => $isChurchMember,
                        // Social welfare fields
                        'orphan_status' => $childData['orphan_status'] ?? 'not_orphan',
                        'disability_status' => isset($childData['disability_status']) && $childData['disability_status'] ? true : false,
                        'disability_type' => $childData['disability_type'] ?? null,
                        'vulnerable_status' => isset($childData['vulnerable_status']) && $childData['vulnerable_status'] ? true : false,
                        'vulnerable_type' => $childData['vulnerable_type'] ?? null,
                        'envelope_number' => $childData['envelope_number'] ?? null,
                    ];

                    // Only add campus and fellowship if child is a church member
                    if ($isChurchMember) {
                        $childFields['campus_id'] = $childData['campus_id'] ?? null;
                        $childFields['community_id'] = $childData['community_id'] ?? null;
                    }

                    // Location fields for children living outside main area
                    if (isset($childData['lives_outside_main_area']) && $childData['lives_outside_main_area'] === 'yes') {
                        $childFields['lives_outside_main_area'] = true;
                        $childFields['region'] = $childData['region'] ?? null;
                        $childFields['district'] = $childData['district'] ?? null;
                        $childFields['city_town'] = $childData['city_town'] ?? null;
                        $childFields['current_church_attended'] = $childData['current_church_attended'] ?? null;
                        $childFields['phone_number'] = $childData['phone_number'] ?? null; // Optional phone number
                    } else {
                        $childFields['lives_outside_main_area'] = false;
                    }

                    $children[] = new Child($childFields);
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
            if (
                $member->marital_status === 'married' &&
                $member->spouse_church_member === 'yes' &&
                !empty($member->spouse_full_name) &&
                !empty($member->spouse_phone_number)
            ) {

                try {
                    // Determine spouse gender based on main member type
                    $spouseGender = ($member->member_type === 'father') ? 'female' : 'male';

                    // Create spouse member data
                    // Use spouse_campus_id and spouse_community_id from request if provided
                    $spouseCampusId = $request->spouse_campus_id ?? $member->campus_id;
                    $spouseCommunityId = $request->spouse_community_id ?? null;

                    $spouseMemberData = [
                        'member_id' => Member::generateMemberId(),
                        'member_type' => 'independent', // Spouse is independent member
                        'membership_type' => 'permanent',
                        'campus_id' => $spouseCampusId, // Use spouse's selected campus
                        'community_id' => $spouseCommunityId, // Use spouse's selected community
                        'envelope_number' => $request->spouse_envelope_number,
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
                        // Sync welfare information so it is visible on both main member and spouse profiles
                        'orphan_status' => $member->spouse_orphan_status ?? 'not_orphan',
                        'disability_status' => $member->spouse_disability_status ?? false,
                        'disability_type' => $member->spouse_disability_type ?? null,
                        // Vulnerability is a family-level status, so mirror it to the spouse record
                        'vulnerable_status' => $member->vulnerable_status ?? false,
                        'vulnerable_type' => $member->vulnerable_type ?? null,
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
                if (
                    $smsEnabled &&
                    $member->marital_status === 'married' &&
                    $member->spouse_church_member === 'yes' &&
                    !empty($member->spouse_phone_number) &&
                    $spouseMember
                ) {

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
                            $enrollIdInt = (int) $enrollId;
                            if ($enrollIdInt >= 10 && $enrollIdInt <= 999) {
                                \Log::info("=== REGISTERING FAMILY TO BIOMETRIC DEVICE ===");
                                \Log::info("Main Member: {$member->full_name} (ID: {$enrollId})");

                                // Register main member
                                try {
                                    $registered = $zktecoService->registerUser(
                                        $enrollIdInt,
                                        (string) $enrollId,
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
                                                (int) $spouseEnrollId,
                                                (string) $spouseEnrollId,
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
                                $teenagers = $allChildren->filter(function ($child) {
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
                                            (int) $teenEnrollId,
                                            (string) $teenEnrollId,
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
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%")
                    ->orWhere('envelope_number', 'like', "%{$search}%");
            });
        }

        // Branch filter (for Usharika to filter by specific branch)
        if ($request->filled('campus_id') && auth()->check() && auth()->user()->getCampus() && auth()->user()->getCampus()->is_main_campus) {
            $query->where('campus_id', $request->campus_id);
        }

        // Membership type filter (permanent/temporary/all)
        if ($request->filled('membership_type') && $request->membership_type !== 'all') {
            $query->where('membership_type', $request->membership_type);
        } elseif (!$request->filled('type') && !$request->filled('archived') && !$request->filled('membership_type')) {
            // Default to 'all' if no specific filter is set
            $request->merge(['membership_type' => 'all']);
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

        // Apply community filter BEFORE executing query
        if ($request->filled('community_id')) {
            $query->where('community_id', $request->community_id);
        }

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
                $childrenQuery->whereHas('member', function ($q) use ($userCampus) {
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

        // Get communities for filter dropdown
        $communitiesQuery = \App\Models\Community::orderBy('name');

        // Filter communities by campus if user is not admin and belongs to a campus
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus) {
                if ($userCampus->is_main_campus) {
                    // Main campus can see all communities (optional, or limit to main campus/sub-campuses)
                    // For now, let's allow seeing all communities or filter by selected campus if present
                    if ($request->filled('campus_id')) {
                        $communitiesQuery->where('campus_id', $request->campus_id);
                    }
                } else {
                    // Branch users can only see their branch's communities
                    $communitiesQuery->where('campus_id', $userCampus->id);
                }
            }
        } elseif ($request->filled('campus_id')) {
            // If admin selects a campus, filter communities by that campus
            $communitiesQuery->where('campus_id', $request->campus_id);
        }

        $communities = $communitiesQuery->get();


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
        $allCount = $permanentCount + $temporaryCount;
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

        return view('members.view', compact('members', 'regions', 'districts', 'wards', 'tribes', 'children', 'campuses', 'communities', 'permanentCount', 'temporaryCount', 'allCount', 'childrenCount'));
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
            $member->load(['children', 'spouseMember', 'mainMember', 'campus', 'community', 'ahadiPledges']);

            // Get children - they belong to the main member (father/mother)
            // Children are linked to the member who created them (father or mother)
            // We need to show children on both father's and mother's pages

            // Create a consolidated collection of children from all family sources
            // (now handled by the all_children attribute in Member model)
            $children = $member->all_children;

            // Remove duplicates by ID (handled by all_children attribute too, but kept for clarity in older code context)
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
                'member_id_str' => $member->member_id,
                'member_type' => $member->member_type,
                'has_spouse_member_id' => !empty($member->spouse_member_id),
                'has_main_member' => !empty($member->mainMember),
                'children_count' => $children->count(),
                'all_children_count' => $member->all_children->count(),
                'member_children_count' => $member->children->count(),
                'spouse_children_count' => $member->spouseMember ? $member->spouseMember->children->count() : 0,
            ]);

            // Check Department Eligibility
            $allDepartments = \App\Models\Department::all();
            $memberDepartments = $member->departments()->pluck('departments.id')->toArray();
            $departmentStatus = [];

            foreach ($allDepartments as $dept) {
                $assigned = in_array($dept->id, $memberDepartments);
                $eligibility = $dept->checkEligibility($member);

                $departmentStatus[] = [
                    'department' => $dept,
                    'assigned' => $assigned,
                    'eligible' => $eligibility['eligible'],
                    'reason' => $eligibility['reason']
                ];
            }

            // Ahadi Pledges for current year
            $currentYear = date('Y');
            $ahadiPledges = $member->ahadiPledges()->where('year', $currentYear)->get();
            $itemTypes = \App\Models\AhadiPledge::ITEMS;

            // If request wants JSON (AJAX request), return JSON
            if (request()->wantsJson() || request()->ajax()) {
                $member->department_status = $departmentStatus;
                return response()->json($member);
            }

            // Otherwise return view
            return view('members.view-member', compact('member', 'children', 'departmentStatus', 'ahadiPledges', 'itemTypes'));
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
            'envelope_number' => 'nullable|string|max:100',
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

            // Baptism fields for member
            'baptism_status' => 'nullable|in:baptized,not_baptized',
            'baptism_date' => 'nullable|date|before:today',
            'baptism_location' => 'nullable|string|max:255',
            'baptized_by' => 'nullable|string|max:255',
            'baptism_certificate_number' => 'nullable|string|max:255',

            // Spouse Welfare Fields
            'spouse_orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'spouse_disability_status' => 'nullable',
            'spouse_disability_type' => 'nullable|string|max:255',

            // Social Welfare Fields
            'orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'disability_status' => 'nullable',
            'disability_type' => 'nullable|string|max:255',
            'vulnerable_status' => 'nullable',
            'vulnerable_type' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        // Check if wedding_date is being updated
        $oldWeddingDate = $member->wedding_date ? $member->wedding_date->format('Y-m-d') : null;
        $newWeddingDate = $validated['wedding_date'] ?? null;
        $weddingDateUpdated = $oldWeddingDate !== $newWeddingDate;

        // Handle booleans for update
        $validated['disability_status'] = $request->has('disability_status') || $request->disability_status == 1;
        $validated['vulnerable_status'] = $request->has('vulnerable_status') || $request->vulnerable_status == 1;
        $validated['spouse_disability_status'] = $request->has('spouse_disability_status') || $request->spouse_disability_status == 1;
        $validated['orphan_status'] = $request->orphan_status ?? 'not_orphan';
        $validated['spouse_orphan_status'] = $request->spouse_orphan_status ?? 'not_orphan';

        $member->update($validated);

        // Sync wedding date to spouse if it was updated
        // Sync wedding date and welfare info to spouse if they were updated
        if ($weddingDateUpdated || $request->has('spouse_orphan_status') || $request->has('spouse_disability_status')) {
            // If this member has a spouse (spouse is a church member)
            if ($member->spouse_member_id) {
                $spouseMember = Member::find($member->spouse_member_id);
                if ($spouseMember) {
                    $spouseUpdates = [];
                    if ($weddingDateUpdated) {
                        $spouseUpdates['wedding_date'] = $newWeddingDate;
                    }

                    // Sync welfare fields if present in request
                    if ($request->has('spouse_orphan_status')) {
                        $spouseUpdates['orphan_status'] = $request->spouse_orphan_status;
                    }
                    if ($request->has('spouse_disability_status')) {
                        $spouseUpdates['disability_status'] = $request->has('spouse_disability_status') || $request->spouse_disability_status == 1;
                        $spouseUpdates['disability_type'] = $request->spouse_disability_type;
                    }
                    // Vulnerability is family-level; when updated on the main member, mirror it to the spouse
                    if ($request->has('vulnerable_status')) {
                        $spouseUpdates['vulnerable_status'] = $request->has('vulnerable_status') || $request->vulnerable_status == 1;
                        $spouseUpdates['vulnerable_type'] = $request->vulnerable_type;
                    }

                    if (!empty($spouseUpdates)) {
                        $spouseMember->update($spouseUpdates);
                        \Log::info('Spouse info synced to spouse member', [
                            'member_id' => $member->id,
                            'spouse_member_id' => $spouseMember->id,
                            'updates' => $spouseUpdates
                        ]);
                    }
                }
            }

            // If this member is a spouse (has a main member) - only sync wedding date back to main member
            // Welfare info usually flows from Head of House -> Spouse, not reverse for these specific fields in this context
            $mainMember = Member::where('spouse_member_id', $member->id)->first();
            if ($mainMember && $weddingDateUpdated) {
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
        $filename = 'members_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Member ID', 'Full Name', 'Phone', 'Email', 'Gender', 'Region', 'District', 'Ward']);

            $query = Member::query();
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('member_id', 'like', "%{$search}%");
                });
            }

            $query->orderBy('created_at', 'desc')->chunk(200, function ($rows) use ($handle) {
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
            // Social welfare fields for child
            'orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'disability_status' => 'nullable',
            'disability_type' => 'nullable|string|max:255',
            'vulnerable_status' => 'nullable',
            'vulnerable_type' => 'nullable|string|max:255',
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
                'orphan_status' => $request->orphan_status ?? 'not_orphan',
                'disability_status' => $request->has('disability_status') || $request->disability_status == 1,
                'disability_type' => $request->disability_type,
                'vulnerable_status' => $request->has('vulnerable_status') || $request->vulnerable_status == 1,
                'vulnerable_type' => $request->vulnerable_type,
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
     * Show a single child profile
     */
    public function showChild(\App\Models\Child $child)
    {
        $child->load('campus');
        return view('children.show', compact('child'));
    }

    /**
     * Show child edit form
     */
    public function editChild(\App\Models\Child $child)
    {
        return back()->with('error', 'Child editing is not yet implemented.');
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
            'orphan_status' => ['nullable', Rule::in(['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])],
            'disability_status' => 'nullable',
            'disability_type' => 'nullable|string|max:255',
            'vulnerable_status' => 'nullable',
            'vulnerable_type' => 'nullable|string|max:255',
        ]);

        try {
            $childData = [
                'full_name' => $request->full_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'orphan_status' => $request->orphan_status ?? 'not_orphan',
                'disability_status' => $request->has('disability_status') || $request->disability_status == 1,
                'disability_type' => $request->disability_type,
                'vulnerable_status' => $request->has('vulnerable_status') || $request->vulnerable_status == 1,
                'vulnerable_type' => $request->vulnerable_type,
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

    /**
     * View members living outside the main church area
     * Accessible to Pastor/Admin only
     */
    public function outsideMainArea(Request $request)
    {
        // Check if user is Pastor or Admin
        if (!auth()->check() || (!auth()->user()->isAdmin() && auth()->user()->role !== 'pastor')) {
            abort(403, 'Unauthorized access. Only Pastor and Admin can view this page.');
        }

        // Get main campus to determine main area region
        $mainCampus = \App\Models\Campus::where('is_main_campus', true)->first();
        $mainCampusRegion = $mainCampus ? $mainCampus->region : null;

        // Query for adult members living outside main area
        // Members with residence_region set (different from main campus region or any region if main campus region is null)
        $membersQuery = Member::query();

        if ($mainCampusRegion) {
            // If main campus has a region, find members with residence_region different from main campus
            $membersQuery->whereNotNull('residence_region')
                ->where('residence_region', '!=', $mainCampusRegion);
        } else {
            // If main campus doesn't have region set, find any member with residence_region set
            $membersQuery->whereNotNull('residence_region');
        }

        // Filter by region if provided
        if ($request->filled('region')) {
            $membersQuery->where('residence_region', $request->region);
        }

        // Apply campus filtering if user is not admin
        if (!auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus) {
                if ($userCampus->is_main_campus) {
                    $userCampus->load('subCampuses');
                    $campusIds = [$userCampus->id];
                    $campusIds = array_merge($campusIds, $userCampus->subCampuses->pluck('id')->toArray());
                    $membersQuery->whereIn('campus_id', $campusIds);
                } else {
                    $membersQuery->where('campus_id', $userCampus->id);
                }
            }
        }

        $members = $membersQuery->with(['campus', 'community'])
            ->orderByRaw('residence_region IS NULL, residence_region')
            ->orderBy('full_name')
            ->paginate(20);
        $members->appends($request->query());

        // Query for children living outside main area (only those under 18)
        $childrenQuery = \App\Models\Child::where('lives_outside_main_area', true)
            ->with(['member.campus', 'member.community'])
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18');

        // Filter children by region if provided
        if ($request->filled('region')) {
            $childrenQuery->where('region', $request->region);
        }

        // Apply campus filtering for children through their parents
        if (!auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus && !$userCampus->is_main_campus) {
                $childrenQuery->whereHas('member', function ($q) use ($userCampus) {
                    $q->where('campus_id', $userCampus->id);
                });
            }
        }

        $children = $childrenQuery->orderByRaw('region IS NULL, region')
            ->orderBy('full_name')
            ->get();

        // Get children over 18 who live outside main area - these should be treated as adults
        $adultChildrenQuery = \App\Models\Child::where('lives_outside_main_area', true)
            ->with(['member.campus', 'member.community'])
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18');

        // Filter adult children by region if provided
        if ($request->filled('region')) {
            $adultChildrenQuery->where('region', $request->region);
        }

        // Apply campus filtering for adult children through their parents
        if (!auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus && !$userCampus->is_main_campus) {
                $adultChildrenQuery->whereHas('member', function ($q) use ($userCampus) {
                    $q->where('campus_id', $userCampus->id);
                });
            }
        }

        $adultChildren = $adultChildrenQuery->orderByRaw('region IS NULL, region')
            ->orderBy('full_name')
            ->get();

        // Ensure adultChildren is always a collection (even if empty)
        if (!$adultChildren) {
            $adultChildren = collect();
        }

        // Get distinct regions for filter dropdown
        $memberRegions = Member::whereNotNull('residence_region')
            ->when($mainCampusRegion, function ($q) use ($mainCampusRegion) {
                $q->where('residence_region', '!=', $mainCampusRegion);
            })
            ->distinct()
            ->pluck('residence_region')
            ->filter()
            ->sort()
            ->values();

        $childRegions = \App\Models\Child::where('lives_outside_main_area', true)
            ->whereNotNull('region')
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')
            ->distinct()
            ->pluck('region')
            ->filter()
            ->sort()
            ->values();

        // Also include regions from adult children
        $adultChildRegions = \App\Models\Child::where('lives_outside_main_area', true)
            ->whereNotNull('region')
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')
            ->distinct()
            ->pluck('region')
            ->filter()
            ->sort()
            ->values();

        // Merge and get unique regions
        $allRegions = $memberRegions->merge($childRegions)->merge($adultChildRegions)->unique()->sort()->values();

        // Get Tanzania locations data for region dropdown
        $tanzaniaLocations = [];
        $locationsPath = public_path('data/tanzania-locations.json');
        if (file_exists($locationsPath)) {
            $content = file_get_contents($locationsPath);
            if ($content !== false) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $tanzaniaLocations = $decoded;
                }
            }
        }

        return view('members.outside-main-area', compact(
            'members',
            'children',
            'adultChildren',
            'allRegions',
            'mainCampusRegion',
            'tanzaniaLocations'
        ));
    }

    /**
     * Send bulk SMS to members living outside the main church area
     */
    public function sendSmsToOutsideMembers(Request $request)
    {
        // Check if user is Pastor or Admin
        if (!auth()->check() || (!auth()->user()->isAdmin() && auth()->user()->role !== 'pastor')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:160',
            'region' => 'nullable|string',
        ]);

        $message = $validated['message'];
        $region = $validated['region'];

        // 1. Get recipients (Adult Members)
        $mainCampus = \App\Models\Campus::where('is_main_campus', true)->first();
        $mainCampusRegion = $mainCampus ? $mainCampus->region : null;

        $membersQuery = Member::query();
        if ($mainCampusRegion) {
            $membersQuery->whereNotNull('residence_region')->where('residence_region', '!=', $mainCampusRegion);
        } else {
            $membersQuery->whereNotNull('residence_region');
        }

        if ($region) {
            $membersQuery->where('residence_region', $region);
        }

        // Apply campus filtering for non-admins
        if (!auth()->user()->isAdmin()) {
            $userLink = auth()->user();
            $userCampus = $userLink->getCampus();
            if ($userCampus) {
                if ($userCampus->is_main_campus) {
                    $userCampus->load('subCampuses');
                    $campusIds = array_merge([$userCampus->id], $userCampus->subCampuses->pluck('id')->toArray());
                    $membersQuery->whereIn('campus_id', $campusIds);
                } else {
                    $membersQuery->where('campus_id', $userCampus->id);
                }
            }
        }

        $adultMembers = $membersQuery->whereNotNull('phone_number')->pluck('phone_number')->toArray();

        // 2. Get recipients (Adult Children/Dependents 18+)
        $adultChildrenQuery = \App\Models\Child::where('lives_outside_main_area', true)
            ->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18');

        if ($region) {
            $adultChildrenQuery->where('region', $region);
        }

        if (!auth()->user()->isAdmin()) {
            $userCampus = auth()->user()->getCampus();
            if ($userCampus && !$userCampus->is_main_campus) {
                $adultChildrenQuery->whereHas('member', function ($q) use ($userCampus) {
                    $q->where('campus_id', $userCampus->id);
                });
            }
        }

        $adultChildrenPhones = $adultChildrenQuery->whereNotNull('phone_number')->pluck('phone_number')->toArray();

        // Combine and unique
        $recipients = array_unique(array_merge($adultMembers, $adultChildrenPhones));
        $recipients = array_values(array_filter($recipients)); // Remove empty values and re-index

        if (empty($recipients)) {
            return response()->json(['success' => false, 'message' => 'No members found with valid phone numbers.']);
        }

        // 3. Send SMS
        $smsService = app(SmsService::class);
        $successCount = 0;
        $failCount = 0;

        foreach ($recipients as $phone) {
            if ($smsService->send($phone, $message)) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "SMS sending process completed. Sent: $successCount, Failed: $failCount",
            'recipients_count' => count($recipients)
        ]);
    }



    // Lookup member by envelope number
    public function lookup(Request $request)
    {
        $envelopeNumber = $request->query('envelope_number');

        if (!$envelopeNumber) {
            return response()->json(['exists' => false]);
        }

        $member = Member::where('envelope_number', $envelopeNumber)->first();

        if ($member) {
            return response()->json([
                'exists' => true,
                'name' => $member->full_name,
                'id' => $member->id
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Extend temporary membership.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function extendMembership(Request $request, Member $member)
    {
        // Check permission
        if (!auth()->user()->hasPermission('members.edit') && !auth()->user()->isAdmin()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You do not have permission to extend membership.'], 403);
            }
            abort(403, 'You do not have permission to extend membership.');
        }

        // Validate request
        $request->validate([
            'duration_months' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($member->membership_type !== 'temporary') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only temporary members can be extended.'], 422);
            }
            return back()->with('error', 'Only temporary members can be extended.');
        }

        try {
            DB::beginTransaction();

            $currentEndDate = $member->membership_end_date ? \Carbon\Carbon::parse($member->membership_end_date) : now();

            // If currently expired, start from today. If active, add to current end date.
            if ($currentEndDate->isPast()) {
                $startDate = now();
            } else {
                $startDate = $currentEndDate;
            }

            $extensionMonths = (int) $request->duration_months;
            $newEndDate = $startDate->copy()->addMonths($extensionMonths);

            // Update member
            $member->membership_end_date = $newEndDate;
            // If status was expired, set back to active
            if ($member->membership_status === 'expired') {
                $member->membership_status = 'active';
            }
            // Update total duration (approximate)
            $member->membership_duration_months = ($member->membership_duration_months ?? 0) + $extensionMonths;

            $member->save();

            // Log the activity (if you have an activity log table, otherwise just Log facade)
            \Log::info("Membership extended for member {$member->id} by {$extensionMonths} months. New end date: {$newEndDate->toDateString()}");

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Membership extended successfully.',
                    'new_end_date' => $newEndDate->format('M d, Y'),
                    'new_status' => ucfirst($member->membership_status)
                ]);
            }

            return back()->with('success', 'Membership extended successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Membership extension failed: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to extend membership.'], 500);
            }
            return back()->with('error', 'Failed to extend membership.');
        }
    }
}