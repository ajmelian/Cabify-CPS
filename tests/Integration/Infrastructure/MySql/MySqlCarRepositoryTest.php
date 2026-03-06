<?php

declare(strict_types=1);

namespace Cabify\Tests\Integration\Infrastructure\MySql;

use Cabify\Entity\Car;
use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\MySqlCarRepository;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use PDO;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Nombre: MySqlCarRepositoryTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter MySqlCarRepositoryTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class MySqlCarRepositoryTest extends TestCase
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
     * Nombre: testReplaceFleetPersistsCarsAndClearsQueue
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testReplaceFleetPersistsCarsAndClearsQueue(): void
    {
        self::$connection->exec('INSERT INTO cars (id, seats) VALUES (10, 4)');
        self::$connection->exec("INSERT INTO groups_queue (id, people, assigned_car_id, status) VALUES (7, 4, 10, 'assigned')");

        $repository = new MySqlCarRepository(self::$connection);
        $repository->replaceFleet([
            new Car(1, 4),
            new Car(2, 5),
        ]);

        $cars = $repository->findAll();

        self::assertCount(2, $cars);
        self::assertSame(1, $cars[0]->id());
        self::assertSame(2, $cars[1]->id());

        $statement = self::$connection->query('SELECT COUNT(*) AS total FROM groups_queue');
        $result = $statement->fetch();
        self::assertSame('0', (string) $result['total']);
    }
}
