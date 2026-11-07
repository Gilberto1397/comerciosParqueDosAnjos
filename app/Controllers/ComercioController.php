<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Response;
use App\Core\View;
use App\Entities\Comercio;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\ComercioService;

final class ComercioController
{
    private ComercioService $service;

    public function __construct()
    {
        $this->service = new ComercioService();
    }

    /** Pública */
    public function index(): void
    {
        $page   = (int) ($_GET['page'] ?? 1);
        $result = $this->service->listar($page, (int) Config::get('per_page'));

        View::render('comercios/index', ['title' => 'Comércios locais'] + $result);
    }

    /** Pública */
    public function show(string $id): void
    {
        $comercio = $this->service->buscar($this->toId($id));

        if ($comercio === null) {
            $this->abort(404);
            return;
        }
        View::render('comercios/show', ['title' => $comercio->titulo, 'comercio' => $comercio]);
    }

    public function create(): void
    {
        View::render('comercios/form', ['title' => 'Novo comércio', 'errors' => [], 'old' => []]);
    }

    public function store(): void
    {
        try {
            $id = $this->service->cadastrar((string) Auth::user(), $_POST, $_FILES['imagem_capa'] ?? null);
            Response::flash('success', 'Comércio cadastrado com sucesso!');
            Response::redirect('/comercios/' . $id);
        } catch (ValidationException $e) {
            http_response_code(422);
            View::render('comercios/form', [
                'title'  => 'Novo comércio',
                'errors' => $e->errors(),
                'old'    => $_POST,
            ]);
        }
    }

    public function edit(string $id): void
    {
        try {
            $comercio = $this->service->buscarEditavel($this->toId($id), (string) Auth::user());
        } catch (NotFoundException) {
            $this->abort(404);
            return;
        } catch (ForbiddenException) {
            $this->abort(403);
            return;
        }

        View::render('comercios/form', [
            'title'    => 'Editar comércio',
            'comercio' => $comercio,
            'errors'   => [],
            'old'      => $this->oldFrom($comercio),
        ]);
    }

    public function update(string $id): void
    {
        $id = $this->toId($id);
        try {
            $this->service->atualizar($id, (string) Auth::user(), $_POST, $_FILES['imagem_capa'] ?? null);
            Response::flash('success', 'Comércio atualizado com sucesso!');
            Response::redirect('/comercios/' . $id);
        } catch (NotFoundException) {
            $this->abort(404);
        } catch (ForbiddenException) {
            $this->abort(403);
        } catch (ValidationException $e) {
            http_response_code(422);
            View::render('comercios/form', [
                'title'    => 'Editar comércio',
                'comercio' => $this->service->buscar($id),
                'errors'   => $e->errors(),
                'old'      => $_POST,
            ]);
        }
    }

    private function toId(string $id): int
    {
        return ctype_digit($id) ? (int) $id : 0;
    }

    private function abort(int $code): void
    {
        http_response_code($code);
        View::render('errors/' . $code, [
            'title' => $code === 403 ? 'Acesso negado' : 'Comércio não encontrado',
        ]);
    }

    private function oldFrom(Comercio $c): array
    {
        return [
            'titulo'             => $c->titulo,
            'descricao'          => $c->descricao,
            'dias'               => $c->diasAtendimento,
            'horario_abertura'   => $c->horarioAbertura,
            'horario_fechamento' => $c->horarioFechamento,
        ];
    }
}
