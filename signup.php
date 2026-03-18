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
#password-hint li { margin-bottom:3px; }
#strength { font-weight:bold; margin-top:5px; }
#confirm-hint { color:red; display:none; font-size:13px; }
.btn { display:flex; gap:10px; margin-top:10px; }
button { flex:1; padding:10px; border:none; border-radius:5px; cursor:pointer; font-size:14px; }
.button1 { background:#007bff; color:#fff; }
.button2 { background:#6c757d; color:#fff; }
.button3 { width:100%; margin-top:10px; background:#28a745; color:#fff; }

/* Google button */
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
    transition:0.3s;
}
.google-btn img { width:20px; margin-right:10px; }
.google-btn:hover { background:#f1f3f4; box-shadow:0 2px 6px rgba(0,0,0,0.2); }

@media (max-width:450px) {
    .card { width:90%; padding:20px; }
}
</style>
</head>

<body>

<div class="card">
<form id="signupForm" action="process_signup.php" method="POST">

    <div class="logo">
        <img src="./imgs/40b3a7667c57b37bb66735d67609798e-modified.png" alt="CineMa Ghar Logo">
    </div>

    <h3 id="heading">Create Account</h3>

    <div class="field">
        <input type="text" name="first_name" class="input-field" placeholder="First Name" required>
    </div>

    <div class="field">
        <input type="text" name="last_name" class="input-field" placeholder="Last Name" required>
    </div>

    <div class="field">
        <input type="text" name="contact" class="input-field" placeholder="Contact Number" required>
    </div>

    <div class="field">
        <input type="email" name="email" class="input-field" placeholder="Email" required>
    </div>

    <div class="field">
        <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
    </div>

    <!-- PASSWORD RULES -->
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

    <button type="button" onclick="window.location='login.php';" class="button3">Already have account?</button>

    <!-- Google Login -->
    <a href="google_login.php" class="google-btn">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAACUCAMAAAAj+tKkAAABNVBMVEX////qQzU0qFNChfT7vAUlefPH1vtNivXc5fzr8P5ilvY9g/T7ugD/vQDqQTP7uAAopUsdo0XpNCLpOyvpMB374uH86+r8wAD++Pj8zm/7tAD//PYzfvT+6cD3+f6Syp71+vb51tTwh4HymJP0q6f4y8n3xMHrT0MbdfOjvvmDxJHg7+Om06/W6toAnTPxj4noIgDoKRHvenPsW1HpNTf+7tD90oL82I3+8tz7wCr94rP8yFH8yF+Ss/j94Ka93sSEqvdYs25ErF5wvIH1trPtbmX7xXDvbBrziiL2oBnrTjPtWy/xeif1lhv3qRbvbytxn/aEqitZqk2/tS6XsD10rUbfuB+wszSHrkLOtie3zPruy3Km0bk1h9w/jdY6mKg5nZM0pWY6kcE6m540onQ5lbI8oYmd0Uy7AAAH7UlEQVR4nO2aaX/aRhDGJSFiO4CsC0xiasTtgBBXnIQ0qYyA5k7dpE3TtDl7fP+PUElgDmm1Wml3ES/6vPPP9vL3PDOzs7tmGAIqFvL5SmNWr/Wqtnq1er1ZqeQLRRJrY6vQqNeqo5baH6iqLCu2ZFlWB4MBOxr2as18kmzFYqM3YgXFxhJYnwT7G7LCtobNhEJZmQ3VviwIALZNSkEZ9Ef1xq4Zi/U5q4LiBpSiCqPeDs0uNIZ9dLplJOV+a1bYDV59PlCiwK3i2B/V6IexUGOVWHguoiJU6UaxUJfVSNZ6ZTtNM4r1Fh7eArFVo4TXIIDnIqpskwJeoRevNICIgx7xVGwKMik8RzLhIBaqfSLuriX0SQax0iIavoXkFqlyLtYGhMO3kKDWyfBV6fA5hCQaTn5Owd5rQnmInYh5mVhzAUkdYRJWWKp8NuEca1Jsku4uXiksHp9Mm2+E1Wro87WwMrBC3V88vjxLmU9gsfzNq5TrF7M+CiPafHhbcXFIb/9Y8OH5y9QGcT7UufWQZfBFg+dHMUeZRjQ+QVDUfr/fGlZrtqrDkdx3Ds7BmIKAx5dvRSlgRWZHw3plO+Xzs+qoJQeEUhEwd+AIA4yg9udBNy+VWVUGjWq4/jJ1ZIPtrKvlId2iWJgJvnO+gukvk0e9dhEG83p4L5vNt6OIHb/iEK0DCqqCdhtUbLIbB2pBqeDxMbM+Ep/CRpjXa6uxErs+mCKSwba7kQJRWfqstHDjx9RQDFYUhOTbUrHmzG6Kgn3UrKDMMHKcODTteiZwFO4htEB1HutzGqyM7S+TD68QQa7GXZzAVcKPT+6G+tvD/5jYuhifPA0hVJPkY+4d8yfPnkPjF9dfIjrN8jx/8vpFcBBlvHM2ru4f8w7hycsgPmWUJB7D8EudvLoLDCLmOQxbD49XhK+fgwjlWaJ8zK0svyLM/uQnTLaAGebBeA3In/AvvYSCkPAL9drhBeIzD59M43Ujir7P8tuE2/1GGSbMd3qb9+iE30zEQbIVbDvsA7QRn64rONEtxNGtrB/Qtpm9u8xA/FEJT6feFFwSHi9sTnSGcXUxBvE5iejMN4LQSBrQ02Q2EV/ZgAlvwsz1oAAmvP1ikHQPDErBa8Inif+b0ymEj+ezt1DXObtBQmf+hR8AuuBaxw9RAY8OztP4OvQvDGrTa40vkAFvZlLYSt/wLwypEdvhR6c7BSzd8S/8CJaD6ClICPCxf+ExDPD4h90CZq4AgLAUvI2cgoQAU751T+GAyHxkAFPnvnUfwGpkLwBhfNnxzgGPvOteQGvkUfKA30EB0bsMIUD/VgIHvLdzQN9WAge8/z8gJuDeW7z7IvEB7lmb8QPuWaMGAO7XVufvg/s2LPhn/r0atwBb3X4NrKlz/7Fur0Z+wLi1X4emVMm/8F4dOzMH/oWJHdyJnOoAh6aQqw/kvYTauRh6eZTjf9ZQAa8OkAQFTH8ArAypktz4jWQiAp4doumyBAMEXH0EX2Dmcm85jpsgAqL+HZewTDgAXB4FXgHn+F84R12igEcHEMDMTf9GEpiEufGvLp9YJgp4mIYEMHMFuB8MeIbIveOu1SEJCE3BDODuiAG36txvKz6JaAhhfKkMqIhBT2E5/j23lkQwhB+ggCVQCjL+x8TcuzcbfJzYJgd4Be/mAb/laTS537lticQK+RDKV7oM+LWtB+3r7rIpSyMECC0RcJt2tVHHufF7Hx+xOjk8h/FlMgEpuOmxJ/1WJqNueHDB54nSJbALulrxvQXh2YAWiUq+AzU4VQI3GVeLgSGXA9i7JCSwJR+l4CUM3IiXOs1mA+1dpuEUl+8MtgunwDf8a9079ncXDyFuN3wMNxhSw44uxhubWwAhXimHJCDwvLSpPwLTjwzhB2iHSYEfmTbVNUIBcVyG78GuICXiqi0iEE61eHx3wuKXylyGrdEN57MJJ3H64dlj2JS6dBhaIq7KCCHkRE6PzHd0EO5v6Wb4OhoKIMcZ04hB/PNjePxCesxSuoREKFpRgtiZSNan0AiWwIcRj7QpWgw5yTI1RLypYa9pfM6E3TqElfBCSHWyQJyijDfdNrcwRfoC3+YCJ1WvEE12fBZFHZ6LmjkRV45IX6E2p1EMdoVqsvuhdhiDzgIds81tdX5R+hZMCLoyChC6yYtP5SZt0xNIzYabWKLPC/GvdIDNmZvIAWQYE9nkFaRhcJNpuazr5XJ7atlfSiLQB+Pvj+AgltAqZKlyVMIF5VJgtOuf+voJ1BHT6Aa7DkVJw6h/h/iPv9+gtcANdSx6hJzxr3fsz6QCj3KBhBJFQsmbiOcoe5xH0Uo5opx+sxFE2EkuWCbC8Bpf0uc1IewoDCWkaLJj8/X8hTJkBRDGaTbohMv5phSlQ3sJqbosct/SzrNSfD6nUqjabHw5wImfI6rdxp1v8PicWZgmoWRh4tnS2vRKRWpr+ICMhj7ARpQR/WwIVpfKxixa5F6vOhRslqIeXOEyCfebOEd/uOwgEkSUpmQfJ12ZFimfRUPXyPPZ0okgilyZDp6tji7hIopSm4K7a2llDnokCo0e2doFIuqTuOUiWWWq0VshmhMjutOiwXlP9zSlT7gIcXQvH3ZI56prTkWkQIqGMdG72o7xXGndtuVecYFjufiG1Ta1ROhWkHp5OrEkY3Hn4ci9/jBEa9Iu66h3nHSlaZ1O19T1cttVWddNs9vpaCTW/g9Y8hjp7h0S2gAAAABJRU5ErkJggg==" alt="Google Icon">
        Sign in with Google
    </a>

</div>

<script>
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
    if(val.length>=10 && val.length<=15){lengthItem.textContent="10–15 characters ✅";lengthItem.style.color="green";score++;} else {lengthItem.textContent="10–15 characters ❌";lengthItem.style.color="red";}
    let nums=val.match(/\d/g);
    if(nums && nums.length>=2){numberItem.textContent="At least 2 numbers ✅";numberItem.style.color="green";score++;} else {numberItem.textContent="At least 2 numbers ❌";numberItem.style.color="red";}
    if(/[#@]/.test(val)){specialItem.textContent="At least one # or @ ✅";specialItem.style.color="green";score++;} else {specialItem.textContent="At least one # or @ ❌";specialItem.style.color="red";}
    if(val[0] && val[0]===val[0].toUpperCase()){capitalItem.textContent="First letter capital ✅";capitalItem.style.color="green";score++;} else {capitalItem.textContent="First letter capital ❌";capitalItem.style.color="red";}
    if(val.length===0){strengthText.textContent="";} else if(score<=2){strengthText.textContent="Strength: Weak 🔴";strengthText.style.color="red";} else if(score===3){strengthText.textContent="Strength: Medium 🟡";strengthText.style.color="orange";} else {strengthText.textContent="Strength: Strong 🟢";strengthText.style.color="green";}
    return score===4;
}

function checkConfirm(){ 
    if(confirmInput.value===passwordInput.value){confirmHint.style.display="none";return true;} 
    else {confirmHint.style.display="block";return false;}
}

passwordInput.addEventListener('input',()=>{hintDiv.style.display="block";checkPasswordRules(passwordInput.value);checkConfirm();});
confirmInput.addEventListener('input',checkConfirm);

form.addEventListener('submit',function(e){
    if(!checkPasswordRules(passwordInput.value)||!checkConfirm()){e.preventDefault();alert("Fix password errors first!");}
});
</script>

</body>
</html>