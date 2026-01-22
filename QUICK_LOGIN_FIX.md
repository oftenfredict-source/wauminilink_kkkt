# Quick Fix for Login Error: "The provided credentials do not match our records"

## 🔍 Troubleshooting Steps

### Step 1: Check if User Exists

Run this command to see all users:
```bash
php check_and_reset_user.php
```

This will:
- List all users in the system
- Show their email, role, and campus
- Allow you to reset passwords

### Step 2: Common Issues & Solutions

#### Issue 1: User Doesn't Exist
**Solution:** Create the user first
1. Go to **Admin → System Management → Manage Users**
2. Click **Create New User**
3. Fill in details and create

#### Issue 2: Wrong Email/Username
**Solution:** Check the exact email
- Make sure you're using the exact email address
- Check for typos (case-sensitive)
- Try the email shown in the user list

#### Issue 3: Wrong Password
**Solution:** Reset the password
```bash
php check_and_reset_user.php
```
- Enter the email when prompted
- Choose to reset password
- New password will be set

#### Issue 4: User Not Assigned to Campus
**Solution:** Assign campus to user
1. Go to **Admin → Manage Users**
2. Click **Edit** on the user
3. Select **Branch/Campus**
4. Save

### Step 3: Quick Password Reset (All Users)

If you want to reset ALL passwords to a default:

```bash
php check_and_reset_user.php
```

Then type `all` when prompted. All passwords will be reset to `password123`.

### Step 4: Test Login

After resetting password:
1. Go to login page
2. Enter email
3. Enter password: `password123` (or the password you set)
4. Click Login

## 🚨 Still Having Issues?

### Check Database Connection
```bash
php artisan migrate:status
```

### Check User Table
```sql
SELECT id, name, email, role, campus_id FROM users;
```

### Check if Password is Hashed
Passwords should be hashed. If you see plain text, that's the problem.

## 📝 Quick Test User Creation

If you need a test user quickly:

1. **Via UI:**
   - Admin → Manage Users → Create New User
   - Fill in details
   - Select campus
   - Create

2. **Via Script:**
   ```bash
   php artisan tinker
   ```
   Then:
   ```php
   $user = \App\Models\User::create([
       'name' => 'Test User',
       'email' => 'test@example.com',
       'password' => \Hash::make('password123'),
       'role' => 'pastor',
       'campus_id' => 1, // Your main campus ID
   ]);
   echo "User created! Email: test@example.com, Password: password123";
   ```

## ✅ Verification Checklist

- [ ] User exists in database
- [ ] Email is correct (case-sensitive)
- [ ] Password is hashed (not plain text)
- [ ] User has campus_id assigned
- [ ] Database connection is working
- [ ] Try resetting password

## 🎯 Most Common Fix

**90% of the time, it's a wrong password. Reset it:**

```bash
php check_and_reset_user.php
```

Enter the email, choose to reset, and use the new password to login!














