<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "Milan@1234";
$db   = "movie_booking_project_1";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:"Poppins",sans-serif;
}
body{
  background:#0d1117;
  color:white;
}

/* HEADER */
.site-header{
  background:#353634;
  padding:10px 0;
  border-bottom:2px solid #30363d;
  position:sticky;
  top:0;
  z-index:999;
}

.navbar{
  max-width:1200px;
  height: 30px;
  margin:auto;
  display:flex;
  align-items:center;
  justify-content:space-between;
}

/* LOGO */
.logo-area{
  display:flex;
  align-items:center;
  gap:20px;
}
.logo-img{
  width:45px;
  height:45px;
  border-radius:50%;
  object-fit:cover;
  border:2px solid #f6fa0b;
}
.logo-text{
  font-size:1.4rem;
  color:#f6fa0b;
  font-weight:600;
}

/* NAVIGATION */
.nav-links{
  display:flex;
  gap:38px;
}
.nav-links a{
  color:#c9d1d9;
  text-decoration:none;
  font-weight:700;
  position:relative;
  padding-bottom:5px;
  transition:.3s;
}
.nav-links a::before{
  content:"";
  width:0%;
  height:2px;
  position:absolute;
  left:0; bottom:0;
  background:#ff5670;
  transition:.3s;
}
.nav-links a:hover::before{
  width:100%;
}

/* SEARCH BAR */
.search-form{
  width:260px;
  
}
.search-form input{
  width:100%;
  height: 37px;
  padding:8px 15px;
  background:#f7d154;
  border:2px solid #2563eb;
  border-radius:30px;
  outline:none;
}
.search-form input:focus{
  border-color:#ff1a3c;
  box-shadow:0 0 10px rgba(255,0,0,0.4);
}

/* ============ PROFILE DROPDOWN FIXED ============ */
.profile-container{
  position:relative;
  user-select:none;
}

.profile-btn{
  background: #fddd26ff;
  padding:10px 16px;
  border-radius:50%;
  color:white;
  cursor:pointer;
  width:45px;
  height:45px;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:.3s;
}

.profile-btn:hover{
  background: #1a02f1ff;
  border-color:#777;
}

/* ARROW hidden now */
.arrow{
  display:none;
}

/* DROPDOWN */
.dropdown-menu{
  position:absolute;
  right:0;
  top:50px;
  width:200px;
  background: #770502ff;
  border:1px solid #444;
  border-radius:12px;
  box-shadow:0 0 15px rgba(0,0,0,0.5);
  display:none;
  flex-direction:column;
  overflow:hidden;
  animation:fade .25s ease-out forwards;
}

@keyframes fade{
  from{opacity:0; transform:translateY(-8px);}
  to{opacity:1; transform:translateY(0);}
}

.dropdown-menu.show{
  display:flex;
}

.dropdown-menu a{
  padding:12px 15px;
  color:#ddd;
  text-decoration:none;
  transition:.3s;
  font-weight:500;
}
.dropdown-menu a:hover{
  background: #bc06c2ff;
  color: white;
}

.wrapper-right{
  display:flex;
  align-items:center;
  gap: 40px;
}

/* RESPONSIVE */
@media(max-width:768px){
  .navbar{
    flex-direction:column;
    gap:15px;
  }
  .nav-links{
    flex-wrap:wrap;
    justify-content:center;
  }
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
      <a href="/Movie_Booking_Project_1/feedback.php">Feedback</a>
      <a href="/Movie_Booking_Project_1/contact.php">Contact</a>
    </nav>

    <!-- RIGHT SIDE -->
    <div class="wrapper-right">

      <form method="get" action="/Movie_Booking_Project_1/search.php" class="search-form">
        <input type="text" name="q" placeholder="Search for movies..." required>
      </form>

      <!-- PROFILE ICON ONLY -->
      <div class="profile-container">
        <div class="profile-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
  <circle cx="12" cy="7" r="4"/>
</svg>

        </div>

        <div class="dropdown-menu">

         <?php if(!isset($_SESSION["user_id"])): ?>

    <a href="/Movie_Booking_Project_1/signup.php">Sign Up</a>
    <a href="/Movie_Booking_Project_1/login.php">Login</a>
    <a href="#">Setting</a>
    <a href="/Movie_Booking_Project_1/admin/admin_login.php">Admin Login</a>

<?php else: ?>
    <!-- Display First Name Only -->
    <a href="/Movie_Booking_Project_1/myprofile.php" style="font-weight:700; color:#fff;">
        👤 <?php echo $_SESSION['first_name'] ?? 'User'; ?> Profile 
    </a>
    <a href="/Movie_Booking_Project_1/logout.php">Logout</a>
    <a href="/Movie_Booking_Project_1/admin/admin_login.php">Admin Login</a>

<?php endif; ?>


        </div>
      </div>

    </div>

  </div>
</header>

<main class="container">
