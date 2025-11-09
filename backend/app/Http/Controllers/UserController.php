<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function balance(User $user)
    {
        return response()->json([
            'status'  => 'sucesso',
            'data'    => [
                'balance'   => (float)$user->balance,
                'user_id'   => $user->id,
                'user_type' => $user->type
            ]
        ]);
    }
}