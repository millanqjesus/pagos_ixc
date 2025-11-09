<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'cpf_cnpj' => $data['cpf_cnpj'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
            'balance' => 0,
        ]);
        return response()->json(['id' => $user->id], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'erro',
                'message' => 'Credenciais inválidas'
            ], 401);
        }
        
        $token = $user->createToken('api')->plainTextToken;
        
        return response()->json([
            'status' => 'sucesso',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'type' => $user->type,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]
        ], 200);
    }
}