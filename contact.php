<?php
require 'includes/header.php';
require 'includes/db.php';
$conn = db_connect();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$message = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $full_name = $_POST['full_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO contact_info (user_id, full_name, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $full_name, $email, $phone);

    if($stmt->execute()){
        $message = "✔ Contact information saved successfully!";
    } else {
        $message = "❌ Something went wrong. Try again.";
    }
}
?>

<style>
body{
    margin:0;
    padding:0;
    font-family: Poppins, sans-serif;
    background: linear-gradient(135deg,#0f172a,#1e293b,#0f172a);
    color:#fff;
}

.contact-container{
    max-width:600px;
    margin:60px auto;
    padding:30px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(6px);
    border-radius:15px;
    box-shadow:0 15px 40px rgba(0,0,0,0.4);
    animation:fadeIn 1.3s ease;
}

@keyframes fadeIn{
    0%{opacity:0; transform:translateY(40px);}
    100%{opacity:1; transform:translateY(0);}
}

h2{
    text-align:center;
    margin-bottom:20px;
    font-size:2rem;
    background:linear-gradient(90deg,#38bdf8,#60a5fa);
    -webkit-text-fill-color:transparent;
    -webkit-background-clip:text;
}

label{
    font-weight:500;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0 20px;
    border:none;
    border-radius:10px;
    background:#ffffff1a;
    color:#fff;
    font-size:1rem;
}

input:focus{
    outline:2px solid #38bdf8;
}

button{
    width:100%;
    padding:12px;
    background:linear-gradient(90deg,#3b82f6,#2563eb);
    color:#fff;
    border:none;
    font-size:1.1rem;
    font-weight:bold;
    border-radius:10px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:scale(1.05);
}

.message{
    text-align:center;
    margin-bottom:15px;
    font-size:1rem;
    color:#a5f3fc;
}
</style>

<div class="contact-container">
    <h2>Contact Information</h2>

    <?php if(!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Enter your full name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="98XXXXXXXX" required>

        <button type="submit">Save Contact Info</button>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
