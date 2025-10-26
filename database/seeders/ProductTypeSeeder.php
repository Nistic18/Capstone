<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Tawilis / Freshwater Sardine',
            'Tunsoy / Sardine',
            'Tamban / Round Scad',
            'Hasa-hasa / Short Mackerel',
            'Alumahan / Mackerel',
            'Bisugo / Threadfin Bream',
            'Danggit / Rabbitfish',
            'Espada / Beltfish',
            'Galunggong (GG) / Round Scad',
            'Salay-salay / Slipmouth Fish',
            'Dilis / Anchovy',
            'Hipon / Shrimp',
            'Pusit / Squid',
            'Takla / Small Shrimp',
        ];

        foreach ($types as $type) {
            ProductType::firstOrCreate(['name' => $type]);
        }
    }
}
