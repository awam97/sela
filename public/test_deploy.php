<?php
header('Content-Type: text/plain; charset=utf-8');

$file = __DIR__ . '/../app/Views/superadmin/settings/index.php';
if (!file_exists($file)) {
    echo "File not found at: $file\n";
    $file = __DIR__ . '/app/Views/superadmin/settings/index.php';
}

if (file_exists($file)) {
    echo "File exists! Path: " . realpath($file) . "\n";
    echo "MD5: " . md5_file($file) . "\n";
    echo "Line Count: " . count(file($file)) . "\n";
    echo "File Size: " . filesize($file) . " bytes\n";
    echo "\n--- FIRST 20 LINES ---\n";
    echo implode("", array_slice(file($file), 0, 20));
    echo "\n--- LINES 330 TO 380 ---\n";
    echo implode("", array_slice(file($file), 330, 50));
} else {
    echo "Could not locate settings index.php file anywhere!\n";
}
