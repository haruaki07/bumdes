<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Configure backup settings for the application.
    |
    */

    'enabled' => env('BACKUP_ENABLED', true),

    'schedule' => [
        'enabled' => env('BACKUP_SCHEDULE_ENABLED', true),
        'frequency' => env('BACKUP_SCHEDULE_FREQUENCY', 'weekly'), // daily, weekly, monthly
        'day' => env('BACKUP_SCHEDULE_DAY', 0), // 0 = Sunday, 1 = Monday, etc.
        'time' => env('BACKUP_SCHEDULE_TIME', '02:00'),
    ],

    'retention' => [
        'enabled' => env('BACKUP_RETENTION_ENABLED', true),
        'keep_last' => env('BACKUP_RETENTION_KEEP_LAST', 10),
        'keep_days' => env('BACKUP_RETENTION_KEEP_DAYS', 30),
    ],

    'paths' => [
        'backup' => storage_path('app/backups'),
        'database' => storage_path('app/backups/database'),
        'files' => storage_path('app/backups/files'),
        'temp' => storage_path('app/backups/temp'),
    ],

    'database' => [
        'enabled' => env('BACKUP_DATABASE_ENABLED', true),
        'connection' => env('DB_CONNECTION', 'mysql'),
        'tables' => [], // Empty array = all tables, or specify tables to backup
        'exclude_tables' => ['sessions', 'cache', 'jobs', 'failed_jobs'], // Tables to exclude
    ],

    'files' => [
        'enabled' => env('BACKUP_FILES_ENABLED', true),
        'include' => [
            storage_path('app/public'),
            public_path('uploads'),
            // Add more paths as needed
        ],
        'exclude' => [
            storage_path('app/backups'),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ],
    ],

    'notifications' => [
        'email' => [
            'enabled' => env('BACKUP_NOTIFICATION_EMAIL_ENABLED', true),
            'to' => env('BACKUP_NOTIFICATION_EMAIL_TO', env('MAIL_FROM_ADDRESS')),
            'on_success' => env('BACKUP_NOTIFICATION_EMAIL_ON_SUCCESS', true),
            'on_failure' => env('BACKUP_NOTIFICATION_EMAIL_ON_FAILURE', true),
        ],
        'database' => [
            'enabled' => env('BACKUP_NOTIFICATION_DATABASE_ENABLED', true),
            'on_success' => env('BACKUP_NOTIFICATION_DATABASE_ON_SUCCESS', true),
            'on_failure' => env('BACKUP_NOTIFICATION_DATABASE_ON_FAILURE', true),
        ],
    ],

    'compression' => [
        'enabled' => env('BACKUP_COMPRESSION_ENABLED', true),
        'level' => env('BACKUP_COMPRESSION_LEVEL', 6), // 1-9, where 9 is highest compression
    ],

];
