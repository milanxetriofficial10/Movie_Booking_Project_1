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

// Fetch all bookings
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
<title>CineMa Ghar - Manage Bookings</title>
<link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:Arial,sans-serif;}
body{background:#f5f7fb;color:#333;}

/* WRAPPER */
.admin-wrapper{display:flex;}

/* SIDEBAR */
.sidebar{
  width:220px;
  height:100vh;
  background:#111827;
  color:#fff;
  padding-top:20px;
  position:fixed;
  transition:.3s;
}
.sidebar.hide{transform:translateX(-100%);}
.sidebar h2{text-align:center;margin-bottom:30px;color:#facc15;}
.sidebar a{display:block;padding:12px 20px;color:#cbd5e1;text-decoration:none;transition:.3s;}
.sidebar a:hover{background:#1e293b;color:#fff;padding-left:25px;}

/* MAIN */
.main{margin-left:220px;padding:20px;transition:.3s;}
.main.full{margin-left:0;}

/* TOPBAR */
.topbar{display:flex;align-items:center;gap:10px;margin-bottom:20px;}
.menu-btn{font-size:22px;background:#111827;color:#fff;padding:8px 12px;cursor:pointer;border-radius:6px;}

/* CARD */
.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.card h2{margin-bottom:15px;}
.msg{background:#d1fae5;padding:12px 16px;border-left:5px solid #10b981;border-radius:6px;font-weight:500;margin-bottom:20px;color:#065f46;}

/* TABLE */
table{width:100%;border-collapse:collapse;margin-top:10px;}
table th{background:#2563eb;color:#fff;text-align:left;padding:12px;font-size:15px;}
table td{padding:10px 12px;background:#fff;border-bottom:1px solid #e5e7eb;font-size:15px;}
table tr:hover td{background:#f1f5f9;}

/* DELETE BUTTON */
a.delete-link{background:#dc2626;color:white !important;padding:6px 12px;border-radius:6px;text-decoration:none;font-size:14px;transition:.2s;}
a.delete-link:hover{background:#b91c1c;}

/* SEATS DISPLAY */
td span.seat-pill{background:#e2e8f0;padding:5px 10px;border-radius:12px;margin-right:5px;display:inline-block;font-size:13px;color:#1e293b;}

/* RESPONSIVE */
@media(max-width:768px){
    table th, table td{font-size:13px;padding:8px;}
    h2{font-size:22px;}
}
</style>
</head>
<body>

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <h2>🎬 Admin</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="movies.php">Add Movies</a>
    <a href="slider.php">Slider</a>
    <a href="screens.php">Screens</a>
    <a href="shows.php">Shows</a>
    <a href="bookings.php">Bookings</a>
    <a href="top_news.php">Top News</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>

<!-- MAIN -->
<div class="main" id="main">
    <div class="topbar">
        <div class="menu-btn" onclick="toggleMenu()">☰</div>
        <h2>📋 Manage Bookings</h2>
    </div>

    <?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

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
                    <th>Booked At</th>
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
                            <td><?= date('M j, Y h:i A', strtotime($row['created_at'])) ?></td>
                            <td>
                                <a class="delete-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this booking?')">Delete</a>
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

<script>
function toggleMenu(){
    document.getElementById("sidebar").classList.toggle("hide");
    document.getElementById("main").classList.toggle("full");
}
</script>
</body>
</html>