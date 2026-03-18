<?php
session_start();
$conn = new mysqli("localhost", "root", "Milan@1234", "movie_booking_project_1");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Handle profile update
if(isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $contact    = $_POST['contact'];
    $address    = $_POST['address'];

    $update_sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', contact='$contact', address='$address' WHERE id=$user_id";
    if($conn->query($update_sql)) {
        $msg = "Profile updated successfully!";
        $user = array_merge($user, $_POST); // Update displayed info
    } else {
        $msg = "Error updating profile.";
    }
}

// Handle password change
if(isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if(password_verify($current_pass, $user['password'])) {
        if($new_pass === $confirm_pass) {
            $new_hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$new_hashed' WHERE id=$user_id");
            $pass_msg = "Password changed successfully!";
        } else {
            $pass_msg = "New passwords do not match.";
        }
    } else {
        $pass_msg = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<style>
    * {box-sizing:border-box;}
    body {
        font-family: 'Montserrat', sans-serif;
        background: linear-gradient(to right, #74ebd5, #ACB6E5);
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .profile-container {
        background:#fff;
        border-radius:15px;
        padding: 30px;
        width: 500px;
        text-align:center;
        box-shadow:0 10px 25px rgba(0,0,0,0.2);
        animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
        0% {opacity:0; transform: translateY(-20px);}
        100% {opacity:1; transform: translateY(0);}
    }
    img {
        width:130px; height:130px; border-radius:50%; object-fit:cover; margin-bottom:20px;
        transition: transform 0.3s ease;
    }
    img:hover { transform: scale(1.05);}
    .tab-buttons {
        display:flex; justify-content: center; margin-bottom: 20px;
    }
    .tab-buttons button {
        background:#FF6B6B; color:#fff; border:none; padding:10px 20px; margin:0 5px; border-radius:50px; cursor:pointer;
        transition: background 0.3s, transform 0.3s;
        font-weight:600;
    }
    .tab-buttons button.active { background:#FF4757; transform: scale(1.05);}
    .tab-buttons button:hover { transform: scale(1.05);}
    form { display:none; text-align:left; animation: slideFade 0.5s ease forwards; }
    form.active { display:block; }
    @keyframes slideFade {
        0% {opacity:0; transform: translateX(-20px);}
        100% {opacity:1; transform: translateX(0);}
    }
    .row { display:flex; gap:10px; }
    .row input { flex:1; }
    label { font-weight:600; margin-top:10px; display:block; color:#555;}
    input { width:100%; padding:8px 10px; margin:8px 0; border-radius:8px; border:1px solid #ccc; font-size:15px;}
    .btn { display:inline-block; padding:12px 25px; background:#FF6B6B; color:white; border-radius:50px; border:none; font-weight:600; margin-top:15px; cursor:pointer; transition:background 0.3s, transform 0.3s; }
    .btn:hover { background:#FF4757; transform:scale(1.05);}
    .logout-btn { background:#333; margin-top:20px; display:block; text-align:center; text-decoration:none; padding:12px; border-radius:50px;}
    .message {color:green; font-weight:600; margin-bottom:10px;}
</style>
</head>
<body>

<div class="profile-container">
    <img src="<?php echo $user['profile_img']; ?>" alt="Profile">

    <div class="tab-buttons">
        <button class="active" onclick="showTab('profileTab')">Edit Profile</button>
        <button onclick="showTab('passwordTab')">Change Password</button>
    </div>

    <?php if(isset($msg)) echo "<div class='message'>$msg</div>"; ?>
    <form id="profileTab" class="active" method="post">
        <div class="row">
            <div>
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>
            </div>
            <div>
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>
            </div>
        </div>
        <label>Email</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        <label>Contact</label>
        <input type="text" name="contact" value="<?php echo $user['contact']; ?>" required>
        <label>Address</label>
        <input type="text" name="address" value="<?php echo $user['address']; ?>" required>
        <button type="submit" name="update_profile" class="btn">Update Profile</button>
    </form>

    <?php if(isset($pass_msg)) echo "<div class='message'>$pass_msg</div>"; ?>
    <form id="passwordTab" method="post">
        <label>Current Password</label>
        <input type="password" name="current_password" required>
        <label>New Password</label>
        <input type="password" name="new_password" required>
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>
        <button type="submit" name="change_password" class="btn">Change Password</button>
    </form>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<script>
function showTab(tabId){
    const tabs = document.querySelectorAll('form');
    tabs.forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');

    const buttons = document.querySelectorAll('.tab-buttons button');
    buttons.forEach(b => b.classList.remove('active'));
    event.currentTarget.classList.add('active');
}
</script>

</body>
</html>
