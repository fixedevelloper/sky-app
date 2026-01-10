<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PointSale;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** ==========================
         *  USER ADMIN
         *  ========================== */
        $user = User::updateOrCreate(
            ['email' => 'contact.info@dsc-group.org'], // clé unique
            [
                'name'     => 'DSC admin',
                'phone'    => '683806782',
                'roles'    => ['admin','pme','commercial'],
                'password' => Hash::make('00235'),
            ]
        );

        /** ==========================
         *  POINT DE VENTE
         *  ========================== */
        PointSale::updateOrCreate(
            [
                'vendor_id' => $user->id,
                'name'      => 'Direction',
            ],
            [
                'localisation' => 'Douala bonamousadi',
            ]
        );


        /** ==========================
         *  CATEGORIES
         *  ========================== */
        $categories = [
            'Philips',
            'Omni',
            'Samsung',
            'Hp',
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category], // clé unique
                ['name' => $category]
            );
        }
    }

}
