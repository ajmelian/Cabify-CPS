<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Request;

/**
 * Nombre: HttpRequest
 * Descripción: Request HTTP tipada para desacoplar superglobales de controladores.
 * Parámetros de entrada: method, path, headers, body
 * Parámetros de salida: Objeto inmutable de request
 * Método de uso: HttpRequest::fromGlobals()
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class HttpRequest
{
    private string $method;

    private string $path;

    /** @var array<string, string> */
    private array $headers;

    private string $body;

    /**
     * Nombre: __construct
     * Descripción: Construye una request HTTP inmutable.
     * Parámetros de entrada: string $method, string $path, array<string, string> $headers, string $body
     * Parámetros de salida: HttpRequest
     * Método de uso: new HttpRequest('GET', '/status', [], '')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param array<string, string> $headers
     */
    public function __construct(string $method, string $path, array $headers, string $body)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Nombre: fromGlobals
     * Descripción: Crea una request HTTP leyendo superglobales en el adapter de entrada.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: HttpRequest
     * Método de uso: HttpRequest::fromGlobals()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = (string) parse_url($requestUri, PHP_URL_PATH);
        $rawBody = file_get_contents('php://input');

        return new self(
            (string) $method,
            $path === '' ? '/' : $path,
            self::normalizedHeaders(),
            $rawBody === false ? '' : $rawBody
        );
    }

    /**
     * Nombre: method
     * Descripción: Devuelve el método HTTP en mayúsculas.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: string
     * Método de uso: $request->method()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Nombre: path
     * Descripción: Devuelve la ruta solicitada sin query string.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: string
     * Método de uso: $request->path()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Nombre: header
     * Descripción: Obtiene una cabecera HTTP por nombre canónico.
     * Parámetros de entrada: string $name
     * Parámetros de salida: string|null
     * Método de uso: $request->header('content-type')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function header(string $name): ?string
    {
        $key = strtolower($name);

        return $this->headers[$key] ?? null;
    }

    /**
     * Nombre: body
     * Descripción: Devuelve el cuerpo raw de la petición.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: string
     * Método de uso: $request->body()
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
     * Nombre: normalizedHeaders
     * Descripción: Normaliza cabeceras a formato lowercase para acceso consistente.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<string, string>
     * Método de uso: self::normalizedHeaders()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @return array<string, string>
     */
    private static function normalizedHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_') && $key !== 'CONTENT_TYPE') {
                continue;
            }

            $headerName = str_replace('_', '-', strtolower(str_starts_with($key, 'HTTP_') ? substr($key, 5) : $key));
            $headers[$headerName] = (string) $value;
        }

        return $headers;
    }
}
