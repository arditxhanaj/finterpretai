<?php

namespace App\Models;

use App\Enums\Sender;
use App\Enums\TranslationStatus;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $conversation_id
 * @property Sender $sender
 * @property string $body
 * @property string|null $body_translated
 * @property TranslationStatus $translation_status
 * @property Carbon|null $translated_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Conversation $conversation
 */
class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    protected $fillable = ['conversation_id', 'sender', 'body'];

    /**
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sender' => Sender::class,
            'translation_status' => TranslationStatus::class,
            'translated_at' => 'datetime',
        ];
    }
}
