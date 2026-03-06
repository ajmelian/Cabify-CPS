<?php

declare(strict_types=1);

namespace Cabify\Application\Query;

/**
 * Nombre: LocateJourneyQuery
 * Descripción: Query de aplicación para localizar el estado actual de un grupo.
 * Parámetros de entrada: int $id
 * Parámetros de salida: Objeto query inmutable.
 * Método de uso: new LocateJourneyQuery(3)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class LocateJourneyQuery
{
    private int $id;

    /**
     * Nombre: __construct
     * Descripción: Inicializa la query de localización para un grupo.
     * Parámetros de entrada: int $id
     * Parámetros de salida: LocateJourneyQuery
     * Método de uso: new LocateJourneyQuery(9)
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
     * Descripción: Devuelve identificador del grupo a localizar.
     * Parámetros de entrada: Ninguno
     * Parámetros de salida: int
     * Método de uso: $query->id()
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
