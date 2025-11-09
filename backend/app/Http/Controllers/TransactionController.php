<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransactionService;
use App\Services\AuthorizationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TransactionController extends Controller
{
    public function __construct(
      private TransactionService $service,
      private AuthorizationService $authorize
    ) {}

    public function transfer(TransferRequest $request)
    {
      DB::beginTransaction();
      try {

        $data = $request->all();
        $tx = $this->service->transfer(
            (int)$data['remitter_id'],
            (int)$data['receiver_id'],
            (float)$data['amount']
        );

        // $payload = [
        //   'remitente' => User::select('name', 'cpf_cnpj', 'email', 'type', 'balance')->find($data['remitter_id'])
        // ];
        
        

        // $authorized = $this->authorize->authorize($payload);

        // if (! $authorized) {
        //     DB::rollBack();
        //     $result->message = 'Transferencia no autorizada';
        //     return $result;
        // }

        DB::commit();
        $statusCode = $tx->status === 'exito' ? 201 : 400;
        return response()->json($tx, $statusCode);

      } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Transfer failed', ['error' => $e->getMessage()]);
        $result->message = 'Error interno al procesar la transferencia';
        return $result;
      }
      
    }
}