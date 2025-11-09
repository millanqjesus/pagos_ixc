<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'comun@demo.com'], [
            'name' => 'Usuario Común',
            'cpf_cnpj' => '12345678901',
            'password' => Hash::make('secret123'),
            'type' => 'comun',
            'balance' => 500,
        ]);

        User::updateOrCreate(['email' => 'lojista@demo.com'], [
            'name' => 'Loja Demo',
            'cpf_cnpj' => '12345678000199',
            'password' => Hash::make('secret123'),
            'type' => 'lojista',
            'balance' => 100,
        ]);
    }
}