<?php
session_start();
require_once '../includes/db.php';

$conn = db_connect();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $admin = $res->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_name'] = $admin['username'];

        header("Location: dashboard.php");
        exit;
    } else {
        $msg = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body { background:#f0f0f0; font-family:Arial; }
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
<h2>Admin Login</h2>
<p><?= $msg ?></p>

<form method="POST">
<input type="text" name="username" placeholder="Username">
<input type="password" name="password" placeholder="Password">
<button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="admin_register.php">Create Admin</a></p>

</div>

</body>
</html>
