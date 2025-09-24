<?php
// Router amélioré pour servir les fichiers statiques et exécuter les fichiers PHP

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// Nettoyer l'URI et gérer les chemins
$uri = ltrim($uri, '/');
$filePath = __DIR__ . '/' . $uri;

// Si c'est un fichier PHP, l'exécuter
if ($uri !== '' && file_exists($filePath) && is_file($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    
    if ($ext === 'php') {
        // Exécuter le fichier PHP
        require_once $filePath;
        return;
    }
    
    // Pour les fichiers statiques (non-PHP)
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf'
    ];
    
    // Définir le type de contenu approprié
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        
        // Ajouter des en-têtes de cache pour les ressources statiques
        header('Cache-Control: public, max-age=3600');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');
        
        // Servir le fichier
        return readfile($filePath);
    }
}

// Si ce n'est pas un fichier existant, inclure index.php
require_once __DIR__ . '/index.php';
?>
