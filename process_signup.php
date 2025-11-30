<?php
$conn = new mysqli("localhost", "root", "", "movie_booking_project_1");

$first = $_POST['first_name'];
$last = $_POST['last_name'];
$address = $_POST['address'];
$email = $_POST['email'];
$pass = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($pass !== $confirm) {
    die("Passwords do not match!");
}

$hashed = password_hash($pass, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (first_name, last_name, address, email, password)
        VALUES ('$first', '$last', '$address', '$email', '$hashed')";

if ($conn->query($sql)) {
    header("Location: login.php");
} else {
    echo "Error: " . $conn->error;
}
?>
