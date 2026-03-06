<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Controller;

use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Response\JsonResponse;
use JsonException;

/**
 * Nombre: StatusController
 * Descripción: Controlador HTTP para healthcheck del servicio.
 * Parámetros de entrada: HttpRequest
 * Parámetros de salida: HttpResponse JSON con estado operativo
 * Método de uso: $controller($request)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class StatusController
{
    /**
     * Nombre: __invoke
     * Descripción: Devuelve estado de servicio en formato JSON.
     * Parámetros de entrada: HttpRequest $request
     * Parámetros de salida: HttpResponse
     * Método de uso: $response = $controller($request)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @throws JsonException
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        return JsonResponse::fromPayload(200, [
            'service' => 'cabify-car-pooling',
            'status' => 'ok',
            'path' => $request->path(),
        ]);
    }
}
