<?php

namespace Database\Seeders;

use Illuminate\Support\Str; 
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
        /* Crear Productos */
        $products = [
            ['name' => 'Paracetamol', 'price' => 10.50, 'barcode' => '1234567890123', 'description' => 'Analgésico y antipirético', 'laboratory_id' => 1],
            ['name' => 'Ibuprofeno', 'price' => 15.75, 'barcode' => '9876543210987', 'description' => 'Antiinflamatorio y analgésico', 'laboratory_id' => 2],
            ['name' => 'Aspirina', 'price' => 8.25, 'barcode' => '4567891234567', 'description' => 'Analgésico y anticoagulante', 'laboratory_id' => 3],
            ['name' => 'Amoxicilina', 'price' => 20.00, 'barcode' => '7894561237890', 'description' => 'Antibiótico de amplio espectro', 'laboratory_id' => 4],
            ['name' => 'Vitamina C', 'price' => 12.00, 'barcode' => '3216549870123', 'description' => 'Suplemento vitamínico', 'laboratory_id' => 1],
            ['name' => 'Omeprazol', 'price' => 18.50, 'barcode' => '6547893210987', 'description' => 'Inhibidor de la bomba de protones', 'laboratory_id' => 2],
            ['name' => 'Diclofenaco', 'price' => 9.00, 'barcode' => '9873216540123', 'description' => 'Antiinflamatorio y analgésico', 'laboratory_id' => 3],
            ['name' => 'Clorfenamina', 'price' => 6.75, 'barcode' => '1597534860123', 'description' => 'Antihistamínico', 'laboratory_id' => 4],
            ['name' => 'Loratadina', 'price' => 14.50, 'barcode' => '7531594860123', 'description' => 'Antihistamínico', 'laboratory_id' => 1],
            ['name' => 'Metformina', 'price' => 22.00, 'barcode' => '3579514860123', 'description' => 'Antidiabético', 'laboratory_id' => 2],
            ['name' => 'Simvastatina', 'price' => 25.00, 'barcode' => '9517534860123', 'description' => 'Reductor de colesterol', 'laboratory_id' => 3],
            ['name' => 'Atorvastatina', 'price' => 28.00, 'barcode' => '7896541230123', 'description' => 'Reductor de colesterol', 'laboratory_id' => 4],
            ['name' => 'Losartán', 'price' => 30.00, 'barcode' => '1237894560123', 'description' => 'Antihipertensivo', 'laboratory_id' => 1],
            ['name' => 'Enalapril', 'price' => 27.50, 'barcode' => '4561237890123', 'description' => 'Antihipertensivo', 'laboratory_id' => 2],
            ['name' => 'Furosemida', 'price' => 10.00, 'barcode' => '7891234560123', 'description' => 'Diurético', 'laboratory_id' => 3],
        ];
        foreach ($products as $productData) {
            Product::create($productData);
        }
        /* Crear Lotes */
        $products = Product::all();

        foreach ($products as $product) {
            $numBatches = rand(2, 3);
            for ($i = 0; $i < $numBatches; $i++) {
                $quantity = rand(10, 100);
                $stock = rand(0, $quantity);
                Batch::create([
                    'code' => strtoupper(Str::random(10)),
                    'stock' => $stock,
                    'quantity' => $quantity,
                    'expiration' => now()->addDays(rand(-30, 90)),
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
