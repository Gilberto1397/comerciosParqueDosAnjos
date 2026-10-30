<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ComercioController;

/** @var App\Core\Router $router */

// Públicas (sem autenticação)
$router->get('/login', [AuthController::class, 'showLogin'], false);
$router->post('/login', [AuthController::class, 'login'], false);

// Protegidas
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/', [ComercioController::class, 'index']);
$router->get('/comercios/novo', [ComercioController::class, 'create']);
$router->post('/comercios', [ComercioController::class, 'store']);
$router->get('/comercios/{id}', [ComercioController::class, 'show']);
