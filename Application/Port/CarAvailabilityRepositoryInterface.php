<?php

declare(strict_types=1);

namespace Cabify\Application\Port;

/**
 * Nombre: CarAvailabilityRepositoryInterface
 * Descripción: Puerto de aplicación para consultar coches con plazas disponibles.
 * Parámetros de entrada: int $people
 * Parámetros de salida: int|null con el identificador del coche.
 * Método de uso: $carId = $repository->findFirstAvailableCarIdForPeople(4)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
interface CarAvailabilityRepositoryInterface
{
    /**
     * Nombre: findFirstAvailableCarIdForPeople
     * Descripción: Devuelve el primer coche con plazas disponibles para un tamaño de grupo.
     * Parámetros de entrada: int $people
     * Parámetros de salida: int|null
     * Método de uso: $repository->findFirstAvailableCarIdForPeople(3)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findFirstAvailableCarIdForPeople(int $people): ?int;
}
