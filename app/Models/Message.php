<?php

namespace App\Models;

use App\Enums\Sender;
use App\Enums\TranslationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['conversation_id', 'sender', 'body'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    protected function casts(): array
    {
        return [
            'sender' => Sender::class,
            'translation_status' => TranslationStatus::class,
            'translated_at' => 'datetime',
        ];
    }
}
