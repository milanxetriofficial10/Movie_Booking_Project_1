<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();

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

// Function to convert minutes to H:M
function formatDuration($minutes) {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf("%dh %02dm", $h, $m);
}
?>

<style>
/* ===== General Reset ===== */
* { box-sizing:border-box; margin:0; padding:0; }
body { font-family:Arial,sans-serif; background:#11141b; color:#fff; }

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
    font-family: 'Merriweather', Georgia, 'Times New Roman', serif;
}
@keyframes ticker {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* ===== Slider ===== */
.slider-container { position:relative; width:100%; max-width:1500px; overflow:hidden; border-radius:12px; box-shadow:0 8px 25px rgba(248, 22, 22, 0.88); }
.slider-slide { position:relative; width:100%; height:450px; display:none; background-size:cover; background-position:center; }
.slider-slide.active { display:block; }
.slider-overlay { position:absolute; top:0; left:0; width:100%; height:100%; background: rgba(24, 17, 17, 0.59); display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; padding:0 20px; }
.slider-overlay h2 { font-weight:700; color:#74f708ff; font-size:49px; margin-bottom:15px; text-shadow:2px 12px 15px #e2e9e2fc; font-family:'Merriweather', Georgia, 'Times New Roman', serif; }
.slider-overlay p { font-weight:600; color:hsla(59, 98%, 51%, 0.99); font-size:25px; margin-bottom:20px; max-width:700px; text-shadow:1px 10px 4px hsla(0, 73%, 57%, 0.97); font-family:'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif; }
.slider-overlay a { display:inline-block; padding:12px 30px; background:#ff4c60; color:#fff; font-weight:bold; border-radius:6px; text-decoration:none; transition:0.3s; font-family:'Times New Roman'; }
.slider-overlay a:hover { background:#ff1a3c; }

/* Slider dots */
.slider-dots { position:absolute; bottom:20px; width:100%; text-align:center; }
.slider-dots span { display:inline-block; width:20px; height:4px; margin:0 6px; background: rgba(255,255,255,0.5); border-radius:20%; cursor:pointer; transition:0.3s; }
.slider-dots span.active { background:#ff4c60; }

/* ===== Movie Cards ===== */
.movie-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:20px; max-width:1200px; margin:50px auto; padding:0 15px; }
.movie-card { background:linear-gradient(145deg,#85f369,#160c14,#f1620e); border-radius:15px; overflow:hidden; box-shadow:0px 10px 5px rgba(235, 24, 24, 0.97); transition:transform 0.3s, box-shadow 0.3s; position:relative; display:flex; flex-direction:column; }
.movie-card:hover { transform:translateY(-8px); box-shadow:0 25px 45px rgba(0,0,0,0.2); }
.movie-card img { background: rgba(24, 17, 17, 0.78); width:100%; height:220px; object-fit:cover; transition:transform 0.5s; }
.movie-card:hover img { transform:scale(1.05); background: rgba(24, 17, 17, 0.59); }
.movie-card h3 { font-size:1.2rem; color:#fff; margin:15px; font-weight:700; text-align:center; }
.movie-card p { font-size:0.95rem; color:#eee; margin:0 15px 15px;  -webkit-line-clamp: 2; line-height:1.5; text-align:justify; flex-grow:1; }

/* ===== Trailer Button Animation ===== */
.trailer-btn { position:absolute; bottom:-60px; left:50%; transform:translateX(-50%); background: rgba(252, 202, 41, 1); color: rgba(3, 3, 3, 1); width: 143px; font-weight:500; padding:12px 20px; border-radius:30px; text-decoration:none; opacity:0; transition:all 0.5s ease; display:flex; align-items:center; gap:8px; }
.movie-card:hover .trailer-btn { bottom:50%; opacity:1; }
.trailer-btn:hover { background:#ff1a3c; box-shadow:0 0 15px rgba(108, 250, 26, 0.94); }

/* ===== Book Now Button ===== */
.movie-card .btn { display:block; margin:0 15px 15px 15px; padding:12px; background:#2563eb; color:#fff; text-align:center; font-weight:600; border-radius:8px; text-decoration:none; transition:0.3s; position:relative; }
.movie-card .btn:hover { background:#1e40af; transform:translateY(-2px); box-shadow:0 8px 15px rgba(37,99,235,0.3); }

/* ===== Responsive ===== */
@media(max-width:768px){ .slider-slide { height:350px; } .slider-overlay h2 { font-size:32px; } .slider-overlay p { font-size:16px; } .movie-card img { height:180px; } }

/* ===== Title Line ===== */
.line-container { position: relative; width: 100%; text-align: center; margin:0px; }
.line-container h2 { display:inline-block; font-size:25px; color:hsla(91, 98%, 52%, 1); font-weight:bold; font-family:'Merriweather', Georgia, 'Times New Roman', serif; position:relative; padding-bottom:0px; text-shadow:1px 10px 4px hsla(101, 87%, 49%, 0.97); }
.line-container h2 span { font-family:'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif; color:rgba(247, 211, 9, 1); font-weight:bold; text-shadow:1px 8px 4px hsla(59, 93%, 40%, 0.97); }
.line-container h2::after { content:""; position:absolute; left:50%; bottom:0; transform:translateX(-50%); width:100%; height:2px; background:linear-gradient(90deg, #ff4d4d, #ff9933); border-radius:2px; }
</style>

<!-- ===== News Ticker ===== -->
<div class="ticker-container">
    <div class="ticker-text"><?=htmlspecialchars($news_text)?></div>
</div>

<!-- ===== Slider ===== -->
<div class="slider-container">
<?php foreach($slides as $s): ?>
    <div class="slider-slide" style="background-image:url('uploads/<?=htmlspecialchars($s['image_name'] ?? '')?>');">
        <div class="slider-overlay">
            <h2><?=htmlspecialchars($s['slide_text'] ?? '')?></h2>
            <?php if(!empty($s['slide_paragraph'] ?? '')): ?>
                <p><?=htmlspecialchars($s['slide_paragraph'])?></p>
            <?php endif; ?>
            <?php if(!empty($s['button_text'] ?? '') && !empty($s['button_link'] ?? '')): ?>
                <a href="<?=htmlspecialchars($s['button_link'])?>" target="_blank"><?=htmlspecialchars($s['button_text'])?></a>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
    <div class="slider-dots"></div>
</div>

<script>
let slides = document.querySelectorAll('.slider-slide');
let dotsContainer = document.querySelector('.slider-dots');
let current = 0;
slides.forEach((s,i)=>{
    let dot = document.createElement('span');
    if(i===0) dot.classList.add('active');
    dot.addEventListener('click',()=>{ goToSlide(i); });
    dotsContainer.appendChild(dot);
});
function showSlide(index){
    slides.forEach(s=>s.classList.remove('active'));
    slides[index].classList.add('active');
    let dots = document.querySelectorAll('.slider-dots span');
    dots.forEach(d=>d.classList.remove('active'));
    dots[index].classList.add('active');
}
function goToSlide(index){ current=index; showSlide(current); }
function nextSlide(){ current=(current+1)%slides.length; showSlide(current); }
setInterval(nextSlide,5000);
showSlide(current);
</script>

<br>
<div class="line-container">
  <h2> <span>Now Showing -</span> Movies Book Now Please <span>| Nepali | Hindi | English | Korean </span></h2>
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
