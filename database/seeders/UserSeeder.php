<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema; // <--- Tambahkan Import ini
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key agar bisa truncate
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel users
        User::truncate();

        // 3. Hidupkan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();

        // 4. Buat user dummy
        User::create([
            'name' => 'Admin Pegadaian',
            'email' => 'admin@pegadaian.com',
            'password' => Hash::make('password123'),
        ]);

        // Tambahan user lain jika perlu
        // User::create([
        //     'name' => 'Staff Gudang',
        //     'email' => 'staff@pegadaian.com',
        //     'password' => Hash::make('password123'),
        // ]);
    }
}