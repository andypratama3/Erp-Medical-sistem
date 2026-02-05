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
                'can' => 'view-dashboard', // Single permission
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'Data Master',
                'can' => 'view-master-data', // Check parent permission
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
                ]
            ],
           [
                'icon' => 'ui-elements',
                'name' => 'Sales DO',
                'path' => '/crm/sales-do',
                'can' => 'view-sales', // Single permission
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'WQS',
                'can' => 'view-wqs', // Parent permission
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
                        'name' => 'Inventory',
                        'path' => '/wqs/inventory',
                        'can' => 'view-inventory'
                    ],
                ]
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'SCM',
                'can' => 'view-scm', // Parent permission
                'subItems' => [
                    [
                        'name' => 'Drivers',
                        'path' => '/scm/drivers',
                        'can' => 'view-drivers'
                    ],
                    [
                        'name' => 'Task Board',
                        'path' => '/scm/task-board',
                        'can' => 'view-scm-tasks'
                    ],
                    [
                        'name' => 'Delivery',
                        'path' => '/scm/deliveries',
                        'can' => 'view-deliveries'
                    ],
                    [
                        'name' => 'Vehicles',
                        'path' => '/scm/vehicles',
                        'can' => 'view-vehicles'
                    ],
                ]
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'ACT',
                'can' => 'view-act', // Parent permission
                'subItems' => [
                    [
                        'name' => 'Task Board',
                        'path' => '/act/task-board',
                        'can' => 'view-act-tasks'
                    ],
                    [
                        'name' => 'Invoice',
                        'path' => '/act/invoices',
                        'can' => 'view-invoices'
                    ],
                ]
            ],
            [
                'icon' => 'ui-elements',
                'name' => 'FIN',
                'can' => 'view-fin', // Parent permission
                'subItems' => [
                    [
                        'name' => 'Task Board',
                        'path' => '/fin/task-board',
                        'can' => 'view-fin-tasks'
                    ],
                    [
                        'name' => 'Collection',
                        'path' => '/fin/collections',
                        'can' => 'view-collections'
                    ],
                    [
                        'name' => 'Aging',
                        'path' => '/fin/aging',
                        'can' => 'view-aging'
                    ],
                ]
            ],
            [
                'icon' => 'gear',
                'name' => 'Settings',
                'can' => ['manage-users', 'manage-roles', 'manage-permissions'], // Multiple permissions - canAny
                'subItems' => [
                    [
                        'name' => 'Users',
                        'path' => '/management-system/users',
                        'can' => 'manage-users'
                    ],
                    [
                        'name' => 'Role',
                        'path' => '/management-system/roles',
                        'can' => 'manage-roles'
                    ],
                    [
                        'name' => 'Permission',
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
                
                // Remove parent if no child items remain
                if (empty($item['subItems'])) {
                    return null;
                }
            }

            return $item;
        })->filter()->values()->all();
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

            'gear' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.0533 2.25C11.3957 2.25 11.7099 2.40629 11.9163 2.66797L12.4707 3.46335C12.7953 3.92236 13.3895 4.16493 13.979 4.06205L14.8949 3.91447C15.3827 3.83313 15.8543 4.02839 16.1516 4.43934L16.7903 5.43469C17.0876 5.84564 17.0876 6.40186 16.7903 6.81281L16.1516 7.80816C15.8543 8.2191 15.3827 8.41437 14.8949 8.33303L13.979 8.18545C13.3895 8.08257 12.7953 8.32514 12.4707 8.78415L11.9163 9.57953C11.7099 9.84121 11.3957 9.9975 11.0533 9.9975C10.7109 9.9975 10.3967 9.84121 10.1903 9.57953L9.63595 8.78415C9.31133 8.32514 8.71714 8.08257 8.12769 8.18545L7.21176 8.33303C6.72394 8.41437 6.25227 8.2191 5.95497 7.80816L5.31625 6.81281C5.01895 6.40186 5.01895 5.84564 5.31625 5.43469L5.95497 4.43934C6.25227 4.02839 6.72394 3.83313 7.21176 3.91447L8.12769 4.06205C8.71714 4.16493 9.31133 3.92236 9.63595 3.46335L10.1903 2.66797C10.3967 2.40629 10.7109 2.25 11.0533 2.25ZM11.0533 7.49C9.7929 7.49 8.77074 6.46784 8.77074 5.2075C8.77074 3.94716 9.7929 2.925 11.0533 2.925C12.3136 2.925 13.3358 3.94716 13.3358 5.2075C13.3358 6.46784 12.3136 7.49 11.0533 7.49Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.34012 11.0487C4.57171 10.7663 4.95186 10.6886 5.28855 10.8489L6.06723 11.2402C6.60928 11.5156 7.25341 11.4216 7.68933 11.0002L8.32804 10.3215C8.62534 10.011 9.09702 9.87369 9.54646 10.0334L10.4624 10.3767C11.0518 10.5986 11.7127 10.3968 12.1084 9.84557L12.6628 9.04319C12.977 8.63224 13.555 8.63224 13.8692 9.04319L14.4236 9.84557C14.8193 10.3968 15.4802 10.5986 16.0696 10.3767L16.9856 10.0334C17.435 9.87369 17.9067 10.011 18.204 10.3215L18.8427 11.0002C19.2786 11.4216 19.9227 11.5156 20.4648 11.2402L21.2435 10.8489C21.5802 10.6886 21.9603 10.7663 22.1919 11.0487C22.4235 11.3312 22.3822 11.733 22.0954 11.963L21.3166 12.3543C20.7746 12.6297 20.5768 13.2759 20.7982 13.8598L21.1415 14.7757C21.3634 15.3651 21.1616 16.026 20.6104 16.4217L19.8317 17.1004C19.5344 17.3975 19.5344 17.9538 19.8317 18.2509L20.6104 18.9296C21.1616 19.3253 21.3634 19.9862 21.1415 20.5756L20.7982 21.4915C20.5768 22.0754 19.7746 22.2772 20.7746 22.5528L20.3166 22.956C19.9227 23.1844 19.4409 23.2657 19.0041 23.0454L18.8427 21.9496C18.204 22.1715 17.435 21.9697 16.9856 21.4184L16.0696 20.6233C15.4802 20.4014 14.8193 20.6032 14.4236 21.1545L13.8692 21.9569C13.555 22.3678 12.977 22.3678 12.6628 21.9569L12.1084 21.1545C11.7127 20.6032 11.0518 20.4014 10.4624 20.6233L9.54646 20.9666C9.09702 21.1263 8.62534 20.989 8.32804 20.6785L7.68933 19.9998C7.25341 19.5784 6.60928 19.4844 6.06723 19.7598L5.28855 20.1511C4.95186 20.3114 4.57171 20.2337 4.34012 19.9513C4.10854 19.6688 4.14986 19.267 4.43667 19.037L5.21535 18.6457C5.75739 18.3703 5.95517 17.7241 5.73375 17.1402L5.39046 16.2243C5.16858 15.6349 5.37037 14.974 5.92157 14.5783L6.70025 13.8996C6.99755 13.6025 6.99755 13.0462 6.70025 12.7491L5.92157 12.0704C5.37037 11.6747 5.16858 11.0138 5.39046 10.4244L5.73375 9.50853C5.95517 8.92464 6.75739 8.72286 5.81535 8.44729L4.43667 9.053C4.14986 9.283 4.10854 9.68482 4.34012 9.9673Z" fill="currentColor"></path></svg>',
        ];

        return $icons[$iconName] ?? '<svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>';
    }
}