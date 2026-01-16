<?php
/**
 * Post-Deployment Setup Script
 * 
 * Run this script once after deploying to cPanel to:
 * 1. Migrate files from storage to uploads directory
 * 2. Fix permissions
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

$publicPath = __DIR__;
$oldStoragePath = dirname(__DIR__) . '/storage/app/public';
$newUploadsPath = $publicPath . '/uploads';

echo "Old storage path: $oldStoragePath\n";
echo "New uploads path: $newUploadsPath\n\n";

// Create uploads directory if it doesn't exist
if (!is_dir($newUploadsPath)) {
    echo "Creating uploads directory...\n";
    if (mkdir($newUploadsPath, 0755, true)) {
        echo "✅ Created uploads directory\n";
    } else {
        echo "❌ Failed to create uploads directory\n";
    }
} else {
    echo "✅ Uploads directory exists\n";
}

// Function to recursively copy directory
function recurseCopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    $count = 0;
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..' && $file != '.gitignore' && $file != '.htaccess') {
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

// Copy files from old storage to new uploads
if (is_dir($oldStoragePath)) {
    echo "\nMigrating files from storage to uploads...\n";
    $copied = recurseCopy($oldStoragePath, $newUploadsPath);
    echo "\n✅ Migrated $copied files\n";
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