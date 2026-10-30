<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Config;
use App\Entities\Usuario;

/** Lê usuários de um arquivo texto no formato "usuario:senha". */
final class UserRepository
{
    public function findByUsername(string $username): ?Usuario
    {
        $file = Config::get('users_file');
        if (!is_readable($file)) {
            return null;
        }

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            [$user, $pass] = array_pad(explode(':', $line, 2), 2, '');
            if (hash_equals($user, $username)) {
                return new Usuario($user, $pass);
            }
        }
        return null;
    }
}
