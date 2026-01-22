<?php
namespace App\Helpers;

class SalesDOHelper
{
    /**
     * Status configuration untuk SalesDO workflow
     */
    public static function getStatusConfig()
    {
        return [
            // CRM Module
            'crm_to_wqs' => [
                'label' => 'CRM to WQS',
                'color' => 'yellow',
                'badge_class' => 'bg-yellow-100 text-yellow-800',
                'description' => 'Document created in CRM, pending WQS review',
                'module' => 'CRM',
                'editable' => true,
                'deletable' => true,
                'submittable' => true,
            ],

            // WQS Module
            'wqs_ready' => [
                'label' => 'WQS Ready',
                'color' => 'blue',
                'badge_class' => 'bg-blue-100 text-blue-800',
                'description' => 'Document submitted to WQS for quality check',
                'module' => 'WQS',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'wqs_on_hold' => [
                'label' => 'WQS On Hold',
                'color' => 'red',
                'badge_class' => 'bg-red-100 text-red-800',
                'description' => 'Document is on hold in WQS for quality issues',
                'module' => 'WQS',
                'editable' => true,
                'deletable' => false,
                'submittable' => true,
            ],

            // SCM Module
            'scm_on_delivery' => [
                'label' => 'On Delivery',
                'color' => 'indigo',
                'badge_class' => 'bg-indigo-100 text-indigo-800',
                'description' => 'Items are being delivered to customer',
                'module' => 'SCM',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'scm_delivered' => [
                'label' => 'Delivered',
                'color' => 'green',
                'badge_class' => 'bg-green-100 text-green-800',
                'description' => 'Items delivered to customer',
                'module' => 'SCM',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            // ACT Module (Accounting/Invoicing)
            'act_tukar_faktur' => [
                'label' => 'Tukar Faktur',
                'color' => 'purple',
                'badge_class' => 'bg-purple-100 text-purple-800',
                'description' => 'Awaiting invoice exchange/preparation',
                'module' => 'ACT',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'act_invoiced' => [
                'label' => 'Invoiced',
                'color' => 'green',
                'badge_class' => 'bg-green-100 text-green-800',
                'description' => 'Invoice has been created and issued',
                'module' => 'ACT',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            // FIN Module (Finance/Collection)
            'fin_on_collect' => [
                'label' => 'On Collection',
                'color' => 'orange',
                'badge_class' => 'bg-orange-100 text-orange-800',
                'description' => 'Invoice is awaiting payment collection',
                'module' => 'FIN',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'fin_paid' => [
                'label' => 'Paid',
                'color' => 'green',
                'badge_class' => 'bg-green-100 text-green-800',
                'description' => 'Payment received in full',
                'module' => 'FIN',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'fin_overdue' => [
                'label' => 'Overdue',
                'color' => 'red',
                'badge_class' => 'bg-red-100 text-red-800',
                'description' => 'Payment is overdue',
                'module' => 'FIN',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],
        ];
    }

    /**
     * Get status config untuk satu status
     */
    public static function getStatusConfigByKey($status)
    {
        $config = self::getStatusConfig();
        return $config[$status] ?? [
            'label' => 'Unknown',
            'color' => 'gray',
            'badge_class' => 'bg-gray-100 text-gray-800',
            'description' => 'Status tidak dikenal',
            'module' => 'Unknown',
            'editable' => false,
            'deletable' => false,
            'submittable' => false,
        ];
    }

    /**
     * Get status label
     */
    public static function getStatusLabel($status)
    {
        return self::getStatusConfigByKey($status)['label'];
    }

    /**
     * Get status color
     */
    public static function getStatusColor($status)
    {
        return self::getStatusConfigByKey($status)['color'];
    }

    /**
     * Get status badge class
     */
    public static function getStatusBadgeClass($status)
    {
        return self::getStatusConfigByKey($status)['badge_class'];
    }

    /**
     * Check if status is editable
     */
    public static function isEditable($status)
    {
        return self::getStatusConfigByKey($status)['editable'] ?? false;
    }

    /**
     * Check if status is deletable
     */
    public static function isDeletable($status)
    {
        return self::getStatusConfigByKey($status)['deletable'] ?? false;
    }

    /**
     * Check if status is submittable to next module
     */
    public static function isSubmittable($status)
    {
        return self::getStatusConfigByKey($status)['submittable'] ?? false;
    }

    /**
     * Get next status after submission
     */
    public static function getNextStatusAfterSubmit($currentStatus)
    {
        $transitions = [
            'crm_to_wqs' => 'wqs_ready',
            'wqs_on_hold' => 'wqs_ready',
            // Add more transitions as needed
        ];

        return $transitions[$currentStatus] ?? null;
    }

    /**
     * Get all statuses grouped by module
     */
    public static function getStatusesByModule()
    {
        $config = self::getStatusConfig();
        $grouped = [];

        foreach ($config as $status => $details) {
            $module = $details['module'];
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][$status] = $details['label'];
        }

        return $grouped;
    }

    /**
     * Get workflow timeline
     */
    public static function getWorkflowTimeline()
    {
        return [
            1 => ['status' => 'crm_to_wqs', 'module' => 'CRM', 'label' => 'Order Created'],
            2 => ['status' => 'wqs_ready', 'module' => 'WQS', 'label' => 'Quality Check'],
            3 => ['status' => 'scm_on_delivery', 'module' => 'SCM', 'label' => 'Delivery'],
            4 => ['status' => 'act_invoiced', 'module' => 'ACT', 'label' => 'Invoice'],
            5 => ['status' => 'fin_paid', 'module' => 'FIN', 'label' => 'Payment'],
        ];
    }

    /**
     * Format currency untuk rupiah
     */
    public static function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Hitung progress percentage berdasarkan status
     */
    public static function getProgressPercentage($status)
    {
        $progress = [
            'crm_to_wqs' => 20,
            'wqs_ready' => 40,
            'scm_on_delivery' => 60,
            'scm_delivered' => 70,
            'act_tukar_faktur' => 75,
            'act_invoiced' => 85,
            'fin_on_collect' => 90,
            'fin_paid' => 100,
            'fin_overdue' => 85,
            'wqs_on_hold' => 40,
        ];

        return $progress[$status] ?? 0;
    }

    /**
     * Validate if DO dapat disubmit
     *
     * @param \App\Models\SalesDO $salesDo
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validateSubmission($salesDo, $items)
    {
        $errors = [];

        // Check status
        if ($salesDo->status !== 'crm_to_wqs') {
            $errors[] = 'DO status must be "CRM to WQS" for submission.';
        }

        // Check items
        if ($items->isEmpty()) {
            $errors[] = 'DO must have at least one item.';
        }

        // Check item details
        foreach ($items as $item) {
            if (!$item->qty_ordered || $item->qty_ordered <= 0) {
                $errors[] = "Item '{$item->product_name}' has invalid quantity.";
            }

            if (!$item->unit_price || $item->unit_price <= 0) {
                $errors[] = "Item '{$item->product_name}' has invalid unit price.";
            }
        }

        // Check customer
        if (!$salesDo->customer_id) {
            $errors[] = 'Customer must be selected.';
        }

        // Check office
        if (!$salesDo->office_id) {
            $errors[] = 'Office must be selected.';
        }

        // Check shipping address
        if (empty($salesDo->shipping_address)) {
            $errors[] = 'Shipping address is required.';
        }

        // Check grand total
        if ($salesDo->grand_total <= 0) {
            $errors[] = 'Grand total must be greater than zero.';
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }
}
