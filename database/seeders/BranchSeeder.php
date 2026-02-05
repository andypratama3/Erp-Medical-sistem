<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main branches
        $branches = [
            [
                'code' => 'HQ',
                'name' => 'Head Quarter Jakarta',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'phone' => '021-5551234',
                'email' => 'hq@company.com',
                'status' => 'active',
            ],
            [
                'code' => 'BDG',
                'name' => 'Bandung Branch',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'phone' => '022-4441234',
                'email' => 'bdg@company.com',
                'status' => 'active',
            ],
            [
                'code' => 'SBY',
                'name' => 'Surabaya Branch',
                'address' => 'Jl. Tunjungan No. 78, Surabaya',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'phone' => '031-3331234',
                'email' => 'sby@company.com',
                'status' => 'active',
            ],
            [
                'code' => 'MKS',
                'name' => 'Makassar Branch',
                'address' => 'Jl. AP Pettarani No. 99, Makassar',
                'city' => 'Makassar',
                'province' => 'Sulawesi Selatan',
                'phone' => '0411-2221234',
                'email' => 'mks@company.com',
                'status' => 'active',
            ],
            [
                'code' => 'BPP',
                'name' => 'Balikpapan Branch',
                'address' => 'Jl. Jenderal Sudirman No. 56, Balikpapan',
                'city' => 'Balikpapan',
                'province' => 'Kalimantan Timur',
                'phone' => '0542-1112234',
                'email' => 'bpp@company.com',
                'status' => 'active',
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::create($branchData);
        }

        // Assign users to branches
        $this->assignUsersToBranches();
    }

    /**
     * Assign users to branches with proper relationships
     */
    private function assignUsersToBranches(): void
    {
        // Get all users and branches
        $users = User::all();
        $branches = Branch::all();

        // Owner gets all branches
        $owner = $users->where('email', 'owner@rmi.local')->first();
        if ($owner) {
            foreach ($branches as $branch) {
                $owner->branches()->attach($branch->id, [
                    'is_default' => $branch->code === 'HQ',
                ]);
            }
            $owner->update(['current_branch_id' => $branches->firstWhere('code', 'HQ')->id]);
        }

        // Admin gets all branches
        $admin = $users->where('email', 'admin@rmi.local')->first();
        if ($admin) {
            foreach ($branches as $branch) {
                $admin->branches()->attach($branch->id, [
                    'is_default' => $branch->code === 'HQ',
                ]);
            }
            $admin->update(['current_branch_id' => $branches->firstWhere('code', 'HQ')->id]);
        }

        // Assign staff to specific branches
        $staffMappings = [
            'staff@rmi.local' => ['HQ', 'BDG'],
            'manager@rmi.local' => ['BDG'],
            'wqs@rmi.local' => ['HQ', 'BDG','MKS', 'SBY'],
            'sales@rmi.local' => ['SBY', 'MKS'],
        ];

        foreach ($staffMappings as $email => $branchCodes) {
            $user = $users->where('email', $email)->first();
            if ($user) {
                $isFirst = true;
                foreach ($branchCodes as $code) {
                    $branch = $branches->firstWhere('code', $code);
                    if ($branch) {
                        $user->branches()->attach($branch->id, [
                            'is_default' => $isFirst,
                        ]);
                        if ($isFirst) {
                            $user->update(['current_branch_id' => $branch->id]);
                            $isFirst = false;
                        }
                    }
                }
            }
        }

        // Set managers for branches
        $managerMappings = [
            'HQ' => 'admin@rmi.local',
            'BDG' => 'manager@rmi.local',
            'SBY' => 'sales@rmi.local',
        ];

        foreach ($managerMappings as $branchCode => $email) {
            $branch = $branches->firstWhere('code', $branchCode);
            $manager = $users->where('email', $email)->first();
            if ($branch && $manager) {
                $branch->update(['manager_id' => $manager->id]);
            }
        }
    }
}
