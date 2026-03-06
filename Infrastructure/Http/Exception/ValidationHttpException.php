<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Http\Exception;

/**
 * Nombre: ValidationHttpException
 * Descripción: Excepción para errores de validación en requests HTTP.
 * Parámetros de entrada: string $message
 * Parámetros de salida: Excepción HTTP 400.
 * Método de uso: throw new ValidationHttpException('Invalid payload')
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ValidationHttpException extends HttpException
{
    /**
     * Nombre: __construct
     * Descripción: Crea una excepción de validación HTTP con estado 400.
     * Parámetros de entrada: string $message
     * Parámetros de salida: ValidationHttpException
     * Método de uso: new ValidationHttpException('Invalid JSON')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(string $message)
    {
        parent::__construct(400, $message);
    }
}
