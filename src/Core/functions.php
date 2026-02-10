<?php

/**
 * Génère une URL absolue en tenant compte de BASE_URL
 * @param string $path Chemin relatif (ex: '/login')
 * @return string URL absolue (ex: 'https://.../public/public/login')
 */
function url($path = '')
{
    // Si l'URL est déjà absolue, on la retourne telle quelle
    if (strpos($path, 'http') === 0) {
        return $path;
    }

    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
    $path = '/' . ltrim($path, '/');

    // Si le chemin est juste '/', on retourne la base (avec ou sans slash final selon la pref, ici sans pour être propre puis ajout si besoin)
    if ($path === '/') {
        return $base . '/';
    }

    return $base . $path;
}

/**
 * Fonction de debug améliorée ("Dump and Die")
 */
if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px; z-index: 9999; position: relative;">';
        var_dump($data);
        echo '</pre>';
        die();
    }
}
