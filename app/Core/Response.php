<?php
declare(strict_types=1);
namespace App\Core;

final class Response
{
    public static function redirect(string $to): never
    {
        header('Location: ' . $to);
        exit;
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}
