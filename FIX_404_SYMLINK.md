# Fix 404 Error: Create Storage Symlink on Live Server

## Problem
Getting 404 when accessing: `https://www.wauminilink.co.tz/demo/storage/member/profile-pictures/filename.jpg`

This means the symlink `public/storage` → `storage/app/public` is missing or broken.

## Solution: Create the Symlink

### Option 1: Via SSH (Recommended)

1. **Connect to your server via SSH**
2. **Navigate to your project:**
   ```bash
   cd /home/wauminilink/demo
   ```
3. **Remove old storage folder (if it exists and is NOT a symlink):**
   ```bash
   rm -rf public/storage
   ```
4. **Create the symlink:**
   ```bash
   php artisan storage:link
   ```
5. **Set permissions:**
   ```bash
   chmod -R 755 storage/app/public
   chmod -R 755 public/storage
   ```
6. **Verify it worked:**
   ```bash
   ls -la public/storage
   ```
   Should show: `public/storage -> ../storage/app/public`

### Option 2: Via cPanel Terminal

1. **Login to cPanel**
2. **Go to:** Terminal (or Advanced → Terminal)
3. **Run these commands:**
   ```bash
   cd /home/wauminilink/demo
   rm -rf public/storage
   php artisan storage:link
   chmod -R 755 storage/app/public
   chmod -R 755 public/storage
   ```

### Option 3: Manual Symlink Creation (If artisan doesn't work)

If `php artisan storage:link` doesn't work, create it manually:

```bash
cd /home/wauminilink/demo
rm -rf public/storage
ln -s ../storage/app/public public/storage
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

### Option 4: Via cPanel File Manager (Alternative)

If you can't use SSH/Terminal, you can try this (less reliable):

1. **Go to cPanel File Manager**
2. **Navigate to:** `/home/wauminilink/demo/public/`
3. **If `storage` folder exists:**
   - Delete it (if it's a regular folder, not a symlink)
4. **Create symlink:**
   - This is tricky in cPanel File Manager
   - Better to use SSH/Terminal

## Verify the Fix

After creating the symlink, test again:

```
https://www.wauminilink.co.tz/demo/storage/member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg
```

**Expected results:**
- ✅ **200 OK** - Image displays (symlink works!)
- ❌ **404 Not Found** - Symlink still not working
- ❌ **403 Forbidden** - Permission issue

## If Still Getting 404

### Check 1: Verify Symlink Exists

```bash
ls -la /home/wauminilink/demo/public/storage
```

Should show:
```
lrwxrwxrwx ... storage -> ../storage/app/public
```

If it shows as a regular directory (`drwxr-xr-x`), delete it and recreate:
```bash
rm -rf /home/wauminilink/demo/public/storage
php artisan storage:link
```

### Check 2: Verify File Exists

```bash
ls -la /home/wauminilink/demo/storage/app/public/member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg
```

If file doesn't exist, check what files are there:
```bash
ls -la /home/wauminilink/demo/storage/app/public/member/profile-pictures/
```

### Check 3: Test Symlink Target

```bash
ls -la /home/wauminilink/demo/public/storage/member/profile-pictures/
```

If this works, the symlink is correct.

### Check 4: Web Server Configuration

If symlink exists but still 404, check:

1. **.htaccess file** in `public/` directory
   - Should allow access to `storage/` directory
   - Should not block image requests

2. **Apache configuration**
   - `FollowSymLinks` should be enabled
   - Check if there are any restrictions

3. **Nginx configuration** (if using Nginx)
   - Should follow symlinks
   - Check location blocks

## Alternative: Copy Files to Public (Temporary Fix)

If symlink absolutely won't work, you can copy files (not recommended for production):

```bash
# Create directory
mkdir -p /home/wauminilink/demo/public/storage/member/profile-pictures

# Copy files
cp -r /home/wauminilink/demo/storage/app/public/member/profile-pictures/* /home/wauminilink/demo/public/storage/member/profile-pictures/

# Set permissions
chmod -R 755 /home/wauminilink/demo/public/storage
```

**Note:** This is a temporary workaround. You'll need to copy files every time a new image is uploaded. Better to fix the symlink.

## Quick Test Script

Create this file on your server to test:

```php
<?php
// test_symlink.php - Place in public/ directory
$symlink = __DIR__ . '/storage';
$target = __DIR__ . '/../storage/app/public';

echo "Symlink exists: " . (file_exists($symlink) ? 'YES' : 'NO') . "\n";
echo "Is symlink: " . (is_link($symlink) ? 'YES' : 'NO') . "\n";
if (is_link($symlink)) {
    echo "Target: " . readlink($symlink) . "\n";
}
echo "Target exists: " . (file_exists($target) ? 'YES' : 'NO') . "\n";
echo "File accessible: " . (file_exists($symlink . '/member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg') ? 'YES' : 'NO') . "\n";
```

Access: `https://www.wauminilink.co.tz/demo/test_symlink.php`

## Summary

**The fix is simple:**
1. SSH into your server
2. Run: `cd /home/wauminilink/demo && rm -rf public/storage && php artisan storage:link`
3. Test the URL again

If you don't have SSH access, contact your hosting provider to create the symlink for you.

