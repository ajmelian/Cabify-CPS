<?php

declare(strict_types=1);

namespace Cabify\Tests\Integration\Infrastructure\MySql;

use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * Nombre: MigrationRunnerIntegrationTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter MigrationRunnerIntegrationTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class MigrationRunnerIntegrationTest extends TestCase
{
    private const TEST_MIGRATION_VERSION = '9999_migration_runner_checksum_test.sql';

    /**
     * Nombre: testMigrationRunnerFailsWhenDatabaseConnectionIsUnavailable
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testMigrationRunnerFailsWhenDatabaseConnectionIsUnavailable(): void
    {
        $workspaceRoot = dirname(__DIR__, 4);
        $tempDirectory = $this->createTemporaryDirectory();
        $tempEnvFile = $tempDirectory . '/broken.env';

        file_put_contents($tempEnvFile, implode("\n", [
            'APP_ENV=test',
            'APP_DEBUG=0',
            'APP_PORT=9091',
            'DB_HOST=127.0.0.1',
            'DB_PORT=1',
            'DB_NAME=cabify',
            'DB_USER=invalid',
            'DB_PASS=invalid',
            'DB_CHARSET=utf8mb4',
            '',
        ]));

        $result = $this->runMigrationScript($workspaceRoot, [
            'CABIFY_ENV_FILE' => $tempEnvFile,
            'CABIFY_MIGRATIONS_PATH' => $workspaceRoot . '/migrations',
        ]);

        self::assertSame(1, $result['exitCode']);
        self::assertStringContainsString('Unable to connect to MySQL database.', $result['stderr']);
    }

    /**
     * Nombre: testMigrationRunnerDetectsChecksumMismatchOnAlreadyAppliedMigration
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testMigrationRunnerDetectsChecksumMismatchOnAlreadyAppliedMigration(): void
    {
        $workspaceRoot = dirname(__DIR__, 4);
        $defaultEnvFile = $workspaceRoot . '/.env';
        if (!is_file($defaultEnvFile)) {
            self::markTestSkipped('.env file is required for migration integration tests.');
        }

        $tempDirectory = $this->createTemporaryDirectory();
        $tempEnvFile = $tempDirectory . '/integration.env';
        $migrationsDirectory = $tempDirectory . '/migrations';

        if (!copy($defaultEnvFile, $tempEnvFile)) {
            throw new RuntimeException('Unable to create temporary .env file for migration integration test.');
        }

        if (!mkdir($migrationsDirectory) && !is_dir($migrationsDirectory)) {
            throw new RuntimeException('Unable to create temporary migrations directory.');
        }

        $migrationFile = $migrationsDirectory . '/' . self::TEST_MIGRATION_VERSION;
        file_put_contents(
            $migrationFile,
            "CREATE TABLE IF NOT EXISTS migration_runner_checksum_test (id INT PRIMARY KEY);\n"
        );

        $firstRun = $this->runMigrationScript($workspaceRoot, [
            'CABIFY_ENV_FILE' => $tempEnvFile,
            'CABIFY_MIGRATIONS_PATH' => $migrationsDirectory,
        ]);

        if ($firstRun['exitCode'] !== 0) {
            self::markTestSkipped('MySQL integration is not available in current environment.');
        }

        file_put_contents(
            $migrationFile,
            "CREATE TABLE IF NOT EXISTS migration_runner_checksum_test (id INT PRIMARY KEY, marker INT NOT NULL DEFAULT 0);\n"
        );

        try {
            $secondRun = $this->runMigrationScript($workspaceRoot, [
                'CABIFY_ENV_FILE' => $tempEnvFile,
                'CABIFY_MIGRATIONS_PATH' => $migrationsDirectory,
            ]);

            self::assertSame(1, $secondRun['exitCode']);
            self::assertStringContainsString(
                'Migration checksum mismatch detected for ' . self::TEST_MIGRATION_VERSION,
                $secondRun['stderr']
            );
        } finally {
            $this->cleanupTestMigrationArtifacts($tempEnvFile);
        }
    }

    private function cleanupTestMigrationArtifacts(string $envFilePath): void
    {
        try {
            foreach (['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_CHARSET', 'DB_USER', 'DB_PASS'] as $key) {
                putenv($key);
                unset($_ENV[$key], $_SERVER[$key]);
            }

            EnvLoader::load($envFilePath);
            $pdo = PdoConnectionFactory::createFromEnvironment();

            $statement = $pdo->prepare('DELETE FROM schema_migrations WHERE version = :version');
            $statement->execute([':version' => self::TEST_MIGRATION_VERSION]);
            $pdo->exec('DROP TABLE IF EXISTS migration_runner_checksum_test');
        } catch (Throwable) {
            // Cleanup must not fail the test result once assertions have passed.
        }
    }

    private function createTemporaryDirectory(): string
    {
        $basePath = sys_get_temp_dir() . '/cabify_migration_test_' . bin2hex(random_bytes(8));

        if (!mkdir($basePath, 0777, true) && !is_dir($basePath)) {
            throw new RuntimeException('Unable to create temporary directory.');
        }

        return $basePath;
    }

    /**
     * @param array<string, string> $extraEnvironment
     * @return array{exitCode:int,stdout:string,stderr:string}
     */
    private function runMigrationScript(string $workspaceRoot, array $extraEnvironment): array
    {
        $baseEnvironment = getenv();
        if ($baseEnvironment === false) {
            $baseEnvironment = [];
        }

        foreach (['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_CHARSET', 'DB_USER', 'DB_PASS'] as $key) {
            unset($baseEnvironment[$key]);
        }

        $process = proc_open(
            ['php', $workspaceRoot . '/scripts/migrate.php'],
            [
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $workspaceRoot,
            array_merge($baseEnvironment, $extraEnvironment)
        );

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to execute migration process.');
        }

        $stdout = stream_get_contents($pipes[1]) ?: '';
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [
            'exitCode' => $exitCode,
            'stdout' => $stdout,
            'stderr' => $stderr,
        ];
    }
}
