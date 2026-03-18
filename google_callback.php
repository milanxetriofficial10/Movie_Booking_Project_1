<?php
require_once 'vendor/autoload.php';
session_start();











$conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");
if ($conn->connect_error) die("DB Connection failed: " . $conn->connect_error);


$client = new Google_Client();
$client->setClientId('299124471446-mg41gc98tg5mo3evvo202os6as0drk7n.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/movie_booking_project_1/google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $email = $google_account_info->email;
    $first_name = $google_account_info->givenName;
    $last_name = $google_account_info->familyName;
    $profile_image = $google_account_info->picture;

    // Check user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        $stmt->bind_result($user_id);
        $stmt->fetch();
    } else {
        // Insert new Google user
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, profile_img) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $profile_image);
        $stmt->execute();
        $user_id = $stmt->insert_id;
    }

    $_SESSION['user_id'] = $user_id;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['profile_img'] = $profile_image;

    header("Location: index.php");
    exit();
} else {
    echo "Error: No Google code returned.";
}
