<?php
// about.php
require_once 'includes/db.php';
require_once 'includes/header.php';
$conn = db_connect();

// Fetch Cinema Name & Logo from DB (optional, you can hardcode too)
$cinema_res = $conn->query("SELECT logo, cinema_name, description FROM cinema_info LIMIT 1");
$cinema = $cinema_res->fetch_assoc();
$cinema_name = "My Cinema Ghar";
$logo = "imgs/e32e183fd326fd5cd49ab3df467e54a8.jpg";
$description = "Welcome to My Cinema Ghar. Enjoy latest movies in high quality!";

?>


<style>
body { font-family: Arial, sans-serif; background:#111; color:#fff; margin:0; padding:0; }
.about-container { max-width:1200px; margin:50px auto; padding:20px; display:flex; flex-wrap:wrap; gap:50px; align-items:center; justify-content:center; }
.about-left, .about-right { flex:1; min-width:300px; }
.about-left img { width:100%; max-width:400px; border-radius:20px; box-shadow:0 15px 35px rgba(255,255,255,0.2); animation: float 4s ease-in-out infinite; }
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}
.about-right h1 { font-size:3rem; margin-bottom:20px; color:#ffdd00; text-shadow: 2px 2px 10px #ff4c60; animation: slideIn 1.5s ease-out; }
.about-right p { font-size:1.1rem; line-height:1.6; color:#eee; animation: fadeIn 2s ease-out; }
.about-right .btn { display:inline-block; padding:12px 25px; margin-top:20px; background:#2563eb; color:#fff; border-radius:8px; text-decoration:none; font-weight:bold; transition:0.3s; animation: fadeIn 2.5s ease-out; }
.about-right .btn:hover { background:#1e40af; transform: translateY(-3px); box-shadow:0 8px 15px rgba(37,99,235,0.3); }

@keyframes slideIn { 0% { opacity:0; transform: translateX(-50px);} 100% { opacity:1; transform: translateX(0);} }
@keyframes fadeIn { 0% { opacity:0;} 100% { opacity:1;} }

@media(max-width:768px){
    .about-container { flex-direction:column; text-align:center; }
    .about-left img { margin-bottom:20px; }
}
</style>

<div class="about-container">
    <div class="about-left">
        <img src="<?=htmlspecialchars($logo)?>" alt="<?=htmlspecialchars($cinema_name)?>">
    </div>
    <div class="about-right">
        <h1>About <?=htmlspecialchars($cinema_name)?></h1>
        <p><?=nl2br(htmlspecialchars($description))?></p>
        <a class="btn" href="movies.php">Book Now</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
