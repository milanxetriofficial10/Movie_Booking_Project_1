 <?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: book.php');
    exit;
}

require 'includes/header.php'; // ✅ keep this ONLY

$conn = db_connect();

$show_id = (int)($_POST['show_id'] ?? 0);
$seats   = json_decode($_POST['seats'] ?? '[]', true);
$user_name  = trim($_POST['user_name'] ?? '');
$user_email = trim($_POST['user_email'] ?? '');
$user_mobile= trim($_POST['user_mobile'] ?? '');

if (!$show_id || !$seats || !$user_name) {
    echo "<p>Invalid booking data.</p>";
    exit;
}

// Fetch show details
$show = $conn->query("
    SELECT s.*, m.title, m.poster, m.genre, m.duration, m.language,
           sc.screen_name,
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

$total = count($seats) * $show['price_amount'];
$seats_json = json_encode($seats);

$show_time = date('d M Y, h:i A', strtotime($show['show_date'] . ' ' . $show['show_time']));
?>


<style>

        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* ❌ body flex हटायो */
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #0b1120 0%, #0f172a 100%);
}

/* ✅ center only this page */
.review-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

/* बाकी CSS SAME */
.review-card {
    max-width: 1300px;
    width: 100%;
    background: rgba(15, 25, 45, 0.65);
    border-radius: 2rem;
    padding: 2rem;
}

        /* Animated background overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg") center/cover no-repeat;
            filter: blur(8px) brightness(0.4);
            z-index: -2;
        }


        h2 {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #ffffff, #a5f3fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        /* Flex container: left image + right details */
        .review-content {
            display: flex;
            gap: 2.5rem;
            flex-wrap: wrap;
        }

        /* ---------- LEFT POSTER ---------- */
        .poster-box {
            flex: 0 0 280px;
            position: relative;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.5);
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        .poster-box img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        .poster-box:hover {
            transform: scale(1.02);
            box-shadow: 0 25px 40px -12px black;
        }

        .poster-box:hover img {
            transform: scale(1.05);
        }

        /* Badge overlay (optional) */
        .poster-box::after {
            content: "Now Showing";
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            letter-spacing: 0.5px;
            border-left: 3px solid #f97316;
        }

        /* ---------- RIGHT DETAILS ---------- */
        .details-box {
            flex: 1;
            min-width: 280px;
        }

        /* Modern info grid */
        .movie-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 1.5rem;
            padding: 1.5rem;
            margin-bottom: 1.8rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .info-item .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #94a3b8;
        }

        .info-item .value {
            font-size: 1rem;
            font-weight: 600;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
            word-break: break-word;
        }

        .info-item .value i {
            color: #f97316;
            width: 20px;
            font-size: 0.9rem;
        }

        /* Seats section */
        .seats-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            flex-wrap: wrap;
            margin: 1.5rem 0 1rem 0;
        }

        .seats-header h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .seat-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            list-style: none;
            margin: 0 0 1.5rem 0;
            padding: 0;
        }

        .seat-list li {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border: 1px solid rgba(249, 115, 22, 0.5);
            padding: 0.5rem 1rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #facc15;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(4px);
            transition: all 0.2s;
        }

        .remove-seat {
            background: #ef4444;
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .remove-seat:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* Total row */
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(95deg, rgba(0, 0, 0, 0.5), rgba(249, 115, 22, 0.1));
            border-radius: 60px;
            padding: 1rem 1.8rem;
            margin: 1.5rem 0;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }

        .total-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: #cbd5e1;
        }

        .total-amount {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #facc15, #ff8c42);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .confirm-btn, .modify-btn {
            flex: 1;
            padding: 0.9rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            border: none;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .confirm-btn {
            background: linear-gradient(95deg, #f97316, #ea580c);
            color: white;
            box-shadow: 0 8px 20px -6px rgba(249, 115, 22, 0.4);
        }

        .confirm-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -8px rgba(249, 115, 22, 0.6);
            filter: brightness(1.05);
        }

        .modify-btn {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modify-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Loader */
        .loader {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 9999;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(249, 115, 22, 0.2);
            border-top: 4px solid #f97316;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .loader p {
            margin-top: 1rem;
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 850px) {
            body {
                padding: 1rem;
            }
            .review-card {
                padding: 1.5rem;
            }
            .poster-box {
                flex: 0 0 220px;
            }
            .poster-box img {
                height: 340px;
            }
            .total-amount {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 650px) {
            .review-content {
                flex-direction: column;
                align-items: center;
            }
            .poster-box {
                flex: 0 0 auto;
                width: 220px;
            }
            .movie-info-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>

<div class="loader" id="loader">
    <div class="spinner"></div>
    <p>Confirming your booking...</p>
</div>

<div class="review-card">
    <h2><i class="fas fa-ticket-alt"></i> Review Your Booking</h2>

    <div class="review-content">
        <div class="poster-box">
            <?php if (!empty($show['poster'])): ?>
                <img src="/Movie_Booking_Project_1/uploads/<?= htmlspecialchars($show['poster']) ?>" alt="<?= htmlspecialchars($show['title']) ?>">
            <?php else: ?>
                <img src="https://placehold.co/400x600/1e293b/white?text=No+Poster">
            <?php endif; ?>
        </div>

        <div class="details-box">
            <div class="movie-info-grid">
                <div class="info-item">
                    <span class="label">Movie</span>
                    <span class="value"><?= htmlspecialchars($show['title']) ?></span>
                </div>

                <div class="info-item">
                    <span class="label">Screen</span>
                    <span class="value"><?= htmlspecialchars($show['screen_name']) ?></span>
                </div>

                <div class="info-item">
                    <span class="label">Show Time</span>
                    <span class="value"><?= htmlspecialchars($show_time) ?></span>
                </div>

                <div class="info-item">
                    <span class="label">Price</span>
                    <span class="value">₹ <?= number_format($show['price_amount'], 2) ?></span>
                </div>
            </div>

            <ul id="seat-list" class="seat-list">
                <?php foreach ($seats as $seat): ?>
                    <li data-seat="<?= htmlspecialchars($seat) ?>">
                        <?= htmlspecialchars($seat) ?>
                        <button type="button" class="remove-seat">✕</button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="total-row">
                <span>Total</span>
                <span id="total">₹ <?= number_format($total, 2) ?></span>
            </div>

            <div class="action-buttons">
                <form id="confirm-form" method="post" action="booking_confirm.php">
                    <input type="hidden" name="show_id" value="<?= $show_id ?>">
                    <input type="hidden" name="seats" id="seats-input" value='<?= $seats_json ?>'>
                    <input type="hidden" name="user_name" value="<?= htmlspecialchars($user_name) ?>">
                    <input type="hidden" name="user_email" value="<?= htmlspecialchars($user_email) ?>">
                    <input type="hidden" name="user_mobile" value="<?= htmlspecialchars($user_mobile) ?>">
                    <button type="submit" class="confirm-btn">Confirm</button>
                </form>

                <form method="get" action="book.php">
                    <input type="hidden" name="show_id" value="<?= $show_id ?>">
                    <button type="submit" class="modify-btn">Modify</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let seats = <?= $seats_json ?>;

function updateTotal() {
    const price = <?= $show['price_amount'] ?>;
    const total = seats.length * price;
    document.getElementById('total').innerText = '₹ ' + total.toFixed(2);
    document.getElementById('seats-input').value = JSON.stringify(seats);
}

document.querySelectorAll('.remove-seat').forEach(btn => {
    btn.addEventListener('click', function() {
        const li = this.parentElement;
        const seat = li.getAttribute('data-seat');
        li.remove();
        seats = seats.filter(s => s !== seat);
        updateTotal();
    });
});

document.getElementById('confirm-form').addEventListener('submit', function() {
    document.getElementById('loader').style.display = 'flex';
});
</script>

<?php require 'includes/footer.php'; ?>
 