<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login when opening dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('login page is accessible to guests', function () {
    $response = $this->get('/login');

    $response->assertOk()
        ->assertSee('RUP Intelligence')
        ->assertSee('Masuk');
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'secret-password',
    ]);

    $response = $this->post('/login', [
        'email' => 'user@example.com',
        'password' => 'secret-password',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

test('invalid credentials are rejected', function () {
    User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'secret-password',
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'user@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
