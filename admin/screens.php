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

    if($stmt->execute()) $msg = "✅ Screen added successfully.";
    else $msg = "❌ Error: ".$stmt->error;
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
<title>CineMa Ghar - Manage Screens</title>
<link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
/* SIDEBAR */
*{
  box-sizing:border-box;
  margin:0;
  padding:0;
  font-family:Arial, sans-serif;
}
body{
  margin:0;
  font-family:Arial, sans-serif;
  background:#f1f5f9;
}

/* WRAPPER */
.admin-wrapper{
  display:flex;
}

/* SIDEBAR */
.sidebar{
  width:220px;
  height:100vh;
  background:#111827;
  color:#fff;
  padding-top:20px;
  position:fixed;
  transition:0.3s;
}

.sidebar.hide{
  transform:translateX(-100%);
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

/* MAIN */
.main{margin-left:220px; padding:20px; transition:.3s;}
.main.full{margin-left:0;}

/* TOPBAR */
.topbar{display:flex; align-items:center; gap:10px; margin-bottom:20px;}
.menu-btn{
    font-size:22px;
    background:#111827;
    color:#fff;
    padding:8px 12px;
    cursor:pointer;
    border-radius:6px;
}

/* CARD */
.card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* FORM */
input, textarea{width:100%; padding:10px; margin-top:10px; border:1px solid #ccc; border-radius:6px;}
button{
    background:#2563eb; color:#fff; padding:10px; border:none; border-radius:6px; margin-top:10px; cursor:pointer;
}
button:hover{background:#1e40af;}

/* TABLE */
table{width:100%; border-collapse:collapse;}
th, td{padding:10px; border:1px solid #ddd; text-align:left;}
th{background:#2563eb; color:#fff;}

/* MESSAGE */
.msg{background:#d1fae5; padding:10px; border-radius:6px; margin-bottom:10px; color:#065f46;}
.two-col{display:flex; gap:20px; flex-wrap:wrap;}
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
        <h2>Manage Screens / Halls</h2>
    </div>

    <?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

    <div class="two-col">
        <!-- ADD SCREEN -->
        <div class="card">
            <h3>Add Screen</h3>
            <form method="post">
                <input name="name" placeholder="Screen name" required>
                <?php if($has_rows): ?><input type="number" name="rows" placeholder="Rows (e.g. 5)"><?php endif; ?>
                <?php if($has_cols): ?><input type="number" name="cols" placeholder="Cols (e.g. 8)"><?php endif; ?>
                <button name="add_screen" type="submit">Add</button>
            </form>
        </div>

        <!-- LIST SCREENS -->
        <div class="card">
            <h3>All Screens</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
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

<script>
function toggleMenu(){
    document.getElementById("sidebar").classList.toggle("hide");
    document.getElementById("main").classList.toggle("full");
}
</script>

</body>
</html>