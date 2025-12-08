<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guard;
use Hash;

class GuardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new Guard();
        $admin->name = "Guard";
        $admin->password = Hash::make("password");
        $admin->email = "guard.gmail.com";
        $admin->token = "";
        $admin->save();
    }
}
