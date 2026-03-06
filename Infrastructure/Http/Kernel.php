<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Http;

use Cabify\Infrastructure\Http\Exception\HttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\JsonResponse;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Router\Router;
use JsonException;
use Throwable;

/**
 * Nombre: Kernel
 * Descripción: Orquesta el ciclo HTTP request-response y mapea errores a respuestas seguras.
 * Parámetros de entrada: Router y HttpRequest
 * Parámetros de salida: HttpResponse
 * Método de uso: $kernel->handle($request)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class Kernel
{
    private Router $router;

    /**
     * Nombre: __construct
     * Descripción: Inicializa el kernel con router de infraestructura.
     * Parámetros de entrada: Router $router
     * Parámetros de salida: Kernel
     * Método de uso: new Kernel($router)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Nombre: handle
     * Descripción: Procesa request y devuelve respuesta HTTP sin exponer detalles internos.
     * Parámetros de entrada: HttpRequest $request
     * Parámetros de salida: HttpResponse
     * Método de uso: $response = $kernel->handle($request)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function handle(HttpRequest $request): HttpResponse
    {
        try {
            $response = $this->router->dispatch($request);
            if ($response === null) {
                return JsonResponse::fromPayload(404, ['error' => 'Resource not found.']);
            }

            return $response;
        } catch (HttpException $exception) {
            return JsonResponse::fromPayload($exception->statusCode(), ['error' => $exception->getMessage()]);
        } catch (JsonException) {
            return JsonResponse::fromPayload(500, ['error' => 'Internal server error.']);
        } catch (Throwable) {
            return JsonResponse::fromPayload(500, ['error' => 'Internal server error.']);
        }
    }
}
