@props(['conversation', 'viewer'])

<div {{ $attributes->class('flex min-h-0 flex-col gap-4') }}>
    <div class="shrink-0">
        <flux:heading size="lg">{{ $conversation->subject }}</flux:heading>
        <flux:separator variant="subtle" class="mt-4" />
    </div>

    <div class="min-h-0 flex-1 space-y-3 overflow-y-auto pr-2">
        @foreach ($conversation->messages as $message)
            <div
                wire:key="msg-{{ $message->id }}"
                class="flex {{ $message->sender === $viewer ? 'justify-end' : 'justify-start' }}"
            >
                <div class="max-w-[75%] rounded-lg px-4 py-2 {{ $message->sender === $viewer
                    ? 'bg-blue-600 text-white'
                    : 'bg-neutral-200 text-neutral-900 dark:bg-neutral-700 dark:text-neutral-100' }}">
                    {{ $message->body }}
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit="send" class="flex shrink-0 gap-2">
        <flux:input wire:model="body" class="flex-1" placeholder="Type a message…" />
        <flux:button type="submit" variant="primary">Send</flux:button>
    </form>
</div>
