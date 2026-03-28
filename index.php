<?php
require 'includes/db.php';
require 'includes/header.php';

$conn = db_connect();

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

$exclude_ids = !empty($now_showing_ids) ? implode(',', $now_showing_ids) : '0';
$upcoming = $conn->query("
    SELECT m.*
    FROM movies m
    LEFT JOIN shows s ON m.id = s.movie_id
    WHERE s.id IS NULL
    GROUP BY m.id
    ORDER BY m.id DESC
");

$news_res = $conn->query("SELECT news_text FROM movie_news ORDER BY created_at DESC");
$news_arr = [];
while($row = $news_res->fetch_assoc()){
    $news_arr[] = $row['news_text'];
}
$news_text = !empty($news_arr) ? implode(" ⚡ ", $news_arr) : "No news available";

$slides_res = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
$slides = [];
while($row = $slides_res->fetch_assoc()) {
    $slides[] = $row;
}

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
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Poppins:wght@300;400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Poppins', Arial, sans-serif;
    background:
        linear-gradient(rgba(26,8,8,0.90), rgba(0,0,0,0.95)),
        url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    color: #fff;
}

#page-loader {
    position: fixed; top:0; left:0;
    width:100%; height:100%;
    background:#000;
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    z-index:9999;
    transition:opacity 0.8s ease;
    overflow:hidden;
}
.loader-vignette {
    position:absolute; inset:0;
    background:radial-gradient(ellipse at center, transparent 30%, rgba(0,0,0,0.92) 100%);
    z-index:2; pointer-events:none;
}
.loader-grain {
    position:absolute; inset:0; opacity:.22; z-index:3; pointer-events:none;
    background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.08'/%3E%3C/svg%3E");
    animation:grn .25s steps(1) infinite;
}
@keyframes grn {
    0%{transform:translate(0,0)} 25%{transform:translate(-3%,-2%)}
    50%{transform:translate(2%,3%)} 75%{transform:translate(-1%,1%)} 100%{transform:translate(0,0)}
}
.loader-stars { position:absolute; inset:0; z-index:1; }
.loader-star {
    position:absolute; background:#fff; border-radius:50%;
    animation:twinkle var(--d,2s) ease-in-out infinite var(--delay,0s);
}
@keyframes twinkle { 0%,100%{opacity:.1} 50%{opacity:.7} }
.loader-projector {
    position:absolute; top:calc(50% - 110px); left:50%; transform:translateX(-50%);
    width:200px; height:5px;
    background:linear-gradient(90deg,transparent,rgba(255,230,100,.1),rgba(255,230,100,.3),rgba(255,230,100,.1),transparent);
    z-index:6; border-radius:50px;
    animation:beam 2s ease-in-out infinite alternate;
}
@keyframes beam { 0%{opacity:.3;width:160px} 100%{opacity:.7;width:240px} }
.loader-scanline {
    position:absolute; left:0; width:100%; height:2px;
    background:linear-gradient(90deg,transparent,#ff4c60 40%,#ffb347 60%,transparent);
    z-index:4; opacity:.7; animation:scan 3s linear infinite;
}
@keyframes scan { 0%{top:-2px;opacity:0} 5%{opacity:.7} 95%{opacity:.7} 100%{top:100%;opacity:0} }
.loader-filmstrip {
    position:absolute; left:0; right:0; height:52px;
    display:flex; overflow:hidden; z-index:5;
}
.loader-filmstrip.top    { top:0;    border-bottom:2px solid #1a1a1a; }
.loader-filmstrip.bottom { bottom:0; border-top:2px solid #1a1a1a; }
.loader-strip-track { display:flex; animation:filmroll 1s linear infinite; flex-shrink:0; }
@keyframes filmroll { from{transform:translateX(0)} to{transform:translateX(-64px)} }
.loader-strip-frame {
    width:64px; height:52px; background:#0a0a0a;
    border-right:2px solid #1a1a1a; flex-shrink:0; position:relative;
}
.loader-strip-frame::before,.loader-strip-frame::after {
    content:''; position:absolute; width:12px; height:9px;
    background:#1a1a1a; border:1px solid #2a2a2a; border-radius:2px;
    left:50%; transform:translateX(-50%);
}
.loader-strip-frame::before { top:7px; }
.loader-strip-frame::after  { bottom:7px; }
.loader-center {
    position:relative; z-index:10;
    display:flex; flex-direction:column; align-items:center; gap:22px;
}
.loader-reel {
    width:100px; height:100px;
    animation:reel-spin 1.3s linear infinite;
    filter:drop-shadow(0 0 18px rgba(255,76,96,.9));
}
@keyframes reel-spin { to { transform:rotate(360deg); } }
.loader-title {
    font-family:'Cinzel',serif; font-size:32px; font-weight:900;
    letter-spacing:14px; color:#fff;
    text-shadow:0 0 30px rgba(255,76,96,.9),0 0 60px rgba(255,76,96,.4);
    animation:title-pulse 2s ease-in-out infinite;
}
@keyframes title-pulse {
    0%,100%{opacity:1;letter-spacing:14px} 50%{opacity:.8;letter-spacing:16px}
}
.loader-subtitle {
    font-size:11px; font-weight:300; letter-spacing:5px;
    color:rgba(255,180,80,.75); text-transform:uppercase; margin-top:-14px;
}
.loader-bar-wrap {
    width:280px; height:3px;
    background:rgba(255,255,255,.08); border-radius:2px; overflow:hidden;
}
.loader-bar-fill {
    height:100%; width:0%;
    background:linear-gradient(90deg,#ff4c60,#ffb347,#ff4c60);
    background-size:200%;
    animation:bar-load 2s ease-out forwards, shimmer 1.5s linear infinite;
    border-radius:2px; box-shadow:0 0 12px rgba(255,76,96,.7);
}
@keyframes bar-load {
    0%{width:0} 40%{width:45%} 70%{width:75%} 90%{width:92%} 100%{width:100%}
}
@keyframes shimmer { 0%{background-position:200%} 100%{background-position:-200%} }
.loader-dots { color:#ffb347; font-size:16px; margin-top:-12px; }
.loader-dots span {
    display:inline-block;
    animation:dot-blink 1s ease-in-out infinite;
}
.loader-dots span:nth-child(2){animation-delay:.2s}
.loader-dots span:nth-child(3){animation-delay:.4s}
@keyframes dot-blink {
    0%,80%,100%{opacity:.2;transform:translateY(0)} 40%{opacity:1;transform:translateY(-4px)}
}


/* ===== SECTION TITLE ===== */
h2.section-title {
    margin: 40px 40px 20px;
    color: #ffeb3b;
    font-size: 26px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    border-left: 5px solid #f8e7e2;
    padding-left: 15px;
}

/* ===== NEWS TICKER ===== */
.ticker-container {
    background: rgba(255,232,0,0.12);
    overflow: hidden;
    white-space: nowrap;
    padding: 10px 0;
    font-weight: bold;
}
.ticker-text {
    display: inline-block;
    padding-left: 100%;
    animation: ticker 20s linear infinite;
    color: #fbf9f9;
    font-family: 'Merriweather', Georgia, 'Times New Roman', serif;
}
@keyframes ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* ===== SLIDER ===== */
.slider-container {
    position: relative;
    width: 100%;
    overflow: hidden;
    border-radius: 0;
    box-shadow: 0 8px 25px rgba(248,22,22,0.88);
}
.slider-slide {
    position: relative;
    width: 100%;
    height: 600px;
    display: none;
    background-size: cover;
    background-position: center;
}
.slider-slide.active { display: block; }
.slider-overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(24,17,17,0.59);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 0 20px;
}
.slider-overlay h2 {
    font-weight: 700;
    color: rgb(250,253,247);
    font-size: 49px;
    margin-bottom: 15px;
    text-shadow: 2px 12px 15px #e2e9e2fc;
    font-family: 'Merriweather', Georgia, 'Times New Roman', serif;
}
.slider-overlay p {
    font-weight: 600;
    color: hsla(22,98%,50%,0.99);
    font-size: 25px;
    margin-bottom: 20px;
    max-width: 700px;
    text-shadow: 1px 10px 4px hsla(0,73%,57%,0.97);
    font-family: 'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif;
}
.slider-overlay a {
    display: inline-block;
    padding: 12px 30px;
    background: #ff4c60;
    color: #fff;
    font-weight: bold;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.3s;
}
.slider-overlay a:hover { background: #ff1a3c; }
.slider-dots {
    position: absolute;
    bottom: 20px;
    width: 100%;
    text-align: center;
}
.slider-dots span {
    display: inline-block;
    width: 20px;
    height: 4px;
    margin: 0 6px;
    background: rgba(255,255,255,0.5);
    border-radius: 20%;
    cursor: pointer;
    transition: 0.3s;
}
.slider-dots span.active { background: #ff4c60; }

/* ===== MOVIE GRID ===== */
.movie-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    padding: 10px 40px 40px;
    justify-content: flex-start;
}

/* ===== MOVIE CARD ===== */
.movie-card {
    position: relative;
    width: 225px;
    height: 420px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 16px;
    overflow: visible;
    box-shadow: 0 8px 28px rgba(0,0,0,0.55);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255,255,255,0.15);
    flex-shrink: 0;
}

.movie-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 20px 40px rgba(0,0,0,0.85);
    border-color: #fe4006;
}

/* Poster */
.movie-card .poster-container {
    position: relative;
    width: 100%;
    height: 270px;
    overflow: hidden;
    border-radius: 16px 16px 0 0;
}

.movie-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1), filter 0.6s ease;
    filter: brightness(0.92) saturate(1);
}

.movie-card:hover img {
    transform: scale(1.12);
    filter: brightness(1.08) saturate(1.25) contrast(1.05);
}

/* Dark overlay on hover */
.movie-card .overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.88) 0%, rgba(0,0,0,0.15) 50%, transparent 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.movie-card:hover .overlay { opacity: 1; }

