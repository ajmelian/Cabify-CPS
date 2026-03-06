<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Application\Dto;

use Cabify\Application\Dto\JourneyState;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: JourneyStateTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter JourneyStateTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JourneyStateTest extends TestCase
{
    /**
     * Nombre: testExposesAssignedJourneyData
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testExposesAssignedJourneyData(): void
    {
        $state = new JourneyState(7, 4, 'assigned', 2);

        self::assertSame(7, $state->id());
        self::assertSame(4, $state->people());
        self::assertSame('assigned', $state->status());
        self::assertSame(2, $state->assignedCarId());
        self::assertTrue($state->isAssigned());
        self::assertFalse($state->isWaiting());
    }

    /**
     * Nombre: testExposesWaitingJourneyData
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testExposesWaitingJourneyData(): void
    {
        $state = new JourneyState(8, 3, 'waiting', null);

        self::assertSame(8, $state->id());
        self::assertSame(3, $state->people());
        self::assertSame('waiting', $state->status());
        self::assertNull($state->assignedCarId());
        self::assertTrue($state->isWaiting());
        self::assertFalse($state->isAssigned());
    }

    /**
     * Nombre: testThrowsExceptionWhenStatusIsInvalid
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenStatusIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JourneyState(1, 2, 'unknown', null);
    }

    /**
     * Nombre: testThrowsExceptionWhenAssignedStatusHasNoCar
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenAssignedStatusHasNoCar(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JourneyState(1, 2, 'assigned', null);
    }
}
