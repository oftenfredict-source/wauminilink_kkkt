# Simple cPanel Checklist - Fix Passport Images

## ⚠️ Security Note
**Never share your cPanel username and password with anyone!** This guide will help you fix it yourself in just a few minutes.

---

## ✅ Quick 5-Step Fix

### Step 1: Upload Fixed View Files (2 minutes)

**In cPanel File Manager:**

1. Navigate to: `resources/views/members/`
2. Click **"Upload"** button
3. Upload this file from your local machine:
   - `resources/views/members/dashboard.blade.php`
4. Repeat for these folders/files:
   - `resources/views/pastor/dashboard.blade.php`
   - `resources/views/dashboard.blade.php` (in `resources/views/`)
   - `resources/views/members/settings.blade.php`
   - `resources/views/members/partials/card-view.blade.php` (in `resources/views/members/partials/`)
   - `resources/views/leaders/bulk-identity-cards.blade.php` (in `resources/views/leaders/`)
   - `resources/views/leaders/identity-card.blade.php` (in `resources/views/leaders/`)
   - `resources/views/members/identity-card.blade.php` (in `resources/views/members/`)

**Total: 8 files to upload**

---

### Step 2: Set Permissions (30 seconds)

1. Navigate to: `public/assets/images/`
2. Right-click `images` folder
3. Click **"Change Permissions"**
4. Set to: **755**
5. Check: **"Recurse into subdirectories"**
6. Click **"Change Permissions"**

---

### Step 3: Clear Cache (1 minute)

1. Navigate to: `public/` folder
2. Click **"New File"**
3. Name: `clear-cache.php`
4. Click **"Edit"**
5. Copy and paste this code:

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

6. Click **"Save Changes"**
7. Open new browser tab
8. Visit: `https://www.wauminilink.co.tz/demo/clear-cache.php`
9. You should see "Cache Cleared!" message
10. Go back to cPanel
11. **Delete** `clear-cache.php` file (important!)

---

### Step 4: Clear Browser Cache (10 seconds)

1. Press: **Ctrl+Shift+Delete** (Windows) or **Cmd+Shift+Delete** (Mac)
2. Select: **"Cached images and files"**
3. Click: **"Clear data"**
4. Close browser and reopen

---

### Step 5: Test (30 seconds)

1. Visit: `https://www.wauminilink.co.tz/demo`
2. Log in
3. Go to member dashboard
4. **Check if passport images display!**

---

## 🎯 That's It!

If you follow these 5 steps, your images should display.

**Total time: ~5 minutes**

---

## ❓ Need Help with a Specific Step?

Tell me which step you're stuck on, and I'll give you more detailed instructions!

For example:
- "I can't find the upload button" → I'll show you where it is
- "I don't know which files to upload" → I'll list them clearly
- "The cache clear file doesn't work" → I'll help troubleshoot

---

## 🔒 Why I Can't Access Your cPanel

**Security reasons:**
- Your cPanel has access to your entire server
- It can modify databases, files, emails, etc.
- Sharing credentials is never safe
- You should be the only one with access

**But don't worry!** These steps are simple and I'll guide you through each one if you get stuck.

---

## 📞 If You Get Stuck

Just tell me:
1. **Which step** you're on (1, 2, 3, 4, or 5)
2. **What you see** (or what error message)
3. **What you're trying to do**

And I'll give you exact instructions for that step!

