<?php

namespace App\Helpers;

class MenuHelper
{
    /**
     * Get main navigation items with permission checks
     * 
     * @return array
     */
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'path' => route('dashboard'),
                'can' => 'view-dashboard',
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'Data Master',
                'can' => 'view-master-data',
                'subItems' => [
                    [
                        'name' => 'Branches',
                        'path' => '/master/branches',
                        'can' => 'view-branches'
                    ],
                    [
                        'name' => 'Offices',
                        'path' => '/master/offices',
                        'can' => 'view-offices'
                    ],
                    [
                        'name' => 'Departments',
                        'path' => '/master/departments',
                        'can' => 'view-departments'
                    ],
                    [
                        'name' => 'Employees',
                        'path' => '/master/employees',
                        'can' => 'view-employees'
                    ],
                    [
                        'name' => 'Customers',
                        'path' => '/master/customers',
                        'can' => 'view-customers'
                    ],
                    [
                        'name' => 'Vendors',
                        'path' => '/master/vendors',
                        'can' => 'view-vendors'
                    ],
                    [
                        'name' => 'Manufactures',
                        'path' => '/master/manufactures',
                        'can' => 'view-manufactures'
                    ],
                    [
                        'name' => 'Products',
                        'path' => '/master/products',
                        'can' => 'view-products'
                    ],
                    [
                        'name' => 'Taxes',
                        'path' => '/master/taxes',
                        'can' => 'view-taxes'
                    ],
                    [
                        'name' => 'Payment Terms',
                        'path' => '/master/payment-terms',
                        'can' => 'view-payment-terms'
                    ],
                    [
                        'name' => 'Bank Accounts',
                        'path' => '/master/bank-accounts',
                        'can' => 'view-bank-accounts'
                    ],
                    [
                        'name' => 'Company Emails',
                        'path' => '/master/email-company',
                        'can' => 'view-email-company'
                    ],
                    [
                        'name' => 'Discount Policy',
                        'path' => '/master/discount-policy',
                        'can' => 'view-discount-policy'
                    ],
                ]
            ],
            [
                'icon' => 'sales',
                'name' => 'Sales & CRM',
                'can' => 'view-sales',
                'subItems' => [
                    [
                        'name' => 'Sales DO',
                        'path' => '/crm/sales-do',
                        'can' => 'view-sales-do'
                    ],
                ]
            ],
            [
                'icon' => 'warehouse',
                'name' => 'WQS',
                'can' => 'view-wqs',
                'subItems' => [
                    [
                        'name' => 'Task Board',
                        'path' => '/wqs/task-board',
                        'can' => 'view-wqs-tasks'
                    ],
                    [
                        'name' => 'Stock Check',
                        'path' => '/wqs/stock-checks',
                        'can' => 'view-stock-checks'
                    ],
                    [
                        'name' => 'Stock Management',
                        'path' => '/wqs/stock',
                        'can' => 'view-stock'
                    ],
                    [
                        'name' => 'Purchase Requests',
                        'path' => '/wqs/purchase-requests',
                        'can' => 'view-purchase-requests'
                    ],
                    [
                        'name' => 'Stock Adjustments',
                        'path' => '/wqs/adjustments',
                        'can' => 'view-stock-adjustments'
                    ],
                    [
                        'name' => 'Stock Snapshots',
                        'path' => '/wqs/snapshots',
                        'can' => 'view-stock-snapshots'
                    ],
                    [
                        'name' => 'Inventory',
                        'path' => '/wqs/inventory',
                        'can' => 'view-inventory'
                    ],
                ]
            ],
            [
                'icon' => 'truck',
                'name' => 'SCM',
                'can' => 'view-scm',
                'subItems' => [
                    [
                        'name' => 'Task Board',
                        'path' => '/scm/task-board',
                        'can' => 'view-scm-tasks'
                    ],
                    [
                        'name' => 'Deliveries',
                        'path' => '/scm/deliveries',
                        'can' => 'view-deliveries'
                    ],
                    [
                        'name' => 'Drivers',
                        'path' => '/scm/drivers',
                        'can' => 'view-drivers'
                    ],
                    [
                        'name' => 'Vehicles',
                        'path' => '/scm/vehicles',
                        'can' => 'view-vehicles'
                    ],
                    [
                        'name' => 'Tracking',
                        'path' => '/scm/tracking',
                        'can' => 'view-tracking'
                    ],
                ]
            ],
            [
                'icon' => 'invoice',
                'name' => 'ACT',
                'can' => 'view-act',
                'subItems' => [
                    [
                        'name' => 'Task Board',
                        'path' => '/act/task-board',
                        'can' => 'view-act-tasks'
                    ],
                    [
                        'name' => 'Invoices',
                        'path' => '/act/invoices',
                        'can' => 'view-invoices'
                    ],
                ]
            ],
            [
                'icon' => 'money',
                'name' => 'FIN',
                'can' => 'view-fin',
                'subItems' => [
                    [
                        'name' => 'Task Board',
                        'path' => '/fin/task-board',
                        'can' => 'view-fin-tasks'
                    ],
                    [
                        'name' => 'Collections',
                        'path' => '/fin/collections',
                        'can' => 'view-collections'
                    ],
                    [
                        'name' => 'Payments',
                        'path' => '/fin/payments',
                        'can' => 'view-payments'
                    ],
                    [
                        'name' => 'Aging Report',
                        'path' => '/fin/aging',
                        'can' => 'view-aging'
                    ],
                ]
            ],
            [
                'icon' => 'shopping-cart',
                'name' => 'Purchasing',
                'can' => 'view-purchasing',
                'subItems' => [
                    [
                        'name' => 'Purchase Orders',
                        'path' => '/purchasing/purchase-orders',
                        'can' => 'view-purchase-orders'
                    ],
                    [
                        'name' => 'Import Control',
                        'path' => '/purchasing/import-control',
                        'can' => 'view-import-control'
                    ],
                    [
                        'name' => 'Forwarder Quotes',
                        'path' => '/purchasing/forwarder-quotes',
                        'can' => 'view-forwarder-quotes'
                    ],
                    [
                        'name' => 'Forwarder Invoices',
                        'path' => '/purchasing/forwarder-invoices',
                        'can' => 'view-forwarder-invoices'
                    ],
                    [
                        'name' => 'CEISA / PIB',
                        'path' => '/purchasing/ceisa',
                        'can' => 'view-ceisa'
                    ],
                    [
                        'name' => 'Forwarding Docs',
                        'path' => '/purchasing/forwarding-docs',
                        'can' => 'view-forwarding-docs'
                    ],
                    [
                        'name' => 'AP Invoices',
                        'path' => '/purchasing/ap-invoices',
                        'can' => 'view-ap-invoices'
                    ],
                    [
                        'name' => 'AP Payments',
                        'path' => '/purchasing/ap-payments',
                        'can' => 'view-ap-payments'
                    ],
                    [
                        'name' => 'Audit Log',
                        'path' => '/purchasing/audit-log',
                        'can' => 'view-purchasing-audit'
                    ],
                ]
            ],
            [
                'icon' => 'users',
                'name' => 'HR & Payroll',
                'can' => ['view-hr', 'view-payroll'],
                'subItems' => [
                    [
                        'name' => 'Employees',
                        'path' => '/hr/employees',
                        'can' => 'view-hr-employees'
                    ],
                    [
                        'name' => 'Attendance',
                        'path' => '/hr/attendance',
                        'can' => 'view-attendance'
                    ],
                    [
                        'name' => 'My Attendance',
                        'path' => '/hr/attendance/my-attendance',
                        'can' => 'view-own-attendance'
                    ],
                    [
                        'name' => 'Leave Requests',
                        'path' => '/hr/leave-requests',
                        'can' => 'view-leave-requests'
                    ],
                    [
                        'name' => 'HR Documents',
                        'path' => '/hr/documents',
                        'can' => 'view-hr-documents'
                    ],
                    [
                        'name' => 'Attendance Settings',
                        'path' => '/hr/settings',
                        'can' => 'manage-attendance-settings'
                    ],
                    ['divider' => true],
                    [
                        'name' => 'Salary Matrix',
                        'path' => '/payroll/salary-matrix',
                        'can' => 'view-salary-matrix'
                    ],
                    [
                        'name' => 'Employee Settings',
                        'path' => '/payroll/employee-settings',
                        'can' => 'view-payroll-settings'
                    ],
                    [
                        'name' => 'Payroll Runs',
                        'path' => '/payroll/runs',
                        'can' => 'view-payroll-runs'
                    ],
                    [
                        'name' => 'Loans & Kasbon',
                        'path' => '/payroll/loans',
                        'can' => 'view-loans'
                    ],
                    [
                        'name' => 'Payroll Reports',
                        'path' => '/payroll/reports/summary',
                        'can' => 'view-payroll-reports'
                    ],
                ]
            ],
            [
                'icon' => 'asset',
                'name' => 'Fixed Assets',
                'can' => 'view-fixed-assets',
                'subItems' => [
                    [
                        'name' => 'Assets',
                        'path' => '/fixed-assets/assets',
                        'can' => 'view-assets'
                    ],
                    [
                        'name' => 'Depreciation',
                        'path' => '/fixed-assets/depreciation',
                        'can' => 'view-depreciation'
                    ],
                    [
                        'name' => 'Maintenance',
                        'path' => '/fixed-assets/maintenance',
                        'can' => 'view-maintenance'
                    ],
                    [
                        'name' => 'Transfers',
                        'path' => '/fixed-assets/transfers',
                        'can' => 'view-transfers'
                    ],
                    [
                        'name' => 'Disposals',
                        'path' => '/fixed-assets/disposals',
                        'can' => 'view-disposals'
                    ],
                    [
                        'name' => 'Asset Audits',
                        'path' => '/fixed-assets/audits',
                        'can' => 'view-asset-audits'
                    ],
                ]
            ],
            [
                'icon' => 'gear',
                'name' => 'System',
                'can' => ['manage-system', 'view-audit-log'],
                'subItems' => [
                    [
                        'name' => 'Configuration',
                        'path' => '/system/config',
                        'can' => 'manage-system-config'
                    ],
                    [
                        'name' => 'Audit Log',
                        'path' => '/system/audit-log',
                        'can' => 'view-system-audit'
                    ],
                    ['divider' => true],
                    [
                        'name' => 'Users',
                        'path' => '/management-system/users',
                        'can' => 'manage-users'
                    ],
                    [
                        'name' => 'Roles',
                        'path' => '/management-system/roles',
                        'can' => 'manage-roles'
                    ],
                    [
                        'name' => 'Permissions',
                        'path' => '/management-system/permissions',
                        'can' => 'manage-permissions'
                    ],
                ]
            ],
        ];
    }

    /**
     * Get menu groups with filtered items based on permissions
     * 
     * @return array
     */
    public static function getMenuGroups()
    {
        $user = auth()->user();
        $items = self::getMainNavItems();

        // Filter items based on permissions
        $filtered = self::filterByPermission($items, $user);

        return [
            [
                'title' => 'Menu',
                'items' => $filtered
            ],
        ];
    }

    /**
     * Recursively filter menu items by permission
     * 
     * @param array $items
     * @param $user
     * @return array
     */
    private static function filterByPermission($items, $user)
    {
        return collect($items)->filter(function ($item) use ($user) {
            // Check for divider
            if (isset($item['divider'])) {
                return true;
            }

            // If no permission is specified, allow it
            if (!isset($item['can'])) {
                return true;
            }

            // If user is not authenticated, deny access
            if (!$user) {
                return false;
            }

            // Check single permission
            if (is_string($item['can'])) {
                return $user->can($item['can']);
            }

            // Check multiple permissions (canAny)
            if (is_array($item['can'])) {
                return $user->canAny($item['can']);
            }

            return false;
        })->map(function ($item) use ($user) {
            // Recursively filter subItems if they exist
            if (isset($item['subItems'])) {
                $item['subItems'] = self::filterByPermission($item['subItems'], $user);
                
                // Remove dividers that might be at the start or end after filtering
                $item['subItems'] = collect($item['subItems'])
                    ->filter(function($subItem, $index) use ($item) {
                        // Remove divider if it's the first item
                        if ($index === 0 && isset($subItem['divider'])) {
                            return false;
                        }
                        // Remove divider if it's the last item
                        if ($index === count($item['subItems']) - 1 && isset($subItem['divider'])) {
                            return false;
                        }
                        return true;
                    })
                    ->values()
                    ->all();
                
                // Remove parent if no child items remain
                if (empty($item['subItems'])) {
                    return null;
                }
            }
            return $item;
        });
    }

    /**
     * Check if a path is currently active
     * 
     * @param string $path
     * @return bool
     */
    public static function isActive($path)
    {
        return request()->is(ltrim($path, '/'));
    }

    /**
     * Get SVG icon by name
     * 
     * @param string $iconName
     * @return string
     */
    public static function getIconSvg($iconName)
    {
        $icons = [
            'dashboard' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z" fill="currentColor"></path></svg>',

            'ui-elements' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037ZM13.0066 2.41453C12.3732 2.09783 11.6277 2.09783 10.9942 2.41453L4.03676 5.89316C3.27449 6.27429 2.79297 7.05339 2.79297 7.90563V16.0946C2.79297 16.9468 3.27448 17.7259 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.7259 21.2079 16.9468 21.2079 16.0946V7.90563C21.2079 7.05339 20.7264 6.27429 19.9641 5.89316L13.0066 2.41453Z" fill="currentColor"></path></svg>',

            'sales' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'warehouse' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 10l8-6 8 6v10a2 2 0 01-2 2H6a2 2 0 01-2-2V10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 21V12h6v9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'truck' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 6h10v8H1V6zm12 0h6l3 3v5h-9V6zm-6 12a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'invoice' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'money' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'shopping-cart' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 2L2 9h7V22h6V9h7L15 2H9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'users' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'asset' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'gear' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.0533 2.25C11.3957 2.25 11.7099 2.40629 11.9163 2.66797L12.4707 3.46335C12.7953 3.92236 13.3895 4.16493 13.979 4.06205L14.8949 3.91447C15.3827 3.83313 15.8543 4.02839 16.1516 4.43934L16.7903 5.43469C17.0876 5.84564 17.0876 6.40186 16.7903 6.81281L16.1516 7.80816C15.8543 8.2191 15.3827 8.41437 14.8949 8.33303L13.979 8.18545C13.3895 8.08257 12.7953 8.32514 12.4707 8.78415L11.9163 9.57953C11.7099 9.84121 11.3957 9.9975 11.0533 9.9975C10.7109 9.9975 10.3967 9.84121 10.1903 9.57953L9.63595 8.78415C9.31133 8.32514 8.71714 8.08257 8.12769 8.18545L7.21176 8.33303C6.72394 8.41437 6.25227 8.2191 5.95497 7.80816L5.31625 6.81281C5.01895 6.40186 5.01895 5.84564 5.31625 5.43469L5.95497 4.43934C6.25227 4.02839 6.72394 3.83313 7.21176 3.91447L8.12769 4.06205C8.71714 4.16493 9.31133 3.92236 9.63595 3.46335L10.1903 2.66797C10.3967 2.40629 10.7109 2.25 11.0533 2.25ZM11.0533 7.49C9.7929 7.49 8.77074 6.46784 8.77074 5.2075C8.77074 3.94716 9.7929 2.925 11.0533 2.925C12.3136 2.925 13.3358 3.94716 13.3358 5.2075C13.3358 6.46784 12.3136 7.49 11.0533 7.49Z" fill="currentColor"></path></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}