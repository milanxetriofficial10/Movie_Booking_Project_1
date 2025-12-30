
<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();

/* ================= FETCH DATA ================= */

// Fetch movies
$movies = $conn->query("SELECT * FROM movies WHERE status='active' ORDER BY created_at DESC");

// Fetch live news
$news_res = $conn->query("SELECT news_text FROM movie_news ORDER BY created_at DESC");
$news_arr = [];
while($row = $news_res->fetch_assoc()){
    $news_arr[] = $row['news_text'];
}
$news_text = !empty($news_arr) ? implode(" ⚡ ", $news_arr) : "No news available";

// Fetch slides
$slides_res = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
$slides = [];
while($row = $slides_res->fetch_assoc()) {
    $slides[] = $row;
}

// Duration helper
function formatDuration($minutes) {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf("%dh %02dm", $h, $m);
}

// Increment views
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id > 0){
    $conn->query("UPDATE movies SET views = views + 1 WHERE id = $id");
}

// Upcoming shows
$shows = $conn->query("
    SELECT s.id, m.title, sc.screen_name, s.show_time, s.price
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    WHERE s.show_time >= NOW()
    ORDER BY s.show_time ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css_part/index.css">
<style>
  body { 
  font-family:Arial,sans-serif; 
 background:
    linear-gradient(
      rgba(26, 8, 8, 0.75),
      rgba(0, 0, 0, 0.95)
    ),
    url("./imgs/7f2f2b740a523a0240c17fab4f7f9733.jpg");
     background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
min-height: 100vh;

  color:#fff;
 }

</style>
</head>
<body>

<div id="page-loader">
	<div class="🤚">
		<div class="👉"></div>
		<div class="👉"></div>
		<div class="👉"></div>
		<div class="👉"></div>
		<div class="🌴"></div>		
		<div class="👍"></div>
	</div>
</div>
<!-- ================= END LOADER ================= -->


<!-- ================= PAGE CONTENT ================= -->
<div id="page-content" style="display:none;">

<!-- News Ticker -->
<div class="ticker-container">
    <div class="ticker-text"><?=htmlspecialchars($news_text)?></div>
</div>

<!-- Slider -->
<div class="slider-container">
<?php foreach($slides as $s): ?>
    <div class="slider-slide" style="background-image:url('uploads/<?=htmlspecialchars($s['image_name'] ?? '')?>');">
        <div class="slider-overlay">
            <h2><?=htmlspecialchars($s['slide_text'] ?? '')?></h2>
            <?php if(!empty($s['slide_paragraph'])): ?>
                <p><?=htmlspecialchars($s['slide_paragraph'])?></p>
            <?php endif; ?>
            <?php if(!empty($s['button_text']) && !empty($s['button_link'])): ?>
                <a href="<?=htmlspecialchars($s['button_link'])?>" target="_blank">
                    <?=htmlspecialchars($s['button_text'])?>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
    <div class="slider-dots"></div>
</div>

<!-- Movie Time -->
<h3 style="margin:20px 0;color:#ffeb3b;font-size:22px;font-weight:bold;">
🎬 Movie Time
</h3>

<!-- Show Times -->
<div class="show-row">
<?php if ($shows && $shows->num_rows > 0): ?>
    <?php while ($row = $shows->fetch_assoc()): ?>
        <div class="show-item">
            <?= htmlspecialchars($row['title']) ?> —
            <?= date('h:i A', strtotime($row['show_time'])) ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="show-item">No shows available.</div>
<?php endif; ?>
</div>

<div class="line-container">
    <h2><span>Now Showing | Nepali | Hindi | English | Korean</span></h2>
</div>

<!-- Movie Grid -->
<section class="movie-grid">
<?php while($m = $movies->fetch_assoc()): ?>
<article class="movie-card">

    <img src="/Movie_Booking_Project_1/uploads/<?=htmlspecialchars($m['poster'] ?: 'default.jpg')?>">

    <h3><?=htmlspecialchars($m['title'])?></h3>

    <div class="language"><?=htmlspecialchars($m['language'] ?? 'N/A')?></div>

    <div style="display:flex;justify-content:space-between;padding:0 15px;font-size:0.9rem;">
        <div>Time: <?=formatDuration($m['duration'])?></div>
        <div>👁️ <?=intval($m['views'])?></div>
    </div>

    <?php if(!empty($m['trailer'])): ?>
        <a class="trailer-btn" href="<?=htmlspecialchars($m['trailer'])?>" target="_blank">
            Watch Trailer
        </a>
    <?php endif; ?>

    <a class="btn" href="movie_view.php?id=<?=$m['id']?>" onclick="incrementView(<?=$m['id']?>)">
        View Show times
    </a>

</article>
<?php endwhile; ?>
</section>

<?php require 'includes/footer.php'; ?>

</div>

<script>
/* Slider */
let slides = document.querySelectorAll('.slider-slide');
let dotsContainer = document.querySelector('.slider-dots');
let current = 0;

slides.forEach((s,i)=>{
    let dot = document.createElement('span');
    if(i===0) dot.classList.add('active');
    dot.onclick = ()=>goToSlide(i);
    dotsContainer.appendChild(dot);
});

function showSlide(i){
    slides.forEach(s=>s.classList.remove('active'));
    slides[i].classList.add('active');
    document.querySelectorAll('.slider-dots span')
        .forEach(d=>d.classList.remove('active'));
    dotsContainer.children[i].classList.add('active');
}
function goToSlide(i){ current=i; showSlide(current); }
setInterval(()=>{ current=(current+1)%slides.length; showSlide(current); },5000);
showSlide(0);

/* Increment view milan */
function incrementView(id){
    fetch('increment_view.php?id='+id);
}

/* PAGE LOADER milan */
window.addEventListener("load",()=>{
    const loader=document.getElementById("page-loader");
    const content=document.getElementById("page-content");

    setTimeout(()=>{
        loader.style.opacity="0";
        setTimeout(()=>{
            loader.style.display="none";
            content.style.display="block";
        },500);
    },800);
});
</script>

</body>
</html>

