<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Quelques noms fixes + du fake
        $default = [
            ['name' => 'Burgers'],
            ['name' => 'Pizzas'],
            ['name' => 'Drinks'],
            ['name' => 'Desserts'],
            ['name' => 'Salads'],
        ];

        // Insert fixe
        foreach ($default as $cat) {
            Category::create($cat);
        }
        DB::table('categories')->insert([
            [
                'category_id' => 1,
                'name'        => 'Chicken Wings',
                'image_url'   => 'https://picsum.photos/400/300?random=1',
                'price'       => 20.00,
                'type'        => 'food',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'category_id' => 1,
                'name'        => 'Summer Salad',
                'image_url'   => 'https://picsum.photos/400/300?random=2',
                'price'       => 10.00,
                'type'        => 'food',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'category_id' => 2,
                'name'        => 'Fresh Lemon Juice',
                'image_url'   => 'https://picsum.photos/400/300?random=3',
                'price'       => 5.50,
                'type'        => 'drink',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

    }
}
