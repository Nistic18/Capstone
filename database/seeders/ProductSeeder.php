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

        // Verify we have data
        if (empty($typeIds)) {
            $this->command->error('No ProductTypes found! Run ProductTypeSeeder first.');
            return;
        }

        if (empty($categoryIds)) {
            $this->command->error('No ProductCategories found! Run ProductCategorySeeder first.');
            return;
        }

        $typeCount = count($typeIds);
        $categoryCount = count($categoryIds);
        $this->command->info("Creating 20 products with {$typeCount} types and {$categoryCount} categories...");

        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => ucfirst($faker->words(2, true)),
                'description' => $faker->sentence(10),
                'price' => $faker->randomFloat(2, 50, 500),
                'image' => 'products/sample' . rand(1, 5) . '.jpg',
                'quantity' => rand(1, 100),
                'user_id' => 3,
                'product_type_id' => $faker->randomElement($typeIds),
                'product_category_id' => $faker->randomElement($categoryIds),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('20 products created successfully!');
    }
}