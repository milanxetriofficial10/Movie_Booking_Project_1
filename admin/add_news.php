<?php
require_once '../includes/db.php';
$conn = db_connect();

$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

/* -----------------------------
   ADD NEWS SECTION
----------------------------- */
if (isset($_POST['submit'])) {
    $headline = $conn->real_escape_string($_POST['headline']);
    $description = $conn->real_escape_string($_POST['description']);
    $news_link = $conn->real_escape_string($_POST['news_link']);
    $youtube_link = $conn->real_escape_string($_POST['youtube_link']);

    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = $upload_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO news (headline, description, image, news_link, youtube_link)
            VALUES ('$headline','$description','$image_name','$news_link','$youtube_link')";
    $conn->query($sql);
}

/* -----------------------------
   DELETE NEWS SECTION
----------------------------- */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $res = $conn->query("SELECT image FROM news WHERE id=$id");
    $row = $res->fetch_assoc();
    if ($row && !empty($row['image'])) {
        @unlink($upload_dir . $row['image']);
    }
    $conn->query("DELETE FROM news WHERE id=$id");
    header("Location: manage_news.php");
    exit;
}

/* -----------------------------
   UPDATE NEWS SECTION
----------------------------- */
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $headline = $conn->real_escape_string($_POST['headline']);
    $description = $conn->real_escape_string($_POST['description']);
    $news_link = $conn->real_escape_string($_POST['news_link']);
    $youtube_link = $conn->real_escape_string($_POST['youtube_link']);

    $sql_update = "UPDATE news SET headline='$headline', description='$description', news_link='$news_link', youtube_link='$youtube_link'";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target = $upload_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $sql_update .= ", image='$image_name'";
    }

    $sql_update .= " WHERE id=$id";
    $conn->query($sql_update);
    header("Location: manage_news.php");
    exit;
}

$news_data = $conn->query("SELECT * FROM news ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage News</title>
<style>
body {
    font-family: Arial;
    background: #eef2f3;
    padding: 30px;
}
h2 {
    text-align: center;
    color: #333;
}
form {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    max-width: 600px;
    margin: 30px auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
input, textarea, button {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}
button {
    background: #007BFF;
    color: #fff;
    border: none;
    cursor: pointer;
}
button:hover {
    background: #0056b3;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    margin-top: 30px;
    border-radius: 8px;
    overflow: hidden;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
th {
    background: #007BFF;
    color: #fff;
}
img {
    width: 100px;
    border-radius: 6px;
}
a.delete, a.edit {
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
}
a.delete { background: #ff4d4d; }
a.edit { background: #28a745; }
a.delete:hover { background: #e60000; }
a.edit:hover { background: #1e7e34; }
</style>
</head>
<body>

<h2>📰 Manage Movie News</h2>

<!-- ADD NEWS FORM -->
<form method="post" enctype="multipart/form-data">
  <input type="text" name="headline" placeholder="Headline" required>
  <textarea name="description" placeholder="Description" required></textarea>
  <input type="text" name="news_link" placeholder="News Link" required>
  <input type="text" name="youtube_link" placeholder="YouTube Link (optional)">
  <input type="file" name="image">
  <button type="submit" name="submit">Add News</button>
</form>

<!-- DISPLAY NEWS TABLE -->
<table>
  <tr>
    <th>ID</th>
    <th>Image</th>
    <th>Headline</th>
    <th>Actions</th>
  </tr>
  <?php while($row = $news_data->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td>
      <?php if(!empty($row['image'])): ?>
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="">
      <?php else: ?>
        <em>No image</em>
      <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row['headline']) ?></td>
    <td>
      <a href="?edit=<?= $row['id'] ?>" class="edit">Edit</a>
      <a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this news?')">Delete</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<?php
// EDIT SECTION FORM (below table)
if (isset($_GET['edit'])):
  $id = (int)$_GET['edit'];
  $edit = $conn->query("SELECT * FROM news WHERE id=$id")->fetch_assoc();
?>
<h2>Edit News ID <?= $edit['id'] ?></h2>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $edit['id'] ?>">
  <input type="text" name="headline" value="<?= htmlspecialchars($edit['headline']) ?>" required>
  <textarea name="description" required><?= htmlspecialchars($edit['description']) ?></textarea>
  <input type="text" name="news_link" value="<?= htmlspecialchars($edit['news_link']) ?>" required>
  <input type="text" name="youtube_link" value="<?= htmlspecialchars($edit['youtube_link']) ?>">
  <input type="file" name="image">
  <button type="submit" name="update">Update News</button>
</form>
<?php endif; ?>

</body>
</html>
