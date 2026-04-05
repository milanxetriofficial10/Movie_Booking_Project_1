<?php
session_start();
require 'includes/db.php';
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

$conn = db_connect();
$user_id = (int)$_SESSION['user_id'];

// LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ===== DELETE BOOKING =====
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $delete_id, $user_id);

    if ($stmt->execute()) {
        header("Location: booking_history.php?deleted=1");
        exit;
    }
}

// ===== FETCH BOOKINGS =====
$stmt = $conn->prepare("
    SELECT b.*, m.title 
    FROM bookings b 
    JOIN movies m ON b.movie_id = m.id
    WHERE b.user_id = ? 
    ORDER BY b.id DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();

// ✅ अब मात्र header include
require 'includes/header.php';
?>

<style>
    /* RESET & GLOBAL */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(rgba(26, 8, 8, 0.90), rgba(0, 0, 0, 0.95)),
                    url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: 'Inter', sans-serif;
        color: #f0f3fa;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
        flex: 1;
    }

    /* Hero Section */
    .hero {
        text-align: center;
        margin-bottom: 50px;
    }

    .hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffb347, #ff6b6b);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .hero p {
        font-size: 1.1rem;
        color: #ccc;
    }

    /* Stats Bar */
    .stats-bar {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 60px;
        padding: 12px 28px;
        margin-bottom: 45px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stats-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        font-weight: 500;
    }

    .stats-item span:first-child {
        font-size: 1.3rem;
    }

    /* Grid – exactly 3 cards per row */
    .bookings-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }

    /* Ticket Card */
    .booking-card {
        background: rgba(18, 22, 32, 0.85);
        backdrop-filter: blur(12px);
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        position: relative;
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.3);
    }

    /* Ticket notch effect */
    .booking-card::before,
    .booking-card::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: #0a0c10;
        border-radius: 50%;
        z-index: 1;
        top: 50%;
        transform: translateY(-50%);
    }

    .booking-card::before {
        left: -10px;
        box-shadow: inset -2px 0 0 rgba(255, 255, 255, 0.1);
    }

    .booking-card::after {
        right: -10px;
        box-shadow: inset 2px 0 0 rgba(255, 255, 255, 0.1);
    }

    .booking-card:hover {
        transform: translateY(-6px);
        border-color: #ffb347;
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5);
    }

    /* Card Header */
    .card-header {
        background: linear-gradient(120deg, rgba(255, 180, 71, 0.2), rgba(255, 107, 107, 0.2));
        padding: 18px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .movie-icon {
        font-size: 2rem;
    }

    .movie-title {
        font-size: 1.3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #fff, #ffb347);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        letter-spacing: -0.3px;
    }

    /* Card Body */
    .card-body {
        padding: 20px;
    }

    .info-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
        font-size: 0.9rem;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.05);
        padding-bottom: 10px;
    }

    .info-icon {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .info-text {
        flex: 1;
    }

    .info-text strong {
        color: #ffb347;
        font-weight: 600;
        display: block;
        margin-bottom: 4px;
    }

    .seats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
    }

    .seat-badge {
        background: rgba(255, 180, 71, 0.2);
        color: #ffb347;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .price {
        font-size: 1.4rem;
        font-weight: 800;
        color: #ffb347;
    }

    .booking-id {
        font-size: 0.7rem;
        color: #aaa;
        margin-top: 8px;
        text-align: right;
    }

    .status {
        display: inline-block;
        background: rgba(34, 197, 94, 0.2);
        color: #22c55e;
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 12px;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        margin-top: 18px;
        padding: 12px;
        background: linear-gradient(135deg, #ff6b6b, #ff8c42);
        border: none;
        border-radius: 40px;
        color: white;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .download-btn:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 70px 20px;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        border-radius: 40px;
    }

    .empty-state i {
        font-size: 4.5rem;
        margin-bottom: 20px;
        opacity: 0.7;
    }

    .empty-state h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #ccc;
        margin-bottom: 20px;
    }

    .btn-explore {
        display: inline-block;
        padding: 12px 28px;
        background: linear-gradient(135deg, #ff6b6b, #ff8c42);
        border-radius: 40px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-explore:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .bookings-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .container {
            margin: 20px auto;
            padding: 0 15px;
        }
        .hero h1 {
            font-size: 2rem;
        }
        .stats-bar {
            flex-direction: column;
            align-items: stretch;
            border-radius: 30px;
            gap: 12px;
        }
        .stats-item {
            justify-content: space-between;
        }
        .bookings-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .booking-card::before,
        .booking-card::after {
            display: none;
        }
    }
    .delete-btn {
    display: inline-flex;
    justify-content: center;
    width: 100%;
    margin-top: 10px;
    padding: 10px;
    background: linear-gradient(135deg, #ff4d4d, #cc0000);
    border-radius: 40px;
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.delete-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(255,0,0,0.4);
}
</style>

<main class="container">
    <div class="hero">
        <h1>🎟️ My Bookings</h1>
        <p>Your movie tickets – safe and ready to download</p>
    </div>

    <?php if ($bookings->num_rows === 0): ?>
        <div class="empty-state">
            <i>🎬</i>
            <h3>No bookings yet</h3>
            <p>Book your first movie ticket now and enjoy the experience!</p>
            <a href="index.php" class="btn-explore">Browse Movies</a>
        </div>
    <?php else: ?>
        <div class="stats-bar">
            <div class="stats-item">
                <span>🎫</span>
                <span><?= $bookings->num_rows ?> <?= $bookings->num_rows == 1 ? 'Booking' : 'Bookings' ?></span>
            </div>
            <div class="stats-item">
                <span>⭐</span>
                <span>All confirmed</span>
            </div>
            <div class="stats-item">
                <span>📧</span>
                <span>Ticket ready</span>
            </div>
        </div>

        <div class="bookings-grid">
            <?php while ($row = $bookings->fetch_assoc()): ?>
                <?php
                $seats_array = json_decode($row['seats'], true);
                $seats = implode(', ', $seats_array);

                // Generate PDF
                $mpdf = new Mpdf(['tempDir' => __DIR__ . '/tmp']);
                $billHTML = "
                <div style='font-family: \"Inter\", sans-serif; max-width: 500px; margin: auto; padding: 20px; background: #fff; border-radius: 24px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <img src='imgs/40b3a7667c57b37bb66735d67609798e-modified.png' style='width: 60px;' alt='Logo'>
                        <h2 style='margin: 8px 0 0; color: #1a2a3a;'>CineMa Ghar</h2>
                        <p style='color: #666;'>Official Ticket</p>
                    </div>
                    <hr>
                    <p><strong>🎬 Movie:</strong> {$row['title']}</p>
                    <p><strong>👤 Name:</strong> {$row['customer_name']}</p>
                    <p><strong>📧 Email:</strong> {$row['customer_email']}</p>
                    <p><strong>💺 Seats:</strong> {$seats}</p>
                    <p><strong>💰 Total:</strong> Rs {$row['total_price']}</p>
                    <p><strong>⏰ Showtime:</strong> " . date('M j, Y H:i', strtotime($row['showtime'])) . "</p>
                    <hr>
                    <p style='text-align: center; font-size: 12px; color: #888;'>Thank you for choosing CineMa Ghar ❤️</p>
                </div>
                ";
                $mpdf->WriteHTML($billHTML);
                $pdfFileName = 'ticket_' . $row['id'] . '.pdf';
                $pdfContent = $mpdf->Output($pdfFileName, 'S');
                ?>
                <div class="booking-card">
                    <div class="card-header">
                        <div class="movie-icon">🎬</div>
                        <div class="movie-title"><?= htmlspecialchars($row['title']) ?></div>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-icon">💺</div>
                            <div class="info-text">
                                <strong>Seats</strong>
                                <div class="seats">
                                    <?php foreach ($seats_array as $seat): ?>
                                        <span class="seat-badge"><?= htmlspecialchars($seat) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-icon">⏰</div>
                            <div class="info-text">
                                <strong>Showtime</strong>
                                <?= date('M j, Y H:i', strtotime($row['showtime'])) ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-icon">💰</div>
                            <div class="info-text">
                                <strong>Total</strong>
                                <span class="price">Rs <?= number_format($row['total_price'], 2) ?></span>
                            </div>
                        </div>
                        <div class="booking-id">Booking ID: #<?= $row['id'] ?></div>
                        <div class="status">✅ Confirmed</div>
                        <a class="download-btn" href="data:application/pdf;base64,<?= base64_encode($pdfContent) ?>" download="<?= $pdfFileName ?>">
                            ⬇ Download Ticket
                        </a>
                        <a class="delete-btn"
   href="user_request.php?request_id=<?= $row['id'] ?>">
   🗑 Cancel Movie Book
</a>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>

<?php require 'includes/footer.php'; ?>