<?php
declare(strict_types=1);
namespace App\Services;

use App\Entities\Comercio;
use App\Exceptions\ValidationException;
use App\Repositories\ComercioRepository;

final class ComercioService
{
    public function __construct(
        private ComercioRepository $repo = new ComercioRepository(),
        private UploadService $uploads = new UploadService(),
    ) {
    }

    /** @return array{items: Comercio[], page: int, pages: int, total: int} */
    public function listar(int $page, int $perPage): array
    {
        $total = $this->repo->count();
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);

        return [
            'items' => $this->repo->paginate($perPage, ($page - 1) * $perPage),
            'page'  => $page,
            'pages' => $pages,
            'total' => $total,
        ];
    }

    public function buscar(int $id): ?Comercio
    {
        return $this->repo->find($id);
    }

    /** @throws ValidationException */
    public function cadastrar(array $data, ?array $file): int
    {
        $titulo     = trim((string) ($data['titulo'] ?? ''));
        $descricao  = trim((string) ($data['descricao'] ?? ''));
        $dias       = array_values(array_intersect(array_keys(Comercio::DIAS), (array) ($data['dias'] ?? [])));
        $abertura   = (string) ($data['horario_abertura'] ?? '');
        $fechamento = (string) ($data['horario_fechamento'] ?? '');

        $errors = [];
        if (mb_strlen($titulo) < 3 || mb_strlen($titulo) > 100) {
            $errors['titulo'] = 'O título deve ter entre 3 e 100 caracteres.';
        }
        if (mb_strlen($descricao) < 10 || mb_strlen($descricao) > 2000) {
            $errors['descricao'] = 'A descrição deve ter entre 10 e 2000 caracteres.';
        }
        if (!$dias) {
            $errors['dias'] = 'Selecione ao menos um dia de atendimento.';
        }
        $timeRx = '/^([01]\d|2[0-3]):[0-5]\d$/';
        if (!preg_match($timeRx, $abertura) || !preg_match($timeRx, $fechamento)) {
            $errors['horario'] = 'Informe horários válidos.';
        } elseif ($abertura >= $fechamento) {
            $errors['horario'] = 'O horário de abertura deve ser anterior ao de fechamento.';
        }

        // Só salva a imagem se o resto estiver válido (evita arquivos órfãos)
        if (!$errors) {
            try {
                $imagem = $this->uploads->saveImage($file);
            } catch (ValidationException $e) {
                $errors += $e->errors();
            }
        } elseif (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $errors['imagem_capa'] = 'Envie uma imagem de capa.';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $this->repo->create(new Comercio(null, $titulo, $descricao, $dias, $abertura, $fechamento, $imagem));
    }
}
