<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Persistence\MySql;

use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use PDOException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Nombre: PdoConnectionFactoryTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter PdoConnectionFactoryTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PdoConnectionFactoryTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $originalEnvironment = [];

    /** @var string[] */
    private array $managedKeys = [
        'DB_HOST',
        'DB_PORT',
        'DB_NAME',
        'DB_CHARSET',
        'DB_USER',
        'DB_PASS',
    ];

    protected function setUp(): void
    {
        foreach ($this->managedKeys as $key) {
            $this->originalEnvironment[$key] = getenv($key);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->managedKeys as $key) {
            $originalValue = $this->originalEnvironment[$key];

            if ($originalValue === false) {
                putenv($key);
                unset($_ENV[$key], $_SERVER[$key]);
                continue;
            }

            putenv($key . '=' . $originalValue);
            $_ENV[$key] = $originalValue;
            $_SERVER[$key] = $originalValue;
        }
    }

    /**
     * Nombre: testThrowsExceptionWhenRequiredEnvironmentVariableIsMissing
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenRequiredEnvironmentVariableIsMissing(): void
    {
        $this->configureEnvironment([
            'DB_HOST' => null,
            'DB_PORT' => '3306',
            'DB_NAME' => 'cabify',
            'DB_CHARSET' => 'utf8mb4',
            'DB_USER' => 'root',
            'DB_PASS' => 'root',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing required environment variable: DB_HOST');

        PdoConnectionFactory::createFromEnvironment();
    }

    /**
     * Nombre: testThrowsExceptionWhenConnectionCannotBeEstablished
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testThrowsExceptionWhenConnectionCannotBeEstablished(): void
    {
        $this->configureEnvironment([
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '1',
            'DB_NAME' => 'cabify',
            'DB_CHARSET' => 'utf8mb4',
            'DB_USER' => 'invalid',
            'DB_PASS' => 'invalid',
        ]);

        try {
            PdoConnectionFactory::createFromEnvironment();
            self::fail('Expected RuntimeException was not thrown.');
        } catch (RuntimeException $exception) {
            self::assertSame('Unable to connect to MySQL database.', $exception->getMessage());
            self::assertInstanceOf(PDOException::class, $exception->getPrevious());
        }
    }

    /**
     * @param array<string, string|null> $environment
     */
    private function configureEnvironment(array $environment): void
    {
        foreach ($environment as $key => $value) {
            if ($value === null) {
                putenv($key);
                unset($_ENV[$key], $_SERVER[$key]);
                continue;
            }

            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
