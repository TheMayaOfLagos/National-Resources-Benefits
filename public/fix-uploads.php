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

// Check what exists at this path
echo "Checking: $uploadsDir\n";
if (file_exists($uploadsDir)) {
    echo "  - file_exists: true\n";
} else {
    echo "  - file_exists: false\n";
}
if (is_link($uploadsDir)) {
    echo "  - is_link: true (target: " . readlink($uploadsDir) . ")\n";
} else {
    echo "  - is_link: false\n";
}
if (is_dir($uploadsDir)) {
    echo "  - is_dir: true\n";
} else {
    echo "  - is_dir: false\n";
}

// Remove broken symlink or file if it exists but isn't a valid directory
if ((is_link($uploadsDir) || file_exists($uploadsDir)) && !is_dir($uploadsDir)) {
    echo "\n⚠️ Found broken symlink or file, removing...\n";
    if (is_link($uploadsDir)) {
        unlink($uploadsDir);
        echo "✅ Removed broken symlink\n";
    }
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
