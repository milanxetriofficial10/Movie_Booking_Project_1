<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="form-wrapper">
    <h2>Create Account</h2>

    <form action="process_signup.php" method="POST">

        <div class="form-group">
            <input type="text" name="first_name" placeholder="First Name" required>
        </div>

        <div class="form-group">
            <input type="text" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="form-group">
            <input type="text" name="address" placeholder="Address" required>
        </div>

        <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="form-group">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>

        <button class="btn">Sign Up</button>

        <div class="social-box">
            <p>Or continue with</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-google"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
            </div>
        </div>

        <div class="switch-link">
            Already have an account? <a href="login.php">Login</a>
        </div>

    </form>
</div>

</body>
</html>
<style>
    /* Reset */
*{
    margin:0; padding:0;
    box-sizing:border-box;
    font-family: "Poppins", sans-serif;
}

body{
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background: linear-gradient(135deg,#0c0f26,#131b3d,#1e2b55);
    background-size:300% 300%;
    animation: bgShift 8s infinite alternate;
}

@keyframes bgShift{
    0%{ background-position: 0% 50%; }
    100%{ background-position: 100% 50%; }
}

.form-wrapper{
    width:400px;
    padding:35px 30px;
    border-radius:20px;
    background: rgba(255,255,255,0.07);
    backdrop-filter: blur(10px);
    box-shadow:0 0 25px rgba(0,0,0,0.4);
    color:white;
    animation: fadeIn 1.2s ease;
}

@keyframes fadeIn {
    0%{ transform:scale(0.6); opacity:0; }
    100%{ transform:scale(1); opacity:1; }
}

.form-wrapper h2{
    text-align:center;
    margin-bottom:20px;
    color:#ffeb71;
    text-shadow:0 0 10px rgba(255,240,120,0.7);
}

.form-group{
    margin-bottom:15px;
}

input{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background: rgba(255,255,255,0.15);
    color:white;
    transition:0.3s;
}

input:focus{
    background: rgba(255,255,255,0.22);
    box-shadow:0 0 10px #ffeb71;
    outline:none;
}

.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#ffeb71;
    color:black;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    transform:scale(1.06);
    box-shadow:0 0 15px #ffeb71;
}

/* Social Icons */
.social-box{
    text-align:center;
    margin-top:20px;
}

.social-box p{
    margin-bottom:10px;
}

.social-icons{
    display:flex;
    justify-content:center;
    gap:20px;
}

.social-icons a{
    width:45px;
    height:45px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:rgba(255,255,255,0.15);
    font-size:1.3rem;
    color:white;
    text-decoration:none;
    transition:0.3s;
}

.social-icons a:hover{
    transform:scale(1.15);
    background:#ffeb71;
    color:black;
}

.switch-link{
    margin-top:18px;
    text-align:center;
}

.switch-link a{
    color:#8bd2ff;
    text-decoration:none;
}
.switch-link a:hover{
    text-shadow:0 0 8px #8bd2ff;
}

</style>