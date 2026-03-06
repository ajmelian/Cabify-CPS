<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Controller;

use Cabify\Infrastructure\Controller\StatusController;
use Cabify\Infrastructure\Request\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: StatusControllerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter StatusControllerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class StatusControllerTest extends TestCase
{
    /**
     * Nombre: testReturnsHealthyServicePayload
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturnsHealthyServicePayload(): void
    {
        $controller = new StatusController();
        $request = new HttpRequest('GET', '/status', [], '');

        $response = $controller($request);
        $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->statusCode());
        self::assertSame('cabify-car-pooling', $payload['service']);
        self::assertSame('ok', $payload['status']);
        self::assertSame('/status', $payload['path']);
    }
}
