<?php

declare(strict_types=1);

namespace Cabify\Tests\Unit\Infrastructure\Config;

use Cabify\Infrastructure\Config\EnvLoader;
use PHPUnit\Framework\TestCase;

/**
 * Nombre: EnvLoaderTest
 * Descripción: Clase de pruebas automatizada con documentación homogeneizada.
 * Parámetros de entrada: Dependencias internas de la prueba.
 * Parámetros de salida: Validaciones PHPUnit.
 * Método de uso: vendor/bin/phpunit --filter EnvLoaderTest
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class EnvLoaderTest extends TestCase
{
    /** @var array<string, string> */
    private array $managedKeys = [];

    /** @var array<string, mixed> */
    private array $envBackup;

    /** @var array<string, mixed> */
    private array $serverBackup;

    protected function setUp(): void
    {
        $this->managedKeys = [];
        $this->envBackup = $_ENV;
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        foreach ($this->managedKeys as $key) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);
        }

        $_ENV = $this->envBackup;
        $_SERVER = $this->serverBackup;
    }

    /**
     * Nombre: testLoadIgnoresMissingFile
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testLoadIgnoresMissingFile(): void
    {
        EnvLoader::load('/tmp/cabify_env_missing_' . uniqid('', true));

        self::assertTrue(true);
    }

    /**
     * Nombre: testLoadParsesValidEntriesAndSkipsCommentsOrInvalidLines
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testLoadParsesValidEntriesAndSkipsCommentsOrInvalidLines(): void
    {
        $keyOne = 'CABIFY_ENV_' . bin2hex(random_bytes(6));
        $keyTwo = 'CABIFY_SERVER_' . bin2hex(random_bytes(6));
        $this->managedKeys[] = $keyOne;
        $this->managedKeys[] = $keyTwo;

        $envFile = tempnam(sys_get_temp_dir(), 'cabify_env_');
        self::assertNotFalse($envFile);

        file_put_contents(
            $envFile,
            "# comment line\n\nINVALID_LINE\n{$keyOne}=value-one\n{$keyTwo}=value-two\n"
        );

        EnvLoader::load($envFile);

        self::assertSame('value-one', getenv($keyOne));
        self::assertSame('value-two', getenv($keyTwo));
        self::assertSame('value-one', $_ENV[$keyOne]);
        self::assertSame('value-two', $_SERVER[$keyTwo]);

        unlink($envFile);
    }

    /**
     * Nombre: testLoadDoesNotOverrideExistingEnvironmentVariables
     * Descripción: Método de prueba/documentación homogeneizada.
     * Parámetros de entrada: Según firma del método.
     * Parámetros de salida: void
     * Método de uso: Se ejecuta mediante PHPUnit.
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function testLoadDoesNotOverrideExistingEnvironmentVariables(): void
    {
        $key = 'CABIFY_LOCKED_' . bin2hex(random_bytes(6));
        $this->managedKeys[] = $key;
        putenv($key . '=already-set');

        $envFile = tempnam(sys_get_temp_dir(), 'cabify_env_');
        self::assertNotFalse($envFile);
        file_put_contents($envFile, $key . "=new-value\n");

        EnvLoader::load($envFile);

        self::assertSame('already-set', getenv($key));

        unlink($envFile);
    }
}
