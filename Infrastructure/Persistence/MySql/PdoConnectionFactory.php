<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Persistence\MySql;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Nombre: PdoConnectionFactory
 * Descripción: Crea conexiones PDO seguras para MySQL usando variables de entorno.
 * Parámetros de entrada: Variables DB_HOST, DB_PORT, DB_NAME, DB_CHARSET, DB_USER, DB_PASS
 * Parámetros de salida: PDO configurado con excepciones y UTF-8
 * Método de uso: $pdo = PdoConnectionFactory::createFromEnvironment()
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PdoConnectionFactory
{
    /**
     * Nombre: createFromEnvironment
     * Descripción: Construye una conexión PDO a MySQL leyendo configuración desde entorno.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: PDO
     * Método de uso: PdoConnectionFactory::createFromEnvironment()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public static function createFromEnvironment(): PDO
    {
        $host = self::requiredEnv('DB_HOST');
        $port = self::requiredEnv('DB_PORT');
        $dbName = self::requiredEnv('DB_NAME');
        $charset = self::requiredEnv('DB_CHARSET');
        $user = self::requiredEnv('DB_USER');
        $password = self::requiredEnv('DB_PASS');

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbName, $charset);

        try {
            return new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to connect to MySQL database.', 0, $exception);
        }
    }

    /**
     * Nombre: requiredEnv
     * Descripción: Recupera una variable de entorno obligatoria para la conexión.
     * Parámetros de entrada: string $key
     * Parámetros de salida: string
     * Método de uso: self::requiredEnv('DB_HOST')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    private static function requiredEnv(string $key): string
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            throw new RuntimeException(sprintf('Missing required environment variable: %s', $key));
        }

        return $value;
    }
}
