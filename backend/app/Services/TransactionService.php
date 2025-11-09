<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private AuthorizationService $authService,
        private NotificationService $notificationService
    ) {}

    public function transfer(int $remitterId, int $receiverId, float $amount): Transaction
    {
        return DB::transaction(function () use ($remitterId, $receiverId, $amount) {
            $rem = User::lockForUpdate()->findOrFail($remitterId);
            $rec = User::lockForUpdate()->findOrFail($receiverId);

            if ($rem->isLojista()) {
                return $this->fail($rem, $rec, $amount, 'Lojista no puede enviar');
            }
            if ($rem->balance < $amount) {
                return $this->fail($rem, $rec, $amount, 'Saldo insuficiente');
            }

            $payload = [
              'remitente'     => User::select('name', 'cpf_cnpj', 'email', 'type', 'balance')->find($remitterId),
              'destinatario'  => User::select('name', 'cpf_cnpj', 'email', 'type')->find($receiverId),
              'amount'        => $amount
            ];

            // Autorización externa
            if (!$this->authService->authorize($payload)) {
                return $this->fail($rem, $rec, $amount, 'Transacción no autorizada');
            }

            // Debitar y acreditar
            $rem->balance -= $amount;
            $rec->balance += $amount;
            $rem->save();
            $rec->save();

            $tx = Transaction::create([
                'remitter_id' => $rem->id,
                'receiver_id' => $rec->id,
                'amount'      => $amount,
                'status'      => 'exito',
            ]);

            // Notificar (mock)
            $this->notificationService->notify($rem->email, "Has enviado R$ {$amount} a {$rec->name}");
            $this->notificationService->notify($rec->email, "Has recibido R$ {$amount} de {$rem->name}");

            return $tx;
        });
    }

    private function fail(User $rem, User $rec, float $amount, string $reason): Transaction
    {
        // No se toca el balance, se registra fallo
        return Transaction::create([
            'remitter_id' => $rem->id,
            'receiver_id' => $rec->id,
            'amount'      => $amount,
            'status'      => 'fallo',
            'reason'      => $reason,
        ]);
    }
}