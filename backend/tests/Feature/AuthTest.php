<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $userData = [
            'name'      => 'Test User',
            'cpf_cnpj'  => '12345678901',
            'email'     => 'test@example.com',
            'password'  => 'password',
            'type'      => 'comun',
            'balance'   => 0.00,
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'type' => 'comun',
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('password'),
            'type' => 'comun',
            'balance' => 0.00,
        ]);

        $credentials = [
            'email' => 'login@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status'
            ]);
    }
}
