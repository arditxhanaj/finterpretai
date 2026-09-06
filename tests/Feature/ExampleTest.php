<?php

use App\Models\User;

it('sends a guest hitting the root url to the login page', function () {
    $this->get(route('home'))
        ->assertRedirect(route('login', absolute: false));
});

it('sends a logged in user hitting the root url to their conversations', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertRedirect(route('dashboard.conversations', absolute: false));
});
