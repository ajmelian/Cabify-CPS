<?php

declare(strict_types=1);

use Cabify\Application\Command\RegisterJourneyCommand;
use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Entity\Car;
use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Persistence\MySql\MySqlCarRepository;
use Cabify\Infrastructure\Persistence\MySql\MySqlJourneyRepository;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Nombre: benchmark_assignments
 * Descripción: Benchmark local de asignación de journeys para validar comportamiento en cargas medias/altas.
 * Parámetros de entrada: --cars, --groups, --max-group-size
 * Parámetros de salida: métricas de tiempo y conteos assigned/waiting
 * Método de uso: php scripts/benchmark_assignments.php --cars=2000 --groups=20000 --max-group-size=4
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */

$options = getopt('', ['cars::', 'groups::', 'max-group-size::']);
$carsCount = max(1, (int) ($options['cars'] ?? 2000));
$groupsCount = max(1, (int) ($options['groups'] ?? 20000));
$maxGroupSize = max(1, min(6, (int) ($options['max-group-size'] ?? 4)));

EnvLoader::load(dirname(__DIR__) . '/.env');
$connection = PdoConnectionFactory::createFromEnvironment();

$migrationFiles = glob(dirname(__DIR__) . '/migrations/*.sql');
if ($migrationFiles === false) {
    fwrite(STDERR, "Unable to read migrations directory.\n");
    exit(1);
}
sort($migrationFiles, SORT_NATURAL);
foreach ($migrationFiles as $migrationFile) {
    $sql = file_get_contents($migrationFile);
    if ($sql !== false) {
        $connection->exec($sql);
    }
}

$carRepository = new MySqlCarRepository($connection);
$journeyRepository = new MySqlJourneyRepository($connection);
$registerHandler = new RegisterJourneyHandler($journeyRepository, $journeyRepository);

$cars = [];
for ($carId = 1; $carId <= $carsCount; $carId++) {
    $cars[] = new Car($carId, 4);
}
$carRepository->replaceFleet($cars);

$assigned = 0;
$waiting = 0;

mt_srand(20260304);
$startNs = hrtime(true);

for ($journeyId = 1; $journeyId <= $groupsCount; $journeyId++) {
    $people = mt_rand(1, $maxGroupSize);
    $result = $registerHandler->handle(new RegisterJourneyCommand($journeyId, $people));

    if ($result->status() === 'assigned') {
        $assigned++;
    } else {
        $waiting++;
    }
}

$elapsedSeconds = (hrtime(true) - $startNs) / 1_000_000_000;

printf("Cars: %d\n", $carsCount);
printf("Journeys: %d\n", $groupsCount);
printf("Max group size: %d\n", $maxGroupSize);
printf("Assigned: %d\n", $assigned);
printf("Waiting: %d\n", $waiting);
printf("Elapsed seconds: %.4f\n", $elapsedSeconds);
printf("Throughput (journeys/s): %.2f\n", $groupsCount / max($elapsedSeconds, 0.0001));
