<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Controller;

use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Handler\LocateJourneyHandler;
use Cabify\Application\Query\LocateJourneyQuery;
use Cabify\Infrastructure\Http\Exception\HttpException;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Response\JsonResponse;
use Cabify\Infrastructure\Security\InputPatterns;
use JsonException;

/**
 * Nombre: PostLocateController
 * Descripción: Controlador HTTP para localizar un grupo en cola o asignado a coche.
 * Parámetros de entrada: HttpRequest JSON con id de grupo
 * Parámetros de salida: HttpResponse (200 asignado, 204 en espera)
 * Método de uso: $controller($request)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PostLocateController
{
    private LocateJourneyHandler $locateJourneyHandler;

    /**
     * Nombre: __construct
     * Descripción: Inyecta caso de uso de localización.
     * Parámetros de entrada: LocateJourneyHandler $locateJourneyHandler
     * Parámetros de salida: PostLocateController
     * Método de uso: new PostLocateController($handler)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(LocateJourneyHandler $locateJourneyHandler)
    {
        $this->locateJourneyHandler = $locateJourneyHandler;
    }

    /**
     * Nombre: __invoke
     * Descripción: Localiza grupo y responde según estado funcional.
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
        $id = $this->extractId($request);

        try {
            $state = $this->locateJourneyHandler->handle(new LocateJourneyQuery($id));
        } catch (JourneyNotFoundException) {
            throw new HttpException(404, 'Journey not found.');
        }

        if ($state->isWaiting()) {
            return new HttpResponse(204, [
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'no-store',
            ], '');
        }

        return JsonResponse::fromPayload(200, [
            'id' => $state->id(),
            'status' => $state->status(),
            'car_id' => $state->assignedCarId(),
        ]);
    }

    /**
     * Nombre: extractId
     * Descripción: Extrae y valida id del payload JSON.
     * Parámetros de entrada: HttpRequest $request
     * Parámetros de salida: int
     * Método de uso: $id = $this->extractId($request)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    private function extractId(HttpRequest $request): int
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
            throw new ValidationHttpException('Payload must be an object with id.');
        }

        if (!array_key_exists('id', $rawPayload)) {
            throw new ValidationHttpException('Payload must include id.');
        }

        $idValue = $rawPayload['id'];
        if (is_int($idValue)) {
            $id = $idValue;
        } elseif (is_string($idValue) && preg_match(InputPatterns::INTEGER, $idValue) === 1) {
            $id = (int) $idValue;
        } else {
            throw new ValidationHttpException('Field id must be an integer.');
        }

        if (preg_match(InputPatterns::POSITIVE_ID, (string) $id) !== 1) {
            throw new ValidationHttpException('Journey id must be a positive integer.');
        }

        return $id;
    }
}
