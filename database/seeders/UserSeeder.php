<?php

namespace Database\Seeders;

use App\Enums\UnitType;
use App\Enums\UserRole;
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
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@ubg.ac.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::SUPERADMIN,
                'unit_type' => null,
                'unit_id' => null,
                'is_active' => true,
            ]
        );

        // Admin Universitas
        User::updateOrCreate(
            ['email' => 'admin@ubg.ac.id'],
            [
                'name' => 'Admin Universitas',
                'password' => Hash::make('password'),
                'role' => UserRole::UNIVERSITAS,
                'unit_type' => UnitType::UNIVERSITAS,
                'unit_id' => null,
                'is_active' => true,
            ]
        );

        $this->command->info('Users seeded successfully!');
        $this->command->table(
            ['Email', 'Role', 'Password'],
            [
                ['superadmin@ubg.ac.id', 'Super Admin', 'password'],
                ['admin@ubg.ac.id', 'Admin Universitas', 'password'],
            ]
        );
    }
}
