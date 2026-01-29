<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(string $locale)
    {
        // Validate locale
        if (!in_array($locale, ['id', 'en'])) {
            $locale = 'id';
        }

        // Store locale in session
        Session::put('locale', $locale);
        App::setLocale($locale);

        return redirect()->back()->with('success', __('messages.settings_page.language') . ' → ' . strtoupper($locale));
    }
}
