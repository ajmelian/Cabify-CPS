<?php

declare(strict_types=1);

namespace Cabify\Application\Exception;

use RuntimeException;

/**
 * Nombre: JourneyNotFoundException
 * Descripción: Excepción de aplicación para grupos inexistentes en operaciones de locate/dropoff.
 * Parámetros de entrada: string $message
 * Parámetros de salida: Excepción tipada.
 * Método de uso: throw new JourneyNotFoundException('...')
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JourneyNotFoundException extends RuntimeException
{
}
