<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
header('Content-Type: application/json');
require '../vendor/autoload.php';
require '../src/config/db.php';
require '../src/config/Books.class.php';
require '../src/config/ServiceTypes.class.php';
require '../src/config/Users.class.php';
require '../src/config/Customer.class.php';
require '../src/config/Service.class.php';
require '../src/config/SyncWithGoogle.class.php';





$app = new \Slim\App;
// $app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
//     $name = $args['name'];
//     $response->getBody()->write("Hello, $name");

//     return $response;
// });

require '../src/routes/API.php';

$app->run();