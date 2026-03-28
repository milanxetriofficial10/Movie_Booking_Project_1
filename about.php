<?php
// this is a about page milan & tej
require_once 'includes/db.php';
require_once 'includes/header.php';

$conn = db_connect();

// cinema ghar 
$cinema_name = 'CineMa Ghar';
$logo = 'imgs/40b3a7667c57b37bb66735d67609798e-modified.png'; // <-- put your image link here
$description = 'Welcome to CineMa Ghar – where stories come alive on the big screen!';

// fetch dynamic 
$movies_count = $conn->query("SELECT COUNT(*) as total FROM movies")->fetch_assoc()['total'] ?? 0;
$bookings_count = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'] ?? 0;
$users_count = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'] ?? 0;
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(rgba(26, 8, 8, 0.90), rgba(0, 0, 0, 0.95)),
                    url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #fff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .about-main {
        flex: 1;
        padding: 60px 20px;
    }

    /* Hero Section - Two Columns */
    .hero {
        max-width: 1400px;
        margin: 0 auto 80px;
        display: flex;
        align-items: center;
        gap: 60px;
        flex-wrap: wrap;
    }

    .hero-left {
        flex: 1;
        min-width: 280px;
        text-align: center;
        position: relative;
    }

    .logo-wrapper {
        position: relative;
        display: inline-block;
    }

    .logo-wrapper img {
        width: 280px;
        height: 280px;
        object-fit: cover;
        border-radius: 90%;
        border: 5px solid rgba(255, 180, 71, 0.5);
        box-shadow: 0 0 40px rgba(255, 180, 71, 0.3);
        transition: transform 0.3s ease;
    }

    .logo-wrapper:hover img {
        transform: scale(1.02);
    }

    /* Decorative rings */
    .logo-wrapper::before,
    .logo-wrapper::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(255, 180, 71, 0.3);
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        animation: pulseRing 2s infinite;
    }

    .logo-wrapper::before {
        width: 320px;
        height: 320px;
        animation-delay: 0s;
    }

    .logo-wrapper::after {
        width: 360px;
        height: 360px;
        animation-delay: 0.5s;
    }

    @keyframes pulseRing {
        0% {
            opacity: 0.5;
            transform: translate(-50%, -50%) scale(0.9);
        }
        100% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(1.2);
        }
    }

    /* Decorative lines */
    .hero-left .line {
        position: absolute;
        background: linear-gradient(90deg, #ffb347, transparent);
        height: 2px;
        width: 100px;
        left: 50%;
        transform: translateX(-50%);
    }

    .hero-left .line-top {
        top: -30px;
    }

    .hero-left .line-bottom {
        bottom: -30px;
    }

    .hero-right {
        flex: 1.5;
        min-width: 300px;
    }

    .hero-right h1 {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #ffb347, #ff6b6b);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .hero-right h1::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, #ffb347, #ff6b6b);
        border-radius: 3px;
    }

    .hero-right p {
        font-size: 1.1rem;
        line-height: 1.8;
        margin: 25px 0 30px;
        color: #e0e0e0;
    }

    /* Stats inside hero-right */
    .hero-stats {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
        margin: 30px 0 20px;
    }

    .stat-item {
        text-align: center;
        min-width: 100px;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 800;
        color: #ffb347;
    }

    .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ccc;
    }

    /* Features Section */
    .features-section {
        max-width: 1400px;
        margin: 80px auto;
    }

    .section-title {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title h2 {
        font-size: 2.2rem;
        font-weight: 700;
        position: relative;
        display: inline-block;
    }

    .section-title h2::before,
    .section-title h2::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, #ffb347, #ff6b6b);
    }

    .section-title h2::before {
        left: -80px;
    }

    .section-title h2::after {
        right: -80px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .feature-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 28px;
        padding: 30px 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .feature-card:hover {
        transform: translateY(-8px);
        border-color: #ffb347;
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5);
    }

    .feature-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        color: #ffb347;
    }

    .feature-card h3 {
        font-size: 1.4rem;
        margin-bottom: 12px;
    }

    .feature-card p {
        font-size: 0.95rem;
        line-height: 1.5;
        color: #ddd;
    }

    /* CTA Section */
    .cta-section {
        max-width: 1000px;
        margin: 80px auto 40px;
        text-align: center;
        background: linear-gradient(120deg, rgba(255, 107, 107, 0.2), rgba(255, 180, 71, 0.2));
        backdrop-filter: blur(10px);
        border-radius: 60px;
        padding: 60px 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .cta-section h2 {
        font-size: 2.2rem;
        margin-bottom: 15px;
    }

    .cta-section p {
        font-size: 1.1rem;
        margin-bottom: 30px;
        color: #ddd;
    }

    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .btn-primary, .btn-secondary {
        display: inline-block;
        padding: 12px 32px;
        border-radius: 40px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #ff6b6b, #ff8c42);
        color: white;
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 107, 107, 0.5);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .hero {
            gap: 40px;
        }
        .hero-left img {
            width: 220px;
            height: 220px;
        }
        .hero-right h1 {
            font-size: 2.8rem;
        }
        .section-title h2::before,
        .section-title h2::after {
            width: 40px;
        }
        .section-title h2::before {
            left: -60px;
        }
        .section-title h2::after {
            right: -60px;
        }
    }

    @media (max-width: 768px) {
        .hero {
            flex-direction: column;
            text-align: center;
        }
        .hero-right h1::after {
            left: 50%;
            transform: translateX(-50%);
        }
        .hero-stats {
            justify-content: center;
        }
        .hero-left .line {
            display: none;
        }
        .section-title h2::before,
        .section-title h2::after {
            display: none;
        }
        .cta-section {
            padding: 40px 20px;
        }
        .cta-section h2 {
            font-size: 1.8rem;
        }
    }
