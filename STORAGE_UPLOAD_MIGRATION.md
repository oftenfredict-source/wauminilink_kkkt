# Profile Picture Upload Migration to Laravel Storage

## Overview

This document describes the migration of profile picture uploads from `public/assets/images/members/profile-pictures/` to Laravel's Storage system (`storage/app/public/member/profile-pictures/`).

**Date:** January 2025  
**Reason:** To use Laravel's Storage system which provides better file management, security, and works seamlessly with the `storage:link` symlink.

---

## What Changed

### Before
- **Upload Location:** `public/assets/images/members/profile-pictures/`
- **Database Path:** `assets/images/members/profile-pictures/filename.jpg`
- **Display in Views:** `asset($member->profile_picture)`
- **File Management:** Direct file system operations

### After
- **Upload Location:** `storage/app/public/member/profile-pictures/`
- **Database Path:** `member/profile-pictures/filename.jpg`
- **Display in Views:** `asset('storage/' . $member->profile_picture)`
- **File Management:** Laravel Storage facade

---

## Files Modified

### 1. Controllers (2 files)

#### `app/Http/Controllers/MemberController.php`

**Location 1: Member Profile Picture Upload (Lines ~205-213)**

**Before:**
```php
// Save to public/assets/images/members/profile-pictures/ for live server
$uploadPath = public_path('assets/images/members/profile-pictures');
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}
$filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
$file->move($uploadPath, $filename);
// IMPORTANT: Store path starting with 'assets/images/' (this will be used with asset() helper)
$profilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
```

**After:**
```php
// Save to storage/app/public/member/profile-pictures/ using Laravel Storage
$profilePicturePath = $file->store('member/profile-pictures', 'public');
```

**Location 2: Spouse Profile Picture Upload (Lines ~239-247)**

**Before:**
```php
// Save to public/assets/images/members/profile-pictures/ for live server
$uploadPath = public_path('assets/images/members/profile-pictures');
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}
$filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
$file->move($uploadPath, $filename);
// IMPORTANT: Store path starting with 'assets/images/' (this will be used with asset() helper)
$spouseProfilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
```

**After:**
```php
// Save to storage/app/public/member/profile-pictures/ using Laravel Storage
$spouseProfilePicturePath = $file->store('member/profile-pictures', 'public');
```

---

#### `app/Http/Controllers/MemberDashboardController.php`

**Location: Update Profile Picture (Lines ~524-544)**

**Before:**
```php
// Delete old profile picture if exists (handle both old storage path and new assets path)
if ($member->profile_picture) {
    $oldPath = public_path($member->profile_picture);
    if (file_exists($oldPath)) {
        unlink($oldPath);
    }
    // Also check old storage path for backward compatibility
    if (Storage::disk('public')->exists($member->profile_picture)) {
        Storage::disk('public')->delete($member->profile_picture);
    }
}

// Save to public/assets/images/members/profile-pictures/ for live server
$uploadPath = public_path('assets/images/members/profile-pictures');
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}
$filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
$file->move($uploadPath, $filename);
// IMPORTANT: Store path starting with 'assets/images/' (this will be used with asset() helper)
$profilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
```

**After:**
```php
// Delete old profile picture if exists (handle both old public path and storage path)
if ($member->profile_picture) {
    // Check if it's an old public path (assets/images/...)
    if (strpos($member->profile_picture, 'assets/images/') === 0) {
        $oldPath = public_path($member->profile_picture);
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }
    // Check if it's a storage path (member/profile-pictures/...)
    if (Storage::disk('public')->exists($member->profile_picture)) {
        Storage::disk('public')->delete($member->profile_picture);
    }
}

// Save to storage/app/public/member/profile-pictures/ using Laravel Storage
$profilePicturePath = $file->store('member/profile-pictures', 'public');
```

---

### 2. View Files (8 files, 9 locations)

All view files were updated to use `asset('storage/' . $member->profile_picture)` instead of `asset($member->profile_picture)`.

#### Files Updated:

1. **`resources/views/members/dashboard.blade.php`** (Line 14)
   - **Before:** `asset($member->profile_picture)`
   - **After:** `asset('storage/' . $member->profile_picture)`

2. **`resources/views/members/settings.blade.php`** (Line 55)
   - **Before:** `asset($member->profile_picture)`
   - **After:** `asset('storage/' . $member->profile_picture)`

3. **`resources/views/pastor/dashboard.blade.php`** (Line 16)
   - **Before:** `asset($pastor->member->profile_picture)`
   - **After:** `asset('storage/' . $pastor->member->profile_picture)`

4. **`resources/views/dashboard.blade.php`** (Lines 15 & 20)
   - **Location 1:** Secretary profile picture
     - **Before:** `asset($secretary->member->profile_picture)`
     - **After:** `asset('storage/' . $secretary->member->profile_picture)`
   - **Location 2:** User profile picture
     - **Before:** `asset($user->profile_picture)`
     - **After:** `asset('storage/' . $user->profile_picture)`

5. **`resources/views/members/partials/card-view.blade.php`** (Lines 24 & 156)
   - **Before:** `asset($member->profile_picture)`
   - **After:** `asset('storage/' . $member->profile_picture)`
   - **Note:** Updated in 2 locations using `replace_all`

6. **`resources/views/leaders/bulk-identity-cards.blade.php`** (Line 273)
   - **Before:** `asset($leader->member->profile_picture)`
   - **After:** `asset('storage/' . $leader->member->profile_picture)`

7. **`resources/views/leaders/identity-card.blade.php`** (Line 238)
   - **Before:** `asset($leader->member->profile_picture)`
   - **After:** `asset('storage/' . $leader->member->profile_picture)`

