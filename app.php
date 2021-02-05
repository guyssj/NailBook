<?php

header('Content-Type: application/json');
require __DIR__ . "/vendor/autoload.php";

use BookNail\ResultAPI;
use BookNail\Token;
$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin, Authorization');

});

$app->add(new \Eko3alpha\Slim\Middleware\CorsMiddleware([
    'http://192.168.1.18:8100' => 'GET, POST, DELETE, PUT',
    'http://localhost:4200' => 'GET, POST, DELETE, PUT',
    'http://localhost:8100' => 'GET, POST, DELETE, PUT',
    'http://localhost' => 'GET, POST, DELETE, PUT',
    'http://miritush.app' => 'GET, POST, DELETE, PUT',

    'http://192.168.0.46:4200' => 'GET, POST, DELETE, PUT',
    'http://172.20.10.2:8100' => 'GET, POST, DELETE, PUT',
    'ionic://localhost' => 'GET, POST, DELETE, PUT',
]));

$container = $app->getContainer();
$container["token"] = function ($container) {
    return new Token([]);
};
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => "/admin", /* or ["/api", "/admin"] */
    "attribute" => "decoded_token_data",
    "cookie" => "userToken",
    "secret" => $_SERVER['Secret'],
    "algorithm" => ["HS384"],
    "secure" => false,
    "error" => function ($response, $arguments) {
        $resultObj = new ResultAPI();
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($arguments["message"]);
        return $response
            ->withHeader("Content-Type", "application/json")
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withStatus($response->getStatusCode())
            ->write(json_encode($resultObj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
    "before" => function ($request, $arguments) use ($container) {
        $container["token"]->populate($arguments["decoded"]);
        $container["User"] = $container["token"]->getUser();
    },
]));

require __DIR__ . "/src/routes/API.php";
require __DIR__ . "/src/routes/UsersRoute.php";
require __DIR__ . "/src/routes/CustomersRoute.php";
require __DIR__ . "/src/routes/ServicesRoute.php";
require __DIR__ . "/src/routes/BooksRoute.php";
require __DIR__ . "/src/routes/WorkdayRoute.php";
require __DIR__ . "/src/routes/HolidayRoute.php";
require __DIR__ . "/src/routes/SettingsRoute.php";
require __DIR__ . "/src/routes/CalendarRoute.php";
require __DIR__ . "/src/routes/AuthRoute.php";



$app->run();
