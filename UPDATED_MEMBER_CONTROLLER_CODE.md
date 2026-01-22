# Updated MemberController.php Code

## ✅ Location 1: Member Profile Picture Upload (Lines 204-212)

**REPLACED:**
```php
$profilePicturePath = $file->store('members/profile-pictures', 'public');
```

**WITH:**
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

---

## ✅ Location 2: Spouse Profile Picture Upload (Lines 235-243)

**REPLACED:**
```php
$spouseProfilePicturePath = $file->store('members/profile-pictures', 'public');
```

**WITH:**
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

---

## 📋 Complete Updated Sections

### Section 1: Member Profile Picture (Lines 183-213)

```php
// Handle profile picture upload
$profilePicturePath = null;
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
    
    // Save to public/assets/images/members/profile-pictures/ for live server
    $uploadPath = public_path('assets/images/members/profile-pictures');
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->move($uploadPath, $filename);
    // IMPORTANT: Store path starting with 'assets/images/' (this will be used with asset() helper)
    $profilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
}
```

### Section 2: Spouse Profile Picture (Lines 215-243)

```php
// Handle spouse profile picture upload
$spouseProfilePicturePath = null;
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
    
    // Save to public/assets/images/members/profile-pictures/ for live server
    $uploadPath = public_path('assets/images/members/profile-pictures');
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    $file->move($uploadPath, $filename);
    // IMPORTANT: Store path starting with 'assets/images/' (this will be used with asset() helper)
    $spouseProfilePicturePath = 'assets/images/members/profile-pictures/' . $filename;
}
```

---

## ✅ What This Does

1. **Creates directory** if it doesn't exist: `public/assets/images/members/profile-pictures/`
2. **Saves file** directly to that directory
3. **Stores path in database** as: `assets/images/members/profile-pictures/filename.jpg`
4. **Works with `asset()` helper** in views to generate: `domain/demo/assets/images/members/profile-pictures/filename.jpg`

---

## 🎯 Result

- ✅ Files saved to: `public/assets/images/members/profile-pictures/`
- ✅ Database stores: `assets/images/members/profile-pictures/filename.jpg`
- ✅ Views display: `asset($member->profile_picture)` generates correct URL
- ✅ No more 404 errors!

---

**The code is already updated in your file!** Just make sure to:
1. Fix existing database records (run SQL query)
2. Update view files (remove `'storage/'` from `asset()` calls)
3. Update `MemberDashboardController.php` if needed



