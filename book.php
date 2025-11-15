<?php
require 'includes/db.php';
require 'includes/header.php';
$conn = db_connect();

$show_id = (int)($_GET['show_id'] ?? 0);
if($show_id <= 0){ echo "<p>Invalid show ID</p>"; exit; }

// Fetch show + movie details (including poster)
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

// Fallback values
if(empty($show['poster'])) $show['poster'] = 'default.jpg';
if(empty($show['genre'])) $show['genre'] = 'Drama';
if(empty($show['duration'])) $show['duration'] = '2h 15m';
if(empty($show['language'])) $show['language'] = 'English';

// Fetch booked seats
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

<div class="movie-container">
  <!-- Left: Poster + Info -->
  <div class="movie-left">
    <img src="/Movie_Booking_Project_1/uploads/<?= htmlspecialchars($show['poster']) ?>" 
         alt="<?= htmlspecialchars($show['title']) ?>" class="movie-poster">
    
  </div>

  <!-- Center: Seat Map -->
  <div class="movie-center">
    <h3>Select Your Seats 🎟️</h3>
    <div id="seat-map" style="display:grid; grid-template-columns:repeat(<?=$cols?>,50px); gap:10px;"></div>
  </div>

  <!-- Right: Booking Form -->
  <div class="movie-right">
    <form id="booking-form" method="post" action="booking_review.php">
      <input type="hidden" name="show_id" value="<?=$show_id?>">
      <input type="hidden" name="seats" id="selected-seats">
      <label>Your Name:
        <input type="text" name="user_name" required>
      </label>
      <label>Your Email:
        <input type="email" name="user_email" required>
      </label>
      <label>Your Mobile:
        <input type="text" name="user_mobile" required>
      </label>
      <p>Total: Rs <span id="total">0</span></p>
      <button type="submit">Review Booking</button>
    </form>
  </div>
</div>

<script>
const bookedSeats = <?=$booked_js?>;
const seatMap = document.getElementById('seat-map');
const selectedSeatsInput = document.getElementById('selected-seats');
const totalSpan = document.getElementById('total');
const price = <?=$price?>;
let selected = [];

for(let r=1; r<=<?=$rows?>; r++){
  for(let c=1; c<=<?=$cols?>; c++){
    const seatId = r+'-'+c;
    const seatDiv = document.createElement('div');
    seatDiv.textContent = seatId;
    seatDiv.classList.add('seat');
    seatDiv.style.animation = `fadeInSeat ${0.05*(r*c)}s forwards`;
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
</script>

<?php require 'includes/footer.php'; ?>

<style>
body{
    background: linear-gradient(120deg,#f0f4f8,#e0e7ff);
}
.movie-container {
  display: flex;
  margin-top: 50px;
  justify-content: space-between;
  align-items: flex-start;
  gap: 30px;
  max-width: 1400px;
  margin: 10 auto;
  flex-wrap: wrap;
  animation: fadeInRow 1s ease forwards;
}

/* Left: Poster + Text */
.movie-left {
  flex: 0 0 300px;
  text-align: center;
  margin-left: 20px;
}

h3{

    text-align: center;
    color: #2563eb;
    font-size: 1.5rem;
    font-family: 'Merriweather', Georgia, 'Times New Roman', serif;

}
.movie-poster {
  width: 100%;
  margin-top: 20px;
  height: 500px;
  border-radius: 16px;
  box-shadow: 0 0 25px rgba(37, 99, 235, 0.5);
  transition: transform 0.5s, box-shadow 0.5s;
}
.movie-poster:hover {
  transform: scale(1.05);
  box-shadow: 0 0 40px rgba(37, 99, 235, 0.8);
}



/* Center: Seat Map */
.movie-center { flex: 1; display:flex; flex-direction:column; align-items:center; }
#seat-map {
  background: rgba(30, 41, 59, 0.9);
  padding: 25px;
  border-radius: 16px;
  box-shadow: 0 0 25px rgba(59, 130, 246, 0.3);
  display: grid;
  justify-content: center;
  height: 500px;
}

/* Seats animation */
@keyframes fadeInSeat { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

.seat {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  border:1px solid #475569;
  background-color:#334155;
  display:flex;
  justify-content:center;
  align-items:center;
  color:#f1f5f9;
  cursor:pointer;
  transition: all 0.25s ease;
}

.seat:hover:not(.booked) {
  transform: scale(1.2);
  background-color:#3b82f6;
  box-shadow:0 0 15px rgba(59,130,246,0.7);
}

.seat.booked { background:#64748b; color:#cbd5e1; cursor:not-allowed; }
.seat.selected { background:#2563eb; box-shadow:0 0 20px rgba(37,99,235,0.9); }

/* Right: Form */
.movie-right { flex:0 0 350px; 
     margin-right: 20px;
}
form {
    margin-top: 20px;
  background: rgba(15,23,42,0.85);
  padding:25px 30px;
  height: 500px;
  border-radius:16px;
  width:100%;
  box-shadow:0 0 25px rgba(37,99,235,0.4);
}

form label { display:block; 
    margin-top:20px;
    margin-bottom:12px; font-weight:500; }
form input { width:100%; padding:10px 14px; margin-top:6px; margin-bottom:18px; border-radius:8px; border:none; background:#1e293b; color:#f1f5f9; transition:0.3s; }
form input:focus { outline:none;
    margin-top:20px;
     background:#0f172a; box-shadow:0 0 10px rgba(59,130,246,0.6); }

button { background:linear-gradient(90deg,#2563eb,#1d4ed8); 
    margin-top:80px;
    color:#fff; border:none; padding:12px 20px; border-radius:10px; width:100%; cursor:pointer; transition:all 0.3s; }
button:hover { background:linear-gradient(90deg,#1e40af,#1d4ed8); transform:scale(1.03); box-shadow:0 0 15px rgba(59,130,246,0.6); }

/* Fade-in animation for row */
@keyframes fadeInRow { from {opacity:0; transform:translateY(30px);} to {opacity:1; transform:translateY(0);} }

/* Responsive */
@media(max-width:1100px) {
  .movie-container { flex-direction:column; align-items:center; }
  .movie-left, .movie-center, .movie-right { width:100%; text-align:center; margin-bottom:20px; }
  #seat-map { margin:20px auto; }
  form { max-width:100%; }
}
</style>
