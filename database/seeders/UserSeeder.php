<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Pemilik Usaha (Owner)
        User::updateOrCreate(
            ['email' => 'owner@rap-iphone.com'],
            [
                'name' => 'Owner Rap-iPhone',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone_number' => '081234567890',
                'avatar' => null,
                'address' => 'Gedung Wisma 46, Lantai 15, Jl. Jend. Sudirman',
                'city' => 'Jakarta Pusat',
                'postal_code' => '10220',
            ]
        );

        // 2. Akun Admin Toko & Operasional
        User::updateOrCreate(
            ['email' => 'admin@rap-iphone.com'],
            [
                'name' => 'Admin Toko Rap-iPhone',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone_number' => '081234567891',
                'avatar' => null,
                'address' => 'Rap-iPhone Flagship Store, Gandaria City Mall LG-22',
                'city' => 'Jakarta Selatan',
                'postal_code' => '12240',
            ]
        );

        // 3. Akun Customer Demo
        User::updateOrCreate(
            ['email' => 'customer@rap-iphone.com'],
            [
                'name' => 'Budi Pratama (Customer)',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_number' => '081234567892',
                'avatar' => null,
                'address' => 'Jl. Jendral Sudirman No. 45, Kebayoran Baru',
                'city' => 'Jakarta Selatan',
                'postal_code' => '12190',
            ]
        );
    }
}
