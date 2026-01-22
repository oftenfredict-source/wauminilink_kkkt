# Very Simple Steps - Fix Passport Images

## 🎯 What You Need to Do (3 Simple Steps)

---

## Step 1: Upload 1 File to Server (5 minutes)

### What to do:
1. **Open cPanel** (the website you showed me: `https://www.wauminilink.co.tz:2083`)
2. **Log in** with your username and password
3. **Click "File Manager"** (look for it in cPanel)
4. **Find your Laravel project folder** (might be called `demo` or `wauminilink` or `public_html/demo`)
5. **Go to this folder:** `resources/views/members/`
6. **Click "Upload" button** (usually at the top)
7. **Select this file from your computer:**
   - `C:\xampp\htdocs\WauminiLink\resources\views\members\dashboard.blade.php`
8. **Wait for upload to finish**
9. **Done!**

**That's it for Step 1!**

---

## Step 2: Clear Cache (2 minutes)

### What to do:
1. **Still in cPanel File Manager**
2. **Go to:** `public/` folder (click on it)
3. **Click "New File"** button
4. **Name it:** `clear-cache.php` (type exactly this)
5. **Click "Edit"** (or double-click the file)
6. **Delete everything** in the file
7. **Copy and paste this code:**

```php
<?php
chdir(__DIR__ . '/..');
exec('php artisan view:clear 2>&1', $output1);
exec('php artisan cache:clear 2>&1', $output2);
exec('php artisan config:clear 2>&1', $output3);
echo "<h2>Cache Cleared!</h2><pre>";
echo "View: " . implode("\n", $output1) . "\n\n";
echo "Cache: " . implode("\n", $output2) . "\n\n";
echo "Config: " . implode("\n", $output3) . "\n\n";
echo "</pre>";
echo "<p><strong>✅ DELETE THIS FILE NOW!</strong></p>";
```

8. **Click "Save Changes"**
9. **Open a new browser tab**
10. **Visit:** `https://www.wauminilink.co.tz/demo/clear-cache.php`
11. **You should see "Cache Cleared!" message**
12. **Go back to cPanel**
13. **Delete the file** `clear-cache.php` (right-click → Delete)

**That's it for Step 2!**

---

## Step 3: Clear Browser and Test (1 minute)

### What to do:
1. **Press these keys together:** `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
2. **Check the box:** "Cached images and files"
3. **Click "Clear data"**
4. **Close your browser completely**
5. **Open browser again**
6. **Visit:** `https://www.wauminilink.co.tz/demo`
7. **Log in**
8. **Check if passport images show!**

**That's it!**

---

## ✅ Done!

If you did all 3 steps, your passport images should now display!

---

## ❓ If You Get Stuck

Tell me which step you're on and what problem you see:

- **Step 1:** "I can't find File Manager" or "I don't know which folder"
- **Step 2:** "I can't create the file" or "The code doesn't work"
- **Step 3:** "Images still don't show"

I'll help you with that specific step!

---

## 📸 Visual Guide

### Step 1 - Finding File Manager:
```
cPanel → Files section → File Manager
```

### Step 1 - Finding the folder:
```
File Manager → public_html → demo → resources → views → members
```

### Step 2 - Creating file:
```
File Manager → public → New File → clear-cache.php
```

---

## 🎯 Summary

**Just 3 things:**
1. ✅ Upload 1 file (`dashboard.blade.php`)
2. ✅ Create cache clear file and run it
3. ✅ Clear browser cache

**Total time: ~8 minutes**

That's all you need to do!

