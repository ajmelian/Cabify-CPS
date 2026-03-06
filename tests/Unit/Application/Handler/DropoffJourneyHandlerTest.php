<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Application\Handler;

use Cabify\Application\Command\DropoffJourneyCommand;
use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Handler\DropoffJourneyHandler;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: DropoffJourneyHandlerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter DropoffJourneyHandlerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffJourneyHandlerTest extends TestCase
{
    /**
     * Nombre: testRemovesJourneyAndReassignsWaitingQueueWhenPossible
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testRemovesJourneyAndReassignsWaitingQueueWhenPossible(): void
    {
        $repository = new DropoffJourneyRepositoryStub(
            [new JourneyGroup(2, 4)],
            [1 => ['people' => 4, 'car_id' => 1]]
        );
        $availability = new DropoffAvailabilityRepositoryStub([4 => [1]]);

        $handler = new DropoffJourneyHandler($repository, $availability);
        $handler->handle(new DropoffJourneyCommand(1));

        self::assertNull($repository->findJourneyById(1));
        $journey2 = $repository->findJourneyById(2);
        self::assertNotNull($journey2);
        self::assertTrue($journey2->isAssigned());
        self::assertSame(1, $journey2->assignedCarId());
    }

    /**
     * Nombre: testThrowsWhenJourneyToDropoffDoesNotExist
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsWhenJourneyToDropoffDoesNotExist(): void
    {
        $repository = new DropoffJourneyRepositoryStub();
        $availability = new DropoffAvailabilityRepositoryStub([]);

        $handler = new DropoffJourneyHandler($repository, $availability);

        $this->expectException(JourneyNotFoundException::class);
        $handler->handle(new DropoffJourneyCommand(99));
    }
}

/**
 * Nombre: DropoffJourneyRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter DropoffJourneyRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffJourneyRepositoryStub implements JourneyRepositoryInterface
{
    /** @var array<int, JourneyGroup> */
    private array $waitingById = [];

    /** @var array<int, array{people:int, car_id:int}> */
    private array $assignedById = [];

    /**
     * @param array<int, JourneyGroup> $initialWaiting
     * @param array<int, array{people:int, car_id:int}> $initialAssigned
     */
    public function __construct(array $initialWaiting = [], array $initialAssigned = [])
    {
        foreach ($initialWaiting as $group) {
            $this->waitingById[$group->id()] = $group;
        }

        $this->assignedById = $initialAssigned;
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
        if (isset($this->waitingById[$group->id()]) || isset($this->assignedById[$group->id()])) {
            throw new DuplicateJourneyException('duplicate');
        }

        $this->waitingById[$group->id()] = $group;
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
        ksort($this->waitingById);

        return array_values($this->waitingById);
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
        if (!isset($this->waitingById[$groupId])) {
            return;
        }

        $group = $this->waitingById[$groupId];
        unset($this->waitingById[$groupId]);
        $this->assignedById[$groupId] = [
            'people' => $group->people(),
            'car_id' => $carId,
        ];
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
        if (isset($this->waitingById[$groupId])) {
            $group = $this->waitingById[$groupId];

            return new JourneyState($group->id(), $group->people(), 'waiting', null);
        }

        if (isset($this->assignedById[$groupId])) {
            return new JourneyState(
                $groupId,
                $this->assignedById[$groupId]['people'],
                'assigned',
                $this->assignedById[$groupId]['car_id']
            );
        }

        return null;
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
        if (isset($this->waitingById[$groupId])) {
            unset($this->waitingById[$groupId]);

            return true;
        }

        if (isset($this->assignedById[$groupId])) {
            unset($this->assignedById[$groupId]);

            return true;
        }

        return false;
    }
}

/**
 * Nombre: DropoffAvailabilityRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter DropoffAvailabilityRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffAvailabilityRepositoryStub implements CarAvailabilityRepositoryInterface
{
    /** @var array<int, array<int, int|null>> */
    private array $responsesByPeople;

    /** @var array<int, int> */
    private array $offsetByPeople = [];

    /**
     * @param array<int, array<int, int|null>> $responsesByPeople
     */
    public function __construct(array $responsesByPeople)
    {
        $this->responsesByPeople = $responsesByPeople;
    }

    /**
     * Nombre: findFirstAvailableCarIdForPeople
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findFirstAvailableCarIdForPeople(int $people): ?int
    {
        $offset = $this->offsetByPeople[$people] ?? 0;
        $responses = $this->responsesByPeople[$people] ?? [null];

        $response = $responses[$offset] ?? null;
        $this->offsetByPeople[$people] = $offset + 1;

        return $response;
    }
}
