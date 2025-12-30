<?php
session_start();

if(isset($_POST['login'])) { // Check if form is submitted

    $conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");

    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    // Get form data safely
    $email_or_contact = isset($_POST['email_or_contact']) ? trim($_POST['email_or_contact']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if(empty($email_or_contact) || empty($password)){
        die("Please enter both Email/Contact and Password!");
    }

    // Query to find user by email or contact
    $sql = "SELECT * FROM users WHERE email = ? OR contact = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_contact, $email_or_contact);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Set session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['first_name'] = $row['first_name'];

            // Redirect to homepage
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Wrong password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.history.back();</script>";
    }

} else {
    // If user opens this PHP directly
    header("Location: login.php");
    exit();
}
?>
