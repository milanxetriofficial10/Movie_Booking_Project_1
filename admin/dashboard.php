<?php
// admin/dashboard.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = db_connect();

// Counts
$totMovies = $totShows = $totBookings = 0;

if ($result = $conn->query("SELECT COUNT(*) as c FROM movies")) {
    $totMovies = $result->fetch_assoc()['c'];
}

if ($conn->query("SHOW TABLES LIKE 'shows'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as c FROM shows");
    $totShows = $result->fetch_assoc()['c'];
}

if ($result = $conn->query("SELECT COUNT(*) as c FROM bookings")) {
    $totBookings = $result->fetch_assoc()['c'];
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/x-icon">
<title>Admin Dashboard</title>

<style>
body{
  margin:0;
  font-family:Arial, sans-serif;
  background:#f1f5f9;
}

/* WRAPPER */
.admin-wrapper{
  display:flex;
}

/* SIDEBAR */
.sidebar{
  width:220px;
  height:100vh;
  background:#111827;
  color:#fff;
  padding-top:20px;
  position:fixed;
  transition:0.3s;
}

.sidebar.hide{
  transform:translateX(-100%);
}

.sidebar h2{
  text-align:center;
  margin-bottom:30px;
  color:#facc15;
}

.sidebar a{
  display:block;
  padding:12px 20px;
  color:#cbd5e1;
  text-decoration:none;
  transition:0.3s;
}

.sidebar a:hover{
  background:#1e293b;
  color:#fff;
  padding-left:25px;
}

/* MAIN */
.main{
  margin-left:220px;
  padding:30px;
  width:100%;
  transition:0.3s;
}

.main.full{
  margin-left:0;
}

/* TOP BAR */
.topbar{
  display:flex;
  align-items:center;
  gap:15px;
  margin-bottom:20px;
}

/* MENU ICON */
.menu-btn{
  font-size:24px;
  cursor:pointer;
  background:#111827;
  color:#fff;
  padding:8px 12px;
  border-radius:6px;
}

/* HEADER */
.header{
  background:#fff;
  padding:15px 20px;
  border-radius:10px;
  box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* CARDS */
.cards{
  display:flex;
  gap:20px;
  margin-top:25px;
  flex-wrap:wrap;
}

.card{
  flex:1;
  min-width:200px;
  background:#fff;
  padding:25px;
  border-radius:12px;
  text-align:center;
  box-shadow:0 4px 12px rgba(0,0,0,0.1);
  transition:0.3s;
}

.card:hover{
  transform:translateY(-5px);
}

.card strong{
  font-size:2.2em;
  color:#2563eb;
}

/* LOGOUT */
.logout{
  color:#f87171 !important;
}
</style>

</head>

<body>

<div class="admin-wrapper">

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <h2>🎬 Admin</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="movies.php">Add Movies</a>
    <a href="slider.php">Slider</a>
    <a href="screens.php">Screens</a>
    <a href="shows.php">Shows</a>
    <a href="bookings.php">Bookings</a>
    <a href="top_news.php">Top News</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <!-- MAIN -->
  <div class="main" id="main">

    <!-- TOP BAR -->
    <div class="topbar">
      <div class="menu-btn" onclick="toggleMenu()">☰</div>
      <h2>Dashboard</h2>
    </div>

    <div class="header">
      <h3>Welcome, <?= htmlspecialchars($_SESSION['admin_name']); ?> 👋</h3>
      <p>Manage your cinema system easily.</p>
    </div>

    <div class="cards">
      <div class="card">
        <h3>🎥 Movies</h3>
        <strong><?= $totMovies ?></strong>
      </div>

      <div class="card">
        <h3>⏰ Shows</h3>
        <strong><?= $totShows ?></strong>
      </div>

      <div class="card">
        <h3>🎟 Bookings</h3>
        <strong><?= $totBookings ?></strong>
      </div>
    </div>

  </div>

</div>

<script>
function toggleMenu(){
  document.getElementById("sidebar").classList.toggle("hide");
  document.getElementById("main").classList.toggle("full");
}
</script>

</body>
</html>