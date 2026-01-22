# Simple Step-by-Step Update Guide

## 🎯 What You Need to Do

You need to change **2 places** in `MemberController.php` where images are uploaded.

---

## 📍 Location 1: Member Profile Picture (Around Line 204)

### Step 1: Find This Code

Look for this line in `app/Http/Controllers/MemberController.php`:

```php
$profilePicturePath = $file->store('members/profile-pictures', 'public');
```

**It should be around line 204**, inside this block:
```php
if ($request->hasFile('profile_picture')) {
    $file = $request->file('profile_picture');
    
    // ... validation code ...
    
    $profilePicturePath = $file->store('members/profile-pictures', 'public');  // ← FIND THIS LINE
}
```

### Step 2: Replace It

**DELETE this line:**
```php
$profilePicturePath = $file->store('members/profile-pictures', 'public');
```

**REPLACE with this code:**
```php
// Save to public/assets/images/members/profile-pictures/ for live server
$uploadPath = public_path('assets/images/members/profile-pictures');
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}
$filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
$file->move($uploadPath, $filename);
$profilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
```

### Step 3: What It Should Look Like After

```php
if ($request->hasFile('profile_picture')) {
    $file = $request->file('profile_picture');
    
    // Validate file type
    if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid file type. Please upload a JPG or PNG image.',
            'errors' => ['profile_picture' => ['Invalid file type']]
        ], 422);
    }
    
    // Validate file size (2MB max)
    if ($file->getSize() > 2 * 1024 * 1024) {
        return response()->json([
            'success' => false,
            'message' => 'File too large. Please upload an image smaller than 2MB.',
            'errors' => ['profile_picture' => ['File too large']]
        ], 422);
    }
    
    // ✅ NEW CODE STARTS HERE
    // Save to public/assets/images/members/profile-pictures/ for live server
    $uploadPath = public_path('assets/images/members/profile-pictures');
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->move($uploadPath, $filename);
    $profilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
    // ✅ NEW CODE ENDS HERE
}
```

---

## 📍 Location 2: Spouse Profile Picture (Around Line 230)

### Step 1: Find This Code

Look for this line in the same file:

```php
$spouseProfilePicturePath = $file->store('members/profile-pictures', 'public');
```

**It should be around line 230**, inside this block:
```php
if ($request->hasFile('spouse_profile_picture')) {
    $file = $request->file('spouse_profile_picture');
    
    // ... validation code ...
    
    $spouseProfilePicturePath = $file->store('members/profile-pictures', 'public');  // ← FIND THIS LINE
}
```

### Step 2: Replace It

**DELETE this line:**
```php
$spouseProfilePicturePath = $file->store('members/profile-pictures', 'public');
```

**REPLACE with this code:**
```php
// Save to public/assets/images/members/profile-pictures/ for live server
$uploadPath = public_path('assets/images/members/profile-pictures');
if (!file_exists($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}
$filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
$file->move($uploadPath, $filename);
$spouseProfilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
```

### Step 3: What It Should Look Like After

```php
if ($request->hasFile('spouse_profile_picture')) {
    $file = $request->file('spouse_profile_picture');
    
    // Validate file type
    if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg'])) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid file type for spouse photo. Please upload a JPG or PNG image.',
            'errors' => ['spouse_profile_picture' => ['Invalid file type']]
        ], 422);
    }
    
    // Validate file size (2MB max)
    if ($file->getSize() > 2 * 1024 * 1024) {
        return response()->json([
            'success' => false,
            'message' => 'Spouse photo file too large. Please upload an image smaller than 2MB.',
            'errors' => ['spouse_profile_picture' => ['File too large']]
        ], 422);
    }
    
    // ✅ NEW CODE STARTS HERE
    // Save to public/assets/images/members/profile-pictures/ for live server
    $uploadPath = public_path('assets/images/members/profile-pictures');
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->move($uploadPath, $filename);
    $spouseProfilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
    // ✅ NEW CODE ENDS HERE
}
```

---

## 🔍 How to Find These Lines Quickly

### Method 1: Use Find Function

1. Open `app/Http/Controllers/MemberController.php`
2. Press `Ctrl+F` (Windows) or `Cmd+F` (Mac)
3. Search for: `store('members/profile-pictures', 'public')`
4. You should find **2 matches**
5. Replace each one with the new code above

### Method 2: Look for Line Numbers

1. Open `app/Http/Controllers/MemberController.php`
2. Go to line **204** - This is Location 1
3. Go to line **230** - This is Location 2
4. Look for the line that says: `$file->store('members/profile-pictures', 'public')`

---

## ✅ Checklist

After making changes:

- [ ] Found line 204 (Member profile picture)
- [ ] Replaced with new code
- [ ] Found line 230 (Spouse profile picture)
- [ ] Replaced with new code
- [ ] Saved the file
- [ ] Tested uploading a new member with photo

---

## 📝 Summary

**What you're changing:**
- **OLD:** `$file->store('members/profile-pictures', 'public')`
- **NEW:** Code that saves to `public/assets/images/members/profile-pictures/` and stores path as `assets/images/members/profile-pictures/filename.jpg`

**Where:**
- Location 1: Around line 204 (member photo)
- Location 2: Around line 230 (spouse photo)

**File:**
- `app/Http/Controllers/MemberController.php`

---

## 🆘 Still Confused?

If you can't find these lines:

1. Open `app/Http/Controllers/MemberController.php`
2. Search for: `profile_picture` (you'll find many matches)
3. Look for the ones that have: `$file->store(`
4. Those are the 2 lines you need to change

---

That's it! Just 2 simple replacements. 🎉



