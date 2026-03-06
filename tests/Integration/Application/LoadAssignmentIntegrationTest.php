<?php

declare(strict_types=1);

namespace Cabify\Tests\Integration\Application;

use Cabify\Application\Command\RegisterJourneyCommand;
use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Entity\Car;
use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\MySqlCarRepository;
use Cabify\Infrastructure\Persistence\MySql\MySqlJourneyRepository;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use PDO;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Nombre: LoadAssignmentIntegrationTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter LoadAssignmentIntegrationTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class LoadAssignmentIntegrationTest extends TestCase
{
    private static ?PDO $connection = null;

    /**
     * Nombre: setUpBeforeClass
     * Descripción: Prepara conexión MySQL y aplica migraciones antes de la suite de integración.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: void
     * Método de uso: PHPUnit invoca automáticamente el ciclo de vida de la clase.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public static function setUpBeforeClass(): void
    {
        try {
            EnvLoader::load(dirname(__DIR__, 3) . '/.env');
            self::$connection = PdoConnectionFactory::createFromEnvironment();

            $migrationFiles = glob(dirname(__DIR__, 3) . '/migrations/*.sql');
            if ($migrationFiles !== false) {
                sort($migrationFiles, SORT_NATURAL);
                foreach ($migrationFiles as $migrationFile) {
                    $migrationSql = file_get_contents($migrationFile);
                    if ($migrationSql !== false) {
                        self::$connection->exec($migrationSql);
                    }
                }
            }
        } catch (Throwable) {
            self::$connection = null;
        }
    }

    protected function setUp(): void
    {
        if (self::$connection === null) {
            self::markTestSkipped('MySQL integration is not available in current environment.');
        }

        self::$connection->exec('DELETE FROM groups_queue');
        self::$connection->exec('DELETE FROM cars');
    }

    /**
     * Nombre: testHandlesMediumLoadAndKeepsStateConsistent
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testHandlesMediumLoadAndKeepsStateConsistent(): void
    {
        $carRepository = new MySqlCarRepository(self::$connection);
        $journeyRepository = new MySqlJourneyRepository(self::$connection);
        $handler = new RegisterJourneyHandler($journeyRepository, $journeyRepository);

        $cars = [];
        for ($carId = 1; $carId <= 100; $carId++) {
            $cars[] = new Car($carId, 4);
        }
        $carRepository->replaceFleet($cars);

        $assigned = 0;
        $waiting = 0;

        for ($journeyId = 1; $journeyId <= 600; $journeyId++) {
            $result = $handler->handle(new RegisterJourneyCommand($journeyId, 1));
            if ($result->status() === 'assigned') {
                $assigned++;
            } else {
                $waiting++;
            }
        }

        self::assertSame(400, $assigned);
        self::assertSame(200, $waiting);

        $assignedCount = (int) self::$connection->query("SELECT COUNT(*) FROM groups_queue WHERE status = 'assigned'")->fetchColumn();
        $waitingCount = (int) self::$connection->query("SELECT COUNT(*) FROM groups_queue WHERE status = 'waiting'")->fetchColumn();

        self::assertSame(400, $assignedCount);
        self::assertSame(200, $waitingCount);
    }
}
