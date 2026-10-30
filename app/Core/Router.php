<?php
declare(strict_types=1);
namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string, regex:string, handler:array, auth:bool}> */
    private array $routes = [];

    public function get(string $path, array $handler, bool $auth = true): void
    {
        $this->add('GET', $path, $handler, $auth);
    }

    public function post(string $path, array $handler, bool $auth = true): void
    {
        $this->add('POST', $path, $handler, $auth);
    }

    private function add(string $method, string $path, array $handler, bool $auth): void
    {
        // /comercios/{id} => regex com grupo nomeado
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'method'  => $method,
            'regex'   => '#^' . $regex . '$#',
            'handler' => $handler,
            'auth'    => $auth,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || !preg_match($route['regex'], $path, $m)) {
                continue;
            }

            if ($route['auth'] && !Auth::check()) {
                Response::redirect('/login');
            }

            if ($method === 'POST' && !Auth::verifyCsrf($_POST['_csrf'] ?? null)) {
                http_response_code(419);
                echo 'Token CSRF inválido. Volte e recarregue a página.';
                return;
            }

            $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = $route['handler'];
            (new $class())->$action(...array_values($params));
            return;
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Página não encontrada']);
    }
}
