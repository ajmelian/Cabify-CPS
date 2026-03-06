<?php

declare(strict_types=1);

namespace Cabify\Service;

use Cabify\Entity\JourneyGroup;

/**
 * Nombre: FairJourneyAssignmentPlanner
 * Descripción: Planifica asignaciones group->car respetando orden de llegada cuando haya capacidad.
 * Parámetros de entrada: lista de groups en espera y snapshot de plazas libres por coche
 * Parámetros de salida: mapa de asignaciones groupId => carId
 * Método de uso: $planner->plan($waitingGroups, $freeSeatsByCarId)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class FairJourneyAssignmentPlanner
{
    /**
     * Nombre: plan
     * Descripción: Calcula asignaciones aplicando first-fit sobre coches ordenados por id.
     * Parámetros de entrada: array<int, JourneyGroup> $waitingGroups, array<int, int> $freeSeatsByCarId
     * Parámetros de salida: array<int, int>
     * Método de uso: $assignments = $planner->plan($waiting, $freeSeats)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param array<int, JourneyGroup> $waitingGroups
     * @param array<int, int> $freeSeatsByCarId
     * @return array<int, int>
     */
    public function plan(array $waitingGroups, array $freeSeatsByCarId): array
    {
        ksort($freeSeatsByCarId);

        $assignments = [];

        foreach ($waitingGroups as $group) {
            foreach ($freeSeatsByCarId as $carId => $freeSeats) {
                if ($freeSeats < $group->people()) {
                    continue;
                }

                $assignments[$group->id()] = $carId;
                $freeSeatsByCarId[$carId] = $freeSeats - $group->people();
                break;
            }
        }

        return $assignments;
    }
}
