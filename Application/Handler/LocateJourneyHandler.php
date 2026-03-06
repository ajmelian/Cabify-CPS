<?php

declare(strict_types=1);

namespace Cabify\Application\Handler;

use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Application\Query\LocateJourneyQuery;

/**
 * Nombre: LocateJourneyHandler
 * Descripción: Caso de uso para localizar el estado actual de un grupo de journey.
 * Parámetros de entrada: LocateJourneyQuery
 * Parámetros de salida: JourneyState
 * Método de uso: $handler->handle(new LocateJourneyQuery(7))
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class LocateJourneyHandler
{
    private JourneyRepositoryInterface $journeyRepository;

    /**
     * Nombre: __construct
     * Descripción: Inyecta puerto de consulta de journeys.
     * Parámetros de entrada: JourneyRepositoryInterface $journeyRepository
     * Parámetros de salida: LocateJourneyHandler
     * Método de uso: new LocateJourneyHandler($journeyRepository)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(JourneyRepositoryInterface $journeyRepository)
    {
        $this->journeyRepository = $journeyRepository;
    }

    /**
     * Nombre: handle
     * Descripción: Recupera estado del journey o lanza excepción si no existe.
     * Parámetros de entrada: LocateJourneyQuery $query
     * Parámetros de salida: JourneyState
     * Método de uso: $state = $handler->handle($query)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @throws JourneyNotFoundException
     */
    public function handle(LocateJourneyQuery $query): JourneyState
    {
        $journey = $this->journeyRepository->findJourneyById($query->id());
        if ($journey === null) {
            throw new JourneyNotFoundException(sprintf('Journey %d not found.', $query->id()));
        }

        return $journey;
    }
}
