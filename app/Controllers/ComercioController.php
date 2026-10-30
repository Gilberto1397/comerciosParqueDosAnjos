<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Config;
use App\Core\Response;
use App\Core\View;
use App\Exceptions\ValidationException;
use App\Services\ComercioService;

final class ComercioController
{
    private ComercioService $service;

    public function __construct()
    {
        $this->service = new ComercioService();
    }

    public function index(): void
    {
        $page   = (int) ($_GET['page'] ?? 1);
        $result = $this->service->listar($page, (int) Config::get('per_page'));

        View::render('comercios/index', ['title' => 'Comércios locais'] + $result);
    }

    public function show(string $id): void
    {
        $comercio = ctype_digit($id) ? $this->service->buscar((int) $id) : null;

        if ($comercio === null) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Comércio não encontrado']);
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
            $id = $this->service->cadastrar($_POST, $_FILES['imagem_capa'] ?? null);
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
}
