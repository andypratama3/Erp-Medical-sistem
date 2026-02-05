<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // OWNER
        $owner = User::firstOrCreate(
            ['email' => 'owner@rmi.local'],
            [
                'name' => 'Owner RMI',
                'password' => Hash::make('owner1234'),
                'email_verified_at' => now(),
            ]
        );
        $owner->assignRole('owner');

        // SUPERADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@rmi.local'],
            [
                'name' => 'Admin RMI',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('superadmin');

        // MANAGER
        $manager = User::firstOrCreate(
            ['email' => 'manager@rmi.local'],
            [
                'name' => 'Manager RMI',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('manager');

        // SALES
        $sales = User::firstOrCreate(
            ['email' => 'sales@rmi.local'],
            [
                'name' => 'Sales RMI',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $sales->assignRole('sales');

        // STAFF CRM
        $staffCRM = User::firstOrCreate(
            ['email' => 'crm@rmi.local'],
            [
                'name' => 'Staff CRM',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $staffCRM->assignRole('crm');

        // STAFF WQS
        $staffWQS = User::firstOrCreate(
            ['email' => 'wqs@rmi.local'],
            [
                'name' => 'Staff WQS',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $staffWQS->assignRole('wqs');

        // STAFF SCM
        $staffSCM = User::firstOrCreate(
            ['email' => 'scm@rmi.local'],
            [
                'name' => 'Staff SCM',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $staffSCM->assignRole('scm');

        // STAFF ACT
        $staffACT = User::firstOrCreate(
            ['email' => 'act@rmi.local'],
            [
                'name' => 'Staff ACT',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $staffACT->assignRole('act');

        // STAFF FIN
        $staffFIN = User::firstOrCreate(
            ['email' => 'fin@rmi.local'],
            [
                'name' => 'Staff FIN',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $staffFIN->assignRole('fin');

        // STAFF REG ALKES
        $staffRegAlkes = User::firstOrCreate(
            ['email' => 'regalkes@rmi.local'],
            [
                'name' => 'Staff REG ALKES',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $staffRegAlkes->assignRole('reg_alkes');

        $this->command->info('✅ Created 9 test users with roles');
    }
}