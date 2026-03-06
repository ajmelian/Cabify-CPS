<?php

declare(strict_types=1);

namespace Cabify\Application\Port;

use Cabify\Entity\Car;

/**
 * Nombre: CarRepositoryInterface
 * Descripción: Puerto de aplicación para persistencia de coches.
 * Parámetros de entrada: Entidades Car
 * Parámetros de salida: Colecciones tipadas de Car
 * Método de uso: Implementar en adapters de infraestructura.
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
interface CarRepositoryInterface
{
    /**
     * Nombre: replaceFleet
     * Descripción: Sustituye la flota actual por una lista nueva de coches.
     * Parámetros de entrada: array<int, Car> $cars
     * Parámetros de salida: void
     * Método de uso: $repository->replaceFleet([$car1, $car2])
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function replaceFleet(array $cars): void;

    /**
     * Nombre: findAll
     * Descripción: Recupera toda la flota ordenada por identificador.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, Car>
     * Método de uso: $cars = $repository->findAll()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findAll(): array;
}
