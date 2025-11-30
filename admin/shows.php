<?php
// admin/shows.php
require_once '../includes/db.php';

$conn = db_connect();
$msg = '';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -------------------
// Fetch movies and screens
$movies  = $conn->query("SELECT id, title FROM movies WHERE status='active' ORDER BY title ASC");
$screens = $conn->query("SELECT id, screen_name FROM screens ORDER BY screen_name ASC");

// -------------------
// Add show
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_show'])){
    $movie_id  = (int)$_POST['movie_id'];
    $screen_id = (int)$_POST['screen_id'];
    $show_time = $_POST['show_time']; // yyyy-mm-ddThh:mm

    // Convert to MySQL DATETIME
    $show_time_mysql = date('Y-m-d H:i:s', strtotime($show_time));

    // Check if price column exists in DB
    $price_column_exists = $conn->query("SHOW COLUMNS FROM shows LIKE 'price'")->num_rows > 0;
    $price = 0;
    if ($price_column_exists) {
        $price = (float)$_POST['price'];
        $stmt = $conn->prepare("INSERT INTO shows (movie_id, screen_id, show_time, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iisd', $movie_id, $screen_id, $show_time_mysql, $price);
    } else {
        $stmt = $conn->prepare("INSERT INTO shows (movie_id, screen_id, show_time) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $movie_id, $screen_id, $show_time_mysql);
    }

    if($stmt->execute()) {
        $msg = "🎉 Show scheduled successfully.";
    } else {
        $msg = "⚠️ Error: " . $stmt->error;
    }
}

// -------------------
// Delete show
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM shows WHERE id=$id");
    header('Location: shows.php'); 
    exit;
}

// -------------------
// Fetch all shows dynamically (price optional)
$show_columns = $conn->query("SHOW COLUMNS FROM shows")->fetch_all(MYSQLI_ASSOC);
$has_price = false;
foreach($show_columns as $col){
    if($col['Field'] == 'price') $has_price = true;
}

$select_fields = "s.id, m.title, sc.screen_name, s.show_time";
if($has_price) $select_fields .= ", s.price";

$shows = $conn->query("
  SELECT $select_fields
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
<link rel="stylesheet" href="/movie-booking/admin/assets/css/admin.css">
<title>Manage Shows</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
.admin-container {padding:20px;}
.two-col {display:flex; gap:20px; flex-wrap:wrap;}
.card {background:#fff; padding:20px; border-radius:10px; flex:1; min-width:300px; box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.card h3 {margin-bottom:15px;}
.card form label {display:block; margin:8px 0 4px;}
.card form input, .card form select {width:100%; padding:8px; margin-bottom:12px; border:1px solid #ccc; border-radius:6px;}
.card form button {padding:10px 15px; background:#1e40af; color:#fff; border:none; border-radius:6px; cursor:pointer;}
.card form button:hover {background:#2563eb;}
table {width:100%; border-collapse:collapse;}
table th, table td {padding:8px; border:1px solid #ddd; text-align:left;}
.msg {background:#d1fae5; padding:10px; border-radius:6px; margin-bottom:12px; color:#065f46;}
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
  <h2>🎬 Manage Shows</h2>
  <?php if($msg) echo "<p class='msg'>$msg</p>"; ?>

  <div class="two-col">
    <!-- Create Show -->
    <div class="card">
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

    <!-- List Shows -->
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
</div>
</body>
</html>
