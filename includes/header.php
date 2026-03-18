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

// Check if user has any bookings
$hasBooking = false;
if(isset($_SESSION["user_id"])){
    $user_id = (int)$_SESSION["user_id"];
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

<title>CineMa Ghar</title>
<link rel="shortcut icon" href="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:"Poppins",sans-serif;}
body{background:#0d1117;color:white;}
.site-header{background:#353634;padding:10px 0;border-bottom:2px solid #30363d;position:sticky;top:0;z-index:999;}
.navbar{max-width:1200px;height:40px;margin:auto;display:flex;align-items:center;justify-content:space-between;}
.logo-area{display:flex;align-items:center;gap:20px;}
.logo-img{width:45px;height:45px;border-radius:50%;object-fit:cover;border:2px solid #f6fa0b;}
.logo-text{font-size:1.4rem;color:#f6fa0b;font-weight:600;}
.nav-links{display:flex;gap:38px;}
.nav-links a{color:#c9d1d9;text-decoration:none;font-weight:700;position:relative;padding-bottom:5px;transition:.3s;}
.nav-links a::before{content:"";width:0%;height:2px;position:absolute;left:0;bottom:0;background:#ff5670;transition:.3s;}
.nav-links a:hover::before{width:100%;}
.search-form{width:260px;}
.search-form input{width:100%;height:37px;padding:8px 15px;background:#f7d154;border:2px solid #2563eb;border-radius:30px;outline:none;}
.search-form input:focus{border-color:#ff1a3c;box-shadow:0 0 10px rgba(255,0,0,0.4);}
.profile-container{position:relative;cursor:pointer;}
.profile-btn{background:#fddd26;border-radius:50%;width:45px;height:45px;display:flex;align-items:center;justify-content:center;overflow:hidden;transition:.3s;}
.profile-btn:hover{background:#1a02f1;}
.profile-btn img{width:100%;height:100%;object-fit:cover;border-radius:50%;}
.dropdown-menu{position:absolute;right:0;top:55px;width:220px;background:#770502;border-radius:12px;box-shadow:0 0 15px rgba(0,0,0,0.5);display:none;flex-direction:column;overflow:hidden;}
.dropdown-menu.show{display:flex;}
.dropdown-menu a{padding:12px 15px;color:#ddd;text-decoration:none;transition:.3s;}
.dropdown-menu a:hover{background:#bc06c2;color:white;}
.wrapper-right{display:flex;align-items:center;gap:40px;}
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

    <!-- LOGO -->
    <div class="logo-area">
      <img src="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" class="logo-img">
      <h1 class="logo-text">CineMa घर</h1>
    </div>

    <!-- NAVIGATION -->
    <nav class="nav-links">
      <a href="/Movie_Booking_Project_1/index.php">Home</a>
      <a href="/Movie_Booking_Project_1/movies.php">Movies</a>
      <a href="/Movie_Booking_Project_1/about.php">About</a>
      <a href="/Movie_Booking_Project_1/contact.php">Contact</a>
    </nav>

    <!-- RIGHT SIDE -->
    <div class="wrapper-right">

      <form method="get" action="/Movie_Booking_Project_1/search.php" class="search-form">
        <input type="text" name="q" placeholder="Search for movies..." required>
      </form>

      <!-- PROFILE -->
      <div class="profile-container">
        <div class="profile-btn">
          <?php
          if(isset($_SESSION["profile_img"]) && !empty($_SESSION["profile_img"])){
              echo '<img src="'.htmlspecialchars($_SESSION["profile_img"]).'" alt="Profile">';
          } else {
              echo '👤';
          }
          ?>
        </div>

        <div class="dropdown-menu">

        <?php if(!isset($_SESSION["user_id"])): ?>

            <a href="/Movie_Booking_Project_1/signup.php">Sign Up</a>
            <a href="/Movie_Booking_Project_1/login.php">Login</a>
            <a href="/Movie_Booking_Project_1/admin/admin_login.php" target="_blank">Admin Login</a>

        <?php else: ?>

            <a href="/Movie_Booking_Project_1/myprofile.php" style="font-weight:700; color:#fff;">
                <?php if(isset($_SESSION["profile_img"]) && !empty($_SESSION["profile_img"])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_img']); ?>" style="width:25px; height:25px; border-radius:50%; margin-right:5px; vertical-align:middle;">
                <?php else: ?>
                    👤
                <?php endif; ?>
                <?php echo htmlspecialchars($_SESSION['first_name']); ?> Profile
            </a>

            <?php if($hasBooking || !empty($_SESSION['has_booking'])): ?>
    <a href="/Movie_Booking_Project_1/booking_history.php">Booking History</a>
<?php endif; ?>

            <a href="/Movie_Booking_Project_1/logout.php">Logout</a>

        <?php endif; ?>

        </div>
      </div>

    </div>
  </div>
</header>