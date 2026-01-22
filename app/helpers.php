<?php

use App\Services\TranslationService;

if (!function_exists('translate')) {
    /**
     * Translate text to Swahili (or configured target language)
     *
     * @param string $text The text to translate
     * @param string|null $sourceLanguage Source language code (default: 'en')
     * @param string|null $targetLanguage Target language code (default: 'sw' for Swahili)
     * @return string Translated text
     */
    function translate(string $text, ?string $sourceLanguage = null, ?string $targetLanguage = null): string
    {
        $service = app(TranslationService::class);
        return $service->translate($text, $sourceLanguage, $targetLanguage);
    }
}

if (!function_exists('translateToSwahili')) {
    /**
     * Translate text to Swahili (convenience function)
     *
     * @param string $text The text to translate
     * @return string Translated text
     */
    function translateToSwahili(string $text): string
    {
        $service = app(TranslationService::class);
        return $service->toSwahili($text);
    }
}

if (!function_exists('translateFromSwahili')) {
    /**
     * Translate text from Swahili to English
     *
     * @param string $text The text to translate
     * @return string Translated text
     */
    function translateFromSwahili(string $text): string
    {
        $service = app(TranslationService::class);
        return $service->fromSwahili($text);
    }
}

if (!function_exists('t')) {
    /**
     * Smart translation function - uses Laravel translations if available,
     * otherwise uses Google Translate based on current locale
     *
     * @param string $key Translation key or text
     * @param array $replace Replacements for translation
     * @param string|null $locale Locale override
     * @return string Translated text
     */
    function t(string $key, array $replace = [], ?string $locale = null): string
    {
        $currentLocale = $locale ?? app()->getLocale();
        
        // First try Laravel translation files
        $translation = trans($key, $replace, $currentLocale);
        
        // If translation file doesn't exist or returns the key, use Google Translate
        if ($translation === $key || str_starts_with($translation, $key . '.')) {
            // Only translate if locale is Swahili and text is in English
            if ($currentLocale === 'sw') {
                $service = app(TranslationService::class);
                return $service->translate($key, 'en', 'sw');
            }
            return $key;
        }
        
        return $translation;
    }
}

if (!function_exists('autoTranslate')) {
    /**
     * Automatically translate text based on current locale
     * If locale is 'sw', translates to Swahili, otherwise returns original
     *
     * @param string $text Text to translate
     * @return string Translated or original text
     */
    function autoTranslate(string $text): string
    {
        $locale = app()->getLocale();
        
        // Only translate if locale is Swahili
        if ($locale === 'sw') {
            $service = app(TranslationService::class);
            return $service->toSwahili($text);
        }
        
        return $text;
    }
}

