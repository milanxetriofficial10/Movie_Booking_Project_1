<?php
session_start();
// Check if page opened directly with optional redirect
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="./imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/x-icon">
<title>CineMa Ghar - Login</title>

<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{
    font-family: Arial, sans-serif;
    background:#f4f6f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.card{
    background:#ffffff;
    width:380px;
    padding:30px;
    border-radius:8px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}
#heading{
    text-align:center;
    margin-bottom:25px;
    font-size:22px;
    color:#333;
}
.field{ margin-bottom:18px; }
.input-field{
    width:100%;
    padding:10px;
    border-radius:5px;
    border:1px solid #ccc;
    font-size:14px;
}
.input-field:focus{
    border-color:#007bff;
    outline:none;
}
.btn{ display:flex; gap:10px; }
button{
    flex:1;
    padding:10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    font-size:14px;
}
.button1{ background:#007bff; color:white; }
.button1:hover{ background:#0056b3; }
.button2{ background:#6c757d; color:white; }
.button2:hover{ background:#545b62; }
.button3{
    width:100%;
    margin-top:15px;
    background:#28a745;
    color:white;
}
.button3:hover{ background:#1e7e34; }
@media(max-width:450px){ .card{ width:90%; } }
</style>
</head>

<body>
<div class="card">
    <form action="process_login.php" method="POST">
        <input type="hidden" name="redirect" value="<?=htmlspecialchars($redirect)?>">
        <p id="heading">Login</p>

        <div class="field">
            <input type="text" name="email_or_contact" class="input-field" placeholder="Email or Contact" required>
        </div>

        <div class="field">
            <input type="password" name="password" class="input-field" placeholder="Password" required>
        </div>

        <div class="btn">
            <button type="submit" name="login" class="button1">Login</button>
            <button type="button" onclick="window.location='signup.php';" class="button2">Sign Up</button>
        </div>

        <button type="button" onclick="alert('Contact admin to reset password');" class="button3">Forgot Password</button>
    </form>
</div>
</body>
</html>