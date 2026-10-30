<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],
        // Optional remote-only storage; requires league/flysystem-aws-s3-v3.
        // Use a private bucket in a different location/account, with least-privilege credentials.
        'erp_offsite' => [
            'driver' => 's3',
            'key' => env('ERP_BACKUP_S3_KEY'),
            'secret' => env('ERP_BACKUP_S3_SECRET'),
            'region' => env('ERP_BACKUP_S3_REGION', 'us-east-1'),
            'bucket' => env('ERP_BACKUP_S3_BUCKET'),
            'endpoint' => env('ERP_BACKUP_S3_ENDPOINT'),
            'use_path_style_endpoint' => env('ERP_BACKUP_S3_PATH_STYLE', false),
            'visibility' => 'private',
            'throw' => true,
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
