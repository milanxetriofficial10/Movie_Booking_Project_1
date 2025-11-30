<?php
// admin/dashboard.php
session_start();
require_once '../includes/db.php';

//  IF ADMIN NOT LOGGED 
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

//  Enable debugging during setup (you can remove later)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = db_connect();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Safe counts with error checks
$totMovies = 0;
$totShows = 0;
$totBookings = 0;

if ($result = $conn->query("SELECT COUNT(*) as c FROM movies")) {
    $row = $result->fetch_assoc();
    $totMovies = $row['c'];
}

if ($conn->query("SHOW TABLES LIKE 'shows'")->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as c FROM shows");
    $row = $result->fetch_assoc();
    $totShows = $row['c'];
}

if ($result = $conn->query("SELECT COUNT(*) as c FROM bookings")) {
    $row = $result->fetch_assoc();
    $totBookings = $row['c'];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/movie-booking/admin/assets/css/admin.css">
  <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
  <title>Admin Dashboard</title>
  <style>
    body {font-family: Arial, sans-serif; background: #f3f4f6; margin:0;}
    .admin-nav {background:#222; color:#fff; padding:12px;}
    .admin-nav a {color:#fff; margin-right:15px; text-decoration:none;}
    .admin-container {padding:30px;}
    .cards {display:flex; gap:20px; margin-top:25px;}
    .card {background:#fff; flex:1; text-align:center; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
    .card strong {font-size:2em; color:#1e40af;}
  </style>
</head>
<body>
<nav class="admin-nav">
  <a href="dashboard.php">Dashboard</a> |
  <a href="movies.php">Add Movies</a> |
  <a href="slider.php">Add Slider</a> |
  <a href="screens.php">Screens</a> |
  <a href="shows.php">Shows</a> |
  <a href="bookings.php">Bookings</a> |
  <a href="top_news.php">Add Top News</a> |
  <a href="admin_location.php">Add location</a> |
  <a href="logout.php">Logout</a>
</nav>

<main class="admin-container">
  <h2>🎬 Welcome, <?= htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
  <p>Quick stats for your cinema system:</p>

  <div class="cards">
    <div class="card">Movies<br><strong><?= htmlspecialchars($totMovies) ?></strong></div>
    <div class="card">Shows<br><strong><?= htmlspecialchars($totShows) ?></strong></div>
    <div class="card">Bookings<br><strong><?= htmlspecialchars($totBookings) ?></strong></div>
  </div>
</main>
</body>
</html>
