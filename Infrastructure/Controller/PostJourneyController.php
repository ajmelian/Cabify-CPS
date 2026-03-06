<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Controller;

use Cabify\Application\Command\RegisterJourneyCommand;
use Cabify\Application\Exception\DuplicateJourneyException;
use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Response\JsonResponse;
use Cabify\Infrastructure\Security\InputPatterns;
use JsonException;

/**
 * Nombre: PostJourneyController
 * Descripción: Controlador HTTP para registrar grupos de journey y resolver asignación inicial.
 * Parámetros de entrada: HttpRequest JSON con id y people
 * Parámetros de salida: HttpResponse JSON con estado assigned|waiting
 * Método de uso: $controller($request)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PostJourneyController
{
    private RegisterJourneyHandler $registerJourneyHandler;

    /**
     * Nombre: __construct
     * Descripción: Inyecta el caso de uso de registro de journey.
     * Parámetros de entrada: RegisterJourneyHandler $registerJourneyHandler
     * Parámetros de salida: PostJourneyController
     * Método de uso: new PostJourneyController($handler)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(RegisterJourneyHandler $registerJourneyHandler)
    {
        $this->registerJourneyHandler = $registerJourneyHandler;
    }

    /**
     * Nombre: __invoke
     * Descripción: Valida request, ejecuta caso de uso y mapea resultado a códigos HTTP.
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

        if (!is_array($rawPayload) || array_is_list($rawPayload)) {
            throw new ValidationHttpException('Payload must be an object with id and people.');
        }

        if (!array_key_exists('id', $rawPayload) || !array_key_exists('people', $rawPayload)) {
            throw new ValidationHttpException('Payload must include id and people.');
        }

        $id = $this->toInt($rawPayload['id'], 'id');
        $people = $this->toInt($rawPayload['people'], 'people');

        if (preg_match(InputPatterns::POSITIVE_ID, (string) $id) !== 1) {
            throw new ValidationHttpException('Journey id must be a positive integer.');
        }

        if (preg_match(InputPatterns::PEOPLE, (string) $people) !== 1) {
            throw new ValidationHttpException('Journey people must be between 1 and 6.');
        }

        try {
            $result = $this->registerJourneyHandler->handle(new RegisterJourneyCommand($id, $people));
        } catch (DuplicateJourneyException) {
            throw new ValidationHttpException('Journey id already exists.');
        }

        if ($result->status() === 'assigned') {
            return JsonResponse::fromPayload(200, [
                'id' => $result->groupId(),
                'status' => $result->status(),
                'car_id' => $result->carId(),
            ]);
        }

        return JsonResponse::fromPayload(202, [
            'id' => $result->groupId(),
            'status' => $result->status(),
        ]);
    }

    /**
     * Nombre: toInt
     * Descripción: Convierte valor escalar de payload a entero válido.
     * Parámetros de entrada: mixed $value, string $field
     * Parámetros de salida: int
     * Método de uso: $id = $this->toInt($payload['id'], 'id')
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     *
     * @param mixed $value
     */
    private function toInt(mixed $value, string $field): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && preg_match(InputPatterns::INTEGER, $value) === 1) {
            return (int) $value;
        }

        throw new ValidationHttpException(sprintf('Field %s must be an integer.', $field));
    }
}
