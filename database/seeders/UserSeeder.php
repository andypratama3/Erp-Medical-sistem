<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SUPERADMIN
        $admin = User::create([
            'name' => 'Admin RMI',
            'email' => 'admin@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('superadmin');

        // MANAGER
        $manager = User::create([
            'name' => 'Manager RMI',
            'email' => 'manager@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $manager->assignRole('manager');

        // STAFF CRM
        $staffCRM = User::create([
            'name' => 'Staff CRM',
            'email' => 'crm@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffCRM->assignRole('staff_crm');

        // STAFF WQS
        $staffWQS = User::create([
            'name' => 'Staff WQS',
            'email' => 'wqs@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffWQS->assignRole('staff_wqs');

        // STAFF SCM
        $staffSCM = User::create([
            'name' => 'Staff SCM',
            'email' => 'scm@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffSCM->assignRole('staff_scm');

        // STAFF ACT
        $staffACT = User::create([
            'name' => 'Staff ACT',
            'email' => 'act@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffACT->assignRole('staff_act');

        // STAFF FIN
        $staffFIN = User::create([
            'name' => 'Staff FIN',
            'email' => 'fin@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffFIN->assignRole('staff_fin');

        // STAFF REG ALKES
        $staffRegAlkes = User::create([
            'name' => 'Staff REG ALKES',
            'email' => 'regalkes@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffRegAlkes->assignRole('staff_reg_alkes');

        $this->command->info('✅ Created 8 users with roles');
    }
}
