<?php
require 'includes/header.php';
require 'includes/db.php';
$conn = db_connect();

$message = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $full_name = $_POST['full_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $user_id   = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO contact_info (user_id, full_name, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $full_name, $email, $phone);

    if($stmt->execute()){
        $message = "✨ Contact information saved successfully!";
    }else{
        $message = "⚠️ Something went wrong. Try again.";
    }
}
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

/* ===== ONLY CONTACT PAGE SCOPE ===== */
.contact-scope{
    font-family:'Poppins',sans-serif;
    background:radial-gradient(circle at top,#1e293b,#020617);
    color:#fff;
    padding:60px 20px;
}

/* ===== Layout ===== */
.contact-wrapper{
    max-width:1100px;
    margin:auto;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:30px;
    animation:fadeUp 1s ease;
}

/* ===== Card ===== */
.contact-card{
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(10px);
    border-radius:20px;
    box-shadow:0 20px 50px rgba(0,0,0,.6);
    padding:35px;
    position:relative;
    overflow:hidden;
}

.contact-card::before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(120deg,transparent,rgba(255,255,255,.08),transparent);
    animation:shine 6s linear infinite;
}

/* ===== Headings ===== */
.contact-card h2,
.contact-card h3{
    text-align:center;
    margin-bottom:20px;
    background:linear-gradient(90deg,#38bdf8,#818cf8);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

/* ===== FORM ELEMENTS (SCOPED) ===== */
.contact-scope label{
    font-weight:500;
}

.contact-scope input{
    width:100%;
    padding:13px;
    margin:10px 0 20px;
    border:none;
    border-radius:12px;
    background:rgba(255,255,255,.12);
    color:#fff;
    font-size:1rem;
    transition:.3s;
}

.contact-scope input:focus{
    outline:none;
    box-shadow:0 0 0 2px rgba(56,189,248,.6);
    background:rgba(255,255,255,.18);
}

.contact-scope button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    background:linear-gradient(90deg,#3b82f6,#2563eb);
    color:#fff;
    font-size:1.1rem;
    font-weight:700;
    cursor:pointer;
    transition:.4s;
}

.contact-scope button:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 35px rgba(59,130,246,.6);
}

/* ===== Message ===== */
.contact-message{
    text-align:center;
    margin-bottom:15px;
    font-size:1rem;
    color:#7dd3fc;
    animation:pulse .8s ease;
}

/* ===== Map ===== */
.map-frame{
    width:100%;
    height:360px;
    border:none;
    border-radius:14px;
    filter:grayscale(15%) contrast(1.1);
}

/* ===== Animations ===== */
@keyframes fadeUp{
    from{opacity:0;transform:translateY(40px);}
    to{opacity:1;transform:none;}
}
@keyframes pulse{
    0%{transform:scale(.96);}
    50%{transform:scale(1.02);}
    100%{transform:scale(1);}
}
@keyframes shine{
    0%{transform:translateX(-100%);}
    100%{transform:translateX(100%);}
}

/* ===== Responsive ===== */
@media(max-width:900px){
    .contact-wrapper{
        grid-template-columns:1fr;
    }
}
</style>

<div class="contact-scope">
    <div class="contact-wrapper">

        <!-- ===== Contact Form ===== -->
        <div class="contact-card">
            <h2>Contact Information</h2>

            <?php if($message): ?>
                <div class="contact-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label>Full Name</label>
                <input type="text" name="full_name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Phone Number</label>
                <input type="text" name="phone" required>

                <button type="submit">Save Contact Info</button>
            </form>
        </div>

        <!-- ===== Map ===== -->
        <div class="contact-card">
            <h3>📍 Pashupati Multiple Campus</h3>
            <iframe 
                class="map-frame"
                src="https://www.google.com/maps?q=Pashupati%20Multiple%20Campus%20Kathmandu&output=embed"
                loading="lazy">
            </iframe>
        </div>

    </div>
</div>

<?php require 'includes/footer.php'; ?>
