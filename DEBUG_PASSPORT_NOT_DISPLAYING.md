# Debug: Passport Images Not Displaying After Setting Permissions

## 🔍 Step-by-Step Debugging Guide

Follow these steps in order to find the exact problem:

---

## Step 1: Check if Files Actually Exist on Server

### Via cPanel File Manager:
1. Navigate to: `public/assets/images/members/profile-pictures/`
2. **Do you see any image files?** (like `69300a3d22450_1764756029.jpeg`)
   - ✅ **YES** → Go to Step 2
   - ❌ **NO** → **Problem found!** Files don't exist. Go to "Solution: Transfer Files"

### Via SSH:
```bash
cd /path/to/your/laravel/project
ls -la public/assets/images/members/profile-pictures/
```

**If folder is empty or doesn't exist:**
- The files were never uploaded to the server
- You need to transfer them from local OR re-upload them

---

## Step 2: Check Database Paths

### Check what's stored in database:
```sql
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 5;
```

**Expected format:**
```
assets/images/members/profile-pictures/filename.jpg
```

**If you see:**
- `members/profile-pictures/filename.jpg` (missing `assets/images/`) → **WRONG!**
- `storage/members/profile-pictures/filename.jpg` → **WRONG!**
- `assets/images/members/profile-pictures/filename.jpg` → **CORRECT!**

**If wrong, fix with SQL:**
```sql
UPDATE members 
SET profile_picture = CONCAT('assets/images/', profile_picture)
WHERE profile_picture IS NOT NULL 
AND profile_picture NOT LIKE 'assets/images/%'
AND profile_picture LIKE 'members/profile-pictures/%';
```

---

## Step 3: Check Browser Console (F12)

1. **Open your website** in browser
2. **Press F12** to open Developer Tools
3. **Go to "Network" tab**
4. **Refresh the page** (F5)
5. **Look for the image request** (filter by "Img" or search for the filename)
6. **Click on the image request** and check:
   - **Status Code**: 
     - `200` = File exists and accessible ✅
     - `404` = File not found ❌
     - `403` = Permission denied ❌
   - **Request URL**: What URL is it trying to load?
     - Should be: `https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/filename.jpg`
     - If it has `/storage/` in it → View files not updated yet

---

## Step 4: Check if View Files Are Updated on Server

### Check one view file on server:
Open: `resources/views/members/dashboard.blade.php` on your live server

**Look for this line (around line 14):**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" 
```

**If you see `'storage/'`** → **WRONG!** View files not updated yet.

**Should be:**
```php
<img src="{{ asset($member->profile_picture) }}" 
```

**If wrong:** Upload the fixed view files to your server.

---

## Step 5: Check Laravel Cache

### Clear all caches on server:

**Via SSH:**
```bash
cd /path/to/your/laravel/project
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

**Via cPanel (create temporary file):**
Create: `public/clear-all-cache.php`
```php
<?php
chdir(__DIR__ . '/..');
exec('php artisan view:clear 2>&1', $output1);
exec('php artisan cache:clear 2>&1', $output2);
exec('php artisan config:clear 2>&1', $output3);
exec('php artisan route:clear 2>&1', $output4);
echo "<h2>All Caches Cleared</h2><pre>";
echo "View: " . implode("\n", $output1) . "\n\n";
echo "Cache: " . implode("\n", $output2) . "\n\n";
echo "Config: " . implode("\n", $output3) . "\n\n";
echo "Route: " . implode("\n", $output4) . "\n\n";
echo "</pre>";
echo "<p><strong>DELETE THIS FILE NOW!</strong></p>";
```

Visit: `https://www.wauminilink.co.tz/demo/clear-all-cache.php`
**Then DELETE the file immediately!**

---

## Step 6: Check APP_URL in .env

### Check your `.env` file on server:
```bash
cat .env | grep APP_URL
```

**Should be:**
```
APP_URL=https://www.wauminilink.co.tz/demo
```

