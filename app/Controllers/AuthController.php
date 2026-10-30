<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Core\View;
use App\Services\AuthService;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            Response::redirect('/');
        }
        View::render('login', ['title' => 'Entrar', 'error' => null, 'username' => '']);
    }

    public function login(): void
    {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ((new AuthService())->attempt($username, $password)) {
            Response::redirect('/');
        }

        http_response_code(401);
        View::render('login', [
            'title'    => 'Entrar',
            'error'    => 'Usuário ou senha inválidos.',
            'username' => $username,
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect('/login');
    }
}
