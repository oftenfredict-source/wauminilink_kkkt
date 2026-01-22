<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Switch application language
     *
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage(Request $request, string $locale)
    {
        // Validate locale
        $allowedLocales = ['en', 'sw'];
        
        if (!in_array($locale, $allowedLocales)) {
            return Redirect::back()->with('error', 'Invalid language selected.');
        }
        
        // Store locale in session
        Session::put('locale', $locale);
        
        // Set locale for current request
        App::setLocale($locale);
        
        // Redirect back with success message
        return Redirect::back()->with('success', 'Language changed successfully.');
    }
    
    /**
     * Get current locale
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentLocale()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'session_locale' => Session::get('locale', config('app.locale'))
        ]);
    }
}







