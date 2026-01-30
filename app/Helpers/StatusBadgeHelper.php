<?php

namespace App\Helpers;

class StatusBadgeHelper
{
    /**
     * Status configuration dengan badge styling
     * Menggunakan color palette dari tema CSS project
     *
     * ✅ FIX #4: All color values now lowercase for consistency
     */
    public static function getStatusConfig()
    {
        return [
            'primary' => [
                'label' => 'Primary',
                'color' => 'primary',
                'badge_class' => 'bg-blue-50 text-blue-500 dark:bg-blue-500/15 dark:text-blue-400',
                'icon' => 'circle',
            ],
            'success' => [
                'label' => 'Success',
                'color' => 'success',  // ✅ Fixed: was 'Success'
                'badge_class' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500',
                'icon' => 'check-circle',
            ],
            'active' => [
                'label' => 'Active',
                'color' => 'active',
                'badge_class' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500',
                'icon' => 'check-circle',
            ],
            'inactive' => [
                'label' => 'Inactive',
                'color' => 'inactive',
                'badge_class' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-500',
                'icon' => 'alert-circle',
            ],
            'error' => [
                'label' => 'Error',
                'color' => 'error',  // ✅ Fixed: was 'Error'
                'badge_class' => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-500',
                'icon' => 'alert-circle',
            ],
            'warning' => [
                'label' => 'Warning',
                'color' => 'warning',
                'badge_class' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500 dark:text-orange-400',
                'icon' => 'alert-triangle',
            ],
            'blocked' => [
                'label' => 'Blocked',
                'color' => 'blocked',
                'badge_class' => 'bg-danger text-yellow-600 dark:bg-red dark:text-orange-400',
                'icon' => 'alert-triangle',
            ],
            'info' => [
                'label' => 'Info',
                'color' => 'info',
                'badge_class' => 'bg-sky-50 text-sky-500 dark:bg-sky-500/15 dark:text-sky-500',
                'icon' => 'info-circle',
            ],
            'light' => [
                'label' => 'Light',
                'color' => 'light',
                'badge_class' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80',
                'icon' => 'sun',
            ],
            'dark' => [
                'label' => 'Dark',
                'color' => 'dark',
                'badge_class' => 'bg-gray-500 text-white dark:bg-white/5 dark:text-white',
                'icon' => 'moon',
            ],
            // CRM Module
            'crm_to_wqs' => [
                'label' => 'CRM to WQS',
                'color' => 'warning',
                'badge_class' => 'bg-warning-50 text-warning-900 dark:bg-warning-500 dark:text-warning-400',
                'icon' => 'clock',
            ],

            // WQS Module
            'wqs_ready' => [
                'label' => 'WQS Ready',
                'color' => 'blue-light',
                'badge_class' => 'bg-blue-light-50 text-blue-light-900 dark:bg-blue-light-500 dark:text-blue-light-400',
                'icon' => 'check-circle',
            ],

            'wqs_on_hold' => [
                'label' => 'WQS On Hold',
                'color' => 'error',
                'badge_class' => 'bg-error-50 text-error-900 dark:bg-error-500/15 dark:text-error-500',
                'icon' => 'alert-circle',
            ],

            // SCM Module
            'scm_on_delivery' => [
                'label' => 'On Delivery',
                'color' => 'brand',
                'badge_class' => 'bg-brand-50 text-brand-900 dark:bg-brand-500/15 dark:text-brand-400',
                'icon' => 'truck',
            ],

            'scm_delivered' => [
                'label' => 'Delivered',
                'color' => 'success',
                'badge_class' => 'bg-success-50 text-success-900 dark:bg-success-500/15 dark:text-success-500',
                'icon' => 'check-circle',
            ],

            // ACT Module
            'act_tukar_faktur' => [
                'label' => 'Tukar Faktur',
                'color' => 'orange',
                'badge_class' => 'bg-orange-50 text-orange-900 dark:bg-orange-500/15 dark:text-orange-400',
                'icon' => 'file-text',
            ],

            'act_invoiced' => [
                'label' => 'Invoiced',
                'color' => 'success',
                'badge_class' => 'bg-success-50 text-success-900 dark:bg-success-500/15 dark:text-success-500',
                'icon' => 'check-circle',
            ],

            // FIN Module
            'fin_on_collect' => [
                'label' => 'On Collection',
                'color' => 'orange',
                'badge_class' => 'bg-orange-50 text-orange-900 dark:bg-orange-500/15 dark:text-orange-400',
                'icon' => 'hourglass',
            ],

            'fin_paid' => [
                'label' => 'Paid',
                'color' => 'success',
                'badge_class' => 'bg-success-50 text-success-900 dark:bg-success-500/15 dark:text-success-500',
                'icon' => 'check-circle',
            ],

            'fin_overdue' => [
                'label' => 'Overdue',
                'color' => 'error',
                'badge_class' => 'bg-error-50 text-error-900 dark:bg-error-500/15 dark:text-error-500',
                'icon' => 'alert-triangle',
            ],
        ];
    }

