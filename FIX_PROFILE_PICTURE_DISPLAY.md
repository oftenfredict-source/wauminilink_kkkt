# Fix Profile Picture Display Issue

## Problem
Images are stored in `public/storage/member/profile-pictures/filename.jpg` but not displaying in the browser.

## Diagnosis Steps

### Step 1: Check Database Paths

Run this SQL query to see what paths are stored:

```sql
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 10;
```

**Expected format:** `member/profile-pictures/filename.jpg`

**Wrong formats:**
- ❌ `storage/member/profile-pictures/filename.jpg` (has `storage/` prefix)
- ❌ `assets/images/members/profile-pictures/filename.jpg` (old format)
- ❌ `public/storage/member/profile-pictures/filename.jpg` (has `public/` prefix)

### Step 2: Run Diagnostic Script

Run the diagnostic script to check all paths:

```bash
php debug_profile_picture_paths.php
```

This will show:
- Database paths
- Whether files exist in storage
- Whether files exist in public/storage
- Symlink status
- Any issues found

### Step 3: Check Storage Symlink

Verify the symlink exists:

```bash
# On Linux/Mac
ls -la public/storage

# Should show:
# public/storage -> ../storage/app/public
```

If symlink doesn't exist, create it:

```bash
php artisan storage:link
```

### Step 4: Check File Permissions

Ensure files are readable:

```bash
# On Linux/Mac
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

---

## Common Issues and Fixes

### Issue 1: Database Path Has `storage/` Prefix

**Symptom:** Database has `storage/member/profile-pictures/filename.jpg`

**Fix:** Remove the `storage/` prefix from database:

```sql
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';
```

### Issue 2: Database Path Has `public/` Prefix

**Symptom:** Database has `public/storage/member/profile-pictures/filename.jpg`

**Fix:** Remove the `public/` prefix:

```sql
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'public/storage/', '')
WHERE profile_picture LIKE 'public/storage/%';
```

### Issue 3: Wrong Path Format in Database

**Symptom:** Database has old format like `assets/images/members/profile-pictures/filename.jpg`

**Fix:** Convert to new format:

```sql
UPDATE members 
SET profile_picture = REPLACE(
    REPLACE(profile_picture, 'assets/images/members/profile-pictures/', 'member/profile-pictures/'),
    'members/profile-pictures/',
    'member/profile-pictures/'
)
WHERE profile_picture LIKE '%profile-pictures%';
```

### Issue 4: Files Not in Storage Directory

**Symptom:** Files exist in `public/storage/` but not in `storage/app/public/`

**Fix:** Move files to correct location:

```bash
# On Linux/Mac
mkdir -p storage/app/public/member/profile-pictures
mv public/storage/member/profile-pictures/* storage/app/public/member/profile-pictures/

# On Windows (PowerShell)
New-Item -ItemType Directory -Force -Path "storage\app\public\member\profile-pictures"
Move-Item -Path "public\storage\member\profile-pictures\*" -Destination "storage\app\public\member\profile-pictures\"
```

Then recreate symlink:

```bash
php artisan storage:link
```

### Issue 5: Symlink Not Working

**Symptom:** `public/storage` is not a symlink or points to wrong location

**Fix:**

1. Remove old symlink/directory:
   ```bash
   # On Linux/Mac
   rm -rf public/storage
   
   # On Windows (PowerShell)
   Remove-Item -Recurse -Force public\storage
   ```

2. Create new symlink:
   ```bash
   php artisan storage:link
   ```

3. Verify:
   ```bash
   ls -la public/storage
   # Should show: public/storage -> ../storage/app/public
   ```

---

## Quick Fix Script

If you want to fix all common issues at once, run this SQL:

```sql
-- Fix paths with storage/ prefix
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';

-- Fix paths with public/storage/ prefix
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'public/storage/', '')
WHERE profile_picture LIKE 'public/storage/%';

-- Fix old assets/images/ paths
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'assets/images/members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'assets/images/members/profile-pictures/%';

-- Fix members/profile-pictures/ to member/profile-pictures/
UPDATE members 
SET profile_picture = REPLACE(profile_picture, 'members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'members/profile-pictures/%';
```

---

## Verification

After fixing, verify:

1. **Check a member's profile picture in database:**
   ```sql
   SELECT id, full_name, profile_picture 
   FROM members 
   WHERE profile_picture IS NOT NULL 
   LIMIT 1;
   ```
   Should show: `member/profile-pictures/filename.jpg`

2. **Check file exists:**
   ```bash
   ls -la storage/app/public/member/profile-pictures/
   ```

3. **Check symlink:**
   ```bash
   ls -la public/storage/member/profile-pictures/
   ```

4. **Test in browser:**
   - Go to member dashboard
   - Check browser DevTools → Network tab
   - Image URL should be: `domain/storage/member/profile-pictures/filename.jpg`
   - Should return 200 OK status

---

## How It Should Work

1. **File Storage:** `storage/app/public/member/profile-pictures/filename.jpg`
2. **Database Path:** `member/profile-pictures/filename.jpg`
3. **Symlink:** `public/storage` → `storage/app/public`
4. **View Code:** `asset('storage/' . $member->profile_picture)`
5. **Generated URL:** `domain/storage/member/profile-pictures/filename.jpg`
6. **File Accessed:** `public/storage/member/profile-pictures/filename.jpg` (via symlink)

---

## Still Not Working?

If images still don't display after all fixes:

1. **Check web server configuration:**
   - Ensure `.htaccess` allows access to `storage/` directory
   - Check if there are any URL rewrite rules blocking it

2. **Check file permissions:**
   ```bash
   chmod -R 755 storage/app/public
   chmod -R 755 public/storage
   ```

3. **Clear Laravel cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

4. **Check browser console:**
   - Open DevTools → Console
   - Look for 404 errors
   - Check the exact URL being requested

5. **Test direct URL:**
   - Try accessing: `http://yourdomain.com/storage/member/profile-pictures/filename.jpg`
   - If this works, the issue is in the view code
   - If this doesn't work, the issue is with the symlink or file location

