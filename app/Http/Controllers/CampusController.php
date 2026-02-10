<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CampusController extends Controller
{
    /**
     * Display a listing of campuses
     */
    public function index()
    {
        $campuses = Campus::with(['parent', 'subCampuses.members', 'subCampuses.memberChildren'])
            ->orderBy('is_main_campus', 'desc')
            ->orderBy('name')
            ->get();

        return view('campuses.index', compact('campuses'));
    }

    /**
     * Show the form for creating a new campus
     */
    public function create()
    {
        // Get main campuses for sub campus selection
        $mainCampuses = Campus::where('is_main_campus', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('campuses.create', compact('mainCampuses'));
    }

    /**
     * Store a newly created campus
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'campus_type' => 'required|in:main,sub',
            'parent_id' => 'nullable|required_if:campus_type,sub|exists:campuses,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $isMainCampus = $request->campus_type === 'main';
            $parentId = $isMainCampus ? null : $request->parent_id;

            // Validate that only one main campus exists
            if ($isMainCampus && Campus::where('is_main_campus', true)->exists()) {
                return redirect()->back()
                    ->with('error', 'A main campus already exists. You can only have one main campus.')
                    ->withInput();
            }

            // Generate campus code
            $code = Campus::generateCode($request->name, $parentId);

            $campus = Campus::create([
                'name' => $request->name,
                'code' => $code,
                'description' => $request->description,
                'address' => $request->address,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'parent_id' => $parentId,
                'is_main_campus' => $isMainCampus,
                'is_active' => true,
            ]);

            Log::info('Campus created', [
                'campus_id' => $campus->id,
                'name' => $campus->name,
                'code' => $campus->code,
                'type' => $isMainCampus ? 'main' : 'sub',
            ]);

            return redirect()->route('campuses.index')
                ->with('success', 'Campus created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating campus', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create campus: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified campus
     */
    public function show(Campus $campus)
    {
        $campus->load(['parent', 'subCampuses', 'members', 'memberChildren.member', 'users', 'communities.churchElder.member', 'evangelismLeader.member']);
        
        // Get members and children for this campus (ordered)
        $campusMembers = $campus->members()->orderBy('full_name')->get();
        $campusChildMembers = $campus->memberChildren()->with('member')->orderBy('full_name')->get();
        
        $memberCount = $campusMembers->count();
        $childMemberCount = $campusChildMembers->count();
        $subCampusMemberCount = 0;
        $subCampusChildMemberCount = 0;
        
        foreach ($campus->subCampuses as $subCampus) {
            $subCampus->load('memberChildren');
            $subCampusMemberCount += $subCampus->members()->count();
            $subCampusChildMemberCount += $subCampus->memberChildren()->count();
        }
        
        $totalMembers = $memberCount + $childMemberCount + $subCampusMemberCount + $subCampusChildMemberCount;

        // Get available evangelism leaders for this campus
        $availableEvangelismLeaders = \App\Models\Leader::where('campus_id', $campus->id)
            ->where('position', 'evangelism_leader')
            ->where('is_active', true)
            ->with('member')
            ->get();

        return view('campuses.show', compact('campus', 'campusMembers', 'campusChildMembers', 'memberCount', 'childMemberCount', 'subCampusMemberCount', 'subCampusChildMemberCount', 'totalMembers', 'availableEvangelismLeaders'));
    }

    /**
     * Show the form for editing the specified campus
     */
    public function edit(Campus $campus)
    {
        $mainCampuses = Campus::where('is_main_campus', true)
            ->where('is_active', true)
            ->where('id', '!=', $campus->id)
            ->orderBy('name')
            ->get();

        // Get available evangelism leaders for this campus
        $availableEvangelismLeaders = \App\Models\Leader::where('campus_id', $campus->id)
            ->where('position', 'evangelism_leader')
            ->where('is_active', true)
            ->with('member')
            ->get();

        return view('campuses.edit', compact('campus', 'mainCampuses', 'availableEvangelismLeaders'));
    }

    /**
     * Update the specified campus
     */
    public function update(Request $request, Campus $campus)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $campus->update([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'region' => $request->region,
                'district' => $request->district,
                'ward' => $request->ward,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : $campus->is_active,
            ]);

            Log::info('Campus updated', [
                'campus_id' => $campus->id,
                'name' => $campus->name,
            ]);

            return redirect()->route('campuses.index')
                ->with('success', 'Campus updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating campus', [
                'campus_id' => $campus->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update campus: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified campus (soft delete)
     */
    public function destroy(Campus $campus)
    {
        try {
            // Check if campus has members
            if ($campus->members()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete campus with existing members. Please transfer members first.');
            }

            // Check if campus has sub campuses
            if ($campus->subCampuses()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete campus with sub campuses. Please delete or transfer sub campuses first.');
            }

            $campus->delete();

            Log::info('Campus deleted', [
                'campus_id' => $campus->id,
                'name' => $campus->name,
            ]);

            return redirect()->route('campuses.index')
                ->with('success', 'Campus deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting campus', [
                'campus_id' => $campus->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete campus: ' . $e->getMessage());
        }
    }

    /**
     * Get campuses for dropdown (AJAX)
     */
    public function getCampuses(Request $request)
    {
        $query = Campus::where('is_active', true);

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

        $campuses = $query->orderBy('name')->get();

        return response()->json($campuses);
    }

    /**
     * Assign evangelism leader to campus
     */
    public function assignEvangelismLeader(Request $request, Campus $campus)
    {
        $validator = Validator::make($request->all(), [
            'evangelism_leader_id' => 'nullable|exists:leaders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Validate that the leader belongs to this campus and is an evangelism leader
            if ($request->evangelism_leader_id) {
                $leader = \App\Models\Leader::find($request->evangelism_leader_id);
                if (!$leader || $leader->campus_id !== $campus->id || $leader->position !== 'evangelism_leader') {
                    return redirect()->back()
                        ->with('error', 'Invalid evangelism leader selected.');
                }
            }

            $campus->update([
                'evangelism_leader_id' => $request->evangelism_leader_id ?: null,
            ]);

            Log::info('Evangelism leader assigned to campus', [
                'campus_id' => $campus->id,
                'leader_id' => $request->evangelism_leader_id,
            ]);

            return redirect()->route('campuses.show', $campus)
                ->with('success', $request->evangelism_leader_id ? 'Evangelism leader assigned successfully!' : 'Evangelism leader removed successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning evangelism leader', [
                'campus_id' => $campus->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to assign evangelism leader: ' . $e->getMessage());
        }
    }
}

