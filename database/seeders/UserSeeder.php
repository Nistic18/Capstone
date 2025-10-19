<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // use bcrypt hashing,
            'role' => 'admin',
        ]);

        // Buyer user account
        User::create([
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'password' => Hash::make('buyer123'),
            'role' => 'buyer',
        ]);
        // Supplier user account
        User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'password' => Hash::make('supplier123'),
            'role' => 'supplier',
        ]);
    }
}
