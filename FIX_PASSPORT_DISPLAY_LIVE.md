# Fix: Passport Images Not Showing on Live Server

## Problem
Passport images are saved to the database correctly, but they don't display on the dashboard/views on the live server.

## Root Cause
The view files were still using `asset('storage/' . $member->profile_picture)`, but the database stores paths like:
```
assets/images/members/profile-pictures/filename.jpg
```

This created wrong URLs like:
- ❌ `https://waumini.co.tz/demo/storage/assets/images/members/profile-pictures/filename.jpg` (WRONG - file doesn't exist there)

Instead of:
- ✅ `https://waumini.co.tz/demo/assets/images/members/profile-pictures/filename.jpg` (CORRECT)

---

## ✅ Files Fixed (9 files total)

### View Files (8 files)
All these files have been updated to use `asset($member->profile_picture)` instead of `asset('storage/' . $member->profile_picture)`:

1. ✅ `resources/views/members/dashboard.blade.php`
2. ✅ `resources/views/pastor/dashboard.blade.php`
3. ✅ `resources/views/dashboard.blade.php` (2 locations - secretary and user)
4. ✅ `resources/views/members/settings.blade.php`
5. ✅ `resources/views/members/partials/card-view.blade.php` (2 locations)
6. ✅ `resources/views/leaders/bulk-identity-cards.blade.php`
7. ✅ `resources/views/leaders/identity-card.blade.php`
8. ✅ `resources/views/members/identity-card.blade.php`

### Controller File (1 file)
9. ✅ `app/Http/Controllers/MemberDashboardController.php` - Fixed to use correct upload path when members update their profile picture

---

## 🚀 Deployment Steps for Live Server

### Step 1: Upload All Fixed Files
Upload these 9 files to your live server (overwrite existing ones):

**View Files (8):**
- `resources/views/members/dashboard.blade.php`
- `resources/views/pastor/dashboard.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/members/settings.blade.php`
- `resources/views/members/partials/card-view.blade.php`
- `resources/views/leaders/bulk-identity-cards.blade.php`
- `resources/views/leaders/identity-card.blade.php`
- `resources/views/members/identity-card.blade.php`

**Controller File (1):**
- `app/Http/Controllers/MemberDashboardController.php`

### Step 2: Clear Laravel Cache on Live Server

**Via SSH (recommended):**
```bash
cd /path/to/your/laravel/project
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

**Or via cPanel File Manager:**
1. Create a temporary file: `public/clear-cache.php`
2. Add this code:
```php
<?php
chdir(__DIR__ . '/..');
exec('php artisan view:clear 2>&1', $output1);
exec('php artisan cache:clear 2>&1', $output2);
exec('php artisan config:clear 2>&1', $output3);
echo "<h2>Cache Cleared</h2><pre>";
echo "View Cache: " . implode("\n", $output1) . "\n\n";
echo "Application Cache: " . implode("\n", $output2) . "\n\n";
echo "Config Cache: " . implode("\n", $output3) . "\n\n";
echo "</pre>";
echo "<p><strong>DELETE THIS FILE NOW!</strong></p>";
```
3. Visit: `https://waumini.co.tz/demo/clear-cache.php`
4. **DELETE the file immediately after use!**

### Step 3: Verify Image Paths in Database

Check that your database has correct paths. Run this SQL query:
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

**If you see paths like:**
```
members/profile-pictures/filename.jpg  (missing 'assets/images/')
```
Then run this SQL to fix them:
```sql
UPDATE members 
SET profile_picture = CONCAT('assets/images/', profile_picture)
WHERE profile_picture IS NOT NULL 
AND profile_picture NOT LIKE 'assets/images/%'
AND profile_picture LIKE 'members/profile-pictures/%';
```

### Step 4: Verify Files Exist on Server

Check that the actual image files exist on your server:
- Path: `public/assets/images/members/profile-pictures/`
- Files should be there: `filename.jpg`, `filename2.jpg`, etc.

**Via cPanel File Manager:**
- Navigate to: `public/assets/images/members/profile-pictures/`
- You should see the uploaded image files

**Via SSH:**
```bash
ls -la public/assets/images/members/profile-pictures/
```

### Step 5: Test on Live Server

1. **Clear browser cache** (Ctrl+Shift+Delete or Cmd+Shift+Delete)
2. **Hard refresh** the page (Ctrl+F5 or Cmd+Shift+R)
3. **Visit member dashboard** - passport should now display
4. **Check browser console** (F12 → Network tab):
   - Look for image requests
   - Should see: `https://waumini.co.tz/demo/assets/images/members/profile-pictures/filename.jpg`
   - Status should be **200 OK** (not 404)

---

## ⚠️ If You Get 404 Error (File Not Found)

If you see an error like:
```
https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/filename.jpeg 404 (Not Found)
```

**This means the file doesn't exist on the server.** See detailed guide: `FIX_MISSING_PASSPORT_FILES.md`

**Quick fix:**
1. Check if folder exists: `public/assets/images/members/profile-pictures/`
2. If not, create it: `mkdir -p public/assets/images/members/profile-pictures && chmod -R 755 public/assets/images`
3. Transfer images from local to live server OR re-upload them on live server

---

## 🔍 Troubleshooting

### If images still don't show:

1. **Check the actual URL in browser:**
   - Right-click broken image → "Open image in new tab"
   - Check the URL - does it have `/demo/` in it?
   - Does it have `/storage/` in it? (should NOT have this)

2. **Check browser console (F12):**
   - Network tab → Look for image requests
   - What status code? (404 = file not found, 403 = permission denied)

3. **Check file permissions:**
   ```bash
   chmod -R 755 public/assets/images
   ```

4. **Check APP_URL in .env:**
   ```
   APP_URL=https://waumini.co.tz/demo
   ```
   Then run: `php artisan config:clear`

5. **Check if folder exists:**
   ```bash
   mkdir -p public/assets/images/members/profile-pictures
   chmod -R 755 public/assets/images
   ```

---

## ✅ Expected Result

After deployment:
- ✅ Passport images display correctly on all dashboards
- ✅ Member profile pages show passport photos
- ✅ Identity cards show passport photos
- ✅ All views use correct URL format: `domain/demo/assets/images/members/profile-pictures/filename.jpg`

---

## Summary

**What was wrong:**
- Views used `asset('storage/' . $path)` but database has `assets/images/...` paths
- This created wrong URLs pointing to non-existent files

**What was fixed:**
- All 8 view files now use `asset($path)` directly
- This generates correct URLs: `domain/demo/assets/images/members/profile-pictures/filename.jpg`

**What you need to do:**
1. Upload the 8 fixed view files to live server
2. Clear Laravel cache on live server
3. Verify database paths are correct
4. Verify image files exist on server
5. Test and clear browser cache

