<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Response;

use JsonException;

/**
 * Nombre: JsonResponse
 * Descripción: Respuesta HTTP JSON con cabeceras seguras por defecto.
 * Parámetros de entrada: statusCode y payload serializable
 * Parámetros de salida: HttpResponse JSON
 * Método de uso: JsonResponse::fromPayload(200, ['status' => 'ok'])
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JsonResponse extends HttpResponse
{
    /**
     * Nombre: fromPayload
     * Descripción: Crea respuesta JSON codificando payload y añadiendo cabeceras OWASP básicas.
     * Parámetros de entrada: int $statusCode, array<mixed> $payload
     * Parámetros de salida: JsonResponse
     * Método de uso: JsonResponse::fromPayload(200, ['ok' => true])
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param array<mixed> $payload
     *
     * @throws JsonException
     */
    public static function fromPayload(int $statusCode, array $payload): self
    {
        return new self(
            $statusCode,
            [
                'Content-Type' => 'application/json; charset=utf-8',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'no-store',
            ],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }
}
