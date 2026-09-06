<?php

namespace App\Jobs;

use App\Enums\Sender;
use App\Enums\TranslationStatus;
use App\Events\MessageSent;
use App\Models\Message;
use App\Services\Translator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TranslateMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function handle(Translator $translator): void
    {
        $locale = $this->message->conversation->client->locale ?? 'English';

        [$from, $to] = $this->message->sender === Sender::Client
            ? [$locale, 'Albanian']
            : ['Albanian', $locale];

        try {
            $this->message->body_translated = $translator->translate($this->message->body, $from, $to);
            $this->message->translation_status = TranslationStatus::Completed;
        } catch (\Throwable $e) {
            $this->message->translation_status = TranslationStatus::Failed;
            report($e);
        }

        $this->message->save();

        broadcast(new MessageSent($this->message));
    }
}
