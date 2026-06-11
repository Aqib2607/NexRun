<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register successfully', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!'
    ]);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'user' => ['id', 'first_name', 'email'],
                     'token'
                 ]
             ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john.doe@example.com'
    ]);
});

test('user can login successfully', function () {
    $user = User::factory()->create([
        'email' => 'jane.doe@example.com',
        'password' => bcrypt('Password123!')
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'jane.doe@example.com',
        'password' => 'Password123!'
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'user',
                     'token'
                 ]
             ]);
});

test('login fails with incorrect password', function () {
    $user = User::factory()->create([
        'email' => 'jane.doe@example.com',
        'password' => bcrypt('Password123!')
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'jane.doe@example.com',
        'password' => 'WrongPassword!'
    ]);

    $response->assertStatus(401);
});

test('authenticated user can fetch profile', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->getJson('/api/v1/auth/me');

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'email' => $user->email
             ]);
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->postJson('/api/v1/auth/logout');

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'message' => 'Logged out successfully.'
             ]);

    $this->assertCount(0, $user->tokens);
});
