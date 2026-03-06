<?php

declare(strict_types=1);

namespace Cabify\Application\Dto;

use InvalidArgumentException;

/**
 * Nombre: JourneyState
 * Descripción: Estado persistido de un grupo para operaciones de locate y gestión de cola.
 * Parámetros de entrada: int $id, int $people, string $status, int|null $assignedCarId
 * Parámetros de salida: DTO tipado de estado de journey.
 * Método de uso: new JourneyState(7, 4, 'assigned', 2)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class JourneyState
{
    private int $id;

    private int $people;

    private string $status;

    private ?int $assignedCarId;

    /**
     * Nombre: __construct
     * Descripción: Crea un estado de journey validando consistencia semántica.
     * Parámetros de entrada: int $id, int $people, string $status, int|null $assignedCarId
     * Parámetros de salida: JourneyState
     * Método de uso: new JourneyState(1, 3, 'waiting', null)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(int $id, int $people, string $status, ?int $assignedCarId)
    {
        if (!in_array($status, ['waiting', 'assigned'], true)) {
            throw new InvalidArgumentException('Journey status must be waiting or assigned.');
        }

        if ($status === 'assigned' && $assignedCarId === null) {
            throw new InvalidArgumentException('Assigned journey must include assigned car id.');
        }

        $this->id = $id;
        $this->people = $people;
        $this->status = $status;
        $this->assignedCarId = $assignedCarId;
    }

    /**
     * Nombre: id
     * Descripción: Devuelve el identificador del grupo.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $state->id()
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
     * Descripción: Devuelve el tamaño del grupo.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $state->people()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function people(): int
    {
        return $this->people;
    }

    /**
     * Nombre: status
     * Descripción: Devuelve estado funcional del grupo (waiting|assigned).
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: string
     * Método de uso: $state->status()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Nombre: assignedCarId
     * Descripción: Devuelve coche asignado o null si sigue en espera.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int|null
     * Método de uso: $state->assignedCarId()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function assignedCarId(): ?int
    {
        return $this->assignedCarId;
    }

    /**
     * Nombre: isWaiting
     * Descripción: Indica si el grupo sigue en cola de espera.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: bool
     * Método de uso: $state->isWaiting()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Nombre: isAssigned
     * Descripción: Indica si el grupo está asignado a un coche.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: bool
     * Método de uso: $state->isAssigned()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function isAssigned(): bool
    {
        return $this->status === 'assigned';
    }
}
