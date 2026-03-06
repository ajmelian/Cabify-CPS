<?php

declare(strict_types=1);

namespace Cabify\Entity;

use InvalidArgumentException;

/**
 * Nombre: Car
 * Descripción: Entidad de dominio que representa un coche con capacidad fija.
 * Parámetros de entrada: id (int), seats (int)
 * Parámetros de salida: Instancia de Car válida
 * Método de uso: new Car(1, 4)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class Car
{
    private int $id;

    private int $seats;

    /**
     * Nombre: __construct
     * Descripción: Crea un coche validando su identificador y número de plazas.
     * Parámetros de entrada: int $id, int $seats
     * Parámetros de salida: Car
     * Método de uso: new Car(10, 6)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(int $id, int $seats)
    {
        if ($id < 1) {
            throw new InvalidArgumentException('Car id must be a positive integer.');
        }

        if ($seats < 1 || $seats > 8) {
            throw new InvalidArgumentException('Car seats must be between 1 and 8.');
        }

        $this->id = $id;
        $this->seats = $seats;
    }

    /**
     * Nombre: id
     * Descripción: Devuelve el identificador del coche.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $car->id()
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
     * Nombre: seats
     * Descripción: Devuelve la capacidad total de plazas del coche.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $car->seats()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function seats(): int
    {
        return $this->seats;
    }
}
