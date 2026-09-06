<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard.conversations' : 'login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/conversations', 'pages::conversations')->name('dashboard.conversations');
    Route::livewire('/conversations/{conversation}', 'pages::conversation-thread')->name('dashboard.conversations.show');
});

Route::livewire('/c/{conversation}', 'pages::conversation')
    ->middleware('signed')
    ->name('conversation.show');

require __DIR__.'/settings.php';
