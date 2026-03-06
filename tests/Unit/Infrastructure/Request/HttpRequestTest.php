<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Request;

use Cabify\Infrastructure\Request\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: HttpRequestTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter HttpRequestTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class HttpRequestTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverBackup;

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
    }

    /**
     * Nombre: testFromGlobalsNormalizesMethodPathAndHeaders
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testFromGlobalsNormalizesMethodPathAndHeaders(): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'post',
            'REQUEST_URI' => '/journey?source=test-suite',
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_TRACE_ID' => 'trace-123',
        ];

        $request = HttpRequest::fromGlobals();

        self::assertSame('POST', $request->method());
        self::assertSame('/journey', $request->path());
        self::assertSame('application/json', $request->header('content-type'));
        self::assertSame('trace-123', $request->header('x-trace-id'));
        self::assertSame('', $request->body());
    }

    /**
     * Nombre: testFromGlobalsUsesDefaultsWhenServerIsEmpty
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testFromGlobalsUsesDefaultsWhenServerIsEmpty(): void
    {
        $_SERVER = [];

        $request = HttpRequest::fromGlobals();

        self::assertSame('GET', $request->method());
        self::assertSame('/', $request->path());
    }
}
