<?php

declare(strict_types=1);

namespace Cabify\Entity;

use InvalidArgumentException;

/**
 * Nombre: JourneyGroup
 * Descripción: Entidad de dominio que representa un grupo de pasajeros en cola o asignado.
 * Parámetros de entrada: id (int), people (int)
 * Parámetros de salida: Instancia válida de JourneyGroup.
 * Método de uso: new JourneyGroup(1, 4)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JourneyGroup
{
    private int $id;

    private int $people;

    /**
     * Nombre: __construct
     * Descripción: Construye un grupo validando su identificador y número de personas.
     * Parámetros de entrada: int $id, int $people
     * Parámetros de salida: JourneyGroup
     * Método de uso: new JourneyGroup(7, 3)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(int $id, int $people)
    {
        if ($id < 1) {
            throw new InvalidArgumentException('Journey id must be a positive integer.');
        }

        if ($people < 1 || $people > 6) {
            throw new InvalidArgumentException('Journey people must be between 1 and 6.');
        }

        $this->id = $id;
        $this->people = $people;
    }

    /**
     * Nombre: id
     * Descripción: Devuelve el identificador del grupo.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $group->id()
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
     * Descripción: Devuelve el número de personas del grupo.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $group->people()
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
