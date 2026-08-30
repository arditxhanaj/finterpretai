<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome')->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::view('dashboard', 'dashboard')->name('dashboard');
// });

Route::get('/c/{conversation}', fn() => view('welcome'))
    ->middleware('signed')
    ->name('conversation.show');

require __DIR__.'/settings.php';
