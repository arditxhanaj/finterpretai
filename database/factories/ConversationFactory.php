<?php

namespace Database\Factories;

use App\Enums\ConversationStatus;
use App\Models\Client;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'subject' => fake()->sentence(4),
            'status' => ConversationStatus::Open,
        ];
    }
}
