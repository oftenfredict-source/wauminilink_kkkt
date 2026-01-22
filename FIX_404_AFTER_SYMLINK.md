# Fix 404 After Symlink Created

## Problem
- ✅ Symlink exists: `public/storage -> /home/wauminilink/demo/storage/app/public`
- ✅ Files are accessible: `ls -la public/storage/member/profile-pictures/` shows files
- ❌ Browser returns 404 when accessing the URL

## Root Causes & Fixes

### Issue 1: Apache Not Following Symlinks

Apache needs `FollowSymLinks` enabled in the directory configuration.

**Fix:** Check/update `.htaccess` in `public/` directory:

```apache
# In /home/wauminilink/demo/public/.htaccess
# Make sure this is present:

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# IMPORTANT: Allow access to storage directory
<Directory "storage">
    Options +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

### Issue 2: Apache Directory Configuration

The main Apache config might not allow symlinks.

**Fix:** Check Apache virtual host configuration. It should have:

```apache
<Directory "/home/wauminilink/demo/public">
    Options +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

If you can't edit Apache config, ask your hosting provider to enable `FollowSymLinks` for your domain.

### Issue 3: SELinux Blocking (If using SELinux)

**Check if SELinux is enabled:**
```bash
getenforce
```

If it shows `Enforcing`, SELinux might be blocking symlink access.

**Fix:**
```bash
# Allow Apache to follow symlinks
setsebool -P httpd_read_user_content 1
setsebool -P httpd_enable_homedirs 1

# Or set context
chcon -R -t httpd_sys_content_t /home/wauminilink/demo/storage/app/public
chcon -R -t httpd_sys_content_t /home/wauminilink/demo/public/storage
```

### Issue 4: Wrong Symlink Path (Absolute vs Relative)

Your symlink uses absolute path. While this works, relative is preferred.

**Fix:** Recreate with relative path:

```bash
cd /home/wauminilink/demo
rm public/storage
ln -s ../storage/app/public public/storage
chmod -R 755 storage/app/public public/storage
```

### Issue 5: Web Server Needs Restart

Sometimes Apache needs to be restarted to recognize new symlinks.

**Fix:** Restart Apache (if you have access):
```bash
sudo systemctl restart httpd
# or
sudo service apache2 restart
```

If you don't have sudo access, ask your hosting provider to restart Apache.

### Issue 6: .htaccess in storage Directory

Check if there's a `.htaccess` in `storage/app/public/` that might be blocking.

**Fix:**
```bash
# Check if .htaccess exists
ls -la /home/wauminilink/demo/storage/app/public/.htaccess

# If it exists and blocks access, remove or modify it
# Usually there shouldn't be one here
```

## Quick Diagnostic Steps

### Step 1: Test Direct File Access

```bash
# Test if file is readable
cat /home/wauminilink/demo/public/storage/member/profile-pictures/HH62rOrdtYWFBPcIyzTGnlaHcx2k57s3DvMC7KD0.jpg | head -c 100
```

If this works, the symlink is fine.

### Step 2: Check Apache Error Logs

```bash
# Check Apache error log
tail -n 50 /var/log/httpd/error_log
# or
tail -n 50 /var/log/apache2/error_log
```

Look for permission denied or symlink-related errors.

### Step 3: Test with PHP Script

Create a test file: `/home/wauminilink/demo/public/test_storage.php`

```php
<?php
$file = __DIR__ . '/storage/member/profile-pictures/HH62rOrdtYWFBPcIyzTGnlaHcx2k57s3DvMC7KD0.jpg';

echo "File exists: " . (file_exists($file) ? 'YES' : 'NO') . "\n";
echo "Is readable: " . (is_readable($file) ? 'YES' : 'NO') . "\n";
echo "File path: " . $file . "\n";
echo "Real path: " . realpath($file) . "\n";

if (file_exists($file)) {
    header('Content-Type: image/jpeg');
    readfile($file);
    exit;
} else {
    echo "File not found!";
}
```

Access: `https://www.wauminilink.co.tz/demo/test_storage.php`

If this displays the image, the issue is with URL rewriting or Apache configuration.

## Most Likely Solution

Since your symlink works in terminal but not in browser, it's almost certainly an Apache configuration issue.

**Try this first:**

1. **Update `.htaccess` in `public/` directory:**
   Add this at the top of `/home/wauminilink/demo/public/.htaccess`:

```apache
# Allow symlinks
Options +FollowSymLinks

# Allow access to storage
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} ^/demo/storage/
    RewriteRule ^storage/(.*)$ storage/$1 [L]
</IfModule>
```

2. **Or create `.htaccess` in `public/storage/` directory:**

```apache
# /home/wauminilink/demo/public/storage/.htaccess
Options +FollowSymLinks
AllowOverride All
Require all granted
```

3. **Contact hosting provider:**
   Ask them to:
   - Enable `FollowSymLinks` for your domain
   - Restart Apache
   - Check if there are any security restrictions blocking symlink access

## Alternative: Use Relative Symlink

Try recreating the symlink with relative path:

```bash
cd /home/wauminilink/demo
rm public/storage
ln -s ../storage/app/public public/storage
ls -la public/storage
```

Should now show: `public/storage -> ../storage/app/public` (relative, not absolute)

## Test After Each Fix

After each fix, test:
```
https://www.wauminilink.co.tz/demo/storage/member/profile-pictures/HH62rOrdtYWFBPcIyzTGnlaHcx2k57s3DvMC7KD0.jpg
```

