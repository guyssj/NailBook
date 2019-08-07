<?php
require '../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('../src/config/credentials.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/NailBook/public/oauth2callback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);

if (! isset($_GET['code'])) {
  //$client->createApplicationDefaultCredentials();
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/NailBook/src/config/GoogleApis.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}