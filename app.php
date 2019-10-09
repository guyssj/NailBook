<?php

use \Firebase\JWT\JWT;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
header('Content-Type: application/json');
require __DIR__ . "/vendor/autoload.php";

require __DIR__ ."/src/config/db.php";
require __DIR__ ."/src/config/Books.class.php";
require __DIR__ ."/src/config/ServiceTypes.class.php";
require __DIR__ ."/src/config/Users.class.php";
require __DIR__ ."/src/config/Customer.class.php";
require __DIR__ ."/src/config/Service.class.php";
require __DIR__ ."/src/config/token.class.php";
require __DIR__ ."/src/config/ResultsApi.class.php";
require __DIR__ ."/src/config/SyncWithGoogle.class.php";


$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
       // ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('access-control-expose-headers', 'X-Token')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With,X-Token, Content-Type, Accept, Origin, Authorization');
       // ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')

});

$app->add(new \Eko3alpha\Slim\Middleware\CorsMiddleware([
    'http://localhost:4200'  => 'GET, POST, DELETE',
    'http://localhost:8100' => 'GET', 'POST',
    'ionic://localhost' => 'GET','POST'
  ]));

$container = $app->getContainer();
$container["token"] = function ($container) {
    return new Token([]);
};
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => "/admin", /* or ["/api", "/admin"] */
    "attribute" => "decoded_token_data",
    "header" => "X-Token",
    "regexp" => "/(.*)/",
    "cookie" => "userToken",
    "secret" => getenv('Secret'),
    "algorithm" => ["HS256"],
    "secure" => false,
    "error" => function ($response, $arguments) {
        $resultObj = new ResultAPI();
        $resultObj->set_statusCode(403);
        $resultObj->set_ErrorMessage( $arguments["message"]);
        return $response
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($resultObj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
    "before" => function ($request, $arguments) use ($container) {
        $container["token"]->populate($arguments["decoded"]);
    }
]));

require __DIR__ . "/src/routes/API.php";
require __DIR__ . "/src/routes/Admin.php";

$app->run();