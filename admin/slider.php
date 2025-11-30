<?php
require_once '../includes/db.php';
$conn = db_connect();
$msg = '';

// ✅ ADD SLIDE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slide'])) {
    $slideText = trim($_POST['slide_text'] ?? '');
    $slideParagraph = trim($_POST['slide_paragraph'] ?? '');
    $buttonText = trim($_POST['button_text'] ?? '');
    $buttonLink = trim($_POST['button_link'] ?? '');

    if (!empty($_FILES['slide']['name'])) {
        $tmp = $_FILES['slide']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['slide']['name'], PATHINFO_EXTENSION));
        $slideName = uniqid('slide_') . '.' . $ext;

        if (move_uploaded_file($tmp, __DIR__ . '/../uploads/' . $slideName)) {
            $stmt = $conn->prepare("INSERT INTO slider_images (image_name, slide_text, slide_paragraph, button_text, button_link) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $slideName, $slideText, $slideParagraph, $buttonText, $buttonLink);
            $stmt->execute();
            $msg = "✅ Slide added successfully!";
        } else {
            $msg = "❌ Failed to upload slide image.";
        }
    } else {
        $msg = "⚠️ Please select an image to upload.";
    }
}

// ✅ DELETE SLIDE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $r = $conn->query("SELECT image_name FROM slider_images WHERE id=$id")->fetch_assoc();
    if ($r && !empty($r['image_name'])) {
        @unlink(__DIR__ . '/../uploads/' . $r['image_name']);
    }
    $conn->query("DELETE FROM slider_images WHERE id=$id");
    header('Location: slider.php');
    exit;
}

// ✅ EDIT SLIDE
if (isset($_POST['edit_slide'])) {
    $id = (int)$_POST['id'];
    $slideText = trim($_POST['slide_text'] ?? '');
    $slideParagraph = trim($_POST['slide_paragraph'] ?? '');
    $buttonText = trim($_POST['button_text'] ?? '');
    $buttonLink = trim($_POST['button_link'] ?? '');
    $newImage = '';

    // Check for new image upload
    if (!empty($_FILES['slide']['name'])) {
        $tmp = $_FILES['slide']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['slide']['name'], PATHINFO_EXTENSION));
        $newImage = uniqid('slide_') . '.' . $ext;
        if (move_uploaded_file($tmp, __DIR__ . '/../uploads/' . $newImage)) {
            // Delete old image
            $old = $conn->query("SELECT image_name FROM slider_images WHERE id=$id")->fetch_assoc();
            if ($old && !empty($old['image_name'])) {
                @unlink(__DIR__ . '/../uploads/' . $old['image_name']);
            }
        } else {
            $newImage = '';
        }
    }

    if ($newImage) {
        $stmt = $conn->prepare("UPDATE slider_images SET image_name=?, slide_text=?, slide_paragraph=?, button_text=?, button_link=? WHERE id=?");
        $stmt->bind_param('sssssi', $newImage, $slideText, $slideParagraph, $buttonText, $buttonLink, $id);
    } else {
        $stmt = $conn->prepare("UPDATE slider_images SET slide_text=?, slide_paragraph=?, button_text=?, button_link=? WHERE id=?");
        $stmt->bind_param('ssssi', $slideText, $slideParagraph, $buttonText, $buttonLink, $id);
    }

    if ($stmt->execute()) {
        $msg = "✅ Slide updated successfully!";
    } else {
        $msg = "❌ Error updating slide: " . $stmt->error;
    }
}

// Fetch all slides
$slides = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
?>
<!doctype html>
<html>
<head>
<title>Manage Slider</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
body { font-family: Arial,sans-serif; background:#f4f6f9; margin:0; padding:0; }
.admin-container { padding:30px; max-width:1200px; margin:0 auto; }
.card { background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-bottom:20px; }
h2,h3 { color:#1e40af; }
.msg { background:#dbeafe; padding:10px; border-left:4px solid #1e3a8a; border-radius:6px; }
input[type="file"], input[type="text"], textarea { width:100%; padding:10px; margin-top:10px; border:1px solid #ccc; border-radius:6px; }
textarea { resize:vertical; height:80px; }
button { background:#1e40af; color:white; padding:10px 16px; border:none; border-radius:6px; cursor:pointer; margin-top:10px; }
button:hover { background:#2563eb; }
table { width:100%; border-collapse: collapse; margin-top:10px; }
table th, table td { padding:10px; border-bottom:1px solid #ddd; text-align:center; vertical-align:middle; }
table th { background:#1e3a8a; color:white; }
table img { width:120px; border-radius:6px; }
a { color:crimson; text-decoration:none; font-weight:bold; }
a:hover { text-decoration:underline; }
.edit-form { background:#f9fafb; padding:10px; border-radius:6px; margin-top:10px; }
</style>
</head>
<body>
<div class="admin-container">
  <h2>🎞️ Manage Slider</h2>
  <?php if ($msg) echo "<p class='msg'>$msg</p>"; ?>

  <!-- ADD SLIDE -->
  <div class="card">
    <h3>Add New Slide</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="file" name="slide" accept="image/*" required>
      <input type="text" name="slide_text" placeholder="Enter main heading text">
      <textarea name="slide_paragraph" placeholder="Enter paragraph text (optional)"></textarea>
      <input type="text" name="button_text" placeholder="Button Text (optional)">
      <input type="text" name="button_link" placeholder="Button Link URL (optional)">
      <button type="submit" name="add_slide">Upload Slide</button>
    </form>
  </div>

  <!-- ALL SLIDES -->
  <div class="card">
    <h3>All Slides</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Text</th>
          <th>Paragraph</th>
          <th>Button</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($s = $slides->fetch_assoc()): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><img src="/Movie_Booking_Project_1/uploads/<?= htmlspecialchars($s['image_name']) ?>" alt="Slide"></td>
          <td><?= htmlspecialchars($s['slide_text']) ?></td>
          <td><?= htmlspecialchars($s['slide_paragraph']) ?></td>
          <td>
            <?php if(!empty($s['button_text'])): ?>
              <a href="<?= htmlspecialchars($s['button_link']) ?>" target="_blank"><?= htmlspecialchars($s['button_text']) ?></a>
            <?php endif; ?>
          </td>
          <td>
            <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this slide?')">Delete</a>
            |
            <a href="#" onclick="toggleEdit(<?= $s['id'] ?>);return false;">Edit</a>
          </td>
        </tr>
        <tr id="editRow<?= $s['id'] ?>" style="display:none;">
          <td colspan="6">
            <div class="edit-form">
              <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <input type="file" name="slide" accept="image/*">
                <input type="text" name="slide_text" value="<?= htmlspecialchars($s['slide_text']) ?>" placeholder="Slide Text">
                <textarea name="slide_paragraph" placeholder="Paragraph"><?= htmlspecialchars($s['slide_paragraph']) ?></textarea>
                <input type="text" name="button_text" value="<?= htmlspecialchars($s['button_text']) ?>" placeholder="Button Text">
                <input type="text" name="button_link" value="<?= htmlspecialchars($s['button_link']) ?>" placeholder="Button Link">
                <button type="submit" name="edit_slide">Save Changes</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function toggleEdit(id){
  const row = document.getElementById('editRow'+id);
  row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>
</body>
</html>
