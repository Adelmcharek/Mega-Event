<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $lang)
    {
        if (!in_array($lang, ['en', 'fr'])) {
            $lang = 'en';
        }
        session(['locale' => $lang]);
        return redirect()->back();
    }
}
