<?php
require_once '../includes/db.php';
$conn = db_connect();

$message = "";
if (isset($_POST['add_location'])) {
    $location = $conn->real_escape_string($_POST['location']);
    if ($location != "") {
        $conn->query("INSERT INTO locations (name) VALUES ('$location')");
        $message = "Location added successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add Locations</title>
</head>
<body>
<h2>Admin Panel: Add Locations</h2>
<?php if($message) echo "<p style='color:green;'>$message</p>"; ?>
<form method="post">
    <input type="text" name="location" placeholder="Enter location" required>
    <button type="submit" name="add_location">Add Location</button>
</form>

<h3>Existing Locations:</h3>
<ul>
<?php
$res = $conn->query("SELECT * FROM locations ORDER BY name ASC");
while($row = $res->fetch_assoc()){
    echo "<li>".htmlspecialchars($row['name'])."</li>";
}
?>
</ul>
</body>
</html>
