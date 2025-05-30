<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JatrAIlService;

class JatrAIlController extends Controller
{
    protected $openAI;

    public function __construct(JatrAIlService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function chat(Request $request)
    {
        $user = $request->user();
        $tokenRecord = $user->oauthTokenFor('openai');

        if (!$tokenRecord) {
            // fallback to global token from env
            $token = config('services.openai.api_key');
        } else {
            $token = $tokenRecord->access_token;
        }

        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        $messages = [
            ['role' => 'user', 'content' => $userMessage]
        ];

        $response = $this->openAI->chat($messages, $token);

        if (isset($response['error'])) {
            return response()->json(['error' => $response['message']], 500);
        }

        $reply = $response['choices'][0]['message']['content'] ?? '';

        return response()->json([
            'reply' => $reply,
            'full_response' => $response,
        ]);
    }
}
