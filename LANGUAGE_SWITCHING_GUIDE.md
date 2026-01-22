# Language Switching Guide - English to Swahili

This guide explains how to switch the application language from English (en) to Swahili (sw) and vice versa.

## Quick Start

### Method 1: Using the Language Switcher (Recommended)

1. **Look for the language icon** in the top navigation bar (next to notifications and user menu)
2. **Click on the language dropdown** (shows current language like "🇬🇧 EN" or "🇹🇿 SW")
3. **Select your preferred language**:
   - **English** (🇬🇧) - for English interface
   - **Kiswahili** (🇹🇿) - for Swahili interface
4. The page will reload with the selected language

### Method 2: Direct URL

You can also switch languages by visiting:
- **English**: `/language/en`
- **Swahili**: `/language/sw`

## How It Works

1. **Session Storage**: Your language preference is stored in your session
2. **Automatic Application**: The selected language is automatically applied to all pages
3. **Persistence**: Your language choice persists until you change it or log out

## Technical Details

### Files Created/Modified

1. **Middleware**: `app/Http/Middleware/SetLocale.php`
   - Automatically sets the application locale from session

2. **Controller**: `app/Http/Controllers/LanguageController.php`
   - Handles language switching requests

3. **Routes**: Added to `routes/web.php`
   - `GET /language/{locale}` - Switch language
   - `GET /api/language/current` - Get current language (API)

4. **Views**: 
   - `resources/views/partials/language-switcher.blade.php` - Language switcher component
   - Added to main layouts

5. **Configuration**: 
   - `bootstrap/app.php` - Middleware registration
   - `config/app.php` - Locale configuration

### Default Language

The default language is set in `config/app.php`:
```php
'locale' => env('APP_LOCALE', 'en'),
```

You can change the default by setting `APP_LOCALE=sw` in your `.env` file.

## Programmatic Usage

### In Controllers

```php
use Illuminate\Support\Facades\App;

// Get current locale
$currentLocale = App::getLocale(); // Returns 'en' or 'sw'

// Set locale programmatically
App::setLocale('sw');
```

### In Blade Templates

```blade
{{-- Check current locale --}}
@if(app()->getLocale() === 'sw')
    <p>Karibu!</p>
@else
    <p>Welcome!</p>
@endif

{{-- Display current locale --}}
Current Language: {{ strtoupper(app()->getLocale()) }}
```

### Using Translation Service

```php
use App\Services\TranslationService;

$translator = app(TranslationService::class);

// Translate text to Swahili
$swahiliText = $translator->toSwahili("Hello World");
// Returns: "Halo Dunia"

// Translate based on current locale
if (app()->getLocale() === 'sw') {
    $text = $translator->toSwahili($englishText);
} else {
    $text = $englishText;
}
```

## Integration with Views

To make your views support both languages, you can:

### Option 1: Conditional Display
```blade
@if(app()->getLocale() === 'sw')
    <h1>Karibu</h1>
@else
    <h1>Welcome</h1>
@endif
```

### Option 2: Use Translation Service
```blade
<h1>{{ translateToSwahili('Welcome') }}</h1>
```

### Option 3: Laravel Translation Files (Recommended for Production)

Create translation files in `lang/en/` and `lang/sw/`:

**lang/en/messages.php:**
```php
return [
    'welcome' => 'Welcome',
    'dashboard' => 'Dashboard',
];
```

**lang/sw/messages.php:**
```php
return [
    'welcome' => 'Karibu',
    'dashboard' => 'Dashibodi',
];
```

Then use in views:
```blade
<h1>{{ __('messages.welcome') }}</h1>
```

## Testing

1. **Test Language Switch**:
   - Visit any page
   - Click the language switcher
   - Verify the page reloads with new language

2. **Test Persistence**:
   - Switch to Swahili
   - Navigate to different pages
   - Verify language remains Swahili

3. **Test Session**:
   - Switch language
   - Check browser session storage
   - Log out and log back in
   - Language should reset to default

## Troubleshooting

### Language Not Switching

1. **Check Middleware**: Ensure `SetLocale` middleware is registered in `bootstrap/app.php`
2. **Check Session**: Verify sessions are working properly
3. **Clear Cache**: Run `php artisan config:clear` and `php artisan cache:clear`
4. **Check Routes**: Verify language routes are accessible

### Language Resets on Page Reload

- This usually means the session isn't persisting
- Check your session configuration in `config/session.php`
- Verify session driver is set correctly

### Translation Not Working

- Ensure Google Translate library is installed: `composer show stichoza/google-translate-php`
- Check internet connection (Google Translate requires internet)
- Review logs: `storage/logs/laravel.log`

## Advanced Configuration

### Add More Languages

1. Update `resources/views/partials/language-switcher.blade.php`:
```php
$locales = [
    'en' => ['name' => 'English', 'flag' => '🇬🇧'],
    'sw' => ['name' => 'Kiswahili', 'flag' => '🇹🇿'],
    'fr' => ['name' => 'Français', 'flag' => '🇫🇷'], // Add French
];
```

2. Update middleware validation in `app/Http/Middleware/SetLocale.php`:
```php
if (!in_array($locale, ['en', 'sw', 'fr'])) {
    $locale = config('app.locale', 'en');
}
```

### Store Language Preference in Database

You can extend the system to store language preference per user:

1. Add `preferred_language` column to `users` table
2. Update `LanguageController` to save to database
3. Load user preference on login

## Notes

- Language preference is stored in **session**, not database
- Language resets to default when user logs out
- The language switcher is visible to all authenticated users
- Translation service works independently and can translate any text to Swahili










