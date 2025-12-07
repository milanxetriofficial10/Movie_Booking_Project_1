<?php
require_once '../includes/db.php';

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

// Fetch all bookings (added b.created_at)
$bookings = $conn->query("
    SELECT 
        b.id, 
        m.title AS movie, 
        sc.screen_name, 
        s.show_time, 
        b.customer_name, 
        b.customer_email, 
        b.price, 
        b.seats,
        b.created_at
    FROM bookings b
    JOIN shows s ON b.show_id = s.id
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    ORDER BY b.created_at DESC
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
          <th>Customer</th>
          <th>Email</th>
          <th>Price</th>
          <th>Booked At</th>    <!-- NEW COLUMN -->
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php if($bookings && $bookings->num_rows > 0): ?>
          <?php while($row = $bookings->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['movie']) ?></td>
              <td><?= htmlspecialchars($row['screen_name']) ?></td>
              <td><?= date('M j, Y h:i A', strtotime($row['show_time'])) ?></td>

              <td>
                <?php 
                $seats = json_decode($row['seats'], true);
                echo $seats ? implode(', ', $seats) : '-';
                ?>
              </td>

              <td><?= htmlspecialchars($row['customer_name']) ?></td>
              <td><?= htmlspecialchars($row['customer_email']) ?></td>

              <td>Rs <?= $row['price'] ?></td>

              <!-- NEW BOOKED TIME -->
              <td><?= date('M j, Y h:i A', strtotime($row['created_at'])) ?></td>

              <td>
                <a class="delete-link" 
                   href="?delete=<?= $row['id'] ?>" 
                   onclick="return confirm('Delete this booking?')">
                   Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10">No bookings found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
<style>
  /* ------------------------------
   GLOBAL LAYOUT & BACKGROUND
--------------------------------*/
body {
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background: #f5f7fb;
    color: #333;
}

/* ------------------------------
   NAVIGATION BAR
--------------------------------*/
.admin-nav {
    background: #1e293b;
    padding: 14px 20px;
    display: flex;
    gap: 20px;
    border-bottom: 2px solid #0f172a;
}

.admin-nav a {
    color: #e2e8f0;
    text-decoration: none;
    font-weight: 500;
    transition: 0.2s;
}

.admin-nav a:hover {
    color: #38bdf8;
}

/* ------------------------------
   PAGE CONTAINER
--------------------------------*/
.admin-container {
    padding: 30px;
}

h2 {
    margin-bottom: 20px;
    font-size: 26px;
    font-weight: 600;
    color: #1e293b;
}

/* ------------------------------
   CARD BOX
--------------------------------*/
.card {
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0px 4px 18px rgba(0,0,0,0.07);
    margin-top: 20px;
}

/* ------------------------------
   SUCCESS MSG BOX
--------------------------------*/
.msg {
    background: #d1fae5;
    padding: 12px 16px;
    border-left: 5px solid #10b981;
    border-radius: 6px;
    font-weight: 500;
    margin-bottom: 20px;
    color: #065f46;
}

/* ------------------------------
   TABLE DESIGN
--------------------------------*/
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th {
    background: #1e293b;
    color: #e2e8f0;
    text-align: left;
    padding: 12px;
    font-size: 15px;
    letter-spacing: 0.3px;
}

table td {
    padding: 10px 12px;
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    font-size: 15px;
}

/* Row hover */
table tr:hover td {
    background: #f1f5f9;
}

/* ------------------------------
   SEAT BADGE STYLE
--------------------------------*/
td span.seat-pill {
    background: #e2e8f0;
    padding: 5px 10px;
    border-radius: 12px;
    margin-right: 5px;
    display: inline-block;
    font-size: 13px;
    color: #1e293b;
}

/* ------------------------------
   DELETE BUTTON
--------------------------------*/
a.delete-link {
    background: #dc2626;
    color: white !important;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.2s;
}

a.delete-link:hover {
    background: #b91c1c;
}

/* ------------------------------
   RESPONSIVE TABLE
--------------------------------*/
@media (max-width: 768px) {
    table th, table td {
        font-size: 13px;
        padding: 8px;
    }

    h2 {
        font-size: 22px;
    }
}

</style>