<?php
require '../includes/db.php';
$conn = db_connect();

$id = (int)$_GET['id'];

$conn->query("UPDATE cancel_requests SET status='rejected' WHERE id=$id");

header("Location: cancel_requests.php");