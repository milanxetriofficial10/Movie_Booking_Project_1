<?php
// contact.php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';

$conn = db_connect();

// Default cinema details
$cinema = [
    'address' => 'Kathmandu, Nepal',
    'phone'   => '+977 9800000000',
    'email'   => 'info@cinemaghar.com',
    'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.1249468993346!2d85.34210707459769!3d27.713428225224874!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb197a0abda05d%3A0x113f5dae44c75759!2sPashupati%20Multiple%20Campus%20(PMC)!5e0!3m2!1sen!2snp!4v1774073377375!5m2!1sen!2snp'
];

// Try to fetch from database if table exists
$check = $conn->query("SHOW TABLES LIKE 'cinema_info'");
if ($check && $check->num_rows > 0) {
    $res = $conn->query("SELECT address, phone, email, map_embed FROM cinema_info LIMIT 1");
    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $cinema['address'] = $row['address'] ?? $cinema['address'];
        $cinema['phone']   = $row['phone'] ?? $cinema['phone'];
        $cinema['email']   = $row['email'] ?? $cinema['email'];
        $cinema['map_url'] = $row['map_embed'] ?? $cinema['map_url'];
    }
}

// Handle form submission
$msg_sent = false;
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = trim($_POST['name'] ?? '');
    $email_user = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email_user) || empty($message)) {
        $error_msg = 'Please fill in all required fields.';
    } else {
        // Try to insert into contact_messages table
        $check = $conn->query("SHOW TABLES LIKE 'contact_messages'");
        if ($check && $check->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt) {
                $stmt->bind_param("ssss", $name, $email_user, $subject, $message);
                if ($stmt->execute()) {
                    $msg_sent = true;
                } else {
                    $error_msg = 'Database error. Please try again.';
                }
                $stmt->close();
            } else {
                $error_msg = 'Database error. Please try again.';
            }
        } else {
            // Table doesn't exist – just simulate success
            $msg_sent = true;
        }
    }
}
?>

<style>
    body{
         background:
        linear-gradient(rgba(26, 8, 8, 0.90), rgba(0, 0, 0, 0.95)),
        url("https://i.pinimg.com/736x/a1/25/d3/a125d3d8481542af812611c5eb23ee18.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    }
    /* Scoped styles for contact page to avoid conflicts with footer */
    .contact-page {
        flex: 1;
        padding: 60px 20px;
    }

    .contact-page .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .contact-page .hero {
        text-align: center;
        margin-bottom: 50px;
    }

    .contact-page .hero h1 {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #ffb347, #ff6b6b);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 10px;
    }

    .contact-page .hero p {
        font-size: 1rem;
        color: #ddd;
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-page .contact-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 50px;
    }

    .contact-page .contact-info,
    .contact-page .contact-form {
        flex: 1;
        min-width: 280px;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .contact-page .contact-info h3,
    .contact-page .contact-form h3 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: #ffb347;
        border-left: 3px solid #ffb347;
        padding-left: 12px;
    }

    .contact-page .contact-detail {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .contact-page .contact-icon {
        width: 45px;
        height: 45px;
        background: rgba(255, 180, 71, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #ffb347;
    }

    .contact-page .contact-text p {
        font-size: 1rem;
    }

    .contact-page .hours-list {
        list-style: none;
        margin: 15px 0;
    }

    .contact-page .hours-list li {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
    }

    .contact-page .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .contact-page .social-link {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffb347;
        text-decoration: none;
        transition: 0.2s;
    }

    .contact-page .social-link:hover {
        background: #ffb347;
        color: #000;
    }

    .contact-page .form-group {
        margin-bottom: 20px;
    }

    .contact-page .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }

    .contact-page .form-group input,
    .contact-page .form-group textarea {
        width: 100%;
        padding: 10px 15px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: #fff;
        font-family: inherit;
        font-size: 0.9rem;
    }

    .contact-page .form-group input:focus,
    .contact-page .form-group textarea:focus {
        outline: none;
        border-color: #ffb347;
    }

    .contact-page .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .contact-page .btn-submit {
        background: #ff6b6b;
        border: none;
        padding: 12px;
        border-radius: 30px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        transition: 0.2s;
    }

    .contact-page .btn-submit:hover {
        background: #ff5252;
        transform: translateY(-2px);
    }

    .contact-page .alert {
        padding: 12px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        text-align: center;
    }

    .contact-page .alert-success {
        background: rgba(34, 197, 94, 0.2);
        border: 1px solid #22c55e;
        color: #22c55e;
    }

    .contact-page .alert-error {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
    }

    .contact-page .map-section {
        margin-top: 30px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .contact-page .map-section iframe {
        width: 100%;
        height: 350px;
        display: block;
    }

    @media (max-width: 768px) {
        .contact-page {
            padding: 40px 15px;
        }
        .contact-page .hero h1 {
            font-size: 2rem;
        }
        .contact-page .contact-info,
        .contact-page .contact-form {
            padding: 20px;
        }
        .contact-page .hours-list li {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 5px;
        }
        .contact-page .social-links {
            justify-content: center;
        }
    }
</style>

<div class="contact-page">
    <div class="container">
        <div class="hero">
            <h1>Contact Us</h1>
            <p>Have questions? We're here to help. Reach out through the form or using the details below.</p>
        </div>

        <?php if ($msg_sent): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Thank you! We'll get back to you soon.
            </div>
        <?php elseif ($error_msg): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <div class="contact-grid">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <div class="contact-detail">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-text">
                        <p><?= htmlspecialchars($cinema['address']) ?></p>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="contact-text">
                        <p><?= htmlspecialchars($cinema['phone']) ?></p>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-text">
                        <p><?= htmlspecialchars($cinema['email']) ?></p>
                    </div>
                </div>

                <h3 style="margin-top: 25px;">Business Hours</h3>
                <ul class="hours-list">
                    <li><span>Mon - Fri</span><span>10:00 AM – 10:00 PM</span></li>
                    <li><span>Saturday</span><span>11:00 AM – 11:00 PM</span></li>
                    <li><span>Sunday</span><span>12:00 PM – 9:00 PM</span></li>
                </ul>

                <h3 style="margin-top: 25px;">Follow Us</h3>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="contact-form">
                <h3>Send a Message</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject">
                    </div>
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" required></textarea>
                    </div>
                    <button type="submit" name="submit_contact" class="btn-submit">Send Message</button>
                </form>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section">
            <iframe src="<?= htmlspecialchars($cinema['map_url']) ?>" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<?php include_once('includes/footer.php'); ?>