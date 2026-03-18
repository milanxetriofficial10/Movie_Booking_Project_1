<?php
require_once '../includes/db.php';
$conn = db_connect();

$msg = '';

// Folder where images are stored
$uploadDir = __DIR__ . '/../uploads/';

// ADD SLIDE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slide'])) {
    $slideText = $_POST['slide_text'] ?? '';

    if (!empty($_FILES['slide']['name'])) {
        $tmp = $_FILES['slide']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['slide']['name'], PATHINFO_EXTENSION));
        $name = uniqid('slide_') . '.' . $ext;

        if (move_uploaded_file($tmp, $uploadDir . $name)) {
            $stmt = $conn->prepare("INSERT INTO slider_images (image_name, slide_text) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $slideText);
            $stmt->execute();
            $msg = "✅ Slide added successfully!";
        } else {
            $msg = "❌ Failed to upload image!";
        }
    } else {
        $msg = "⚠️ Please select an image to upload!";
    }
}

// DELETE SLIDE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Delete file from server
    $file = $conn->query("SELECT image_name FROM slider_images WHERE id=$id")->fetch_assoc();
    if ($file && !empty($file['image_name']) && file_exists($uploadDir . $file['image_name'])) {
        @unlink($uploadDir . $file['image_name']);
    }

    $conn->query("DELETE FROM slider_images WHERE id=$id");
    header("Location: slider.php");
    exit;
}

// EDIT SLIDE
if (isset($_POST['edit_slide'])) {
    $id = (int)$_POST['id'];
    $text = $_POST['slide_text'] ?? '';

    // Check if a new image is uploaded
    if (!empty($_FILES['slide']['name'])) {
        $tmp = $_FILES['slide']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['slide']['name'], PATHINFO_EXTENSION));
        $name = uniqid('slide_') . '.' . $ext;

        if (move_uploaded_file($tmp, $uploadDir . $name)) {
            // Delete old image
            $old = $conn->query("SELECT image_name FROM slider_images WHERE id=$id")->fetch_assoc();
            if ($old && !empty($old['image_name']) && file_exists($uploadDir . $old['image_name'])) {
                @unlink($uploadDir . $old['image_name']);
            }

            $stmt = $conn->prepare("UPDATE slider_images SET image_name=?, slide_text=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $text, $id);
            $stmt->execute();
            $msg = "✅ Slide updated successfully!";
        } else {
            $msg = "❌ Failed to upload new image!";
        }
    } else {
        $stmt = $conn->prepare("UPDATE slider_images SET slide_text=? WHERE id=?");
        $stmt->bind_param("si", $text, $id);
        $stmt->execute();
        $msg = "✅ Slide updated successfully!";
    }
}

// FETCH ALL SLIDES
$slides = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
?>
<!doctype html>
<html>
<head>
<title>Manage Slider</title>
<style>
body{
  margin:0;
  font-family:Arial, sans-serif;
  background:#f1f5f9;
}

/* SIDEBAR */
.sidebar{
  width:220px;
  height:100vh;
  background:#111827;
  color:#fff;
  position:fixed;
  padding-top:20px;
  transition:0.3s;
}
.sidebar.hide{
  transform:translateX(-100%);
}

