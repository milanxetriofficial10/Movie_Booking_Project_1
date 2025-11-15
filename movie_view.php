<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();

$id = (int)($_GET['id'] ?? 0);

// Fetch movie
$movie = $conn->query("SELECT * FROM movies WHERE id=$id")->fetch_assoc();
if(!$movie){ 
    echo "<p style='text-align:center; font-size:1.2rem;'>Movie not found</p>"; 
    exit; 
}

// Check if screens table exists
$screenExists = $conn->query("SHOW TABLES LIKE 'screens'")->num_rows > 0;

// Check if shows table has 'price' column
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

<style>
/* ===== Reset & Base ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(120deg,#f0f4f8,#e0e7ff);
  color: #1f2937;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

a {
  text-decoration: none;
  transition: all 0.3s ease;
}

h2,h3 {
  margin: 0;
  font-weight: 600;
}

/* ===== Container ===== */
.movie-detail {
  display: flex;
  flex-wrap: wrap;
  max-width: 1200px;
  margin: 10px auto;
  gap: 40px;
  padding: 0 20px;
  align-items: flex-start;
  animation: fadeInUp 1s ease forwards;
}

/* ===== Poster ===== */
.poster {
  flex-shrink: 0;
  max-width: 400px;
  overflow: hidden;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.2);
  transform: translateY(20px);
  opacity: 0;
  animation: fadeInUp 1s ease forwards;
  animation-delay: 0.2s;
}

.poster img {
  width: 100%;
  display: block;
  height: 500px;
  border-radius: 15px;
  transition: transform 0.4s ease, filter 0.4s ease;
}

.poster img:hover {
  transform: scale(1.05) rotate(1deg);
  filter: brightness(1.05);
}

/* ===== Info Section ===== */
.info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 18px;
  transform: translateX(30px);
  opacity: 0;
  animation: fadeInLeft 1s ease forwards;
  animation-delay: 0.4s;
}

.info h2 {
  font-size: 2.4rem;
  color: #1e3a8a;
}

.info p {
  font-size: 1rem;
  line-height: 1.6;
  color: #374151;
}

.info strong {
  color: #111827;
}

/* ===== Upcoming Shows ===== */
h3 {
  font-size: 1.8rem;
  color: #2563eb;
  margin-bottom: 15px;
}

.show-list {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
  gap: 20px;
}

.show-list li {
  background: #fff;
  padding: 15px 20px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.show-list li:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.show-list li span {
  font-weight: 500;
  color: #1f2937;
  font-size: 0.95rem;
}

.show-list li .btn {
  background: #1e40af;
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-weight: bold;
  width: 149px;
  transition: background 0.3s, transform 0.2s;
}

.show-list li .btn:hover {
  background: #2563eb;
  transform: scale(1.05);
}

/* ===== Animations ===== */
@keyframes fadeInUp {
  0% { transform: translateY(30px); opacity: 0; }
  100% { transform: translateY(0); opacity: 1; }
}

@keyframes fadeInLeft {
  0% { transform: translateX(50px); opacity: 0; }
  100% { transform: translateX(0); opacity: 1; }
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

<script>
  // Fade-in on scroll for movie info
const fadeElems = document.querySelectorAll('.info, .poster');
window.addEventListener('scroll', () => {
  fadeElems.forEach(el => {
    const rect = el.getBoundingClientRect();
    if(rect.top < window.innerHeight - 50){
      el.style.opacity = 1;
      el.style.transform = 'translate(0,0)';
    }
  });
});

</script>

<div class="movie-detail">
  <div class="poster">
    <?php if(!empty($movie['poster'])): ?>
      <img src="/Movie_Booking_Project_1/uploads/<?=htmlspecialchars($movie['poster'])?>" alt="<?=htmlspecialchars($movie['title'])?>">
    <?php else: ?>
      <img src="/Movie_Booking_Project_1/uploads/default.jpg" alt="No Poster">
    <?php endif; ?>
  </div>

  <div class="info">
    <h2><?=htmlspecialchars($movie['title'])?></h2>
    <p><?=nl2br(htmlspecialchars($movie['description'] ?? 'No description available.'))?></p>
    <p>
      <strong>Duration:</strong> <?=htmlspecialchars($movie['duration'] ?? 'N/A')?> min | 
      <strong>Genre:</strong> <?=htmlspecialchars($movie['genre'] ?? 'N/A')?>
    </p>
    
    <h3>Upcoming Shows</h3>
    <?php if($shows && $shows->num_rows > 0): ?>
      <ul class="show-list">
        <?php while($s = $shows->fetch_assoc()): ?>
          <li>
            <span>
              <?=date('M j, Y H:i', strtotime($s['show_time'] ?? 'now'))?>
              <?php if($screenExists) echo " — ".htmlspecialchars($s['screen_name'] ?? 'Unknown'); ?>
              — Rs <?=htmlspecialchars($priceExists ? ($s['price'] ?? 0) : 0)?>
            </span>
            <a class="btn" href="book.php?show_id=<?= $s['id'] ?? 0 ?>">Book Now</a>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p style="color:#6b7280;">No upcoming shows available.</p>
    <?php endif; ?>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
