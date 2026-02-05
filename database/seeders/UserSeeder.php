<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {

    

        $owner = User::create([
            'name' => 'Owner RMI',
            'email' => 'owner@rmi.local',
            'password'=> Hash::make('owner1234'),
            'email_verified_at' => now(),
        ]);

        $owner->assignRole('owner');

        // SUPERADMIN
        $admin = User::create([
            'name' => 'Admin RMI',
            'email' => 'admin@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('superadmin');


        // Sales
        $sales = User::create([
            'name' => 'Sales RMI',
            'email' => 'sales@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $sales->assignRole('sales');
 

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
        $staffCRM->assignRole('crm');

        // STAFF WQS
        $staffWQS = User::create([
            'name' => 'Staff WQS',
            'email' => 'wqs@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffWQS->assignRole('wqs');

        // STAFF SCM
        $staffSCM = User::create([
            'name' => 'Staff SCM',
            'email' => 'scm@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffSCM->assignRole('scm');

        // STAFF ACT
        $staffACT = User::create([
            'name' => 'Staff ACT',
            'email' => 'act@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffACT->assignRole('act');

        // STAFF FIN
        $staffFIN = User::create([
            'name' => 'Staff FIN',
            'email' => 'fin@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffFIN->assignRole('fin');

        // STAFF REG ALKES
        $staffRegAlkes = User::create([
            'name' => 'Staff REG ALKES',
            'email' => 'regalkes@rmi.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $staffRegAlkes->assignRole('reg_alkes');

        $this->command->info('✅ Created 8 users with roles');
    }
}
