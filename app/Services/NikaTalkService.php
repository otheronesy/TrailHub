<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NikaTalkService
{
    public function sendMessage(string $channel, string $message, string $token = null)
    {
        $token = $token ?? config('services.slack.user_token');

        $response = Http::withToken($token)
            ->post(config('services.slack.api_base_url') . 'chat.postMessage', [
                'channel' => $channel,
                'text' => $message,
            ]);

        return $this->handleResponse($response);
    }

    public function getMessages(string $channel, string $token = null)
    {
        $token = $token ?? config('services.slack.user_token');

        $response = Http::withToken($token)
            ->get(config('services.slack.api_base_url') . 'conversations.history', [
                'channel' => $channel,
            ]);

        return $this->handleResponse($response);
    }

    public function updateMessage(string $channel, string $ts, string $message, string $token = null)
    {
        $token = $token ?? config('services.slack.user_token');

        $response = Http::withToken($token)
            ->post(config('services.slack.api_base_url') . 'chat.update', [
                'channel' => $channel,
                'ts' => $ts,
                'text' => $message,
            ]);

        return $this->handleResponse($response);
    }

    public function deleteMessage(string $channel, string $ts, string $token = null)
    {
        $token = $token ?? config('services.slack.user_token');

        $response = Http::withToken($token)
            ->post(config('services.slack.api_base_url') . 'chat.delete', [
                'channel' => $channel,
                'ts' => $ts,
            ]);

        return $this->handleResponse($response);
    }

    private function handleResponse($response)
    {
        if ($response->successful() && $response->json('ok')) {
            return $response->json();
        }

        Log::error('Slack API Error', ['response' => $response->body()]);

        return [
            'error' => true,
            'message' => $response->json('error') ?? 'Unknown error',
        ];
    }
}
