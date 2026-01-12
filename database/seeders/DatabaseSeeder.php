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
            ['phone' => '683806782'], // clé unique
            [
                'name'     => 'DSC admin',
                'phone'    => '683806782',
                'roles'    => ['admin','pme','commercial','distribute','vendor'],
                'password' => Hash::make('00235'),
                'email'=>'contact.info@dsc-group.org'
            ]
        );
        $user = User::updateOrCreate(
            ['phone' => '694103519'], // clé unique
            [
                'name'     => 'DSC admin',
                'phone'    => '694103519',
                'roles'    => ['admin','pme','commercial','distribute','vendor'],
                'password' => Hash::make('00231'),
                'email'=>'contact.info6@dsc-group.org'
            ]
        );
        $user = User::updateOrCreate(
            ['phone' => '659086230'], // clé unique
            [
                'name'     => 'pme01',
                'phone'    => '659086230',
                'roles'    => ['pme'],
                'password' => Hash::make('12345'),
                'email'=>'contact.info5@dsc-group.org'
            ]
        );

        $user = User::updateOrCreate(
            ['phone' => '695088562'], // clé unique
            [
                'name'     => 'pme02',
                'phone'    => '695088562',
                'roles'    => ['pme'],
                'password' => Hash::make('12346'),
                'email'=>'contact.info4@dsc-group.org'
            ]
        );
        $user = User::updateOrCreate(
            ['phone' => '656206233'], // clé unique
            [
                'name'     => 'pme03',
                'phone'    => '656206233',
                'roles'    => ['pme'],
                'password' => Hash::make('12347'),
                'email'=>'contact.info3@dsc-group.org'
            ]
        );
        $user = User::updateOrCreate(
            ['phone' => '671999356'], // clé unique
            [
                'name'     => 'pme04',
                'phone'    => '671999356',
                'roles'    => ['pme'],
                'password' => Hash::make('12348'),
                'email'=>'contact.info1@dsc-group.org'
            ]
        );
        $user = User::updateOrCreate(
            ['phone' => '690595418'], // clé unique
            [
                'name'     => 'pme04',
                'phone'    => '690595418',
                'roles'    => ['pme','commercial','distribute','vendor'],
                'password' => Hash::make('12349'),
                'email'=>'contact.info2@dsc-group.org'
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
