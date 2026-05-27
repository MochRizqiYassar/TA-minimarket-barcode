<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {

    $user = User::factory()->create([
        'status' => 'active',
        'role' => 'admin',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertRedirect(route('admin.dashboard'));
});

test('users can not authenticate with invalid password', function () {

    $user = User::factory()->create([
        'status' => 'active',
        'role' => 'admin',
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {

    $user = User::factory()->create([
        'status' => 'active',
        'role' => 'admin',
    ]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();

    $response->assertRedirect('/');
});
