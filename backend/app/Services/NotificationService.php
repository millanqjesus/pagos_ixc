<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notify($email, $message): bool
    {
        $url = env('AUTHORIZER_URL', 'https://66ad1f3cb18f3614e3b478f5.mockapi.io/v1').'/send';

        try {

          $payload = [
            "email"   => $email,
            "message" => $message
          ];
            
            $response = Http::timeout(3)->post($url, $payload);

            if (! $response->successful()) {
                Log::warning('Authorizer HTTP error', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            $json = $response->json();

            // Si el mock devuelve un campo explícito 'authorized', lo respetamos
            if (isset($json['authorized'])) {
                return (bool) $json['authorized'];
            }

            // Si devuelve 'status' con texto evaluable
            if (isset($json['status'])) {
                $status = strtolower((string) $json['status']);
                return in_array($status, ['approved', 'authorized', 'ok', 'true', 'success'], true);
            }

            // Para endpoints tipo mockapi (que devuelven 201 con el recurso creado),
            // consideramos respuesta HTTP exitosa como autorización por defecto.
            return true;

        } catch (\Exception $e) {
            Log::error('Authorizer request failed', [
                'url' => $url,
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }
}