    /**
     * Get config untuk status tertentu
     */
    public static function getStatusConfigByKey($status)
    {
        $config = self::getStatusConfig();
        return $config[$status] ?? [
            'label' => 'Unknown',
            'color' => 'gray',
            'badge_class' => 'bg-gray-50 text-gray-900 dark:bg-gray-500/15 dark:text-gray-400',
            'icon' => 'help-circle',
        ];
    }

    /**
     * Get badge class untuk Alpine.js getStatusClass()
     * Format: { 'status': 'badge-class' }
     *
     * ✅ FIX #4: Maps using color value (which is now consistently lowercase)
     */
    public static function getStatusBadgeClasses()
    {
        $statuses = self::getStatusConfig();
        $classes = [];

        foreach ($statuses as $key => $status) {
            $classes[$status['color']] = $status['badge_class'];
        }

        return $classes;
    }

    /**
     * Get all statuses untuk dropdown/select
     */
    public static function getStatusOptions()
    {
        $statuses = self::getStatusConfig();
        $options = [];

        foreach ($statuses as $key => $status) {
            $options[$key] = $status['label'];
        }

        return $options;
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
     * Get badge class
     */
    public static function getStatusBadgeClass($status)
    {
        return self::getStatusConfigByKey($status)['badge_class'];
    }

    /**
     * Get status icon
     */
    public static function getStatusIcon($status)
    {
        return self::getStatusConfigByKey($status)['icon'];
    }

    /**
     * Helper untuk blade: menampilkan HTML badge
     */
    public static function renderBadge($status, $includeIcon = true)
    {
        $config = self::getStatusConfigByKey($status);
        $icon = $includeIcon ? '<span class="inline-block w-2 h-2 rounded-full mr-2 bg-current"></span>' : '';

        return "<span class=\"inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {$config['badge_class']}\">
            {$icon}{$config['label']}
        </span>";
    }

    /**
     * Get status grouped by module
     */
    public static function getStatusesByModule()
    {
        $statuses = self::getStatusConfig();
        $grouped = [
            'CRM' => [],
            'WQS' => [],
            'SCM' => [],
            'ACT' => [],
            'FIN' => [],
        ];

        foreach ($statuses as $key => $config) {
            if (strpos($key, 'crm_') === 0) {
                $grouped['CRM'][$key] = $config['label'];
            } elseif (strpos($key, 'wqs_') === 0) {
                $grouped['WQS'][$key] = $config['label'];
            } elseif (strpos($key, 'scm_') === 0) {
                $grouped['SCM'][$key] = $config['label'];
            } elseif (strpos($key, 'act_') === 0) {
                $grouped['ACT'][$key] = $config['label'];
            } elseif (strpos($key, 'fin_') === 0) {
                $grouped['FIN'][$key] = $config['label'];
            }
        }

        // Remove empty modules
        return array_filter($grouped, fn($items) => !empty($items));
    }

    /**
     * Convert status config to JavaScript format
     * Untuk digunakan dalam Alpine.js
     */
    public static function toJavaScript()
    {
        $statuses = self::getStatusConfig();
        $jsClasses = [];

        foreach ($statuses as $key => $status) {
            $jsClasses[$status['label']] = $status['badge_class'];
        }

        return json_encode($jsClasses);
    }

    /**
     * Get status warna hex untuk styling custom
     */
    public static function getStatusColors()
    {
        return [
            'warning' => '#f79009',      // Orange/Yellow
            'blue-light' => '#0ba5ec',   // Light Blue
            'error' => '#f04438',        // Red
            'brand' => '#465fff',        // Brand Purple
            'success' => '#12b76a',      // Green
            'orange' => '#fb6514',       // Orange
            'gray' => '#667085',         // Gray
        ];
    }

    /**
     * Get status dengan warna hex
     */
    public static function getStatusWithColor($status)
    {
        $config = self::getStatusConfigByKey($status);
        $colors = self::getStatusColors();
        $color = $colors[$config['color']] ?? '#667085';

        return array_merge($config, ['hex_color' => $color]);
    }
}
