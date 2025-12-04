<?php
session_start();

$conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$first   = trim($_POST['first_name']);
$last    = trim($_POST['last_name']);
$email   = trim($_POST['email']);
$password = trim($_POST['password']);
$confirm  = trim($_POST['confirm_password']);

if ($password !== $confirm) {
    die("Passwords do not match!");
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $first, $last, $email, $hashed);

if ($stmt->execute()) {
    // Set session for first name
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['first_name'] = $first;

    header("Location: index.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
