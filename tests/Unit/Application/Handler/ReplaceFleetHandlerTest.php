<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Application\Handler;

use Cabify\Application\Command\ReplaceFleetCommand;
use Cabify\Application\Handler\ReplaceFleetHandler;
use Cabify\Application\Port\CarRepositoryInterface;
use Cabify\Entity\Car;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: ReplaceFleetHandlerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter ReplaceFleetHandlerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ReplaceFleetHandlerTest extends TestCase
{
    /**
     * Nombre: testDelegatesFleetReplacementToRepository
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testDelegatesFleetReplacementToRepository(): void
    {
        $repository = new InMemoryCarRepository();
        $handler = new ReplaceFleetHandler($repository);

        $cars = [new Car(1, 4), new Car(2, 6)];
        $handler->handle(new ReplaceFleetCommand($cars));

        self::assertCount(2, $repository->storedCars);
        self::assertSame(1, $repository->storedCars[0]->id());
        self::assertSame(6, $repository->storedCars[1]->seats());
    }
}

/**
 * Nombre: InMemoryCarRepository
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter InMemoryCarRepository
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class InMemoryCarRepository implements CarRepositoryInterface
{
    /** @var array<int, Car> */
    public array $storedCars = [];

    /**
     * Nombre: replaceFleet
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function replaceFleet(array $cars): void
    {
        $this->storedCars = $cars;
    }

    /**
     * Nombre: findAll
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findAll(): array
    {
        return $this->storedCars;
    }
}
