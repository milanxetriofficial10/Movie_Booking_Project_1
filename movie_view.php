<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();

$id = (int)($_GET['id'] ?? 0);

// Increment view count
if($id > 0){
    $conn->query("UPDATE movies SET views = views + 1 WHERE id = $id");
}

// Fetch movie
$movie = $conn->query("SELECT * FROM movies WHERE id=$id")->fetch_assoc();
if(!$movie){ 
    echo "<p style='text-align:center; font-size:1.2rem;'>Movie not found</p>"; 
    exit; 
}

// Check tables
$screenExists = $conn->query("SHOW TABLES LIKE 'screens'")->num_rows > 0;

$priceExists = false;
$columns = $conn->query("SHOW COLUMNS FROM shows")->fetch_all(MYSQLI_ASSOC);
foreach($columns as $col){
    if($col['Field'] === 'price') $priceExists = true;
}

// Fetch upcoming shows
if($screenExists){
    $stmt = $conn->prepare("
        SELECT s.*, sc.screen_name
        FROM shows s
        JOIN screens sc ON s.screen_id = sc.id
        WHERE s.movie_id = ? AND s.show_time >= NOW()
        ORDER BY s.show_time ASC
    ");
} else {
    $stmt = $conn->prepare("
        SELECT *
        FROM shows
        WHERE movie_id = ? AND show_time >= NOW()
        ORDER BY show_time ASC
    ");
}
$stmt->bind_param('i', $id);
$stmt->execute();
$shows = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?=htmlspecialchars($movie['title'])?></title>

<!-- 🔹 Timro existing CSS exactly yehi xa (unchanged) -->
<style>
/* loader ko CSS timile yaha aafai halne */
/* From Uiverse.io by Nawsome */ 
.loader {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: row;
}

.slider {
  overflow: hidden;
  background-color: white;
  margin: 0 15px;
  height: 80px;
  top: 250px;
  width: 20px;
  border-radius: 30px;
  box-shadow: 15px 15px 20px rgba(0, 0, 0, 0.1), -15px -15px 30px #fff,
    inset -5px -5px 10px rgba(0, 0, 255, 0.1),
    inset 5px 5px 10px rgba(0, 0, 0, 0.1);
  position: relative;
}

.slider::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  border-radius: 100%;
  box-shadow: inset 0px 0px 0px rgba(0, 0, 0, 0.3), 0px 420px 0 400px #2697f3,
    inset 0px 0px 0px rgba(0, 0, 0, 0.1);
  animation: animate_2 2.5s ease-in-out infinite;
  animation-delay: calc(-0.5s * var(--i));
}

@keyframes animate_2 {
  0% {
    transform: translateY(250px);
    filter: hue-rotate(0deg);
  }

  50% {
    transform: translateY(0);
  }

  100% {
    transform: translateY(250px);
    filter: hue-rotate(180deg);
  }
}


/* ===== Reset & Base ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg, #0f172a, #1e293b, #0f172a);
  color: #f8fafc;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  animation: fadeBg 10s infinite alternate ease-in-out;
}

@keyframes fadeBg {
  0% { background: linear-gradient(120deg, #0f172a, #1e293b); }
  100% { background: linear-gradient(120deg, #1e293b, #111827); }
}

a { text-decoration: none; transition: all 0.3s ease; }
h2,h3 { margin: 0; font-weight: 600; }

/* ===== Container ===== */
.movie-detail {
  display: flex;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 15px auto;
  gap: 40px;
  padding: 0 20px;
  animation: slideFade 1.2s ease forwards;
}

@keyframes slideFade {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* ===== Poster ===== */
.poster {
  flex-shrink: 0;
  max-width: 400px;
  overflow: hidden;
  border-radius: 18px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
  animation: softGlow 3s infinite alternate ease-in-out;
}

@keyframes softGlow {
  0% { box-shadow: 0 10px 25px rgba(255,255,255,0.15); }
  100% { box-shadow: 0 20px 50px rgba(255,255,255,0.25); }
}

.poster img {
  width: 100%;
  height: 500px;
  border-radius: 18px;
  transition: transform 0.6s ease, filter 0.4s ease;
}

.poster img:hover {
  transform: scale(1.07) rotate(1deg);
  filter: brightness(1.12);
}

/* ===== Info Section ===== */
.info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 18px;
  animation: slideLeft 1.3s ease forwards;
}

@keyframes slideLeft {
  0% { opacity: 0; transform: translateX(50px); }
  100% { opacity: 1; transform: translateX(0); }
}

.info h2 {
  font-size: 2.5rem;
  background: linear-gradient(90deg, #38bdf8, #60a5fa, #818cf8);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shineText 3s infinite ease-in-out;
}

@keyframes shineText {
  0% { filter: brightness(1); }
  50% { filter: brightness(1.5); }
  100% { filter: brightness(1); }
}

.info p {
  font-size: 1.05rem;
  line-height: 1.7;
  color: #cbd5e1;
}

/* ===== Stats ===== */
.info .movie-stats {
  display: flex;
  justify-content: space-between;
  font-weight: 600;
  font-size: 1rem;
  color: #e2e8f0;
  padding: 10px 0;
  border-top: 1px solid #475569;
  border-bottom: 1px solid #475569;
  animation: fadeInUp 1.5s ease forwards;
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* ===== Upcoming Shows ===== */
h3 {
  font-size: 1.6rem;
  color: #38bdf8;
  margin-bottom: 10px;
  text-shadow: 0 0 10px rgba(56,189,248,0.8);
}

.show-list {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
  gap: 20px;
}

.show-list li {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(6px);
  padding: 15px 20px;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.25);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
  animation: cardPop 0.8s ease forwards;
}

@keyframes cardPop {
  0% { opacity: 0; transform: translateY(20px) scale(0.95); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

.show-list li:hover {
  transform: translateY(-6px) scale(1.03);
  box-shadow: 0 15px 35px rgba(0,0,0,0.4);
  background: rgba(255,255,255,0.12);
}

.show-list li span {
  font-weight: 500;
  color: #e2e8f0;
}

/* ===== Buttons ===== */
.show-list li .btn {
  background: linear-gradient(90deg, #3b82f6, #2563eb);
  color: #fff;
  padding: 10px 18px;
  border-radius: 10px;
  font-weight: bold;
  width: 150px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  animation: pulseBtn 2.2s infinite ease;
}

@keyframes pulseBtn {
  0% { transform: scale(1); box-shadow: 0 0 10px rgba(59,130,246,0.4); }
  50% { transform: scale(1.07); box-shadow: 0 0 20px rgba(37,99,235,0.6); }
  100% { transform: scale(1); box-shadow: 0 0 10px rgba(59,130,246,0.4); }
}

.show-list li .btn:hover {
  transform: scale(1.1);
  background: linear-gradient(90deg, #60a5fa, #3b82f6);
}

/* ===== Responsive ===== */
@media(max-width:992px){
  .movie-detail { flex-direction: column; align-items:center; }
  .poster { max-width:100%; }
}

@media(max-width:500px){
  .show-list li { flex-direction: column; gap:10px; align-items:flex-start; }
  .show-list li .btn { width:100%; text-align:center; }
}

</style>
</head>

<body>

<!-- ================= LOADER ================= -->
<section class="loader" id="page-loader">
    <div class="slider" style="--i:0"></div>
    <div class="slider" style="--i:1"></div>
    <div class="slider" style="--i:2"></div>
    <div class="slider" style="--i:3"></div>
    <div class="slider" style="--i:4"></div>
</section>
<!-- =============== END LOADER =============== -->


<!-- ================= PAGE CONTENT ================= -->
<div id="page-content" style="display:none;">

<div class="movie-detail">

  <div class="poster">
    <?php if(!empty($movie['poster'])): ?>
      <img src="/Movie_Booking_Project_1/uploads/<?=htmlspecialchars($movie['poster'])?>">
    <?php else: ?>
      <img src="/Movie_Booking_Project_1/uploads/default.jpg">
    <?php endif; ?>
  </div>

  <div class="info">
    <h2><?=htmlspecialchars($movie['title'])?></h2>

    <p><?=nl2br(htmlspecialchars($movie['description'] ?? 'No description available.'))?></p>

    <div class="movie-stats">
      <div>⏱ <?=htmlspecialchars($movie['duration'] ?? 'N/A')?> min</div>
      <div>👁 <?=intval($movie['views'] ?? 0)?> Views</div>
    </div>

    <p><strong>Genre:</strong> <?=htmlspecialchars($movie['genre'] ?? 'N/A')?></p>

    <h3>Upcoming Shows</h3>

    <?php if($shows && $shows->num_rows > 0): ?>
    <ul class="show-list">

    <?php while($s = $shows->fetch_assoc()): ?>
      <li>
        <span>
          <?=date('M j, Y H:i', strtotime($s['show_time']))?>
          <?php if($screenExists): ?>
            — <?=htmlspecialchars($s['screen_name'])?>
          <?php endif; ?>
          — Rs <?=htmlspecialchars($priceExists ? ($s['price'] ?? 0) : 0)?>
        </span>

        <?php if(!isset($_SESSION['user_id'])): ?>
          <a class="btn" href="login.php?redirect=book.php?show_id=<?=$s['id']?>">Book Now</a>
        <?php else: ?>
          <a class="btn" href="book.php?show_id=<?=$s['id']?>">Book Now</a>
        <?php endif; ?>
      </li>
    <?php endwhile; ?>

    </ul>
    <?php else: ?>
      <p style="color:#6b7280;">No upcoming shows available.</p>
    <?php endif; ?>

  </div>
</div>

<?php require 'includes/footer.php'; ?>

</div>
<!-- =============== END PAGE CONTENT =============== -->


<!-- ================= LOADER SCRIPT ================= -->
<script>
window.addEventListener("load", () => {
    const loader = document.getElementById("page-loader");
    const content = document.getElementById("page-content");

    setTimeout(() => {
        loader.style.opacity = "0";
        setTimeout(() => {
            loader.style.display = "none";
            content.style.display = "block";
        }, 500);
    }, 800);
});
</script>

</body>
</html>
