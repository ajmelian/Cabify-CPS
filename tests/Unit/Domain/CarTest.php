<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Domain;

use Cabify\Entity\Car;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: CarTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter CarTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class CarTest extends TestCase
{
    /**
     * Nombre: testCreatesCarWithValidInput
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testCreatesCarWithValidInput(): void
    {
        $car = new Car(1, 4);

        self::assertSame(1, $car->id());
        self::assertSame(4, $car->seats());
    }

    /**
     * Nombre: testThrowsExceptionWhenSeatsAreOutOfRange
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenSeatsAreOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Car(2, 0);
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

        new Car(0, 4);
    }
}
