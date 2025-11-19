<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primeiro, cria os Parâmetros (Obrigatório)
        $this->call(ParametroTemplateSeeder::class);

        // 2. Cria o Admin Principal (Sempre fixo)
        User::firstOrCreate(
            ['email' => 'admin@sisdise.com'],
            [
                'name' => 'Admin do Sistema',
                'password' => Hash::make('admin123'),
                'tipo' => 'Administrador'
            ]
        );

        // 3. Cria os Dados de Demonstração (Avaliadores, Empresas, Diagnósticos)
        $this->call(DemoSeeder::class);
    }
}
