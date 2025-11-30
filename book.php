<?php
require 'includes/db.php';
$conn = db_connect();

$show_id = (int)($_GET['show_id'] ?? 0);
if($show_id <= 0){ echo "<p>Invalid show ID</p>"; exit; }

$show = $conn->query("
    SELECT s.*, m.title, m.genre, m.duration, m.poster,
           sc.screen_name,
           COALESCE(sc.rows, 5) AS rows_count,
           COALESCE(sc.cols, 8) AS cols_count,
           COALESCE(s.price, 350) AS price_amount
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    WHERE s.id=$show_id
")->fetch_assoc();

if(!$show){ echo "<p>Show not found</p>"; exit; }

if(empty($show['poster'])) $show['poster'] = 'default.jpg';
if(empty($show['genre'])) $show['genre'] = 'Drama';
if(empty($show['duration'])) $show['duration'] = '2h 15m';
if(empty($show['language'])) $show['language'] = 'English';

$stmt = $conn->prepare("SELECT seats FROM bookings WHERE show_id=?");
$stmt->bind_param('i', $show_id);
$stmt->execute();
$res = $stmt->get_result();
$booked = [];
while($r = $res->fetch_assoc()){
    $arr = json_decode($r['seats'], true);
    if(is_array($arr)) $booked = array_merge($booked, $arr);
}
$booked = array_unique($booked);
$booked_js = json_encode($booked);

$price = (float)$show['price_amount'];
$rows  = (int)$show['rows_count'];
$cols  = (int)$show['cols_count'];
?>

<?php
// header.php
// header.php

// Database connection
$host = "localhost";
$user = "root";
$pass = "Milan@1234";
$db   = "movie_booking_project_1"; // change to your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch locations for dropdown
$locations = $conn->query("SELECT * FROM locations ORDER BY name ASC");
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

</form>
  </div>

</header>

<main class="container">
<style>




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
  width: 100%;
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
/* Make nav-links flex-align items center */
.nav-links {
  flex: 2;
  display: flex;
  justify-content: center;
  align-items: center; /* vertically center all items */
  gap: 20px;
}

/* Style location dropdown like nav link */
.location-dropdown select {
  padding: 5px 2px;
  border-radius: 10px;
  border: 1px solid #ff586eff;
  background: #ddbe58ff;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
}

.location-dropdown select:hover {
  border-color: #ff1a3c;
  transform: scale(1.05);
}

/* Remove default form margin */
.location-dropdown {
  margin: 0;
}

/* Responsive adjustment for small screens */
@media (max-width: 768px) {
  .nav-links {
    flex-direction: column;
    gap: 10px;
  }

  .location-dropdown select {
    width: 100%;
    max-width: 200px;
  }
}


.nav-links a {
  color: #c9d1d9;
  text-decoration: none;
  font-size: 1rem;
  font-weight: 700;
  margin-right: 9px;
    font-family: 'Kari', cursive, sans‑serif;
  transition: 0.3s;
   position: relative;
  padding-bottom: 5px; /* space for underline */
}

.nav-links a::after {
  content: "→"; /* arrow symbol; you can change to "" for line */
  position: absolute;
  left: 50%;
  transform: translateX(-50%) translateY(5px); /* center below text */
  opacity: 0;
  transition: all 0.3s;
  font-size: 14px;
  color: #58a6ff; /* arrow color */
}

.nav-links a:hover::after {
  opacity: 1;
  transform: translateX(-50%) translateY(0px); /* move arrow up slightly */
}

/* Optional: underline instead of arrow */
.nav-links a::before {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 0%;
  height: 2px;
  background: #ff586eff;
  transition: width 0.3s;
}

.nav-links a:hover::before {
  width: 100%;
}

//* ===== Form Container ===== */
.search-form {
    display: inline-block;
    position: relative;
    max-width: 600px;
    width: 100%;
    margin: 30px auto;
    
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
<div class="movie-top">
  <div class="poster-slide">
    <img src="/Movie_Booking_Project_1/uploads/<?= htmlspecialchars($show['poster']) ?>" 
         alt="<?= htmlspecialchars($show['title']) ?>">
    <div class="movie-overlay">
      <h2><?= htmlspecialchars($show['title']) ?></h2>
      <div class="movie-tags">
        <span class="tag genre"><?= htmlspecialchars($show['genre']) ?></span>
        <span class="tag duration"><?= htmlspecialchars($show['duration']) ?></span>
        <span class="tag language"><?= htmlspecialchars($show['language']) ?></span>
      </div>
      <p>Hall: <?= htmlspecialchars($show['screen_name']) ?></p>
      <p>Price: Rs <?= $price ?></p>
    </div>
  </div>
</div>

<div class="seat-section">
  <h3>Select Your Seats 🎟️</h3>
  <div id="seat-map"></div>
</div>

<div class="booking-section">
  <form id="booking-form" method="post" action="booking_review.php">
    <input type="hidden" name="show_id" value="<?=$show_id?>">
    <input type="hidden" name="seats" id="selected-seats">
    <label>Your Name:
      <input type="text" name="user_name" placeholder="Enter your name" required>
    </label>
    <label>Your Email:
      <input type="email" name="user_email" placeholder="Enter your email" required>
    </label>
    <label>Your Mobile:
      <input type="text" name="user_mobile" placeholder="Enter your mobile number" required>
    </label>
    <p>Total: Rs <span id="total">0</span></p>
    <button type="submit">Review Booking</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const bookedSeats = <?=$booked_js?>;
  const seatMap = document.getElementById('seat-map');
  const selectedSeatsInput = document.getElementById('selected-seats');
  const totalSpan = document.getElementById('total');
  const price = <?=$price?>;
  let selected = [];

  // set fixed grid based on PHP rows/cols
  seatMap.style.display = 'grid';
  seatMap.style.gridTemplateColumns = `repeat(<?=$cols?>, 50px)`; // use exact column count
  seatMap.style.gridAutoRows = '50px'; // row height
  seatMap.style.gap = '10px';
  seatMap.style.width = '100%';
  seatMap.style.padding = '20px';
  seatMap.style.borderRadius = '16px';
  seatMap.style.backgroundColor = 'rgba(30, 41, 59, 0.9)';

  for(let r=1; r<=<?=$rows?>; r++){
    for(let c=1; c<=<?=$cols?>; c++){
      const seatId = r+'-'+c;
      const seatDiv = document.createElement('div');
      seatDiv.textContent = seatId;
      seatDiv.classList.add('seat');
      if(bookedSeats.includes(seatId)){
        seatDiv.classList.add('booked');
      } else {
        seatDiv.addEventListener('click', function(){
          if(seatDiv.classList.contains('selected')){
            seatDiv.classList.remove('selected');
            selected = selected.filter(s => s !== seatId);
          } else {
            seatDiv.classList.add('selected');
            selected.push(seatId);
          }
          selectedSeatsInput.value = JSON.stringify(selected);
          totalSpan.textContent = selected.length * price;
        });
      }
      seatMap.appendChild(seatDiv);
    }
  }
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('booking-form');
    const nameInput = form.querySelector('input[name="user_name"]');
    const mobileInput = form.querySelector('input[name="user_mobile"]');

    form.addEventListener('submit', (e) => {
        let valid = true;
        let messages = [];

        // Name validation: number not allowed
        if(/\d/.test(nameInput.value)){
            valid = false;
            messages.push("Name मा number हुनु हुँदैन।");
        }


        // Mobile validation: only digits, exactly 10 digits
        const mobileValue = mobileInput.value.trim();
        if(!/^\d{10}$/.test(mobileValue)){
            valid = false;
            messages.push("Mobile number केवल 10 अंकको हुनु पर्दछ।");
        }

        if(!valid){
            e.preventDefault(); // stop form submission
            alert(messages.join("\n"));
        }
    });

    // Optional: live input filtering
    nameInput.addEventListener('input', () => {
        nameInput.value = nameInput.value.replace(/[0-9]/g, '');
    });
    mobileInput.addEventListener('input', () => {
        mobileInput.value = mobileInput.value.replace(/\D/g, '').slice(0,10);
    });
});

