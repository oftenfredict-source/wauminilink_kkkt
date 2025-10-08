<?php

namespace App\Http\Controllers;

use App\Models\Celebration;
use App\Models\Member;
use Illuminate\Http\Request;

class CelebrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Celebration::query();

        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('celebrant_name', 'like', "%{$s}%")
                  ->orWhere('venue', 'like', "%{$s}%")
                  ->orWhere('type', 'like', "%{$s}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('celebration_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('celebration_date', '<=', $request->date('to'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $celebrations = $query->orderBy('celebration_date', 'desc')->paginate(10);
        $celebrations->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json($celebrations);
        }

        $totalMembers = Member::count();
        return view('services.celebrations.page', compact('celebrations', 'totalMembers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'celebration_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'celebrant_name' => 'nullable|string|max:255',
            'expected_guests' => 'nullable|integer|min:0',
            'budget' => 'nullable|numeric|min:0',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $celebration = Celebration::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Celebration saved successfully',
            'celebration' => $celebration,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Celebration $celebration)
    {
        return response()->json($celebration);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Celebration $celebration)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'celebration_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'celebrant_name' => 'nullable|string|max:255',
            'expected_guests' => 'nullable|integer|min:0',
            'budget' => 'nullable|numeric|min:0',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $celebration->update($validated);

        return response()->json(['success' => true, 'message' => 'Celebration updated successfully', 'celebration' => $celebration]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Celebration $celebration)
    {
        $celebration->delete();
        return response()->json(['success' => true, 'message' => 'Celebration deleted successfully']);
    }

    /**
     * Export celebrations to CSV
     */
    public function exportCsv(Request $request)
    {
        $filename = 'celebrations_' . now()->format('Ymd_His') . '.csv';
        $celebrations = Celebration::orderBy('celebration_date', 'desc')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function() use ($celebrations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Title','Celebrant Name','Type','Celebration Date','Start Time','End Time','Venue','Expected Guests','Budget','Description','Special Requests','Notes']);
            foreach ($celebrations as $c) {
                fputcsv($handle, [
                    $c->title,
                    $c->celebrant_name,
                    $c->type,
                    optional($c->celebration_date)->format('Y-m-d'),
                    $c->start_time,
                    $c->end_time,
                    $c->venue,
                    $c->expected_guests,
                    $c->budget,
                    $c->description,
                    $c->special_requests,
                    $c->notes,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }
}
