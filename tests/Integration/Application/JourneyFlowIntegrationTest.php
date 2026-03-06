<?php

declare(strict_types=1);

namespace Cabify\Tests\Integration\Application;

use Cabify\Application\Command\DropoffJourneyCommand;
use Cabify\Application\Command\RegisterJourneyCommand;
use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Handler\DropoffJourneyHandler;
use Cabify\Application\Handler\LocateJourneyHandler;
use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Application\Query\LocateJourneyQuery;
use Cabify\Entity\Car;
use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\MySqlCarRepository;
use Cabify\Infrastructure\Persistence\MySql\MySqlJourneyRepository;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use PDO;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Nombre: JourneyFlowIntegrationTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter JourneyFlowIntegrationTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JourneyFlowIntegrationTest extends TestCase
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
     * Nombre: testJourneyFlowReassignsWaitingGroupAfterDropoff
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testJourneyFlowReassignsWaitingGroupAfterDropoff(): void
    {
        $carRepository = new MySqlCarRepository(self::$connection);
        $journeyRepository = new MySqlJourneyRepository(self::$connection);

        $registerHandler = new RegisterJourneyHandler($journeyRepository, $journeyRepository);
        $dropoffHandler = new DropoffJourneyHandler($journeyRepository, $journeyRepository);
        $locateHandler = new LocateJourneyHandler($journeyRepository);

        $carRepository->replaceFleet([new Car(1, 4)]);

        $firstResult = $registerHandler->handle(new RegisterJourneyCommand(1, 4));
        self::assertSame('assigned', $firstResult->status());
        self::assertSame(1, $firstResult->carId());

        $secondResult = $registerHandler->handle(new RegisterJourneyCommand(2, 2));
        self::assertSame('waiting', $secondResult->status());

        $secondBefore = $locateHandler->handle(new LocateJourneyQuery(2));
        self::assertTrue($secondBefore->isWaiting());

        $dropoffHandler->handle(new DropoffJourneyCommand(1));

        $secondAfter = $locateHandler->handle(new LocateJourneyQuery(2));
        self::assertTrue($secondAfter->isAssigned());
        self::assertSame(1, $secondAfter->assignedCarId());

        $this->expectException(JourneyNotFoundException::class);
        $locateHandler->handle(new LocateJourneyQuery(1));
    }

    /**
     * Nombre: testDropoffKeepsArrivalOrderWhenCapacityIsLimited
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testDropoffKeepsArrivalOrderWhenCapacityIsLimited(): void
    {
        $carRepository = new MySqlCarRepository(self::$connection);
        $journeyRepository = new MySqlJourneyRepository(self::$connection);

        $registerHandler = new RegisterJourneyHandler($journeyRepository, $journeyRepository);
        $dropoffHandler = new DropoffJourneyHandler($journeyRepository, $journeyRepository);
        $locateHandler = new LocateJourneyHandler($journeyRepository);

        $carRepository->replaceFleet([new Car(1, 4)]);

        $registerHandler->handle(new RegisterJourneyCommand(10, 4));
        $registerHandler->handle(new RegisterJourneyCommand(11, 3));
        $registerHandler->handle(new RegisterJourneyCommand(12, 2));

        $dropoffHandler->handle(new DropoffJourneyCommand(10));

        $journey11 = $locateHandler->handle(new LocateJourneyQuery(11));
        $journey12 = $locateHandler->handle(new LocateJourneyQuery(12));

        self::assertTrue($journey11->isAssigned());
        self::assertSame(1, $journey11->assignedCarId());
        self::assertTrue($journey12->isWaiting());
    }
}
