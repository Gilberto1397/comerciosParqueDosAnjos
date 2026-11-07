<?php
declare(strict_types=1);
namespace App\Services;

use App\Entities\Comercio;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
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

    /**
     * Retorna o comércio apenas se pertencer ao usuário.
     * @throws NotFoundException
     * @throws ForbiddenException
     */
    public function buscarEditavel(int $id, string $usuario): Comercio
    {
        $comercio = $this->repo->find($id);
        if ($comercio === null) {
            throw new NotFoundException();
        }
        if (!$comercio->pertenceA($usuario)) {
            throw new ForbiddenException();
        }
        return $comercio;
    }

    /** @throws ValidationException */
    public function cadastrar(string $usuario, array $data, ?array $file): int
    {
        [$campos, $errors] = $this->validar($data);

        $imagem = '';
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

        return $this->repo->create(new Comercio(
            null, $campos['titulo'], $campos['descricao'], $campos['dias'],
            $campos['abertura'], $campos['fechamento'], $imagem, $usuario
        ));
    }

    /**
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws ValidationException
     */
    public function atualizar(int $id, string $usuario, array $data, ?array $file): void
    {
        $comercio = $this->buscarEditavel($id, $usuario);
        [$campos, $errors] = $this->validar($data);

        // Na edição a imagem é opcional: só troca se um novo arquivo foi enviado
        $enviouImagem = ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $novaImagem = null;
        if (!$errors && $enviouImagem) {
            try {
                $novaImagem = $this->uploads->saveImage($file);
            } catch (ValidationException $e) {
                $errors += $e->errors();
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        $imagemAntiga = $comercio->imagemCapa;
        $comercio->titulo            = $campos['titulo'];
        $comercio->descricao         = $campos['descricao'];
        $comercio->diasAtendimento   = $campos['dias'];
        $comercio->horarioAbertura   = $campos['abertura'];
        $comercio->horarioFechamento = $campos['fechamento'];
        if ($novaImagem !== null) {
            $comercio->imagemCapa = $novaImagem;
        }

        if (!$this->repo->update($comercio)) {
            if ($novaImagem !== null) {
                $this->uploads->delete($novaImagem);
            }
            throw new ForbiddenException();
        }

        if ($novaImagem !== null) {
            $this->uploads->delete($imagemAntiga);
        }
    }

    /** @return array{0: array, 1: array} [campos normalizados, erros] */
    private function validar(array $data): array
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

        return [
            ['titulo' => $titulo, 'descricao' => $descricao, 'dias' => $dias,
             'abertura' => $abertura, 'fechamento' => $fechamento],
            $errors,
        ];
    }
}
