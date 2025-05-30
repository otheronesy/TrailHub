<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NikaTalkService;

class NikaTalkController extends Controller
{
    protected $slackService;

    public function __construct(NikaTalkService $slackService)
    {
        $this->slackService = $slackService;
    }

    public function sendMessage(Request $request)
    {
        $channel = $request->input('channel');
        $text = $request->input('text');

        $result = $this->slackService->sendMessage($channel, $text);

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['message']], 500);
        }

        return response()->json($result);
    }

    public function getMessages(Request $request)
    {
        $channel = $request->input('channel');

        $result = $this->slackService->getMessages($channel);

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['message']], 500);
        }

        return response()->json($result);
    }

    public function updateMessage(Request $request)
    {
        $channel = $request->input('channel');
        $ts = $request->input('ts');
        $text = $request->input('text');

        $result = $this->slackService->updateMessage($channel, $ts, $text);

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['message']], 500);
        }

        return response()->json($result);
    }

    public function deleteMessage(Request $request)
    {
        $channel = $request->input('channel');
        $ts = $request->input('ts');

        $result = $this->slackService->deleteMessage($channel, $ts);

        if (!empty($result['error'])) {
            return response()->json(['error' => $result['message']], 500);
        }

        return response()->json($result);
    }
}
