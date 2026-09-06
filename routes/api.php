<?php

use App\Http\Controllers\ConversationController;
use Illuminate\Support\Facades\Route;

Route::post('/conversations', [ConversationController::class, 'store'])
    ->name('conversations.store');
