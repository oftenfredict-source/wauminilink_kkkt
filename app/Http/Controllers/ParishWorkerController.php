<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ParishWorkerActivity;
use App\Models\ParishWorkerReport;
use App\Models\Member;
use App\Models\Campus;
use App\Models\CandleAction;
use Illuminate\Support\Facades\Auth;

class ParishWorkerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkPermission()
    {
        $user = Auth::user();
        if (!$user->isParishWorker() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index()
    {
        $this->checkPermission();
        $user = Auth::user();
        $campus = $user->getCampus();

        $recentActivities = ParishWorkerActivity::where('user_id', $user->id)
            ->orderBy('activity_date', 'desc')
            ->take(5)
            ->get();

        $recentReports = ParishWorkerReport::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total_activities' => ParishWorkerActivity::where('user_id', $user->id)->count(),
            'total_reports' => ParishWorkerReport::where('user_id', $user->id)->count(),
            'pending_activities' => ParishWorkerActivity::where('user_id', $user->id)->where('status', 'pending')->count(),
            'candles_on_hand' => CandleAction::where('action_type', 'purchase')->sum('quantity') - CandleAction::where('action_type', 'distribution')->sum('quantity'),
        ];

        return view('parish-worker.dashboard', compact('recentActivities', 'recentReports', 'stats', 'campus'));
    }

    public function activitiesIndex()
    {
        $this->checkPermission();
        $user = Auth::user();
        $activities = ParishWorkerActivity::where('user_id', $user->id)
            ->orderBy('activity_date', 'desc')
            ->paginate(15);

        return view('parish-worker.activities.index', compact('activities'));
    }

    public function createActivity()
    {
        $this->checkPermission();
        return view('parish-worker.activities.create');
    }

    public function storeActivity(Request $request)
    {
        $this->checkPermission();
        $user = Auth::user();
        $campus = $user->getCampus();

        $validated = $request->validate([
            'activity_type' => 'required|in:altar_cleanliness,womens_department,sunday_school,holy_communion,church_candles,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'activity_date' => 'required|date',
            'status' => 'required|in:completed,pending',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;
        $validated['campus_id'] = $campus ? $campus->id : null;

        ParishWorkerActivity::create($validated);

        return redirect()->route('parish-worker.activities.index')
            ->with('success', 'Activity recorded successfully.');
    }

    public function reportsIndex()
    {
        $this->checkPermission();
        $user = Auth::user();
        $reports = ParishWorkerReport::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('parish-worker.reports.index', compact('reports'));
    }

    public function createReport()
    {
        $this->checkPermission();
        return view('parish-worker.reports.create');
    }

    public function storeReport(Request $request)
    {
        $this->checkPermission();
        $user = Auth::user();
        $campus = $user->getCampus();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after_or_equal:report_period_start',
        ]);

        $validated['user_id'] = $user->id;
        $validated['campus_id'] = $campus ? $campus->id : null;
        $validated['submitted_at'] = now();
        $validated['status'] = 'submitted';

        ParishWorkerReport::create($validated);

        return redirect()->route('parish-worker.reports.index')
            ->with('success', 'Report submitted successfully.');
    }

    public function showReport(ParishWorkerReport $report)
    {
        $this->checkPermission();
        $user = Auth::user();

        if ($report->user_id !== $user->id && !$user->isAdmin() && !$user->isPastor()) {
            abort(403);
        }

        return view('parish-worker.reports.show', compact('report'));
    }

    public function candlesIndex()
    {
        $this->checkPermission();
        $user = Auth::user();

        $actions = CandleAction::with('campus')
            ->orderBy('action_date', 'desc')
            ->paginate(15);

        // Calculate stock
        $purchased = CandleAction::where('action_type', 'purchase')->sum('quantity');
        $distributed = CandleAction::where('action_type', 'distribution')->sum('quantity');
        $onHand = $purchased - $distributed;

        return view('parish-worker.candles.index', compact('actions', 'onHand', 'purchased', 'distributed'));
    }

    public function createCandleAction()
    {
        $this->checkPermission();
        $campuses = Campus::where('is_active', true)->get();
        return view('parish-worker.candles.create', compact('campuses'));
    }

    public function storeCandleAction(Request $request)
    {
        $this->checkPermission();
        $user = Auth::user();

        $validated = $request->validate([
            'action_type' => 'required|in:purchase,distribution',
            'campus_id' => 'required_if:action_type,distribution|nullable|exists:campuses,id',
            'quantity' => 'required|integer|min:1',
            'cost' => 'required_if:action_type,purchase|nullable|numeric|min:0',
            'action_date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;

        CandleAction::create($validated);

        return redirect()->route('parish-worker.candles.index')
            ->with('success', 'Candle ' . $request->action_type . ' recorded successfully.');
    }
}
