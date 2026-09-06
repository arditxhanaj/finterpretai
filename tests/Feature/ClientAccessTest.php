<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\URL;

function signedLinkTo(Conversation $conversation, ?string $expiresAt = null): string
{
    return URL::temporarySignedRoute(
        'conversation.show',
        $expiresAt ? now()->parse($expiresAt) : now()->addDays(30),
        $conversation,
    );
}

it('lets a client into their conversation with a valid signed link', function () {
    $conversation = Conversation::factory()->create(['subject' => 'VAT registration']);

    $this->get(signedLinkTo($conversation))
        ->assertOk()
        ->assertSee('VAT registration');
});

it('rejects an unsigned link', function () {
    $conversation = Conversation::factory()->create();

    $this->get(route('conversation.show', $conversation))
        ->assertForbidden();
});

it('rejects a tampered signature', function () {
    $conversation = Conversation::factory()->create();

    // Appending to the signature always produces a different one, unlike
    // swapping a character, which can collide with the value already there.
    $tampered = signedLinkTo($conversation).'0';

    $this->get($tampered)->assertForbidden();
});

it('rejects an expired link', function () {
    $conversation = Conversation::factory()->create();

    $expired = URL::temporarySignedRoute(
        'conversation.show',
        now()->subMinute(),
        $conversation,
    );

    $this->get($expired)->assertForbidden();
});

it('404s for a conversation that does not exist', function () {
    $conversation = Conversation::factory()->create();
    $link = signedLinkTo($conversation);

    $conversation->delete();

    $this->get($link)->assertNotFound();
});

it('resolves the conversation by ulid, not by id', function () {
    $conversation = Conversation::factory()->create();

    expect(signedLinkTo($conversation))
        ->toContain($conversation->ulid)
        ->not->toContain("/c/{$conversation->id}?");
});
