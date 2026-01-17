<?php
/**
 * Quick Fix Script - Creates uploads directory
 * 
 * This script REMOVES any symlink and creates a REAL directory.
 *
 * Access: https://yourdomain.com/fix-uploads.php?token=NRB-SETUP-2024
 * DELETE AFTER USE!
 */

$token = $_GET['token'] ?? '';
if ($token !== 'NRB-SETUP-2024') {
    die('Forbidden');
}

echo "<pre>";
echo "=== Quick Fix: Creating REAL uploads directory ===\n\n";

$uploadsDir = __DIR__ . '/uploads';

// Check what exists at this path
echo "Checking: $uploadsDir\n";
echo "  - file_exists(): " . (file_exists($uploadsDir) ? 'true' : 'false') . "\n";
echo "  - is_link(): " . (is_link($uploadsDir) ? 'true' : 'false') . "\n";
echo "  - is_dir(): " . (is_dir($uploadsDir) ? 'true' : 'false') . "\n";
echo "  - is_file(): " . (is_file($uploadsDir) ? 'true' : 'false') . "\n";

if (is_link($uploadsDir)) {
    $target = @readlink($uploadsDir);
    echo "  - symlink target: " . ($target ?: 'unreadable') . "\n";
}

// FORCE REMOVE if it's a symlink (regardless of whether it works)
if (is_link($uploadsDir)) {
    echo "\n⚠️ Found SYMLINK at uploads path - REMOVING IT...\n";
    if (@unlink($uploadsDir)) {
        echo "✅ Removed symlink successfully\n";
    } else {
        echo "❌ Failed to remove symlink via PHP\n";
        echo "\nRun this in cPanel Terminal:\n";
        echo "  rm -f " . $uploadsDir . "\n";
        echo "  mkdir -p " . $uploadsDir . "\n";
        echo "</pre>";
        exit;
    }
}

// Also check parent directory for symlink (public_html/uploads)
$parentUploads = dirname(__DIR__) . '/uploads';
echo "\nChecking parent: $parentUploads\n";
echo "  - is_link(): " . (is_link($parentUploads) ? 'true' : 'false') . "\n";
if (is_link($parentUploads)) {
    echo "  - symlink target: " . @readlink($parentUploads) . "\n";
    echo "  ⚠️ This symlink in public_html may be fine (for web access)\n";
}

// Create main directory
if (!is_dir($uploadsDir)) {
    if (@mkdir($uploadsDir, 0755, true)) {
        echo "✅ Created: $uploadsDir\n";
    } else {
        $error = error_get_last();
        echo "❌ Failed to create: $uploadsDir\n";
        echo "   Error: " . ($error['message'] ?? 'Unknown') . "\n";

        // Try to see what's blocking
        if (is_link($uploadsDir)) {
            echo "   Reason: Symlink exists at path\n";
        } elseif (is_file($uploadsDir)) {
            echo "   Reason: File exists at path\n";
        }
    }
} else {
    echo "✓ Exists: $uploadsDir\n";
}

// Only continue if uploads directory exists
if (!is_dir($uploadsDir)) {
    echo "\n❌ Cannot continue - uploads directory doesn't exist.\n";
    echo "Try running in cPanel Terminal:\n";
    echo "  rm -f /home/zpprnrpp/public_html/public/uploads\n";
    echo "  mkdir -p /home/zpprnrpp/public_html/public/uploads\n";
    echo "</pre>";
    exit;
}

// Create subdirectories
$dirs = ['settings', 'avatars', 'ranks', 'gateways', 'gateway-qr', 'payment-methods', 'kyc-documents', 'funding-applications'];

foreach ($dirs as $dir) {
    $path = $uploadsDir . '/' . $dir;
    if (!is_dir($path)) {
        if (@mkdir($path, 0755, true)) {
            echo "✅ Created: uploads/$dir\n";
        } else {
            echo "❌ Failed: uploads/$dir\n";
        }
    } else {
        echo "✓ Exists: uploads/$dir\n";
    }
}

// Fix permissions
@chmod($uploadsDir, 0755);
if (is_dir($uploadsDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        @chmod($item->getPathname(), $item->isDir() ? 0755 : 0644);
    }
    echo "\n✅ Permissions fixed\n";
}

// Test write
echo "\nWrite test: ";
$testFile = $uploadsDir . '/test-' . time() . '.txt';
if (@file_put_contents($testFile, 'test')) {
    echo "✅ SUCCESS\n";
    @unlink($testFile);
} else {
    echo "❌ FAILED\n";
}

// Verify
echo "\n=== Directory listing ===\n";
if (is_dir($uploadsDir)) {
    foreach (scandir($uploadsDir) as $item) {
        if ($item === '.' || $item === '..') continue;
        echo (is_dir($uploadsDir . '/' . $item) ? '[DIR] ' : '[FILE] ') . "$item\n";
    }
}

echo "\n✅ Done! Now test uploading an image.\n";
echo "⚠️ DELETE this file (fix-uploads.php) now!\n";
echo "</pre>";