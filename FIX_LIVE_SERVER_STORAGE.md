# Fix Profile Pictures Not Displaying on Live Server

## Problem
Files exist in `storage/app/public/member/profile-pictures/` but images don't display in browser.

## Root Cause
The symlink `public/storage` → `storage/app/public` is missing or broken on the live server.

## Solution

### Step 1: Check if Symlink Exists

In cPanel File Manager, navigate to:
```
/home/wauminilink/demo/public/
```

Look for a folder named `storage`. 

**If it exists:**
- Check if it's a symlink (should show as a link icon or different color)
- If it's a regular folder, delete it first

**If it doesn't exist:**
- You need to create it

### Step 2: Create the Symlink

#### Option A: Via SSH (Recommended)

1. Connect to your server via SSH
2. Navigate to your project:
   ```bash
   cd /home/wauminilink/demo
   ```
3. Remove old storage folder if it exists (if it's not a symlink):
   ```bash
   rm -rf public/storage
   ```
4. Create the symlink:
   ```bash
   php artisan storage:link
   ```
5. Verify:
   ```bash
   ls -la public/storage
   ```
   Should show: `public/storage -> ../storage/app/public`

#### Option B: Via cPanel File Manager

1. **Delete old storage folder** (if it exists and is NOT a symlink):
   - Go to: `public/storage`
   - Delete the entire folder

2. **Create symlink manually:**
   - This is tricky in cPanel, so SSH is better
   - Or use Terminal in cPanel if available

#### Option C: Via cPanel Terminal

1. Open cPanel → Terminal
2. Run:
   ```bash
   cd /home/wauminilink/demo
   rm -rf public/storage
   php artisan storage:link
   ```

### Step 3: Verify File Permissions

Set correct permissions:

```bash
# Via SSH or Terminal
cd /home/wauminilink/demo
chmod -R 755 storage/app/public
chmod -R 755 public/storage
```

### Step 4: Test the URL

After creating the symlink, test if the file is accessible:

1. **Get a filename from database:**
   ```sql
   SELECT profile_picture FROM members WHERE profile_picture IS NOT NULL LIMIT 1;
   ```
   Example result: `member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg`

2. **Test URL in browser:**
   ```
   https://www.wauminilink.co.tz/demo/storage/member/profile-pictures/1LLmjUHvBMG63RybHfnnfHtkISb8p8fqP0Vvwlgn.jpg
   ```

3. **If you get 404:**
   - Symlink is not working
   - Check symlink again: `ls -la public/storage`
   - Recreate it: `php artisan storage:link`

4. **If you get 403 (Forbidden):**
   - Permission issue
   - Run: `chmod -R 755 storage/app/public public/storage`

5. **If image displays:**
   - ✅ Symlink is working!
   - The issue might be in the view code or database paths

### Step 5: Fix Database Paths (If Needed)

Make sure database paths are correct:

```sql
-- Check current paths
SELECT id, full_name, profile_picture 
FROM members 
WHERE profile_picture IS NOT NULL 
LIMIT 5;

-- Fix plural 'members' to singular 'member'
UPDATE members
SET profile_picture = REPLACE(profile_picture, 'members/profile-pictures/', 'member/profile-pictures/')
WHERE profile_picture LIKE 'members/profile-pictures/%';

-- Remove 'storage/' prefix if present
UPDATE members
SET profile_picture = REPLACE(profile_picture, 'storage/', '')
WHERE profile_picture LIKE 'storage/%';
```

Expected format: `member/profile-pictures/filename.jpg`

## Quick Checklist

- [ ] Symlink exists: `public/storage` → `storage/app/public`
- [ ] File exists: `storage/app/public/member/profile-pictures/filename.jpg`
- [ ] Permissions correct: `755` on directories
- [ ] Database path correct: `member/profile-pictures/filename.jpg` (not `members/` or `storage/`)
- [ ] Test URL works: `https://www.wauminilink.co.tz/demo/storage/member/profile-pictures/filename.jpg`

## Common Issues

### Issue 1: Symlink Not Created
**Symptom:** `public/storage` doesn't exist or is a regular folder
**Fix:** Run `php artisan storage:link` via SSH/Terminal

### Issue 2: Wrong Path in Database
**Symptom:** Database has `members/profile-pictures/` (plural)
**Fix:** Run the UPDATE query above

### Issue 3: Permission Denied
**Symptom:** 403 Forbidden error
**Fix:** `chmod -R 755 storage/app/public public/storage`

### Issue 4: File Not Found
**Symptom:** 404 Not Found error
**Fix:** 
1. Verify file exists in `storage/app/public/member/profile-pictures/`
2. Verify symlink exists and points correctly
3. Check web server configuration

## Verification Commands

```bash
# Check symlink
ls -la /home/wauminilink/demo/public/storage

# Check file exists
ls -la /home/wauminilink/demo/storage/app/public/member/profile-pictures/

# Check permissions
ls -ld /home/wauminilink/demo/storage/app/public
ls -ld /home/wauminilink/demo/public/storage
```

