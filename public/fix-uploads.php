<?php
/**
 * Quick Fix Script - Creates uploads directory
 *
 * Access: https://yourdomain.com/fix-uploads.php?token=NRB-SETUP-2024
 * DELETE AFTER USE!
 */

$token = $_GET['token'] ?? '';
if ($token !== 'NRB-SETUP-2024') {
    die('Forbidden');
}

echo "<pre>";
echo "=== Quick Fix: Creating uploads directory ===\n\n";

$uploadsDir = __DIR__ . '/uploads';

// Create main directory
if (!is_dir($uploadsDir)) {
    if (mkdir($uploadsDir, 0755, true)) {
        echo "✅ Created: $uploadsDir\n";
    } else {
        echo "❌ Failed to create: $uploadsDir\n";
    }
} else {
    echo "✓ Exists: $uploadsDir\n";
}

// Create subdirectories
$dirs = ['settings', 'avatars', 'ranks', 'gateways', 'gateway-qr', 'payment-methods', 'kyc-documents', 'funding-applications'];

foreach ($dirs as $dir) {
    $path = $uploadsDir . '/' . $dir;
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            echo "✅ Created: uploads/$dir\n";
        } else {
            echo "❌ Failed: uploads/$dir\n";
        }
    } else {
        echo "✓ Exists: uploads/$dir\n";
    }
}

// Fix permissions
chmod($uploadsDir, 0755);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($iterator as $item) {
    chmod($item->getPathname(), $item->isDir() ? 0755 : 0644);
}
echo "\n✅ Permissions fixed\n";

// Test write
echo "\nWrite test: ";
$testFile = $uploadsDir . '/test-' . time() . '.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✅ SUCCESS\n";
    unlink($testFile);
} else {
    echo "❌ FAILED\n";
}

// Verify
echo "\n=== Directory listing ===\n";
foreach (scandir($uploadsDir) as $item) {
    if ($item === '.' || $item === '..') continue;
    echo (is_dir($uploadsDir . '/' . $item) ? '[DIR] ' : '[FILE] ') . "$item\n";
}

echo "\n✅ Done! Now test uploading an image.\n";
echo "⚠️ DELETE this file (fix-uploads.php) now!\n";
echo "</pre>";
