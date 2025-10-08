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
            'phone_number' => ['required','string','max:20','regex:/^\+255[0-9]{9,15}$/'],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',

            'education_level' => ['nullable', Rule::in(['primary','secondary','high_level','certificate','diploma','bachelor_degree','masters','phd','professor','not_studied'])],
            'profession' => 'required|string|max:100',

            // Guardian for temporary members
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

            // Spouse info fields (only if permanent & father/mother & spouse_alive is yes)
            'spouse_alive' => ['nullable', Rule::in(['yes','no'])],
            'spouse_full_name' => 'nullable|required_if:spouse_alive,yes|string|max:255',
            'spouse_date_of_birth' => 'nullable|required_if:spouse_alive,yes|date|before:today',
            'spouse_education_level' => ['nullable','required_if:spouse_alive,yes', Rule::in(['primary','secondary','high_level','certificate','diploma','bachelor_degree','masters','phd','professor','not_studied'])],
            'spouse_profession' => 'nullable|required_if:spouse_alive,yes|string|max:100',
            'spouse_nida_number' => 'nullable|string|max:20',
            'spouse_email' => 'nullable|email|max:255',
            'spouse_phone_number' => ['nullable','required_if:spouse_alive,yes','string','max:20','regex:/^\+255[0-9]{9,15}$/'],
            'spouse_tribe' => 'nullable|required_if:spouse_alive,yes|string|max:100',
            'spouse_other_tribe' => 'nullable|required_if:spouse_tribe,Other|string|max:100',
        ];
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
            // Handle avatar upload
            $profilePicturePath = null;
            if ($request->hasFile('avatar')) {
                $profilePicturePath = $request->file('avatar')->store('members/avatars', 'public');
            }

            // Generate unique member ID
            $memberId = Member::generateMemberId();

            // Create member
            \Log::info('=== CREATING MEMBER ===');
            \Log::info('Member ID to be created: ' . $memberId);
            
            $memberData = [
                'member_id' => $memberId,
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
                'profile_picture' => $profilePicturePath,
                'spouse_alive' => $request->spouse_alive,
                'spouse_full_name' => $request->spouse_full_name,
                'spouse_date_of_birth' => $request->spouse_date_of_birth,
                'spouse_education_level' => $request->spouse_education_level,
                'spouse_profession' => $request->spouse_profession,
                'spouse_nida_number' => $request->spouse_nida_number,
                'spouse_email' => $request->spouse_email,
                'spouse_phone_number' => $request->spouse_phone_number,
                'spouse_tribe' => $request->spouse_tribe,
                'spouse_other_tribe' => $request->spouse_other_tribe,
            ];
            
            \Log::info('Member data to be saved:', $memberData);
            
            $member = Member::create($memberData);
            
            \Log::info('=== MEMBER CREATED SUCCESSFULLY ===');
            \Log::info('Created member ID: ' . $member->id);
            \Log::info('Created member data:', $member->toArray());

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

    if ($request->wantsJson()) {
        return response()->json($members);
    }

    return view('members.view', compact('members', 'regions', 'districts', 'wards', 'tribes', 'archivedMembers'));
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
        return response()->json($member->load('children'));
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
        'email' => 'sometimes|required|email|max:255',
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
    $member->delete();
    return response()->json(['success' => true, 'message' => 'Member deleted successfully']);
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

}