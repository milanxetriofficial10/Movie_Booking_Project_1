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

// Fetch show info and movie poster
$show = $conn->query("
    SELECT s.*, m.title, m.poster, COALESCE(s.price,350) AS price_amount
    FROM shows s
    JOIN movies m ON s.movie_id=m.id
    WHERE s.id=$show_id
")->fetch_assoc();

if(!$show){ echo "<p>Show not found</p>"; exit; }

$total = count($seats) * $show['price_amount'];
$seats_json = json_encode($seats);
?>

<style>
/* ===== Base Styles ===== */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg,#e0f2fe,#f0f9ff);
  color: #1f2937;
}

/* ===== Review Card ===== */
.review-card {
    max-width: 700px;
    margin: 30px auto;
    padding: 30px 25px;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    position: relative;
    overflow: hidden;
    animation: cardFadeIn 0.8s ease forwards;
}

/* Heading */
.review-card h2 {
    text-align: center;
    color: #1e3a8a;
    margin-bottom: 25px;
    font-size: 2rem;
    animation: fadeUp 0.6s ease forwards;
}

/* Movie Poster */
.review-card img.poster {
    display: block;
    margin: 0 auto 25px;
    max-width: 240px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    transform: scale(0.95);
    transition: transform 0.4s ease, box-shadow 0.3s ease;
}
.review-card img.poster:hover {
    transform: scale(1.05) rotate(1deg);
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
}

/* Info Text */
.review-card p {
    font-size: 1rem;
    margin-bottom: 14px;
    color: #374151;
    line-height: 1.5;
}

/* Seats List */
.seat-list {
    list-style: none;
    padding-left: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}
.seat-list li {
    background: #e5e7eb;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    cursor: default;
    position: relative;
    animation: seatFadeIn 0.6s ease forwards;
}
.seat-list li:nth-child(even) { animation-delay: 0.2s; }
.seat-list li:nth-child(odd) { animation-delay: 0.4s; }

/* Delete Button */
.seat-list li button.remove-seat {
    margin-left: 8px;
    padding: 3px 8px;
    background: #f87171;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
}
.seat-list li button.remove-seat:hover {
    background: #ef4444;
    transform: scale(1.1);
}

/* Buttons */
button.confirm-btn, button.modify-btn {
    width: 100%;
    padding: 12px;
    margin-top: 12px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    color: #fff;
    transition: all 0.3s ease;
}
button.confirm-btn {
    background: linear-gradient(90deg,#2563eb,#1d4ed8);
}
button.confirm-btn:hover {
    background: linear-gradient(90deg,#1e40af,#1d4ed8);
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(37,99,235,0.5);
}
button.modify-btn {
    background: linear-gradient(90deg,#f87171,#ef4444);
}
button.modify-btn:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(248,113,113,0.5);
}

/* ===== Animations ===== */
@keyframes cardFadeIn {
    0% { opacity: 0; transform: translateY(30px);}
    100% { opacity: 1; transform: translateY(0);}
}

@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(20px);}
    100% { opacity: 1; transform: translateY(0);}
}

@keyframes seatFadeIn {
    0% { opacity: 0; transform: scale(0.8);}
    100% { opacity: 1; transform: scale(1);}
}

/* Responsive */
@media(max-width:600px){
    .review-card { padding: 20px; }
    .seat-list { justify-content: center; }
}

</style>

<div class="review-card">
    <h2>🎬 Review Booking</h2>

    <?php if(!empty($show['poster'])): ?>
        <img class="poster" src="/Movie_Booking_Project_1/uploads/<?=htmlspecialchars($show['poster'])?>" 
             alt="<?=htmlspecialchars($show['title'])?>">
    <?php endif; ?>

    <p><strong>Movie:</strong> <?=htmlspecialchars($show['title'])?></p>
    <p><strong>Price per Seat:</strong> Rs <?=$show['price_amount']?></p>
    <p><strong>Total:</strong> Rs <span id="total"><?= $total ?></span></p>

    <h3>Selected Seats:</h3>
    <ul id="seat-list" class="seat-list">
        <?php foreach($seats as $seat): ?>
            <li data-seat="<?=htmlspecialchars($seat)?>">
                <?=htmlspecialchars($seat)?> 
                <button type="button" class="remove-seat">Delete</button>
            </li>
        <?php endforeach; ?>
    </ul>

    <form id="confirm-form" method="post" action="booking_confirm.php">
        <input type="hidden" name="show_id" value="<?=$show_id?>">
        <input type="hidden" name="seats" id="seats-input" value='<?=$seats_json?>'>
        <input type="hidden" name="user_name" value="<?=htmlspecialchars($user_name)?>">
        <input type="hidden" name="user_email" value="<?=htmlspecialchars($user_email)?>">
        <input type="hidden" name="user_mobile" value="<?=htmlspecialchars($user_mobile)?>">
        <button type="submit" class="confirm-btn">Confirm Booking</button>
    </form>

    <form method="get" action="book.php">
        <input type="hidden" name="show_id" value="<?=$show_id?>">
        <button type="submit" class="modify-btn">Modify Selection</button>
    </form>
</div>

<script>
let seats = <?= $seats_json ?>;

function updateTotal(){
    const price = <?= $show['price_amount'] ?>;
    document.getElementById('total').textContent = seats.length * price;
    document.getElementById('seats-input').value = JSON.stringify(seats);
}

// Handle remove seat buttons
document.querySelectorAll('.remove-seat').forEach(btn=>{
    btn.addEventListener('click', function(){
        const li = this.parentElement;
        const seat = li.getAttribute('data-seat');
        seats = seats.filter(s => s !== seat);
        li.remove();
        updateTotal();
    });
});

updateTotal();
</script>

<?php require 'includes/footer.php'; ?>
<script>
    // ===== Seat Remove & Total Animation =====
let seats = <?= $seats_json ?>;

function updateTotal(){
    const price = <?= $show['price_amount'] ?>;
    const totalEl = document.getElementById('total');
    totalEl.textContent = seats.length * price;
    document.getElementById('seats-input').value = JSON.stringify(seats);
    totalEl.style.transform = 'scale(1.2)';
    setTimeout(()=> totalEl.style.transform = 'scale(1)',200);
}

// Remove seat with fade-out animation
document.querySelectorAll('.remove-seat').forEach(btn=>{
    btn.addEventListener('click', function(){
        const li = this.parentElement;
        li.style.transition = 'all 0.3s ease';
        li.style.opacity = 0;
        li.style.transform = 'scale(0.7)';
        setTimeout(()=> li.remove(), 300);
        const seat = li.getAttribute('data-seat');
        seats = seats.filter(s => s !== seat);
        setTimeout(updateTotal, 300);
    });
});

updateTotal();

</script>