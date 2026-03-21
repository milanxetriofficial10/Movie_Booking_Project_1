<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = db_connect();
$msg = "";

/* ================= FETCH MOVIES & SCREENS ================= */

// IMPORTANT: status हटाइयो ताकि सबै movie देखियोस्
$movies  = $conn->query("SELECT id, title FROM movies ORDER BY title ASC");
$screens = $conn->query("SELECT id, screen_name FROM screens ORDER BY screen_name ASC");

/* ================= ADD SHOW ================= */

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_show'])){

    $movie_id  = (int)$_POST['movie_id'];
    $screen_id = (int)$_POST['screen_id'];
    $show_time = $_POST['show_time'];

    if(empty($movie_id) || empty($screen_id) || empty($show_time)){
        $msg = "⚠️ All fields required!";
    } else {

        // convert datetime-local → mysql format
        $show_time_mysql = date('Y-m-d H:i:s', strtotime($show_time));

        // check price column
        $check = $conn->query("SHOW COLUMNS FROM shows LIKE 'price'");
        $has_price = $check->num_rows > 0;

        if($has_price){
            $price = (float)$_POST['price'];

            $stmt = $conn->prepare("
                INSERT INTO shows (movie_id, screen_id, show_time, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iisd", $movie_id, $screen_id, $show_time_mysql, $price);

        } else {

            $stmt = $conn->prepare("
                INSERT INTO shows (movie_id, screen_id, show_time)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iis", $movie_id, $screen_id, $show_time_mysql);
        }

        if($stmt->execute()){
            $msg = "🎉 Show Added Successfully!";
        } else {
            $msg = "❌ Error: ".$stmt->error;
        }
    }
}

/* ================= DELETE ================= */

if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM shows WHERE id=$id");
    header("Location: shows.php");
    exit;
}

/* ================= FETCH SHOWS ================= */

$check = $conn->query("SHOW COLUMNS FROM shows LIKE 'price'");
$has_price = $check->num_rows > 0;

$fields = "s.id, m.title, sc.screen_name, s.show_time";
if($has_price) $fields .= ", s.price";

$shows = $conn->query("
    SELECT $fields
    FROM shows s
    JOIN movies m ON s.movie_id = m.id
    JOIN screens sc ON s.screen_id = sc.id
    ORDER BY s.show_time DESC
");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Shows</title>
<link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
*{box-sizing:border-box; margin:0; padding:0; font-family:Arial,sans-serif;}
body{background:#f1f5f9;}
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
.sidebar h2{text-align:center; margin-bottom:30px; color:#facc15;}
.sidebar a{display:block; padding:12px 20px; color:#cbd5e1; text-decoration:none; transition:.3s;}
.sidebar a:hover{background:#1e293b; color:#fff; padding-left:25px;}

/* MAIN */
.main{margin-left:220px; padding:20px; transition:.3s;}
.main.full{margin-left:0;}

/* TOPBAR */
.topbar{display:flex; align-items:center; gap:10px; margin-bottom:20px;}
.menu-btn{font-size:22px; background:#111827; color:#fff; padding:8px 12px; cursor:pointer; border-radius:6px;}

/* CARD */
.card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.card h3{margin-bottom:15px;}
.card form label{display:block; margin:8px 0 4px;}
.card form input, .card form select{width:100%; padding:8px; margin-bottom:12px; border:1px solid #ccc; border-radius:6px;}
.card form button{padding:10px 15px; background:#1e40af; color:#fff; border:none; border-radius:6px; cursor:pointer;}
.card form button:hover{background:#2563eb;}

/* TABLE */
table{width:100%; border-collapse:collapse; margin-top:10px;}
table th, table td{padding:8px; border:1px solid #ddd; text-align:left;}
th{background:#2563eb; color:#fff;}

/* MESSAGE */
.msg{background:#d1fae5; padding:10px; border-radius:6px; margin-bottom:10px; color:#065f46;}

/* RESPONSIVE */
.two-col{display:flex; gap:20px; flex-wrap:wrap;}
@media(max-width:768px){
  .two-col{flex-direction:column;}
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

<div class="main" id="main">
    <div class="topbar">
        <div class="menu-btn" onclick="toggleMenu()">☰</div>
        <h2>🎬 Manage Shows</h2>
    </div>

    <?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

    <!-- CREATE SHOW -->
    <div class="card" style="max-width:500px;">
        <h3>Create Show</h3>
        <form method="post">
            <label>Movie</label>
            <select name="movie_id" required>
                <option value="">Select movie</option>
                <?php if($movies && $movies->num_rows > 0) while($m = $movies->fetch_assoc()): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['title']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Screen</label>
            <select name="screen_id" required>
                <option value="">Select screen</option>
                <?php if($screens && $screens->num_rows > 0) mysqli_data_seek($screens,0); while($s = $screens->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['screen_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Show Time</label>
            <input type="datetime-local" name="show_time" required>

            <?php if($has_price): ?>
            <label>Price (Rs)</label>
            <input type="number" step="0.01" name="price" placeholder="e.g. 350" required>
            <?php endif; ?>

            <button name="create_show" type="submit">Add Show</button>
        </form>
    </div>

    <!-- ALL SHOWS TABLE BELOW -->
    <div class="card">
        <h3>All Shows</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Movie</th><th>Screen</th><th>Date</th><th>Time</th>
                    <?php if($has_price) echo "<th>Price</th>"; ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($shows && $shows->num_rows > 0): ?>
                    <?php while($row = $shows->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['screen_name']) ?></td>
                        <td><?= date('M j, Y', strtotime($row['show_time'])) ?></td>
                        <td><?= date('H:i', strtotime($row['show_time'])) ?></td>
                        <?php if($has_price) echo "<td>Rs ".htmlspecialchars($row['price'])."</td>"; ?>
                        <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this show?')">Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $has_price ? 7 : 6 ?>">No shows found.</td></tr>
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