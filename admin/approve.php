<?php
require '../includes/db.php';
$conn = db_connect();

$id = (int)$_GET['id'];

// status update
$conn->query("UPDATE cancel_requests SET status='approved' WHERE id=$id");

// booking delete (main logic)
$conn->query("
    DELETE b FROM bookings b
    JOIN cancel_requests cr ON b.id = cr.booking_id
    WHERE cr.id = $id
");

header("Location: cancel_requests.php");