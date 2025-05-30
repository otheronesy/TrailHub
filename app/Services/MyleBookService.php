<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MyleBookService
{
    public function lookup($word)
    {
        $base = config('services.mylebook.base_url');
        $key = config('services.mylebook.api_key');

        $response = Http::get("{$base}{$word}?key={$key}");

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => 'Unable to fetch definition',
            'status' => $response->status()
        ];
    }
}