</script>

<?php require 'includes/footer.php'; ?>

<style>
body { background: #3e4849ff; color:#f1f5f9; font-family:Arial,sans-serif; }

.movie-top {
  display:flex;
  justify-content:center;
}

.poster-slide {
  width:100%;
  max-width:1500px;
  position:relative;
  overflow:hidden;
  border-radius:16px;
  box-shadow:0 0 30px rgba(37,99,235,0.5);
}

.poster-slide img {
  width: 100%;
  height: 450px;
  object-fit:cover;
  transition: transform 1.5s ease, filter 1.5s ease;
}

.poster-slide img:hover {
  transform:scale(1.05);
  filter:brightness(1.1);
}

.movie-overlay {
  position:absolute;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background: rgba(5, 5, 5, 0.45);
  color:#f1f5f9;
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  padding:20px;
  text-align:center;
  opacity:0;
  transform:translateY(-20px);
  animation: fadeIn 1s forwards;
}

.movie-overlay h2 { font-size:3rem; font-family: 'Merriweather', Georgia, 'Times New Roman', serif; margin-bottom:10px; color: rgba(255, 255, 255, 0.99); }
.movie-tags { margin-bottom:10px; display:flex; gap:20px; flex-wrap:wrap; justify-content:center; }
.tag { padding:5px 20px; font-family: 'Merriweather', Georgia, 'Times New Roman', serif; border-radius:13px; color: #fff; font-weight:500; font-size:1.3rem; }
.genre { font-family: 'Merriweather', Georgia, 'Times New Roman', serif; background: #3b82f6; }
.duration {font-family: 'Merriweather', Georgia, 'Times New Roman', serif; background: #aa7707ff; }
.language { font-family: 'Merriweather', Georgia, 'Times New Roman', serif;background: #f59e0b; }
.movie-overlay p {font-family: 'Merriweather', Georgia, 'Times New Roman', serif; margin:5px 0; font-size: 1.7rem; }

@keyframes fadeIn {
  to { opacity:1; transform:translateY(0); }
}

.seat-section { display:flex; flex-direction:column; align-items:center; margin:20px auto; max-width:500px; }
#seat-map { min-height:400px; width:100%; }

.seat { width:50px; height:50px; border-radius:10px; border:1px solid #475569; background:#334155; color:#f1f5f9; display:flex; justify-content:center; align-items:center; cursor:pointer; transition:0.25s; }
.seat:hover:not(.booked){ transform:scale(1.2); background:#3b82f6; box-shadow:0 0 15px rgba(59,130,246,0.7); }
.seat.booked { background:#64748b; color:#cbd5e1; cursor:not-allowed; }
.seat.selected { background:#2563eb; box-shadow:0 0 20px rgba(37,99,235,0.9); }

.booking-section { display:flex; justify-content:center; margin:20px auto; max-width:500px; }
form { background:rgba(73,81,99,0.85); padding:25px; border-radius:16px; box-shadow:0 0 25px rgba(37,99,235,0.4); width:100%; }
form label { display:block; margin:15px 0 5px; font-weight:500; }
form input { width:100%; padding:10px; border-radius:8px; border:none; background:#1e293b; color:#f1f5f9; transition:0.3s; }
form input:focus { outline:none; background:#0f172a; box-shadow:0 0 10px rgba(59,130,246,0.6); }
button { background:linear-gradient(90deg,#2563eb,#1d4ed8); margin-top:20px; color:#fff; border:none; padding:12px; border-radius:10px; width:100%; cursor:pointer; }
button:hover { transform:scale(1.03); box-shadow:0 0 15px rgba(59,130,246,0.6); }

@media(max-width:900px){
  .poster-slide { width:95%; }
  form { width:95%; }
}
</style>
