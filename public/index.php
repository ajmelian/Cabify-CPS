<?php

declare(strict_types=1);

use Cabify\Application\Handler\ReplaceFleetHandler;
use Cabify\Application\Handler\RegisterJourneyHandler;
use Cabify\Application\Handler\DropoffJourneyHandler;
use Cabify\Application\Handler\LocateJourneyHandler;
use Cabify\Infrastructure\Config\EnvLoader;
use Cabify\Infrastructure\Controller\PostDropoffController;
use Cabify\Infrastructure\Controller\PostLocateController;
use Cabify\Infrastructure\Controller\PostJourneyController;
use Cabify\Infrastructure\Controller\PutCarsController;
use Cabify\Infrastructure\Controller\StatusController;
use Cabify\Infrastructure\Http\Kernel;
use Cabify\Infrastructure\Persistence\MySql\MySqlCarRepository;
use Cabify\Infrastructure\Persistence\MySql\MySqlJourneyRepository;
use Cabify\Infrastructure\Persistence\MySql\PdoConnectionFactory;
use Cabify\Infrastructure\Request\HttpRequest;
use Cabify\Infrastructure\Router\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

try {
    EnvLoader::load(dirname(__DIR__) . '/.env');

    $connection = PdoConnectionFactory::createFromEnvironment();
    $carRepository = new MySqlCarRepository($connection);
    $journeyRepository = new MySqlJourneyRepository($connection);
    $replaceFleetHandler = new ReplaceFleetHandler($carRepository);
    $registerJourneyHandler = new RegisterJourneyHandler($journeyRepository, $journeyRepository);
    $dropoffJourneyHandler = new DropoffJourneyHandler($journeyRepository, $journeyRepository);
    $locateJourneyHandler = new LocateJourneyHandler($journeyRepository);

    $router = new Router();
    $router->add('GET', '/status', new StatusController());
    $router->add('PUT', '/cars', new PutCarsController($replaceFleetHandler));
    $router->add('POST', '/journey', new PostJourneyController($registerJourneyHandler));
    $router->add('POST', '/dropoff', new PostDropoffController($dropoffJourneyHandler));
    $router->add('POST', '/locate', new PostLocateController($locateJourneyHandler));

    $kernel = new Kernel($router);
    $response = $kernel->handle(HttpRequest::fromGlobals());
    $response->send();
} catch (\Throwable) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode(['error' => 'Internal server error.'], JSON_THROW_ON_ERROR);
}
