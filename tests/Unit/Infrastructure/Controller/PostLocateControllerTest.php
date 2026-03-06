<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Controller;

use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Handler\LocateJourneyHandler;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use Cabify\Infrastructure\Controller\PostLocateController;
use Cabify\Infrastructure\Http\Exception\HttpException;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: PostLocateControllerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter PostLocateControllerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PostLocateControllerTest extends TestCase
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
        $controller = new PostLocateController(
            new LocateJourneyHandler(
                new LocateControllerJourneyRepositoryStub([
                    20 => new JourneyState(20, 4, 'assigned', 9),
                ])
            )
        );

        $request = new HttpRequest('POST', '/locate', ['content-type' => 'application/json'], '{"id":20}');
        $response = $controller($request);

        self::assertSame(200, $response->statusCode());
        self::assertSame('{"id":20,"status":"assigned","car_id":9}', $response->body());
    }

    /**
     * Nombre: testReturns204WhenJourneyIsWaiting
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturns204WhenJourneyIsWaiting(): void
    {
        $controller = new PostLocateController(
            new LocateJourneyHandler(
                new LocateControllerJourneyRepositoryStub([
                    21 => new JourneyState(21, 3, 'waiting', null),
                ])
            )
        );

        $request = new HttpRequest('POST', '/locate', ['content-type' => 'application/json'], '{"id":21}');
        $response = $controller($request);

        self::assertSame(204, $response->statusCode());
        self::assertSame('', $response->body());
    }

    /**
     * Nombre: testThrows404WhenJourneyIsMissing
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrows404WhenJourneyIsMissing(): void
    {
        $controller = new PostLocateController(new LocateJourneyHandler(new LocateControllerJourneyRepositoryStub([])));

        $request = new HttpRequest('POST', '/locate', ['content-type' => 'application/json'], '{"id":300}');

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
        $controller = new PostLocateController(new LocateJourneyHandler(new LocateControllerJourneyRepositoryStub([])));

        $request = new HttpRequest('POST', '/locate', ['content-type' => 'application/json'], '{"id":0}');

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }
}

/**
 * Nombre: LocateControllerJourneyRepositoryStub
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter LocateControllerJourneyRepositoryStub
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class LocateControllerJourneyRepositoryStub implements JourneyRepositoryInterface
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
