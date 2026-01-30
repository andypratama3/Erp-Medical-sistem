<?php

namespace App\Helpers;

class SalesDOHelper
{
    /**
     * Master status configuration
     */
    public static function getStatusConfig(): array
    {
        return [

            // ======================
            // CRM
            // ======================
            'crm_to_wqs' => [
                'label' => 'CRM to WQS',
                'module' => 'CRM',
                'color' => 'warning',
                'badge_class' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-400',
                'icon' => 'clock',
                'editable' => true,
                'deletable' => true,
                'submittable' => true,
            ],

            // ======================
            // WQS
            // ======================
            'wqs_ready' => [
                'label' => 'WQS Ready',
                'module' => 'WQS',
                'color' => 'blue-light',
                'badge_class' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/15 dark:text-blue-light-400',
                'icon' => 'check-circle',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'wqs_on_hold' => [
                'label' => 'WQS On Hold',
                'module' => 'WQS',
                'color' => 'error',
                'badge_class' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
                'icon' => 'alert-circle',
                'editable' => true,
                'deletable' => false,
                'submittable' => true,
            ],

            // ======================
            // SCM
            // ======================
            'scm_on_delivery' => [
                'label' => 'On Delivery',
                'module' => 'SCM',
                'color' => 'brand',
                'badge_class' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/15 dark:text-brand-400',
                'icon' => 'truck',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'scm_delivered' => [
                'label' => 'Delivered',
                'module' => 'SCM',
                'color' => 'success',
                'badge_class' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                'icon' => 'check-circle',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            // ======================
            // ACT
            // ======================
            'act_tukar_faktur' => [
                'label' => 'Tukar Faktur',
                'module' => 'ACT',
                'color' => 'orange',
                'badge_class' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
                'icon' => 'file-text',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'act_invoiced' => [
                'label' => 'Invoiced',
                'module' => 'ACT',
                'color' => 'success',
                'badge_class' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                'icon' => 'check-circle',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            // ======================
            // FIN
            // ======================
            'fin_on_collect' => [
                'label' => 'On Collection',
                'module' => 'FIN',
                'color' => 'orange',
                'badge_class' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
                'icon' => 'hourglass',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'fin_paid' => [
                'label' => 'Paid',
                'module' => 'FIN',
                'color' => 'success',
                'badge_class' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                'icon' => 'check-circle',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],

            'fin_overdue' => [
                'label' => 'Overdue',
                'module' => 'FIN',
                'color' => 'error',
                'badge_class' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
                'icon' => 'alert-triangle',
                'editable' => false,
                'deletable' => false,
                'submittable' => false,
            ],
        ];
    }

    /**
     * Single status config
     */
    public static function getStatusConfigByKey(string $status): array
    {
        return self::getStatusConfig()[$status] ?? [
            'label' => 'Unknown',
            'module' => 'Unknown',
            'color' => 'gray',
            'badge_class' => 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400',
            'icon' => 'help-circle',
            'editable' => false,
            'deletable' => false,
            'submittable' => false,
        ];
    }

    // ======================
    // SHORTCUT HELPERS
    // ======================

    public static function icon(string $status): string
    {
        return self::getStatusConfigByKey($status)['icon'];
    }

    public static function isEditable(string $status): bool
    {
        return self::getStatusConfigByKey($status)['editable'];
    }

    public static function isDeletable(string $status): bool
    {
        return self::getStatusConfigByKey($status)['deletable'];
    }

    public static function isSubmittable(string $status): bool
    {
        return self::getStatusConfigByKey($status)['submittable'];
    }

    // ======================
    // WORKFLOW
    // ======================

    public static function nextStatus(string $current): ?string
    {
        return [
            'crm_to_wqs' => 'wqs_ready',
            'wqs_on_hold' => 'wqs_ready',
            'scm_on_delivery' => 'scm_delivered',
            'act_tukar_faktur' => 'act_invoiced',
            'fin_on_collect' => 'fin_paid',
        ][$current] ?? null;
    }

    public static function canTransition(string $from, string $to): bool
    {
        $allowed = [
            'crm_to_wqs' => ['wqs_ready'],
            'wqs_ready' => ['scm_on_delivery'],
            'wqs_on_hold' => ['wqs_ready'],
            'scm_on_delivery' => ['scm_delivered'],
            'scm_delivered' => ['act_tukar_faktur'],
            'act_tukar_faktur' => ['act_invoiced'],
            'act_invoiced' => ['fin_on_collect'],
            'fin_on_collect' => ['fin_paid', 'fin_overdue'],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    /**
     * Status grouped by module
     */
    public static function statusesByModule(): array
    {
        $grouped = [];

        foreach (self::getStatusConfig() as $key => $config) {
            $grouped[$config['module']][$key] = $config['label'];
        }

        return $grouped;
    }
}
