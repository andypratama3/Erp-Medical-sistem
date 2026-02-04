<?php

namespace App\Listeners;

use App\Events\PaymentOverdue;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * Send overdue notice to customer and escalate internally
 */
class SendOverdueNotice
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(PaymentOverdue $event): void
    {
        $invoice = $event->invoice;
        $customer = $invoice->customer;
        $daysOverdue = $event->daysOverdue;

        try {
            // TODO: Send overdue email to customer
            // Mail::to($customer->email)
            //     ->send(new PaymentOverdueMail($invoice, $daysOverdue));

            // Notify finance team
            $finUsers = User::role('finance')
                ->where('current_branch_id', $invoice->branch_id)
                ->get();

            foreach ($finUsers as $user) {
                $this->notificationService->send([
                    'user_id' => $user->id,
                    'type' => 'payment_overdue',
                    'title' => '⚠️ Payment Overdue',
                    'message' => sprintf(
                        'Invoice %s is %d days overdue. Customer: %s. Amount: Rp %s',
                        $invoice->invoice_number,
                        abs($daysOverdue),
                        $customer->name,
                        number_format($invoice->total_amount, 0, ',', '.')
                    ),
                    'url' => route('fin.aging.by-customer', $customer),
                    'data' => [
                        'invoice_id' => $invoice->id,
                        'days_overdue' => $daysOverdue,
                        'severity' => $daysOverdue > 30 ? 'critical' : 'warning',
                    ],
                ]);
            }

            // Escalate to management if severely overdue (>30 days)
            if (abs($daysOverdue) > 30) {
                $managers = User::role('owner')
                    ->orWhereHas('roles', fn($q) => $q->where('name', 'admin'))
                    ->where('current_branch_id', $invoice->branch_id)
                    ->get();

                foreach ($managers as $manager) {
                    $this->notificationService->send([
                        'user_id' => $manager->id,
                        'type' => 'payment_critical_overdue',
                        'title' => '🚨 Critical: Payment >30 Days Overdue',
                        'message' => sprintf(
                            'URGENT: Invoice %s is %d days overdue. Immediate action required.',
                            $invoice->invoice_number,
                            abs($daysOverdue)
                        ),
                        'url' => route('fin.aging.by-customer', $customer),
                        'data' => [
                            'invoice_id' => $invoice->id,
                            'days_overdue' => $daysOverdue,
                        ],
                    ]);
                }
            }

            Log::info('Overdue notice sent', [
                'invoice_id' => $invoice->id,
                'days_overdue' => $daysOverdue,
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to send overdue notice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
