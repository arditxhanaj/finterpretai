<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::livewire('/c/{conversation}', 'pages::conversation')
    ->middleware('signed')
    ->name('conversation.show');

require __DIR__.'/settings.php';
