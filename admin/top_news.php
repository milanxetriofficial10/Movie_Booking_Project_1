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
<title>Manage Movie News</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
body{font-family: Arial, sans-serif; background:#111; color:#fff; margin:0; padding:20px;}
.container{max-width:800px; margin:auto;}
form{margin-bottom:20px;}
input[type=text]{width:100%; padding:10px; margin-bottom:10px; border-radius:5px; border:none;}
input[type=submit]{padding:10px 20px; border:none; border-radius:5px; background:#4CAF50; color:#fff; cursor:pointer;}
input[type=submit]:hover{background:#45a049;}
.message{margin-bottom:15px; color:#4CAF50;}
.error{margin-bottom:15px; color:#f44336;}
table{width:100%; border-collapse:collapse;}
th,td{padding:10px; border-bottom:1px solid #444; text-align:left;}
th{background:#222;}
a.btn{padding:5px 10px; background:#1e40af; color:#fff; border-radius:4px; text-decoration:none; margin-right:5px;}
a.btn:hover{background:#2563eb;}
</style>
</head>
<body>
<div class="container">
    <h2>Manage Movie News</h2>

    <?php if($msg): ?><p class="message"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
    <?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

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
</body>
</html>
