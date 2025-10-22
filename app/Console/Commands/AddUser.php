<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admin=User::create([
            'name' => 'Administrateur sky',
            'phone' => '675000056',
            'user_type' => 'admin',
            'email' => 'admin@dsc-skypay.org',
            'password'=>Hash::make('admin1234'),
        ]);
        $commercial=User::create([
            'name' => 'Commercial sky',
            'phone' => '675000000',
            'user_type' => 'commercial',
            'email' => 'commercial@dsc-skypay.org',
            'password'=>Hash::make('commerce1236'),
        ]);
        $finance=User::create([
            'name' => 'Finance sky',
            'phone' => '675000006',
            'user_type' => 'finance',
            'email' => 'finance@dsc-skypay.org',
            'password'=>Hash::make('finance1237'),
        ]);
        $rhop=User::create([
            'name' => 'Ressource sky',
            'phone' => '675000007',
            'user_type' => 'commercial',
            'email' => 'rhop@dsc-skypay.org',
            'password'=>Hash::make('ressource1238'),
        ]);
    }
}
