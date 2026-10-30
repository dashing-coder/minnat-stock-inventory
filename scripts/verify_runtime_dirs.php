<?php

declare(strict_types=1);

/**
 * M/S Afrin Traders ERP — Laravel runtime directory preflight.
 *
 * Run from any directory:
 *   php scripts/verify_runtime_dirs.php --repair
 *   php scripts/verify_runtime_dirs.php --check
 *
 * --repair: create missing directories, then test writable runtime directories.
 * --check: never create or delete anything; only report directory status.
 *
 * This tool deliberately does not touch .env, APP_KEY, databases, or cache data.
 */

$root = dirname(__DIR__);
if (!is_file($root . '/artisan') || !is_file($root . '/bootstrap/app.php')) {
    fwrite(STDERR, "ERROR: Cannot identify the Laravel project root.\n");
    exit(2);
}

$mode = $argv[1] ?? '--repair';
if (!in_array($mode, ['--repair', '--check'], true)) {
    fwrite(STDERR, "Usage: php scripts/verify_runtime_dirs.php [--repair|--check]\n");
    exit(2);
}

$paths = [
    'storage/app',
    'storage/app/private',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

$failures = 0;
echo "Laravel runtime directory preflight (" . substr($mode, 2) . ")\n";
echo "Project: {$root}\n\n";

foreach ($paths as $relative) {
    $absolute = $root . '/' . $relative;

    if (is_link($absolute)) {
        echo "[FAIL] {$relative}: symbolic link requires manual review\n";
        $failures++;
        continue;
    }

    if (!is_dir($absolute)) {
        if ($mode === '--check') {
            echo "[FAIL] {$relative}: missing\n";
            $failures++;
            continue;
        }
        if (!@mkdir($absolute, 0775, true) && !is_dir($absolute)) {
            echo "[FAIL] {$relative}: could not create directory\n";
            $failures++;
            continue;
        }
        echo "[CREATED] {$relative}\n";
    } else {
        echo "[OK] {$relative}: exists\n";
    }

    // Laravel must be able to write into these paths. Create and delete only
    // a uniquely named probe file; do not alter existing sessions or caches.
    $probe = $absolute . '/.afrin_write_test_' . bin2hex(random_bytes(8));
    $handle = @fopen($probe, 'x');
    if ($handle === false) {
        echo "[FAIL] {$relative}: not writable by PHP CLI user\n";
        $failures++;
        continue;
    }
    fclose($handle);
    @unlink($probe);
}

if ($failures > 0) {
    fwrite(STDERR, "\nFAILED: {$failures} issue(s). Check path permissions and the messages above.\n");
    exit(1);
}

echo "\nPASS: all required directories exist and are writable by the PHP CLI user.\n";
echo "Note: Web-server PHP permissions may differ from CLI PHP permissions.\n";
exit(0);
