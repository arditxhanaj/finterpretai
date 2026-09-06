<?php

use App\Enums\ConversationStatus;
use App\Models\Conversation;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\On;

new #[Title('Conversations')] class extends Component
{
    #[Computed]
    public function conversations()
    {
        return Conversation::query()
            ->with('client')
            ->withCount('messages')
            ->latest('last_message_at')
            ->get();
    }

    #[On('echo-private:conversations_dashboard,.conversation.created')]
    public function onConversationCreated(): void
    {
        //
    }
}; ?>

<section class="w-full">
    <flux:heading size="xl" level="1">Conversations</flux:heading>
    <flux:subheading>Client questions waiting for a reply</flux:subheading>

    <flux:separator variant="subtle" class="my-6" />

    <div class="flex flex-col gap-2">
        @forelse ($this->conversations as $conversation)
            <a
                href="{{ route('dashboard.conversations.show', $conversation) }}"
                wire:key="conversation-{{ $conversation->id }}"
                wire:navigate
                class="flex items-center gap-4 rounded-lg border border-neutral-200 p-4 transition hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-800"
            >
                <flux:avatar size="sm" :name="$conversation->client->name" />

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $conversation->client->name }}
                        </span>
                        <flux:badge size="sm" :color="$conversation->status === ConversationStatus::Open ? 'lime' : 'zinc'">
                            {{ $conversation->status->value }}
                        </flux:badge>
                    </div>

                    <p class="truncate text-sm text-neutral-600 dark:text-neutral-400">
                        {{ $conversation->subject }}
                    </p>
                </div>

                <div class="shrink-0 text-right text-xs text-neutral-500 dark:text-neutral-400">
                    <div>{{ $conversation->messages_count }} messages</div>
                    <div>{{ $conversation->last_message_at?->diffForHumans() ?? '—' }}</div>
                </div>
            </a>
        @empty
            <flux:callout>No conversations yet.</flux:callout>
        @endforelse
    </div>
</section>
