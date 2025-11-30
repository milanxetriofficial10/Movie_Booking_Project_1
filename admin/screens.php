<?php
// admin/screens.php
require_once '../includes/db.php';
$conn = db_connect();
$msg = '';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ---- add screen ----
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_screen'])){
    $screen_name = trim($_POST['name']);
    $screen_rows = (int)($_POST['rows'] ?? 0);
    $screen_cols = (int)($_POST['cols'] ?? 0);

    // Check if 'rows' and 'cols' exist in DB
    $columns = $conn->query("SHOW COLUMNS FROM screens")->fetch_all(MYSQLI_ASSOC);
    $has_rows = $has_cols = false;
    foreach($columns as $col){
        if($col['Field'] === 'rows') $has_rows = true;
        if($col['Field'] === 'cols') $has_cols = true;
    }

    if($has_rows && $has_cols){
        $stmt = $conn->prepare("INSERT INTO screens (screen_name, `rows`, `cols`) VALUES (?,?,?)");
        $stmt->bind_param('sii',$screen_name,$screen_rows,$screen_cols);
    } else {
        $stmt = $conn->prepare("INSERT INTO screens (screen_name) VALUES (?)");
        $stmt->bind_param('s',$screen_name);
    }

    if($stmt->execute()) $msg = "Screen added successfully.";
    else $msg = "Error: ".$stmt->error;
}

// ---- delete ----
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM screens WHERE id=$id");
    header('Location: screens.php'); exit;
}

// ---- fetch screens dynamically ----
$columns = $conn->query("SHOW COLUMNS FROM screens")->fetch_all(MYSQLI_ASSOC);
$has_rows = $has_cols = false;
foreach($columns as $col){
    if($col['Field'] === 'rows') $has_rows = true;
    if($col['Field'] === 'cols') $has_cols = true;
}

$fields = "id, screen_name";
if($has_rows) $fields .= ", `rows`";
if($has_cols) $fields .= ", `cols`";

$screens = $conn->query("SELECT $fields FROM screens ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="/movie-booking/admin/assets/css/admin.css">
<title>Manage Screens</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
.admin-container {padding:20px;}
.card {background:#fff; padding:20px; border-radius:10px; margin-bottom:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1);}
table {width:100%; border-collapse:collapse;}
table th, table td {padding:8px; border:1px solid #ddd; text-align:left;}
.msg {background:#d1fae5; padding:10px; border-radius:6px; margin-bottom:12px; color:#065f46;}
.two-col {display:flex; gap:20px; flex-wrap:wrap;}
.card form input {width:100%; padding:8px; margin-bottom:12px; border:1px solid #ccc; border-radius:6px;}
.card form button {padding:10px 15px; background:#1e40af; color:#fff; border:none; border-radius:6px; cursor:pointer;}
.card form button:hover {background:#2563eb;}
</style>
</head>
<body>
<nav class="admin-nav">
  <a href="dashboard.php">Dashboard</a> |
  <a href="movies.php">Movies</a> |
  <a href="screens.php">Screens</a> |
  <a href="shows.php">Shows</a> |
  <a href="bookings.php">Bookings</a> |
  <a href="logout.php">Logout</a>
</nav>

<div class="admin-container">
  <h2>Screens / Halls</h2>
  <?php if($msg) echo "<p class='msg'>$msg</p>"; ?>

  <div class="two-col">
    <!-- Add Screen -->
    <div class="card">
      <h3>Add Screen</h3>
      <form method="post">
        <input name="name" placeholder="Screen name" required>
        <?php if($has_rows): ?><input type="number" name="rows" placeholder="Rows (e.g. 5)"><?php endif; ?>
        <?php if($has_cols): ?><input type="number" name="cols" placeholder="Columns (e.g. 8)"><?php endif; ?>
        <button name="add_screen" type="submit">Add</button>
      </form>
    </div>

    <!-- List Screens -->
    <div class="card">
      <h3>All Screens</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Name</th>
            <?php if($has_rows) echo "<th>Rows</th>"; ?>
            <?php if($has_cols) echo "<th>Cols</th>"; ?>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if($screens && $screens->num_rows > 0): ?>
          <?php while($s = $screens->fetch_assoc()): ?>
            <tr>
              <td><?= $s['id'] ?></td>
              <td><?= htmlspecialchars($s['screen_name']) ?></td>
              <?php if($has_rows) echo "<td>".htmlspecialchars($s['rows'])."</td>"; ?>
              <?php if($has_cols) echo "<td>".htmlspecialchars($s['cols'])."</td>"; ?>
              <td><a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete screen?')">Delete</a></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="<?= 2 + ($has_rows?1:0) + ($has_cols?1:0) +1 ?>">No screens found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
