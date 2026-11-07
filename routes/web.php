<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ComercioController;

/** @var App\Core\Router $router */

// Autenticação
$router->get('/login', [AuthController::class, 'showLogin'], false);
$router->post('/login', [AuthController::class, 'login'], false);
$router->post('/logout', [AuthController::class, 'logout']);

// Públicas: visitantes podem ver a lista e os dados dos comércios
$router->get('/', [ComercioController::class, 'index'], false);
$router->get('/comercios', [ComercioController::class, 'index'], false);

// Protegidas (ATENÇÃO: /comercios/novo deve vir antes de /comercios/{id})
$router->get('/comercios/novo', [ComercioController::class, 'create']);
$router->post('/comercios', [ComercioController::class, 'store']);

// Pública
$router->get('/comercios/{id}', [ComercioController::class, 'show'], false);

// Protegidas: somente o dono do comércio pode editar (verificado no service)
$router->get('/comercios/{id}/editar', [ComercioController::class, 'edit']);
$router->post('/comercios/{id}', [ComercioController::class, 'update']);
