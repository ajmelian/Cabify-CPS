<?php

declare(strict_types=1);

namespace Cabify\Application\Exception;

use RuntimeException;

/**
 * Nombre: DuplicateJourneyException
 * Descripción: Excepción de aplicación cuando se intenta registrar un grupo duplicado.
 * Parámetros de entrada: string $message
 * Parámetros de salida: Excepción de aplicación tipada.
 * Método de uso: throw new DuplicateJourneyException('...')
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DuplicateJourneyException extends RuntimeException
{
}
