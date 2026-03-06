<?php

declare(strict_types=1);

namespace Cabify\Application\Handler;

use Cabify\Application\Command\ReplaceFleetCommand;
use Cabify\Application\Port\CarRepositoryInterface;

/**
 * Nombre: ReplaceFleetHandler
 * Descripción: Caso de uso que reemplaza la flota completa y resetea estado operativo.
 * Parámetros de entrada: ReplaceFleetCommand
 * Parámetros de salida: void
 * Método de uso: $handler->handle($command)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class ReplaceFleetHandler
{
    private CarRepositoryInterface $carRepository;

    /**
     * Nombre: __construct
     * Descripción: Inyecta el puerto de persistencia de coches.
     * Parámetros de entrada: CarRepositoryInterface $carRepository
     * Parámetros de salida: ReplaceFleetHandler
     * Método de uso: new ReplaceFleetHandler($carRepository)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    /**
     * Nombre: handle
     * Descripción: Ejecuta el reemplazo de flota de manera transaccional vía repositorio.
     * Parámetros de entrada: ReplaceFleetCommand $command
     * Parámetros de salida: void
     * Método de uso: $handler->handle($command)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function handle(ReplaceFleetCommand $command): void
    {
        $this->carRepository->replaceFleet($command->cars());
    }
}
