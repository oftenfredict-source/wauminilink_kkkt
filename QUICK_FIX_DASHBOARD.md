# Quick Fix for Dashboard Issues

## If "Page Not Corresponding" Error

### Step 1: Run Migrations (CRITICAL)
```bash
php artisan migrate
```

This adds:
- `is_usharika_admin` to users table
- `campus_id` to leaders table

### Step 2: Set Up Main Campus (Usharika)
1. Go to **Campuses** → **Add New Campus**
2. Select **Main Campus**
3. Name it "Usharika" or your main church name
4. Fill in details and save

### Step 3: Assign Campus to Users
1. Go to **Users** management
2. Edit each user (admin, pastor, secretary, etc.)
3. Set `campus_id` to:
   - Main campus ID for Usharika users
   - Branch ID for branch users

### Step 4: Set Usharika Admin Flag
For main admin user:
```sql
UPDATE users SET is_usharika_admin = 1 WHERE role = 'admin' AND campus_id = (SELECT id FROM campuses WHERE is_main_campus = true LIMIT 1);
```

Or via UI:
1. Go to Users → Edit Admin
2. Check "Is Usharika Admin" (if field exists)
3. Save

### Step 5: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Step 6: Test Access
1. **Login as Admin** → Should see "Usharika Dashboard" in sidebar
2. **Click it** → Should show dashboard with all branches
3. **Create a branch** → Assign a user to it
4. **Login as branch user** → Should see "Branch Dashboard"

## Common Errors

### Error: "You must be assigned to a branch"
**Fix:** Assign `campus_id` to the user

### Error: "View not found"
**Fix:** Clear view cache: `php artisan view:clear`

### Error: Route not found
**Fix:** Clear route cache: `php artisan route:clear`

### Error: Method does not exist (isBranchUser, isUsharikaAdmin)
**Fix:** 
1. Check User model has these methods
2. Clear config cache: `php artisan config:clear`

## Still Not Working?

Check Laravel error log:
```
storage/logs/laravel.log
```

Or check browser console for errors.














