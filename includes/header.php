<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* DATABASE CONNECTION */
$host = "localhost";
$user = "root";
$pass = "Milan@1234";
$db   = "movie_booking_project_1";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🔥 GET LATEST USER DATA (IMPORTANT FIX)
$profile_img = "";
$first_name = "";

if(isset($_SESSION["user_id"])){
    $user_id = (int)$_SESSION["user_id"];

    $stmt = $conn->prepare("SELECT first_name, profile_img FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if($userData){
        $profile_img = $userData['profile_img'];
        $first_name  = $userData['first_name'];

        // 🔥 UPDATE SESSION ALSO (so everywhere works)
        $_SESSION["profile_img"] = $profile_img;
        $_SESSION["first_name"]  = $first_name;
    }
}

// Check if user has bookings
$hasBooking = false;
if(isset($_SESSION["user_id"])){
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM bookings WHERE user_id=?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if($res && $res['cnt'] > 0){
        $hasBooking = true;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="shortcut icon" href="./imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Pacifico&family=Dancing+Script&family=Allura&family=Satisfy&family=Playfair+Display&family=Cormorant+Garamond&display=swap" rel="stylesheet">
<title>CineMa Ghar - Movies</title>

<style>
/* SAME DESIGN - NO CHANGE */
*{margin:0;padding:0;box-sizing:border-box;font-family:"Poppins",sans-serif;}
body{background: #0d1117;color:white;}
.site-header{background: transparent;padding:10px 0;border-bottom:2px solid #54f603;position:sticky;top:0;z-index:999;}
.navbar{max-width:1200px;height:40px;margin:auto;display:flex;align-items:center;justify-content:space-between;}
.logo-area{display:flex;align-items:center;gap:20px;}
.logo-img{width:45px;height:45px;border-radius:50%;object-fit:cover;border:2px solid #f6fa0b;}
.logo-text{font-size:1.4rem;color:#f6fa0b;font-weight:600;}
.nav-links{display:flex;gap:25px;}

.nav-links a {
    font-family: 'Playfair Display', serif; /* try any from the list */
    font-size: 1.0rem;
    color: rgb(247, 247, 247);
    text-decoration: none;
    font-weight: 520;
    padding: 4px 10px;
    position: relative;
    transition: all 0.4s ease;
}

/* Hover effect */
.nav-links a:hover {
    color: #f483f2;
    text-shadow: 0 0 6px rgba(255,107,129,0.7);
    transform: scale(1.05) rotate(-1deg);
}

/* Elegant underline animation */
.nav-links a::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 2px;
    left: 0;
    bottom: -4px;
    border-radius: 2px;
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.4s ease;
}

.nav-links a:hover::after {
    transform: scaleX(1);
    transform-origin: left;
}
/* ===== SEARCH BAR MAGIC ===== */
.search-form {
    width: 300px;
}

.search-box {
    position: relative;
    width: 100%;
}

/* INPUT FIELD */
.search-box input {
    width: 100%;
    height: 42px;
    padding: 10px 120px 10px 18px;
    border-radius: 50px;
    border: 2px solid transparent;
    outline: none;
    background: transparent;
    color: rgb(255, 255, 255);
    font-size: 14px;
    transition: all 0.4s ease;
    box-shadow: 0 0 8px rgba(106, 253, 8, 0.98);
}

/* GLOW ON FOCUS */
.search-box input:focus {
    border: 2px solid #00f7ff;
    box-shadow: 0 0 12px rgba(0,247,255,0.7),
                0 0 25px rgba(0,247,255,0.4);
}

/* PLACEHOLDER STYLE */
.search-box input::placeholder {
    color: #aaa;
    font-style: italic;
}

/* SEARCH BUTTON */
.search-box button {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);

    height: 32px;
    padding: 0 18px;
    border-radius: 30px;
    border: none;

    background: transparent;
    color: #fff;
    font-weight: bold;
    cursor: pointer;

    transition: all 0.3s ease;
}

/* BUTTON HOVER EFFECT */
.search-box button:hover {
    transform: translateY(-50%) scale(1.08);
    background: transparent;
    box-shadow: 0 0 10px rgba(0,247,255,0.8);
}

/* SMOOTH HOVER GLOW FOR INPUT */
.search-box:hover input {
    box-shadow: 0 0 15px rgba(255, 0, 204, 0.4);
}
/* ===== PROFILE ICON (CIRCLE GLOW) ===== */
.profile-container {
    position: relative;
    cursor: pointer;
}

.profile-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    overflow: hidden;

    display: flex;
    align-items: center;
    justify-content: center;

    background: linear-gradient(135deg, #ff00cc, #fbfbfd);
    box-shadow: 0 0 8px rgba(247, 24, 203, 0.5);

    transition: all 0.3s ease;
}

/* hover glow */
.profile-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 0 12px rgba(0, 247, 255, 0.8),
                0 0 25px rgba(255, 0, 204, 0.6);
}

/* image inside */
.profile-btn img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

/* ===== DROPDOWN MENU ===== */
.dropdown-menu {
    position: absolute;
    right: 0;
    top: 60px;
    width: 230px;

    background: rgba(20, 20, 30, 0.95);
    backdrop-filter: blur(10px);

    border-radius: 14px;
    padding: 10px 0;

    display: none;
    flex-direction: column;

    box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    border: 1px solid rgba(255,255,255,0.08);

    animation: dropdownFade 0.3s ease;
}

.dropdown-menu.show {
    display: flex;
}

/* dropdown animation */
@keyframes dropdownFade {
    from {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* links inside */
.dropdown-menu a {
    padding: 12px 18px;
    color: #ddd;
    text-decoration: none;
    font-size: 14px;

    display: flex;
    align-items: center;
    gap: 10px;

    transition: all 0.3s ease;
}

/* hover effect */
.dropdown-menu a:hover {
    background: linear-gradient(90deg, rgba(255,0,204,0.2), rgba(0,247,255,0.2));
    color: #fff;
    padding-left: 20px;
}

/* divider (optional feel) */
.dropdown-menu a:not(:last-child) {
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.wrapper-right{display:flex;align-items:center;gap:40px;}

/* ===== LIVE SEARCH DROPDOWN ===== */
#searchResult {
    position: absolute;
    top: 110%;
    left: 0;
    width: 100%;
    background: rgba(15,15,20,0.95);
    backdrop-filter: blur(12px);
    border-radius: 12px;
    overflow: hidden;
    display: none;
    z-index: 999;
    box-shadow: 0 10px 30px rgba(0,0,0,0.7);
}

/* live card */
.live-card {
    display: flex;
    gap: 10px;
    padding: 10px;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: 0.3s;
}

.live-card:hover {
    background: rgba(255,255,255,0.05);
}

/* image */
.live-card img {
    width: 45px;
    height: 65px;
    object-fit: cover;
    border-radius: 6px;
}

/* text */
.live-info {
    flex: 1;
}

.live-info h4 {
    margin: 0;
    font-size: 13px;
    color: #fff;
}

/* book button */
.live-btn {
    font-size: 11px;
    color: #ff5722;
    text-decoration: none;
    font-weight: bold;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const profile = document.querySelector(".profile-container");
  const menu = document.querySelector(".dropdown-menu");

  profile.addEventListener("click", () => {
    menu.classList.toggle("show");
  });

  document.addEventListener("click", (e) => {
    if (!profile.contains(e.target)) {
      menu.classList.remove("show");
    }
  });
});
</script>
</head>

<body>

<header class="site-header">
  <div class="navbar">

    <div class="logo-area">
      <img src="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" class="logo-img">
      <h1 class="logo-text">CineMa घर</h1>
    </div>

    <nav class="nav-links">
      <a href="/Movie_Booking_Project_1/index.php">Home</a>
      <a href="/Movie_Booking_Project_1/movies.php">Movies</a>
      <a href="/Movie_Booking_Project_1/about.php">About</a>
      <a href="/Movie_Booking_Project_1/contact.php">Contact</a>
    </nav>

    <div class="wrapper-right">

   <form method="get" action="/Movie_Booking_Project_1/search.php" class="search-form" autocomplete="off">
  <div class="search-box">

    <input type="text" id="liveSearch" name="q" placeholder="Search movies...">

    <button type="submit">Search</button>

    <!-- LIVE RESULT DROPDOWN -->
    <div id="searchResult"></div>

  </div>
</form>

      <!-- PROFILE -->
      <div class="profile-container">
        <div class="profile-btn">
          <?php
          if(!empty($profile_img)){
              echo '<img src="'.htmlspecialchars($profile_img).'" alt="Profile">';
          } else {
              echo '👤';
          }
          ?>
        </div>

        <div class="dropdown-menu">

    <?php if(!isset($_SESSION["user_id"])): ?>

        <a href="/Movie_Booking_Project_1/signup.php">Sign Up</a>
        <a href="/Movie_Booking_Project_1/login.php">Login</a>

        <?php if(!isset($_SESSION["admin_logged"])): ?>
            <a href="/Movie_Booking_Project_1/admin/admin_login.php" target="_blank">Admin Login</a>
        <?php else: ?>
            <a href="/Movie_Booking_Project_1/admin/dashboard.php" target="_blank">Admin Dashboard</a>
        <?php endif; ?>

    <?php else: ?>

        <a href="/Movie_Booking_Project_1/myprofile.php">
            <?php if(!empty($profile_img)): ?>
                <img src="<?php echo htmlspecialchars($profile_img); ?>" style="width:25px;height:25px;border-radius:50%;margin-right:5px;">
            <?php else: ?>
                👤
            <?php endif; ?>
            <?php echo htmlspecialchars($first_name); ?> Profile
        </a>

        <?php if($hasBooking): ?>
            <a href="/Movie_Booking_Project_1/booking_history.php">Booking History</a>
        <?php endif; ?>

        <a href="/Movie_Booking_Project_1/logout.php">Logout</a>

        <?php if(!isset($_SESSION["admin_logged"])): ?>
            <a href="/Movie_Booking_Project_1/admin/admin_login.php" target="_blank">Admin Login</a>
        <?php else: ?>
            <a href="/Movie_Booking_Project_1/admin/dashboard.php" target="_blank">Admin Dashboard</a>
        <?php endif; ?>

    <?php endif; ?>

</div>
      </div>

    </div>
  </div>
</header>
<script>
const input = document.getElementById("liveSearch");
const resultBox = document.getElementById("searchResult");

input.addEventListener("keyup", function(){
    let query = this.value.trim();

    if(query.length === 0){
        resultBox.style.display = "none";
        return;
    }

    fetch("/Movie_Booking_Project_1/search_ajax.php?q=" + query)
    .then(res => res.text())
    .then(data => {
        resultBox.innerHTML = data;
        resultBox.style.display = "block";
    });
});

/* hide when clicking outside */
document.addEventListener("click", function(e){
    if(!document.querySelector(".search-box").contains(e.target)){
        resultBox.style.display = "none";
    }
});
</script>