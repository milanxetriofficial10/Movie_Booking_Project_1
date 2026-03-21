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

$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// UPDATE PROFILE
if(isset($_POST['update_profile'])) {

    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $contact    = $_POST['contact'];
    $address    = $_POST['address'];

    $profile_img = $user['profile_img'];

    // IMAGE UPLOAD FIX
    if(isset($_FILES['profile_img']) && $_FILES['profile_img']['name'] != "") {

        $target_dir = "uplimgs/40b3a7667c57b37bb66735d67609798e-modified.pngoads/";

        // create folder if not exists
        if(!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // FIX PERMISSION ISSUE
        chmod($target_dir, 0777);

        $file_name = time() . "_" . basename($_FILES["profile_img"]["name"]);
        $target_file = $target_dir . $file_name;

        if(move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file)) {
            $profile_img = $target_file;
        } else {
            echo "<script>alert('Image upload failed! Check folder permission');</script>";
        }
    }

    $conn->query("UPDATE users SET 
        first_name='$first_name',
        last_name='$last_name',
        email='$email',
        contact='$contact',
        address='$address',
        profile_img='$profile_img'
        WHERE id=$user_id");

    // 🔥 REDIRECT TO HOME PAGE AFTER UPDATE
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CineMa Ghar - Profile</title>
<link rel="shortcut icon" href="./imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/x-icon">

<style>
*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:Poppins;
     background:
        linear-gradient(rgba(26, 8, 8, 0.90), rgba(0, 0, 0, 0.95)),
        url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
   
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.profile-container{
    background: transparent;
    border-radius:20px;
    padding:25px;
    width:100%;
    max-width:700px;
}

.profile-flex{
    display:flex;
    gap:20px;
    align-items:flex-start;
}

.profile-left{
    width:150px;
    text-align:center;
}

.profile-left img{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #667eea;
}

/* 🔥 NAME + EMAIL STYLE */
.profile-info{
    margin-top:10px;
}

.profile-info h4{
    font-size:16px;
    color: #f4f0f0;
}

.profile-info p{
    font-size:13px;
    color: rgb(247, 242, 242);
}

.profile-left input{
    margin-top:10px;
}

.profile-right{
    flex:1;
}

label{
    font-size:13px;
    color: #fffbfb;
    font-weight:600;
}

input{
    width:100%;
    padding:10px;
    margin:5px 0 12px;
    border-radius:8px;
    border:1px solid #ccc;
}

.error{
    color:red;
    font-size:12px;
    margin-top:-8px;
    margin-bottom:8px;
}

.btn{
    background:#ff6b6b;
    color:#fff;
    padding:12px;
    border:none;
    border-radius:30px;
    width:100%;
    cursor:pointer;
}

@media(max-width:600px){
    .profile-flex{
        flex-direction:column;
        align-items:center;
    }
}
</style>
</head>

<body>

<div class="profile-container">

<form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">

<div class="profile-flex">

    <!-- LEFT IMAGE -->
    <div class="profile-left">
        <img src="<?php echo !empty($user['profile_img']) ? $user['profile_img'] : 'default.png'; ?>">

        <!-- 🔥 NAME + EMAIL SHOW -->
        <div class="profile-info">
            <h4><?php echo $user['first_name'] . " " . $user['last_name']; ?></h4>
            <p><?php echo $user['email']; ?></p>
        </div>

        <input type="file" name="profile_img">
    </div>

    <!-- RIGHT FORM -->
    <div class="profile-right">

        <label>First Name</label>
        <input type="text" id="fname" name="first_name" value="<?php echo $user['first_name']; ?>">
        <div id="fnameErr" class="error"></div>

        <label>Last Name</label>
        <input type="text" id="lname" name="last_name" value="<?php echo $user['last_name']; ?>">
        <div id="lnameErr" class="error"></div>

        <label>Email</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>">
        <div id="emailErr" class="error"></div>

        <label>Contact</label>
        <input type="text" id="contact" name="contact" value="<?php echo $user['contact']; ?>">
        <div id="contactErr" class="error"></div>

        <label>Address</label>
        <input type="text" id="address" name="address" value="<?php echo $user['address']; ?>">
        <div id="addressErr" class="error"></div>

        <button type="submit" name="update_profile" class="btn">Update Profile</button>

    </div>

</div>
</form>

</div>

<script>
function validateForm(){
    let valid = true;

    document.querySelectorAll('.error').forEach(e => e.innerHTML="");

    let fname = document.getElementById('fname').value.trim();
    let lname = document.getElementById('lname').value.trim();
    let email = document.getElementById('email').value.trim();
    let contact = document.getElementById('contact').value.trim();

    if(fname === ""){
        document.getElementById('fnameErr').innerHTML="First name required";
        valid=false;
    }

    if(lname === ""){
        document.getElementById('lnameErr').innerHTML="Last name required";
        valid=false;
    }

    if(!email.includes("@")){
        document.getElementById('emailErr').innerHTML="Valid email required";
        valid=false;
    }

    if(contact.length < 7){
        document.getElementById('contactErr').innerHTML="Invalid contact";
        valid=false;
    }

    return valid;
}
</script>

</body>
</html>