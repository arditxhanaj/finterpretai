<?php

use App\Enums\Sender;
use App\Models\Conversation;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.chat')] class extends Component
{
    public Conversation $conversation;

    public string $body = '';

    public function send(): void
    {
        $this->validate(['body' => ['required', 'string', 'max:2000']]);

        $this->conversation->messages()->create([
            'sender' => Sender::Client,
            'body' => $this->body,
        ]);

        $this->body = '';
    }
}; ?>

<div class="flex h-screen flex-col">
    <header class="shrink-0 border-b p-4">
        {{ $conversation->subject }}
    </header>

    <div class="flex-1 overflow-y-auto p-4 space-y-3">
        @foreach ($conversation->messages as $message)
            <div wire:key="{{ $message->id }}"
                 class="flex {{ $message->sender === Sender::Client ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[75%] rounded-lg px-4 py-2 {{ $message->sender === Sender::Client ? 'bg-blue-600 text-white' : 'bg-gray-100' }}">
                    {{ $message->body }}
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit="send" class="shrink-0 border-t p-4 flex gap-2">
        <flux:input wire:model="body" class="flex-1" />
        <flux:button type="submit" variant="primary">Send</flux:button>
    </form>
</div>
