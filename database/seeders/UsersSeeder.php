<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'Password123!';

    public function run(): void
    {
        $users = [
            ['name' => 'Argus Admin', 'email' => 'admin@tableya.test', 'role' => 'admin', 'phone' => '09971234567', 'address' => 'Barangay Cuambogan, Tagum City'],
            ['name' => 'Maria Santos', 'email' => 'maria.customer@tableya.test', 'role' => 'customer', 'phone' => '09971234568', 'address' => 'Barangay Apokon, Tagum City'],
            ['name' => 'Juan dela Cruz', 'email' => 'juan.customer@tableya.test', 'role' => 'customer', 'phone' => '09971234569', 'address' => 'Barangay Mankilam, Tagum City'],
            ['name' => 'Ana Reyes', 'email' => 'ana.customer@tableya.test', 'role' => 'customer', 'phone' => '09971234570', 'address' => 'Barangay Magugpo, Tagum City'],
            ['name' => 'Carlo Fernandez', 'email' => 'carlo.customer@tableya.test', 'role' => 'customer', 'phone' => '09971234571', 'address' => 'Barangay Buhangin, Davao City'],
            ['name' => 'Liza Mendoza', 'email' => 'liza.reseller@tableya.test', 'role' => 'reseller', 'phone' => '09971234572', 'address' => 'Barangay Panacan, Davao City'],
            ['name' => 'Ramon Castillo', 'email' => 'ramon.reseller@tableya.test', 'role' => 'reseller', 'phone' => '09971234573', 'address' => 'Barangay Gredu, Panabo City'],
            ['name' => 'Grace Villanueva', 'email' => 'grace.reseller@tableya.test', 'role' => 'reseller', 'phone' => '09971234574', 'address' => 'Barangay Matiao, Mati City'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make(self::DEMO_PASSWORD),
                    'role' => $user['role'],
                    'phone' => $user['phone'],
                    'address' => $user['address'],
                ]
            );
        }
    }
}
