<?php

declare(strict_types=1);

namespace Cabify\Application\Handler;

use Cabify\Application\Command\RegisterJourneyCommand;
use Cabify\Application\Dto\RegisterJourneyResult;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\CarAvailabilitySnapshotRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use Cabify\Service\FairJourneyAssignmentPlanner;

/**
 * Nombre: RegisterJourneyHandler
 * Descripción: Caso de uso que registra un grupo y ejecuta asignación por orden de llegada cuando sea posible.
 * Parámetros de entrada: RegisterJourneyCommand
 * Parámetros de salida: RegisterJourneyResult
 * Método de uso: $handler->handle($command)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class RegisterJourneyHandler
{
    private JourneyRepositoryInterface $journeyRepository;

    private CarAvailabilityRepositoryInterface $carAvailabilityRepository;

    private FairJourneyAssignmentPlanner $assignmentPlanner;

    /**
     * Nombre: __construct
     * Descripción: Inyecta puertos de journeys y disponibilidad de coches.
     * Parámetros de entrada: JourneyRepositoryInterface $journeyRepository, CarAvailabilityRepositoryInterface $carAvailabilityRepository
     * Parámetros de salida: RegisterJourneyHandler
     * Método de uso: new RegisterJourneyHandler($journeyRepo, $carRepo)
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
     * Descripción: Registra el grupo y evalúa asignación en cola ordenada.
     * Parámetros de entrada: RegisterJourneyCommand $command
     * Parámetros de salida: RegisterJourneyResult
     * Método de uso: $result = $handler->handle($command)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function handle(RegisterJourneyCommand $command): RegisterJourneyResult
    {
        $group = new JourneyGroup($command->id(), $command->people());
        $this->journeyRepository->createWaitingJourney($group);

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

        $assignedCarIdForNewGroup = $assignments[$group->id()] ?? null;
        if ($assignedCarIdForNewGroup !== null) {
            return RegisterJourneyResult::assigned($group->id(), $assignedCarIdForNewGroup);
        }

        return RegisterJourneyResult::waiting($group->id());
    }
}
