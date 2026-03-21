<?php
require 'includes/db.php';
require 'includes/header.php';

$conn = db_connect();

/* ================= NOW SHOWING ================= */
$now_showing = $conn->query("
    SELECT m.*, MIN(s.show_time) AS first_show
    FROM movies m
    INNER JOIN shows s ON m.id = s.movie_id
    WHERE s.show_time >= NOW()
    GROUP BY m.id
    ORDER BY first_show ASC
");

$now_showing_ids = [];
while($row = $now_showing->fetch_assoc()){
    $now_showing_ids[] = $row['id'];
}
$now_showing->data_seek(0);

/* ================= UPCOMING ================= */
$exclude_ids = !empty($now_showing_ids) ? implode(',', $now_showing_ids) : '0';
$upcoming = $conn->query("
    SELECT m.*
    FROM movies m
    LEFT JOIN shows s ON m.id = s.movie_id
    WHERE s.id IS NULL
    GROUP BY m.id
    ORDER BY m.id DESC
");

/* ================= NEWS ================= */
$news_res = $conn->query("SELECT news_text FROM movie_news ORDER BY created_at DESC");
$news_arr = [];
while($row = $news_res->fetch_assoc()){
    $news_arr[] = $row['news_text'];
}
$news_text = !empty($news_arr) ? implode(" ⚡ ", $news_arr) : "No news available";


/* ================= DURATION ================= */
function formatDuration($minutes) {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf("%dh %02dm", $h, $m);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css_part/index.css">
<title>Movies - CineMa Ghar</title>
<style>
/* ===== GLOBAL STYLES ===== */
body { 
    font-family: 'Poppins', Arial, sans-serif; 
    background:
        linear-gradient(rgba(26, 8, 8, 0.90), rgba(0, 0, 0, 0.95)),
        url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    color: #fff;
    margin: 0;
    padding: 0;
}

h2.section-title {
    margin: 40px 0 20px;
    color: #ffeb3b;
    font-size: 28px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    border-left: 5px solid #ff5722;
    padding-left: 15px;
}

/* ===== MOVIE CARD – TRAILER OVERLAY CENTERED ===== */
.movie-card {
position: relative;
width: 290px;
height: 460px;
background: rgba(255, 255, 255, 0.1);
backdrop-filter: blur(10px);
-webkit-backdrop-filter: blur(10px);
border-radius: 20px;
overflow: hidden;
box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
transition: transform 0.3s ease, box-shadow 0.3s ease;
cursor: pointer;
border: 1px solid rgba(255, 255, 255, 0.2);
}

.movie-card:hover {
transform: translateY(-8px) scale(1.02);
box-shadow: 0 25px 45px rgba(0, 0, 0, 0.8);
border-color: #fe4006;
}

/* Poster container */
.movie-card .poster-container {
position: relative; /* important for center positioning */
width: 100%;
height: 300px;
overflow: hidden;
}

.movie-card img {
width: 100%;
height: 100%;
object-fit: cover;
transition: transform 0.5s ease;
}

.movie-card:hover img {
transform: scale(1.1);
}

/* Gradient overlay */
.movie-card .overlay {
position: absolute;
top: 0;
left: 0;
width: 100%;
height: 100%;
background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 50%);
opacity: 0;
transition: opacity 0.3s ease;
pointer-events: none;
}

.movie-card:hover .overlay {
opacity: 1;
}

/* ===== TRAILER BUTTON – SLIDE UP FROM BOTTOM TO TOP ===== */
.movie-card {
  position: relative;
  overflow: hidden;
}

.movie-card .trailer-btn {
  position: absolute;
  bottom: -40px; /* start hidden below card */
  left: 50%;
  transform: translateX(-50%) scale(0.8); /* center horizontally, small initially */

  background: rgba(255, 255, 255, 0.3); /* transparent like card */
  border: 2px solid rgba(255, 255, 255, 0.5);
  color: #fff;
  padding: 10px 20px;
  border-radius: 40px;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
  backdrop-filter: blur(5px);

  opacity: 0;
  transition: all 0.5s cubic-bezier(0.25, 1, 0.5, 1); /* smooth rising animation */
  z-index: 10;
  white-space: nowrap;
  letter-spacing: 1px;
  cursor: pointer;
}

/* On card hover – slide button toward the top */
.movie-card:hover .trailer-btn {
  bottom: 120%; /* final position near the top */
  opacity: 1;
  transform: translateX(-50%) scale(1); /* grow slightly */
}

/* Slight hover effect for the button itself */
.movie-card .trailer-btn:hover {
  transform: translateX(-50%) scale(1.05);
}

/* Card content */
.movie-card .card-content {
padding: 12px 12px 16px;
background: rgba(33, 33, 33, 0.7);
backdrop-filter: blur(5px);
border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.movie-card h3 {
margin: 0 0 6px;
font-size: 18px;
font-weight: 600;
color: #fff;
text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
}

.movie-card .language {
display: inline-block;
background: #ff5722;
color: #fff;
font-size: 11px;
font-weight: 500;
padding: 3px 8px;
border-radius: 30px;
margin-bottom: 8px;
letter-spacing: 0.5px;
box-shadow: 0 2px 5px rgba(255,87,34,0.3);
}

.movie-card .meta-info {
display: flex;
justify-content: space-between;
align-items: center;
font-size: 12px;
color: #ddd;
margin: 8px 0;
background: rgba(0,0,0,0.4);
padding: 4px 8px;
border-radius: 30px;
backdrop-filter: blur(5px);
}

.movie-card .duration {
display: flex;
align-items: center;
gap: 3px;
}

.movie-card .duration::before {
content: "⏱️";
font-size: 11px;
}

.movie-card .views {
display: flex;
align-items: center;
gap: 3px;
}

.movie-card .views::before {
content: "👁️";
font-size: 11px;
}

.movie-card .next-show {
font-size: 11px;
color: #ffeb3b;
background: rgba(0,0,0,0.5);
padding: 4px 8px;
border-radius: 30px;
text-align: center;
margin: 8px 0;
border: 1px dashed #ffeb3b;
}

/* Buttons container */
.movie-card .buttons {
display: flex;
gap: 8px;
margin-top: 10px;
}

/* View Shows button */
.movie-card .btn {
flex: 1;
background: linear-gradient(45deg, #ff5722, #923100);
border: none;
color: #fff;

padding: 5px 10;
border-radius: 30px;
font-size: 12px;
font-weight: 600;
text-decoration: none;
text-align: center;
box-shadow: 0 4px 15px rgba(255,87,34,0.4);
transition: all 0.3s ease;
}

.movie-card .btn:hover {
background: linear-gradient(45deg, #ff8a50, #ff5722);
box-shadow: 0 6px 20px rgba(255,87,34,0.6);
transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
.movie-card {
width: 200px;
}
.movie-card .poster-container {
height: 240px;
}
.movie-card h3 {
font-size: 16px;
}
.movie-card .trailer-btn {
padding: 8px 16px;
font-size: 12px;
}
}

@media (max-width: 480px) {
.movie-card {
width: 160px;
}
.movie-card .poster-container {
height: 200px;
}
.movie-card h3 {
font-size: 14px;
}
.movie-card .btn {
font-size: 11px;
padding: 4px 0;
}
.movie-card .trailer-btn {
padding: 6px 12px;
font-size: 10px;
}
}

</style>
</head>
<body>

<!-- News Ticker -->
<div class="ticker-container">
    <div class="ticker-text"><?=htmlspecialchars($news_text)?></div>
</div>

<!-- Now Showing Section -->
<h2 class="section-title">🎬 Now Showing</h2>
<section class="movie-grid">
<?php if($now_showing->num_rows > 0): ?>
    <?php while($m = $now_showing->fetch_assoc()): ?>
    <article class="movie-card">
        <div class="poster-container">
            <img src="uploads/<?=htmlspecialchars($m['poster'] ?: 'default.jpg')?>" alt="<?=htmlspecialchars($m['title'])?>">
            <div class="overlay"></div>
        </div>
        <div class="card-content">
            <h3><?=htmlspecialchars($m['title'])?></h3>
            <span class="language"><?=htmlspecialchars($m['language'] ?? 'N/A')?></span>
            <div class="meta-info">
                <span class="duration"><?=formatDuration($m['duration'])?></span>
                <span class="views"><?=intval($m['views'])?></span>
            </div>
            <div class="buttons">
                <?php if(!empty($m['trailer'])): ?>
                    <a class="trailer-btn" href="<?=htmlspecialchars($m['trailer'])?>" target="_blank">Trailer</a>
                <?php endif; ?>
                <a class="btn" href="movie_view.php?id=<?=$m['id']?>">View Show & Book</a>
            </div>
        </div>
    </article>
    <?php endwhile; ?>
<?php else: ?>
    <p>🍿 No movies showing right now...</p>
<?php endif; ?>
</section>

<!-- Upcoming Movies Section -->
<h2 class="section-title">⏳ Upcoming Movies</h2>
<section class="movie-grid">
<?php if($upcoming->num_rows > 0): ?>
    <?php while($m = $upcoming->fetch_assoc()): ?>
    <article class="movie-card">
        <div class="poster-container">
            <img src="uploads/<?=htmlspecialchars($m['poster'] ?: 'default.jpg')?>" alt="<?=htmlspecialchars($m['title'])?>">
            <div class="overlay"></div>
        </div>
        <div class="card-content">
            <h3><?=htmlspecialchars($m['title'])?></h3>
            <span class="language"><?=htmlspecialchars($m['language'] ?? 'N/A')?></span>
            <div class="next-show">No shows scheduled yet</div>
            <div class="buttons">
                <?php if(!empty($m['trailer'])): ?>
                    <a class="trailer-btn" href="<?=htmlspecialchars($m['trailer'])?>" target="_blank">Trailer</a>
                <?php endif; ?>
                <a class="btn" href="movie_view.php?id=<?=$m['id']?>">View Shows</a>
            </div>
        </div>
    </article>
    <?php endwhile; ?>
<?php else: ?>
    <p>🎥 No upcoming movies scheduled...</p>
<?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
</div>

</body>
</html>