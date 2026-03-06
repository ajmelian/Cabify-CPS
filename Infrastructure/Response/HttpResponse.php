<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Response;

/**
 * Nombre: HttpResponse
 * Descripción: Respuesta HTTP tipada para salida segura desde adapters.
 * Parámetros de entrada: statusCode, headers, body
 * Parámetros de salida: Objeto de respuesta listo para enviar
 * Método de uso: new HttpResponse(200, ['Content-Type' => 'application/json'], '{}')
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
class HttpResponse
{
    private int $statusCode;

    /** @var array<string, string> */
    private array $headers;

    private string $body;

    /**
     * Nombre: __construct
     * Descripción: Inicializa una respuesta HTTP inmutable.
     * Parámetros de entrada: int $statusCode, array<string, string> $headers, string $body
     * Parámetros de salida: HttpResponse
     * Método de uso: new HttpResponse(404, [], '')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param array<string, string> $headers
     */
    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Nombre: statusCode
     * Descripción: Devuelve el código de estado HTTP.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $response->statusCode()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Nombre: body
     * Descripción: Devuelve el cuerpo HTTP serializado.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: string
     * Método de uso: $response->body()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Nombre: send
     * Descripción: Emite cabeceras, código y body al cliente HTTP.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: void
     * Método de uso: $response->send()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
