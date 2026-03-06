<?php

declare(strict_types=1);

namespace Cabify\Application\Port;

/**
 * Nombre: CarAvailabilitySnapshotRepositoryInterface
 * Descripción: Puerto de aplicación para obtener snapshot de plazas libres por coche.
 * Parámetros de entrada: Ninguno
 * Parámetros de salida: array<int, int> carId => freeSeats
 * Método de uso: $snapshot = $repository->findAvailableSeatsByCarId()
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
interface CarAvailabilitySnapshotRepositoryInterface
{
    /**
     * Nombre: findAvailableSeatsByCarId
     * Descripción: Recupera plazas libres actuales por coche ordenadas por id ascendente.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, int>
     * Método de uso: $availability = $repository->findAvailableSeatsByCarId()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @return array<int, int>
     */
    public function findAvailableSeatsByCarId(): array;
}
