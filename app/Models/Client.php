<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'email', 'locale'];

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
