<?php

declare(strict_types=1);

namespace Cabify\Infrastructure\Controller;

use Cabify\Application\Command\DropoffJourneyCommand;
use Cabify\Application\Exception\JourneyNotFoundException;
use Cabify\Application\Handler\DropoffJourneyHandler;
use Cabify\Infrastructure\Http\Exception\HttpException;
use Cabify\Infrastructure\Http\Exception\ValidationHttpException;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Response\HttpResponse;
use Cabify\Infrastructure\Response\JsonResponse;
use Cabify\Infrastructure\Security\InputPatterns;
use JsonException;

/**
 * Nombre: PostDropoffController
 * Descripción: Controlador HTTP para finalizar un journey y liberar capacidad de coches.
 * Parámetros de entrada: HttpRequest JSON con id de grupo
 * Parámetros de salida: HttpResponse JSON
 * Método de uso: $controller($request)
 * Fecha de desarrollo: 2026-03-04
 * Autor: Aythami Melián Perdomo
 * Fecha de actualización: 2026-03-04
 * Autor actualización: Aythami Melián Perdomo
 */
final class PostDropoffController
{
    private DropoffJourneyHandler $dropoffJourneyHandler;

    /**
     * Nombre: __construct
     * Descripción: Inyecta caso de uso de dropoff.
     * Parámetros de entrada: DropoffJourneyHandler $dropoffJourneyHandler
     * Parámetros de salida: PostDropoffController
     * Método de uso: new PostDropoffController($handler)
     * Fecha de desarrollo: 2026-03-04
     * Autor: Aythami Melián Perdomo
     * Fecha de actualización: 2026-03-04
     * Autor actualización: Aythami Melián Perdomo
     */
    public function __construct(DropoffJourneyHandler $dropoffJourneyHandler)
    {
        $this->dropoffJourneyHandler = $dropoffJourneyHandler;
    }

    /**
     * Nombre: __invoke
     * Descripción: Valida request de dropoff, ejecuta caso de uso y mapea errores a HTTP.
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
            $this->dropoffJourneyHandler->handle(new DropoffJourneyCommand($id));
        } catch (JourneyNotFoundException) {
            throw new HttpException(404, 'Journey not found.');
        }

        return JsonResponse::fromPayload(200, [
            'id' => $id,
            'status' => 'dropped_off',
        ]);
    }

    /**
     * Nombre: extractId
     * Descripción: Extrae y valida el id del payload JSON de la request.
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
