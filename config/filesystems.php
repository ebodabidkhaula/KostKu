<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('SUPABASE_BUCKET'),
            'endpoint' => env('SUPABASE_ENDPOINT') ?: (
                str_ends_with(env('SUPABASE_URL', ''), '/rest/v1')
                    ? substr(env('SUPABASE_URL'), 0, -strlen('/rest/v1'))
                    : env('SUPABASE_URL')
            ),
            'public' => true,
            'use_path_style_endpoint' => true,
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
