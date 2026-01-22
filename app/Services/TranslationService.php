<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    protected $translator;
    protected $defaultTargetLanguage;
    protected $defaultSourceLanguage;

    public function __construct()
    {
        $this->defaultTargetLanguage = config('translation.target_language', 'sw');
        $this->defaultSourceLanguage = config('translation.source_language', 'en');
        
        try {
            $this->translator = new GoogleTranslate();
            $this->translator->setSource($this->defaultSourceLanguage);
            $this->translator->setTarget($this->defaultTargetLanguage);
        } catch (\Exception $e) {
            Log::warning('Google Translate initialization failed: ' . $e->getMessage());
            $this->translator = null;
        }
    }

    /**
     * Translate text to Swahili (or configured target language)
     *
     * @param string $text The text to translate
     * @param string|null $sourceLanguage Source language code (default: 'en')
     * @param string|null $targetLanguage Target language code (default: 'sw' for Swahili)
     * @param bool $useCache Whether to use cache for translations
     * @return string Translated text or original text if translation fails
     */
    public function translate(
        string $text,
        ?string $sourceLanguage = null,
        ?string $targetLanguage = null,
        bool $useCache = true
    ): string {
        // Return original text if translator is not available
        if (!$this->translator) {
            return $text;
        }

        // Skip translation if text is empty
        if (empty(trim($text))) {
            return $text;
        }

        // Use defaults if not specified
        $source = $sourceLanguage ?? $this->defaultSourceLanguage;
        $target = $targetLanguage ?? $this->defaultTargetLanguage;

        // Return original if source and target are the same
        if ($source === $target) {
            return $text;
        }

        // Check cache if enabled
        if ($useCache) {
            $cacheKey = $this->getCacheKey($text, $source, $target);
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        try {
            // Set source and target languages
            $this->translator->setSource($source);
            $this->translator->setTarget($target);

            // Translate the text
            $translated = $this->translator->translate($text);

            // Cache the result if enabled
            if ($useCache && $translated) {
                Cache::put($cacheKey, $translated, now()->addDays(30)); // Cache for 30 days
            }

            return $translated ?: $text;
        } catch (\Exception $e) {
            Log::warning('Translation failed: ' . $e->getMessage(), [
                'text' => substr($text, 0, 100),
                'source' => $source,
                'target' => $target
            ]);
            return $text; // Return original text on error
        }
    }

    /**
     * Translate text to Swahili (convenience method)
     *
     * @param string $text The text to translate
     * @return string Translated text
     */
    public function toSwahili(string $text): string
    {
        return $this->translate($text, 'en', 'sw');
    }

    /**
     * Translate text from Swahili to English
     *
     * @param string $text The text to translate
     * @return string Translated text
     */
    public function fromSwahili(string $text): string
    {
        return $this->translate($text, 'sw', 'en');
    }

    /**
     * Translate an array of texts
     *
     * @param array $texts Array of texts to translate
     * @param string|null $sourceLanguage Source language
     * @param string|null $targetLanguage Target language
     * @return array Array of translated texts
     */
    public function translateArray(
        array $texts,
        ?string $sourceLanguage = null,
        ?string $targetLanguage = null
    ): array {
        $translated = [];
        foreach ($texts as $key => $text) {
            $translated[$key] = $this->translate($text, $sourceLanguage, $targetLanguage);
        }
        return $translated;
    }

    /**
     * Detect the language of a given text
     *
     * @param string $text The text to detect language for
     * @return string|null Language code or null if detection fails
     */
    public function detectLanguage(string $text): ?string
    {
        if (!$this->translator || empty(trim($text))) {
            return null;
        }

        try {
            return $this->translator->detectLanguage($text);
        } catch (\Exception $e) {
            Log::warning('Language detection failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get cache key for translation
     *
     * @param string $text
     * @param string $source
     * @param string $target
     * @return string
     */
    protected function getCacheKey(string $text, string $source, string $target): string
    {
        return 'translation.' . md5($text . $source . $target);
    }

    /**
     * Clear translation cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        // Note: This clears all cache. For more granular control, you'd need to track cache keys
        Cache::flush();
    }

    /**
     * Check if translation service is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->translator !== null;
    }
}










