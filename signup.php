<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="./imgs/40b3a7667c57b37bb66735d67609798e-modified.png" type="image/x-icon">
<title>CineMa Ghar - Sign Up</title>

<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: Arial, sans-serif;
    background:#4a4a4b;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.card {
    background:#fff;
    width:400px;
    padding:30px;
    border-radius:8px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}
.logo { text-align:center; margin-bottom:10px; }
.logo img { width:80px; }
#heading { text-align:center; margin-bottom:15px; font-size:22px; color:#333; }
.field { margin-bottom:12px; }
.input-field { width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; }
.input-field:focus { border-color:#007bff; outline:none; }

#password-hint { font-size:13px; margin-top:5px; }
#strength { font-weight:bold; margin-top:5px; }
#confirm-hint { color:red; display:none; font-size:13px; }

.btn { display:flex; gap:10px; margin-top:10px; }
button { flex:1; padding:10px; border:none; border-radius:5px; cursor:pointer; font-size:14px; }
.button1 { background:#007bff; color:#fff; }
.button2 { background:#6c757d; color:#fff; }
.button3 { width:100%; margin-top:10px; background:#28a745; color:#fff; }

.google-btn {
    display:flex;
    align-items:center;
    justify-content:center;
    width:100%;
    margin-top:15px;
    padding:10px;
    background:#fff;
    border:1px solid #ddd;
    border-radius:5px;
    cursor:pointer;
    text-decoration:none;
}
.google-btn img { width:20px; margin-right:10px; }

@media (max-width:450px) {
    .card { width:90%; padding:20px; }
}
</style>
</head>

<body>

<div class="card">
<form id="signupForm" action="process_signup.php" method="POST">

    <div class="logo">
        <img src="./imgs/40b3a7667c57b37bb66735d67609798e-modified.png">
    </div>

    <h3 id="heading">Create Account</h3>

    <!-- NAME -->
    <div class="field">
        <input type="text" id="first_name" name="first_name" class="input-field" placeholder="First Name" required>
    </div>

    <div class="field">
        <input type="text" id="last_name" name="last_name" class="input-field" placeholder="Last Name" required>
    </div>

    <!-- CONTACT -->
    <div class="field">
        <input type="text" id="contact" name="contact" class="input-field" placeholder="Contact Number" required>
    </div>

    <div class="field">
        <input type="email" name="email" class="input-field" placeholder="Email" required>
    </div>

    <div class="field">
        <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
    </div>

    <div id="password-hint" style="display:none;">
        <ul>
            <li id="length">10–15 characters ❌</li>
            <li id="number">At least 2 numbers ❌</li>
            <li id="special">At least one # or @ ❌</li>
            <li id="capital">First letter capital ❌</li>
        </ul>
        <p id="strength"></p>
    </div>

    <div class="field">
        <input type="password" id="confirm_password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
        <div id="confirm-hint">Passwords do not match ❌</div>
    </div>

    <div class="btn">
        <button type="submit" class="button1">Sign Up</button>
        <button type="reset" class="button2">Reset</button>
    </div>

    <button type="button" onclick="window.location='login.php';" class="button3">
        Already have account?
    </button>

</form>
</div>

<script>
const first = document.getElementById("first_name");
const last = document.getElementById("last_name");
const contact = document.getElementById("contact");

// 🚫 NAME: only letters allowed
[first, last].forEach(input => {
    input.addEventListener("input", function () {
        this.value = this.value.replace(/[^A-Za-z]/g, "");
    });
});

// 🚫 CONTACT: only numbers + max 10 digits
contact.addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, "").slice(0, 10);
});

const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const hintDiv = document.getElementById('password-hint');
const confirmHint = document.getElementById('confirm-hint');
const form = document.getElementById('signupForm');

const lengthItem = document.getElementById('length');
const numberItem = document.getElementById('number');
const specialItem = document.getElementById('special');
const capitalItem = document.getElementById('capital');
const strengthText = document.getElementById('strength');

function checkPasswordRules(val){
    let score = 0;

    if(val.length>=10 && val.length<=15){
        lengthItem.textContent="10–15 characters ✅";
        lengthItem.style.color="green"; score++;
    } else {
        lengthItem.textContent="10–15 characters ❌";
        lengthItem.style.color="red";
    }

    let nums=val.match(/\d/g);
    if(nums && nums.length>=2){
        numberItem.textContent="At least 2 numbers ✅";
        numberItem.style.color="green"; score++;
    } else {
        numberItem.textContent="At least 2 numbers ❌";
        numberItem.style.color="red";
    }

    if(/[#@]/.test(val)){
        specialItem.textContent="At least one # or @ ✅";
        specialItem.style.color="green"; score++;
    } else {
        specialItem.textContent="At least one # or @ ❌";
        specialItem.style.color="red";
    }

    if(val[0] && val[0]===val[0].toUpperCase()){
        capitalItem.textContent="First letter capital ✅";
        capitalItem.style.color="green"; score++;
    } else {
        capitalItem.textContent="First letter capital ❌";
        capitalItem.style.color="red";
    }

    return score===4;
}

function checkConfirm(){
    if(confirmInput.value===passwordInput.value){
        confirmHint.style.display="none";
        return true;
    } else {
        confirmHint.style.display="block";
        return false;
    }
}

passwordInput.addEventListener('input',()=>{
    hintDiv.style.display="block";
    checkPasswordRules(passwordInput.value);
    checkConfirm();
});

confirmInput.addEventListener('input',checkConfirm);

form.addEventListener('submit',function(e){
    if(!checkPasswordRules(passwordInput.value) || !checkConfirm()){
        e.preventDefault();
        alert("Fix validation errors first!");
    }
});
</script>

</body>
</html>