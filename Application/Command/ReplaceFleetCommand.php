<?php

declare(strict_types=1);

namespace Cabify\Application\Command;

use Cabify\Entity\Car;

/**
 * Nombre: ReplaceFleetCommand
 * Descripción: Comando de aplicación para reemplazar la flota completa de coches.
 * Parámetros de entrada: array<int, Car> $cars
 * Parámetros de salida: Objeto comando inmutable para el caso de uso.
 * Método de uso: new ReplaceFleetCommand([$car1, $car2])
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ReplaceFleetCommand
{
    /** @var array<int, Car> */
    private array $cars;

    /**
     * Nombre: __construct
     * Descripción: Construye el comando con la colección tipada de coches a persistir.
     * Parámetros de entrada: array<int, Car> $cars
     * Parámetros de salida: ReplaceFleetCommand
     * Método de uso: new ReplaceFleetCommand([$car])
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(array $cars)
    {
        $this->cars = $cars;
    }

    /**
     * Nombre: cars
     * Descripción: Devuelve los coches del comando.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: array<int, Car>
     * Método de uso: $command->cars()
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @return array<int, Car>
     */
    public function cars(): array
    {
        return $this->cars;
    }
}
