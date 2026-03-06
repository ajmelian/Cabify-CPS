<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Router;

use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Router\Router;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: RouterTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter RouterTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class RouterTest extends TestCase
{
    /**
     * Nombre: testDispatchesRegisteredRouteByMethodAndPath
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testDispatchesRegisteredRouteByMethodAndPath(): void
    {
        $router = new Router();
        $router->add('GET', '/status', static fn (HttpRequest $request): HttpResponse => new HttpResponse(
            200,
            ['Content-Type' => 'application/json; charset=utf-8'],
            '{"path":"' . $request->path() . '"}'
        ));

        $response = $router->dispatch(new HttpRequest('GET', '/status', [], ''));

        self::assertNotNull($response);
        self::assertSame(200, $response->statusCode());
        self::assertSame('{"path":"/status"}', $response->body());
    }

    /**
     * Nombre: testReturnsNullWhenRouteDoesNotExist
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturnsNullWhenRouteDoesNotExist(): void
    {
        $router = new Router();
        $router->add('GET', '/status', static fn (): HttpResponse => new HttpResponse(200, [], 'ok'));

        $response = $router->dispatch(new HttpRequest('POST', '/status', [], ''));

        self::assertNull($response);
    }
}
