<?php

declare(strict_types=1);

namespace Cabify\Application\Command;

/**
 * Nombre: RegisterJourneyCommand
 * Descripción: Comando de aplicación para registrar un nuevo grupo en el sistema.
 * Parámetros de entrada: int $id, int $people
 * Parámetros de salida: Objeto comando inmutable.
 * Método de uso: new RegisterJourneyCommand(8, 4)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class RegisterJourneyCommand
{
    private int $id;

    private int $people;

    /**
     * Nombre: __construct
     * Descripción: Inicializa el comando con id de grupo y tamaño.
     * Parámetros de entrada: int $id, int $people
     * Parámetros de salida: RegisterJourneyCommand
     * Método de uso: new RegisterJourneyCommand(2, 5)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(int $id, int $people)
    {
        $this->id = $id;
        $this->people = $people;
    }

    /**
     * Nombre: id
     * Descripción: Devuelve el identificador del grupo del comando.
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

    /**
     * Nombre: people
     * Descripción: Devuelve el número de personas del grupo del comando.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $command->people()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function people(): int
    {
        return $this->people;
    }
}
