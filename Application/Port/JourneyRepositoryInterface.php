<?php

declare(strict_types=1);

namespace Cabify\Application\Port;

use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Dto\JourneyState;
use Cabify\Entity\JourneyGroup;

/**
 * Nombre: JourneyRepositoryInterface
 * Descripción: Puerto de aplicación para gestionar grupos de journey en persistencia.
 * Parámetros de entrada: JourneyGroup y IDs de grupo/coche
 * Parámetros de salida: Colecciones tipadas de JourneyGroup
 * Método de uso: Implementación en adapter MySQL.
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
interface JourneyRepositoryInterface
{
    /**
     * Nombre: createWaitingJourney
     * Descripción: Inserta un nuevo grupo en estado de espera.
     * Parámetros de entrada: JourneyGroup $group
     * Parámetros de salida: void
     * Método de uso: $repository->createWaitingJourney($group)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @throws DuplicateJourneyException
     */
    public function createWaitingJourney(JourneyGroup $group): void;

    /**
     * Nombre: findWaitingJourneys
     * Descripción: Recupera grupos en espera ordenados por llegada.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, JourneyGroup>
     * Método de uso: $journeys = $repository->findWaitingJourneys()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @return array<int, JourneyGroup>
     */
    public function findWaitingJourneys(): array;

    /**
     * Nombre: assignJourneyToCar
     * Descripción: Marca un grupo como asignado a un coche.
     * Parámetros de entrada: int $groupId, int $carId
     * Parámetros de salida: void
     * Método de uso: $repository->assignJourneyToCar(1, 10)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function assignJourneyToCar(int $groupId, int $carId): void;

    /**
     * Nombre: findJourneyById
     * Descripción: Recupera estado de un grupo por id para operaciones de localización.
     * Parámetros de entrada: int $groupId
     * Parámetros de salida: JourneyState|null
     * Método de uso: $journey = $repository->findJourneyById(7)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findJourneyById(int $groupId): ?JourneyState;

    /**
     * Nombre: removeJourneyById
     * Descripción: Elimina un grupo del sistema al completar su dropoff.
     * Parámetros de entrada: int $groupId
     * Parámetros de salida: bool
     * Método de uso: $removed = $repository->removeJourneyById(7)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function removeJourneyById(int $groupId): bool;
}
