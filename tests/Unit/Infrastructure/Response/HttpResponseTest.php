<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Response;

use Cabify\Infrastructure\Response\HttpResponse;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: HttpResponseTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter HttpResponseTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class HttpResponseTest extends TestCase
{
    /**
     * Nombre: testSendEmitsStatusAndBody
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testSendEmitsStatusAndBody(): void
    {
        $response = new HttpResponse(202, ['X-Test' => 'yes'], '{"ok":true}');

        ob_start();
        $response->send();
        $output = ob_get_clean();

        self::assertSame(202, http_response_code());
        self::assertSame('{"ok":true}', $output);
    }
}
