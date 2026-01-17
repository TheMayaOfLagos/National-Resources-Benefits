<?php
/**
 * Post-Deployment Setup Script
 * 
 * Run this script once after deploying to cPanel to:
 * 1. Create uploads directory structure in public_html/uploads (NOT public_html/public/uploads)
 * 2. Migrate files from storage to uploads directory
 * 3. Fix permissions
 * 
 * Access via: https://yourdomain.com/setup-storage.php?token=YOUR_TOKEN
 * DELETE THIS FILE AFTER RUNNING!
 */

// Security check - only allow from specific IPs or with a token
$allowedToken = 'NRB-SETUP-2024'; // Change this!
$providedToken = $_GET['token'] ?? '';

if ($providedToken !== $allowedToken) {
    http_response_code(403);
    die('Forbidden - Invalid token. Use: ?token=YOUR_TOKEN');
}

echo "<h1>Storage Migration Script</h1>";
echo "<pre>";

// __DIR__ is public_html/public, so we go up one level to get public_html
$publicHtmlPath = dirname(__DIR__);
$oldStoragePath = $publicHtmlPath . '/storage/app/public';
$wrongUploadsPath = __DIR__ . '/uploads'; // Wrong path: public_html/public/uploads
$newUploadsPath = $publicHtmlPath . '/uploads'; // Correct path: public_html/uploads

echo "Script location: " . __DIR__ . "\n";
echo "Public HTML path: $publicHtmlPath\n";
echo "Old storage path: $oldStoragePath\n";
echo "Correct uploads path: $newUploadsPath\n";
echo "Wrong uploads path: $wrongUploadsPath\n\n";

// Required subdirectories for Filament uploads
$requiredDirs = [
    'settings',
    'avatars', 
    'ranks',
    'gateways',
    'gateway-qr',
    'payment-methods',
    'kyc-documents',
    'funding-applications',
];

// Create uploads directory and all subdirectories in the CORRECT location (public_html/uploads)
echo "Creating uploads directory structure in public_html/uploads...\n";
foreach ($requiredDirs as $dir) {
    $fullPath = $newUploadsPath . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "  ✅ Created: uploads/$dir/\n";
        } else {
            echo "  ❌ Failed to create: uploads/$dir/\n";
        }
    } else {
        echo "  ✓ Exists: uploads/$dir/\n";
    }
}

// Also ensure base uploads directory exists
if (!is_dir($newUploadsPath)) {
    mkdir($newUploadsPath, 0755, true);
}
echo "✅ Directory structure ready\n";

// Function to recursively copy directory
function recurseCopy($src, $dst) {
    if (!is_dir($src)) return 0;
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    $count = 0;
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..' && $file != '.gitignore' && $file != '.htaccess' && $file != '.gitkeep') {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                $count += recurseCopy($srcPath, $dstPath);
            } else {
                if (copy($srcPath, $dstPath)) {
                    chmod($dstPath, 0644);
                    echo "  Copied: $file\n";
                    $count++;
                } else {
                    echo "  ❌ Failed to copy: $file\n";
                }
            }
        }
    }
    closedir($dir);
    return $count;
}

// Function to recursively copy and delete source
function recurseCopyAndDelete($src, $dst) {
    if (!is_dir($src)) return 0;
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    $count = 0;
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                $count += recurseCopyAndDelete($srcPath, $dstPath);
                @rmdir($srcPath); // Remove empty directory
            } else {
                if (copy($srcPath, $dstPath)) {
                    chmod($dstPath, 0644);
                    unlink($srcPath); // Delete source file after copy
                    echo "  Moved: $file\n";
                    $count++;
                } else {
                    echo "  ❌ Failed to move: $file\n";
                }
            }
        }
    }
    closedir($dir);
    return $count;
}

// Migrate files from WRONG location (public_html/public/uploads) to CORRECT location (public_html/uploads)
if (is_dir($wrongUploadsPath)) {
    echo "\n⚠️ Found files in wrong location (public/uploads), migrating to correct location...\n";
    $moved = recurseCopyAndDelete($wrongUploadsPath, $newUploadsPath);
    @rmdir($wrongUploadsPath); // Try to remove the wrong uploads folder
    echo "✅ Moved $moved files from wrong location\n";
}

// Copy files from old storage to new uploads
if (is_dir($oldStoragePath)) {
    echo "\nMigrating files from storage to uploads...\n";
    $copied = recurseCopy($oldStoragePath, $newUploadsPath);
    echo "\n✅ Migrated $copied files from storage\n";
} else {
    echo "\n⚠️ Old storage path doesn't exist, nothing to migrate\n";
}

// Fix permissions on uploads
echo "\nFixing permissions on uploads directory...\n";
chmod($newUploadsPath, 0755);

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($newUploadsPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    if ($item->isDir()) {
        chmod($item->getPathname(), 0755);
    } else {
        chmod($item->getPathname(), 0644);
    }
}

echo "✅ Permissions fixed\n";

echo "\n✅ Migration complete!\n";
echo "\n⚠️ IMPORTANT: Delete this file (setup-storage.php) after running!\n";
echo "\n📋 Note: Your files are now served from /uploads/ instead of /storage/\n";
echo "</pre>";