<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Router;

use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;

/**
 * Nombre: Router
 * Descripción: Router HTTP minimalista por método y ruta exacta.
 * Parámetros de entrada: method, path, handler callable
 * Parámetros de salida: HttpResponse del handler registrado
 * Método de uso: $router->add('GET', '/status', $handler)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class Router
{
    /** @var array<string, callable(HttpRequest): HttpResponse> */
    private array $routes = [];

    /**
     * Nombre: add
     * Descripción: Registra una ruta exacta por método HTTP.
     * Parámetros de entrada: string $method, string $path, callable $handler
     * Parámetros de salida: void
     * Método de uso: $router->add('PUT', '/cars', $handler)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function add(string $method, string $path, callable $handler): void
    {
        $key = strtoupper($method) . ' ' . $path;
        $this->routes[$key] = $handler;
    }

    /**
     * Nombre: dispatch
     * Descripción: Ejecuta el handler asociado a la request y devuelve su respuesta.
     * Parámetros de entrada: HttpRequest $request
     * Parámetros de salida: HttpResponse|null
     * Método de uso: $response = $router->dispatch($request)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function dispatch(HttpRequest $request): ?HttpResponse
    {
        $key = $request->method() . ' ' . $request->path();
        $handler = $this->routes[$key] ?? null;

        if ($handler === null) {
            return null;
        }

        return $handler($request);
    }
}
