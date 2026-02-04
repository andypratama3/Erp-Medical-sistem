<?php

namespace Database\Seeders;

use App\Models\SCMDelivery;
use App\Models\DeliveryTracking;
use App\Models\SalesDO;
use App\Models\SCMDriver;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $salesDOs = SalesDO::whereIn('status', ['wqs_ready', 'scm_on_delivery', 'scm_delivered'])->get();
        $drivers = SCMDriver::all();

        if ($salesDOs->isEmpty() || $drivers->isEmpty()) {
            $this->command->warn('Skipping DeliverySeeder - missing required data');
            return;
        }

        foreach ($salesDOs->take(15) as $index => $salesDO) {
            $driver = $drivers->random();

            $delivery = SCMDelivery::create([
                'sales_do_id' => $salesDO->id,
                'driver_id' => $driver->id,
                'delivery_date' => now()->addDays(rand(1, 7))->format('Y-m-d'),
                'departure_time' => rand(0, 1) ? now()->subHours(rand(1, 48))->format('H:i:s') : null,
                'arrival_time' => rand(0, 1) ? now()->subHours(rand(0, 24))->format('H:i:s') : null,
                'delivery_status' => collect(['scheduled', 'on_route', 'delivered', 'failed'])->random(),
                'receiver_name' => $salesDO->pic_customer,
                'receiver_position' => collect(['Manager', 'Staff', 'Admin', 'Supervisor'])->random(),
                'received_at' => rand(0, 1) ? now()->subHours(rand(1, 48)) : null,
                'delivery_notes' => rand(0, 1) ? 'Handle with care - Medical equipment' : null,
            ]);

            // Create delivery tracking
            if (in_array($delivery->delivery_status, ['on_route', 'delivered'])) {
                $trackingCount = rand(2, 5);
                for ($i = 0; $i < $trackingCount; $i++) {
                    DeliveryTracking::create([
                        'sales_do_id' => $salesDO->id,
                        'delivery_id' => $delivery->id,
                        'status' => collect(['departed', 'in_transit', 'arrived'])->random(),
                        'latitude' => -6.2 + (rand(0, 100) / 100),
                        'longitude' => 106.8 + (rand(0, 100) / 100),
                        'address' => 'Jakarta Location ' . ($i + 1),
                        'notes' => rand(0, 1) ? 'En route to destination' : null,
                        'recorded_at' => now()->subHours(48 - ($i * 12)),
                        'recorded_by' => $driver->id,
                    ]);
                }
            }
        }
    }
}
