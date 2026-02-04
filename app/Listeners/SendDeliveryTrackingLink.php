<?php

namespace App\Listeners;

use App\Events\DeliveryDispatched;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Send tracking link to customer when delivery is dispatched
 */
class SendDeliveryTrackingLink
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(DeliveryDispatched $event): void
    {
        $delivery = $event->delivery;
        $customer = $event->salesDo->customer;

        try {
            // Send notification to customer contact person if exists
            if ($customer->contact_email) {
                // TODO: Implement email/SMS sending
                // Mail::to($customer->contact_email)
                //     ->send(new DeliveryDispatchedMail($delivery));
                
                Log::info('Delivery tracking notification sent', [
                    'delivery_id' => $delivery->id,
                    'customer_email' => $customer->contact_email,
                ]);
            }

            // Notify internal CRM users
            $crmUsers = \App\Models\User::role('crm')
                ->where('current_branch_id', $delivery->branch_id)
                ->get();

            foreach ($crmUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'delivery_dispatched',
                    'title' => 'Delivery Dispatched',
                    'message' => sprintf(
                        'DO %s is now on the way. Driver: %s',
                        $event->salesDo->do_code,
                        $delivery->driver->name
                    ),
                    'url' => route('scm.tracking.show', $delivery->tracking_number ?? $delivery->id),
                    'data' => [
                        'delivery_id' => $delivery->id,
                        'tracking_number' => $delivery->tracking_number,
                    ],
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('Failed to send delivery tracking', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