.movie-card .poster-container::after {
    content: '';
    position: absolute;
    top: 0; left: -75%;
    width: 50%; height: 100%;
    background: linear-gradient(
        120deg,
        transparent 0%,
        rgba(255,255,255,0.13) 50%,
        transparent 100%
    );
    transform: skewX(-20deg);
    opacity: 0;
    transition: none;
    z-index: 5;
    pointer-events: none;
}

.movie-card:hover .poster-container::after {
    animation: shimmer-sweep 0.7s ease forwards;
}

@keyframes shimmer-sweep {
    0%   { left: -75%; opacity: 1; }
    100% { left: 125%; opacity: 1; }
}

/* Trailer button floats above card */
.movie-card .trailer-btn {
    position: absolute;
    bottom: 105%;
    left: 50%;
    transform: translateX(-50%) translateY(20px) scale(0.85);
    background: rgba(255,255,255,0.18);
    border: 1.5px solid rgba(255,255,255,0.55);
    color: #fff;
    padding: 6px 35px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    backdrop-filter: blur(6px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    z-index: 20;
    white-space: nowrap;
    letter-spacing: 0.5px;
    pointer-events: none;
}

.movie-card:hover .trailer-btn {
    opacity: 1;
    transform: translateX(-50%) translateY(0) scale(1);
    pointer-events: auto;
}

.movie-card .trailer-btn:hover {
    background: rgba(255,76,96,0.7);
    border-color: #ff4c60;
}

/* Card content */
.movie-card .card-content {
    padding: 10px 12px 14px;
    background: rgba(20,20,20,0.8);
    backdrop-filter: blur(5px);
    border-top: 1px solid rgba(255,255,255,0.08);
    border-radius: 0 0 16px 16px;
}

.movie-card h3 {
    margin: 0 0 5px;
    font-size: 15px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;   /* ← थप्नुस् */
}

.movie-card .language {
    display: inline-block;
    background: #ff5722;
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 20px;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
}

.movie-card .meta-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    color: #ccc;
    margin: 6px 0;
    background: rgba(0,0,0,0.35);
    padding: 3px 8px;
    border-radius: 20px;
}

