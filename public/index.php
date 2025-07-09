<?php

use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Dotenv\Dotenv;
use App\Controllers\GroupController;

require __DIR__ . '/../vendor/autoload.php';

// .env 
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();

// middleware
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => $_ENV['DB_DRIVER'],
    'database' => $_ENV['DB_DATABASE'],
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->post('/groups', [GroupController::class, 'create']);
$app->post('/groups/{id}/join', [GroupController::class, 'join']);
$app->post('/groups/{id}/messages', [GroupController::class, 'sendMessage']);
$app->get('/groups/{id}/messages', [GroupController::class, 'getMessages']);
$app->get('/groups', [GroupController::class, 'getAll']);

// Test route
$app->get('/ping', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['message' => 'pong']));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();