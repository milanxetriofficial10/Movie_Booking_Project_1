<?php
require 'includes/db.php';
require 'includes/header.php';
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats – <?= htmlspecialchars($show['title']) ?></title>
    <!-- Font Awesome for seat icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(145deg, #0b0f1a 0%, #1a2332 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main container */
        .booking-layout {
            display: flex;
            gap: 2rem;
            max-width: 1400px;
            margin: 3rem auto;
            padding: 0 2rem;
            flex-wrap: wrap;
            justify-content: center;
            flex: 1;
        }

        /* ---------- POSTER CARD ---------- */
        .poster-side {
            flex: 0 0 300px;
            perspective: 1000px;
        }

        .poster-wrapper {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.8);
            transform: rotateY(0deg);
            transition: transform 0.4s ease;
        }

        .poster-wrapper:hover {
            transform: rotateY(2deg) scale(1.02);
        }

        .poster-wrapper img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 24px;
        }

        .poster-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(10, 15, 30, 0.85);
            backdrop-filter: blur(8px);
            color: white;
            padding: 1.8rem 1.2rem;
            text-align: center;
            border-top: 2px solid #3b82f6;
        }

        .poster-overlay h2 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 5px rgba(0,0,0,0.5);
        }

        .poster-overlay p {
            margin: 0.3rem 0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .poster-overlay i {
            color: #fbbf24;
            width: 20px;
        }

        .tags {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .tag {
            padding: 6px 18px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .genre {
            background: #3b82f6;
            box-shadow: 0 4px 10px rgba(59,130,246,0.4);
        }

        .language {
            background: #10b981;
            box-shadow: 0 4px 10px rgba(16,185,129,0.4);
        }

        /* ---------- SEAT + FORM SIDE ---------- */
        .seat-booking-side {
            flex: 1;
            min-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Seat map card */
        .seat-section {
            background: rgba(20, 28, 45, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 1.8rem 1.8rem 2.2rem;
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.6), inset 0 1px 2px rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.03);
        }

        .seat-section h3 {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seat-section h3 i {
            color: #fbbf24;
            font-size: 2rem;
        }

        #seat-map {
            display: grid;
            gap: 12px;
            justify-content: center;
            padding: 10px 0;
        }

        /* Individual seat */
        .seat {
            width: 55px;
            height: 55px;
            background: #253244;
            border-radius: 18px 18px 10px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #4b5563;
            box-shadow: 0 5px 0 #0f172a;
            color: #b0c4de;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .seat i {
            font-size: 1.6rem;
            margin-bottom: 2px;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .seat span {
            background: rgba(0,0,0,0.4);
            padding: 2px 6px;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        /* Available hover */
        .seat:not(.booked):hover {
            transform: translateY(-6px);
            background: #3b82f6;
            border-color: #fbbf24;
            box-shadow: 0 10px 20px -5px #3b82f6;
        }

        .seat:not(.booked):hover i {
            color: white;
        }

        /* Selected seat */
        .seat.selected {
            background: #2563eb;
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.5), 0 8px 0 #0f172a;
            transform: translateY(-3px);
        }

        .seat.selected i {
            color: white;
        }

        /* Booked seat */
        .seat.booked {
            background: #4b5563;
            border-color: #2d3748;
            box-shadow: 0 5px 0 #1e293b;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .seat.booked i {
            color: #9ca3af;
        }

        /* Legend */
        .legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px dashed #4b5563;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .legend-icon {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #253244;
            border: 1px solid #4b5563;
            box-shadow: 0 3px 0 #0f172a;
        }

        .legend-icon i {
            font-size: 1.2rem;
            color: #94a3b8;
        }

        .legend-icon.available i { color: #94a3b8; }
        .legend-icon.selected { background: #2563eb; }
        .legend-icon.selected i { color: white; }
        .legend-icon.booked { background: #4b5563; opacity: 0.6; }
        .legend-icon.booked i { color: #9ca3af; }

        /* Booking form */
        .booking-section {
            background: rgba(20, 28, 45, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.03);
            box-shadow: 0 20px 30px -10px black;
        }

        .booking-section form {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .form-row label {
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-row label i {
            color: #fbbf24;
            width: 20px;
        }

        .booking-section input {
            background: #1e2a3a;
            border: 1px solid #3b4a5e;
            border-radius: 40px;
            padding: 14px 20px;
            color: white;
            font-size: 1rem;
            transition: 0.2s;
            outline: none;
        }

        .booking-section input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.3);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            padding: 1rem 1.5rem;
            border-radius: 60px;
            margin: 0.5rem 0;
            border: 1px solid #3b82f6;
        }

        .total-text {
            font-weight: 600;
            font-size: 1.2rem;
            color: #fbbf24;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            color: white;
        }

        button {
            background: linear-gradient(95deg, #2563eb, #1d4ed8);
            border: none;
            padding: 16px;
            border-radius: 60px;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        button:hover {
            background: linear-gradient(95deg, #1d4ed8, #2563eb);
            transform: scale(1.02);
            box-shadow: 0 10px 25px -5px #2563eb;
        }

        button i {
            font-size: 1.3rem;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            padding: 1.5rem;
            color: #7f8c9f;
            font-size: 0.9rem;
            border-top: 1px solid #253244;
            margin-top: 2rem;
        }

        @media (max-width: 800px) {
            .seat-booking-side {
                min-width: 100%;
            }
            #seat-map {
                grid-template-columns: repeat(auto-fit, minmax(45px, 1fr));
            }
            .seat {
                width: 45px;
                height: 45px;
            }
            .seat i {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <main class="booking-layout">
        <!-- POSTER SIDE -->
        <div class="poster-side">
            <div class="poster-wrapper">
                <img src="/Movie_Booking_Project_1/uploads/<?= htmlspecialchars($show['poster']) ?>" 
                     alt="<?= htmlspecialchars($show['title']) ?>">
                <div class="poster-overlay">
                    <h2><?= htmlspecialchars($show['title']) ?></h2>
                    <p><i class="fas fa-video"></i> <?= htmlspecialchars($show['screen_name']) ?></p>
                    <p><i class="fas fa-clock"></i> <?= htmlspecialchars($show['duration']) ?></p>
                    <p><i class="fas fa-tag"></i> Rs <?= $price ?>/seat</p>
                    <div class="tags">
                        <span class="tag genre"><?= htmlspecialchars($show['genre']) ?></span>
                        <span class="tag language"><?= htmlspecialchars($show['language']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEAT + FORM SIDE -->
        <div class="seat-booking-side">
            <!-- Seat Map -->
            <div class="seat-section">
                <h3>
                    <i class="fas fa-couch"></i> Select Your Seats
                </h3>
                <div id="seat-map"></div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-icon available"><i class="fas fa-chair"></i></div>
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

            <!-- Booking Form -->
            <div class="booking-section">
                <form id="booking-form" method="post" action="booking_review.php">
                    <input type="hidden" name="show_id" value="<?= $show_id ?>">
                    <input type="hidden" name="seats" id="selected-seats">

                    <div class="form-row">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="user_name" placeholder="e.g., John Doe" required>
                    </div>

                    <div class="form-row">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="user_email" placeholder="john@example.com" required>
                    </div>

                    <div class="form-row">
                        <label><i class="fas fa-phone-alt"></i> Mobile Number</label>
                        <input type="text" name="user_mobile" placeholder="10-digit mobile" required>
                    </div>

                    <div class="total-row">
                        <span class="total-text">Total Amount</span>
                        <span class="total-amount" id="total">Rs 0</span>
                    </div>

                    <button type="submit">
                        <i class="fas fa-ticket-alt"></i> Proceed to Review
                    </button>
                </form>
            </div>
        </div>
    </main>

    <div class="footer-note">
        <i class="fas fa-shield-alt"></i> Secure checkout · Prices inclusive of taxes
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bookedSeats = <?= $booked_js ?>;
            const seatMap = document.getElementById('seat-map');
            const selectedSeatsInput = document.getElementById('selected-seats');
            const totalSpan = document.getElementById('total');
            const price = <?= $price ?>;
            let selected = [];

            // Build grid
            seatMap.style.gridTemplateColumns = `repeat(<?= $cols ?>, 55px)`;

            for (let r = 1; r <= <?= $rows ?>; r++) {
                for (let c = 1; c <= <?= $cols ?>; c++) {
                    const seatId = r + '-' + c;
                    const seatDiv = document.createElement('div');
                    seatDiv.className = 'seat';
                    seatDiv.setAttribute('data-seat', seatId);

                    // Icon + seat number
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

            // Form validation (same as before)
            const form = document.getElementById('booking-form');
            const nameInput = form.querySelector('input[name="user_name"]');
            const mobileInput = form.querySelector('input[name="user_mobile"]');

            form.addEventListener('submit', (e) => {
                let valid = true;
                let messages = [];
                if (/\d/.test(nameInput.value)) {
                    valid = false;
                    messages.push("Name cannot contain numbers.");
                }
                if (!/^\d{10}$/.test(mobileInput.value.trim())) {
                    valid = false;
                    messages.push("Mobile number must be exactly 10 digits.");
                }
                if (!valid) {
                    e.preventDefault();
                    alert(messages.join("\n"));
                }
            });

            nameInput.addEventListener('input', () => {
                nameInput.value = nameInput.value.replace(/[0-9]/g, '');
            });

            mobileInput.addEventListener('input', () => {
                mobileInput.value = mobileInput.value.replace(/\D/g, '').slice(0, 10);
            });
        });
    </script>
</body>
</html>
<?php include 'includes/footer.php'; ?>