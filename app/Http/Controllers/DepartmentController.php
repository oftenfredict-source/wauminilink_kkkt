<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Member;
use App\Models\Child;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::withCount('members')->get();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'nullable|array',
            'criteria.min_age' => 'nullable|integer',
            'criteria.max_age' => 'nullable|integer',
            'criteria.gender' => 'nullable|in:male,female',
            'criteria.marital_status' => 'nullable|string',
            'criteria.has_children' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $user = auth()->user();
        $department->load([
            'members' => function ($query) use ($user) {
                // Scope to campus for Evangelism Leaders
                if ($user->isEvangelismLeader()) {
                    $campus = $user->getCampus();
                    if ($campus) {
                        $query->where('members.campus_id', $campus->id);
                    }
                }
            },
            'children' => function ($query) use ($user) {
                // Scope to campus for children too
                if ($user->isEvangelismLeader()) {
                    $campus = $user->getCampus();
                    if ($campus) {
                        $query->where('children.campus_id', $campus->id);
                    }
                }
            }
        ]);
        $members = $department->members;
        $children = $department->children;

        // Attach eligibility status to each member for display
        foreach ($members as $member) {
            $member->eligibility_status = $department->checkEligibility($member);
        }

        return view('admin.departments.show', compact('department', 'members', 'children'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'nullable|array',
            'criteria.min_age' => 'nullable|integer',
            'criteria.max_age' => 'nullable|integer',
            'criteria.gender' => 'nullable|in:male,female',
            'criteria.marital_status' => 'nullable|string',
            'criteria.has_children' => 'nullable|boolean',
        ]);

        // Fix boolean handling for checkbox often sent as string "1" or "on"
        if (isset($validated['criteria']['has_children'])) {
            $validated['criteria']['has_children'] = filter_var($validated['criteria']['has_children'], FILTER_VALIDATE_BOOLEAN);
        }

        $department->update($validated);

        return redirect()->route('departments.show', $department)->with('success', 'Department updated successfully.');
    }

    /**
     * Assign a member or child to the department.
     */
    /**
     * Assign a member or child to the department.
     */
    public function assignMember(Request $request, Department $department)
    {
        // General (Secretary) users cannot assign members
        if (auth()->user()->isSecretary()) {
            return back()->with('error', 'You do not have permission to assign members.');
        }

        $request->validate([
            'member_id' => 'nullable|required_without:child_id|exists:members,id',
            'child_id' => 'nullable|required_without:member_id|exists:children,id',
            'force' => 'sometimes|boolean',
        ]);

        if ($request->member_id) {
            $member = Member::findOrFail($request->member_id);

            // Check if already assigned
            if ($department->members()->where('department_member.member_id', $member->id)->exists()) {
                return back()->with('error', 'Member is already assigned to this department.');
            }

            // Validate eligibility
            $eligibility = $department->checkEligibility($member);

            if (!$eligibility['eligible'] && !$request->has('force')) {
                return back()->with('error', 'Eligibility Check Failed: ' . $eligibility['reason'] . ' (Use force assignment if necessary)');
            }

            $department->members()->attach($member->id, ['status' => 'active']);
            return back()->with('success', 'Member assigned successfully.');
        } else {
            $child = Child::findOrFail($request->child_id);

            // Check if already assigned
            if ($department->children()->where('department_member.child_id', $child->id)->exists()) {
                return back()->with('error', 'Child is already assigned to this department.');
            }

            // Validate eligibility for children
            $eligibility = $department->checkEligibility($child);

            if (!$eligibility['eligible'] && !$request->has('force')) {
                return back()->with('error', 'Eligibility Check Failed: ' . $eligibility['reason'] . ' (Use force assignment if necessary)');
            }

            $department->children()->attach($child->id, ['status' => 'active']);
            return back()->with('success', 'Child assigned successfully.');
        }
    }

    /**
     * Check eligibility via AJAX.
     */
    public function checkEligibility(Request $request, Department $department, Member $member)
    {
        $result = $department->checkEligibility($member);
        return response()->json($result);
    }

    /**
     * Suggst eligible members and children for the department.
     */
    public function suggest(Department $department)
    {
        $user = auth()->user();

        // Suggest Members
        $memberQuery = Member::query()->whereDoesntHave('departments', function ($q) use ($department) {
            $q->where('department_id', $department->id);
        });

        // Scope to campus for Evangelism Leaders
        if ($user->isEvangelismLeader()) {
            $campus = $user->getCampus();
            if ($campus) {
                $memberQuery->where('campus_id', $campus->id);
            }
        }

        $criteria = $department->criteria;

        if (isset($criteria['min_age'])) {
            $minDate = now()->subYears($criteria['min_age']);
            $memberQuery->where('date_of_birth', '<=', $minDate);
        }
        if (isset($criteria['max_age'])) {
            $maxDate = now()->subYears($criteria['max_age'] + 1)->addDay();
            $memberQuery->where('date_of_birth', '>=', $maxDate);
        }
        if (isset($criteria['gender']) && in_array($criteria['gender'], ['male', 'female'])) {
            $memberQuery->where('gender', $criteria['gender']);
        }
        if (isset($criteria['marital_status'])) {
            $memberQuery->where('marital_status', $criteria['marital_status']);
        }

        $candidates = $memberQuery->limit(50)->get();
        $memberSuggestions = $candidates->filter(function ($member) use ($department) {
            return $department->checkEligibility($member)['eligible'];
        })->map(function ($m) {
            $m->person_type = 'member';
            return $m;
        });

        // Suggest Children
        $childQuery = Child::query()->whereDoesntHave('departments', function ($q) use ($department) {
            $q->where('department_id', $department->id);
        });

        if ($user->isEvangelismLeader()) {
            $campus = $user->getCampus();
            if ($campus) {
                $childQuery->where('campus_id', $campus->id);
            }
        }

        // Apply same age/gender criteria to children if applicable
        if (isset($criteria['min_age'])) {
            $minDate = now()->subYears($criteria['min_age']);
            $childQuery->where('date_of_birth', '<=', $minDate);
        }
        if (isset($criteria['max_age'])) {
            $maxDate = now()->subYears($criteria['max_age'] + 1)->addDay();
            $childQuery->where('date_of_birth', '>=', $maxDate);
        }
        if (isset($criteria['gender']) && in_array($criteria['gender'], ['male', 'female'])) {
            $childQuery->where('gender', $criteria['gender']);
        }

        $childCandidates = $childQuery->limit(50)->get();
        $childSuggestions = $childCandidates->filter(function ($child) use ($department) {
            return $department->checkEligibility($child)['eligible'];
        })->map(function ($c) {
            $c->person_type = 'child';
            $c->age = \Carbon\Carbon::parse($c->date_of_birth)->age;
            return $c;
        });

        $allSuggestions = $memberSuggestions->concat($childSuggestions);

        return response()->json($allSuggestions->values());
    }

    /**
     * Remove member from department.
     */
    public function removeMember(Department $department, Member $member)
    {
        // General (Secretary) users cannot remove members
        if (auth()->user()->isSecretary()) {
            return back()->with('error', 'You do not have permission to remove members.');
        }

        $department->members()->detach($member->id);
        return back()->with('success', 'Member removed from department.');
    }

    /**
     * Remove child from department.
     */
    public function removeChild(Department $department, Child $child)
    {
        // General (Secretary) users cannot remove children
        if (auth()->user()->isSecretary()) {
            return back()->with('error', 'You do not have permission to remove children.');
        }

        $department->children()->detach($child->id);
        return back()->with('success', 'Child removed from department.');
    }
}
