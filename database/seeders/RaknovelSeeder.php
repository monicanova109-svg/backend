<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class RaknovelSeeder extends Seeder
{
    public function run(): void
    {
        $cat = [];
        foreach (['Drama', 'Sejarah', 'Sastra', 'Romansa'] as $name) {
            $cat[$name] = Category::create(['category' => $name])->category_id;
        }

        $items = [
            ['Drama', 'Laskar Pelangi', 85000, 12],
            ['Sejarah', 'Bumi Manusia', 95000, 4],
            ['Sastra', 'Cantik Itu Luka', 110000, 0],
            ['Sejarah', 'Laut Bercerita', 90000, 9],
            ['Romansa', 'Dilan 1990', 75000, 20],
            ['Drama', 'Pulang', 80000, 3],
        ];

        foreach ($items as [$c, $name, $price, $stock]) {
            Product::create([
                'category_id' => $cat[$c],
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'description' => 'Novel ' . $name,
            ]);
        }
    }
}