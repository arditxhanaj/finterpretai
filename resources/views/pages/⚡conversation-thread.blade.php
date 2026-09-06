<?php

use App\Enums\Sender;
use App\Models\Conversation;
use Livewire\Component;
use App\Events\MessageSent;
use Livewire\Attributes\On;
use App\Jobs\TranslateMessage;

new class extends Component
{
    public Conversation $conversation;

    public string $body = '';

    public function send(): void
    {
        $this->validate(['body' => ['required', 'string', 'max:2000']]);

        $message = $this->conversation->messages()->create([
            'sender' => Sender::Staff,
            'body' => $this->body,
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));
        TranslateMessage::dispatch($message)->afterResponse();

        $this->body = '';
    }


    #[On('echo:conversations.{conversation.ulid},.message.sent')]
    public function onMessageSent(): void
    {
        //
    }

}; ?>

{{-- staff --}}
<x-chat-thread :conversation="$conversation" :viewer="\App\Enums\Sender::Staff" class="h-full" />
