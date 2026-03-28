<?php
require 'includes/db.php';
require 'includes/header.php';

$conn = db_connect();

$show_id = (int)($_GET['show_id'] ?? 0);
if ($show_id <= 0) {
    echo "<p style='color:red; text-align:center;'>Invalid show ID. Please go back and try again.</p>";
    exit;
}

// Use prepared statement for security
$stmt = $conn->prepare("
    SELECT s.*, m.title, m.genre, m.duration, m.poster,
           sc.screen_name,
           COALESCE(sc.rows, 5) AS rows_count,
           COALESCE(sc.cols, 8) AS cols_count,
           COALESCE(s.price, 350) AS price_amount
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $show_id);
$stmt->execute();
$show = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$show) {
    echo "<p style='color:red; text-align:center;'>Show not found. Please choose another show.</p>";
    exit;
}

// Default fallbacks for missing data
$show['poster']   = empty($show['poster']) ? 'default.jpg' : $show['poster'];
$show['genre']    = empty($show['genre']) ? 'Drama' : $show['genre'];
$show['duration'] = empty($show['duration']) ? '2h 15m' : $show['duration'];
$show['language'] = empty($show['language']) ? 'English' : $show['language'];

// Fetch booked seats (again using prepared statement)
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= htmlspecialchars($show['title']) ?> – Select Seats</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ---------- PAGE LOADER ---------- */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0a0f1c;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s ease;
        }
        .loader {
            text-align: center;
        }
        .loader i {
            font-size: 3.5rem;
            color: #fbbf24;
            animation: spin 1s linear infinite;
        }
        .loader p {
            margin-top: 1rem;
            font-size: 1.2rem;
            font-weight: 500;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #fbbf24, #fff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(rgba(26, 8, 8, 0.8), rgba(0, 0, 0, 0.5)),
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
            transition: all 0.3s ease;
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

        /* Seat map grid – fully responsive */
        #seat-map {
            display: grid;
            grid-template-columns: repeat(<?= $cols ?>, minmax(55px, 75px));
            gap: 10px;
            justify-content: center;
            margin: 1rem 0;
        }

        .seat {
            background: rgba(37, 50, 68, 0.9);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid rgba(255,255,255,0.25);
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            aspect-ratio: 1 / 1; /* ensures square seats */
            font-size: 0.7rem;
            font-weight: 600;
            text-align: center;
        }

        .seat i {
            font-size: 1.4rem;
            margin-bottom: 4px;
            color: #cbd5e1;
        }

        .seat span {
            font-size: 0.7rem;
            font-weight: 500;
            background: rgba(0,0,0,0.4);
            padding: 2px 4px;
            border-radius: 20px;
            display: inline-block;
        }

        .seat.selected {
            background: #3b82f6;
            border-color: #fbbf24;
            transform: scale(0.96);
            box-shadow: 0 0 12px rgba(59,130,246,0.5);
        }

        .seat.booked {
            background: rgba(75, 85, 99, 0.7);
            cursor: not-allowed;
            opacity: 0.6;
            filter: grayscale(0.2);
            transform: none;
        }

        .seat:not(.booked):hover {
            background: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.3);
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
            padding-top: 0.8rem;
            border-top: 1px dashed rgba(255,255,255,0.2);
            font-size: 0.8rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(37, 50, 68, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .legend-icon.selected { background: #3b82f6; }
        .legend-icon.booked { background: rgba(75, 85, 99, 0.7); }

        /* Selected seats summary */
        .selected-summary {
            background: rgba(15, 23, 42, 0.7);
            border-radius: 16px;
            padding: 0.8rem;
            margin: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .selected-summary span {
            color: #fbbf24;
            font-weight: 600;
        }

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
            padding: 10px 16px;
            color: white;
            font-size: 0.9rem;
            outline: none;
            transition: 0.2s;
        }

        .booking-section input:focus {
            border-color: #3b82f6;
            background: rgba(30, 42, 58, 0.9);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.7);
            padding: 0.7rem 1rem;
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
            padding: 12px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background: #1d4ed8;
            transform: scale(1.02);
        }

        /* Responsive adjustments */
        @media (max-width: 800px) {
            .booking-layout {
                padding: 0 1rem;
            }
            #seat-map {
                grid-template-columns: repeat(<?= $cols ?>, minmax(45px, 55px));
                gap: 6px;
            }
            .seat i {
                font-size: 1.1rem;
            }
            .seat span {
                font-size: 0.6rem;
            }
        }

        @media (max-width: 550px) {
            #seat-map {
                grid-template-columns: repeat(<?= $cols ?>, minmax(35px, 45px));
                gap: 4px;
            }
        }
    </style>
</head>
<body>

<!-- Page Loader -->
<div id="preloader">
    <div class="loader">
        <i class="fas fa-couch"></i>
        <p>Loading seat map...</p>
    </div>
</div>

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
            <span class="info-badge"><i class="fas fa-coins"></i> Rs <?= number_format($price, 2) ?>/seat</span>
        </div>
    </div>

    <div class="seat-booking-side">
        <div class="seat-section">
            <h3><i class="fas fa-couch"></i> Select Seats</h3>
            <div id="seat-map"></div>
            <div class="selected-summary" id="selected-summary">
                <i class="fas fa-check-circle"></i> Selected seats: <span id="selected-count">0</span> | Total: <span id="selected-total">Rs 0</span>
            </div>
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
                    <span class="total-text">Grand Total</span>
                    <span class="total-amount" id="total">Rs 0</span>
                </div>

                <button type="submit">
                    <i class="fas fa-ticket-alt"></i> Review Booking
                </button>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const bookedSeats = <?= $booked_js ?>;
        const seatMap = document.getElementById('seat-map');
        const selectedSeatsInput = document.getElementById('selected-seats');
        const totalSpan = document.getElementById('total');
        const selectedCountSpan = document.getElementById('selected-count');
        const selectedTotalSpan = document.getElementById('selected-total');
        const price = <?= $price ?>;
        let selected = [];

        // Clear any existing content (safety)
        seatMap.innerHTML = '';

        // Create seats
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
                    seatDiv.addEventListener('click', function (e) {
                        e.stopPropagation();
                        if (seatDiv.classList.contains('selected')) {
                            seatDiv.classList.remove('selected');
                            selected = selected.filter(s => s !== seatId);
                        } else {
                            seatDiv.classList.add('selected');
                            selected.push(seatId);
                        }
                        updateSelectionSummary();
                    });
                }
                seatMap.appendChild(seatDiv);
            }
        }

        function updateSelectionSummary() {
            const total = selected.length * price;
            selectedSeatsInput.value = JSON.stringify(selected);
            totalSpan.innerText = 'Rs ' + total.toFixed(2);
            selectedCountSpan.innerText = selected.length;
            selectedTotalSpan.innerText = 'Rs ' + total.toFixed(2);
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

        // Initial update
        updateSelectionSummary();

        // Hide preloader after everything is loaded
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 600);
            }
        });
    });
</script>
</body>
</html>