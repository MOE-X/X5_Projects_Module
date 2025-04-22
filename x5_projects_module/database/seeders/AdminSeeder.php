<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name'            => 'Admin',
            'email'           => 'admin@example.com',
            'password'        => bcrypt('secret123'), 
            'phone'           => '+1234567890',
            'dob'             => '1990-01-01',
            'gender_id'       => 1, 
            'user_role_id'    => 1,
        ]);
    }
}