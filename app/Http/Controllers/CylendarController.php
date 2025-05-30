<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CylendarService;

class CylendarController extends Controller
{
    protected $cylendarService;

    public function __construct(CylendarService $cylendarService)
    {
        $this->cylendarService = $cylendarService;
    }

    public function getEvents(Request $request)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        $events = $this->cylendarService->getEvents($tokenRecord->access_token);

        return response()->json($events);
    }

    public function getEvent(Request $request, $eventId)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        $event = $this->cylendarService->getEvent($eventId, $tokenRecord->access_token);

        return response()->json($event);
    }

    public function createEvent(Request $request)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        $validated = $request->validate([
            'summary' => 'required|string',
            'start.dateTime' => 'required_without:start.date|string',
            'start.date' => 'required_without:start.dateTime|string',
            'start.timeZone' => 'required_if:start.dateTime,!=,null|string',
            'end.dateTime' => 'required_without:end.date|string',
            'end.date' => 'required_without:end.dateTime|string',
            'end.timeZone' => 'required_if:end.dateTime,!=,null|string',
        ]);

        $start = isset($validated['start']['dateTime'])
            ? ['dateTime' => $validated['start']['dateTime'], 'timeZone' => $validated['start']['timeZone']]
            : ['date' => $validated['start']['date']];

        $end = isset($validated['end']['dateTime'])
            ? ['dateTime' => $validated['end']['dateTime'], 'timeZone' => $validated['end']['timeZone']]
            : ['date' => $validated['end']['date']];

        $eventData = [
            'summary' => $validated['summary'],
            'start' => $start,
            'end' => $end,
        ];

        try {
            $event = $this->cylendarService->createEvent($eventData, $tokenRecord->access_token);
            return response()->json($event);
        } catch (\Exception $e) {
            \Log::error('Google Calendar create event error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create event', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateEvent(Request $request, $eventId)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        $validated = $request->validate([
            'summary' => 'sometimes|string',
            'start.dateTime' => 'sometimes|required_with:end.dateTime|string',
            'start.date' => 'sometimes|required_with:end.date|string',
            'start.timeZone' => 'sometimes|required_if:start.dateTime,!=,null|string',
            'end.dateTime' => 'sometimes|required_with:start.dateTime|string',
            'end.date' => 'sometimes|required_with:start.date|string',
            'end.timeZone' => 'sometimes|required_if:end.dateTime,!=,null|string',
        ]);

        $start = null;
        $end = null;

        if (isset($validated['start']['dateTime'])) {
            $start = [
                'dateTime' => $validated['start']['dateTime'],
                'timeZone' => $validated['start']['timeZone'] ?? 'Asia/Manila',
            ];
            $end = [
                'dateTime' => $validated['end']['dateTime'],
                'timeZone' => $validated['end']['timeZone'] ?? 'Asia/Manila',
            ];
        } elseif (isset($validated['start']['date'])) {
            $start = ['date' => $validated['start']['date']];
            $end = ['date' => $validated['end']['date']];
        }

        $data = [];
        if (isset($validated['summary'])) {
            $data['summary'] = $validated['summary'];
        }
        if ($start) {
            $data['start'] = $start;
        }
        if ($end) {
            $data['end'] = $end;
        }

        try {
            $event = $this->cylendarService->updateEvent($eventId, $data, $tokenRecord->access_token);
            return response()->json($event);
        } catch (\Exception $e) {
            \Log::error('Google Calendar update event error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update event', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteEvent(Request $request, $eventId)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        try {
            $this->cylendarService->deleteEvent($eventId, $tokenRecord->access_token);
            return response()->json(['message' => 'Event deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Google Calendar delete event error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete event', 'message' => $e->getMessage()], 500);
        }
    }
}
