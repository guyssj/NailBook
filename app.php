<?php

header('Content-Type: application/json');
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/config/ExceptionHandler.class.php";


use BookNail\ResultAPI;
use BookNail\Token;

$app = new Slim\App();
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin, Authorization');
});
$allowHosts = str_replace("\s", " ", $_SERVER['ALLOWHOST']);
$corsArray = json_decode($allowHosts, true);

$app->add(new \Eko3alpha\Slim\Middleware\CorsMiddleware($corsArray));

$container = $app->getContainer();
$container["token"] = function ($container) {
    return new Token([]);
};
$container['errorHandler'] = function ($container) {
    return new ExceptionHandler();
};
$container['notFoundHandler'] = function ($container) {
    return new ExceptionHandler();
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

$app->add(new BookNail\contextMiddleware($container));

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
