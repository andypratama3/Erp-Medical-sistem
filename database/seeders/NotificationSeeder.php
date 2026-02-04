<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Skipping NotificationSeeder - no users found');
            return;
        }

        $notificationTemplates = [
            [
                'type'    => 'sales_do_submitted',
                'title'   => 'New Sales DO Submitted',
                'message' => 'Sales DO #{do_code} has been submitted and awaiting processing',
                'url'     => '/sales/do',
            ],
            [
                'type'    => 'delivery_scheduled',
                'title'   => 'Delivery Scheduled',
                'message' => 'Delivery for DO #{do_code} has been scheduled',
                'url'     => '/logistics/delivery',
            ],
            [
                'type'    => 'invoice_created',
                'title'   => 'Invoice Created',
                'message' => 'Invoice #{invoice_number} has been created',
                'url'     => '/finance/invoices',
            ],
            [
                'type'    => 'payment_received',
                'title'   => 'Payment Received',
                'message' => 'Payment for Invoice #{invoice_number} has been received',
                'url'     => '/finance/payments',
            ],
            [
                'type'    => 'stock_check_required',
                'title'   => 'Stock Check Required',
                'message' => 'Stock check is required for DO #{do_code}',
                'url'     => '/warehouse/stock-check',
            ],
        ];

        foreach ($users->take(10) as $user) {
            foreach (range(1, rand(3, 8)) as $i) {
                $template = $notificationTemplates[array_rand($notificationTemplates)];

                Notification::create([
                    'user_id' => $user->id,
                    'type'    => $template['type'],
                    'title'   => $template['title'],
                    'message' => str_replace(
                        ['{do_code}', '{invoice_number}'],
                        ['DO-' . rand(1000, 9999), 'INV-' . rand(1000, 9999)],
                        $template['message']
                    ),
                    'url'     => $template['url'],
                    'data'    => [
                        'do_code'        => 'DO-' . rand(1000, 9999),
                        'invoice_number' => 'INV-' . rand(1000, 9999),
                    ],
                    'read_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
