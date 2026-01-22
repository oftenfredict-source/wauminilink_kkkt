# Fix Password Reset on Hosted Server

## Problem
The password reset feature shows "Route not found" error on the hosted server even after code changes.

## Important: Server-Side Steps Required

Since this is a **hosted server** (not local), you need to perform these steps on your server:

### Step 1: Upload All Changed Files

Make sure these files are uploaded to your server:
- `routes/web.php` (route change)
- `app/Http/Controllers/MemberController.php` (controller change)
- `resources/views/members/view.blade.php` (frontend change)
- `resources/views/admin/users.blade.php` (frontend change - if using admin users page)

### Step 2: Clear All Laravel Caches on Server

**SSH into your server** and run these commands in your project directory:

```bash
# Clear route cache
php artisan route:clear

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# OPTIONAL: Rebuild caches (only if your server uses cached routes/config)
# php artisan route:cache
# php artisan config:cache
```

### Step 3: Set Correct Permissions

Make sure Laravel can write to storage and cache directories:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

(Replace `www-data` with your web server user if different)

### Step 4: Verify Route is Registered

Test if the route is registered:

```bash
php artisan route:list | grep reset-password
```

You should see:
```
POST  members/{id}/reset-password  members.reset-password
```

### Step 5: Check Server Logs

If it still doesn't work, check Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

Then try the password reset again and watch for any errors.

---

## Alternative: If You Can't SSH

If you don't have SSH access, you can create a temporary PHP file to clear caches:

### Create: `public/clear-cache.php`

```php
<?php
// WARNING: Delete this file after use for security!

// Change to your Laravel root directory
chdir(__DIR__ . '/..');

// Clear caches
exec('php artisan route:clear 2>&1', $output1);
exec('php artisan cache:clear 2>&1', $output2);
exec('php artisan config:clear 2>&1', $output3);
exec('php artisan view:clear 2>&1', $output4);

echo "<h2>Cache Cleared</h2>";
echo "<pre>";
echo "Route Cache: " . implode("\n", $output1) . "\n\n";
echo "Application Cache: " . implode("\n", $output2) . "\n\n";
echo "Config Cache: " . implode("\n", $output3) . "\n\n";
echo "View Cache: " . implode("\n", $output4) . "\n\n";
echo "</pre>";
echo "<p><strong>IMPORTANT: Delete this file now for security!</strong></p>";
```

1. Upload this file to `public/clear-cache.php`
2. Visit: `https://yourdomain.com/clear-cache.php`
3. **DELETE the file immediately after use!**

---

## Verify the Fix

After clearing caches:

1. **Clear your browser cache** (Ctrl+Shift+Delete or Cmd+Shift+Delete)
2. **Hard refresh** the page (Ctrl+F5 or Cmd+Shift+R)
3. Try resetting a member's password again
4. Check browser console (F12) for any JavaScript errors
5. Check network tab to see the actual request URL and response

---

## Debugging: Check What's Happening

### In Browser Console (F12)

After clicking "Reset Password", check:
1. **Console tab** - Look for the log messages:
   - "Resetting password for member ID: X"
   - "Request URL: /members/X/reset-password"
2. **Network tab** - Find the POST request to `/members/X/reset-password`:
   - Check the **Status Code** (should be 200, not 404)
   - Check the **Response** (should be JSON, not HTML)

### Expected Response (Success)

```json
{
  "success": true,
  "message": "Password reset successfully.",
  "password": "ABC12345",
  "sms_sent": true,
  "member_name": "John Doe",
  "phone_number": "+255..."
}
```

### If You See 404

- Route cache wasn't cleared
- Files weren't uploaded correctly
- Route conflict (less likely now)

### If You See 419

- CSRF token issue
- Session expired - refresh the page

### If You See 403

- User doesn't have admin permissions
- Check `auth()->user()->isAdmin()` returns true

---

## Still Not Working?

If it's still not working after all these steps:

1. **Check the actual error** in browser console (F12 → Network tab)
2. **Check server logs**: `storage/logs/laravel.log`
3. **Verify file uploads**: Make sure all files were uploaded correctly
4. **Test the route directly**: Try accessing the route with a tool like Postman or curl

### Test Route Directly (from server)

```bash
curl -X POST https://yourdomain.com/members/1/reset-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -b "laravel_session=YOUR_SESSION_COOKIE"
```

---

## Summary of Changes Made

1. **Route**: Changed from `/members/{member}/reset-password` to `/members/{id}/reset-password`
2. **Controller**: Changed parameter from `Member $member` to `$id` and manually find member
3. **Frontend**: Added better error handling and debugging logs
4. **Route constraint**: Added `where('id', '[0-9]+')` to ensure proper matching

All these changes need to be on your server for it to work!


