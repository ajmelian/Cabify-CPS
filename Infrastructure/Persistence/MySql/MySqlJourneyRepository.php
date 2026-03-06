<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Persistence\MySql;

use Cabify\Application\Dto\JourneyState;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Port\CarAvailabilityRepositoryInterface;
use Cabify\Application\Port\CarAvailabilitySnapshotRepositoryInterface;
use Cabify\Application\Port\JourneyRepositoryInterface;
use Cabify\Entity\JourneyGroup;
use PDO;
use PDOException;

/**
 * Nombre: MySqlJourneyRepository
 * Descripción: Adaptador MySQL para gestión de journeys y consulta de disponibilidad de flota.
 * Parámetros de entrada: PDO
 * Parámetros de salida: Operaciones de cola/asignación sobre MySQL
 * Método de uso: new MySqlJourneyRepository($pdo)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class MySqlJourneyRepository implements
    JourneyRepositoryInterface,
    CarAvailabilityRepositoryInterface,
    CarAvailabilitySnapshotRepositoryInterface
{
    private PDO $connection;

    /**
     * Nombre: __construct
     * Descripción: Inyecta conexión PDO para operaciones de journeys.
     * Parámetros de entrada: PDO $connection
     * Parámetros de salida: MySqlJourneyRepository
     * Método de uso: new MySqlJourneyRepository($pdo)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Nombre: createWaitingJourney
     * Descripción: Inserta un grupo en estado waiting.
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
    public function createWaitingJourney(JourneyGroup $group): void
    {
        try {
            $statement = $this->connection->prepare(
                'INSERT INTO groups_queue (id, people, assigned_car_id, status) VALUES (:id, :people, NULL, :status)'
            );
            $statement->execute([
                ':id' => $group->id(),
                ':people' => $group->people(),
                ':status' => 'waiting',
            ]);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                throw new DuplicateJourneyException(sprintf('Journey with id %d already exists.', $group->id()), 0, $exception);
            }

            throw $exception;
        }
    }

    /**
     * Nombre: findWaitingJourneys
     * Descripción: Recupera grupos en espera por orden de llegada.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, JourneyGroup>
     * Método de uso: $repository->findWaitingJourneys()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @return array<int, JourneyGroup>
     */
    public function findWaitingJourneys(): array
    {
        $statement = $this->connection->prepare(
            "SELECT id, people FROM groups_queue WHERE status = 'waiting' ORDER BY created_at ASC, id ASC"
        );
        $statement->execute();

        $rows = $statement->fetchAll();

        $journeys = [];
        foreach ($rows as $row) {
            $journeys[] = new JourneyGroup((int) $row['id'], (int) $row['people']);
        }

        return $journeys;
    }

    /**
     * Nombre: assignJourneyToCar
     * Descripción: Asigna un grupo waiting a un coche concreto.
     * Parámetros de entrada: int $groupId, int $carId
     * Parámetros de salida: void
     * Método de uso: $repository->assignJourneyToCar(4, 9)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function assignJourneyToCar(int $groupId, int $carId): void
    {
        $statement = $this->connection->prepare(
            "UPDATE groups_queue SET assigned_car_id = :car_id, status = 'assigned' WHERE id = :group_id AND status = 'waiting'"
        );
        $statement->execute([
            ':car_id' => $carId,
            ':group_id' => $groupId,
        ]);
    }

    /**
     * Nombre: findFirstAvailableCarIdForPeople
     * Descripción: Busca el primer coche por id con plazas libres para un grupo.
     * Parámetros de entrada: int $people
     * Parámetros de salida: int|null
     * Método de uso: $repository->findFirstAvailableCarIdForPeople(3)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findFirstAvailableCarIdForPeople(int $people): ?int
    {
        foreach ($this->findAvailableSeatsByCarId() as $carId => $freeSeats) {
            if ($freeSeats >= $people) {
                return $carId;
            }
        }

        return null;
    }

    /**
     * Nombre: findAvailableSeatsByCarId
     * Descripción: Recupera snapshot de plazas libres por coche en una única consulta agregada.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, int>
     * Método de uso: $snapshot = $repository->findAvailableSeatsByCarId()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @return array<int, int>
     */
    public function findAvailableSeatsByCarId(): array
    {
        $statement = $this->connection->prepare(
            "SELECT c.id,
                    (c.seats - COALESCE(SUM(g.people), 0)) AS free_seats
             FROM cars c
             LEFT JOIN groups_queue g
               ON g.assigned_car_id = c.id
              AND g.status = 'assigned'
             GROUP BY c.id, c.seats
             ORDER BY c.id ASC"
        );
        $statement->execute();

        $rows = $statement->fetchAll();

        $availability = [];
        foreach ($rows as $row) {
            $availability[(int) $row['id']] = (int) $row['free_seats'];
        }

        return $availability;
    }

    /**
     * Nombre: findJourneyById
     * Descripción: Obtiene estado funcional de un grupo por identificador.
     * Parámetros de entrada: int $groupId
     * Parámetros de salida: JourneyState|null
     * Método de uso: $repository->findJourneyById(4)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findJourneyById(int $groupId): ?JourneyState
    {
        $statement = $this->connection->prepare(
            'SELECT id, people, status, assigned_car_id FROM groups_queue WHERE id = :group_id LIMIT 1'
        );
        $statement->execute([':group_id' => $groupId]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return new JourneyState(
            (int) $row['id'],
            (int) $row['people'],
            (string) $row['status'],
            isset($row['assigned_car_id']) ? (int) $row['assigned_car_id'] : null
        );
    }

    /**
     * Nombre: removeJourneyById
     * Descripción: Elimina un grupo de la cola o de asignación al hacer dropoff.
     * Parámetros de entrada: int $groupId
     * Parámetros de salida: bool
     * Método de uso: $repository->removeJourneyById(4)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function removeJourneyById(int $groupId): bool
    {
        $statement = $this->connection->prepare('DELETE FROM groups_queue WHERE id = :group_id');
        $statement->execute([':group_id' => $groupId]);

        return $statement->rowCount() > 0;
    }
}
