# Instructions: Update Profile Picture Display on All Pages

## Overview
Since images are now stored with the path `assets/images/members/profile-pictures/imagefilename.jpg` in the database, you need to update all view files to remove the `'storage/'` prefix from the `asset()` helper.

**Change from:**
```php
asset('storage/' . $member->profile_picture)
```

**Change to:**
```php
asset($member->profile_picture)
```

This is because the path already includes `assets/images/`, so `asset()` will correctly generate: `domain/demo/assets/images/members/profile-pictures/imagefilename.jpg`

---

## Files to Update (9 locations in 8 files)

### 1. `resources/views/members/dashboard.blade.php`

**Line:** ~14

**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile Picture" class="rounded-circle border border-primary border-2" style="width:48px; height:48px; object-fit:cover;">
```

**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" alt="Profile Picture" class="rounded-circle border border-primary border-2" style="width:48px; height:48px; object-fit:cover;">
```

---

### 2. `resources/views/members/settings.blade.php`

**Line:** ~55

**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}"
```

**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}"
```

**Note:** Check the full line - there may be more attributes after this. Only change the `src` part.

---

### 3. `resources/views/pastor/dashboard.blade.php`

**Line:** ~16

**FIND:**
```php
<img src="{{ asset('storage/' . $pastor->member->profile_picture) }}" alt="Pastor Profile" class="rounded-circle border border-primary border-2" style="width:48px; height:48px; object-fit:cover;">
```

**REPLACE WITH:**
```php
<img src="{{ asset($pastor->member->profile_picture) }}" alt="Pastor Profile" class="rounded-circle border border-primary border-2" style="width:48px; height:48px; object-fit:cover;">
```

---

### 4. `resources/views/dashboard.blade.php`

**Location 1 - Line:** ~15 (Secretary profile picture)

**FIND:**
```php
<img src="{{ asset('storage/' . $secretary->member->profile_picture) }}"
```

**REPLACE WITH:**
```php
<img src="{{ asset($secretary->member->profile_picture) }}"
```

**Location 2 - Line:** ~20 (User profile picture)

**FIND:**
```php
<img src="{{ asset('storage/' . $user->profile_picture) }}"
```

**REPLACE WITH:**
```php
<img src="{{ asset($user->profile_picture) }}"
```

---

### 5. `resources/views/members/partials/card-view.blade.php`

**Location 1 - Line:** ~24

**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
```

**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" alt="Profile" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
```

**Location 2 - Line:** ~156

**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
```

**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}" alt="Profile" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
```

---

### 6. `resources/views/leaders/bulk-identity-cards.blade.php`

**Line:** ~273

**FIND:**
```php
<img src="{{ asset('storage/' . $leader->member->profile_picture) }}"
```

**REPLACE WITH:**
```php
<img src="{{ asset($leader->member->profile_picture) }}"
```

**Note:** Check the full line - there may be more attributes after this. Only change the `src` part.

---

### 7. `resources/views/leaders/identity-card.blade.php`

**Line:** ~238

**FIND:**
```php
<img src="{{ asset('storage/' . $leader->member->profile_picture) }}"
```

**REPLACE WITH:**
```php
<img src="{{ asset($leader->member->profile_picture) }}"
```

**Note:** Check the full line - there may be more attributes after this. Only change the `src` part.

---

### 8. `resources/views/members/identity-card.blade.php`

**Line:** ~481

**FIND:**
```php
<img src="{{ asset('storage/' . $member->profile_picture) }}"
```

**REPLACE WITH:**
```php
<img src="{{ asset($member->profile_picture) }}"
```

**Note:** Check the full line - there may be more attributes after this. Only change the `src` part.

---

## Quick Find & Replace Method

If you want to do a bulk replacement, you can use your code editor's Find & Replace feature:

1. **Open Find & Replace** (usually Ctrl+H or Cmd+H)
2. **Enable Regular Expression mode** (if available)
3. **Find:** `asset\('storage/' \. \$[a-zA-Z_]+(->[a-zA-Z_]+)*->profile_picture\)`
4. **Replace:** `asset($1->profile_picture)`

**OR** simpler approach - Find and Replace in each file:

**Find:** `asset('storage/' .`
**Replace:** `asset(`

Then manually fix any that don't match the pattern (like `$pastor->member->profile_picture` or `$secretary->member->profile_picture`).

---

## Verification Steps

After making changes:

1. **Clear view cache:**
   ```bash
   php artisan view:clear
   ```

2. **Test each page:**
   - Member Dashboard - should show member's profile picture
   - Member Settings - should show member's profile picture
   - Main Dashboard - should show secretary/user profile picture
   - Pastor Dashboard - should show pastor's profile picture
   - Member Identity Cards - should show member's profile picture
   - Leader Identity Cards - should show leader's profile picture
   - Member Card View - should show member's profile picture

3. **Check browser console:**
   - Open browser Developer Tools (F12)
   - Go to Network tab
   - Refresh the page
   - Look for image requests
   - Verify URLs are: `domain/demo/assets/images/members/profile-pictures/imagefilename.jpg`
   - Should NOT be: `domain/demo/storage/assets/images/...` (this would be wrong)

4. **Check for broken images:**
   - If you see broken image icons, check:
     - The path in database is correct: `assets/images/members/profile-pictures/imagefilename.jpg`
     - The file actually exists in `public/assets/images/members/profile-pictures/`
     - File permissions are correct (755 for directories, 644 for files)

---

## Backward Compatibility (If Needed)

If you have some old records in the database that still use the old storage path format (`members/profile-pictures/...`), you can add a helper method to handle both:

**File:** `app/Models/Member.php`

Add this method:

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
    
    // If it's the old storage format, convert it
    if (strpos($this->profile_picture, 'members/profile-pictures/') === 0) {
        return asset('assets/images/' . $this->profile_picture);
    }
    
    // Otherwise, assume it's from old storage location
    return asset('storage/' . $this->profile_picture);
}
```

Then in views, use:
```php
<img src="{{ $member->profile_picture_url }}" ...>
```

This will handle both old and new path formats automatically.

---

## Summary

- **Total files to update:** 8 files
- **Total locations:** 9 image display locations
- **Change:** Remove `'storage/' .` from `asset('storage/' . $variable->profile_picture)`
- **Result:** `asset($variable->profile_picture)` will correctly generate the full URL

After updating, all profile pictures should display correctly at:
`https://www.wauminilink.co.tz/demo/assets/images/members/profile-pictures/imagefilename.jpg`



