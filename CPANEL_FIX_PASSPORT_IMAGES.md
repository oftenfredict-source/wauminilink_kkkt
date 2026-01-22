# Fix Passport Images via cPanel - Step by Step

## 🎯 Goal
Fix passport images not displaying using cPanel File Manager.

---

## Step 1: Log into cPanel

1. **Go to:** `https://www.wauminilink.co.tz:2083`
2. **Enter your username and password**
3. **Click "Log in"**

---

## Step 2: Open File Manager

1. In cPanel, find **"File Manager"** (usually under "Files" section)
2. **Click "File Manager"**
3. **Select:** "Document Root for: wauminilink.co.tz" (or your domain)
4. **Click "Go"**

---

## Step 3: Navigate to Images Folder

1. **Navigate to your Laravel project folder** (usually `public_html/demo` or similar)
2. **Go to:** `public/assets/images/members/profile-pictures/`
3. **Verify files exist** - You should see image files (`.jpeg`, `.jpg`, `.png`)

**If folder doesn't exist:**
- Navigate to `public/assets/images/`
- Click **"New Folder"** → Name it `members`
- Open `members` folder
- Click **"New Folder"** → Name it `profile-pictures`

---

## Step 4: Check View Files Are Updated

### Check if view files need updating:

1. **Navigate to:** `resources/views/members/`
2. **Open:** `dashboard.blade.php`
3. **Press Ctrl+F** (Find)
4. **Search for:** `asset('storage/'`
5. **If you find it** → View file needs updating!

### Upload Fixed View Files:

**You need to upload these 8 files from your local machine:**

1. `resources/views/members/dashboard.blade.php`
2. `resources/views/pastor/dashboard.blade.php`
3. `resources/views/dashboard.blade.php`
4. `resources/views/members/settings.blade.php`
5. `resources/views/members/partials/card-view.blade.php`
6. `resources/views/leaders/bulk-identity-cards.blade.php`
7. `resources/views/leaders/identity-card.blade.php`
8. `resources/views/members/identity-card.blade.php`

**How to upload:**
1. **Navigate to the correct folder** in cPanel File Manager
2. **Click "Upload"** button (top menu)
3. **Select the file** from your local machine
4. **Upload** - It will overwrite the existing file
5. **Repeat for all 8 files**

---

## Step 5: Set Folder Permissions

1. **Navigate to:** `public/assets/images/`
2. **Right-click** on `images` folder
3. **Click "Change Permissions"**
4. **Set permissions to 755:**
   - ✅ Owner: Read, Write, Execute (7)
   - ✅ Group: Read, Execute (5)
   - ✅ Public: Read, Execute (5)
5. **Check "Recurse into subdirectories"** (important!)
6. **Click "Change Permissions"**

---

## Step 6: Clear Laravel Cache

### Option A: Create Cache Clear File (Easiest)

1. **Navigate to:** `public/` folder
2. **Click "New File"**
3. **Name it:** `clear-cache.php`
4. **Click "Edit"** (or double-click the file)
5. **Paste this code:**
```php
<?php
chdir(__DIR__ . '/..');
exec('php artisan view:clear 2>&1', $output1);
exec('php artisan cache:clear 2>&1', $output2);
exec('php artisan config:clear 2>&1', $output3);
exec('php artisan route:clear 2>&1', $output4);
echo "<h2>All Caches Cleared</h2><pre>";
echo "View Cache: " . implode("\n", $output1) . "\n\n";
echo "Application Cache: " . implode("\n", $output2) . "\n\n";
echo "Config Cache: " . implode("\n", $output3) . "\n\n";
echo "Route Cache: " . implode("\n", $output4) . "\n\n";
echo "</pre>";
echo "<p><strong>DELETE THIS FILE NOW!</strong></p>";
```
6. **Save** the file
7. **Visit in browser:** `https://www.wauminilink.co.tz/demo/clear-cache.php`
8. **Check the output** - Should show "Cleared" messages
9. **Go back to cPanel File Manager**
10. **Delete** `clear-cache.php` file (important for security!)

### Option B: Use Terminal (If Available)

