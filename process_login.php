<?php
session_start();

$conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 1) {
    $row = $res->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['first_name'] = $row['first_name'];  // only first name

        header("Location: index.php");
        exit();
    } else {
        echo "Wrong password!";
    }
} else {
    echo "User not found!";
}
?>
