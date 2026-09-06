<?php

use App\Enums\Sender;
use App\Enums\TranslationStatus;
use App\Jobs\TranslateMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    // The queue runs synchronously in tests, so without this the translation
    // job would make a real (billable) call to the Claude API.
    Queue::fake();
});

it('lets a client send a message', function () {
    $conversation = Conversation::factory()->create();

    Livewire::test('pages::conversation', ['conversation' => $conversation])
        ->set('body', 'Guten Tag, ich habe eine Frage.')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('body', '');

    $message = $conversation->messages()->sole();

    expect($message->sender)->toBe(Sender::Client)
        ->and($message->body)->toBe('Guten Tag, ich habe eine Frage.')
        ->and($message->translation_status)->toBe(TranslationStatus::Pending)
        ->and($message->body_translated)->toBeNull();
});

it('lets staff reply', function () {
    $conversation = Conversation::factory()->create();

    Livewire::actingAs(User::factory()->create())
        ->test('pages::conversation-thread', ['conversation' => $conversation])
        ->set('body', 'Pershendetje!')
        ->call('send')
        ->assertHasNoErrors();

    expect($conversation->messages()->sole()->sender)->toBe(Sender::Staff);
});

it('queues a translation for every message sent', function () {
    $conversation = Conversation::factory()->create();

    Livewire::test('pages::conversation', ['conversation' => $conversation])
        ->set('body', 'Muss ich mich registrieren?')
        ->call('send');

    Queue::assertPushed(TranslateMessage::class);
});

it('refuses to send an empty message', function () {
    $conversation = Conversation::factory()->create();

    Livewire::test('pages::conversation', ['conversation' => $conversation])
        ->set('body', '')
        ->call('send')
        ->assertHasErrors(['body' => 'required']);

    expect($conversation->messages()->count())->toBe(0);
});

it('refuses to send a message over the length limit', function () {
    $conversation = Conversation::factory()->create();

    Livewire::test('pages::conversation', ['conversation' => $conversation])
        ->set('body', str_repeat('a', 2001))
        ->call('send')
        ->assertHasErrors(['body' => 'max']);

    expect($conversation->messages()->count())->toBe(0);
});

it('stamps last_message_at so the dashboard can order by it', function () {
    $conversation = Conversation::factory()->create(['last_message_at' => null]);

    Livewire::test('pages::conversation', ['conversation' => $conversation])
        ->set('body', 'Hallo')
        ->call('send');

    expect($conversation->fresh()->last_message_at)->not->toBeNull();
});

it('returns messages oldest first', function () {
    $conversation = Conversation::factory()->create();

    $first = Message::factory()->for($conversation)->create(['body' => 'first']);
    $second = Message::factory()->for($conversation)->create(['body' => 'second']);
    $third = Message::factory()->for($conversation)->create(['body' => 'third']);

    // Updating an older row must not move it in the ordering.
    $first->update(['body_translated' => 'perkthim']);

    expect($conversation->fresh()->messages->pluck('id')->all())
        ->toBe([$first->id, $second->id, $third->id]);
});

it('deletes messages when their conversation is deleted', function () {
    $conversation = Conversation::factory()->create();
    Message::factory()->for($conversation)->count(3)->create();

    $conversation->delete();

    expect(Message::count())->toBe(0);
});
