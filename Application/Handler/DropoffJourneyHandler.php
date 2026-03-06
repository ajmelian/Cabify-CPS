<?php

declare(strict_types=1);

namespace Cabify\Application\Handler;

use Cabify\Application\Command\DropoffJourneyCommand;
use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\CarAvailabilitySnapshotRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Service\FairJourneyAssignmentPlanner;

/**
 * Nombre: DropoffJourneyHandler
 * Descripción: Caso de uso para finalizar un grupo y reasignar cola en espera cuando haya capacidad.
 * Parámetros de entrada: DropoffJourneyCommand
 * Parámetros de salida: void
 * Método de uso: $handler->handle($command)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffJourneyHandler
{
    private JourneyRepositoryInterface $journeyRepository;

    private CarAvailabilityRepositoryInterface $carAvailabilityRepository;

    private FairJourneyAssignmentPlanner $assignmentPlanner;

    /**
     * Nombre: __construct
     * Descripción: Inyecta puertos de journeys y disponibilidad de coches.
     * Parámetros de entrada: JourneyRepositoryInterface $journeyRepository, CarAvailabilityRepositoryInterface $carAvailabilityRepository
     * Parámetros de salida: DropoffJourneyHandler
     * Método de uso: new DropoffJourneyHandler($journeyRepo, $carRepo)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(
        JourneyRepositoryInterface $journeyRepository,
        CarAvailabilityRepositoryInterface $carAvailabilityRepository,
        ?FairJourneyAssignmentPlanner $assignmentPlanner = null
    ) {
        $this->journeyRepository = $journeyRepository;
        $this->carAvailabilityRepository = $carAvailabilityRepository;
        $this->assignmentPlanner = $assignmentPlanner ?? new FairJourneyAssignmentPlanner();
    }

    /**
     * Nombre: handle
     * Descripción: Finaliza el journey indicado y procesa reasignación de cola en espera.
     * Parámetros de entrada: DropoffJourneyCommand $command
     * Parámetros de salida: void
     * Método de uso: $handler->handle(new DropoffJourneyCommand(5))
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @throws JourneyNotFoundException
     */
    public function handle(DropoffJourneyCommand $command): void
    {
        $removed = $this->journeyRepository->removeJourneyById($command->id());
        if (!$removed) {
            throw new JourneyNotFoundException(sprintf('Journey %d not found.', $command->id()));
        }

        $waitingGroups = $this->journeyRepository->findWaitingJourneys();

        $assignments = [];
        if ($this->carAvailabilityRepository instanceof CarAvailabilitySnapshotRepositoryInterface) {
            $assignments = $this->assignmentPlanner->plan(
                $waitingGroups,
                $this->carAvailabilityRepository->findAvailableSeatsByCarId()
            );
        } else {
            foreach ($waitingGroups as $waitingGroup) {
                $carId = $this->carAvailabilityRepository->findFirstAvailableCarIdForPeople($waitingGroup->people());
                if ($carId === null) {
                    continue;
                }

                $assignments[$waitingGroup->id()] = $carId;
            }
        }

        foreach ($assignments as $groupId => $carId) {
            $this->journeyRepository->assignJourneyToCar($groupId, $carId);
        }
    }
}
