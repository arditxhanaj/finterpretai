<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConversationRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

class ConversationController extends Controller
{
    public function store(StoreConversationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $client = Client::updateOrCreate(['email' => $validated['email']], ['name' => $validated['name'], 'locale' => $validated['locale']]);

        $conversation = $client->conversations()->create(['subject' => $validated['subject']]);

        return response()->json([
            'url' => URL::temporarySignedRoute('conversation.show', now()->addDays(30), ['conversation' => $conversation]),
        ], 201);
    }
}
