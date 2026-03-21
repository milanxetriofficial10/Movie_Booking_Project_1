<?php
require 'includes/db.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: book.php');
    exit;
}

require 'includes/header.php';
$conn = db_connect();

$show_id = (int)($_POST['show_id'] ?? 0);
$seats   = json_decode($_POST['seats'] ?? '[]', true);
$user_name  = trim($_POST['user_name'] ?? '');
$user_email = trim($_POST['user_email'] ?? '');
$user_mobile= trim($_POST['user_mobile'] ?? '');

if(!$show_id || !$seats || !$user_name) {
    echo "<p>Invalid booking data.</p>"; exit;
}

// Fetch show details with movie and screen info
$show = $conn->query("
    SELECT s.*, m.title, m.poster, m.genre, m.duration, m.language,
           sc.screen_name,
           COALESCE(s.price, 350) AS price_amount
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    WHERE s.id = $show_id
")->fetch_assoc();

if(!$show){ echo "<p>Show not found</p>"; exit; }

$total = count($seats) * $show['price_amount'];
$seats_json = json_encode($seats);

// Format show time if available
$show_time = date('d M Y, h:i A', strtotime($show['show_date'] . ' ' . $show['show_time']));
?>

<style>
body {
  font-family: 'Poppins', sans-serif;
      background:
        linear-gradient(rgba(26, 8, 8, 0.88), rgba(0, 0, 0, 0.95)),
        url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
}

/* CARD */
.review-card {
    max-width: 10000px;
    margin: 10px auto;
    padding: 25px;
    border-radius: 16px;
   
}

.review-content {
    display: flex;
    gap: 30px;
}

/* LEFT IMAGE - PERFECT POSTER STYLE */
.poster-box {
    width: 260px;
}

.poster-box img {
    width: 100%;
    height: 480px; /* fixed poster height */
    object-fit: cover; /* no stretch */
    border-radius: 10px; /* small smooth corner (not round) */
    

    transition: all 0.4s ease;
}

/* HOVER EFFECT */
.poster-box img:hover {
    transform: scale(1.04) rotate(0.5deg);
    box-shadow: 
        0 20px 40px rgba(0,0,0,0.35),
        0 0 0 3px #fff inset;
}
.poster-box {
    position: relative;
}

.poster-box::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 12px;
    background: linear-gradient(to top, rgba(0,0,0,0.2), transparent);
    pointer-events: none;
}
/* RIGHT DETAILS */
.details-box {
    flex: 1;
}

/* Info grid */
.movie-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item .label {
    font-size: 0.8rem;
    text-transform: uppercase;
    color: #f9fafc;
    letter-spacing: 0.5px;
}

