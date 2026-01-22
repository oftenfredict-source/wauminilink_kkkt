# How to Access Branch Dashboard - Step by Step Guide

## 🎯 Overview

To login to the branch dashboard, you need:
1. A branch (sub campus) created
2. A user assigned to that branch
3. Login with that user's credentials

## 📋 Step-by-Step Instructions

### Step 1: Create a Branch (As Usharika Admin)

1. **Login as Usharika Admin** (main admin user)
2. Go to **Campuses** in the sidebar
3. Click **Add New Campus**
4. Fill in the form:
   - **Campus Type**: Select **Sub Campus**
   - **Parent Campus**: Select **Usharika** (main campus)
   - **Name**: Enter branch name (e.g., "Longuo B Branch")
   - Fill in other details (address, location, etc.)
5. Click **Create Campus**

✅ Branch is now created!

---

### Step 2: Assign a User to the Branch

You have **two options**:

#### Option A: Assign Existing User to Branch

1. Go to **Users** management (Admin → Manage Users)
2. Find the user you want to assign (e.g., a pastor, secretary)
3. Click **Edit**
4. Set **Campus/Branch**: Select the branch you just created
5. Save

#### Option B: Create New User for Branch

1. Go to **Users** → **Add New User**
2. Fill in user details:
   - Name, Email, Password
   - **Role**: Select role (pastor, secretary, treasurer, etc.)
   - **Campus/Branch**: Select the branch
3. Click **Create User**

✅ User is now assigned to the branch!

---

### Step 3: Login to Branch Dashboard

1. **Logout** from current session (if logged in)
2. Go to **Login** page
3. Enter the **branch user's credentials**:
   - Email/Username
   - Password
4. Click **Login**

✅ **You will be automatically redirected to Branch Dashboard!**

---

## 🎨 What You'll See

### After Login:
- **Automatic Redirect**: You'll be taken to Branch Dashboard
- **Sidebar Menu**: Shows "Branch Dashboard" menu item
- **Dashboard Content**: 
  - Your branch name at the top
  - Statistics for your branch only
  - Recent members in your branch
  - Recent leaders in your branch
  - Quick actions to register members, assign leaders, etc.

### Branch Dashboard Features:
- ✅ View only your branch members
- ✅ Register new members (auto-assigned to your branch)
- ✅ Assign leaders (to your branch)
- ✅ View branch statistics
- ✅ Manage branch data

---

## 🔄 Quick Setup Script

If you want to quickly set up a test branch user, run:

```bash
php artisan tinker
```

Then run:
```php
// Get or create a branch
$branch = \App\Models\Campus::where('is_main_campus', false)->first();
if (!$branch) {
    $mainCampus = \App\Models\Campus::where('is_main_campus', true)->first();
    $branch = \App\Models\Campus::create([
        'name' => 'Test Branch',
        'code' => 'SUB-001',
        'parent_id' => $mainCampus->id,
        'is_main_campus' => false,
        'is_active' => true,
    ]);
}

// Create or update a user for this branch
$user = \App\Models\User::firstOrCreate(
    ['email' => 'branch@test.com'],
    [
        'name' => 'Branch Pastor',
        'password' => \Hash::make('password123'),
        'role' => 'pastor',
    ]
);
$user->campus_id = $branch->id;
$user->save();

echo "Branch User Created!\n";
echo "Email: branch@test.com\n";
echo "Password: password123\n";
echo "Branch: {$branch->name}\n";
```

---

## ✅ Verification Checklist

After setup, verify:

- [ ] Branch exists in Campuses list
- [ ] User has `campus_id` set to branch ID
- [ ] User can login
- [ ] User is redirected to Branch Dashboard
- [ ] Sidebar shows "Branch Dashboard" menu
- [ ] Dashboard shows branch name
- [ ] Can register members (goes to branch automatically)

---

## 🚨 Troubleshooting

### Issue: Still redirecting to wrong dashboard
**Fix:**
1. Check user's `campus_id` is set correctly
2. Clear cache: `php artisan cache:clear`
3. Logout and login again

### Issue: "You must be assigned to a branch" error
**Fix:**
1. Assign `campus_id` to the user
2. Make sure branch exists and is active

### Issue: Can't see Branch Dashboard in sidebar
**Fix:**
1. Check user's campus assignment
2. Verify `isBranchUser()` method works
3. Clear view cache: `php artisan view:clear`

---

## 📝 Example Workflow

1. **Usharika Admin** creates branch "Longuo B"
2. **Usharika Admin** creates user "Pastor John" and assigns to "Longuo B"
3. **Pastor John** logs in → Automatically goes to Branch Dashboard
4. **Pastor John** sees only "Longuo B" data
5. **Pastor John** registers members → All go to "Longuo B" automatically
6. **Usharika Admin** logs in → Sees Usharika Dashboard with all branches

---

## 🎉 Summary

**To login to branch dashboard:**
1. ✅ Create branch
2. ✅ Assign user to branch
3. ✅ Login with branch user credentials
4. ✅ Automatically redirected to Branch Dashboard!

**That's it!** The system automatically detects branch users and redirects them to the correct dashboard.














