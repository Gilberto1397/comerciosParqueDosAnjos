<?php
declare(strict_types=1);
namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $pdo = new PDO('sqlite:' . Config::get('db_path'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::migrate($pdo);
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec('CREATE TABLE IF NOT EXISTS comercios (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            titulo TEXT NOT NULL,
            descricao TEXT NOT NULL,
            dias_atendimento TEXT NOT NULL,
            horario_abertura TEXT NOT NULL,
            horario_fechamento TEXT NOT NULL,
            imagem_capa TEXT NOT NULL,
            usuario TEXT,
            criado_em TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )');

        // Migração: bancos criados antes da coluna "usuario" (dono do comércio)
        $cols = array_column($pdo->query('PRAGMA table_info(comercios)')->fetchAll(), 'name');
        if (!in_array('usuario', $cols, true)) {
            $pdo->exec('ALTER TABLE comercios ADD COLUMN usuario TEXT');
        }
    }
}
