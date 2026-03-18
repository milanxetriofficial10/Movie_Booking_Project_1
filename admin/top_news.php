<?php
require '../includes/db.php';

$conn = db_connect();
$msg = '';
$error = '';


// Handle Add News
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])){
    $news_text = trim($_POST['news_text'] ?? '');
    if($news_text != ''){
        $stmt = $conn->prepare("INSERT INTO movie_news (news_text) VALUES (?)");
        $stmt->bind_param('s', $news_text);
        if($stmt->execute()){
            $msg = "News added successfully!";
        } else {
            $error = "Error adding news.";
        }
        $stmt->close();
    } else {
        $error = "Please enter news text.";
    }
}



// Handle Delete News
if(isset($_GET['delete_id'])){
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM movie_news WHERE id=?");
    $stmt->bind_param('i', $delete_id);
    if($stmt->execute()){
        $msg = "News deleted successfully!";
    } else {
        $error = "Error deleting news.";
    }
    $stmt->close();
}



// Handle Update News
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_news'])){
    $news_id   = (int)$_POST['news_id'];
    $news_text = trim($_POST['news_text'] ?? '');
    if($news_text != ''){
        $stmt = $conn->prepare("UPDATE movie_news SET news_text=? WHERE id=?");
        $stmt->bind_param('si', $news_text, $news_id);
        if($stmt->execute()){
            $msg = "News updated successfully!";
        } else {
            $error = "Error updating news.";
        }
        $stmt->close();
    } else {
        $error = "Please enter news text.";
    }
}



// Fetch all news
$news_list = $conn->query("SELECT * FROM movie_news ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CineMa Ghar - Manage Movie News</title>
<link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:Arial,sans-serif;}
body{background:#f5f7fb;color:#333;}
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
.topbar{display:flex;align-items:center;gap:10px;margin-bottom:20px;}
.menu-btn{font-size:22px;background:#111827;color:#fff;padding:8px 12px;cursor:pointer;border-radius:6px;}

/* CARD */
.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.card h2{margin-bottom:15px;}

/* MESSAGE BOX */
.msg{background:#d1fae5;padding:12px 16px;border-left:5px solid #10b981;border-radius:6px;font-weight:500;margin-bottom:20px;color:#065f46;}
.error{background:#fee2e2;padding:12px 16px;border-left:5px solid #dc2626;border-radius:6px;font-weight:500;margin-bottom:20px;color:#b91c1c;}

/* FORM */
form input[type=text]{width:100%;padding:10px;margin-bottom:10px;border-radius:5px;border:1px solid #ccc;}
form input[type=submit]{padding:10px 20px;border:none;border-radius:5px;background:#1e40af;color:#fff;cursor:pointer;transition:.2s;}
form input[type=submit]:hover{background:#2563eb;}

/* TABLE */
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;}
th{background:#2563eb;color:#fff;}
a.btn{padding:5px 10px;background:#1e40af;color:#fff;border-radius:4px;text-decoration:none;margin-right:5px;transition:.2s;}
a.btn:hover{background:#2563eb;}

/* RESPONSIVE */
@media(max-width:768px){
    h2{font-size:22px;}
    th, td{padding:8px;font-size:13px;}
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
        <h2>📰 Manage Movie News</h2>
    </div>

    <?php if($msg) echo "<div class='msg'>$msg</div>"; ?>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>

    <div class="card">
        <!-- Add News Form -->
        <form method="POST">
            <input type="text" name="news_text" placeholder="Enter live news here">
            <input type="submit" name="add_news" value="Add News">
        </form>

        <!-- News Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>News Text</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($news = $news_list->fetch_assoc()): ?>
                <tr>
                    <td><?= $news['id'] ?></td>
                    <td><?= htmlspecialchars($news['news_text']) ?></td>
                    <td>
                        <!-- Edit form -->
                        <form style="display:inline;" method="POST">
                            <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                            <input type="text" name="news_text" value="<?= htmlspecialchars($news['news_text']) ?>">
                            <input type="submit" name="update_news" value="Update">
                        </form>
                        <a class="btn" href="?delete_id=<?= $news['id'] ?>" onclick="return confirm('Are you sure you want to delete this news?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
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
