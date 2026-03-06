<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Http;

use Cabify\Infrastructure\Http\Exception\HttpException;
use Cabify\Infrastructure\Http\Kernel;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Router\Router;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: KernelTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter KernelTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class KernelTest extends TestCase
{
    /**
     * Nombre: testReturns404ForUnknownRoute
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturns404ForUnknownRoute(): void
    {
        $kernel = new Kernel(new Router());

        $response = $kernel->handle(new HttpRequest('GET', '/unknown', [], ''));

        self::assertSame(404, $response->statusCode());
        self::assertSame('{"error":"Resource not found."}', $response->body());
    }

    /**
     * Nombre: testMapsHttpExceptionToStatusCodeAndErrorBody
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testMapsHttpExceptionToStatusCodeAndErrorBody(): void
    {
        $router = new Router();
        $router->add('POST', '/any', static function (): HttpResponse {
            throw new HttpException(400, 'Invalid request.');
        });

        $kernel = new Kernel($router);
        $response = $kernel->handle(new HttpRequest('POST', '/any', [], ''));

        self::assertSame(400, $response->statusCode());
        self::assertSame('{"error":"Invalid request."}', $response->body());
    }

    /**
     * Nombre: testMapsUnexpectedExceptionToInternalServerError
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testMapsUnexpectedExceptionToInternalServerError(): void
    {
        $router = new Router();
        $router->add('POST', '/explode', static function (): HttpResponse {
            throw new \RuntimeException('unexpected');
        });

        $kernel = new Kernel($router);
        $response = $kernel->handle(new HttpRequest('POST', '/explode', [], ''));

        self::assertSame(500, $response->statusCode());
        self::assertSame('{"error":"Internal server error."}', $response->body());
    }
}
