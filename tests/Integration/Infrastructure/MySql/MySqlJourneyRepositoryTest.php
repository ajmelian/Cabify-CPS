<?php

declare(strict_types=1);

namespace Cabify\Tests\Integration\Infrastructure\MySql;

use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Entity\JourneyGroup;
use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\MySqlJourneyRepository;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use PDO;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Nombre: MySqlJourneyRepositoryTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter MySqlJourneyRepositoryTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class MySqlJourneyRepositoryTest extends TestCase
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
            EnvLoader::load(dirname(__DIR__, 4) . '/.env');
            self::$connection = PdoConnectionFactory::createFromEnvironment();

            $migrationFiles = glob(dirname(__DIR__, 4) . '/migrations/*.sql');
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
     * Nombre: testCreatesWaitingJourneyAndRetrievesArrivalOrder
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testCreatesWaitingJourneyAndRetrievesArrivalOrder(): void
    {
        $repository = new MySqlJourneyRepository(self::$connection);

        $repository->createWaitingJourney(new JourneyGroup(2, 4));
        usleep(1000);
        $repository->createWaitingJourney(new JourneyGroup(1, 2));

        $waiting = $repository->findWaitingJourneys();

        self::assertCount(2, $waiting);
        self::assertSame(2, $waiting[0]->id());
        self::assertSame(1, $waiting[1]->id());
    }

    /**
     * Nombre: testFindsAvailableCarAndAssignsJourney
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testFindsAvailableCarAndAssignsJourney(): void
    {
        self::$connection->exec('INSERT INTO cars (id, seats) VALUES (1, 4), (2, 6)');

        $repository = new MySqlJourneyRepository(self::$connection);
        $repository->createWaitingJourney(new JourneyGroup(7, 4));
        $repository->assignJourneyToCar(7, 1);

        $carIdForTwoPeople = $repository->findFirstAvailableCarIdForPeople(2);
        self::assertSame(2, $carIdForTwoPeople);

        $carIdForSixPeople = $repository->findFirstAvailableCarIdForPeople(6);
        self::assertSame(2, $carIdForSixPeople);

        $carIdForSevenPeople = $repository->findFirstAvailableCarIdForPeople(7);
        self::assertNull($carIdForSevenPeople);
    }

    /**
     * Nombre: testThrowsDuplicateJourneyExceptionWhenIdAlreadyExists
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsDuplicateJourneyExceptionWhenIdAlreadyExists(): void
    {
        $repository = new MySqlJourneyRepository(self::$connection);
        $repository->createWaitingJourney(new JourneyGroup(3, 2));

        $this->expectException(DuplicateJourneyException::class);
        $repository->createWaitingJourney(new JourneyGroup(3, 4));
    }

    /**
     * Nombre: testFindJourneyByIdReturnsWaitingAndAssignedStates
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testFindJourneyByIdReturnsWaitingAndAssignedStates(): void
    {
        self::$connection->exec('INSERT INTO cars (id, seats) VALUES (11, 6)');

        $repository = new MySqlJourneyRepository(self::$connection);
        $repository->createWaitingJourney(new JourneyGroup(20, 4));
        $repository->createWaitingJourney(new JourneyGroup(21, 2));
        $repository->assignJourneyToCar(21, 11);

        $waiting = $repository->findJourneyById(20);
        self::assertNotNull($waiting);
        self::assertTrue($waiting->isWaiting());
        self::assertNull($waiting->assignedCarId());

        $assigned = $repository->findJourneyById(21);
        self::assertNotNull($assigned);
        self::assertTrue($assigned->isAssigned());
        self::assertSame(11, $assigned->assignedCarId());
    }

    /**
     * Nombre: testRemoveJourneyByIdDeletesExistingAndReturnsBoolean
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testRemoveJourneyByIdDeletesExistingAndReturnsBoolean(): void
    {
        $repository = new MySqlJourneyRepository(self::$connection);
        $repository->createWaitingJourney(new JourneyGroup(30, 3));

        self::assertTrue($repository->removeJourneyById(30));
        self::assertNull($repository->findJourneyById(30));
        self::assertFalse($repository->removeJourneyById(30));
    }
}
