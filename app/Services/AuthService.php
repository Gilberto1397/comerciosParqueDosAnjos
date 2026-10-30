<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Auth;
use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private UserRepository $users = new UserRepository())
    {
    }

    public function attempt(string $username, string $password): bool
    {
        $user = $this->users->findByUsername(trim($username));

        if ($user === null || !$user->verificaSenha($password)) {
            return false;
        }

        Auth::login($user->username);
        return true;
    }
}
