<?php

namespace BookNail;

use Firebase\JWT\JWT;
use DateTime;
use Slim\Http\Response as Response;
use Slim\Http\Request as Request;

class contextMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, $response, $next)
    {
        global $tokenGlobal;
        $method = $request->getOriginalMethod();
        $token = $request->getHeader("Authorization");

        if ($token == null || $method == "OPTIONS") {
            $response = $next($request, $response);
            return $response;
        }
        $Test = str_replace("Bearer", "", $token[0]);
        try {
            $decode = JWT::decode(ltrim($Test, " "), $_SERVER['Secret'], ["HS256", "HS384"]);
        } catch (\Throwable $th) {
            $response = $next($request, $response);
            return $response;
        }
        if (isset($decode->auth)) {
            $decodeToken = new Token([
                "iat" => $decode->iat,
                "exp" => $decode->exp,
                "sub" => $decode->sub,
                "auth" => $decode->auth,
                "scope" => $decode->scope
            ]);
        } else {
            $decodeToken = new Token([
                "iat" => $decode->iat,
                "exp" => $decode->exp,
                "sub" => $decode->sub,
                "auth" => null,
                "scope" => $decode->scope
            ]);
        }
        $now = new DateTime();
        if ($decode->exp >= $now->getTimeStamp())
            $tokenGlobal = $decodeToken;

        $response = $next($request, $response);
        return $response;
    }
}
