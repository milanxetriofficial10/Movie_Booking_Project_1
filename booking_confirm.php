<?php
session_start();
require 'includes/db.php';
require 'includes/header.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p>Invalid request.</p>");
}

// ================= POST DATA =================
$show_id = (int)($_POST['show_id'] ?? 0);
$user_name = trim($_POST['user_name'] ?? '');
$user_email = trim($_POST['user_email'] ?? '');
$user_mobile = trim($_POST['user_mobile'] ?? '');
$seats = json_decode($_POST['seats'] ?? '[]', true);

if (!$show_id || !$user_name || !$user_email || !$user_mobile || !is_array($seats) || count($seats) == 0) {
    die("<p>All fields and at least one seat are required.</p>");
}

// ================= FETCH SHOW =================
$show_stmt = $conn->prepare("SELECT price, show_time, movie_id FROM shows WHERE id=?");
$show_stmt->bind_param('i', $show_id);
$show_stmt->execute();
$show = $show_stmt->get_result()->fetch_assoc();

if (!$show) die("<p>Show not found.</p>");

$movie_id   = (int)$show['movie_id'];
$price      = (float)$show['price'];
$showtime   = $show['show_time'];
$total_price = $price * count($seats);
$seats_json = json_encode($seats);

// ================= SEAT CHECK =================
$book_stmt = $conn->prepare("SELECT seats FROM bookings WHERE show_id=?");
$book_stmt->bind_param('i', $show_id);
$book_stmt->execute();
$res = $book_stmt->get_result();

$booked = [];
while ($r = $res->fetch_assoc()) {
    $arr = json_decode($r['seats'], true);
    if (is_array($arr)) $booked = array_merge($booked, $arr);
}

foreach ($seats as $seat) {
    if (in_array($seat, $booked)) {
        die("<p>Seat {$seat} is already booked!</p>");
    }
}

// ================= INSERT BOOKING =================
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$insert_stmt = $conn->prepare("
    INSERT INTO bookings 
    (movie_id, show_id, seats, customer_name, customer_email, customer_mobile, price, total_price, user_id, showtime)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$insert_stmt->bind_param(
    'iissssddis',
    $movie_id,
    $show_id,
    $seats_json,
    $user_name,
    $user_email,
    $user_mobile,
    $price,
    $total_price,
    $user_id,
    $showtime
);

if (!$insert_stmt->execute()) {
    die("<p>Booking failed.</p>");
}

// ================= PDF GENERATE =================
$mpdf = new Mpdf(['tempDir' => __DIR__ . '/tmp']);

$billHTML = "
<div style='font-family: DejaVu Sans, sans-serif; max-width:420px; margin:auto; padding:20px; border:1px solid #ddd; border-radius:12px;'>
    <div style='text-align:center;'>
        <img src='imgs/40b3a7667c57b37bb66735d67609798e-modified.png' 
             style='width:70px; margin-bottom:8px;' alt='CineMa Ghar Logo'>
        <h2 style='margin:0;'>CineMa Ghar</h2>
        <p style='margin:4px 0; color:#555;'>Movie Booking Receipt</p>
    </div>
    <div style='height:4px; background:#3498db; margin:15px 0; border-radius:4px;'></div>
    <table width='100%' cellpadding='6' cellspacing='0' style='font-size:13px;'>
        <tr><td><strong>Name</strong></td><td style='text-align:right;'>{$user_name}</td></tr>
        <tr style='background:#f7f7f7;'><td><strong>Email</strong></td><td style='text-align:right;'>{$user_email}</td></tr>
        <tr><td><strong>Mobile</strong></td><td style='text-align:right;'>{$user_mobile}</td></tr>
    </table>
    <div style='height:2px; background:#eee; margin:12px 0;'></div>
    <table width='100%' cellpadding='6' cellspacing='0' style='font-size:13px;'>
        <tr><td><strong>Seats</strong></td><td style='text-align:right;'>".implode(', ', $seats)."</td></tr>
        <tr style='background:#f7f7f7;'><td><strong>Price / Seat</strong></td><td style='text-align:right;'>Rs {$price}</td></tr>
        <tr><td><strong>Total Amount</strong></td><td style='text-align:right; font-weight:bold; color:#27ae60;'>Rs {$total_price}</td></tr>
        <tr style='background:#f7f7f7;'><td><strong>Show Time</strong></td><td style='text-align:right;'>".date('M j, Y H:i', strtotime($showtime))."</td></tr>
    </table>
    <div style='height:4px; background:#27ae60; margin:15px 0; border-radius:4px;'></div>
    <p style='text-align:center; font-size:12px; color:#555; margin:0;'>
        Thank you for booking with <strong>CineMa Ghar</strong> ❤️<br>
        Enjoy your movie 🍿
    </p>
</div>
";

$mpdf->WriteHTML($billHTML);
$pdfFileName = 'bill_' . time() . '.pdf';
$pdfContent = $mpdf->Output($pdfFileName, 'S');

// ================= EMAIL SEND =================
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'cinemaghar80@gmail.com';
    $mail->Password = 'vudr dciy kmtw aqao';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('cinemaghar80@gmail.com', 'CineMa Ghar');
    $mail->addAddress($user_email, $user_name);

    $mail->isHTML(true);
    $mail->Subject = "🎟️ Booking Confirmed - CineMa Ghar";
    $mail->Body = "Dear {$user_name},<br><br>Your movie booking is <b>successfully confirmed</b>.<br>Please find your ticket attached.<br><br>Enjoy the show! 🍿<br><b>CineMa Ghar</b>";
    $mail->addStringAttachment($pdfContent, $pdfFileName);
    $mail->send();
} catch (Exception $e) {
    error_log("Email failed: " . $mail->ErrorInfo);
}

// ================= CONFIRMATION PAGE =================
echo "
<div style='max-width:500px;margin:50px auto;text-align:center;font-family:sans-serif;'>
    <h2>✅ Booking Confirmed</h2>
    <p>Dear {$user_name}, your booking was successful.</p>
    <a href='data:application/pdf;base64,".base64_encode($pdfContent)."' download='{$pdfFileName}'
       style='padding:10px 20px;background:#27ae60;color:#fff;border-radius:6px;text-decoration:none;'>
       Download Bill
    </a>
    <br><br>
    <a href='index.php'
       style='padding:10px 20px;background:#3498db;color:#fff;border-radius:6px;text-decoration:none;'>
       Back to Movies
    </a>
</div>
";

// ================= UPDATE HEADER LINK IMMEDIATELY =================
$_SESSION['has_booking'] = true;

?>

<?php require 'includes/footer.php'; ?>