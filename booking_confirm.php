<?php
require 'includes/db.php';
require 'includes/header.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client as TwilioClient;
use Mpdf\Mpdf;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p>Invalid request.</p>");
}

// Get POST data
$show_id = (int)($_POST['show_id'] ?? 0);
$user_name = trim($_POST['user_name'] ?? '');
$user_email = trim($_POST['user_email'] ?? '');
$user_mobile = trim($_POST['user_mobile'] ?? '');
$seats = json_decode($_POST['seats'] ?? '[]', true);

if (!$show_id || !$user_name || !$user_email || !$user_mobile || !is_array($seats) || count($seats) == 0) {
    die("<p>All fields and at least one seat are required.</p>");
}

// Fetch show info
$show_stmt = $conn->prepare("SELECT price, show_time, movie_id FROM shows WHERE id=?");
$show_stmt->bind_param('i', $show_id);
$show_stmt->execute();
$show_res = $show_stmt->get_result();
$show = $show_res->fetch_assoc();
if (!$show) die("<p>Show not found.</p>");

$movie_id = (int)$show['movie_id'];
$price = (float)$show['price'];
$showtime = $show['show_time'];
$total_price = $price * count($seats);
$seats_json = json_encode($seats);

// Check already booked seats
$book_stmt = $conn->prepare("SELECT seats FROM bookings WHERE show_id=?");
$book_stmt->bind_param('i', $show_id);
$book_stmt->execute();
$res = $book_stmt->get_result();
$booked = [];
while ($r = $res->fetch_assoc()) {
    $arr = json_decode($r['seats'], true);
    if (is_array($arr)) $booked = array_merge($booked, $arr);
}
$booked = array_unique($booked);

foreach ($seats as $seat) {
    if (in_array($seat, $booked)) die("<p>Seat $seat is already booked!</p>");
}

$user_id = null;

// Insert booking
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
    die("<p>Error inserting booking: " . $insert_stmt->error . "</p>");
}

// ===== Generate PDF using mPDF =====
$mpdf = new Mpdf();

$billHTML = "
<div style='font-family:sans-serif; max-width:400px; margin:auto; padding:20px; border:1px solid #ddd; border-radius:10px;'>
    <div style='text-align:center;'>
        <img src='imgs/e32e183fd326fd5cd49ab3df467e54a8.jpg' style='width:80px;' alt='CineMa Ghar'>
        <h2>CineMa Ghar</h2>
        <p>Booking Receipt</p>
    </div>
    <div style='background:#f1f8ff; padding:10px; border-left:4px solid #3498db; border-radius:6px; margin-top:10px;'>
        <p><strong>Name:</strong> {$user_name}</p>
        <p><strong>Email:</strong> {$user_email}</p>
        <p><strong>Mobile:</strong> {$user_mobile}</p>
        <p><strong>Seats:</strong> " . implode(', ', $seats) . "</p>
        <p><strong>Price per Seat:</strong> Rs {$price}</p>
        <p><strong>Total Price:</strong> Rs {$total_price}</p>
        <p><strong>Show Time:</strong> " . date('M j, Y H:i', strtotime($showtime)) . "</p>
    </div>
    <p style='margin-top:10px; font-weight:bold;'>Thank you for booking with CineMa Ghar!</p>
</div>
";

$mpdf->WriteHTML($billHTML);
$pdfFileName = 'bill_' . time() . '.pdf';
$pdfContent = $mpdf->Output($pdfFileName, 'S'); // 'S' = return as string

// ===== Send Email with PDF attachment =====
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'cinemaghar80@gmail.com';
    $mail->Password   = 'kidu xsrn fvpa uxwe';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('cinemaghar80@gmail.com', 'CineMa Ghar');
    $mail->addAddress($user_email, $user_name);

    $mail->isHTML(true);
    $mail->Subject = "Your CineMa Ghar Booking Receipt";

    $mail->Body = "Dear {$user_name},<br><br>
        Your booking is confirmed. Please find the attached bill.<br>
        Thank you for booking with CineMa Ghar!";

    $mail->addStringAttachment($pdfContent, $pdfFileName, 'base64', 'application/pdf');

    $mail->send();
} catch (Exception $e) {
    echo "<p>Email could not be sent: {$mail->ErrorInfo}</p>";
}

// ===== Twilio SMS =====
$sid = 'AC0e4c68c6dc1c009662f313dfab49cf41';
$token = 'cd994a2619109ab79e49cf615e6338c4';
$twilio_number = '+12023357259';

if (strpos($user_mobile, '+977') !== 0) {
    $user_mobile = '+977' . preg_replace('/^0/', '', $user_mobile);
}

$client = new TwilioClient($sid, $token);
$sms_body = "Booking Confirmed: Seats " . implode(', ', $seats) . " | Total Rs $total_price | Show: " . date('M j, Y H:i', strtotime($showtime));
try {
    $client->messages->create(
        $user_mobile,
        [
            'from' => $twilio_number,
            'body' => $sms_body
        ]
    );
} catch (Exception $e) {
    echo "<p>SMS could not be sent: " . $e->getMessage() . "</p>";
}

// ===== Show confirmation page with download link =====
echo "<div style='max-width:500px;margin:50px auto;text-align:center;font-family:sans-serif;'>";
echo "<h2>Booking Confirmed ✅</h2>";
echo "<p>Dear {$user_name}, your booking is successful.</p>";
echo "<a href='data:application/pdf;base64,".base64_encode($pdfContent)."' download='{$pdfFileName}' style='display:inline-block;padding:10px 20px;background:#27ae60;color:#fff;border-radius:6px;text-decoration:none;'>Download Your Bill</a>";
echo "<br><br><a href='index.php' style='display:inline-block;padding:10px 20px;background:#3498db;color:#fff;border-radius:6px;text-decoration:none;'>Back to Movies</a>";
echo "</div>";
?>

<?php require 'includes/footer.php'; ?>