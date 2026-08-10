<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channels
    |--------------------------------------------------------------------------
    |
    | Supported: "database", "mail", "fcm", "whatsapp"
    |
    */
    'default_channels' => ['database'],

    /*
    |--------------------------------------------------------------------------
    | Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Default fallback locale and model attribute to inspect for preferred locale.
    |
    */
    'default_locale' => 'ar',
    'locale_column'  => 'lang',

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    |
    */
    'queue' => [
        'enabled'       => true,
        'connection'    => env('NOTIFICATION_QUEUE_CONNECTION', 'default'),
        'queue_name'    => env('NOTIFICATION_QUEUE_NAME', 'notifications'),
        'backoff'       => [5, 15, 60],
        'max_tries'     => 3,
        'channel_queues' => [
            'mail'     => 'emails',
            'fcm'      => 'push-notifications',
            'whatsapp' => 'whatsapp',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Guard & User Models Mapping
    |--------------------------------------------------------------------------
    |
    | Defines guard names and their associated Eloquent models.
    |
    */
    'guards' => [
        // Example: Add your guard names and models here.
        // 'admin' => [
        //     'model' => \App\Models\Admin::class,
        //     'label' => 'Admins',
        // ],
        // 'student' => [
        //     'model' => \App\Models\Student::class,
        //     'label' => 'Students',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Self Guard (Exclude Sender)
    |--------------------------------------------------------------------------
    |
    | When sending from the dashboard, the authenticated user's guard name.
    | Users of this guard will be excluded from receiving self-sent notifications.
    | Set to null to disable this behavior.
    |
    */
    'self_guard' => null,

    /*
    |--------------------------------------------------------------------------
    | Channel Credentials & Driver Options
    |--------------------------------------------------------------------------
    */
    'channels' => [
        'whatsapp' => [
            'api_url'       => env('BULQ_WHATSAPP_API_URL'),
            'token'         => env('BULQ_WHATSAPP_TOKEN'),
            'template_name' => env('BULQ_WHATSAPP_TEMPLATE', 'login_otp'),
            'language'      => 'ar',
            'timeout'       => 15,
        ],
        'fcm' => [
            // The fully-qualified class name of your FCM driver.
            // Must implement \NotificationSystem\Contracts\FcmDriverInterface.
            // Set to null to use the built-in log-only mock.
            'driver_class' => env('NOTIFICATION_FCM_DRIVER', null),
            'timeout'      => 10,
        ],
        'mail' => [
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'from_name'    => env('MAIL_FROM_NAME', config('app.name', 'Laravel')),
            'logo'         => 'vendor/notification-system/images/logo.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Delivery Logs Table Configuration
    |--------------------------------------------------------------------------
    */
    'table_name' => 'notification_logs',
    'logging_enabled' => true,
    'log_retention_days' => 90, // Set to null to keep logs forever

    /*
    |--------------------------------------------------------------------------
    | Routes & Dashboard Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'dashboard' => [
            'enabled'    => true,
            'prefix'     => 'dashboard/notifications',
            'middleware' => ['web', 'auth:admin'],
            'as'         => 'dashboard.notifications.',
        ],
        'api' => [
            'enabled'    => true,
            'prefix'     => 'api/v1/notifications',
            'middleware' => ['api', 'auth'],
            'as'         => 'api.notifications.',

            // Optional: Fully-qualified API Resource class for notification responses.
            // e.g. \App\Http\Resources\NotificationResource::class
            'resource_class' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding & Author
    |--------------------------------------------------------------------------
    */
    'branding' => [
        'author'       => 'Abdulrhman Goda',
        'author_url'   => 'https://abdogoda.github.io/AG/',
        'app_name'     => env('APP_NAME', 'Laravel'),
        'primary_color'=> '#06b6d4',
        'logo_url'     => null,
    ],
];
