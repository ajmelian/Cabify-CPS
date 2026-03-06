<?php

declare(strict_types=1);

/**
 * Nombre: check_coverage
 * Descripción: Valida que la cobertura total de líneas cumpla un umbral mínimo leyendo Clover XML.
 * Parámetros de entrada: ruta al clover.xml y porcentaje mínimo esperado
 * Parámetros de salida: código de proceso 0 si cumple, 1 si no cumple o hay error
 * Método de uso: php scripts/check_coverage.php build/coverage/clover.xml 80
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */

if ($argc < 3) {
    fwrite(STDERR, "Usage: php scripts/check_coverage.php <clover.xml> <minimum_percentage>\n");
    exit(1);
}

$cloverPath = $argv[1];
$minimumRaw = $argv[2];

if (!is_file($cloverPath)) {
    fwrite(STDERR, sprintf("Coverage file not found: %s\n", $cloverPath));
    exit(1);
}

if (!is_numeric($minimumRaw)) {
    fwrite(STDERR, sprintf("Invalid minimum percentage: %s\n", $minimumRaw));
    exit(1);
}

$minimum = (float) $minimumRaw;
if ($minimum <= 0.0 || $minimum > 100.0) {
    fwrite(STDERR, "Minimum percentage must be > 0 and <= 100.\n");
    exit(1);
}

$xml = @simplexml_load_file($cloverPath);
if ($xml === false) {
    fwrite(STDERR, sprintf("Unable to parse Clover XML: %s\n", $cloverPath));
    exit(1);
}

$metricsNodes = $xml->xpath('/coverage/project/metrics');
if ($metricsNodes === false || !isset($metricsNodes[0])) {
    fwrite(STDERR, "Coverage metrics not found in Clover XML.\n");
    exit(1);
}

$metrics = $metricsNodes[0];
$coveredStatements = (int) ($metrics['coveredstatements'] ?? 0);
$totalStatements = (int) ($metrics['statements'] ?? 0);

if ($totalStatements === 0) {
    fwrite(STDERR, "Total statements is zero, cannot calculate coverage.\n");
    exit(1);
}

$coverage = ($coveredStatements / $totalStatements) * 100.0;

fwrite(
    STDOUT,
    sprintf(
        "Line coverage: %.2f%% (%d/%d). Required minimum: %.2f%%.\n",
        $coverage,
        $coveredStatements,
        $totalStatements,
        $minimum
    )
);

if ($coverage < $minimum) {
    fwrite(STDERR, "Coverage threshold not met.\n");
    exit(1);
}

fwrite(STDOUT, "Coverage threshold satisfied.\n");
