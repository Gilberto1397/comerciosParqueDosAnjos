<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);

// Autoload simples (PSR-4 like): App\Core\Router => app/Core/Router.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$config = require BASE_PATH . '/app/config.php';
App\Core\Config::load($config);
App\Core\Auth::start();

/** Helper de escape (XSS) usado nas views. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
