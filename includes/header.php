<?php
// header.php

// Database connection
$host = "localhost";
$user = "root";
$pass = "Milan@1234";
$db   = "movie_booking_project_1";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch locations
$locations = $conn->query("SELECT * FROM locations ORDER BY name ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CineMa Ghar</title>

  <!-- Favicon (Circular Image) -->
  <link rel="shortcut icon" href="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">

  <style>
    /* Basic Reset */
    *{
      margin:0; padding:0;
      box-sizing:border-box;
      font-family:"Poppins",sans-serif;
    }

    body{
      background:#0d1117;
      color:#fff;
    }

    /* Header */
    .site-header{
      background:#353634ff;
      border-bottom:2px solid #30363d;
      padding:9px 0;
      position:sticky;
      top:0;
      z-index:1000;
    }

    .navbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      width:100%;
      max-width:1200px;
      margin:auto;
    }

    /* ====== LOGO AREA WITH IMAGE ====== */
    .logo-area{
      display:flex;
      align-items:center;
      gap:10px;
    }

    .logo-img{
      width:45px;
      height:45px;
      border-radius:50%;
      object-fit:cover;
      border:2px solid #f6fa0bff;
    }

    .logo-text{
      font-size:1.4rem;
      font-weight:600;
      color:#f6fa0bff;
      letter-spacing:1.5px;
    }

    /* Nav Links */
    .nav-links{
      flex:2;
      display:flex;
      justify-content:center;
      align-items:center;
      gap:20px;
    }

    .nav-links a{
      color:#c9d1d9;
      text-decoration:none;
      font-size:1rem;
      font-weight:700;
      transition:0.3s;
      margin: 9px;
      position:relative;
      padding-bottom:5px;
    }

    .nav-links a::before{
      content:"";
      position:absolute;
      left:0;
      bottom:0;
      width:0%;
      height:2px;
      background:#ff586eff;
      transition:width 0.3s;
    }

    .nav-links a:hover::before{
      width:100%;
    }

    /* Search */
    .search-form{
      display:inline-block;
      position:relative;
      width:220px;
    }

    .search-form input{
      width:100%;
      padding:8px 15px;
      background:#f7d154ff;
      border:2px solid #2563eb;
      border-radius:30px;
      font-size:15px;
      outline:none;
    }

    .search-form input:focus{
      border-color:#ff1a3c;
      box-shadow:0 0 10px rgba(255,0,0,0.4);
    }

    @media(max-width:768px){
      .navbar{flex-direction:column; gap:10px;}
      .nav-links{flex-wrap:wrap;}
    }
  </style>
</head>

<body>

<header class="site-header">
  <div class="navbar">

    <!-- ====== LOGO WITH IMAGE ====== -->
    <div class="logo-area">
      <img src="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" class="logo-img">
      <h1 class="logo-text">🎬 CineMa घर</h1>
    </div>

    <!-- Navigation -->
    <nav class="nav-links">
      <a href="/Movie_Booking_Project_1/index.php">Home</a>
      <a href="/Movie_Booking_Project_1/movies.php">Movies</a>
      <a href="/Movie_Booking_Project_1/about.php">About</a>
      <a href="/Movie_Booking_Project_1/feedback.php">Feedback</a>
      <a href="/Movie_Booking_Project_1/contact.php">Contact</a>
    </nav>

    <!-- Search -->
    <form method="get" action="/Movie_Booking_Project_1/search.php" class="search-form">
      <input type="text" name="q" placeholder="Search movies..." required>
    </form>

  </div>
</header>

<main class="container">
