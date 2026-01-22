# Complete Fix Guide: Images Not Displaying

## Problem
- ✅ Images exist in database
- ✅ Images exist on server
- ❌ Images NOT displaying in browser

## Step-by-Step Fix

### Step 1: Run Diagnostic

```bash
php diagnose_image_display.php
```

This will show you exactly what's wrong.

### Step 2: Fix Database Paths

Run these SQL queries in order:

```sql
-- 1. Check current paths
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 5;

-- 2. Remove 'storage/' prefix if present
UPDATE members
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';

-- 3. Remove 'public/storage/' prefix if present
UPDATE members
SET profile_picture = REPLACE(profile_picture, 'public/storage/', '')
WHERE profile_picture LIKE 'public/storage/%';

-- 4. Fix plural 'members/' to singular 'member/'
UPDATE members
SET profile_picture = REPLACE(profile_picture, 'members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'members/profile-pictures/%';

-- 5. Verify fix
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 5;
```

**Expected result:** All paths should be: `member/profile-pictures/filename.jpg`

### Step 3: Fix Symlink (On Live Server)

#### Via SSH:
```bash
cd /home/wauminilink/demo
rm -rf public/storage
php artisan storage:link
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

#### Via cPanel Terminal:
1. Open cPanel → Terminal
2. Run the commands above

#### Verify symlink:
```bash
ls -la public/storage
```
Should show: `public/storage -> ../storage/app/public`

### Step 4: Test Direct URL

1. Get a filename from database:
   ```sql
   SELECT profile_picture FROM members WHERE profile_picture IS NOT NULL LIMIT 1;
   ```
   Example: `member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg`

2. Test in browser:
   ```
   https://www.wauminilink.co.tz/demo/storage/member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg
   ```

3. **If 404 Not Found:**
   - Symlink is missing or broken
   - Recreate: `php artisan storage:link`

4. **If 403 Forbidden:**
   - Permission issue
   - Fix: `chmod -R 755 storage/app/public public/storage`

5. **If image displays:**
   - ✅ Symlink works!
   - Check view code or browser console

### Step 5: Check View Code

Make sure views use:
```php
asset('storage/' . $member->profile_picture)
```

NOT:
```php
asset($member->profile_picture)  // ❌ WRONG
asset('storage/storage/' . $member->profile_picture)  // ❌ WRONG
```

### Step 6: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 7: Check Browser Console

1. Open browser DevTools (F12)
2. Go to Console tab
3. Look for errors
4. Go to Network tab
5. Reload page
6. Check image requests:
   - Status should be **200 OK**
   - If **404**: Symlink issue
   - If **403**: Permission issue
   - If **500**: Server error

## Common Issues & Fixes

### Issue 1: Database Path Has Wrong Format

**Symptom:** Database has `storage/member/profile-pictures/...` or `members/profile-pictures/...`

**Fix:**
```sql
UPDATE members
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';

UPDATE members
SET profile_picture = REPLACE(profile_picture, 'members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'members/profile-pictures/%';
```

### Issue 2: Symlink Missing or Broken

**Symptom:** `public/storage` doesn't exist or is a regular folder

**Fix:**
```bash
rm -rf public/storage
php artisan storage:link
```

### Issue 3: Wrong Permissions

**Symptom:** 403 Forbidden error

**Fix:**
```bash
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

### Issue 4: Files in Wrong Location

**Symptom:** Files in `public/storage/` but not in `storage/app/public/`

**Fix:**
```bash
# Move files to correct location
mkdir -p storage/app/public/member/profile-pictures
mv public/storage/member/profile-pictures/* storage/app/public/member/profile-pictures/
# Then recreate symlink
rm -rf public/storage
php artisan storage:link
```

## Quick Checklist

- [ ] Database paths are: `member/profile-pictures/filename.jpg` (not `members/` or `storage/`)
- [ ] Symlink exists: `public/storage` → `storage/app/public`
- [ ] Files exist: `storage/app/public/member/profile-pictures/filename.jpg`
- [ ] Permissions correct: `755` on directories
- [ ] Views use: `asset('storage/' . $member->profile_picture)`
- [ ] Direct URL works: `domain/storage/member/profile-pictures/filename.jpg`
- [ ] Cache cleared: `php artisan cache:clear`

## Still Not Working?

1. **Check web server logs:**
   - Look for 404 or 403 errors
   - Check Apache/Nginx error logs

2. **Check .htaccess:**
   - Ensure it allows access to `storage/` directory
   - Should not block image requests

3. **Test with different browser:**
   - Clear browser cache
   - Try incognito/private mode

4. **Check file actually exists:**
   ```bash
   ls -la storage/app/public/member/profile-pictures/
   ```

5. **Verify symlink target:**
   ```bash
   readlink public/storage
   ```
   Should show: `../storage/app/public`

## Expected Flow

1. **Upload:** File saved to `storage/app/public/member/profile-pictures/filename.jpg`
2. **Database:** Path stored as `member/profile-pictures/filename.jpg`
3. **View:** Generates URL: `asset('storage/' . $member->profile_picture)`
4. **URL:** Becomes `domain/storage/member/profile-pictures/filename.jpg`
5. **Symlink:** `public/storage` → `storage/app/public`
6. **Access:** Browser accesses `public/storage/member/profile-pictures/filename.jpg`
7. **Result:** Image displays! ✅

If any step fails, images won't display.

