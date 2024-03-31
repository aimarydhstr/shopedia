<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Setting::create([
            'name' => 'Shopedia',
            'description' => 'Merupakan toko online yang mempunyai sistem membership',
            'favicon' => 'favicon.png',
            'logo' => 'logo.png',
            'bank_number' => '1234567890'
        ]);

        \App\Models\User::create([
            'name' => 'Admin Shopedia',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'phone' => '089237137121',
            'address' => 'Jalan Atmin',
            'role' => 'Admin',
            'is_active' => true,
        ]);
    }
}
