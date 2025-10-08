<?php

namespace App\Http\Controllers;

use App\Models\SpecialEvent;
use App\Models\Member;
use Illuminate\Http\Request;

class SpecialEventController extends Controller
{
    public function index(Request $request)
    {
        // Get total members count for the layout
        $totalMembers = Member::count();
        
        $query = SpecialEvent::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s){
                $q->where('title','like',"%$s%")
                  ->orWhere('speaker','like',"%$s%")
                  ->orWhere('venue','like',"%$s%");
            });
        }
        if ($request->filled('from')) $query->whereDate('event_date','>=',$request->from);
        if ($request->filled('to')) $query->whereDate('event_date','<=',$request->to);
        $events = $query->orderBy('event_date','desc')->paginate(10);
        if ($request->wantsJson()) return response()->json($events);
        
        return view('services.special.page', compact('events', 'totalMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_date' => 'nullable|date',
            'title' => 'nullable|string|max:255',
            'speaker' => 'nullable|string|max:255',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'budget_amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $event = SpecialEvent::create($validated);
        return response()->json(['success'=>true,'event'=>$event]);
    }

    public function show(SpecialEvent $specialEvent)
    {
        return response()->json($specialEvent);
    }

    public function update(Request $request, SpecialEvent $specialEvent)
    {
        $validated = $request->validate([
            'event_date' => 'nullable|date',
            'title' => 'nullable|string|max:255',
            'speaker' => 'nullable|string|max:255',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'venue' => 'nullable|string|max:255',
            'attendance_count' => 'nullable|integer|min:0',
            'budget_amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $specialEvent->update($validated);
        
        return response()->json(['success'=>true,'event'=>$specialEvent]);
    }

    public function destroy(SpecialEvent $specialEvent)
    {
        $specialEvent->delete();
        return response()->json(['success'=>true]);
    }
}



