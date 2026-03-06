<?php

declare(strict_types=1);

use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Crea la tabla interna de control de migraciones si no existe.
 */
function ensureSchemaMigrationsTable(\PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS schema_migrations (
            version VARCHAR(255) NOT NULL,
            checksum CHAR(64) NOT NULL,
            applied_at TIMESTAMP(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
            PRIMARY KEY (version)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

/**
 * Recupera migraciones ya aplicadas con su checksum.
 *
 * @return array<string, string>
 */
function loadAppliedMigrations(\PDO $pdo): array
{
    $statement = $pdo->query('SELECT version, checksum FROM schema_migrations');
    if ($statement === false) {
        throw new \RuntimeException('Unable to read applied migrations.');
    }

    $rows = $statement->fetchAll();
    $applied = [];
    foreach ($rows as $row) {
        $applied[(string) $row['version']] = (string) $row['checksum'];
    }

    return $applied;
}

/**
 * Aplica una migración y la registra en schema_migrations.
 */
function applyMigration(\PDO $pdo, string $version, string $checksum, string $sql): void
{
    $pdo->exec($sql);

    $statement = $pdo->prepare(
        'INSERT INTO schema_migrations (version, checksum) VALUES (:version, :checksum)'
    );
    $statement->execute([
        ':version' => $version,
        ':checksum' => $checksum,
    ]);
}

try {
    $envPath = getenv('CABIFY_ENV_FILE');
    if ($envPath === false || $envPath === '') {
        $envPath = dirname(__DIR__) . '/.env';
    }
    EnvLoader::load($envPath);

    $pdo = PdoConnectionFactory::createFromEnvironment();
    ensureSchemaMigrationsTable($pdo);
    $appliedMigrations = loadAppliedMigrations($pdo);

    $migrationsPath = getenv('CABIFY_MIGRATIONS_PATH');
    if ($migrationsPath === false || $migrationsPath === '') {
        $migrationsPath = dirname(__DIR__) . '/migrations';
    }
    $files = glob($migrationsPath . '/*.sql');
    if ($files === false) {
        throw new \RuntimeException('Unable to read migrations directory.');
    }
    sort($files, SORT_NATURAL);

    $appliedCount = 0;
    $skippedCount = 0;

    foreach ($files as $file) {
        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new \RuntimeException(sprintf('Unable to read migration file: %s', basename($file)));
        }

        $version = basename($file);
        $checksum = hash('sha256', $sql);
        $appliedChecksum = $appliedMigrations[$version] ?? null;

        if ($appliedChecksum !== null) {
            if ($appliedChecksum !== $checksum) {
                throw new \RuntimeException(
                    sprintf('Migration checksum mismatch detected for %s. Review migration history before continuing.', $version)
                );
            }

            $skippedCount++;
            fwrite(STDOUT, sprintf("Skipped migration (already applied): %s\n", $version));
            continue;
        }

        applyMigration($pdo, $version, $checksum, $sql);
        $appliedCount++;
        fwrite(STDOUT, sprintf("Applied migration: %s\n", $version));
    }

    fwrite(
        STDOUT,
        sprintf("Migrations finished. Applied: %d. Skipped: %d.\n", $appliedCount, $skippedCount)
    );
} catch (\Throwable $throwable) {
    fwrite(STDERR, sprintf("Migration error: %s\n", $throwable->getMessage()));
    exit(1);
}
