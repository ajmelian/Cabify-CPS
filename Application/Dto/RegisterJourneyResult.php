<?php

declare(strict_types=1);

namespace Cabify\Application\Dto;

/**
 * Nombre: RegisterJourneyResult
 * Descripción: Resultado tipado del registro de journey, asignado o en espera.
 * Parámetros de entrada: int $groupId, string $status, int|null $carId
 * Parámetros de salida: DTO de resultado para capa HTTP.
 * Método de uso: RegisterJourneyResult::assigned(1, 3)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class RegisterJourneyResult
{
    private int $groupId;

    private string $status;

    private ?int $carId;

    /**
     * Nombre: __construct
     * Descripción: Crea un resultado de registro de journey.
     * Parámetros de entrada: int $groupId, string $status, int|null $carId
     * Parámetros de salida: RegisterJourneyResult
     * Método de uso: new RegisterJourneyResult(10, 'waiting', null)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    private function __construct(int $groupId, string $status, ?int $carId)
    {
        $this->groupId = $groupId;
        $this->status = $status;
        $this->carId = $carId;
    }

    /**
     * Nombre: assigned
     * Descripción: Fabrica resultado para journey asignado a coche.
     * Parámetros de entrada: int $groupId, int $carId
     * Parámetros de salida: RegisterJourneyResult
     * Método de uso: RegisterJourneyResult::assigned(3, 1)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public static function assigned(int $groupId, int $carId): self
    {
        return new self($groupId, 'assigned', $carId);
    }

    /**
     * Nombre: waiting
     * Descripción: Fabrica resultado para journey en espera.
     * Parámetros de entrada: int $groupId
     * Parámetros de salida: RegisterJourneyResult
     * Método de uso: RegisterJourneyResult::waiting(3)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public static function waiting(int $groupId): self
    {
        return new self($groupId, 'waiting', null);
    }

    /**
     * Nombre: groupId
     * Descripción: Devuelve identificador del grupo procesado.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $result->groupId()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function groupId(): int
    {
        return $this->groupId;
    }

    /**
     * Nombre: status
     * Descripción: Devuelve estado funcional del journey (assigned|waiting).
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: string
     * Método de uso: $result->status()
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
     * Nombre: carId
     * Descripción: Devuelve el coche asignado si existe.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int|null
     * Método de uso: $result->carId()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function carId(): ?int
    {
        return $this->carId;
    }
}
