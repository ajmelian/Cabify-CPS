<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Controller;

use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use Cabify\Infrastructure\Controller\PostJourneyController;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: PostJourneyControllerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter PostJourneyControllerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PostJourneyControllerTest extends TestCase
{
    /**
     * Nombre: testReturns200WhenJourneyIsAssigned
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturns200WhenJourneyIsAssigned(): void
    {
        $controller = new PostJourneyController(
            new RegisterJourneyHandler(
                new ControllerJourneyRepositoryStub(),
                new ControllerAvailabilityRepositoryStub([4 => [5]])
            )
        );

        $request = new HttpRequest(
            'POST',
            '/journey',
            ['content-type' => 'application/json'],
            '{"id":15,"people":4}'
        );

        $response = $controller($request);

        self::assertSame(200, $response->statusCode());
        self::assertSame('{"id":15,"status":"assigned","car_id":5}', $response->body());
    }

    /**
     * Nombre: testReturns202WhenJourneyIsWaiting
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturns202WhenJourneyIsWaiting(): void
    {
        $controller = new PostJourneyController(
            new RegisterJourneyHandler(
                new ControllerJourneyRepositoryStub(),
                new ControllerAvailabilityRepositoryStub([3 => [null]])
            )
        );

        $request = new HttpRequest(
            'POST',
            '/journey',
            ['content-type' => 'application/json'],
            '{"id":16,"people":3}'
        );

        $response = $controller($request);

        self::assertSame(202, $response->statusCode());
        self::assertSame('{"id":16,"status":"waiting"}', $response->body());
    }

    /**
     * Nombre: testThrowsValidationExceptionForInvalidPeoplePattern
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsValidationExceptionForInvalidPeoplePattern(): void
    {
        $controller = new PostJourneyController(
            new RegisterJourneyHandler(
                new ControllerJourneyRepositoryStub(),
                new ControllerAvailabilityRepositoryStub([])
            )
        );

        $request = new HttpRequest(
            'POST',
            '/journey',
            ['content-type' => 'application/json'],
            '{"id":17,"people":9}'
        );

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }

    /**
     * Nombre: testThrowsValidationExceptionForInvalidJsonPayload
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsValidationExceptionForInvalidJsonPayload(): void
    {
        $controller = new PostJourneyController(
            new RegisterJourneyHandler(
                new ControllerJourneyRepositoryStub(),
                new ControllerAvailabilityRepositoryStub([])
            )
        );

        $request = new HttpRequest('POST', '/journey', ['content-type' => 'application/json'], '{"id":1');

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }
}

/**
 * Nombre: ControllerJourneyRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter ControllerJourneyRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ControllerJourneyRepositoryStub implements JourneyRepositoryInterface
{
    /** @var array<int, JourneyGroup> */
    private array $waiting = [];

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
        $this->waiting[$group->id()] = $group;
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
        ksort($this->waiting);

        return array_values($this->waiting);
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
        unset($this->waiting[$groupId]);
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
        if (!isset($this->waiting[$groupId])) {
            return null;
        }

        $group = $this->waiting[$groupId];

        return new JourneyState($group->id(), $group->people(), 'waiting', null);
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
        if (!isset($this->waiting[$groupId])) {
            return false;
        }

        unset($this->waiting[$groupId]);

        return true;
    }
}

/**
 * Nombre: ControllerAvailabilityRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter ControllerAvailabilityRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ControllerAvailabilityRepositoryStub implements CarAvailabilityRepositoryInterface
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
