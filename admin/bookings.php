<?php
require_once '../includes/db.php';
// session_start();  

$conn = db_connect();
$msg = '';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Delete booking
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM bookings WHERE id=$id");
    $msg = "🗑 Booking deleted successfully.";
}

// Fetch all bookings
$bookings = $conn->query("
    SELECT b.id, m.title as movie, sc.screen_name, s.show_time, b.customer_name, b.customer_email, b.price, b.seats
    FROM bookings b
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    ORDER BY s.show_time DESC
");

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="/movie-booking/admin/assets/css/admin.css">
<title>Manage Bookings</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
.admin-container {padding:20px;}
.card {background:#fff; padding:20px; border-radius:10px; margin-bottom:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1);}
table {width:100%; border-collapse:collapse;}
table th, table td {padding:8px; border:1px solid #ddd; text-align:left;}
.msg {background:#d1fae5; padding:10px; border-radius:6px; margin-bottom:12px; color:#065f46;}
a.delete-link {color:#b91c1c; text-decoration:none;}
a.delete-link:hover {text-decoration:underline;}
</style>
</head>
<body>
<nav class="admin-nav">
  <a href="dashboard.php">Dashboard</a> |
  <a href="movies.php">Movies</a> |
  <a href="screens.php">Screens</a> |
  <a href="shows.php">Shows</a> |
  <a href="bookings.php">Bookings</a>
</nav>

<div class="admin-container">
  <h2>📋 Manage Bookings</h2>
  <?php if($msg) echo "<p class='msg'>$msg</p>"; ?>

  <div class="card">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Movie</th>
          <th>Screen</th>
          <th>Show Time</th>
          <th>Seats</th>
          <th>Customer Name</th>
          <th>Email</th>
          <th>Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if($bookings && $bookings->num_rows > 0): ?>
          <?php while($row = $bookings->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['movie'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['screen_name'] ?? '') ?></td>
              <td><?= date('M j, Y H:i', strtotime($row['show_time'] ?? '')) ?></td>
              <td>
                <?php 
                // Seats JSON decode  This code is used to decode the JSON-encoded 
                $seats = json_decode($row['seats'], true);
                echo $seats ? implode(', ', $seats) : '-';
                ?>
              </td>
              <td><?= htmlspecialchars($row['customer_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['customer_email'] ?? '') ?></td>
              <td>Rs <?= $row['price'] ?? '0' ?></td>
              <td><a class="delete-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this booking?')">Delete</a></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9">No bookings found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
