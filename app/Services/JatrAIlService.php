<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JatrAIlService
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = config('services.openai.api_base_url') . 'chat/completions';
    }

    public function chat($messages, $token, $model = 'gpt-4o-mini')
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ])->post($this->endpoint, [
            'model' => $model,
            'messages' => $messages,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => $response->body(),
        ];
    }
}
