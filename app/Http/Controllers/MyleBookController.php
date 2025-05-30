<?php

namespace App\Http\Controllers;

use App\Services\MyleBookService;  // Update service name
use Illuminate\Http\Request;

class MyleBookController extends Controller  // Update controller name
{
    protected $dictionary;  // Update service reference

    public function __construct(MyleBookService $dictionary)  // Update service name
    {
        $this->dictionary = $dictionary;
    }

    public function define(Request $request)
    {
        $word = $request->input('word', 'example');
        $data = $this->dictionary->lookup($word);  // Update service method

        return response()->json($data);
    }
}
