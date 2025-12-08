<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $user = new User();
       $user->name = "Admin";
       $user->password = Hash::make("password");
       $user->email = "admin@gmail.com";
       $user->role ="admin";
       $user->token = "";
       $user->save();

       $user = new User();
       $user->name = "Guard";
       $user->password = Hash::make("password");
       $user->email = "guard@gmail.com";
       $user->role ="guard";
       $user->token = "";
       $user->save();
    }
}
