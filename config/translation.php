<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Translate service integration.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Source Language
    |--------------------------------------------------------------------------
    |
    | The default source language code for translations.
    | Common codes: 'en' (English), 'sw' (Swahili), 'fr' (French), etc.
    |
    */

    'source_language' => env('TRANSLATION_SOURCE_LANGUAGE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Default Target Language
    |--------------------------------------------------------------------------
    |
    | The default target language code for translations.
    | Set to 'sw' for Swahili.
    |
    */

    'target_language' => env('TRANSLATION_TARGET_LANGUAGE', 'sw'),

    /*
    |--------------------------------------------------------------------------
    | Enable Caching
    |--------------------------------------------------------------------------
    |
    | Whether to cache translations to reduce API calls and improve performance.
    |
    */

    'enable_cache' => env('TRANSLATION_ENABLE_CACHE', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | How long to cache translations (in minutes).
    |
    */

    'cache_duration' => env('TRANSLATION_CACHE_DURATION', 43200), // 30 days in minutes

    /*
    |--------------------------------------------------------------------------
    | Auto-detect Language
    |--------------------------------------------------------------------------
    |
    | Whether to automatically detect the source language before translating.
    |
    */

    'auto_detect' => env('TRANSLATION_AUTO_DETECT', false),
];