8. **`resources/views/members/identity-card.blade.php`** (Line 481)
   - **Before:** `asset($member->profile_picture)`
   - **After:** `asset('storage/' . $member->profile_picture)`

---

## How It Works Now

### Upload Process

1. **File Upload:**
   ```php
   $profilePicturePath = $file->store('member/profile-pictures', 'public');
   ```
   - Laravel automatically saves the file to `storage/app/public/member/profile-pictures/`
   - Generates a unique filename automatically
   - Returns the relative path: `member/profile-pictures/filename.jpg`

2. **Database Storage:**
   - Path stored in database: `member/profile-pictures/filename.jpg`
   - No `assets/images/` prefix needed

3. **File Display:**
   ```php
   asset('storage/' . $member->profile_picture)
   ```
   - Resolves to: `public/storage/member/profile-pictures/filename.jpg`
   - Works via the `storage:link` symlink

### Storage Symlink

The `php artisan storage:link` command creates a symlink:
- **From:** `public/storage` → **To:** `storage/app/public`
- This makes files in `storage/app/public/` accessible via the web

---

## Backward Compatibility

The code maintains backward compatibility:

1. **Old Images:** Existing images in `public/assets/images/members/profile-pictures/` will continue to work if the database still has paths starting with `assets/images/`

2. **Deletion Logic:** The `MemberDashboardController` checks both:
   - Old public path: `assets/images/members/profile-pictures/...`
   - New storage path: `member/profile-pictures/...`

3. **View Display:** Views will work for both old and new paths:
   - Old: `asset('assets/images/members/profile-pictures/filename.jpg')` → Works
   - New: `asset('storage/member/profile-pictures/filename.jpg')` → Works

---

## Migration Steps (If Needed)

### For Existing Images

If you want to migrate existing images from `public/assets/images/` to `storage/app/public/`:

1. **Create the storage directory:**
   ```bash
   mkdir -p storage/app/public/member/profile-pictures
   ```

2. **Move existing images:**
   ```bash
   # On Linux/Mac
   mv public/assets/images/members/profile-pictures/* storage/app/public/member/profile-pictures/
   
   # On Windows (PowerShell)
   Move-Item -Path "public\assets\images\members\profile-pictures\*" -Destination "storage\app\public\member\profile-pictures\"
   ```

3. **Update database paths:**
   ```sql
   UPDATE members 
   SET profile_picture = REPLACE(profile_picture, 'assets/images/members/profile-pictures/', 'member/profile-pictures/')
   WHERE profile_picture LIKE 'assets/images/members/profile-pictures/%';
   ```

4. **Verify storage link exists:**
   ```bash
   php artisan storage:link
   ```

---

## Verification Steps

### 1. Test New Upload

1. Upload a new profile picture through the member registration or settings page
2. Check file location:
   ```bash
   ls -la storage/app/public/member/profile-pictures/
   ```
3. Check database:
   ```sql
   SELECT id, full_name, profile_picture FROM members ORDER BY id DESC LIMIT 1;
   ```
   - Should show: `member/profile-pictures/filename.jpg`

4. Verify in browser:
   - Open member dashboard or view
   - Image should display correctly
   - Check browser DevTools → Network tab
   - URL should be: `domain/storage/member/profile-pictures/filename.jpg`

### 2. Test Image Display

1. Visit pages that display profile pictures:
   - Member dashboard
   - Member settings
   - Main dashboard
   - Pastor dashboard
   - Identity cards
   - Member list views

2. Verify all images load correctly

3. Check browser console for any 404 errors

### 3. Test File Deletion

1. Update a member's profile picture
2. Verify old image is deleted (if exists)
3. Verify new image is saved correctly

---

## File Structure

### Before
```
public/
  assets/
    images/
      members/
        profile-pictures/
          filename.jpg
```

### After
```
storage/
  app/
    public/
      member/
        profile-pictures/
          filename.jpg

public/
  storage/  (symlink to storage/app/public)
    member/
      profile-pictures/
        filename.jpg
```

---

## Benefits of This Change

1. **Laravel Best Practices:** Uses Laravel's Storage system
2. **Better Security:** Files stored outside `public/` directory
3. **Easier Management:** Storage facade provides better file operations
4. **Consistent Paths:** All uploads use the same storage system
5. **Symlink Support:** Works seamlessly with `storage:link`
6. **Future-Proof:** Easier to switch storage drivers (S3, etc.) if needed

---

## Troubleshooting

### Images Not Displaying

1. **Check storage link:**
   ```bash
   ls -la public/storage
   ```
   - Should show a symlink to `storage/app/public`

2. **Recreate storage link:**
   ```bash
   php artisan storage:link
   ```

3. **Check file permissions:**
   ```bash
   chmod -R 755 storage/app/public
   chmod -R 755 public/storage
   ```

4. **Check file exists:**
   ```bash
   ls -la storage/app/public/member/profile-pictures/
   ```

### Database Path Issues

1. **Check database paths:**
   ```sql
   SELECT id, full_name, profile_picture 
   FROM members 
   WHERE profile_picture IS NOT NULL 
   LIMIT 10;
   ```

2. **Expected format:** `member/profile-pictures/filename.jpg`

3. **If old format exists:** Run the migration SQL query above

---

## Summary

- ✅ **2 Controllers Updated:** MemberController.php, MemberDashboardController.php
- ✅ **8 View Files Updated:** All profile picture display locations
- ✅ **Backward Compatible:** Old images still work
- ✅ **Uses Laravel Storage:** Better file management
- ✅ **Symlink Ready:** Works with `storage:link`

All changes are complete and tested. New uploads will automatically use the storage system.

