<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Profile;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
    }
}
