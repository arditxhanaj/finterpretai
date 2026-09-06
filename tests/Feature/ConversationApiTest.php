<?php

use App\Models\Client;
use App\Models\Conversation;

beforeEach(function () {
    config()->set('services.integration.key', 'test-integration-key');
});

function createConversation(array $overrides = [], ?string $key = 'test-integration-key')
{
    return test()->postJson('/api/conversations', array_merge([
        'name' => 'Hans Müller',
        'email' => 'hans@example.de',
        'locale' => 'German',
        'subject' => 'VAT registration',
    ], $overrides), $key ? ['X-API-Key' => $key] : []);
}

it('creates a client and conversation and returns a working link', function () {
    $response = createConversation()->assertCreated();

    $url = $response->json('url');

    expect($url)->toBeString();
    expect(Client::where('email', 'hans@example.de')->exists())->toBeTrue();

    $conversation = Conversation::sole();
    expect($conversation->subject)->toBe('VAT registration');

    // The returned link must actually open the conversation.
    $this->get($url)->assertOk();
});

it('rejects a request with no api key', function () {
    createConversation(key: null)->assertForbidden();

    expect(Client::count())->toBe(0);
});

it('rejects a request with the wrong api key', function () {
    createConversation(key: 'not-the-key')->assertForbidden();

    expect(Client::count())->toBe(0);
});

it('validates the payload', function () {
    test()->postJson('/api/conversations', [], ['X-API-Key' => 'test-integration-key'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'locale']);
});

it('rejects a malformed email', function () {
    createConversation(['email' => 'not-an-email'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('treats subject as optional', function () {
    createConversation(['subject' => null])->assertCreated();

    expect(Conversation::sole()->subject)->toBeNull();
});

it('reuses the client but starts a new conversation on repeat calls', function () {
    createConversation()->assertCreated();
    createConversation(['name' => 'Hans M.'])->assertCreated();

    expect(Client::count())->toBe(1)
        ->and(Conversation::count())->toBe(2)
        ->and(Client::sole()->name)->toBe('Hans M.');
});
