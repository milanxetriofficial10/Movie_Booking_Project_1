<?php
require '../includes/db.php';
$conn = db_connect();

$result = $conn->query("
    SELECT 
        cr.*, 
        b.id as booking_id,
        u.name as user_name,
        m.title as movie_title
    FROM cancel_requests cr
    JOIN bookings b ON cr.booking_id = b.id
    JOIN users u ON cr.user_id = u.id
    JOIN movies m ON b.movie_id = m.id
    ORDER BY cr.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin - Cancel Requests</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family: Arial, sans-serif;
    background:#0f172a;
    color:white;
}

/* SIDEBAR */
.sidebar{
    width:220px;
    height:100vh;
    background:#111827;
    color:#fff;
    padding-top:20px;
    position:fixed;
    left:0;
    top:0;
}

.sidebar h2{
    text-align:center;
    margin-bottom:30px;
    color:#facc15;
}

.sidebar a{
    display:block;
    padding:12px 20px;
    color:#cbd5e1;
    text-decoration:none;
    transition:0.3s;
}

.sidebar a:hover{
    background:#1e293b;
    color:#fff;
    padding-left:25px;
}

/* MAIN CONTENT */
.main{
    margin-left:220px; /* 🔥 important */
    padding:30px;
}

/* TABLE DESIGN */
h2{
    text-align:center;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#1e293b;
    border-radius:10px;
    overflow:hidden;
}

th, td{
    padding:12px;
    text-align:center;
}

th{
    background:#334155;
}

tr:nth-child(even){
    background:#0f172a;
}

/* STATUS */
.status{
    padding:5px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.pending{ background:orange; }
.approved{ background:green; }
.rejected{ background:red; }

/* BUTTONS */
a{
    text-decoration:none;
    padding:6px 10px;
    border-radius:5px;
    color:white;
    margin:2px;
    display:inline-block;
}

.approve{ background:green; }
.reject{ background:red; }

/* RESPONSIVE */
@media(max-width:768px){
    .sidebar{
        width:180px;
    }

    .main{
        margin-left:180px;
    }

    table{
        font-size:12px;
    }
}
</style>
</head>

<body>

<!-- sidebar -->
<div class="sidebar">
    <h2>🎬 Admin</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="movies.php">Add Movies</a>
    <a href="slider.php">Slider</a>
    <a href="screens.php">Screens</a>
    <a href="shows.php">Shows</a>
    <a href="bookings.php">Bookings</a>
    <a href="cancel_requests.php">Requests</a>
    <a href="top_news.php">Top News</a>
    <a href="logout.php">Logout</a>
</div>

<!-- main -->
<div class="main">

<h2>📩 Cancel Requests (Admin Panel)</h2>

<table>
<tr>
    <th>User Name</th>
    <th>Movie</th>
    <th>Booking ID</th>
    <th>Reason</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
    <td><?= htmlspecialchars($row['user_name'] ?? 'Unknown User') ?></td>

    <td>🎬 <?= htmlspecialchars($row['movie_title']) ?></td>

    <td>#<?= $row['booking_id'] ?></td>

    <td><?= htmlspecialchars($row['reason']) ?></td>

    <td>
        <span class="status <?= $row['status'] ?>">
            <?= ucfirst($row['status']) ?>
        </span>
    </td>

    <td>
        <?php if($row['status'] == 'pending'): ?>
            <a class="approve" href="approve.php?id=<?= $row['id'] ?>">✅ Approve</a>
            <a class="reject" href="reject.php?id=<?= $row['id'] ?>">❌ Reject</a>
        <?php else: ?>
            ✔ Done
        <?php endif; ?>
    </td>
</tr>

<?php endwhile; ?>

</table>

</div>

</body>
</html>