1. In cPanel, find **"Terminal"** or **"SSH Access"**
2. **Open Terminal**
3. **Run commands:**
```bash
cd /path/to/your/laravel/project
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## Step 7: Check .env File (APP_URL)

1. **Navigate to root** of Laravel project (where `.env` file is)
2. **Find:** `.env` file
3. **Click "Edit"**
4. **Find:** `APP_URL=`
5. **Should be:**
   ```
   APP_URL=https://www.wauminilink.co.tz/demo
   ```
6. **If wrong, change it**
7. **Save**
8. **Clear config cache** (Step 6)

---

## Step 8: Test Direct Image Access

1. **Note one filename** from `profile-pictures` folder (e.g., `69300a3d22450_1764756029.jpeg`)
2. **Open new browser tab**
3. **Try accessing:**
   ```
   https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/69300a3d22450_1764756029.jpeg
   ```
4. **Results:**
   - ✅ **Image displays** → File exists, problem was cache/view
   - ❌ **404 Not Found** → Check filename matches database
   - ❌ **403 Forbidden** → Permission issue (redo Step 5)

---

## Step 9: Clear Browser Cache

1. **Press:** Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac)
2. **Select:** "Cached images and files"
3. **Time range:** "All time"
4. **Click "Clear data"**
5. **Close and reopen browser**
6. **Hard refresh:** Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

---

## Step 10: Test on Website

1. **Visit your website:** `https://www.wauminilink.co.tz/demo`
2. **Log in**
3. **Go to member dashboard** or member list
4. **Check if passport images display**

**If still not working:**
- Check browser console (F12 → Network tab)
- Look for image requests
- Check what URL it's trying to load
- Check status code (200/404/403)

---

## 📋 Quick Checklist

- [ ] Logged into cPanel
- [ ] Opened File Manager
- [ ] Verified images exist in `public/assets/images/members/profile-pictures/`
- [ ] Checked view files (searched for `asset('storage/'`)
- [ ] Uploaded 8 fixed view files (if needed)
- [ ] Set permissions to 755 on `images` folder (recursive)
- [ ] Created and ran `clear-cache.php`
- [ ] Deleted `clear-cache.php` file
- [ ] Checked APP_URL in `.env` file
- [ ] Tested direct image access
- [ ] Cleared browser cache
- [ ] Hard refreshed page (Ctrl+F5)
- [ ] Tested on website

---

## 🔍 Troubleshooting in cPanel

### Problem: Can't find Laravel project folder

**Solution:**
- Look in: `public_html/demo` or `public_html/wauminilink` or `domains/wauminilink.co.tz/public_html/demo`
- Check where your website files are located

### Problem: Can't edit .env file

**Solution:**
- Make sure you're in the root folder (where `artisan` file is)
- `.env` file should be there
- If hidden, enable "Show Hidden Files" in File Manager settings

### Problem: Permission change doesn't work

**Solution:**
- Make sure you're changing permissions on the **folder**, not individual files
- Check "Recurse into subdirectories"
- Try changing permissions on parent folder first

### Problem: Cache clear file doesn't work

**Solution:**
- Check file is in `public/` folder
- Check PHP code is correct (no typos)
- Check file permissions (should be 644)
- Try accessing via browser directly

---

## ✅ Expected Result

After completing all steps:
- ✅ Images display correctly on all dashboards
- ✅ Member profile pages show passport photos
- ✅ Identity cards show passport photos
- ✅ No 404 or 403 errors

---

## 💡 Pro Tips

1. **Always delete cache clear files** after use (security risk!)
2. **Backup before editing** - cPanel File Manager has "Backup" option
3. **Use "Find" (Ctrl+F)** to search in files quickly
4. **Check file dates** - Make sure uploaded files have recent dates
5. **Test one image first** - If one works, all should work

---

## 🆘 Still Not Working?

If images still don't display after all steps:

1. **Check browser console (F12):**
   - Network tab → Find image request
   - What URL is it trying to load?
   - What status code?

2. **Check database:**
   - Run SQL: `SELECT profile_picture FROM members WHERE profile_picture IS NOT NULL LIMIT 5;`
   - Do paths start with `assets/images/`?

3. **Check Laravel logs:**
   - In cPanel, navigate to: `storage/logs/`
   - Open `laravel.log`
   - Look for recent errors

Tell me what you find, and I'll help you fix it!

