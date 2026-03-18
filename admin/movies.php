<?php
// admin/movies.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = db_connect();
$msg = '';

$uploadDir = __DIR__ . '/../uploads/';
$languages = ['Nepali','Hindi','English','Korean','Chinese','Japanese'];

// ADD / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $duration = ((int)$_POST['duration_hours'] * 60) + (int)$_POST['duration_minutes'];
    $genre = trim($_POST['genre']);
    $trailer = trim($_POST['trailer']);
    $language = trim($_POST['language']);
    $posterName = $_POST['old_poster'] ?? null;
    $id = (int)($_POST['movie_id'] ?? 0);

    // Handle poster upload
    if (!empty($_FILES['poster']['name'])) {
        $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array($ext, $allowed)) {
            $posterName = uniqid('p_').'.'.$ext;
            move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir.$posterName);
        }
    }

    if ($id > 0) {
        // UPDATE existing movie
        $stmt = $conn->prepare("UPDATE movies SET title=?,description=?,duration=?,genre=?,trailer=?,poster=?,language=? WHERE id=?");
        $stmt->bind_param("ssissssi",$title,$desc,$duration,$genre,$trailer,$posterName,$language,$id);
        $msg = "Movie Updated!";
    } else {
        // ADD new movie
        $stmt = $conn->prepare("INSERT INTO movies (title,description,duration,genre,trailer,poster,language) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("ssissss",$title,$desc,$duration,$genre,$trailer,$posterName,$language);
        $msg = "Movie Added!";
    }
    $stmt->execute();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM movies WHERE id=$id");
    header("Location: movies.php");
    exit;
}

// EDIT
$editMovie = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editMovie = $conn->query("SELECT * FROM movies WHERE id=$id")->fetch_assoc();
}

$movies = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");

function formatDuration($m){
    return floor($m/60)."h ".($m%60)."m";
}
?>

<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>CineMa Ghar - Manage Movies</title>
<link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/x-icon">
<style>
/* --- CSS same as your original --- */
body{margin:0;font-family:Arial, sans-serif;background:#f1f5f9;}
.sidebar{width:220px;height:100vh;background:#111827;color:#fff;position:fixed;padding-top:20px;transition:0.3s;}
.sidebar.hide{transform:translateX(-100%);}
.sidebar h2{text-align:center;color:#facc15;margin-bottom:25px;}
.sidebar a{display:block;padding:12px 20px;color:#cbd5e1;text-decoration:none;transition:0.3s;}
.sidebar a:hover{background:#1e293b;color:#fff;padding-left:25px;}
.main{margin-left:220px;padding:25px;transition:0.3s;}
.main.full{margin-left:0;}
.topbar{display:flex;align-items:center;gap:15px;margin-bottom:20px;}
.menu-btn{font-size:22px;background:#111827;color:#fff;padding:8px 12px;border-radius:6px;cursor:pointer;}
.container{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.two-col{display:flex;gap:20px;flex-wrap:wrap;}
.card{flex:1;min-width:320px;background:#ffffff;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);transition:0.3s;}
.card:hover{transform:translateY(-3px);}
input, textarea, select{width:100%;padding:10px;margin:6px 0;border-radius:6px;border:1px solid #d1d5db;}
button{width:100%;padding:10px;background:#2563eb;color:white;border:none;border-radius:6px;cursor:pointer;transition:0.3s;}
button:hover{background:#1e40af;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th, td{padding:10px;border-bottom:1px solid #e5e7eb;}
th{background:#2563eb;color:#fff;}
tr:hover{background:#f9fafb;}
img{width:55px;border-radius:6px;}
.msg{background:#d1fae5;color:#065f46;padding:10px;border-radius:6px;margin-bottom:15px;}
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
  <h2>Manage Movies</h2>
</div>

<div class="container">
<?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

<div class="two-col">
<!-- FORM -->
<div class="card">
<h3><?= $editMovie ? "Edit Movie" : "Add Movie" ?></h3>
<form method="post" enctype="multipart/form-data">
    <?php if($editMovie): ?>
    <input type="hidden" name="movie_id" value="<?= $editMovie['id'] ?>">
    <input type="hidden" name="old_poster" value="<?= $editMovie['poster'] ?>">
    <?php endif; ?>

    <input name="title" placeholder="Title" required value="<?= htmlspecialchars($editMovie['title'] ?? '') ?>">
    <textarea name="description"><?= htmlspecialchars($editMovie['description'] ?? '') ?></textarea>

    <div style="display:flex;gap:10px;">
      <input type="number" name="duration_hours" placeholder="Hours" 
        value="<?= isset($editMovie['duration']) ? floor($editMovie['duration']/60) : '' ?>">
      <input type="number" name="duration_minutes" placeholder="Minutes" 
        value="<?= isset($editMovie['duration']) ? ($editMovie['duration'] % 60) : '' ?>">
    </div>

    <input name="genre" placeholder="Genre" value="<?= htmlspecialchars($editMovie['genre'] ?? '') ?>">
    <input name="trailer" placeholder="Trailer URL" value="<?= htmlspecialchars($editMovie['trailer'] ?? '') ?>">

    <select name="language">
    <?php foreach($languages as $l): ?>
        <option value="<?= $l ?>" <?= ($editMovie['language'] ?? '') === $l ? 'selected' : '' ?>><?= $l ?></option>
    <?php endforeach; ?>
    </select>

    <input type="file" name="poster">
    <button><?= $editMovie ? "Update Movie" : "Add Movie" ?></button>
</form>
</div>

<!-- TABLE -->
<div class="card">
<h3>All Movies</h3>
<table>
<tr><th>ID</th><th>Poster</th><th>Title</th><th>Duration</th><th>Action</th></tr>
<?php while($m=$movies->fetch_assoc()): ?>
<tr>
<td><?= $m['id'] ?></td>
<td><?php if($m['poster']) echo "<img src='../uploads/".$m['poster']."'>" ?></td>
<td><?= htmlspecialchars($m['title']) ?></td>
<td><?= formatDuration($m['duration']) ?></td>
<td>
<a href="?edit=<?= $m['id'] ?>">Edit</a> |
<a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

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