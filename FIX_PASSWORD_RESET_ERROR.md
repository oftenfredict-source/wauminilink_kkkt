# Fix Password Reset Error: "Unexpected token '<'" and "Route not found"

## Problem
When resetting a user's password from the admin page, two errors can occur:
1. "Request failed: Unexpected token '<'" - happens when JavaScript tries to parse HTML error pages (404, 419, etc.) as JSON
2. "Route not found" - happens when the route model binding fails or route cache is stale

## Solution
1. Updated the `resetPassword` function in two files to properly handle HTTP errors before parsing JSON responses
2. Changed the route from using route model binding `{member}` to using `{id}` parameter for more reliable routing
3. Updated the controller to manually find the member instead of relying on route model binding

---

## File 1: `resources/views/admin/users.blade.php`

**Location:** Around line 828-845

**Find this code:**
```javascript
preConfirm: () => {
    return fetch(`/admin/users/${userId}/reset-password`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to reset password');
        }
        return data;
    })
    .catch(error => {
        Swal.showValidationMessage(`Request failed: ${error.message}`);
    });
},
```

**Replace with:**
```javascript
preConfirm: () => {
    return fetch(`/admin/users/${userId}/reset-password`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        // Check if response is OK
        if (!response.ok) {
            // Try to parse error message from JSON response
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Server error: ${response.status} ${response.statusText}`);
            } else {
                // If not JSON, it's likely an HTML error page
                const text = await response.text();
                if (response.status === 404) {
                    throw new Error('Route not found. Please check if the route is properly configured.');
                } else if (response.status === 419) {
                    throw new Error('CSRF token mismatch. Please refresh the page and try again.');
                } else if (response.status === 403) {
                    throw new Error('You do not have permission to perform this action.');
                } else {
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                }
            }
        }
        
        // Parse JSON response
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to reset password');
        }
        return data;
    })
    .catch(error => {
        Swal.showValidationMessage(`Request failed: ${error.message}`);
    });
},
```

---

## File 2: `resources/views/members/view.blade.php`

**Location:** Around line 4094-4111

**Find this code:**
```javascript
preConfirm: () => {
    return fetch(`/members/${memberId}/reset-password`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to reset password');
        }
        return data;
    })
    .catch(error => {
        Swal.showValidationMessage(`Request failed: ${error.message}`);
    });
},
```

**Replace with:**
```javascript
preConfirm: () => {
    return fetch(`/members/${memberId}/reset-password`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        // Check if response is OK
        if (!response.ok) {
            // Try to parse error message from JSON response
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const errorData = await response.json();
                throw new Error(errorData.message || `Server error: ${response.status} ${response.statusText}`);
            } else {
                // If not JSON, it's likely an HTML error page
                const text = await response.text();
                if (response.status === 404) {
                    throw new Error('Route not found. Please check if the route is properly configured.');
                } else if (response.status === 419) {
                    throw new Error('CSRF token mismatch. Please refresh the page and try again.');
                } else if (response.status === 403) {
                    throw new Error('You do not have permission to perform this action.');
                } else {
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                }
            }
        }
        
        // Parse JSON response
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Failed to reset password');
        }
        return data;
    })
    .catch(error => {
        Swal.showValidationMessage(`Request failed: ${error.message}`);
    });
},
```

---

## What Changed?

1. **Added `Accept: application/json` header** - Ensures Laravel returns JSON responses
2. **Changed to `async/await` pattern** - Allows proper error handling
3. **Added response status check** - Checks `response.ok` before parsing
4. **Added content-type validation** - Verifies response is JSON before parsing
5. **Better error messages** - Provides specific messages for common HTTP errors:
   - 404: Route not found
   - 419: CSRF token mismatch
   - 403: Permission denied
   - Other: Generic server error with status code

---

## File 3: `routes/web.php` - Route Fix

**Location:** Around line 257-260

**Find this code:**
```php
// Password reset - Admin only
Route::post('/members/{member}/reset-password', [MemberController::class, 'resetPassword'])
    ->middleware('permission:members.edit')
    ->name('members.reset-password');
```

**Replace with:**
```php
// Password reset - Admin only
Route::post('/members/{id}/reset-password', [MemberController::class, 'resetPassword'])
    ->middleware('permission:members.edit')
    ->name('members.reset-password');
```

---

## File 4: `app/Http/Controllers/MemberController.php` - Controller Fix

**Location:** Around line 1164-1176

**Find this code:**
```php
public function resetPassword(Request $request, Member $member)
{
    // Check if user is admin
    if (!auth()->user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Only administrators can reset member passwords.'
        ], 403);
    }

    try {
        // Find user account for this member
        $user = User::where('member_id', $member->id)->first();
```

**Replace with:**
```php
public function resetPassword(Request $request, $id)
{
    // Check if user is admin
    if (!auth()->user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Only administrators can reset member passwords.'
        ], 403);
    }

    try {
        // Find the member
        $member = Member::findOrFail($id);
        
        // Find user account for this member
        $user = User::where('member_id', $member->id)->first();
```

---

## Additional Steps

After making the route and controller changes:
1. Clear the route cache: `php artisan route:clear`
2. Clear application cache: `php artisan cache:clear`
3. Clear browser cache (Ctrl+Shift+Delete)
4. Refresh the page

## Testing

After making these changes:
1. Clear browser cache (Ctrl+Shift+Delete)
2. Clear Laravel route cache: `php artisan route:clear`
3. Refresh the admin users page or members page
4. Try resetting a user's/member's password
5. You should now see proper error messages instead of "Unexpected token '<'" or "Route not found"

---

**Note:** These changes have already been applied to your codebase. If you need to verify or re-apply them, use the code above.

---

## ⚠️ IMPORTANT FOR HOSTED SERVERS

If you're deploying to a **hosted server** (not local), you **MUST** clear caches on the server after uploading files. See **`HOSTED_SERVER_FIX_INSTRUCTIONS.md`** for detailed steps.

**Quick steps for hosted servers:**
1. Upload all changed files
2. SSH into server and run: `php artisan route:clear && php artisan cache:clear && php artisan config:clear && php artisan view:clear`
3. Clear browser cache
4. Test again

Without clearing server caches, the route changes won't take effect!

