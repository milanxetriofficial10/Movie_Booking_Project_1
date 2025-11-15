<?php
// header.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CineMa Ghar</title>
</head>
<body>

<header class="site-header">
  <div class="navbar">
    <div class="logo-area">
      <h1 class="logo">🎬 CineMa घर</h1>
    </div>



    <nav class="nav-links">
      <a href="/Movie_Booking_Project_1/index.php">Home</a>
      <a href="/Movie_Booking_Project_1/movies.php">CineMa Ghar</a>
      <a href="/Movie_Booking_Project_1/about.php">About</a>
      <a href="/Movie_Booking_Project_1/feedback.php">Feedback</a>
      <a href="/Movie_Booking_Project_1/contact.php">Contact Us</a>
    </nav>
     <form method="get" action="/Movie_Booking_Project_1/search.php" class="search-form">
    <input type="text" name="q" placeholder="Search movies, shows..." required>

</form>
  </div>

</header>

<main class="container">
<style>
   

    /* Basic Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

body {
  background: #0d1117;
  color: #fff;
}

/* Navbar */
.site-header {
  background: #353634ff;
  border-bottom: 2px solid #30363d;
  padding: 9px 0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

/* Navbar Layout Update */
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 90%;
  max-width: 1200px;
  margin: auto;
  position: relative;
}

/* Left: Logo */
.logo-area {
  flex: 1;
}

.logo {
  font-size: 1.4rem;
  font-weight: 600;
  color: #f6fa0bff;
  letter-spacing: 1.5px;
}

/* Center: Navigation Links */
.nav-links {
  flex: 2;
  display: flex;
  justify-content: center;
  gap: 30px;
}

.nav-links a {
  color: #c9d1d9;
  text-decoration: none;
  font-size: 1rem;
  font-weight: 700;
    font-family: 'Kari', cursive, sans‑serif;
  transition: 0.3s;
}

.nav-links a:hover {
  color: #58a6ff;
}

//* ===== Form Container ===== */
.search-form {
    display: inline-block;
    position: relative;
    max-width: 600px;
    width: 100%;
    margin: 20px auto;
    
}

/* ===== Input Field ===== */
.search-form input[type="text"] {
    width: 100%;
    height: 36px;
    padding: 1px 20px;
    background: #f7d154ff;
    border: 2px solid #2563eb;
    border-radius: 50px;
    outline: none;
    transition: all 0.3s ease;
    font-size: 16px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.search-form input[type="text"]:focus {
    border-color: #ff1a3c;
    box-shadow: 0 5px 15px rgba(255,26,60,0.3);
    transform: scale(1.02);
}

/* ===== Button ===== */
.search-form button {
    position: absolute;
    right: 2px;
    top: 2px;
    bottom: 2px;
    padding: 0 20px;
    border: none;
    background: #2563eb;
    color: #fff;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 16px;
}

.search-form button:hover {
    background: #1e40af;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(37,99,235,0.3);
}

/* ===== Smooth transition on input & button ===== */
.search-form input[type="text"],
.search-form button {
    transition: all 0.3s ease;
}

/* ===== Responsive ===== */
@media(max-width:500px){
    .search-form {
        max-width: 90%;
    }
    .search-form input[type="text"] {
        padding: 10px 15px;
        font-size: 14px;
    }
    .search-form button {
        padding: 0 15px;
        font-size: 14px;
    }
}

/* Responsive Navbar */
@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .nav-links {
    justify-content: center;
    flex-wrap: wrap;
  }

  .search-area {
    justify-content: center;
    max-width: 100%;
  }
}


</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("movieSearch");
  const results = document.getElementById("searchResults");

  input.addEventListener("input", async () => {
    const query = input.value.trim();
    if (query.length === 0) {
      results.style.display = "none";
      results.innerHTML = "";
      return;
    }

    try {
      const response = await fetch(`/Movie_Booking_Project_1/search.php?q=${encodeURIComponent(query)}`);
      const movies = await response.json();

      if (movies.length === 0) {
        results.innerHTML = "<p style='padding:10px;color:#999;'>No movies found</p>";
        results.style.display = "block";
        return;
      }

      results.innerHTML = movies.map(m => `
        <a href="/Movie_Booking_Project_1/index.php?id=${m.id}">
          <img src="/Movie_Booking_Project_1/uploads/${m.poster}" alt="${m.title}">
          <div>
            <strong>${m.title}</strong><br>
            <small style="color:#999;">${m.genre || 'Unknown Genre'}</small>
          </div>
        </a>
      `).join("");

      results.style.display = "block";
    } catch (error) {
      console.error("Search error:", error);
    }
  });

  document.addEventListener("click", (e) => {
    if (!results.contains(e.target) && e.target !== input) {
      results.style.display = "none";
    }
  });
});

</script>