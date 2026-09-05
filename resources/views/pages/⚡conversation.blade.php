<?php

use App\Enums\Sender;
use App\Models\Conversation;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Events\MessageSent;
use Livewire\Attributes\On;


new #[Layout('layouts.chat')] class extends Component
{
    public Conversation $conversation;

    public string $body = '';

    public function send(): void
    {
        $this->validate(['body' => ['required', 'string', 'max:2000']]);

        $message = $this->conversation->messages()->create([
            'sender' => Sender::Client,
            'body' => $this->body,
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));

        $this->body = '';
    }

    #[On('echo:conversations.{conversation.ulid},.message.sent')]
    public function onMessageSent(): void
    {
        //
    }
}; ?>

{{-- client --}}
<x-chat-thread :conversation="$conversation" :viewer="\App\Enums\Sender::Client" class="h-full"/>
