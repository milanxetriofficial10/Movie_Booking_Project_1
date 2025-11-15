<?php
require 'includes/db.php'; // DB connection
$conn = db_connect();
require 'includes/header.php'; // your header

require 'vendor/autoload.php'; // PHPMailer + Twilio
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

$feedback_msg = "";

// ===== Handle feedback form submission =====
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO feedback (name, email, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $email, $rating, $comment);

    if($stmt->execute()) {
        $feedback_msg = "Thank you! Your feedback has been submitted.";
    } else {
        $feedback_msg = "Error submitting feedback. Try again.";
    }
}

// ===== Slider Images =====
$slides_res = $conn->query("SELECT * FROM feedback_slider ORDER BY id ASC"); // natural order
$slides = [];
while($row = $slides_res->fetch_assoc()) {
    $slides[] = $row;
}
?>

<div class="container">
    <div class="slider-section">
        <?php foreach($slides as $i => $s): ?>
            <img src="/Movie_Booking_Project_1/imgs/<?=htmlspecialchars($s['image_name'])?>" class="<?= $i===0 ? 'active' : '' ?>" alt="Slide Image">
        <?php endforeach; ?>
    </div>
    <div class="text-section">
        <h2>Submit Your Feedback</h2>
        <p>Let us know what you think about our movies and booking system!</p>
        <?php if($feedback_msg): ?>
            <div class="msg"><?=htmlspecialchars($feedback_msg)?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <select name="rating" required>
                <option value="">Rating</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="5">5 ⭐</option>
            </select>
            <textarea name="comment" placeholder="Your Feedback" rows="5" required></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</div>

<style>
* { box-sizing:border-box; margin:0; padding:0; font-family:'Poppins', sans-serif; }
body { background:#11141b; color:#fff; padding:0; margin:0; }
.container { max-width:900px; width:100%; display:flex; background:#1b1c2f; border-radius:20px; overflow:hidden; margin:50px auto; }
.slider-section { width:50%; position:relative; overflow:hidden; }
.slider-section img { width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; opacity:0; transition:opacity 1s ease; }
.slider-section img.active { opacity:1; }
.text-section { width:50%; padding:40px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; background:linear-gradient(145deg,#2a2c3e,#1b1c2f); }
.text-section h2 { font-size:2rem; color:#ffe816; margin-bottom:15px; text-shadow:1px 1px 10px rgba(0,0,0,0.5); }
.text-section p { margin-bottom:25px; font-size:1.1rem; }
input, select, textarea { width:100%; padding:12px; margin-bottom:15px; border-radius:10px; border:none; background: rgba(255,255,255,0.05); color:#fff; }
button { width:100%; padding:15px; border:none; border-radius:12px; background:linear-gradient(90deg,#ff4c60,#ff9933); color:#fff; font-weight:700; cursor:pointer; transition:0.3s; }
button:hover { background:linear-gradient(90deg,#ff9933,#ff4c60); }
.msg { text-align:center; margin-bottom:15px; font-weight:bold; color:#0f0; }
@media(max-width:900px){ .container{flex-direction:column;} .slider-section,.text-section{width:100%; padding:20px;} }
</style>

<script>
// ===== Slider Auto Loop =====
window.addEventListener('load', function(){
    const slides = document.querySelectorAll('.slider-section img');
    let current = 0;

    function showSlide(index){
        slides.forEach(s => s.classList.remove('active'));
        slides[index].classList.add('active');
    }

    function nextSlide(){
        current = (current + 1) % slides.length; // loop back to first
        showSlide(current);
    }

    showSlide(current); // show first immediately
    setInterval(nextSlide, 4000); // every 4s
});
</script>

<?php require 'includes/footer.php'; ?>
