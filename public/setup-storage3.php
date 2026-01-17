<?php
/**
 * Storage Setup Script v3
 * 
 * Strategy:
 * - Laravel writes to: public_path('uploads') = /public_html/public/uploads (REAL directory)
 * - Web accessible at: /public_html/uploads (SYMLINK pointing to public/uploads)
 * 
 * This creates: public_html/uploads -> public/uploads
 * 
 * Access via: https://yourdomain.com/setup-storage3.php?token=NRB-SETUP-2024
 * DELETE THIS FILE AFTER RUNNING!
 */

$allowedToken = 'NRB-SETUP-2024';
$providedToken = $_GET['token'] ?? '';

if ($providedToken !== $allowedToken) {
    http_response_code(403);
    die('Forbidden - Invalid token. Use: ?token=YOUR_TOKEN');
}

echo "<h1>Storage Setup Script v3</h1>";
echo "<pre>";

// Paths - IMPORTANT: Laravel writes to public/uploads, web accesses /uploads
$publicDir = __DIR__;                                   // /home/user/public_html/public
$publicHtmlDir = dirname(__DIR__);                      // /home/user/public_html
$laravelUploadsPath = $publicDir . '/uploads';          // Where Laravel WRITES: /public_html/public/uploads
$webSymlinkPath = $publicHtmlDir . '/uploads';          // Web accessible SYMLINK: /public_html/uploads -> public/uploads
$oldStoragePath = $publicHtmlDir . '/storage/app/public';

echo "=== Path Configuration ===\n";
echo "Laravel public dir: $publicDir\n";
echo "Public HTML dir: $publicHtmlDir\n";
echo "Laravel writes to: $laravelUploadsPath\n";
echo "Web symlink at: $webSymlinkPath -> public/uploads\n";
echo "Old storage: $oldStoragePath\n\n";

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

// Step 1: Create REAL uploads directory where Laravel writes (public_html/public/uploads)
echo "=== Step 1: Creating Laravel uploads directory ===\n";
if (!is_dir($laravelUploadsPath)) {
    if (mkdir($laravelUploadsPath, 0755, true)) {
        echo "✅ Created: $laravelUploadsPath\n";
    } else {
        echo "❌ Failed to create: $laravelUploadsPath\n";
        echo "   Check directory permissions!\n";
    }
} else {
    echo "✓ Already exists: $laravelUploadsPath\n";
}

// Create subdirectories in Laravel uploads path
foreach ($requiredDirs as $dir) {
    $fullPath = $laravelUploadsPath . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "  ✅ Created: public/uploads/$dir/\n";
        } else {
            echo "  ❌ Failed: public/uploads/$dir/\n";
        }
    } else {
        echo "  ✓ Exists: public/uploads/$dir/\n";
    }
}

// Step 2: Handle web-accessible symlink (public_html/uploads -> public/uploads)
echo "\n=== Step 2: Creating web symlink ===\n";
echo "Goal: $webSymlinkPath -> public/uploads\n";

