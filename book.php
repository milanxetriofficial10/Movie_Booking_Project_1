<?php
require 'includes/db.php';
require 'includes/header.php'; // Make sure header starts HTML properly

$conn = db_connect();

$show_id = (int)($_GET['show_id'] ?? 0);
if ($show_id <= 0) {
    echo "<p>Invalid show ID</p>";
    exit;
}

$show = $conn->query("
    SELECT s.*, m.title, m.genre, m.duration, m.poster,
           sc.screen_name,
           COALESCE(sc.rows, 5) AS rows_count,
           COALESCE(sc.cols, 8) AS cols_count,
           COALESCE(s.price, 350) AS price_amount
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    WHERE s.id = $show_id
")->fetch_assoc();

if (!$show) {
    echo "<p>Show not found</p>";
    exit;
}

// Default fallbacks
$show['poster']   = empty($show['poster']) ? 'default.jpg' : $show['poster'];
$show['genre']    = empty($show['genre']) ? 'Drama' : $show['genre'];
$show['duration'] = empty($show['duration']) ? '2h 15m' : $show['duration'];
$show['language'] = empty($show['language']) ? 'English' : $show['language'];

// Fetch booked seats
$stmt = $conn->prepare("SELECT seats FROM bookings WHERE show_id = ?");
$stmt->bind_param('i', $show_id);
$stmt->execute();
$res = $stmt->get_result();
$booked = [];
while ($r = $res->fetch_assoc()) {
    $arr = json_decode($r['seats'], true);
    if (is_array($arr)) {
        $booked = array_merge($booked, $arr);
    }
}
$booked = array_unique($booked);
$booked_js = json_encode($booked);

$price = (float)$show['price_amount'];
$rows  = (int)$show['rows_count'];
$cols  = (int)$show['cols_count'];
?>

<!-- Page-specific styles -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(rgba(26, 8, 8, 0.58), rgba(0, 0, 0, 0.95)),
                    url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #fff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Main container */
    .booking-layout {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 2rem;
        flex: 1;
    }

    /* Movie title */
    .movie-title-center {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .movie-title-center h1 {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 0 2px 5px rgba(0,0,0,0.5);
        background: linear-gradient(90deg, #fbbf24, #fff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Movie info row */
    .movie-info-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .movie-info-left p {
        margin: 0.3rem 0;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .movie-info-left i {
        width: 24px;
        color: #fbbf24;
    }

    .movie-info-right {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .info-badge {
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(4px);
        padding: 5px 15px;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 500;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .info-badge i {
        margin-right: 5px;
        color: #fbbf24;
    }

    /* Seat + booking side */
    .seat-booking-side {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .seat-section, .booking-section {
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        border-radius: 24px;
        padding: 1.5rem;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .seat-section {
        flex: 1.5;
        min-width: 300px;
    }

    .booking-section {
        flex: 1;
        min-width: 280px;
    }

    .seat-section h3, .booking-section h3 {
        font-size: 1.3rem;
        font-weight: 500;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .seat-section h3 i, .booking-section h3 i {
        color: #fbbf24;
    }

    /* Seat map grid */
    #seat-map {
        display: grid;
        gap: 8px;
        justify-content: center;
        margin: 1rem 0;
    }

    .seat {
        width: 45px;
        height: 45px;
        background: rgba(37, 50, 68, 0.8);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 2px 0 rgba(0,0,0,0.3);
        font-size: 0.65rem;
        font-weight: 600;
    }

    .seat i {
        font-size: 1.2rem;
        margin-bottom: 2px;
        color: #cbd5e1;
    }

    .seat.selected {
        background: #3b82f6;
        border-color: #fbbf24;
        transform: scale(0.98);
    }

    .seat.booked {
        background: rgba(75, 85, 99, 0.6);
        cursor: not-allowed;
        opacity: 0.7;
    }

    .seat:not(.booked):hover {
        background: #2563eb;
        transform: translateY(-2px);
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 0.8rem;
        border-top: 1px dashed rgba(255,255,255,0.2);
        font-size: 0.8rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .legend-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: rgba(37, 50, 68, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .legend-icon.selected { background: #3b82f6; }
    .legend-icon.booked { background: rgba(75, 85, 99, 0.6); }

    /* Booking form */
    .booking-section form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-row {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .form-row label {
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-row label i {
        color: #fbbf24;
        width: 18px;
    }

    .booking-section input {
        background: rgba(30, 42, 58, 0.7);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 40px;
        padding: 8px 15px;
        color: white;
        font-size: 0.9rem;
        outline: none;
    }

    .booking-section input:focus {
        border-color: #3b82f6;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(15, 23, 42, 0.7);
        padding: 0.6rem 1rem;
        border-radius: 40px;
        margin: 0.5rem 0;
    }

    .total-text {
        font-weight: 600;
        color: #fbbf24;
    }

    .total-amount {
        font-size: 1.4rem;
        font-weight: 700;
    }

    button {
        background: #2563eb;
        border: none;
        padding: 10px;
        border-radius: 40px;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    button:hover {
        background: #1d4ed8;
        transform: scale(1.02);
    }

    /* Responsive */
    @media (max-width: 800px) {
        .booking-layout {
            padding: 0 1rem;
        }
        #seat-map {
            grid-template-columns: repeat(<?= $cols ?>, 38px);
        }
        .seat {
            width: 38px;
            height: 38px;
        }
        .seat i {
            font-size: 1rem;
        }
    }
</style>

<main class="booking-layout">
    <div class="movie-title-center">
        <h1><?= htmlspecialchars($show['title']) ?></h1>
    </div>

    <div class="movie-info-row">
        <div class="movie-info-left">
            <p><i class="fas fa-video"></i> <?= htmlspecialchars($show['screen_name']) ?></p>
            <p><i class="fas fa-clock"></i> <?= htmlspecialchars($show['duration']) ?></p>
        </div>
        <div class="movie-info-right">
            <span class="info-badge"><i class="fas fa-tag"></i> <?= htmlspecialchars($show['genre']) ?></span>
            <span class="info-badge"><i class="fas fa-globe"></i> <?= htmlspecialchars($show['language']) ?></span>
            <span class="info-badge"><i class="fas fa-coins"></i> Rs <?= $price ?>/seat</span>
        </div>
    </div>

    <div class="seat-booking-side">
        <div class="seat-section">
            <h3><i class="fas fa-couch"></i> Select Seats</h3>
            <div id="seat-map"></div>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-icon"><i class="fas fa-chair"></i></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon selected"><i class="fas fa-chair"></i></div>
                    <span>Selected</span>
                </div>
                <div class="legend-item">
                    <div class="legend-icon booked"><i class="fas fa-chair"></i></div>
                    <span>Booked</span>
                </div>
            </div>
        </div>

        <div class="booking-section">
            <form id="booking-form" method="post" action="booking_review.php">
                <input type="hidden" name="show_id" value="<?= $show_id ?>">
                <input type="hidden" name="seats" id="selected-seats">

                <div class="form-row">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="user_name" placeholder="Your name" required>
                </div>
                <div class="form-row">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="user_email" placeholder="Your email" required>
                </div>
                <div class="form-row">
                    <label><i class="fas fa-phone-alt"></i> Mobile Number</label>
                    <input type="text" name="user_mobile" placeholder="10-digit number" required>
                </div>

                <div class="total-row">
                    <span class="total-text">Total</span>
                    <span class="total-amount" id="total">Rs 0</span>
                </div>

                <button type="submit">
                    <i class="fas fa-ticket-alt"></i> Review Booking
                </button>
            </form>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const bookedSeats = <?= $booked_js ?>;
        const seatMap = document.getElementById('seat-map');
        const selectedSeatsInput = document.getElementById('selected-seats');
        const totalSpan = document.getElementById('total');
        const price = <?= $price ?>;
        let selected = [];

        // Set grid columns
        seatMap.style.gridTemplateColumns = `repeat(<?= $cols ?>, 45px)`;

        for (let r = 1; r <= <?= $rows ?>; r++) {
            for (let c = 1; c <= <?= $cols ?>; c++) {
                const seatId = r + '-' + c;
                const seatDiv = document.createElement('div');
                seatDiv.className = 'seat';
                seatDiv.setAttribute('data-seat', seatId);
                seatDiv.innerHTML = `<i class="fas fa-chair"></i><span>${seatId}</span>`;

                if (bookedSeats.includes(seatId)) {
                    seatDiv.classList.add('booked');
                } else {
                    seatDiv.addEventListener('click', function () {
                        if (seatDiv.classList.contains('selected')) {
                            seatDiv.classList.remove('selected');
                            selected = selected.filter(s => s !== seatId);
                        } else {
                            seatDiv.classList.add('selected');
                            selected.push(seatId);
                        }
                        selectedSeatsInput.value = JSON.stringify(selected);
                        totalSpan.innerText = 'Rs ' + (selected.length * price);
                    });
                }
                seatMap.appendChild(seatDiv);
            }
        }

        // Form validation
        const form = document.getElementById('booking-form');
        const nameInput = form.querySelector('input[name="user_name"]');
        const mobileInput = form.querySelector('input[name="user_mobile"]');

        nameInput.addEventListener('input', () => {
            nameInput.value = nameInput.value.replace(/[0-9]/g, '');
        });

        mobileInput.addEventListener('input', () => {
            mobileInput.value = mobileInput.value.replace(/\D/g, '').slice(0, 10);
        });

        form.addEventListener('submit', (e) => {
            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one seat.');
                return;
            }
            if (!/^\d{10}$/.test(mobileInput.value.trim())) {
                e.preventDefault();
                alert('Mobile number must be exactly 10 digits.');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>