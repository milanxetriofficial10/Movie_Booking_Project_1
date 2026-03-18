<?php
require_once '../includes/db.php';
$conn = db_connect();
$msg = '';

// Add hall
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hall'])) {
    $hallName = trim($_POST['hall_name'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if($hallName !== '') {
        $stmt = $conn->prepare("INSERT INTO cinema_halls (hall_name, location) VALUES (?, ?)");
        $stmt->bind_param('ss', $hallName, $location);
        if($stmt->execute()) {
            $msg = "✅ Cinema hall added successfully!";
        } else {
            $msg = "❌ Error: " . $stmt->error;
        }
    } else {
        $msg = "⚠️ Hall name is required!";
    }
}

// Delete hall
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM cinema_halls WHERE id=$id");
    header('Location: cinema_halls.php');
    exit;
}

// Fetch all halls
$halls = $conn->query("SELECT * FROM cinema_halls ORDER BY id DESC");
?>

<!doctype html>
<html>
<head>
<title>Manage Cinema Halls</title>
 <link rel="shortcut icon" href="../imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/png">
<style>
body { font-family: Arial,sans-serif; background:#f4f6f9; padding:0; margin:0; }
.container { max-width: 800px; margin: 30px auto; padding: 20px; background:#fff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
h2 { color: #1e40af; }
.msg { background:#dbeafe; padding:10px; border-left:4px solid #1e3a8a; border-radius:6px; margin-bottom:15px; }
input[type="text"] { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ccc; }
button { padding:10px 16px; border:none; background:#1e40af; color:white; border-radius:6px; cursor:pointer; }
button:hover { background:#2563eb; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
table th, table td { padding:10px; border-bottom:1px solid #ddd; text-align:center; }
table th { background:#1e3a8a; color:white; }
a { color:crimson; text-decoration:none; font-weight:bold; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="container">
<h2>🎬 Manage Cinema Halls</h2>
<?php if($msg) echo "<p class='msg'>$msg</p>"; ?>

<form method="post">
    <input type="text" name="hall_name" placeholder="Hall Name" required>
    <input type="text" name="location" placeholder="Location (optional)">
    <button type="submit" name="add_hall">Add Hall</button>
</form>

<h3>All Halls</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Hall Name</th>
            <th>Location</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while($h = $halls->fetch_assoc()): ?>
        <tr>
            <td><?= $h['id'] ?></td>
            <td><?= htmlspecialchars($h['hall_name']) ?></td>
            <td><?= htmlspecialchars($h['location']) ?></td>
            <td><a href="cinema_halls.php?delete=<?= $h['id'] ?>" onclick="return confirm('Delete this hall?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
</body>
</html>
