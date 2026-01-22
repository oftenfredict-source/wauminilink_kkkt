# Troubleshooting Dashboard Issues

## Common Issues & Solutions

### Issue 1: "Page Not Corresponding" Error

**Possible Causes:**
1. Migrations not run
2. User doesn't have campus_id assigned
3. Missing view files
4. Route not registered

**Solutions:**

#### Step 1: Run Migrations
```bash
php artisan migrate
```

#### Step 2: Check User's Campus Assignment
```sql
-- Check if user has campus_id
SELECT id, name, email, role, campus_id FROM users WHERE id = YOUR_USER_ID;

-- If NULL, assign to main campus
UPDATE users SET campus_id = (SELECT id FROM campuses WHERE is_main_campus = true LIMIT 1) WHERE id = YOUR_USER_ID;
```

#### Step 3: Verify Routes
```bash
php artisan route:list --name=branch
php artisan route:list --name=usharika
```

#### Step 4: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Issue 2: Dashboard Not Showing in Sidebar

**Check:**
1. User's role and campus assignment
2. Sidebar menu conditions
3. User helper methods (isBranchUser, isUsharikaAdmin)

**Debug:**
Add this to any view to check user status:
```php
@php
    $user = auth()->user();
    $campus = $user->getCampus();
    dump([
        'user_id' => $user->id,
        'role' => $user->role,
        'campus_id' => $user->campus_id,
        'is_usharika_admin' => $user->is_usharika_admin,
        'campus' => $campus ? $campus->name : 'NULL',
        'is_main_campus' => $campus ? $campus->is_main_campus : false,
        'isBranchUser' => $user->isBranchUser(),
        'isUsharikaAdmin' => $user->isUsharikaAdmin(),
    ]);
@endphp
```

### Issue 3: 403 Forbidden Error

**Causes:**
- User not assigned to campus
- User trying to access wrong dashboard
- Campus not set up correctly

**Fix:**
1. Assign user to campus
2. Create main campus if doesn't exist
3. Check middleware logic

### Issue 4: View Not Found Error

**Check if views exist:**
- `resources/views/branch/dashboard.blade.php`
- `resources/views/usharika/dashboard.blade.php`

**Fix:**
- Views should be created (already done)
- Clear view cache: `php artisan view:clear`

## Quick Fix Checklist

- [ ] Run `php artisan migrate`
- [ ] Assign campus_id to users
- [ ] Create main campus (Usharika)
- [ ] Set is_usharika_admin = true for main admin
- [ ] Clear all caches
- [ ] Check routes are registered
- [ ] Verify views exist
- [ ] Check user's campus assignment

## Testing Steps

1. **Login as Admin**
   - Should see "Usharika Dashboard" in sidebar
   - Click it → Should show Usharika dashboard

2. **Create a Branch**
   - Go to Campuses → Create Branch
   - Assign a user to that branch

3. **Login as Branch User**
   - Should see "Branch Dashboard" in sidebar
   - Click it → Should show branch dashboard

4. **Register Member as Branch User**
   - Go to Add Member
   - Branch should be hidden/auto-selected
   - Submit → Member goes to branch

## Still Having Issues?

Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Or check browser console for JavaScript errors.














