<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Http\Exception;

use RuntimeException;

/**
 * Nombre: HttpException
 * Descripción: Excepción base HTTP para mapear errores a códigos de estado.
 * Parámetros de entrada: int $statusCode, string $message
 * Parámetros de salida: Excepción tipada de capa HTTP.
 * Método de uso: throw new HttpException(404, 'Not found')
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
class HttpException extends RuntimeException
{
    private int $statusCode;

    /**
     * Nombre: __construct
     * Descripción: Crea una excepción HTTP con código y mensaje.
     * Parámetros de entrada: int $statusCode, string $message
     * Parámetros de salida: HttpException
     * Método de uso: new HttpException(400, 'Bad request')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(int $statusCode, string $message)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    /**
     * Nombre: statusCode
     * Descripción: Devuelve el código de estado HTTP asociado.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $exception->statusCode()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
