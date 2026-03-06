<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Application\Handler;

use Cabify\Application\Command\RegisterJourneyCommand;
use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: RegisterJourneyHandlerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter RegisterJourneyHandlerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class RegisterJourneyHandlerTest extends TestCase
{
    /**
     * Nombre: testAssignsJourneyWhenCarIsAvailable
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testAssignsJourneyWhenCarIsAvailable(): void
    {
        $repository = new HandlerJourneyRepositoryStub();
        $availability = new HandlerAvailabilityRepositoryStub([
            4 => [3],
        ]);

        $handler = new RegisterJourneyHandler($repository, $availability);
        $result = $handler->handle(new RegisterJourneyCommand(10, 4));

        self::assertSame('assigned', $result->status());
        self::assertSame(3, $result->carId());
        self::assertSame(10, $result->groupId());
    }

    /**
     * Nombre: testReturnsWaitingWhenNoCarIsAvailable
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturnsWaitingWhenNoCarIsAvailable(): void
    {
        $repository = new HandlerJourneyRepositoryStub();
        $availability = new HandlerAvailabilityRepositoryStub([
            4 => [null],
        ]);

        $handler = new RegisterJourneyHandler($repository, $availability);
        $result = $handler->handle(new RegisterJourneyCommand(11, 4));

        self::assertSame('waiting', $result->status());
        self::assertNull($result->carId());
        self::assertSame(11, $result->groupId());
    }

    /**
     * Nombre: testMaintainsArrivalOrderWhenPossible
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testMaintainsArrivalOrderWhenPossible(): void
    {
        $repository = new HandlerJourneyRepositoryStub([
            new JourneyGroup(1, 6),
        ]);
        $availability = new HandlerAvailabilityRepositoryStub([
            6 => [null],
            4 => [9],
        ]);

        $handler = new RegisterJourneyHandler($repository, $availability);
        $result = $handler->handle(new RegisterJourneyCommand(2, 4));

        self::assertSame('assigned', $result->status());
        self::assertSame(9, $result->carId());
        self::assertArrayNotHasKey(1, $repository->assignedCarsByGroup);
        self::assertSame(9, $repository->assignedCarsByGroup[2]);
    }

    /**
     * Nombre: testPropagatesDuplicateJourneyError
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testPropagatesDuplicateJourneyError(): void
    {
        $repository = new HandlerJourneyRepositoryStub();
        $availability = new HandlerAvailabilityRepositoryStub([
            4 => [3],
        ]);

        $handler = new RegisterJourneyHandler($repository, $availability);
        $handler->handle(new RegisterJourneyCommand(12, 4));

        $this->expectException(DuplicateJourneyException::class);
        $handler->handle(new RegisterJourneyCommand(12, 4));
    }
}

/**
 * Nombre: HandlerJourneyRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter HandlerJourneyRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class HandlerJourneyRepositoryStub implements JourneyRepositoryInterface
{
    /** @var array<int, JourneyGroup> */
    private array $waitingById = [];

    /** @var array<int, int> */
    public array $assignedCarsByGroup = [];

    /**
     * @param array<int, JourneyGroup> $initialWaiting
     */
    public function __construct(array $initialWaiting = [])
    {
        foreach ($initialWaiting as $group) {
            $this->waitingById[$group->id()] = $group;
        }
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
        if (isset($this->waitingById[$group->id()]) || isset($this->assignedCarsByGroup[$group->id()])) {
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
        $this->assignedCarsByGroup[$groupId] = $carId;
        unset($this->waitingById[$groupId]);
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

        if (isset($this->assignedCarsByGroup[$groupId])) {
            return new JourneyState($groupId, 1, 'assigned', $this->assignedCarsByGroup[$groupId]);
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

        if (isset($this->assignedCarsByGroup[$groupId])) {
            unset($this->assignedCarsByGroup[$groupId]);

            return true;
        }

        return false;
    }
}

/**
 * Nombre: HandlerAvailabilityRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter HandlerAvailabilityRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class HandlerAvailabilityRepositoryStub implements CarAvailabilityRepositoryInterface
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
