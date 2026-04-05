<?php
session_start();
require 'includes/db.php';

$conn = db_connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = (int)($_GET['request_id'] ?? 0);

// SUBMIT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO cancel_requests (booking_id, user_id, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $booking_id, $user_id, $reason);
    $stmt->execute();

    header("Location: booking_history.php?request_sent=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancel Request</title>
</head>
<body style="background:#111; color:white; text-align:center; padding:50px;">

<h2>❌ Cancel Booking Request</h2>

<form method="POST">
    <textarea name="reason" placeholder="Why do you want to cancel?" required
        style="width:300px; height:100px;"></textarea><br><br>

    <button type="submit" style="padding:10px 20px;">Submit Request</button>
</form>

</body>
</html>