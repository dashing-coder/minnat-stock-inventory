<?php

return [
    // Never enable browser restore on a public production system without maintenance mode.
    'allow_web_restore' => env('ERP_ALLOW_WEB_RESTORE', false),
    // Legacy .json.gz files are unencrypted. They are preserved but cannot be restored by default.
    'allow_legacy_restore' => env('ERP_ALLOW_LEGACY_BACKUP_RESTORE', false),
    'retention_days' => env('ERP_BACKUP_RETENTION_DAYS', 30),
    // Off-site replication is encrypted-only, private S3-compatible and verified by re-reading.
    'offsite_enabled' => env('ERP_BACKUP_OFFSITE_ENABLED', false),
    'offsite_disk' => 'erp_offsite',
    'offsite_prefix' => env('ERP_BACKUP_OFFSITE_PREFIX', 'afrin-erp'),
    // Enable only AFTER the remote destination is verified; retain existing local backups meanwhile.
    'require_offsite' => env('ERP_BACKUP_REQUIRE_OFFSITE', false),
    // DR drills are permitted only on dedicated NON-production staging installations.
    'drill_enabled' => env('ERP_BACKUP_DRILL_ENABLED', false),
    'drill_database' => env('ERP_BACKUP_DRILL_DATABASE', ''),
    'drill_connection' => [
        'host' => env('ERP_BACKUP_DRILL_HOST'),
        'port' => env('ERP_BACKUP_DRILL_PORT'),
        'username' => env('ERP_BACKUP_DRILL_USERNAME'),
        'password' => env('ERP_BACKUP_DRILL_PASSWORD'),
    ],
    'max_archive_bytes' => env('ERP_BACKUP_MAX_ARCHIVE_BYTES', 150 * 1024 * 1024),
];