.sidebar h2{
  text-align:center;
  color:#facc15;
  margin-bottom:25px;
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
.main{
  margin-left:220px;
  padding:25px;
  transition:0.3s;
}
.main.full{
  margin-left:0;
}

/* TOPBAR */
.topbar{
  display:flex;
  align-items:center;
  gap:15px;
  margin-bottom:20px;
}

.menu-btn{
  font-size:22px;
  background:#111827;
  color:#fff;
  padding:8px 12px;
  border-radius:6px;
  cursor:pointer;
}

/* CONTAINER */
.container{
  background:#fff;
  padding:20px;
  border-radius:12px;
  box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* TWO COLUMN */
.two-col{
  display:flex;
  gap:20px;
  flex-wrap:wrap;
}

.card{
  flex:1;
  min-width:320px;
  background:#ffffff;
  padding:20px;
  border-radius:12px;
  box-shadow:0 2px 8px rgba(0,0,0,0.08);
  transition:0.3s;
}
.card:hover{
  transform:translateY(-3px);
}

/* FORM */
input, textarea, select{
  width:100%;
  padding:10px;
  margin:6px 0;
  border-radius:6px;
  border:1px solid #d1d5db;
}

button{
  width:100%;
  padding:10px;
  background:#2563eb;
  color:white;
  border:none;
  border-radius:6px;
  cursor:pointer;
  transition:0.3s;
}
button:hover{
  background:#1e40af;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}

th, td{
  padding:10px;
  border-bottom:1px solid #e5e7eb;
}

th{
  background:#2563eb;
  color:#fff;
}

tr:hover{
  background:#f9fafb;
}

img{
  width:55px;
  border-radius:6px;
}

/* MESSAGE */
.msg{
  background:#d1fae5;
  color:#065f46;
  padding:10px;
  border-radius:6px;
  margin-bottom:15px;
}
.main{ margin-left:220px; padding:20px; transition:.3s; }
.main.full{margin-left:0;}
/* TOPBAR */
.topbar{ display:flex; align-items:center; gap:10px; margin-bottom:20px; }
.menu-btn{ font-size:22px; background:#111827; color:#fff; padding:8px 12px; cursor:pointer; border-radius:6px; }
/* CARD */
.card{ background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-bottom:20px; }
/* FORM */
input, textarea{ width:100%; padding:10px; margin-top:10px; border:1px solid #ccc; border-radius:6px; }
button{ background:#2563eb; color:#fff; padding:10px; border:none; border-radius:6px; margin-top:10px; cursor:pointer; }
button:hover{ background:#1e40af; }
/* TABLE */
table{ width:100%; border-collapse:collapse; }
th, td{ padding:10px; border-bottom:1px solid #ddd; text-align:center; }
th{ background:#2563eb; color:#fff; }
img{ width:100px; border-radius:6px; }
.msg{ background:#d1fae5; padding:10px; border-radius:6px; margin-bottom:10px; }
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
  <h2>Manage Slider</h2>
</div>

<?php if ($msg) echo "<div class='msg'>$msg</div>"; ?>

<div class="card">
  <h3>Add Slide</h3>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="slide" required>
    <input type="text" name="slide_text" placeholder="Heading">
    <textarea name="slide_paragraph" placeholder="Paragraph"></textarea>
    <input type="text" name="button_text" placeholder="Button Text">
    <input type="text" name="button_link" placeholder="Button Link">
    <button name="add_slide">Upload</button>
  </form>
</div>

<div class="card">
  <h3>All Slides</h3>
  <table>
    <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Text</th>
      <th>Action</th>
    </tr>
    <?php while($s = $slides->fetch_assoc()): ?>
    <tr>
      <td><?= $s['id'] ?></td>
      <td><img src="/Movie_Booking_Project_1/uploads/<?= htmlspecialchars($s['image_name']) ?>"></td>
      <td><?= htmlspecialchars($s['slide_text']) ?></td>
      <td>
        <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this slide?')">Delete</a> |
        <a href="#" onclick="toggleEdit(<?= $s['id'] ?>); return false;">Edit</a>
      </td>
    </tr>
    <tr id="editRow<?= $s['id'] ?>" style="display:none;">
      <td colspan="4">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?= $s['id'] ?>">
          <input type="file" name="slide">
          <input type="text" name="slide_text" value="<?= htmlspecialchars($s['slide_text']) ?>">
          <textarea name="slide_paragraph"><?= htmlspecialchars($s['slide_paragraph']) ?></textarea>
          <button name="edit_slide">Update</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</div>

<script>
function toggleMenu(){
  document.getElementById("sidebar").classList.toggle("hide");
  document.getElementById("main").classList.toggle("full");
}
function toggleEdit(id){
  let row = document.getElementById("editRow"+id);
  row.style.display = row.style.display === "none" ? "table-row" : "none";
}
</script>

</body>
</html>