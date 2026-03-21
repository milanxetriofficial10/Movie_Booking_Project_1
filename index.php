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

/* ================= SLIDER ================= */
$slides_res = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
$slides = [];
while($row = $slides_res->fetch_assoc()) {
    $slides[] = $row;
}

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
<style>

.🤚 {
  --skin-color: #E4C560;
  --tap-speed: 0.6s;
  --tap-stagger: 0.1s;
  position: relative;
  width: 80px;
  height: 60px;
  margin-left: 650px;
  top: 300px;
}

.🤚:before {
  content: '';
  display: block;
  width: 180%;
  height: 75%;
  position: absolute;
  top: 70%;
  right: 20%;
  background-color: black;
  border-radius: 40px 10px;
  filter: blur(10px);
  opacity: 0.3;
}

.🌴 {
  display: block;
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background-color: var(--skin-color);
  border-radius: 10px 40px;
}

.👍 {
  position: absolute;
  width: 120%;
  height: 38px;
  background-color: var(--skin-color);
  bottom: -18%;
  right: 1%;
  transform-origin: calc(100% - 20px) 20px;
  transform: rotate(-20deg);
  border-radius: 30px 20px 20px 10px;
  border-bottom: 2px solid rgba(0, 0, 0, 0.1);
  border-left: 2px solid rgba(0, 0, 0, 0.1);
}

.👍:after {
  width: 20%;
  height: 60%;
  content: '';
  background-color: rgba(255, 255, 255, 0.3);
  position: absolute;
  bottom: -8%;
  left: 5px;
  border-radius: 60% 10% 10% 30%;
  border-right: 2px solid rgba(0, 0, 0, 0.05);
}

.👉 {
  position: absolute;
  width: 80%;
  height: 35px;
  background-color: var(--skin-color);
  bottom: 32%;
  right: 64%;
  transform-origin: 100% 20px;
  animation-duration: calc(var(--tap-speed) * 2);
  animation-timing-function: ease-in-out;
  animation-iteration-count: infinite;
  transform: rotate(10deg);
}

.👉:before {
  content: '';
  position: absolute;
  width: 140%;
  height: 30px;
  background-color: var(--skin-color);
  bottom: 8%;
  right: 65%;
  transform-origin: calc(100% - 20px) 20px;
  transform: rotate(-60deg);
  border-radius: 20px;
}

.👉:nth-child(1) {
  animation-delay: 0;
  filter: brightness(70%);
  animation-name: tap-upper-1;
}

.👉:nth-child(2) {
  animation-delay: var(--tap-stagger);
  filter: brightness(80%);
  animation-name: tap-upper-2;
}

.👉:nth-child(3) {
  animation-delay: calc(var(--tap-stagger) * 2);
  filter: brightness(90%);
  animation-name: tap-upper-3;
}

.👉:nth-child(4) {
  animation-delay: calc(var(--tap-stagger) * 3);
  filter: brightness(100%);
  animation-name: tap-upper-4;
}

@keyframes tap-upper-1 {
  0%, 50%, 100% {
    transform: rotate(10deg) scale(0.4);
  }

  40% {
    transform: rotate(50deg) scale(0.4);
  }
}

@keyframes tap-upper-2 {
  0%, 50%, 100% {
    transform: rotate(10deg) scale(0.6);
  }

  40% {
    transform: rotate(50deg) scale(0.6);
  }
}

@keyframes tap-upper-3 {
  0%, 50%, 100% {
    transform: rotate(10deg) scale(0.8);
  }

  40% {
    transform: rotate(50deg) scale(0.8);
  }
}

@keyframes tap-upper-4 {
  0%, 50%, 100% {
    transform: rotate(10deg) scale(1);
  }

  40% {
    transform: rotate(50deg) scale(1);
  }
}

* { box-sizing:border-box; margin:0; padding:0; }

/* News Ticker */
.ticker-container { background:#ffe8; overflow:hidden; white-space:nowrap; padding:10px 0; font-weight:bold; }
.ticker-text { display:inline-block; padding-left:100%; animation:ticker 20s linear infinite; color:#fbf9f9; font-family: 'Merriweather', Georgia, 'Times New Roman', serif; }
@keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-100%); } }

/* Slider */
.slider-container { position:relative; width:100%; max-width:1500px; overflow:hidden; border-radius:12px; box-shadow:0 8px 25px rgba(248, 22, 22, 0.88); }
.slider-slide { position:relative; width:100%; height:500px; display:none; background-size:cover; background-position:center; }
.slider-slide.active { display:block; }
.slider-overlay { position:absolute; top:0; left:0; width:100%; height:100%; background: rgba(24, 17, 17, 0.59); display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding:0 20px; }
.slider-overlay h2 { font-weight:700; color:#74f708ff; font-size:49px; margin-bottom:15px; text-shadow:2px 12px 15px #e2e9e2fc; font-family:'Merriweather', Georgia, 'Times New Roman', serif; }
.slider-overlay p { font-weight:600; color:hsla(59, 98%, 51%, 0.99); font-size:25px; margin-bottom:20px; max-width:700px; text-shadow:1px 10px 4px hsla(0, 73%, 57%, 0.97); font-family:'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif; }
.slider-overlay a { display:inline-block; padding:12px 30px; background:#ff4c60; color:#fff; font-weight:bold; border-radius:6px; text-decoration:none; transition:0.3s; font-family:'Times New Roman'; }
.slider-overlay a:hover { background:#ff1a3c; }
.slider-dots { position:absolute; bottom:20px; width:100%; text-align:center; }
.slider-dots span { display:inline-block; width:20px; height:4px; margin:0 6px; background: rgba(255,255,255,0.5); border-radius:20%; cursor:pointer; transition:0.3s; }
.slider-dots span.active { background:#ff4c60; }


/* Title Line */
.line-container { position: relative; width: 100%; text-align: center; margin:0px; }
.line-container h2 { display:inline-block; font-size:25px; color:hsla(91, 98%, 52%, 1); font-weight:bold; font-family:'Merriweather', Georgia, 'Times New Roman', serif; position:relative; padding-bottom:0px; text-shadow:1px 10px 4px hsla(101, 87%, 49%, 0.97); }
.line-container h2 span { font-family:'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif; color:rgba(247, 211, 9, 1); font-weight:bold; text-shadow:1px 8px 4px hsla(59, 93%, 40%, 0.97); }
.line-container h2::after { content:""; position:absolute; left:50%; bottom:0; transform:translateX(-50%); width:100%; height:2px; background:linear-gradient(90deg, #ff4d4d, #ff9933); border-radius:2px; }

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

<div id="page-loader">
    <div class="🤚">
        <div class="👉"></div><div class="👉"></div><div class="👉"></div>
        <div class="👉"></div><div class="🌴"></div><div class="👍"></div>
    </div>
</div>

<div id="page-content" style="display:none;">
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

<script>
// Slider
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

// Page loader
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