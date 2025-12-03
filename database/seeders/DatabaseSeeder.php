<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users first
        $this->call([
            UserSeeder::class,
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed product types and categories BEFORE products
        $this->call([
            ProductTypeSeeder::class,
            ProductCategorySeeder::class,
        ]);

        // NOW seed products (after types and categories exist)
        $this->call([
            ProductSeeder::class,
        ]);
    }
}