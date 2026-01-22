<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Settings Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the default settings for the WauminiLink system.
    | These settings can be overridden through the admin interface.
    |
    */

    'defaults' => [
        // General Settings
        'app_name' => [
            'value' => 'Waumini Link',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'The name of the church management system',
            'validation_rules' => ['required', 'string', 'max:255']
        ],
        'app_version' => [
            'value' => '1.0.0',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Current version of the system',
            'validation_rules' => ['required', 'string']
        ],
        'church_name' => [
            'value' => 'KKKT Ushirika wa Longuo',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Name of the church',
            'validation_rules' => ['required', 'string', 'max:255']
        ],
        'church_address' => [
            'value' => '',
            'type' => 'text',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Physical address of the church',
            'validation_rules' => ['nullable', 'string']
        ],
        'church_phone' => [
            'value' => '',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Primary phone number',
            'validation_rules' => ['nullable', 'string', 'max:20']
        ],
        'church_email' => [
            'value' => '',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Primary email address',
            'validation_rules' => ['nullable', 'email', 'max:255']
        ],
        'timezone' => [
            'value' => 'Africa/Dar_es_Salaam',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'System timezone',
            'validation_rules' => ['required', 'string'],
            'options' => [
                'Africa/Dar_es_Salaam' => 'Dar es Salaam (EAT)',
                'Africa/Nairobi' => 'Nairobi (EAT)',
                'Africa/Kampala' => 'Kampala (EAT)',
                'UTC' => 'UTC'
            ]
        ],
        'date_format' => [
            'value' => 'd/m/Y',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Default date format',
            'validation_rules' => ['required', 'string'],
            'options' => [
                'd/m/Y' => 'DD/MM/YYYY',
                'm/d/Y' => 'MM/DD/YYYY',
                'Y-m-d' => 'YYYY-MM-DD',
                'd-m-Y' => 'DD-MM-YYYY'
            ]
        ],
        'currency' => [
            'value' => 'TZS',
            'type' => 'string',
            'category' => 'general',
            'group' => 'basic',
            'description' => 'Default currency',
            'validation_rules' => ['required', 'string', 'size:3'],
            'options' => [
                'TZS' => 'Tanzanian Shilling',
                'USD' => 'US Dollar',
                'EUR' => 'Euro',
                'GBP' => 'British Pound'
            ]
        ],

        // Membership Settings
        'child_max_age' => [
            'value' => 18,
            'type' => 'integer',
            'category' => 'membership',
            'group' => 'basic',
            'description' => 'Maximum age for a child before auto-conversion to independent person',
            'validation_rules' => ['required', 'integer', 'min:1', 'max:30']
        ],
        'age_reference' => [
            'value' => 'today',
            'type' => 'string',
            'category' => 'membership',
            'group' => 'basic',
            'description' => 'Reference date for age calculations',
            'validation_rules' => ['required', 'in:today,end_of_year'],
            'options' => [
                'today' => 'Today',
                'end_of_year' => 'End of Year'
            ]
        ],
        'auto_generate_member_id' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'membership',
            'group' => 'basic',
            'description' => 'Automatically generate member IDs when adding new members',
            'validation_rules' => ['boolean']
        ],
        'member_id_prefix' => [
            'value' => 'WM',
            'type' => 'string',
            'category' => 'membership',
            'group' => 'basic',
            'description' => 'Prefix for member IDs',
            'validation_rules' => ['required', 'string', 'max:10']
        ],
        'require_phone_verification' => [
            'value' => false,
            'type' => 'boolean',
            'category' => 'membership',
            'group' => 'advanced',
            'description' => 'Require phone number verification for new members',
            'validation_rules' => ['boolean']
        ],
        'allow_duplicate_phone' => [
            'value' => false,
            'type' => 'boolean',
            'category' => 'membership',
            'group' => 'advanced',
            'description' => 'Allow duplicate phone numbers for different members',
            'validation_rules' => ['boolean']
        ],

        // Finance Settings
        'enable_tithes' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Enable tithes management',
            'validation_rules' => ['boolean']
        ],
        'enable_offerings' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Enable offerings management',
            'validation_rules' => ['boolean']
        ],
        'enable_donations' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Enable donations management',
            'validation_rules' => ['boolean']
        ],
        'enable_pledges' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Enable pledges management',
            'validation_rules' => ['boolean']
        ],
        'enable_budgets' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Enable budget management',
            'validation_rules' => ['boolean']
        ],
        'enable_expenses' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Enable expense management',
            'validation_rules' => ['boolean']
        ],
        'require_expense_approval' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'advanced',
            'description' => 'Require approval for expenses above threshold',
            'validation_rules' => ['boolean']
        ],
        'expense_approval_threshold' => [
            'value' => 100000,
            'type' => 'float',
            'category' => 'finance',
            'group' => 'advanced',
            'description' => 'Amount threshold requiring approval (in TZS)',
            'validation_rules' => ['required', 'numeric', 'min:0']
        ],
        'auto_generate_receipts' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'finance',
            'group' => 'basic',
            'description' => 'Automatically generate receipts for financial transactions',
            'validation_rules' => ['boolean']
        ],

        // Notification Settings
        'enable_email_notifications' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'notifications',
            'group' => 'basic',
            'description' => 'Enable email notifications',
            'validation_rules' => ['boolean']
        ],
        'enable_sms_notifications' => [
            'value' => false,
            'type' => 'boolean',
            'category' => 'notifications',
            'group' => 'basic',
            'description' => 'Enable SMS notifications',
            'validation_rules' => ['boolean']
        ],
        'sms_api_url' => [
            'value' => '',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS provider API endpoint URL',
            'validation_rules' => ['nullable', 'url']
        ],
        'sms_api_key' => [
            'value' => '',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS provider API key/token',
            'validation_rules' => ['nullable', 'string']
        ],
        'sms_username' => [
            'value' => '',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS provider username (for basic GET APIs)',
            'validation_rules' => ['nullable', 'string']
        ],
        'sms_password' => [
            'value' => '',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS provider password (for basic GET APIs)',
            'validation_rules' => ['nullable', 'string']
        ],
        'sms_sender_id' => [
            'value' => 'WAUMINI',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS sender ID/name',
            'validation_rules' => ['nullable', 'string', 'max:11']
        ],
        'sms_leader_appointment_template' => [
            'value' => "Hongera {{name}}! Umechaguliwa rasmi kuwa {{position}} wa kanisa la {{church_name}}.\n\nMungu akupe hekima, ujasiri na neema katika kutimiza wajibu huu wa kiroho.\n\nTunakuombea uongozi wenye upendo, umoja na maendeleo katika huduma ya Bwana.",
            'type' => 'text',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS template for leader appointment notifications. Use {{name}}, {{position}}, and {{church_name}} as placeholders.',
            'validation_rules' => ['nullable', 'string']
        ],
        'sms_payment_approval_template' => [
            'value' => "Hongera {{name}}! {{payment_type}} yako ya TZS {{amount}} tarehe {{date}} imethibitishwa na imepokelewa kikamilifu.\nAsante kwa mchango wako wa kiroho. Mungu akubariki!",
            'type' => 'text',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS template for payment approval notifications. Use {{name}}, {{payment_type}}, {{amount}}, and {{date}} as placeholders.',
            'validation_rules' => ['nullable', 'string']
        ],
        'notification_email' => [
            'value' => '',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'basic',
            'description' => 'Email address for sending notifications',
            'validation_rules' => ['nullable', 'email', 'max:255']
        ],
        'notification_sms_provider' => [
            'value' => 'local',
            'type' => 'string',
            'category' => 'notifications',
            'group' => 'advanced',
            'description' => 'SMS provider for notifications',
            'validation_rules' => ['required', 'in:local,africas_talking,nexmo'],
            'options' => [
                'local' => 'Local (Test)',
                'africas_talking' => 'Africa\'s Talking',
                'nexmo' => 'Nexmo'
            ]
        ],
        'celebrations_notification_days' => [
            'value' => 7,
            'type' => 'integer',
            'category' => 'notifications',
            'group' => 'basic',
            'description' => 'Days in advance to send celebration notifications',
            'validation_rules' => ['required', 'integer', 'min:1', 'max:30']
        ],
        'events_notification_days' => [
            'value' => 3,
            'type' => 'integer',
            'category' => 'notifications',
            'group' => 'basic',
            'description' => 'Days in advance to send event notifications',
            'validation_rules' => ['required', 'integer', 'min:1', 'max:14']
        ],

        // Security Settings
        'session_timeout' => [
            'value' => 120,
            'type' => 'integer',
            'category' => 'security',
            'group' => 'basic',
            'description' => 'Session timeout in minutes',
            'validation_rules' => ['required', 'integer', 'min:15', 'max:480']
        ],
        'require_password_change' => [
            'value' => false,
            'type' => 'boolean',
            'category' => 'security',
            'group' => 'advanced',
            'description' => 'Require password change on first login',
            'validation_rules' => ['boolean']
        ],
        'password_min_length' => [
            'value' => 8,
            'type' => 'integer',
            'category' => 'security',
            'group' => 'advanced',
            'description' => 'Minimum password length',
            'validation_rules' => ['required', 'integer', 'min:6', 'max:32']
        ],
        'max_login_attempts' => [
            'value' => 5,
            'type' => 'integer',
            'category' => 'security',
            'group' => 'advanced',
            'description' => 'Maximum login attempts before lockout',
            'validation_rules' => ['required', 'integer', 'min:3', 'max:10']
        ],
        'lockout_duration' => [
            'value' => 15,
            'type' => 'integer',
            'category' => 'security',
            'group' => 'advanced',
            'description' => 'Lockout duration in minutes',
            'validation_rules' => ['required', 'integer', 'min:5', 'max:60']
        ],

        // Appearance Settings
        'theme_color' => [
            'value' => 'waumini',
            'type' => 'string',
            'category' => 'appearance',
            'group' => 'basic',
            'description' => 'Primary theme color',
            'validation_rules' => ['required', 'in:waumini,primary,secondary,success,danger,warning,info'],
            'options' => [
                'waumini' => 'Waumini Purple (Primary)',
                'primary' => 'Blue',
                'secondary' => 'Gray (Secondary)',
                'success' => 'Green (Success)',
                'danger' => 'Red (Danger)',
                'warning' => 'Yellow (Warning)',
                'info' => 'Cyan (Info)'
            ]
        ],
        'sidebar_style' => [
            'value' => 'dark',
            'type' => 'string',
            'category' => 'appearance',
            'group' => 'basic',
            'description' => 'Sidebar style',
            'validation_rules' => ['required', 'in:dark,light'],
            'options' => [
                'dark' => 'Dark Sidebar',
                'light' => 'Light Sidebar'
            ]
        ],
        'show_member_photos' => [
            'value' => true,
            'type' => 'boolean',
            'category' => 'appearance',
            'group' => 'basic',
            'description' => 'Show member photos in member lists',
            'validation_rules' => ['boolean']
        ],
        'items_per_page' => [
            'value' => 25,
            'type' => 'integer',
            'category' => 'appearance',
            'group' => 'basic',
            'description' => 'Number of items per page in lists',
            'validation_rules' => ['required', 'integer', 'min:10', 'max:100'],
            'options' => [
                10 => '10 items',
                25 => '25 items',
                50 => '50 items',
                100 => '100 items'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings Categories
    |--------------------------------------------------------------------------
    |
    | Define the categories and their display information
    |
    */
    'categories' => [
        'general' => [
            'name' => 'General Settings',
            'description' => 'Basic system configuration',
            'icon' => 'fas fa-cog',
            'color' => 'primary'
        ],
        'membership' => [
            'name' => 'Membership Settings',
            'description' => 'Member management configuration',
            'icon' => 'fas fa-users',
            'color' => 'success'
        ],
        'finance' => [
            'name' => 'Finance Settings',
            'description' => 'Financial management configuration',
            'icon' => 'fas fa-money-bill-wave',
            'color' => 'warning'
        ],
        'notifications' => [
            'name' => 'Notification Settings',
            'description' => 'Email and SMS notification configuration',
            'icon' => 'fas fa-bell',
            'color' => 'info'
        ],
        'security' => [
            'name' => 'Security Settings',
            'description' => 'Security and authentication configuration',
            'icon' => 'fas fa-shield-alt',
            'color' => 'danger'
        ],
        'appearance' => [
            'name' => 'Appearance Settings',
            'description' => 'UI and display configuration',
            'icon' => 'fas fa-palette',
            'color' => 'secondary'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings Groups
    |--------------------------------------------------------------------------
    |
    | Define the groups within each category
    |
    */
    'groups' => [
        'basic' => [
            'name' => 'Basic Settings',
            'description' => 'Essential settings for system operation'
        ],
        'advanced' => [
            'name' => 'Advanced Settings',
            'description' => 'Advanced configuration options'
        ],
        'system' => [
            'name' => 'System Settings',
            'description' => 'Internal system configuration'
        ]
    ]
];