.movie-card .duration::before { content: "⏱ "; font-size: 10px; }
.movie-card .views::before    { content: "👁 "; font-size: 10px; }

.movie-card .next-show {
    font-size: 10px;
    color: #ffeb3b;
    background: rgba(0,0,0,0.4);
    padding: 3px 8px;
    border-radius: 20px;
    text-align: center;
    margin: 6px 0;
    border: 1px dashed #ffeb3b;
}

/* Buttons */
.movie-card .buttons {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}

.movie-card .btn {
    flex: 1;
    background: linear-gradient(45deg, #ff5722, #923100);
    border: none;
    color: #fff;
    padding: 7px 0;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    box-shadow: 0 3px 10px rgba(255,87,34,0.35);
    transition: all 0.3s ease;
    display: block;
}

.movie-card .btn:hover {
    background: linear-gradient(45deg, #ff8a50, #ff5722);
    box-shadow: 0 5px 18px rgba(255,87,34,0.6);
    transform: translateY(-2px);
}

/* ===== TITLE LINE ===== */
.line-container { position: relative; width: 100%; text-align: center; margin: 0; }
.line-container h2 {
    display: inline-block;
    font-size: 25px;
    color: hsla(91,98%,52%,1);
    font-weight: bold;
    font-family: 'Merriweather', Georgia, 'Times New Roman', serif;
    position: relative;
    text-shadow: 1px 10px 4px hsla(101,87%,49%,0.97);
}
.line-container h2 span {
    font-family: 'Lato', 'Open Sans', 'Gill Sans', Calibri, sans-serif;
    color: rgba(247,211,9,1);
    font-weight: bold;
}
.line-container h2::after {
    content: "";
    position: absolute;
    left: 50%; bottom: 0;
    transform: translateX(-50%);
    width: 100%; height: 2px;
    background: linear-gradient(90deg, #ff4d4d, #ff9933);
    border-radius: 2px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
    .movie-grid { padding: 10px 24px 32px; gap: 20px; }
}
@media (max-width: 768px) {
    .movie-grid { padding: 10px 16px 28px; gap: 16px; }
    .movie-card { width: 170px; height: 370px; }
    .movie-card .poster-container { height: 220px; }
    .movie-card h3 { font-size: 13px; }
    h2.section-title { margin: 30px 16px 16px; font-size: 20px; }
    .slider-slide { height: 380px; }
    .slider-overlay h2 { font-size: 28px; }
    .slider-overlay p { font-size: 16px; }
}
@media (max-width: 480px) {
    .movie-grid { padding: 8px 12px 24px; gap: 12px; }
    .movie-card { width: 145px; height: 340px; }
    .movie-card .poster-container { height: 190px; }
    .movie-card h3 { font-size: 12px; }
    .movie-card .btn { font-size: 10px; padding: 6px 0; }
    .slider-slide { height: 280px; }
    .slider-overlay h2 { font-size: 20px; }
}

</style>
</head>
<body>

<div id="page-loader">
    <div class="loader-vignette"></div>
    <div class="loader-grain"></div>
    <div class="loader-stars" id="loaderStars"></div>
    <div class="loader-projector"></div>
    <div class="loader-scanline"></div>

    <div class="loader-filmstrip top" id="stripTop"></div>

    <div class="loader-center">
        <div class="loader-reel">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="46" stroke="#ff4c60" stroke-width="1.5" opacity=".35"/>
                <circle cx="50" cy="50" r="36" stroke="#ff4c60" stroke-width="1" opacity=".2"/>
                <circle cx="50" cy="50" r="12" fill="#ff4c60" opacity=".95"/>
                <circle cx="50" cy="50" r="5"  fill="#000"/>
                <line x1="50" y1="4"  x2="50" y2="19" stroke="#ff4c60" stroke-width="3.5" stroke-linecap="round"/>
                <line x1="50" y1="81" x2="50" y2="96" stroke="#ff4c60" stroke-width="3.5" stroke-linecap="round"/>
                <line x1="4"  y1="50" x2="19" y2="50" stroke="#ff4c60" stroke-width="3.5" stroke-linecap="round"/>
                <line x1="81" y1="50" x2="96" y2="50" stroke="#ff4c60" stroke-width="3.5" stroke-linecap="round"/>
                <line x1="15" y1="15" x2="26" y2="26" stroke="#ff4c60" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="74" y1="74" x2="85" y2="85" stroke="#ff4c60" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="85" y1="15" x2="74" y2="26" stroke="#ff4c60" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="26" y1="74" x2="15" y2="85" stroke="#ff4c60" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="50" cy="19" r="5.5" fill="#111" stroke="#ff4c60" stroke-width="1.5"/>
                <circle cx="50" cy="81" r="5.5" fill="#111" stroke="#ff4c60" stroke-width="1.5"/>
                <circle cx="19" cy="50" r="5.5" fill="#111" stroke="#ff4c60" stroke-width="1.5"/>
                <circle cx="81" cy="50" r="5.5" fill="#111" stroke="#ff4c60" stroke-width="1.5"/>
            </svg>
        </div>
        <div class="loader-title">CineMa Ghar</div>
        <div class="loader-subtitle">Now Loading Your Experience</div>
        <div class="loader-bar-wrap"><div class="loader-bar-fill"></div></div>
        <div class="loader-dots">
            <span>●</span><span>●</span><span>●</span>
        </div>
    </div>

    <div class="loader-filmstrip bottom" id="stripBot"></div>
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

// Build film strips dynamically
['stripTop','stripBot'].forEach(id => {
    const el = document.getElementById(id);
    for(let t = 0; t < 2; t++){
        const tr = document.createElement('div');
        tr.className = 'loader-strip-track';
        for(let i = 0; i < 18; i++){
            const f = document.createElement('div');
            f.className = 'loader-strip-frame';
            tr.appendChild(f);
        }
        el.appendChild(tr);
    }
});

// Build stars
const starsEl = document.getElementById('loaderStars');
for(let i = 0; i < 70; i++){
    const s = document.createElement('div');
    s.className = 'loader-star';
    const sz = Math.random() * 2 + 1;
    s.style.cssText = `width:${sz}px;height:${sz}px;top:${Math.random()*100}%;left:${Math.random()*100}%;--d:${(Math.random()*3+1.5).toFixed(1)}s;--delay:${(Math.random()*2).toFixed(1)}s;`;
    starsEl.appendChild(s);
}

// Page loader hide
window.addEventListener("load", () => {
    const loader = document.getElementById("page-loader");
    const content = document.getElementById("page-content");
    setTimeout(() => {
        loader.style.opacity = "0";
        setTimeout(() => {
            loader.style.display = "none";
            content.style.display = "block";
        }, 600);
    }, 900);
});
</script>
</body>
</html>