<?php
require 'includes/db.php';
$conn = db_connect();

$q = trim($_GET['q'] ?? '');

if(!$q) exit;

$search = "%$q%";

$stmt = $conn->prepare("
SELECT id, title, poster 
FROM movies 
WHERE (title LIKE ? OR genre LIKE ?) 
AND status='active'
LIMIT 6
");

$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$res = $stmt->get_result();

while($m = $res->fetch_assoc()):
?>

<div class="live-card">
    <img src="uploads/<?=htmlspecialchars($m['poster'] ?: 'default.jpg')?>">

    <div class="live-info">
        <h4><?=htmlspecialchars($m['title'])?></h4>

        <a href="movie_view.php?id=<?=$m['id']?>" class="live-btn">
            🎟 Book
        </a>
    </div>
</div>

<?php endwhile; ?>