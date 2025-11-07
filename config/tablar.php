<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    | Here you can change the default title of your admin panel.
    |
    */

    'title' => config('app.name'),
    'title_prefix' => '',
    'title_postfix' => '',
    'bottom_title' => config('app.name'),
    'current_version' => 'v0.0.0',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    */

    'logo' => '<b>'.config('app.name').'</b>',
    'logo_img_alt' => config('app.name'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can set up an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'assets/tablar-logo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
     *
     * Default path is 'resources/views/vendor/tablar' as null. Set your custom path here If you need.
     */

    'views_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look at the layout section here:
    |
    */

    'layout' => 'vertical',
    // boxed, combo, condensed, fluid, fluid-vertical, horizontal, navbar-overlap, navbar-sticky, rtl, vertical, vertical-right, vertical-transparent

    'layout_light_sidebar' => false,
    'layout_light_topbar' => false,
    'layout_enable_top_header' => true,

    /*
    |--------------------------------------------------------------------------
    | Sticky Navbar for Top Nav
    |--------------------------------------------------------------------------
    |
    | Here you can enable/disable the sticky functionality of Top Navigation Bar.
    |
    | For detailed instructions, you can look at the Top Navigation Bar classes here:
    |
    */

    'sticky_top_nav_bar' => true,

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions, you can look at the admin panel classes here:
    |
    */

    'classes_body' => '',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions, you can look at the urls section here:
    |
    */

    'use_route_url' => true,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password.request',
    'password_email_url' => 'password.email',
    'profile_url' => false,
    'setting_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Display Alert
    |--------------------------------------------------------------------------
    |
    | Display Alert Visibility.
    |
    */
    'display_alert' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    |
    */

    'menu' => [
        // Navbar items:
        [
            'text' => 'Home',
            'icon' => 'ti ti-home',
            'route' => 'dashboard',
        ],

        [
            'text' => 'Usaha',
            'icon' => 'ti ti-building',
            'route' => 'businesses.index',
        ],

        [
            'text' => 'Jenis Usaha',
            'icon' => 'ti ti-category',
            'route' => 'business-types.index',
        ],

        [
            'text' => 'Pengajuan Usaha',
            'icon' => 'ti ti-briefcase',
            'route' => 'business-registrations.index',
        ],

        // e-billing app
        [
            'group' => 'e-billing',
            'text' => 'Dashboard',
            'icon' => 'ti ti-layout-dashboard',
            'route' => 'e-billing.dashboard',
        ],

        [
            'group' => 'e-billing',
            'text' => 'Data Master',
            'url' => '#',
            'icon' => 'ti ti-database',
            'active' => ['e-billing/master-data/*'],
            'submenu' => [
                [
                    'group' => 'e-billing',
                    'text' => 'Site',
                    'route' => 'e-billing.master-data.sites.index',
                    'icon' => 'ti ti-world-pin',
                    'hasAnyPermission' => ['read-sites'],
                ],
                [
                    'group' => 'e-billing',
                    'text' => 'Perangkat',
                    'route' => 'e-billing.master-data.devices.index',
                    'icon' => 'ti ti-router',
                    'hasAnyPermission' => ['read-devices'],
                ],
                [
                    'group' => 'e-billing',
                    'text' => 'Paket',
                    'route' => 'e-billing.master-data.packages.index',
                    'icon' => 'ti ti-package',
                    'hasAnyPermission' => ['read-packages'],
                ],
                [
                    'group' => 'e-billing',
                    'text' => 'Pelanggan',
                    'route' => 'e-billing.master-data.customers.index',
                    'icon' => 'ti ti-users',
                    'hasAnyPermission' => ['read-customers'],
                ],
            ],
        ],

        [
            'group' => 'e-billing',
            'text' => 'Transaksi',
            'url' => '#',
            'icon' => 'ti ti-credit-card-pay',
            'active' => ['e-billing/invoices/*'],
            'submenu' => [
                [
                    'group' => 'e-billing',
                    'text' => 'Tagihan',
                    'icon' => 'ti ti-file-invoice',
                    'route' => 'e-billing.invoices.index',
                    'hasAnyPermission' => ['read-invoices'],
                ],
            ],
        ],

        [
            'group' => 'e-billing',
            'text' => 'Pengaturan',
            'url' => '#',
            'icon' => 'ti ti-settings',
            'active' => ['e-billing/settings/*'],
            'submenu' => [
                [
                    'group' => 'e-billing',
                    'text' => 'Manajemen Role',
                    'route' => 'e-billing.settings.roles.index',
                    'icon' => 'ti ti-lock',
                    'hasAnyPermission' => ['read-roles'],
                ],
                [
                    'group' => 'e-billing',
                    'text' => 'Manajemen User',
                    'route' => 'e-billing.settings.users.index',
                    'icon' => 'ti ti-users',
                    'hasAnyPermission' => ['read-users'],
                ],
                [
                    'group' => 'e-billing',
                    'text' => 'Metode Pembayaran',
                    'route' => 'e-billing.settings.payment-methods.index',
                    'icon' => 'ti ti-credit-card',
                    'hasAnyPermission' => ['read-payment-methods'],
                ],
                [
                    'group' => 'e-billing',
                    'text' => 'Pengaturan Sistem',
                    'route' => ['e-billing.settings.show', ['group' => 'account']],
                    'icon' => 'ti ti-settings',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    |
    */

    'filters' => [
        // TakiElias\Tablar\Menu\Filters\GateFilter::class,
        TakiElias\Tablar\Menu\Filters\HrefFilter::class,
        TakiElias\Tablar\Menu\Filters\SearchFilter::class,
        TakiElias\Tablar\Menu\Filters\ActiveFilter::class,
        TakiElias\Tablar\Menu\Filters\ClassesFilter::class,
        TakiElias\Tablar\Menu\Filters\LangFilter::class,
        TakiElias\Tablar\Menu\Filters\DataFilter::class,
        \App\Filters\RouteGroupFilter::class,
        \App\Filters\RolePermissionFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Vite
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Vite support.
    |
    | For detailed instructions you can look the Vite here:
    | https://laravel-vite.dev
    |
    */

    'vite' => true,

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://livewire.laravel.com
    |
    */

    'livewire' => false,
];
