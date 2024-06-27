<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Profile;

use App\Models\Laboratory;
use App\Models\Product;
use App\Models\Batch;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* Crear Perfil & Usuario */
        $admin = Profile::create(["name" => "Luis", "lastname" => "Cuiza"]);
        $user = Profile::create(["name" => "Alfredo", "lastname" => "Duran"]);
        User::create([
            'name' => 'admin',
            'role' => 'admin',
            'email' => 'admin@pharmacy.com',
            'password' => Hash::make('admin'),
            'profile_id' => $admin->id
        ]);
        User::create([
            'name' => 'user',
            'role' => 'user',
            'email' => 'user@pharmacy.com',
            'password' => Hash::make('user'),
            'profile_id' => $user->id
        ]);
        /* Crear LAboratorios */
        $laboratories = [
            ['name' => 'Laboratorio Alfa', 'phone' => '123456789', 'email' => 'contacto@alfa.com'],
            ['name' => 'Laboratorio Beta', 'phone' => '987654321', 'email' => 'contacto@beta.com'],
            ['name' => 'Laboratorio Gamma', 'phone' => '456123789', 'email' => 'contacto@gamma.com'],
            ['name' => 'Laboratorio Delta', 'phone' => '789321456', 'email' => 'contacto@delta.com'],
        ];
        foreach ($laboratories as $labData) {
            Laboratory::create($labData);
        }
    }
}