if (is_link($webSymlinkPath)) {
    $target = readlink($webSymlinkPath);
    echo "✓ Symlink already exists: $webSymlinkPath -> $target\n";
    
    // Check if it points to correct location
    if ($target !== 'public/uploads' && $target !== './public/uploads') {
        echo "⚠️ Symlink points to wrong target, recreating...\n";
        unlink($webSymlinkPath);
        if (@symlink('public/uploads', $webSymlinkPath)) {
            echo "✅ Recreated symlink: $webSymlinkPath -> public/uploads\n";
        }
    }
} elseif (is_dir($webSymlinkPath) && !is_link($webSymlinkPath)) {
    // Real directory exists at symlink location - migrate files
    echo "⚠️ Found real directory at symlink location, migrating...\n";
    
    $movedCount = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($webSymlinkPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $relativePath = substr($item->getPathname(), strlen($webSymlinkPath) + 1);
        $destPath = $laravelUploadsPath . '/' . $relativePath;
        
        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }
        } else {
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            if (!file_exists($destPath)) {
                if (copy($item->getPathname(), $destPath)) {
                    unlink($item->getPathname());
                    $movedCount++;
                }
            } else {
                unlink($item->getPathname()); // Remove duplicate
            }
        }
    }
    echo "  Moved $movedCount files to Laravel uploads\n";
    
    // Remove empty directories
    $dirs = [];
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($webSymlinkPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iter as $item) {
        if ($item->isDir()) {
            @rmdir($item->getPathname());
        }
    }
    @rmdir($webSymlinkPath);
    
    // Create symlink
    if (!file_exists($webSymlinkPath)) {
        if (@symlink('public/uploads', $webSymlinkPath)) {
            echo "✅ Created symlink: $webSymlinkPath -> public/uploads\n";
        } else {
            echo "❌ Failed to create symlink\n";
            echo "   Run manually: ln -s public/uploads ~/public_html/uploads\n";
        }
    }
} elseif (!file_exists($webSymlinkPath)) {
    // Nothing exists, create symlink
    if (@symlink('public/uploads', $webSymlinkPath)) {
        echo "✅ Created symlink: $webSymlinkPath -> public/uploads\n";
    } else {
        echo "❌ Failed to create symlink (may be disabled)\n";
        echo "   Run manually in cPanel Terminal:\n";
        echo "   cd ~/public_html && ln -s public/uploads uploads\n";
    }
} else {
    echo "⚠️ Unknown file at: $webSymlinkPath\n";
}

// Step 3: Migrate from old storage location
echo "\n=== Step 3: Migrating from old storage ===\n";
if (is_dir($oldStoragePath)) {
    $copiedCount = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($oldStoragePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $relativePath = substr($item->getPathname(), strlen($oldStoragePath) + 1);
        $destPath = $laravelUploadsPath . '/' . $relativePath;
        
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
    echo "ℹ️ No old storage path, skipping\n";
}

// Step 4: Fix permissions
echo "\n=== Step 4: Fixing permissions ===\n";
if (is_dir($laravelUploadsPath)) {
    chmod($laravelUploadsPath, 0755);
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($laravelUploadsPath, RecursiveDirectoryIterator::SKIP_DOTS),
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
}

// Step 5: Verification
echo "\n=== Step 5: Verification ===\n";

// Check Laravel uploads
echo "Laravel uploads ($laravelUploadsPath):\n";
if (is_dir($laravelUploadsPath)) {
    echo "  ✅ Directory exists\n";
    echo "  Contents:\n";
    foreach (scandir($laravelUploadsPath) as $item) {
        if ($item === '.' || $item === '..') continue;
        $type = is_dir($laravelUploadsPath . '/' . $item) ? '[DIR]' : '[FILE]';
        echo "    $type $item\n";
    }
} else {
    echo "  ❌ Does not exist!\n";
}

// Check web symlink
echo "\nWeb symlink ($webSymlinkPath):\n";
if (is_link($webSymlinkPath)) {
    echo "  ✅ Symlink -> " . readlink($webSymlinkPath) . "\n";
} elseif (is_dir($webSymlinkPath)) {
    echo "  ⚠️ Real directory (symlink creation may have failed)\n";
} else {
    echo "  ❌ Does not exist!\n";
}

// Test write
echo "\nWrite test:\n";
$testFile = $laravelUploadsPath . '/test-write-' . time() . '.txt';
if (file_put_contents($testFile, 'Test content')) {
    echo "  ✅ Write successful\n";
    unlink($testFile);
} else {
    echo "  ❌ Write failed! Check permissions.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ Setup complete!\n";
echo "\nHow it works now:\n";
echo "  1. Laravel writes to: /public_html/public/uploads/\n";
echo "  2. Web accesses via: /public_html/uploads/ (symlink)\n";
echo "  3. URL format: https://yourdomain.com/uploads/settings/file.jpg\n";
echo "\n⚠️ DELETE this file (setup-storage3.php) now!\n";
echo "\n⚠️ Run these commands on server:\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n";
echo "</pre>";
