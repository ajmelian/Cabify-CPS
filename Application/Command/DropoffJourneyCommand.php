<?php

declare(strict_types=1);

namespace Cabify\Application\Command;

/**
 * Nombre: DropoffJourneyCommand
 * Descripción: Comando de aplicación para finalizar un journey y liberar plazas.
 * Parámetros de entrada: int $id
 * Parámetros de salida: Objeto comando inmutable.
 * Método de uso: new DropoffJourneyCommand(7)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class DropoffJourneyCommand
{
    private int $id;

    /**
     * Nombre: __construct
     * Descripción: Inicializa el comando de dropoff para un grupo concreto.
     * Parámetros de entrada: int $id
     * Parámetros de salida: DropoffJourneyCommand
     * Método de uso: new DropoffJourneyCommand(12)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Nombre: id
     * Descripción: Devuelve identificador del grupo a finalizar.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $command->id()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function id(): int
    {
        return $this->id;
    }
}
