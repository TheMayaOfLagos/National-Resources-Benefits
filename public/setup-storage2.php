<?php
/**
 * Storage Setup Script v2
 *
 * Creates symlink from public_html/public/uploads -> ../uploads
 * so Laravel's public_path('uploads') resolves to the web-accessible public_html/uploads
 *
 * Access via: https://yourdomain.com/setup-storage2.php?token=NRB-SETUP-2024
 * DELETE THIS FILE AFTER RUNNING!
 */

$allowedToken = 'NRB-SETUP-2024';
$providedToken = $_GET['token'] ?? '';

if ($providedToken !== $allowedToken) {
    http_response_code(403);
    die('Forbidden - Invalid token. Use: ?token=YOUR_TOKEN');
}

echo "<h1>Storage Setup Script v2</h1>";
echo "<pre>";

// Paths
$scriptLocation = __DIR__;                              // /home/user/public_html/public
$publicHtmlPath = dirname(__DIR__);                     // /home/user/public_html
$realUploadsPath = $publicHtmlPath . '/uploads';        // /home/user/public_html/uploads (web accessible)
$symlinkPath = $scriptLocation . '/uploads';            // /home/user/public_html/public/uploads (Laravel writes here)
$oldStoragePath = $publicHtmlPath . '/storage/app/public';

echo "Script location: $scriptLocation\n";
echo "Public HTML path: $publicHtmlPath\n";
echo "Real uploads path: $realUploadsPath\n";
echo "Symlink path: $symlinkPath\n";
echo "Old storage path: $oldStoragePath\n\n";

// Required subdirectories
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

// Step 1: Create real uploads directory in public_html/uploads
echo "=== Step 1: Creating real uploads directory ===\n";
if (!is_dir($realUploadsPath)) {
    if (mkdir($realUploadsPath, 0755, true)) {
        echo "✅ Created: $realUploadsPath\n";
    } else {
        echo "❌ Failed to create: $realUploadsPath\n";
    }
} else {
    echo "✓ Already exists: $realUploadsPath\n";
}

// Create subdirectories
foreach ($requiredDirs as $dir) {
    $fullPath = $realUploadsPath . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "  ✅ Created: uploads/$dir/\n";
        } else {
            echo "  ❌ Failed: uploads/$dir/\n";
        }
    } else {
        echo "  ✓ Exists: uploads/$dir/\n";
    }
}

// Step 2: Handle the symlink
echo "\n=== Step 2: Creating symlink ===\n";
echo "Target: $symlinkPath -> ../uploads\n";

if (is_link($symlinkPath)) {
    $currentTarget = readlink($symlinkPath);
    echo "✓ Symlink already exists, points to: $currentTarget\n";
} elseif (is_dir($symlinkPath)) {
    // It's a real directory, need to move contents and replace with symlink
    echo "⚠️ Found real directory at symlink path, migrating contents...\n";

    // Move files from wrong location to correct location
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($symlinkPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $movedCount = 0;
    foreach ($iterator as $item) {
        $relativePath = substr($item->getPathname(), strlen($symlinkPath) + 1);
        $destPath = $realUploadsPath . '/' . $relativePath;

        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }
        } else {
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            if (copy($item->getPathname(), $destPath)) {
                unlink($item->getPathname());
                $movedCount++;
                echo "  Moved: $relativePath\n";
            }
        }
    }
    echo "✅ Moved $movedCount files\n";

    // Remove the now-empty directory
    function removeEmptyDirs($dir) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                removeEmptyDirs($path);
            }
        }
        @rmdir($dir);
    }
    removeEmptyDirs($symlinkPath);

    // Create symlink
    if (!file_exists($symlinkPath)) {
        if (@symlink('../uploads', $symlinkPath)) {
            echo "✅ Created symlink: $symlinkPath -> ../uploads\n";
        } else {
            echo "❌ Failed to create symlink (may be disabled on this host)\n";
            echo "   Fallback: Recreating directory structure\n";
            mkdir($symlinkPath, 0755, true);
        }
    }
} elseif (!file_exists($symlinkPath)) {
    // Nothing exists, create symlink
    if (@symlink('../uploads', $symlinkPath)) {
        echo "✅ Created symlink: $symlinkPath -> ../uploads\n";
    } else {
        echo "❌ Failed to create symlink (may be disabled on this host)\n";
        echo "   Creating regular directory as fallback\n";
        mkdir($symlinkPath, 0755, true);
    }
} else {
    echo "⚠️ Unknown file type at: $symlinkPath\n";
}

// Step 3: Migrate from old storage location
echo "\n=== Step 3: Migrating from old storage ===\n";
if (is_dir($oldStoragePath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($oldStoragePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $copiedCount = 0;
    foreach ($iterator as $item) {
        $relativePath = substr($item->getPathname(), strlen($oldStoragePath) + 1);
        $destPath = $realUploadsPath . '/' . $relativePath;

        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }
        } else {
            if (!file_exists($destPath)) {
                $destDir = dirname($destPath);
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                if (copy($item->getPathname(), $destPath)) {
                    $copiedCount++;
                    echo "  Copied: $relativePath\n";
                }
            }
        }
    }
    echo "✅ Copied $copiedCount files from old storage\n";
} else {
    echo "ℹ️ No old storage path found, skipping\n";
}

// Step 4: Fix permissions
echo "\n=== Step 4: Fixing permissions ===\n";
chmod($realUploadsPath, 0755);

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($realUploadsPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    if ($item->isDir()) {
        chmod($item->getPathname(), 0755);
    } else {
        chmod($item->getPathname(), 0644);
    }
}
echo "✅ Permissions fixed (dirs: 755, files: 644)\n";

// Step 5: Verify
echo "\n=== Step 5: Verification ===\n";
if (is_link($symlinkPath)) {
    echo "✅ Symlink active: $symlinkPath -> " . readlink($symlinkPath) . "\n";
} elseif (is_dir($symlinkPath)) {
    echo "⚠️ Using directory fallback (symlinks may be disabled)\n";
}

// List contents
echo "\nContents of $realUploadsPath:\n";
$items = scandir($realUploadsPath);
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $path = $realUploadsPath . '/' . $item;
    $type = is_dir($path) ? '[DIR]' : '[FILE]';
    echo "  $type $item\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Setup complete!\n";
echo "\n⚠️ IMPORTANT: Delete this file (setup-storage2.php) now!\n";
echo "\nTest URL: " . rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') . "/uploads/\n";
echo "</pre>";
