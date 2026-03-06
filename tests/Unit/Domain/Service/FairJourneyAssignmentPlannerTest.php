<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Domain\Service;

use Cabify\Entity\JourneyGroup;
use Cabify\Service\FairJourneyAssignmentPlanner;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: FairJourneyAssignmentPlannerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter FairJourneyAssignmentPlannerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class FairJourneyAssignmentPlannerTest extends TestCase
{
    /**
     * Nombre: testAssignsInArrivalOrderWhenCapacityAllows
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testAssignsInArrivalOrderWhenCapacityAllows(): void
    {
        $planner = new FairJourneyAssignmentPlanner();

        $assignments = $planner->plan(
            [new JourneyGroup(1, 2), new JourneyGroup(2, 2), new JourneyGroup(3, 3)],
            [10 => 4, 20 => 3]
        );

        self::assertSame(10, $assignments[1]);
        self::assertSame(10, $assignments[2]);
        self::assertSame(20, $assignments[3]);
    }

    /**
     * Nombre: testSkipsBlockedJourneyAndAssignsFollowingWhenPossible
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testSkipsBlockedJourneyAndAssignsFollowingWhenPossible(): void
    {
        $planner = new FairJourneyAssignmentPlanner();

        $assignments = $planner->plan(
            [new JourneyGroup(1, 6), new JourneyGroup(2, 3), new JourneyGroup(3, 2)],
            [1 => 4, 2 => 2]
        );

        self::assertArrayNotHasKey(1, $assignments);
        self::assertSame(1, $assignments[2]);
        self::assertSame(2, $assignments[3]);
    }

    /**
     * Nombre: testUsesCarsOrderedByIdForDeterministicFairness
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testUsesCarsOrderedByIdForDeterministicFairness(): void
    {
        $planner = new FairJourneyAssignmentPlanner();

        $assignments = $planner->plan(
            [new JourneyGroup(1, 1), new JourneyGroup(2, 1), new JourneyGroup(3, 1)],
            [20 => 1, 10 => 2]
        );

        self::assertSame(10, $assignments[1]);
        self::assertSame(10, $assignments[2]);
        self::assertSame(20, $assignments[3]);
    }
}
