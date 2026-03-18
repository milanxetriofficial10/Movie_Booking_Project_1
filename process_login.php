<?php
session_start();

if(isset($_POST['login'])) {

    $conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");
    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    $email_or_contact = isset($_POST['email_or_contact']) ? trim($_POST['email_or_contact']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $redirect = isset($_POST['redirect']) ? trim($_POST['redirect']) : 'index.php';

    if(empty($email_or_contact) || empty($password)){
        die("Please enter both Email/Contact and Password!");
    }

    // Find user by email or contact
    $sql = "SELECT * FROM users WHERE email = ? OR contact = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_contact, $email_or_contact);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 1){
        $row = $res->fetch_assoc();
        if(password_verify($password, $row['password'])){
            // Login successful
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['first_name'] = $row['first_name'];

            // Redirect to intended page
            header("Location: " . urldecode($redirect));
            exit();
        } else {
            echo "<script>alert('Wrong password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }
} else {
    // If accessed directly, redirect to login page
    header("Location: login.php");
    exit();
}
?>