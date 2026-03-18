<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('299124471446-1mmsvcr4odlah20uvpjvf5nd7d2esaij.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/Movie_Booking_Project_1/google_callback.php');

$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();

header("Location: " . $login_url);
exit();