**If wrong:**
1. Edit `.env` file
2. Set: `APP_URL=https://www.wauminilink.co.tz/demo`
3. Run: `php artisan config:clear`

---

## Step 7: Test Direct File Access

### Try accessing the image directly in browser:
```
https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/69300a3d22450_1764756029.jpeg
```

**Results:**
- ✅ **Image displays** → File exists, permissions OK, problem is in view/cache
- ❌ **404 Not Found** → File doesn't exist on server
- ❌ **403 Forbidden** → Permission issue

---

## 🔧 Solutions Based on What You Find

### Solution 1: Files Don't Exist on Server

**Problem:** Files were uploaded locally but never transferred to live server.

**Fix:**
1. **Transfer files from local to live:**
   - Local: `C:\xampp\htdocs\WauminiLink\public\assets\images\members\profile-pictures\`
   - Live: `public/assets/images/members/profile-pictures/`
   - Use FTP, cPanel File Manager, or SCP

2. **OR re-upload images on live server:**
   - Edit each member on live server
   - Upload passport image again
   - Save

---

### Solution 2: View Files Not Updated

**Problem:** View files still use `asset('storage/' . ...)` instead of `asset(...)`

**Fix:**
Upload these 8 fixed view files to your server:
- `resources/views/members/dashboard.blade.php`
- `resources/views/pastor/dashboard.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/members/settings.blade.php`
- `resources/views/members/partials/card-view.blade.php`
- `resources/views/leaders/bulk-identity-cards.blade.php`
- `resources/views/leaders/identity-card.blade.php`
- `resources/views/members/identity-card.blade.php`

Then clear cache: `php artisan view:clear`

---

### Solution 3: Database Paths Wrong

**Problem:** Database has wrong path format

**Fix:**
Run this SQL query:
```sql
-- Check current paths
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 5;

-- Fix paths (if needed)
UPDATE members 
SET profile_picture = CONCAT('assets/images/', profile_picture)
WHERE profile_picture IS NOT NULL 
AND profile_picture NOT LIKE 'assets/images/%'
AND profile_picture LIKE 'members/profile-pictures/%';
```

---

### Solution 4: Cache Not Cleared

**Problem:** Laravel is serving old cached views

**Fix:**
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

Then **clear browser cache** (Ctrl+Shift+Delete) and **hard refresh** (Ctrl+F5)

---

### Solution 5: Permissions Still Wrong

**Problem:** Permissions not set correctly

**Fix:**
```bash
# Set folder permissions
chmod -R 755 public/assets/images

# Set file permissions (if files exist)
find public/assets/images/members/profile-pictures -type f -exec chmod 644 {} \;

# Set ownership (replace www-data with your web server user)
chown -R www-data:www-data public/assets/images
```

---

## 🎯 Quick Diagnostic Checklist

Run through this checklist:

- [ ] **Files exist on server?** 
  - Check: `public/assets/images/members/profile-pictures/` has image files
- [ ] **Database paths correct?**
  - Check: Paths start with `assets/images/members/profile-pictures/`
- [ ] **View files updated?**
  - Check: Views use `asset($member->profile_picture)` not `asset('storage/' . ...)`
- [ ] **Cache cleared?**
  - Run: `php artisan view:clear && php artisan cache:clear`
- [ ] **Browser cache cleared?**
  - Press: Ctrl+Shift+Delete, then Ctrl+F5
- [ ] **Direct file access works?**
  - Try: `https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/filename.jpg`
- [ ] **Permissions correct?**
  - Check: Folders are 755, files are 644
- [ ] **APP_URL correct?**
  - Check: `.env` has `APP_URL=https://www.wauminilink.co.tz/demo`

---

## 📋 Tell Me What You Found

After checking the steps above, tell me:

1. **Do files exist on server?** (Yes/No)
2. **What does browser console show?** (Status code: 200/404/403)
3. **What URL is it trying to load?** (Copy the exact URL from Network tab)
4. **Can you access the image directly?** (Try the direct URL in browser)

With this information, I can give you the exact fix!

