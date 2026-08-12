<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function ask(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'message' => 'I am ready to answer your questions.',
                'status' => 'ready',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }
}