<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Controller;

use Cabify\Application\Command\ReplaceFleetCommand;
use Cabify\Application\Handler\ReplaceFleetHandler;
use Cabify\Entity\Car;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Response\JsonResponse;
use Cabify\Infrastructure\Security\InputPatterns;
use JsonException;

/**
 * Nombre: PutCarsController
 * Descripción: Controlador HTTP para reemplazar la flota completa con validación estricta.
 * Parámetros de entrada: HttpRequest JSON con array de coches
 * Parámetros de salida: HttpResponse JSON
 * Método de uso: $controller($request)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PutCarsController
{
    private ReplaceFleetHandler $replaceFleetHandler;

    /**
     * Nombre: __construct
     * Descripción: Inyecta el caso de uso que reemplaza la flota.
     * Parámetros de entrada: ReplaceFleetHandler $replaceFleetHandler
     * Parámetros de salida: PutCarsController
     * Método de uso: new PutCarsController($handler)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(ReplaceFleetHandler $replaceFleetHandler)
    {
        $this->replaceFleetHandler = $replaceFleetHandler;
    }

    /**
     * Nombre: __invoke
     * Descripción: Valida payload, ejecuta caso de uso y devuelve respuesta HTTP.
     * Parámetros de entrada: HttpRequest $request
     * Parámetros de salida: HttpResponse
     * Método de uso: $response = $controller($request)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @throws JsonException
     */
    public function __invoke(HttpRequest $request): HttpResponse
    {
        $contentType = $request->header('content-type') ?? '';
        if (stripos($contentType, 'application/json') === false) {
            throw new ValidationHttpException('Content-Type must be application/json.');
        }

        try {
            $rawPayload = json_decode($request->body(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new ValidationHttpException('Invalid JSON payload.');
        }

        if (!is_array($rawPayload) || !array_is_list($rawPayload)) {
            throw new ValidationHttpException('Payload must be an array of cars.');
        }

        $cars = $this->toCars($rawPayload);
        $this->replaceFleetHandler->handle(new ReplaceFleetCommand($cars));

        return JsonResponse::fromPayload(200, ['status' => 'fleet_replaced']);
    }

    /**
     * Nombre: toCars
     * Descripción: Convierte payload validado en entidades Car con validación regex y semántica.
     * Parámetros de entrada: array<mixed> $payload
     * Parámetros de salida: array<int, Car>
     * Método de uso: $cars = $this->toCars($payload)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param array<mixed> $payload
     * @return array<int, Car>
     */
    private function toCars(array $payload): array
    {
        $cars = [];
        $registeredIds = [];

        foreach ($payload as $index => $item) {
            if (!is_array($item)) {
                throw new ValidationHttpException(sprintf('Car at index %d must be an object.', $index));
            }

            if (!array_key_exists('id', $item) || !array_key_exists('seats', $item)) {
                throw new ValidationHttpException(sprintf('Car at index %d must include id and seats.', $index));
            }

            $id = $this->toInt($item['id'], 'id', $index);
            $seats = $this->toInt($item['seats'], 'seats', $index);

            if (preg_match(InputPatterns::POSITIVE_ID, (string) $id) !== 1) {
                throw new ValidationHttpException(sprintf('Car id at index %d is invalid.', $index));
            }

            if (preg_match(InputPatterns::CAR_SEATS, (string) $seats) !== 1) {
                throw new ValidationHttpException(sprintf('Car seats at index %d must be between 1 and 8.', $index));
            }

            if (isset($registeredIds[$id])) {
                throw new ValidationHttpException(sprintf('Car id %d is duplicated.', $id));
            }

            $registeredIds[$id] = true;
            $cars[] = new Car($id, $seats);
        }

        return $cars;
    }

    /**
     * Nombre: toInt
     * Descripción: Normaliza un valor escalar a entero válido para validación.
     * Parámetros de entrada: mixed $value, string $field, int $index
     * Parámetros de salida: int
     * Método de uso: $value = $this->toInt($item['id'], 'id', 0)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param mixed $value
     */
    private function toInt(mixed $value, string $field, int $index): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && preg_match(InputPatterns::INTEGER, $value) === 1) {
            return (int) $value;
        }

        throw new ValidationHttpException(sprintf('Field %s at index %d must be an integer.', $field, $index));
    }
}
