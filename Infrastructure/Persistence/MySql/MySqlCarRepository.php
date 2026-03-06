<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Persistence\MySql;

use Cabify\Application\Port\CarRepositoryInterface;
use Cabify\Entity\Car;
use PDO;
use Throwable;

/**
 * Nombre: MySqlCarRepository
 * Descripción: Adaptador MySQL para persistir y leer la flota de coches mediante PDO.
 * Parámetros de entrada: PDO y entidades Car
 * Parámetros de salida: Entidades Car persistidas/recuperadas
 * Método de uso: $repo = new MySqlCarRepository($pdo)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class MySqlCarRepository implements CarRepositoryInterface
{
    private PDO $connection;

    /**
     * Nombre: __construct
     * Descripción: Inyecta conexión PDO para operaciones de persistencia.
     * Parámetros de entrada: PDO $connection
     * Parámetros de salida: MySqlCarRepository
     * Método de uso: new MySqlCarRepository($pdo)
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
     * Nombre: replaceFleet
     * Descripción: Sustituye toda la flota en una transacción atómica.
     * Parámetros de entrada: array<int, Car> $cars
     * Parámetros de salida: void
     * Método de uso: $repo->replaceFleet([$car])
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function replaceFleet(array $cars): void
    {
        $this->connection->beginTransaction();

        try {
            // Reset completo del estado operativo al cargar nueva flota.
            $this->connection->exec('DELETE FROM groups_queue');
            $this->connection->exec('DELETE FROM cars');
            $statement = $this->connection->prepare('INSERT INTO cars (id, seats) VALUES (:id, :seats)');

            foreach ($cars as $car) {
                $statement->execute([
                    ':id' => $car->id(),
                    ':seats' => $car->seats(),
                ]);
            }

            $this->connection->commit();
        } catch (Throwable $throwable) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $throwable;
        }
    }

    /**
     * Nombre: findAll
     * Descripción: Recupera la flota completa ordenada por id ascendente.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, Car>
     * Método de uso: $cars = $repo->findAll()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function findAll(): array
    {
        $statement = $this->connection->prepare('SELECT id, seats FROM cars ORDER BY id ASC');
        $statement->execute();

        $rows = $statement->fetchAll();

        $cars = [];
        foreach ($rows as $row) {
            $cars[] = new Car((int) $row['id'], (int) $row['seats']);
        }

        return $cars;
    }
}
