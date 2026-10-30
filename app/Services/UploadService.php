<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\Config;
use App\Exceptions\ValidationException;

final class UploadService
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /** Valida e move a imagem. Retorna o nome do arquivo salvo. */
    public function saveImage(?array $file): string
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            throw new ValidationException(['imagem_capa' => 'Envie uma imagem de capa.']);
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException(['imagem_capa' => 'Falha no upload da imagem.']);
        }
        if ($file['size'] > Config::get('upload_max')) {
            throw new ValidationException(['imagem_capa' => 'A imagem deve ter no máximo 2 MB.']);
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::ALLOWED[$mime]) || @getimagesize($file['tmp_name']) === false) {
            throw new ValidationException(['imagem_capa' => 'Formato inválido. Use JPG, PNG ou WEBP.']);
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::ALLOWED[$mime];
        $dest = rtrim(Config::get('upload_dir'), '/') . '/' . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new ValidationException(['imagem_capa' => 'Não foi possível salvar a imagem.']);
        }
        return $name;
    }
}
