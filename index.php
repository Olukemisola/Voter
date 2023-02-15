<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/db.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
// $app = AppFactory::create();

// function handleRequest(Request $request, Response $response)
// {
//     $response->getBody()->write("Hello, Kemi");
//     return $response; 
// }


// $app->get(
//     '/',
//     function (Request $request, Response $response) {
//         return handleRequest($request, $response);
//     }

// );
$app = AppFactory::create();
$app->addRoutingMiddleware();

$app->get('/create/voter', function (Request $request, Response $response) {

    $response->getBody()->write("Hello, Kemisola");
    return $response;
});
// friends routes

require __DIR__ . '\public\routes\evoters.php';
require __DIR__ . '\public\routes\vote.php';
// require "public/Middlewares/AuthMiddleware.php";



$app->run();
