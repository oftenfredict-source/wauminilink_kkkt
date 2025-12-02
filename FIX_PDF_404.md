# Fix PDF 404 Error on Hosted Server

## Issue
PDF routes returning 404 "Not Found" error on hosted server.

## Solutions Applied

### 1. Route Fix
- Added dynamic route `/reports/export/{format}` that accepts `pdf` or `excel` as parameter
- This matches the URL structure used in views: `/reports/export/pdf`

### 2. Server Configuration Steps

#### Step 1: Clear Route Cache
Run these commands on your server via SSH or hosting control panel:

```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### Step 2: Rebuild Route Cache (Production)
```bash
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

#### Step 3: Verify .htaccess File
Ensure `public/.htaccess` exists and contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Step 4: Check Document Root
Ensure your hosting document root points to the `public` directory, not the project root.

#### Step 5: Verify File Permissions
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage
```

#### Step 6: Test Route
After clearing cache, test the route:
```
https://yourdomain.com/reports/export/pdf?report_type=income-vs-expenditure
```

## Alternative: Use Named Routes
If the dynamic route still doesn't work, update views to use named routes:

Instead of:
```javascript
const url = `/reports/export/${format}?report_type=...`;
```

Use:
```javascript
const url = `{{ route('reports.export.pdf') }}?report_type=...`;
// or for excel
const url = `{{ route('reports.export.excel') }}?report_type=...`;
```

## Debugging
If issue persists, check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Check if route is registered:
```bash
php artisan route:list | grep export
```

