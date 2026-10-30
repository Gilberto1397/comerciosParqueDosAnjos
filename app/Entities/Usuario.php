<?php
declare(strict_types=1);
namespace App\Entities;

final class Usuario
{
    public function __construct(
        public string $username,
        public string $password, // texto puro ou hash (password_hash)
    ) {
    }

    public function verificaSenha(string $informada): bool
    {
        $isHash = str_starts_with($this->password, '$2y$') || str_starts_with($this->password, '$argon');
        return $isHash
            ? password_verify($informada, $this->password)
            : hash_equals($this->password, $informada);
    }
}
