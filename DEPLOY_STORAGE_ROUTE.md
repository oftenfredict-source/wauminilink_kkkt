# Quick Deploy: Storage Route Fix

## What You Need to Do

I've updated your **local** `routes/web.php` file. You need to copy this change to your **live server**.

## The Code (Copy This)

Add this to the **END** of `/home/wauminilink/demo/routes/web.php`:

```php
// Serve storage files directly (bypasses symlink issues)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    $realPath = realpath($filePath);
    $storagePath = realpath(storage_path('app/public'));
    
    if (!$realPath || strpos($realPath, $storagePath) !== 0 || !file_exists($realPath) || !is_file($realPath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($realPath) ?: 'image/jpeg';
    return response()->file($realPath, ['Content-Type' => $mimeType]);
})->where('path', '.*');
```

## Easiest Way: Via cPanel

1. **Login to cPanel**
2. **File Manager** → Navigate to `/home/wauminilink/demo/routes/`
3. **Edit** `web.php`
4. **Scroll to the very end**
5. **Paste the code above**
6. **Save**

Then run in Terminal:
```bash
cd /home/wauminilink/demo
php artisan route:clear
```

## That's It!

After this, images will display. The route serves files directly from storage, bypassing the symlink issue completely.

