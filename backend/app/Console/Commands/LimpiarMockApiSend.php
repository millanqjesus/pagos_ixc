<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LimpiarMockApiSend extends Command
{
    protected $signature = 'mockapisend:limpiar';
    protected $description = 'Elimina todos los registros del recurso /send en MockAPI';

    public function handle()
    {
        $baseUrl = 'https://66ad1f3cb18f3614e3b478f5.mockapi.io/v1/send';

        $this->info('Obteniendo registros de MockAPI/send...');

        $response = Http::get($baseUrl);

        if (!$response->successful()) {
            $this->error('Error al obtener registros: ' . $response->status());
            return;
        }

        $registros = $response->json();

        foreach ($registros as $registro) {
            $id = $registro['id'] ?? null;

            if (!$id) {
              $this->warn("Registro sin ID, se omite");
              continue;
            }
            $deleteUrl = "{$baseUrl}/{$id}";

            $deleteResponse = Http::delete($deleteUrl);

            if ($deleteResponse->successful()) {
                $this->info("✅ Eliminado registro ID {$id}");
                Log::info("MockAPI: Eliminado registro {$id}");
            } else {
                $this->warn("⚠️ Error al eliminar ID {$id}");
                Log::warning("MockAPI: Falló eliminación {$id}", [
                    'status' => $deleteResponse->status(),
                    'body' => $deleteResponse->body(),
                ]);
            }
        }

        $this->info('🧹 Limpieza completada.');
    }
}
