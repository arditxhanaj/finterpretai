<?php

namespace Database\Factories;

use App\Enums\Sender;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender' => Sender::Client,
            'body' => fake()->sentence(),
        ];
    }

    public function fromStaff(): static
    {
        return $this->state(fn () => ['sender' => Sender::Staff]);
    }
}
