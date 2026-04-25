<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

// Intergiciel pour définir la langue de l'application à chaque requête
// Récupère la langue depuis la session (fr ou en), avec 'en' comme valeur par défaut
// Appliqué globalement à toutes les requêtes web via bootstrap/app.php
// La langue est changée par LanguageController qui stocke le choix en session
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Lecture de la langue depuis la session — 'en' si aucune préférence enregistrée
        $locale = session('locale', 'en');

        // Application de la locale à Laravel pour les traductions (__()) et Carbon
        App::setLocale($locale);

        return $next($request);
    }
}
