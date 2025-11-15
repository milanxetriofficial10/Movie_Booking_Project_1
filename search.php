<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();

// Get search keyword
$keyword = trim($_GET['q'] ?? '');
if (!$keyword) {
    echo "<p>Please enter something to search.</p>";
    require 'includes/footer.php';
    exit;

}

$searchTerm = "%$keyword%";

// Fetch movies matching keyword in title, description or genre
$stmt = $conn->prepare("SELECT * FROM movies WHERE title LIKE ? OR description LIKE ? OR genre LIKE ? AND status='active' ORDER BY created_at DESC");
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$movies = $stmt->get_result();

// Function to convert minutes to H:M
function formatDuration($minutes) {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf("%dh %02dm", $h, $m);
}

// Fetch live news
$news_res = $conn->query("SELECT news_text FROM movie_news ORDER BY created_at DESC");
$news_arr = [];
while($row = $news_res->fetch_assoc()){
    $news_arr[] = $row['news_text'];
}
$news_text = !empty($news_arr) ? implode(" ⚡ ", $news_arr) : "No news available";

?>

<style>
/* Copy movie-grid and movie-card styles from index.php for consistency */
.movie-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:20px; max-width:1200px; margin:50px auto; padding:0 15px; }
.movie-card { background:linear-gradient(145deg,#85f369,#160c14,#f1620e); border-radius:15px; overflow:hidden; box-shadow:0px 10px 5px rgba(235, 24, 24, 0.97); transition:transform 0.3s, box-shadow 0.3s; position:relative; display:flex; flex-direction:column; }
.movie-card:hover { transform:translateY(-8px); box-shadow:0 25px 45px rgba(0,0,0,0.2); }
.movie-card img { background: rgba(24, 17, 17, 0.78); width:100%; height:220px; object-fit:cover; transition:transform 0.5s; }
.movie-card:hover img { transform:scale(1.05); background: rgba(24, 17, 17, 0.59); }
.movie-card h3 { font-size:1.2rem; color:#fff; margin:15px; font-weight:700; text-align:center; }
.movie-card p { font-size:0.95rem; color:#eee; margin:0 15px 15px;  -webkit-line-clamp: 2; line-height:1.5; text-align:justify; flex-grow:1; }
.trailer-btn { position:absolute; bottom:-60px; left:50%; transform:translateX(-50%); background: rgba(252, 202, 41, 1); color: rgba(3, 3, 3, 1); width: 143px; font-weight:500; padding:12px 20px; border-radius:30px; text-decoration:none; opacity:0; transition:all 0.5s ease; display:flex; align-items:center; gap:8px; }
.movie-card:hover .trailer-btn { bottom:50%; opacity:1; }
.trailer-btn:hover { background:#ff1a3c; box-shadow:0 0 15px rgba(108, 250, 26, 0.94); }
.movie-card .btn { display:block; margin:0 15px 15px 15px; padding:12px; background:#2563eb; color:#fff; text-align:center; font-weight:600; border-radius:8px; text-decoration:none; transition:0.3s; position:relative; }
.movie-card .btn:hover { background:#1e40af; transform:translateY(-2px); box-shadow:0 8px 15px rgba(37,99,235,0.3); }

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
</style>

<!-- ===== News Ticker ===== -->
<div class="ticker-container">
    <div class="ticker-text"><?=htmlspecialchars($news_text)?></div>
</div>

<h2 style="text-align:center; margin:20px 0; color:#ffcc00;">Search Results for "<?=htmlspecialchars($keyword)?>"</h2>

<?php if($movies->num_rows === 0): ?>
    <p style="text-align:center; color:#eee;">No movies found matching your search.</p>
<?php else: ?>
<section class="movie-grid">
<?php while($m = $movies->fetch_assoc()): ?>
  <article class="movie-card">
    <?php if($m['poster'] && file_exists("uploads/".$m['poster'])): ?>
      <img src="uploads/<?=htmlspecialchars($m['poster'])?>" alt="<?=htmlspecialchars($m['title'])?>">
    <?php else: ?>
      <img src="uploads/default.jpg" alt="No Poster">
    <?php endif; ?>
    <h3><?=htmlspecialchars($m['title'])?></h3>
    <p><?=nl2br(htmlspecialchars(substr($m['description'],0,69)))?></p>
    <p style="text-align:center; font-weight:bold; color:#fff; margin-bottom:8px;">Duration: <?=formatDuration($m['duration'])?></p>

    <?php if(!empty($m['trailer'])): ?>
      <a class="trailer-btn" href="<?=htmlspecialchars($m['trailer'])?>" target="_blank">Watch Trailer</a>
    <?php endif; ?>

    <a class="btn" href="movie_view.php?id=<?=htmlspecialchars($m['id'])?>">View Show times</a>
  </article>
<?php endwhile; ?>
</section>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
