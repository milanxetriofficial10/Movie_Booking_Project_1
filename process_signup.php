<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get form data
$first    = trim($_POST['first_name']);
$last     = trim($_POST['last_name']);
$email    = trim($_POST['email']);
$contact  = trim($_POST['contact']);   // Contact added
$password = trim($_POST['password']);
$confirm  = trim($_POST['confirm_password']);

// Initialize errors array
$errors = [];

// Password confirmation check
if ($password !== $confirm) {
    $errors[] = "Passwords do not match!";
}

// Password validation rules
if (strlen($password) < 10) {
    $errors[] = "Password must be at least 10 characters.";
}
if (!preg_match('/\d/', $password)) {
    $errors[] = "Password must contain at least one number.";
}
if (!preg_match('/[*#]/', $password)) {
    $errors[] = "Password must contain at least one * or #.";
}
if (!preg_match('/^[A-Z]/', $password)) {
    $errors[] = "Password must start with a capital letter.";
}

// If there are any errors, display them and stop
if (count($errors) > 0) {
    foreach ($errors as $err) {
        echo "<p style='color:red;'>$err</p>";
    }
    exit;
}

// Hash the password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$sql = "INSERT INTO users (first_name, last_name, email, contact, password) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $first, $last, $email, $contact, $hashed);

if ($stmt->execute()) {
    // Set session
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['first_name'] = $first;

    // Redirect to index
    header("Location: index.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
