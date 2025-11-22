<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductCategory;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Fetch all available type and category IDs
        $typeIds = ProductType::pluck('id')->toArray();
        $categoryIds = ProductCategory::pluck('id')->toArray();

        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => ucfirst($faker->words(2, true)), // Random 2-word name
                'description' => $faker->sentence(10),
                'price' => $faker->randomFloat(2, 50, 500), // Price between 5 0 and 500
                'image' => 'products/sample' . rand(1, 5) . '.jpg', // Placeholder image paths
                'quantity' => rand(1, 100),
                'user_id' => 3, // Default owner/seller
                'product_type_id' => $faker->randomElement($typeIds),
                'product_category_id' => $faker->randomElement($categoryIds),
                'status' => 'available', // Optional default status
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
