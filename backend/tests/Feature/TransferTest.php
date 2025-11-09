<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Criar usuarios
        $this->remitter = User::factory()->create([
            'type' => 'comun',
            'balance' => 100.00,
        ]);
        $this->receiver = User::factory()->create([
            'type' => 'lojista',
            'balance' => 0.00,
        ]);
    }

    public function test_transferencia_exitosa_autorizada()
    {
        Http::fake([
            '*' => Http::response(['authorized' => true], 200),
        ]);

        $payload = [
            'remitter_id' => $this->remitter->id,
            'receiver_id' => $this->receiver->id,
            'amount' => 50.00,
        ];

        $token = $this->remitter->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/transfer', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'exito']);

        $this->assertEquals(50.00, $this->remitter->fresh()->balance);
        $this->assertEquals(50.00, $this->receiver->fresh()->balance);
    }

    public function test_transferencia_rechazada_por_autorizador()
    {
        Http::fake([
            '*' => Http::response(['authorized' => false], 200),
        ]);

        $payload = [
            'remitter_id' => $this->remitter->id,
            'receiver_id' => $this->receiver->id,
            'amount' => 50.00,
        ];

        $token = $this->remitter->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/transfer', $payload);

        $response->assertStatus(400)
            ->assertJsonFragment(['status' => 'fallo']);

        $this->assertEquals(100.00, $this->remitter->fresh()->balance);
        $this->assertEquals(0.00, $this->receiver->fresh()->balance);
    }

    public function test_lojista_no_puede_transferir()
    {
        $lojistaRemitter = User::factory()->create([
            'type' => 'lojista',
            'balance' => 100.00,
        ]);
        $receiver = User::factory()->create([
            'type' => 'comun',
            'balance' => 0.00,
        ]);

        $payload = [
            'remitter_id' => $lojistaRemitter->id,
            'receiver_id' => $receiver->id,
            'amount' => 50.00,
        ];

        $token = $lojistaRemitter->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/transfer', $payload);

        $response->assertStatus(400) // O 400, dependiendo de la implementación del backend
            ->assertJsonFragment(['status' => 'fallo', 'reason' => 'Lojista no puede enviar']); // Mensaje de error esperado

        $this->assertEquals(100.00, $lojistaRemitter->fresh()->balance);
        $this->assertEquals(0.00, $receiver->fresh()->balance);
    }

    public function test_transferencia_rechazada_por_saldo_insuficiente()
    {
        $this->remitter = User::factory()->create([
            'type' => 'comun',
            'balance' => 30.00, // Saldo insuficiente para la transferencia de 50.00
        ]);
        $this->receiver = User::factory()->create([
            'type' => 'lojista',
            'balance' => 0.00,
        ]);

        $payload = [
            'remitter_id' => $this->remitter->id,
            'receiver_id' => $this->receiver->id,
            'amount' => 50.00,
        ];

        $token = $this->remitter->createToken('api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/transfer', $payload);

        $response->assertStatus(400)
            ->assertJsonFragment(['status' => 'fallo', 'reason' => 'Saldo insuficiente']); // Mensaje de error esperado

        $this->assertEquals(30.00, $this->remitter->fresh()->balance);
        $this->assertEquals(0.00, $this->receiver->fresh()->balance);
    }
}