</style>
<main class="about-main">
    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-left">
            <div class="logo-wrapper">
                <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($cinema_name) ?>">
                <div class="line line-top"></div>
                <div class="line line-bottom"></div>
            </div>
        </div>
        <div class="hero-right">
            <h1><?= htmlspecialchars($cinema_name) ?></h1>
            <p><?= nl2br(htmlspecialchars($description)) ?></p>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number"><?= $movies_count ?></div>
                    <div class="stat-label">Movies</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $bookings_count ?></div>
                    <div class="stat-label">Bookings</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $users_count ?></div>
                    <div class="stat-label">Happy Users</div>
                </div>
            </div>
        </div>
    </div>



    <!-- Features Section -->
    <div class="features-section">
        <div class="section-title">
            <h2>Why Choose Us?</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-film"></i></div>
                <h3>Latest Movies</h3>
                <p>Get the newest blockbusters and classic favorites in stunning HD quality.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chair"></i></div>
                <h3>Smart Seat Selection</h3>
                <p>Pick your perfect spot with our interactive seat map – real-time availability.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-credit-card"></i></div>
                <h3>Secure Payments</h3>
                <p>Multiple payment options with bank‑level security. Instant confirmation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-history"></i></div>
                <h3>Booking History</h3>
                <p>View and download your past e‑tickets anytime, anywhere.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-clock"></i></div>
                <h3>Flexible Showtimes</h3>
                <p>Morning, afternoon, and evening shows to fit your schedule.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-ticket-alt"></i></div>
                <h3>Easy Booking</h3>
                <p>Just a few clicks to reserve your seat – no hassle, no queues.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-envelope"></i></div>
                <h3>E‑Tickets Delivered</h3>
                <p>Receive your tickets via email and QR code for quick entry.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-popcorn"></i></div>
                <h3>Snack Combos</h3>
                <p>Add popcorn and drinks to your booking for a complete experience.</p>
            </div>
        </div>
    </div>



    <!-- Call to Action -->
    <div class="cta-section">
        <h2>Ready for the big screen?</h2>
        <p>Book your tickets now and enjoy the ultimate cinema experience.</p>
        <div class="cta-buttons">
            <a href="movies.php" class="btn-primary">🎬 Book Now</a>
            <a href="contact.php" class="btn-secondary">📞 Contact Us</a>
        </div>
    </div>
</main>



<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php require_once 'includes/footer.php'; ?>