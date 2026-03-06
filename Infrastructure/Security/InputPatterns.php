<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Security;

/**
 * Nombre: InputPatterns
 * Descripción: Centraliza regex de validación para entradas externas.
 * Parámetros de entrada: Ninguno
 * Parámetros de salida: Expresiones regulares seguras
 * Método de uso: InputPatterns::POSITIVE_ID
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class InputPatterns
{
    public const INTEGER = '/^-?[0-9]+$/';

    public const POSITIVE_ID = '/^[1-9][0-9]*$/';

    public const PEOPLE = '/^[1-6]$/';

    public const CAR_SEATS = '/^[1-8]$/';

    /**
     * Nombre: __construct
     * Descripción: Evita instanciación de clase estática de patrones de entrada.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: InputPatterns
     * Método de uso: No aplica (constructor privado)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    private function __construct()
    {
    }
}
