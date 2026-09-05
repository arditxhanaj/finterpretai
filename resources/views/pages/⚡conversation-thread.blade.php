<?php

use App\Enums\Sender;
use App\Models\Conversation;
use Livewire\Component;

new class extends Component
{
    public Conversation $conversation;

    public string $body = '';

    public function send(): void
    {
        $this->validate(['body' => ['required', 'string', 'max:2000']]);

        $this->conversation->messages()->create([
            'sender' => Sender::Staff,
            'body' => $this->body,
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        $this->body = '';
    }
}; ?>

{{-- staff --}}
<x-chat-thread :conversation="$conversation" :viewer="\App\Enums\Sender::Staff" class="h-full" />
