<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Controller;

use Cabify\Application\Handler\ReplaceFleetHandler;
use Cabify\Application\Port\CarRepositoryInterface;
use Cabify\Entity\Car;
use Cabify\Infrastructure\Controller\PutCarsController;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: PutCarsControllerTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter PutCarsControllerTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PutCarsControllerTest extends TestCase
{
    /**
     * Nombre: testReturnsSuccessWhenPayloadIsValid
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReturnsSuccessWhenPayloadIsValid(): void
    {
        $repository = new ControllerCarRepository();
        $controller = new PutCarsController(new ReplaceFleetHandler($repository));

        $request = new HttpRequest(
            'PUT',
            '/cars',
            ['content-type' => 'application/json; charset=utf-8'],
            '[{"id":1,"seats":4},{"id":"2","seats":"6"}]'
        );

        $response = $controller($request);

        self::assertSame(200, $response->statusCode());
        self::assertSame('{"status":"fleet_replaced"}', $response->body());
        self::assertCount(2, $repository->storedCars);
        self::assertSame(2, $repository->storedCars[1]->id());
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
        $repository = new ControllerCarRepository();
        $controller = new PutCarsController(new ReplaceFleetHandler($repository));

        $request = new HttpRequest(
            'PUT',
            '/cars',
            ['content-type' => 'application/json; charset=utf-8'],
            '[{"id":0,"seats":4}]'
        );

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }

    /**
     * Nombre: testThrowsValidationExceptionWhenContentTypeIsInvalid
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsValidationExceptionWhenContentTypeIsInvalid(): void
    {
        $repository = new ControllerCarRepository();
        $controller = new PutCarsController(new ReplaceFleetHandler($repository));

        $request = new HttpRequest('PUT', '/cars', ['content-type' => 'text/plain'], '[]');

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }

    /**
     * Nombre: testThrowsValidationExceptionWhenJsonIsInvalid
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsValidationExceptionWhenJsonIsInvalid(): void
    {
        $repository = new ControllerCarRepository();
        $controller = new PutCarsController(new ReplaceFleetHandler($repository));

        $request = new HttpRequest('PUT', '/cars', ['content-type' => 'application/json'], '[{"id":1');

        $this->expectException(ValidationHttpException::class);
        $controller($request);
    }
}

/**
 * Nombre: ControllerCarRepository
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter ControllerCarRepository
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ControllerCarRepository implements CarRepositoryInterface
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