.info-item .value {
    font-size: 1.1rem;
    font-weight: 600;
    color: rgb(249, 250, 252);
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-item .value i {
    color: #f9a516;
    width: 20px;
}

.seat-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.seat-list li {
    background: #2563eb;
    padding: 6px 12px;
    border-radius: 30px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.remove-seat {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.remove-seat:hover {
    background: #dc2626;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: transparent;
    padding: 15px 20px;
    border-radius: 50px;
    margin: 20px 0;
    font-weight: 600;
}

.total-text {
    font-size: 1.1rem;
    color: #67ff38;
}

.total-amount {
    font-size: 1.8rem;
    color: #0afb0e;
}

/* BUTTONS */
.confirm-btn, .modify-btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 10px;
}

.confirm-btn {
    background: transparent;
    border: 2px solid #2563eb;
    color: white;
}

.confirm-btn:hover {
    background: #1d4ed8;
    transform: scale(1.02);
}

.modify-btn {
    background: transparent;
    color: #fafbfc;
    border: 1px solid #cbd5e1;
}

.modify-btn:hover {
    background: #e2e8f0;
}

/* LOADER */
.loader {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.95);
    display: none;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 9999;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #ddd;
    border-top: 5px solid #2563eb;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loader p {
    margin-top: 12px;
    color: #2563eb;
    font-weight: 500;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}

/* MOBILE */
@media(max-width:768px){
    .review-content {
        flex-direction: column;
        align-items: center;
    }
    .poster-box img {
        width: 200px;
    }
}
</style>

<!-- LOADER -->
<div class="loader" id="loader">
    <div class="spinner"></div>
    <p>Processing your booking...</p>
</div>

<div class="review-card">
    <h2 style="text-align:center; margin-bottom:20px;">🎬 Review Your Booking</h2>

    <div class="review-content">
        <!-- LEFT POSTER -->
        <div class="poster-box">
            <?php if(!empty($show['poster'])): ?>
                <img src="/Movie_Booking_Project_1/uploads/<?=htmlspecialchars($show['poster'])?>" alt="<?=htmlspecialchars($show['title'])?>">
            <?php endif; ?>
        </div>

        <!-- RIGHT DETAILS -->
        <div class="details-box">
            <!-- Dynamic info grid -->
            <div class="movie-info-grid">
                <div class="info-item">
                    <span class="label">Movie</span>
                    <span class="value"><?=htmlspecialchars($show['title'])?></span>
                </div>
                <div class="info-item">
                    <span class="label">Screen</span>
                    <span class="value"><i class="fas fa-video"></i> <?=htmlspecialchars($show['screen_name'])?></span>
                </div>
                <div class="info-item">
                    <span class="label">Genre</span>
                    <span class="value"><i class="fas fa-tag"></i> <?=htmlspecialchars($show['genre'])?></span>
                </div>
                <div class="info-item">
                    <span class="label">Language</span>
                    <span class="value"><i class="fas fa-globe"></i> <?=htmlspecialchars($show['language'])?></span>
                </div>
                <div class="info-item">
                    <span class="label">Duration</span>
                    <span class="value"><i class="fas fa-clock"></i> <?=htmlspecialchars($show['duration'])?></span>
                </div>
                <div class="info-item">
                    <span class="label">Show Time</span>
                    <span class="value"><i class="fas fa-calendar-alt"></i> <?=htmlspecialchars($show_time)?></span>
                </div>
                <div class="info-item">
                    <span class="label">Price per seat</span>
                    <span class="value"><i class="fas fa-coins"></i> Rs <?=$show['price_amount']?></span>
                </div>
            </div>

            <!-- Selected seats section -->
            <h3 style="margin-bottom:10px;">Selected Seats</h3>
            <ul id="seat-list" class="seat-list">
                <?php foreach($seats as $seat): ?>
                    <li data-seat="<?=htmlspecialchars($seat)?>">
                        <?=htmlspecialchars($seat)?>
                        <button type="button" class="remove-seat">×</button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Total amount -->
            <div class="total-row">
                <span class="total-text">Total Amount</span>
                <span class="total-amount" id="total">Rs <?= $total ?></span>
            </div>

            <!-- Confirm form -->
            <form id="confirm-form" method="post" action="booking_confirm.php">
                <input type="hidden" name="show_id" value="<?=$show_id?>">
                <input type="hidden" name="seats" id="seats-input" value='<?=$seats_json?>'>
                <input type="hidden" name="user_name" value="<?=htmlspecialchars($user_name)?>">
                <input type="hidden" name="user_email" value="<?=htmlspecialchars($user_email)?>">
                <input type="hidden" name="user_mobile" value="<?=htmlspecialchars($user_mobile)?>">
                <button type="submit" class="confirm-btn">Confirm Booking</button>
            </form>

            <!-- Modify button (go back to seat selection) -->
            <form method="get" action="book.php">
                <input type="hidden" name="show_id" value="<?=$show_id?>">
                <button type="submit" class="modify-btn">Modify Seats</button>
            </form>
        </div>
    </div>
</div>

<script>
let seats = <?= $seats_json ?>;

function updateTotal(){
    const price = <?= $show['price_amount'] ?>;
    document.getElementById('total').innerText = 'Rs ' + (seats.length * price);
    document.getElementById('seats-input').value = JSON.stringify(seats);
}

// Remove seat functionality
document.querySelectorAll('.remove-seat').forEach(btn=>{
    btn.addEventListener('click', function(){
        const li = this.parentElement;
        const seat = li.getAttribute('data-seat');
        li.remove();
        seats = seats.filter(s => s !== seat);
        updateTotal();
    });
});

// Show loader on confirm submit
document.getElementById('confirm-form').addEventListener('submit', function(){
    document.getElementById('loader').style.display = 'flex';
});
</script>

<?php require 'includes/footer.php'; ?>