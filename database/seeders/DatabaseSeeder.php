<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criando o usuário Admin
        $admin = User::create([
            'id' => \Str::uuid(),  // Gera um UUID para o usuário
            'name' => 'Admin', // Nome do admin
            'email' => 'admin@lagoasanta.mg.bov.br', // E-mail do admin
            'email_verified_at' => now(), // E-mail verificado
            'password' => Hash::make('*mg2011tiuserBR'), // Senha do admin
            'cpf' => 'admin', // CPF (se necessário)
            'status' => 'active', // Status do usuário (ativo)
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $test = User::create([
            'id' => \Str::uuid(),  // Gera um UUID para o usuário
            'name' => 'Tester', // Nome do admin
            'email' => 'test@lagoasanta.mg.bov.br', // E-mail do admin
            'email_verified_at' => now(), // E-mail verificado
            'password' => Hash::make('*mg2011tiuserBR'), // Senha do admin
            'cpf' => 'test', // CPF (se necessário)
            'status' => 'pending', // Status do usuário (ativo)
            'created_at' => now(),
            'updated_at' => now(),
        ]);



        // Criando os papéis
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador com todas as permissões.',
            'level' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Role::create([
            'name' => 'manager',
            'description' => 'Gerente com permissões limitadas.',
            'level' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $guestRole = Role::create([
            'name' => 'guest',
            'description' => 'Visitante com permissões mínimas.',
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Atribuindo o papel 'admin' ao usuário criado
        $admin->roles()->attach($adminRole);
        $test->roles()->attach($guestRole);
    }
}
