# Google Translate Integration - Usage Guide

This guide explains how to use the Google Translate integration to translate text to Swahili in your Laravel application.

## Installation

The Google Translate library has been installed via Composer:
- **Package**: `stichoza/google-translate-php`
- **Service**: `App\Services\TranslationService`

## Configuration

You can configure the translation service by adding these variables to your `.env` file:

```env
# Translation Configuration
TRANSLATION_SOURCE_LANGUAGE=en
TRANSLATION_TARGET_LANGUAGE=sw
TRANSLATION_ENABLE_CACHE=true
TRANSLATION_CACHE_DURATION=43200
TRANSLATION_AUTO_DETECT=false
```

- `TRANSLATION_SOURCE_LANGUAGE`: Default source language (default: 'en')
- `TRANSLATION_TARGET_LANGUAGE`: Default target language (default: 'sw' for Swahili)
- `TRANSLATION_ENABLE_CACHE`: Enable caching of translations (default: true)
- `TRANSLATION_CACHE_DURATION`: Cache duration in minutes (default: 43200 = 30 days)
- `TRANSLATION_AUTO_DETECT`: Auto-detect source language (default: false)

## Usage

### Method 1: Using Helper Functions (Recommended)

The easiest way to use translation is through helper functions:

```php
// Translate to Swahili (default)
$swahiliText = translateToSwahili("Hello, welcome to our system");
// Returns: "Halo, karibu kwenye mfumo wetu"

// Translate with custom source/target languages
$translated = translate("Bonjour", "fr", "sw");
// Translates from French to Swahili

// Translate from Swahili to English
$englishText = translateFromSwahili("Hujambo");
// Returns: "Hello"
```

### Method 2: Using the Service Directly

You can also inject the `TranslationService` into your controllers or use dependency injection:

```php
use App\Services\TranslationService;

class YourController extends Controller
{
    public function index(TranslationService $translator)
    {
        $text = "Welcome to the dashboard";
        $swahiliText = $translator->toSwahili($text);
        
        // Or use the general translate method
        $translated = $translator->translate($text, 'en', 'sw');
        
        return view('your.view', compact('swahiliText'));
    }
}
```

### Method 3: Using Facade Pattern

You can also resolve the service from the container:

```php
use App\Services\TranslationService;

$translator = app(TranslationService::class);
$translated = $translator->toSwahili("Hello World");
```

## Examples in Controllers

### Example 1: Translating User Messages

```php
use App\Services\TranslationService;

class NotificationController extends Controller
{
    public function sendNotification(TranslationService $translator)
    {
        $message = "Your payment has been received";
        $swahiliMessage = $translator->toSwahili($message);
        
        // Send SMS or notification in Swahili
        // ...
    }
}
```

### Example 2: Translating Form Labels

```php
// In your Blade template
<label>{{ translateToSwahili('Full Name') }}</label>
<input type="text" name="name" placeholder="{{ translateToSwahili('Enter your name') }}">
```

### Example 3: Translating Arrays

```php
$labels = [
    'name' => 'Full Name',
    'email' => 'Email Address',
    'phone' => 'Phone Number'
];

$translator = app(TranslationService::class);
$translatedLabels = $translator->translateArray($labels);
// Returns: ['name' => 'Jina Kamili', 'email' => 'Anwani ya Barua Pepe', ...]
```

## Language Codes

Common language codes you can use:

- `en` - English
- `sw` - Swahili
- `fr` - French
- `es` - Spanish
- `de` - German
- `ar` - Arabic
- `zh` - Chinese
- `ja` - Japanese

For a full list, refer to [Google Translate supported languages](https://cloud.google.com/translate/docs/languages).

## Caching

Translations are automatically cached for 30 days by default to improve performance and reduce API calls. You can:

- Disable caching: Set `TRANSLATION_ENABLE_CACHE=false` in `.env`
- Clear cache: `$translator->clearCache()`

## Error Handling

The service is designed to be fault-tolerant:
- If translation fails, it returns the original text
- Errors are logged but don't break your application
- If the service is unavailable, it gracefully returns the original text

## Notes

1. **Rate Limits**: Google Translate has rate limits. Caching helps reduce API calls.
2. **Internet Required**: The service requires an active internet connection.
3. **Free Usage**: The library uses Google's free translation service, which may have usage limits.
4. **Accuracy**: Machine translation may not always be 100% accurate. Review important translations.

## Troubleshooting

If translations are not working:

1. Check your internet connection
2. Verify the service is available: `app(TranslationService::class)->isAvailable()`
3. Check Laravel logs for error messages
4. Ensure the library is installed: `composer show stichoza/google-translate-php`

## Advanced Usage

### Detect Language

```php
$translator = app(TranslationService::class);
$detected = $translator->detectLanguage("Hujambo");
// Returns: "sw"
```

### Translate Without Caching

```php
$translator = app(TranslationService::class);
$translated = $translator->translate("Hello", "en", "sw", false);
// The last parameter disables caching
```

## Integration with Existing Code

You can easily integrate translation into your existing SMS service or other services:

```php
use App\Services\TranslationService;

class SmsService
{
    protected $translator;
    
    public function __construct(TranslationService $translator)
    {
        $this->translator = $translator;
    }
    
    public function sendSwahiliSms($phone, $englishMessage)
    {
        $swahiliMessage = $this->translator->toSwahili($englishMessage);
        // Send SMS with Swahili message
    }
}
```










