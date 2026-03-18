<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get form data safely
$first    = trim($_POST['first_name'] ?? '');
$last     = trim($_POST['last_name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$contact  = trim($_POST['contact'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm  = trim($_POST['confirm_password'] ?? '');

// Error array
$errors = [];

// Basic empty check
if ($first === '' || $last === '' || $email === '' || $contact === '' || $password === '' || $confirm === '') {
    $errors[] = "All fields are required!";
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format!";
}

// Password match check
if ($password !== $confirm) {
    $errors[] = "Passwords do not match!";
}

// 🔐 PASSWORD RULES

// Length (10–15)
if (strlen($password) < 10 || strlen($password) > 15) {
    $errors[] = "Password must be between 10 and 15 characters.";
}

// At least 2 numbers
if (preg_match_all('/\d/', $password) < 2) {
    $errors[] = "Password must contain at least 2 numbers.";
}

// Must contain # or @
if (!preg_match('/[#@]/', $password)) {
    $errors[] = "Password must contain at least one # or @.";
}

// First letter capital
if (!preg_match('/^[A-Z]/', $password)) {
    $errors[] = "Password must start with a capital letter.";
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $errors[] = "Email already registered!";
}

// If errors exist → show
if (!empty($errors)) {
    foreach ($errors as $err) {
        echo "<p style='color:red; text-align:center;'>$err</p>";
    }
    echo "<p style='text-align:center;'><a href='signup.php'>Go Back</a></p>";
    exit;
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, contact, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $first, $last, $email, $contact, $hashed);

if ($stmt->execute()) {

    // Session set
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['first_name'] = $first;

    // Redirect
    header("Location: index.php");
    exit();

} else {
    echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
}
?>