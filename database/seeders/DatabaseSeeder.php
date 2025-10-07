<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PointSale;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      $user=  User::factory()->create([
            'name' => 'Admin sky',
            'phone' => '675066919',
            'user_type' => 'admin',
            'email' => 'sky@example.com',
        ]);
        PointSale::create([
           'name'=>'Direction' ,
            'localisation'=>'Douala bonamousadi',
            'vendor_id'=>$user->id
        ]);

        DB::table('categories')->insert([
            [
                'name'        => 'Philips',
            ],
            [
                'name'        => 'Omni',

            ],
            [
                'name'        => 'Samsung',

            ],
            [
                'name'        => 'Hp',

            ],
            ]);

    }
}
