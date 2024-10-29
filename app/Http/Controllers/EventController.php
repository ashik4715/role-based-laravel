<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Services\Requestor\JWTRequest;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agent_id = JWTRequest::getAgentId();

        // Retrieve events with notes (stored as JSON)
        $events = Event::where('agent_id', $agent_id)
            ->get(['id', 'event_title', 'notes', 'created_at', 'updated_at']);

        $formattedEvents = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'event_title' => $event->event_title,
                'notes' => $event->notes,
                'created_at' => Carbon::parse($event->created_at)->format('d-m-Y'),
                'updated_at' => Carbon::parse($event->updated_at)->format('d-m-Y'),
            ];
        });

        return response()->json([
            'message' => 'Events with notes retrieved successfully',
            'events' => $formattedEvents,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $agent_id = JWTRequest::getAgentId();

        $validatedData = $request->validate([
            'event_title' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
            'notes.*.name' => 'nullable|string',
            'notes.*.phone_number' => 'nullable|string',
            'notes.*.address' => 'nullable|string',
            'notes.*.team_leader_name' => 'nullable|string',
            'notes.*.others' => 'nullable|string',
        ]);

        $validatedData['agent_id'] = $agent_id;

        // Store the event with notes as JSON
        $event = Event::create($validatedData);

        return response()->json([
            'message' => 'Event saved successfully',
            'event' => $event
        ], 201);
    }

    public function update(Request $request, Event $event)
    {
        $agent_id = JWTRequest::getAgentId();

        if ($event->agent_id !== $agent_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'event_title' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
            'notes.*.name' => 'nullable|string',
            'notes.*.phone_number' => 'nullable|string',
            'notes.*.address' => 'nullable|string',
            'notes.*.team_leader_name' => 'nullable|string',
            'notes.*.others' => 'nullable|string',
        ]);

        $validatedData['agent_id'] = $agent_id;

        // Update the event with notes as JSON
        $event->update($validatedData);

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => $event
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $agent_id = JWTRequest::getAgentId();

        if ($event->agent_id !== $agent_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ], 200);
    }
}
