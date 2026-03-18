<?php
session_start();
require_once '../includes/db.php';

$conn = db_connect();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!$username || !$password) {
        $msg = "All fields are required!";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);

        if ($stmt->execute()) {
            $msg = "Admin registered successfully! <a href='admin_login.php'>Login Now</a>";
        } else {
            $msg = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Register</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
body { background:#eef2f3; font-family:Arial; }
.form-box {
    width:350px; margin:100px auto; background:white;
    padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);
}
input { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #999; }
button { width:100%; padding:10px; background:#1e40af; border:none; color:white; border-radius:6px; }
p { color:red; }
</style>
</head>
<body>

<div class="form-box">
<h2>Create Admin</h2>
<p><?= $msg ?></p>

<form method="POST">
<input type="text" name="username" placeholder="Admin Username">
<input type="password" name="password" placeholder="Password">
<button type="submit">Register</button>
</form>

</div>

</body>
</html>


