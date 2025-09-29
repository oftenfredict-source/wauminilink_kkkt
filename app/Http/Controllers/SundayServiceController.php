<?php

namespace App\Http\Controllers;

use App\Models\SundayService;
use App\Models\Member;
use Illuminate\Http\Request;

class SundayServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SundayService::query();

        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function($q) use ($s) {
                $q->where('theme', 'like', "%{$s}%")
                  ->orWhere('preacher', 'like', "%{$s}%")
                  ->orWhere('venue', 'like', "%{$s}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('service_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('service_date', '<=', $request->date('to'));
        }

        $services = $query->orderBy('service_date', 'desc')->paginate(10);
        $services->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json($services);
        }

        $totalMembers = Member::count();
        return view('services.sunday.page', compact('services', 'totalMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_date' => 'required|date|unique:sunday_services,service_date',
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'offerings_amount' => 'nullable|numeric|min:0',
            'scripture_readings' => 'nullable|string',
            'choir' => 'nullable|string|max:255',
            'announcements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $service = SundayService::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sunday service saved successfully',
            'service' => $service,
        ], 201);
    }

    public function show(SundayService $sundayService)
    {
        return response()->json($sundayService);
    }

    public function update(Request $request, SundayService $sundayService)
    {
        $validated = $request->validate([
            'service_date' => 'sometimes|required|date|unique:sunday_services,service_date,' . $sundayService->id,
            'theme' => 'nullable|string|max:255',
            'preacher' => 'nullable|string|max:255',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'offerings_amount' => 'nullable|numeric|min:0',
            'scripture_readings' => 'nullable|string',
            'choir' => 'nullable|string|max:255',
            'announcements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $sundayService->update($validated);

        return response()->json(['success' => true, 'message' => 'Sunday service updated successfully', 'service' => $sundayService]);
    }

    public function destroy(SundayService $sundayService)
    {
        $sundayService->delete();
        return response()->json(['success' => true, 'message' => 'Sunday service deleted successfully']);
    }

    public function exportCsv(Request $request)
    {
        $filename = 'sunday_services_' . now()->format('Ymd_His') . '.csv';
        $services = SundayService::orderBy('service_date', 'desc')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function() use ($services) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Service Date','Theme','Preacher','Start Time','End Time','Venue','Attendance','Offerings','Scripture Readings','Choir','Announcements','Notes']);
            foreach ($services as $s) {
                fputcsv($handle, [
                    optional($s->service_date)->format('Y-m-d'),
                    $s->theme,
                    $s->preacher,
                    $s->start_time,
                    $s->end_time,
                    $s->venue,
                    $s->attendance_count,
                    $s->offerings_amount,
                    $s->scripture_readings,
                    $s->choir,
                    $s->announcements,
                    $s->notes,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }
}


