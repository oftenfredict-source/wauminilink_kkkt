# Quick Fix: Profile Pictures Not Displaying

## The Problem

If images are stored in `public/storage/member/profile-pictures/` but not displaying, the most common issue is **the database path has the wrong format**.

## Quick Diagnosis

Run this SQL query to check your database paths:

```sql
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 5;
```

### What You Should See:
✅ **CORRECT:** `member/profile-pictures/filename.jpg`

### What You Might See (WRONG):
❌ `storage/member/profile-pictures/filename.jpg` (has `storage/` prefix - **THIS IS THE PROBLEM**)
❌ `public/storage/member/profile-pictures/filename.jpg` (has `public/storage/` prefix)
❌ `assets/images/members/profile-pictures/filename.jpg` (old format)

## The Fix

### Option 1: Quick SQL Fix (Recommended)

Run this SQL to fix all paths at once:

```sql
-- Remove 'storage/' prefix if present
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';

-- Remove 'public/storage/' prefix if present
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'public/storage/', '')
WHERE profile_picture LIKE 'public/storage/%';

-- Fix old format to new format
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'assets/images/members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'assets/images/members/profile-pictures/%';

-- Fix plural 'members' to singular 'member'
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'members/profile-pictures/%';
```

### Option 2: Use the SQL File

1. Open `fix_profile_picture_paths.sql`
2. Run each UPDATE statement in your database
3. Verify with the SELECT query at the end

## Why This Happens

The view code uses:
```php
asset('storage/' . $member->profile_picture)
```

If the database has `storage/member/profile-pictures/filename.jpg`, it becomes:
```
asset('storage/storage/member/profile-pictures/filename.jpg')
```

Which is **WRONG** and won't work!

The database should only have: `member/profile-pictures/filename.jpg`

Then `asset('storage/' . $member->profile_picture)` becomes:
```
asset('storage/member/profile-pictures/filename.jpg')
```

Which is **CORRECT**! ✅

## Verification

After running the fix:

1. **Check database:**
   ```sql
   SELECT profile_picture FROM members WHERE profile_picture IS NOT NULL LIMIT 1;
   ```
   Should show: `member/profile-pictures/filename.jpg`

2. **Check file exists:**
   - File should be at: `storage/app/public/member/profile-pictures/filename.jpg`
   - Symlink should make it accessible at: `public/storage/member/profile-pictures/filename.jpg`

3. **Test in browser:**
   - Go to member dashboard
   - Open browser DevTools (F12) → Network tab
   - Reload page
   - Check image request URL
   - Should be: `yourdomain.com/storage/member/profile-pictures/filename.jpg`
   - Should return: **200 OK** (not 404)

## Still Not Working?

1. **Check symlink exists:**
   ```bash
   php artisan storage:link
   ```

2. **Check file permissions:**
   ```bash
   # On Linux/Mac
   chmod -R 755 storage/app/public
   chmod -R 755 public/storage
   ```

3. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

4. **Test direct URL:**
   - Try: `http://yourdomain.com/storage/member/profile-pictures/ACTUAL_FILENAME.jpg`
   - Replace `ACTUAL_FILENAME.jpg` with a real filename from your database
   - If this works, the issue is fixed!
   - If this doesn't work, check the symlink

