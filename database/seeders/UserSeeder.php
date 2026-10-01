<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin / Clinic Owner
        User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'name' => 'Dr. Alexander Wright',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+1 (555) 019-2831',
                'specialization' => 'Clinic Owner & Medical Director',
                'is_active' => true,
            ]
        );

        // Doctor
        User::firstOrCreate(
            ['email' => 'doctor@clinic.com'],
            [
                'name' => 'Dr. Sarah Jenkins',
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'phone' => '+1 (555) 014-9922',
                'specialization' => 'General Medicine & Family Physician',
                'is_active' => true,
            ]
        );

        // Receptionist
        User::firstOrCreate(
            ['email' => 'receptionist@clinic.com'],
            [
                'name' => 'Emily Davis',
                'password' => Hash::make('password'),
                'role' => 'receptionist',
                'phone' => '+1 (555) 017-4488',
                'specialization' => 'Front Desk Manager',
                'is_active' => true,
            ]
        );
    }
}
