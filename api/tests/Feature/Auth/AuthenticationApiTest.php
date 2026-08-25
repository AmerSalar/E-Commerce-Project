<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Arr;

class AuthenticationApiTest extends TestCase
{
    use RefreshDatabase;
    public function test_guests_can_register(): void
    {
        $payload = [
            'name' => "Mohammed",
            'email' => "mohammed@gmail.com",
            'password' => "mo1234//",
            'password_confirmation' => "mo1234//",
        ];
        $response = $this->postJson('/api/register', $payload);

        // 201
        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'message',
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ]
                ]
            ]);

        $this->assertDatabaseHas(
            'users',
            Arr::only($payload, ['name', 'email'])
        );
    }
}
