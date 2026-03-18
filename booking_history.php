<?php
session_start();
require 'includes/db.php';
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$conn = db_connect();
$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.*, m.title 
    FROM bookings b 
    JOIN movies m ON b.movie_id = m.id
    WHERE b.user_id=? 
    ORDER BY b.id DESC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Booking History - CineMa Ghar</title>
<style>
body{font-family:sans-serif;background:#0d1117;color:white;}
.container{max-width:900px;margin:50px auto;}
table{width:100%;border-collapse:collapse;}
th,td{padding:10px;border:1px solid #ccc;}
th{background:#3498db;color:white;}
a.download{padding:5px 10px;background:#27ae60;color:white;text-decoration:none;border-radius:5px;}
</style>
</head>
<body>

<div class="container">
<h2>🎬 Booking History</h2>

<?php if($bookings->num_rows === 0): ?>
    <p>No bookings found.</p>
<?php else: ?>
<table>
<tr>
    <th>Movie</th>
    <th>Seats</th>
    <th>Showtime</th>
    <th>Total Price</th>
    <th>Ticket</th>
</tr>
<?php while($row = $bookings->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td><?= implode(', ', json_decode($row['seats'], true)) ?></td>
    <td><?= date('M j, Y H:i', strtotime($row['showtime'])) ?></td>
    <td>Rs <?= $row['total_price'] ?></td>
    <td>
        <?php
        $mpdf = new Mpdf(['tempDir'=>__DIR__.'/tmp']);
        $billHTML = "<h2>CineMa Ghar Ticket</h2>
        <p><strong>Name:</strong> {$row['customer_name']}</p>
        <p><strong>Email:</strong> {$row['customer_email']}</p>
        <p><strong>Movie:</strong> {$row['title']}</p>
        <p><strong>Seats:</strong> ".implode(', ', json_decode($row['seats'], true))."</p>
        <p><strong>Total:</strong> Rs {$row['total_price']}</p>
        <p><strong>Showtime:</strong> ".date('M j, Y H:i', strtotime($row['showtime']))."</p>";
        $mpdf->WriteHTML($billHTML);
        $pdfFileName = 'bill_'.$row['id'].'.pdf';
        $pdfContent = $mpdf->Output($pdfFileName, 'S');
        ?>
        <a class="download" href="data:application/pdf;base64,<?= base64_encode($pdfContent) ?>" download="<?= $pdfFileName ?>">Download</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>
</div>

</body>
</html>