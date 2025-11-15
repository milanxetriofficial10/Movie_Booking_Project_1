<?php
// admin/movies.php
session_start();
require_once '../includes/db.php';

// 🔒 Login check: only logged-in admin can access
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$conn = db_connect();
$msg = '';

// Folder to store uploaded posters
$uploadDir = __DIR__ . '/../uploads/';

// Handle Add or Update Movie
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $duration_hours = (int)$_POST['duration_hours'];
    $duration_minutes = (int)$_POST['duration_minutes'];
    $duration = $duration_hours * 60 + $duration_minutes; // convert to total minutes
    $genre = trim($_POST['genre']);
    $trailer = trim($_POST['trailer']);
    $posterName = $_POST['old_poster'] ?? null;
    $id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;

    // Handle poster upload
    if (!empty($_FILES['poster']['name'])) {
        $tmp = $_FILES['poster']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            $msg = "Invalid image type. Only jpg, jpeg, png, gif allowed.";
        } else {
            $posterName = uniqid('p_') . '.' . $ext;
            if (!move_uploaded_file($tmp, $uploadDir . $posterName)) {
                $msg = "Failed to upload poster.";
            }
        }
    }

    // Insert or Update
    if (!$msg) {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE movies SET title=?, description=?, duration=?, genre=?, trailer=?, poster=? WHERE id=?");
            $stmt->bind_param('ssisssi', $title, $desc, $duration, $genre, $trailer, $posterName, $id);
            $msg = $stmt->execute() ? "Movie updated successfully!" : "Error: " . $stmt->error;
        } else {
            $stmt = $conn->prepare("INSERT INTO movies (title,description,duration,genre,trailer,poster) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssisss', $title, $desc, $duration, $genre, $trailer, $posterName);
            $msg = $stmt->execute() ? "Movie added successfully!" : "Error: " . $stmt->error;
        }
    }
}

// Handle Delete Movie
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $movie = $conn->query("SELECT poster FROM movies WHERE id=$id")->fetch_assoc();
    if ($movie && $movie['poster']) @unlink($uploadDir . $movie['poster']);
    $conn->query("DELETE FROM movies WHERE id=$id");
    header('Location: movies.php');
    exit;
}

// Handle Edit (fetch data)
$editMovie = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $editMovie = $conn->query("SELECT * FROM movies WHERE id=$id")->fetch_assoc();
}

// Fetch all movies
$movies = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");

// Function to convert minutes to H:M
function formatDuration($minutes) {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf("%dh %02dm", $h, $m);
}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Movies</title>
<link rel="stylesheet" href="/movie-booking/admin/assets/css/admin.css">
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; margin:0; padding:0; }
.admin-container { max-width: 1200px; margin: 30px auto; padding: 20px; background:#fff; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#111; margin-bottom:20px; }
.two-col { display:flex; gap:20px; flex-wrap:wrap; }
.card { flex:1; min-width:300px; background:#fafafa; padding:20px; border-radius:8px; box-shadow:0 0 5px rgba(0,0,0,0.05); }
.card h3 { margin-top:0; color:#333; }
input, textarea, button, select { width:100%; padding:10px; margin:5px 0; border-radius:5px; border:1px solid #ccc; box-sizing:border-box; }
button { background:#2563eb; color:#fff; border:none; cursor:pointer; transition:0.3s; }
button:hover { background:#1e40af; }
.msg { padding:10px; background:#def1de; color:#166534; border-radius:5px; margin-bottom:15px; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
table th, table td { padding:10px; text-align:left; border-bottom:1px solid #ddd; }
table th { background:#2563eb; color:#fff; }
table img { border-radius:5px; width:60px; }
a { color:#2563eb; text-decoration:none; margin-right:8px; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="admin-container">
  <h2>🎬 Manage Movies</h2>
  <?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
  <div class="two-col">
    <!-- Add/Edit Movie Form -->
    <div class="card">
      <h3><?= $editMovie ? 'Edit Movie' : 'Add Movie' ?></h3>
      <form method="post" enctype="multipart/form-data">
        <?php if($editMovie): ?>
          <input type="hidden" name="movie_id" value="<?=$editMovie['id']?>">
          <input type="hidden" name="old_poster" value="<?=$editMovie['poster']?>">
        <?php endif; ?>
        <input name="title" placeholder="Title" required value="<?=htmlspecialchars($editMovie['title'] ?? '')?>">
        <textarea name="description" placeholder="Description"><?=htmlspecialchars($editMovie['description'] ?? '')?></textarea>
        
        <!-- Duration as hours + minutes -->
        <div style="display:flex; gap:10px;">
            <input name="duration_hours" type="number" placeholder="Hours" min="0" required value="<?= isset($editMovie['duration']) ? floor($editMovie['duration']/60) : '' ?>">
            <input name="duration_minutes" type="number" placeholder="Minutes" min="0" max="59" required value="<?= isset($editMovie['duration']) ? $editMovie['duration']%60 : '' ?>">
        </div>

        <input name="genre" placeholder="Genre" required value="<?=htmlspecialchars($editMovie['genre'] ?? '')?>">
        <input name="trailer" placeholder="Trailer URL (YouTube embed or link)" value="<?=htmlspecialchars($editMovie['trailer'] ?? '')?>">
        <input type="file" name="poster" accept="image/*">
        <?php if($editMovie && $editMovie['poster']): ?>
          <p>Current Poster: <img src="/Movie_Booking_Project_1/uploads/<?=$editMovie['poster']?>" style="width:60px;"></p>
        <?php endif; ?>
        <button type="submit" name="add_movie"><?= $editMovie ? 'Update Movie' : 'Add Movie' ?></button>
      </form>
    </div>

    <!-- Movies List -->
    <div class="card">
      <h3>All Movies</h3>
      <table>
        <thead><tr><th>ID</th><th>Poster</th><th>Title</th><th>Duration</th><th>Trailer</th><th>Action</th></tr></thead>
        <tbody>
        <?php while($m = $movies->fetch_assoc()): ?>
          <tr>
            <td><?=$m['id']?></td>
            <td>
              <?php if($m['poster'] && file_exists($uploadDir.$m['poster'])): ?>
                <img src="/Movie_Booking_Project_1/uploads/<?=$m['poster']?>">
              <?php else: ?>
                <span style="color:#999;font-size:12px;">No Image</span>
              <?php endif; ?>
            </td>
            <td><?=htmlspecialchars($m['title'])?></td>
            <td><?=formatDuration($m['duration'])?></td>
            <td>
              <?php if(!empty($m['trailer'])): ?>
                <a href="<?=$m['trailer']?>" target="_blank">Watch</a>
              <?php else: ?>
                <span style="color:#999;font-size:12px;">N/A</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="movies.php?edit=<?=$m['id']?>">Edit</a>
              <a href="movies.php?delete=<?=$m['id']?>" onclick="return confirm('Delete this movie?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
