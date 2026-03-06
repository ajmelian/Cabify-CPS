<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Application\Handler;

use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Handler\LocateJourneyHandler;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Application\Query\LocateJourneyQuery;
use Cabify\Entity\JourneyGroup;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: LocateJourneyHandlerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter LocateJourneyHandlerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class LocateJourneyHandlerTest extends TestCase
{
    /**
     * Nombre: testReturnsAssignedJourneyState
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturnsAssignedJourneyState(): void
    {
        $repository = new LocateJourneyRepositoryStub([
            7 => new JourneyState(7, 4, 'assigned', 2),
        ]);

        $handler = new LocateJourneyHandler($repository);
        $state = $handler->handle(new LocateJourneyQuery(7));

        self::assertTrue($state->isAssigned());
        self::assertSame(2, $state->assignedCarId());
    }

    /**
     * Nombre: testReturnsWaitingJourneyState
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturnsWaitingJourneyState(): void
    {
        $repository = new LocateJourneyRepositoryStub([
            8 => new JourneyState(8, 3, 'waiting', null),
        ]);

        $handler = new LocateJourneyHandler($repository);
        $state = $handler->handle(new LocateJourneyQuery(8));

        self::assertTrue($state->isWaiting());
        self::assertNull($state->assignedCarId());
    }

    /**
     * Nombre: testThrowsWhenJourneyDoesNotExist
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsWhenJourneyDoesNotExist(): void
    {
        $handler = new LocateJourneyHandler(new LocateJourneyRepositoryStub([]));

        $this->expectException(JourneyNotFoundException::class);
        $handler->handle(new LocateJourneyQuery(999));
    }
}

/**
 * Nombre: LocateJourneyRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter LocateJourneyRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class LocateJourneyRepositoryStub implements JourneyRepositoryInterface
{
    /** @var array<int, JourneyState> */
    private array $statesById;

    /**
     * @param array<int, JourneyState> $statesById
     */
    public function __construct(array $statesById)
    {
        $this->statesById = $statesById;
    }

    /**
     * Nombre: createWaitingJourney
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function createWaitingJourney(JourneyGroup $group): void
    {
        if (isset($this->statesById[$group->id()])) {
            throw new DuplicateJourneyException('duplicate');
        }

        $this->statesById[$group->id()] = new JourneyState($group->id(), $group->people(), 'waiting', null);
    }

    /**
     * Nombre: findWaitingJourneys
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findWaitingJourneys(): array
    {
        $waiting = [];

        foreach ($this->statesById as $state) {
            if ($state->isWaiting()) {
                $waiting[] = new JourneyGroup($state->id(), $state->people());
            }
        }

        return $waiting;
    }

    /**
     * Nombre: assignJourneyToCar
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function assignJourneyToCar(int $groupId, int $carId): void
    {
        if (!isset($this->statesById[$groupId])) {
            return;
        }

        $current = $this->statesById[$groupId];
        $this->statesById[$groupId] = new JourneyState($current->id(), $current->people(), 'assigned', $carId);
    }

    /**
     * Nombre: findJourneyById
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findJourneyById(int $groupId): ?JourneyState
    {
        return $this->statesById[$groupId] ?? null;
    }

    /**
     * Nombre: removeJourneyById
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function removeJourneyById(int $groupId): bool
    {
        if (!isset($this->statesById[$groupId])) {
            return false;
        }

        unset($this->statesById[$groupId]);

        return true;
    }
}
