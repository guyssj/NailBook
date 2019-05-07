<?php

// Update the path below to your autoload.php,
// see https://getcomposer.org/doc/01-basic-usage.md
require '../vendor/autoload.php';
use Twilio\Rest\Client;

// Find your Account Sid and Auth Token at twilio.com/console
$sid    = "ACcbebc33e1e8ad7d6e5018f910fba71d5";
$token  = "8cb215a65aac854818f48fb26f837ba5";
$twilio = new Client($sid, $token);

$message = $twilio->messages
                  ->create("+972504277550", // to
                           array("from" => "+9720504277550", "body" => "body")
                  );

print($message->sid);