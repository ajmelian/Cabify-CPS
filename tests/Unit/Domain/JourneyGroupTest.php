<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Domain;

use Cabify\Entity\JourneyGroup;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: JourneyGroupTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter JourneyGroupTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JourneyGroupTest extends TestCase
{
    /**
     * Nombre: testCreatesJourneyGroupWithValidInput
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testCreatesJourneyGroupWithValidInput(): void
    {
        $group = new JourneyGroup(10, 4);

        self::assertSame(10, $group->id());
        self::assertSame(4, $group->people());
    }

    /**
     * Nombre: testThrowsExceptionWhenIdIsInvalid
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenIdIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JourneyGroup(0, 4);
    }

    /**
     * Nombre: testThrowsExceptionWhenPeopleAreOutOfRange
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenPeopleAreOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JourneyGroup(10, 7);
    }
}
