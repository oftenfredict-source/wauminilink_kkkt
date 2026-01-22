<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\TranslationService;

class LanguageComposer
{
    protected $translator;

    public function __construct(TranslationService $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $locale = app()->getLocale();
        
        // Share translation helper with all views
        $view->with('translate', function($text) use ($locale) {
            if ($locale === 'sw' && $this->translator->isAvailable()) {
                return $this->translator->toSwahili($text);
            }
            return $text;
        });
        
        // Share current locale
        $view->with('currentLocale', $locale);
        $view->with('isSwahili', $locale === 'sw');
    }
}










