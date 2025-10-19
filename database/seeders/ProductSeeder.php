<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => ucfirst($faker->words(2, true)), // Random 2-word name
                'description' => $faker->sentence(10),
                'price' => $faker->randomFloat(2, 50, 500), // Price between 50 and 500
                'image' => 'products/sample' . rand(1, 5) . '.jpg', // Placeholder image paths
                'quantity' => rand(1, 100), // Random quantity between 1 and 100
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
