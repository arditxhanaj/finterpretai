<?php

namespace App\Services;

use Anthropic\Client;
use Anthropic\Messages\TextBlock;

class Translator
{
    public function translate(string $text, string $from, string $to): string
    {
        $client = new Client(apiKey: config('services.anthropic.key'));

        $message = $client->messages->create(
            model: 'claude-opus-5',
            maxTokens: 2000,
            outputConfig: ['effort' => 'low'],
            system: [[
                'type' => 'text',
                'text' => 'You translate messages between an accounting firm and its clients. '
                    ."Translate from {$from} to {$to}. Preserve tone, keep accounting and tax "
                    .'terminology accurate, and keep numbers and dates exactly as written. '
                    .'Reply with the translation only — no preamble, no notes, no quotes.',
            ]],
            messages: [
                ['role' => 'user', 'content' => $text],
            ],
        );

        foreach ($message->content as $block) {
            if ($block instanceof TextBlock) {
                return trim($block->text);
            }
        }

        throw new \RuntimeException('No text block in response.');
    }
}
