<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();
$movies = $conn->query("SELECT * FROM movies WHERE status='active' ORDER BY created_at DESC");
// Fetch live news
$news_res = $conn->query("SELECT news_text FROM movie_news ORDER BY created_at DESC");
$news_arr = [];
while($row = $news_res->fetch_assoc()){
    $news_arr[] = $row['news_text'];
}
$news_text = !empty($news_arr) ? implode(" ⚡ ", $news_arr) : "No news available";

// Function to convert minutes to H:M
function formatDuration($minutes) {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf("%dh %02dm", $h, $m);
}
?>
<style>

  /* ===== Trailer Button Animation ===== */
.trailer-btn { position:absolute; bottom:-60px; left:50%; transform:translateX(-50%); background: rgba(252, 202, 41, 1); color: rgba(3, 3, 3, 1); width: 143px; font-weight:500; padding:12px 20px; border-radius:30px; text-decoration:none; opacity:0; transition:all 0.5s ease; display:flex; align-items:center; gap:8px; }
.movie-card:hover .trailer-btn { bottom:50%; opacity:1; }
.trailer-btn:hover { background:#ff1a3c; box-shadow:0 0 15px rgba(108, 250, 26, 0.94); }



/* ===== Title Line ===== */
.line-container { position: relative; width: 100%; text-align: center; margin:0px; }
.line-container h2 { display:inline-block; font-size:25px; color:hsla(91, 98%, 52%, 1); font-weight:bold; font-family:'Merriweather', Georgia, 'Times New Roman', serif; position:relative; padding-bottom:0px; text-shadow:1px 10px 4px hsla(101, 87%, 49%, 0.97); }
.line-container h2 span { font-family:'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif; color:rgba(247, 211, 9, 1); font-weight:bold; text-shadow:1px 8px 4px hsla(59, 93%, 40%, 0.97); }
.line-container h2::after { content:""; position:absolute; left:50%; bottom:0; transform:translateX(-50%); width:100%; height:2px; background:linear-gradient(90deg, #ff4d4d, #ff9933); border-radius:2px; }

  /* ===== News Ticker ===== */
.ticker-container {
    background:#ffe816;
    overflow:hidden;
    white-space:nowrap;
    padding:10px 0;
    font-weight:bold;
}
.ticker-text {
    display:inline-block;
    padding-left:100%;
    animation:ticker 20s linear infinite;
    color:#111;
}
@keyframes ticker {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

 /* ===== Movie Grid ===== */
.movie-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 30px;
  padding: 40px;
  max-width: 1500px;
  margin: 0 auto;
}

/* ===== Movie Card ===== */
.movie-card {
  background: linear-gradient(145deg, #85f369ff, #160c14ff, #f1620eff);
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
  position: relative;
}

.movie-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 25px 45px rgba(0,0,0,0.2);
}

/* Poster Image */
.movie-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.movie-card:hover img {
  transform: scale(1.05);
}

/* Movie Title */
.movie-card h3 {
  font-size: 1.2rem;
  color: #f5f2f2ff;
  margin: 15px;
  font-weight: 700;
  text-align: center;
}

/* Movie Description */
.movie-card p {
  font-size: 0.95rem;
  color: #eef0f3ff;
  margin: 0 15px 15px 15px;
  flex-grow: 1;
  line-height: 1.5;
  text-align: justify;
}

/* View Showtimes Button */
.movie-card .btn {
  display: block;
  margin: 0 15px 15px 15px;
  padding: 12px;
  background: #2563eb;
  color: #fff;
  text-align: center;
  font-weight: 600;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.3s, transform 0.2s;
}

.movie-card .btn:hover {
  background: #1e40af;
  transform: translateY(-2px);
  box-shadow: 0 8px 15px rgba(37,99,235,0.3);
}

/* Responsive */
@media (max-width: 1024px) {
  .movie-card img { height: 300px; }
}

@media (max-width: 768px) {
  .movie-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
  .movie-card img { height: 280px; }
}

@media (max-width: 480px) {
  .movie-card img { height: 220px; }
  .movie-card h3 { font-size: 1.2rem; }
  .movie-card p { font-size: 0.85rem; }
  .movie-card .btn { padding: 10px; font-size: 0.9rem; }
}
</style>
<!-- ===== News Ticker ===== -->
<div class="ticker-container">
    <div class="ticker-text"><?=htmlspecialchars($news_text)?></div>
</div>
<br>
<br>
<br>
<div class="line-container">
  <h2>Movies Book Now Please <span>| Nepali | Hindi | English | Korian </span></h2>
</div>


<!-- ===== Movie Section ===== -->
<section class="movie-grid">
<?php while($m = $movies->fetch_assoc()): ?>
  <article class="movie-card">
    <?php if($m['poster']): ?>
      <img src="/Movie_Booking_Project_1/uploads/<?=htmlspecialchars($m['poster'])?>" alt="<?=htmlspecialchars($m['title'])?>">
    <?php else: ?>
      <img src="/Movie_Booking_Project_1/uploads/default.jpg" alt="No Poster">
    <?php endif; ?>
    <h3><?=htmlspecialchars($m['title'])?></h3>
    <p><?=nl2br(htmlspecialchars(substr($m['description'],0,69)))?></p>

    <!-- Show duration -->
    <p style="text-align:center; font-weight:bold; color:#fff; margin-bottom:8px;">
      Duration: <?=formatDuration($m['duration'])?>
    </p>

    <!-- Trailer Button with Animation -->
    <?php if(!empty($m['trailer'])): ?>
      <a class="trailer-btn" href="<?=htmlspecialchars($m['trailer'])?>" target="_blank">Watch Trailer</a>
    <?php endif; ?>

    <a class="btn" href="movie_view.php?id=<?=htmlspecialchars($m['id'])?>">View Show times</a>
  </article>
<?php endwhile; ?>
</section>


<?php require 'includes/footer.php'; ?>
