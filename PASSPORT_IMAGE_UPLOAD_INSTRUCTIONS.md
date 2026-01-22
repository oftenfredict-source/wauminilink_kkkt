# Instructions: Update Passport Image Upload Path for Live Server

## Overview
Your live server stores images in `public/assets/images` instead of Laravel's default `storage/app/public`. Follow these instructions to update the code.

---

## Step 1: Update MemberController.php

**File:** `app/Http/Controllers/MemberController.php`

### Location 1: Member Profile Picture Upload (around line 204)

**FIND THIS CODE:**
```php
$profilePicturePath = $file->store('members/profile-pictures', 'public');
```

**REPLACE WITH:**
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

**VERIFY:** After upload, check the database - the `profile_picture` column should contain: `assets/images/members/profile-pictures/imagefilename.jpg`

### Location 2: Spouse Profile Picture Upload (around line 237)

**FIND THIS CODE:**
```php
$spouseProfilePicturePath = $file->store('members/profile-pictures', 'public');
```

**REPLACE WITH:**
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

**VERIFY:** After upload, check the database - the `spouse_profile_picture` column should contain: `assets/images/members/profile-pictures/imagefilename.jpg`

---

## Step 2: Update MemberDashboardController.php

**File:** `app/Http/Controllers/MemberDashboardController.php`

### Location: Update Profile Picture (around line 525-530)

**FIND THIS CODE:**
```php
// Delete old profile picture if exists
if ($member->profile_picture && Storage::disk('public')->exists($member->profile_picture)) {
    Storage::disk('public')->delete($member->profile_picture);
}

// Store new profile picture
$profilePicturePath = $file->store('members/profile-pictures', 'public');
```

**REPLACE WITH:**
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
$profilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
```

---

## Step 3: Update All View Files to Display Images

Update all view files that display profile pictures. Change from `asset('storage/...')` to `asset(...)` since the path already includes `assets/images/`.

### Files to Update:

#### 1. `resources/views/members/dashboard.blade.php` (line 14)
**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" ...>
```

#### 2. `resources/views/members/settings.blade.php` (line 55)
**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" ...>
```

#### 3. `resources/views/pastor/dashboard.blade.php` (line 16)
**FIND:**
```php
<img src="{{ asset('storage/' . $pastor->member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($pastor->member->profile_picture) }}" ...>
```

#### 4. `resources/views/dashboard.blade.php` (lines 15 and 20)
**FIND:**
```php
<img src="{{ asset('storage/' . $secretary->member->profile_picture) }}" ...>
<img src="{{ asset('storage/' . $user->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($secretary->member->profile_picture) }}" ...>
<img src="{{ asset($user->profile_picture) }}" ...>
```

#### 5. `resources/views/members/partials/card-view.blade.php` (lines 24 and 156)
**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" ...>
```

#### 6. `resources/views/leaders/bulk-identity-cards.blade.php` (line 273)
**FIND:**
```php
<img src="{{ asset('storage/' . $leader->member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($leader->member->profile_picture) }}" ...>
```

#### 7. `resources/views/leaders/identity-card.blade.php` (line 238)
**FIND:**
```php
<img src="{{ asset('storage/' . $leader->member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($leader->member->profile_picture) }}" ...>
```

#### 8. `resources/views/members/identity-card.blade.php` (line 481)
**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" ...>
```
**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" ...>
```

---

## Step 4: Handle Backward Compatibility (Optional but Recommended)

If you have existing images in the old storage location, you can add a helper method to check both locations.

**File:** `app/Models/Member.php`

Add this method to handle both old and new paths:

```php
public function getProfilePictureUrlAttribute()
{
    if (!$this->profile_picture) {
        return null;
    }
    
    // If path already starts with 'assets/images', use it directly
    if (strpos($this->profile_picture, 'assets/images/') === 0) {
        return asset($this->profile_picture);
    }
    
    // Otherwise, assume it's from old storage location
    return asset('storage/' . $this->profile_picture);
}
```

Then in views, you can use:
```php
<img src="{{ $member->profile_picture_url }}" ...>
```

---

## Step 5: Create Directory on Live Server

Make sure the directory exists on your live server:

```bash
mkdir -p public/assets/images/members/profile-pictures
chmod -R 755 public/assets/images
```

Or create it via cPanel File Manager:
- Navigate to `public/assets/images/`
- Create folder `members`
- Inside `members`, create folder `profile-pictures`
- Set permissions to 755

---

## Step 6: Clear Cache After Changes

After making all changes, clear Laravel cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Summary of Changes

1. **2 locations** in `MemberController.php` - Change upload path
2. **1 location** in `MemberDashboardController.php` - Change upload path and deletion logic
3. **8 view files** - Change image display paths from `asset('storage/...')` to `asset(...)`
4. **Create directory** `public/assets/images/members/profile-pictures/` on live server
5. **Clear cache** after deployment

---

## Testing

After deployment:
1. Upload a new member with passport photo
2. Check that image is saved in `public/assets/images/members/profile-pictures/`
3. Verify image displays correctly in member views
4. Test updating an existing member's photo

---

## ⚠️ IMPORTANT: Path Verification

### What Should Be Stored in Database:
The `profile_picture` column in the `members` table should contain:
```
assets/images/members/profile-pictures/imagefilename.jpg
```

### What You'll See in Browser Console:
When using `asset($member->profile_picture)`, the full URL should be:
```
https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/imagefilename.jpg
```

### ❌ WRONG Path (Missing `assets/images/`):
If you see in console:
```
domain/demo/members/profile-pictures/imagefilename
```
This means the path stored in database is: `members/profile-pictures/imagefilename` (WRONG!)

### ✅ CORRECT Path:
Should be:
```
domain/demo/assets/images/members/profile-pictures/imagefilename
```
This means the path stored in database is: `assets/images/members/profile-pictures/imagefilename` (CORRECT!)

### How to Fix If Path is Wrong:
If you already uploaded images with the wrong path, you need to:
1. Update the database records to add `assets/images/` prefix
2. Or run a migration/script to fix existing records

**SQL Query to Fix Existing Records:**
```sql
UPDATE members 
SET profile_picture = CONCAT('assets/images/', profile_picture)
WHERE profile_picture IS NOT NULL 
AND profile_picture NOT LIKE 'assets/images/%'
AND profile_picture LIKE 'members/profile-pictures/%';
```

