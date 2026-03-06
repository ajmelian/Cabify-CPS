<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Controller;

use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Handler\DropoffJourneyHandler;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use Cabify\Infrastructure\Controller\PostDropoffController;
use Cabify\Infrastructure\Http\Exception\HttpException;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: PostDropoffControllerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter PostDropoffControllerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PostDropoffControllerTest extends TestCase
{
    /**
     * Nombre: testReturns200WhenJourneyIsDroppedOff
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturns200WhenJourneyIsDroppedOff(): void
    {
        $controller = new PostDropoffController(
            new DropoffJourneyHandler(
                new DropoffControllerJourneyRepositoryStub([
                    4 => new JourneyState(4, 4, 'assigned', 1),
                ]),
                new DropoffControllerAvailabilityRepositoryStub([])
            )
        );

        $request = new HttpRequest('POST', '/dropoff', ['content-type' => 'application/json'], '{"id":4}');

        $response = $controller($request);

        self::assertSame(200, $response->statusCode());
        self::assertSame('{"id":4,"status":"dropped_off"}', $response->body());
    }

    /**
     * Nombre: testThrows404WhenJourneyDoesNotExist
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrows404WhenJourneyDoesNotExist(): void
    {
        $controller = new PostDropoffController(
            new DropoffJourneyHandler(
                new DropoffControllerJourneyRepositoryStub([]),
                new DropoffControllerAvailabilityRepositoryStub([])
            )
        );

        $request = new HttpRequest('POST', '/dropoff', ['content-type' => 'application/json'], '{"id":400}');

        $this->expectException(HttpException::class);
        $controller($request);
    }

    /**
     * Nombre: testThrowsValidationExceptionWhenPayloadIsInvalid
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsValidationExceptionWhenPayloadIsInvalid(): void
    {
        $controller = new PostDropoffController(
            new DropoffJourneyHandler(
                new DropoffControllerJourneyRepositoryStub([]),
                new DropoffControllerAvailabilityRepositoryStub([])
            )
        );

        $request = new HttpRequest('POST', '/dropoff', ['content-type' => 'application/json'], '{"id":0}');

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }
}

/**
 * Nombre: DropoffControllerJourneyRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter DropoffControllerJourneyRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffControllerJourneyRepositoryStub implements JourneyRepositoryInterface
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

        $state = $this->statesById[$groupId];
        $this->statesById[$groupId] = new JourneyState($state->id(), $state->people(), 'assigned', $carId);
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

/**
 * Nombre: DropoffControllerAvailabilityRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter DropoffControllerAvailabilityRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffControllerAvailabilityRepositoryStub implements CarAvailabilityRepositoryInterface
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
