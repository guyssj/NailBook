<?php

header('Content-Type: application/json');
require __DIR__ . "/vendor/autoload.php";

require __DIR__ . "/src/config/db.php";
require __DIR__ . "/src/config/Books.class.php";
require __DIR__ . "/src/config/ServiceTypes.class.php";
require __DIR__ . "/src/config/Users.class.php";
require __DIR__ . "/src/config/Customer.class.php";
require __DIR__ . "/src/config/Service.class.php";
require __DIR__ . "/src/config/token.class.php";
require __DIR__ . "/src/config/ResultsApi.class.php";
require __DIR__ . "/src/config/SyncWithGoogle.class.php";
require __DIR__ . "/src/config/globalSMS.class.php";
require __DIR__ . "/src/config/WorkingHours.class.php";
require __DIR__ . "/src/config/Logger.class.php";
require __DIR__ . "/src/config/CloseDays.class.php";
require __DIR__ . "/src/config/LockHours.class.php";
require __DIR__ . "/src/config/TimeSlots.class.php";
require __DIR__ . "/src/config/Holidays.class.php";
require __DIR__ . "/src/config/Settings.class.php";
require __DIR__ . "/src/services/booking.service.php";
require __DIR__ . "/src/services/lockhours.service.php";
require __DIR__ . "/src/services/customers.service.php";
require __DIR__ . "/src/services/otp.service.php";
require __DIR__ . "/src/config/otp.class.php";




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
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin, Authorization');
    //->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

});

$app->add(new \Eko3alpha\Slim\Middleware\CorsMiddleware([
    'http://192.168.31.142:8100' => 'GET, POST, DELETE, PUT',
    'http://localhost:4200' => 'GET, POST, DELETE, PUT',
    'http://localhost:8100' => 'GET, POST, DELETE, PUT',
    'http://192.168.1.34:8100' => 'GET, POST, DELETE, PUT',
    'http://172.20.10.3:8100' => 'GET, POST, DELETE, PUT',
    'ionic://localhost' => 'GET, POST',
]));

$container = $app->getContainer();
$container["token"] = function ($container) {
    return new Token([]);
};
$app->add(new \Tuupola\Middleware\JwtAuthentication([
    "path" => "/admin", /* or ["/api", "/admin"] */
    "attribute" => "decoded_token_data",
    // "header" => "X-Token",
    // "regexp" => "/(.*)/",
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
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8100')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withStatus($response->getStatusCode())
            ->write(json_encode($resultObj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
    "before" => function ($request, $arguments) use ($container) {
        $container["token"]->populate($arguments["decoded"]);
    },
]));

require __DIR__ . "/src/routes/API.php";
require __DIR__ . "/src/routes/Admin.php";
require __DIR__ . "/src/routes/CustomersRoute.php";
require __DIR__ . "/src/routes/ServicesRoute.php";
require __DIR__ . "/src/routes/BooksRoute.php";
require __DIR__ . "/src/routes/LocksRoute.php";
require __DIR__ . "/src/routes/WorkdayRoute.php";
require __DIR__ . "/src/routes/HolidayRoute.php";
require __DIR__ . "/src/routes/SettingsRoute.php";



$app->run();
