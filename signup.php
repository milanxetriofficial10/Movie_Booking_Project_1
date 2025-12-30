<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up</title>
<style>
/* Global Styles */
body {
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 110vh;
    margin: 0;
}

/* Card Container */
.card {
    background: linear-gradient(163deg, #00ff75 0%, #3700ff 100%);
    border-radius: 25px;
    padding: 25px;
    box-shadow: 0 12px 50px rgba(0, 0, 0, 0.5);
    width: 490px;
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
}

/* Inner Card */
.card2 {
    background: #814545ff;
    padding: 40px 35px;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    gap: 25px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.3);
}

/* Form Heading */
#heading {
    text-align: center;
    font-size: 1.9em;
    color: #00ff75;
    font-weight: 700;
    margin-bottom: 15px;
}

/* Row for side-by-side fields */
.row {
    display: flex;
    gap: 20px;
}

.row .input-field {
    flex: 1;
}

/* Field Styling */
.field {
    display: flex;
    flex-direction: column;
    position: relative;
}

.input-field {
    padding: 14px 17px;
    border-radius: 10px;
    margin-top: 10px;
    border: none;
    outline: none;
    font-size: 1.05em;
    color: #00ff75;
    background-color: #2b2b2b;
    box-shadow: inset 3px 5px 12px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
    width: 90%;
}

.input-field::placeholder {
    color: rgba(0, 255, 117, 0.7);
}

.input-field:focus {
    box-shadow: inset 3px 5px 15px rgba(0, 255, 117, 0.7);
}

/* Password Hint Styling */
#password-hint {
    font-size: 0.85em;
    margin-top: 5px;
}

#password-hint ul {
    padding-left: 1em;
    margin: 0;
}

#password-hint li {
    margin-bottom: 4px;
    transition: all 0.3s ease;
}

/* Confirm password hint */
#confirm-hint {
    font-size: 0.85em;
    color: #ff4c4c;
    margin-top: 3px;
    display: none;
}

/* Buttons Styling */
.btn {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.button1, .button2, .button3 {
    padding: 14px 28px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 1em;
    transition: all 0.4s ease;
}

.button1 {
    background: linear-gradient(163deg, #00ff75 0%, #3700ff 100%);
    color: #000;
}
.button1:hover {
    background: linear-gradient(163deg, #00aa55 0%, #2400aa 100%);
    color: #fff;
}

.button2 {
    background: linear-gradient(163deg, #ffcc00 0%, #ff6600 100%);
    color: #000;
}
.button2:hover {
    background: linear-gradient(163deg, #ffaa00 0%, #cc3300 100%);
    color: #fff;
}

.button3 {
    background: linear-gradient(163deg, #ff3366 0%, #ff66cc 100%);
    color: #fff;
    width: 100%;
    margin-top: 20px;
}
.button3:hover {
    background: linear-gradient(163deg, #cc0033 0%, #ff33aa 100%);
}

/* Responsive */
@media (max-width: 550px) {
    .card {
        width: 90%;
        padding: 20px;
    }
    .card2 {
        padding: 25px;
    }
    .row {
        flex-direction: column;
    }
    .input-field {
        font-size: 0.95em;
        padding: 12px 14px;
    }
    .button1, .button2, .button3 {
        font-size: 0.95em;
        padding: 12px 18px;
    }
}

</style>
</head>
<body>

<div class="card">
  <div class="card2">
    <form class="form" id="signupForm" action="process_signup.php" method="POST">
      <p id="heading">Sign Up</p>

      <div class="field">
        
        <input type="text" name="first_name" class="input-field" placeholder="First Name" required>
      </div>
      <div class="field">
        <input type="text" name="last_name" class="input-field" placeholder="Last Name" required>
      </div>
      <div class="field">
        <input type="text" name="address" class="input-field" placeholder="Address" required>
      </div>
      <div class="field">

        <input type="email" name="email" class="input-field"  placeholder="Email" required>
        
      </div>

      <!-- Password -->
      <div class="field">
        <input type="password" id="password" name="password" class="input-field" placeholder="Password" required>
      </div>
      <div id="password-hint" style="display:none; color:rgb(0,255,200);">
        <ul>
          <li id="length">At least 10 characters ❌</li>
          <li id="number">At least one number ❌</li>
          <li id="special">At least one * or # ❌</li>
          <li id="capital">First letter capital ❌</li>
        </ul>
      </div>

      <!-- Confirm Password -->
      <div class="field">
        <input type="password" id="confirm_password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
        <div id="confirm-hint">Passwords do not match ❌</div>
      </div>

      <div class="btn">
        <button type="submit" class="button1">Sign Up</button>
        <button type="reset" class="button2">Reset</button>
      </div>

      <button type="button" onclick="window.location='login.php';" class="button3">Already have an account?</button>
    </form>
  </div>
</div>

<script>
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const hintDiv = document.getElementById('password-hint');
const lengthItem = document.getElementById('length');
const numberItem = document.getElementById('number');
const specialItem = document.getElementById('special');
const capitalItem = document.getElementById('capital');
const confirmHint = document.getElementById('confirm-hint');
const form = document.getElementById('signupForm');

function checkPasswordRules(val){
    let valid = true;

    if(val.length >= 10){
        lengthItem.textContent = "At least 10 characters ✅";
        lengthItem.style.color = "lightgreen";
    } else {
        lengthItem.textContent = "At least 10 characters ❌";
        lengthItem.style.color = "rgb(0,255,200)";
        valid = false;
    }

    if(/\d/.test(val)){
        numberItem.textContent = "At least one number ✅";
        numberItem.style.color = "lightgreen";
    } else {
        numberItem.textContent = "At least one number ❌";
        numberItem.style.color = "rgb(0,255,200)";
        valid = false;
    }

    if(/[*#]/.test(val)){
        specialItem.textContent = "At least one * or # ✅";
        specialItem.style.color = "lightgreen";
    } else {
        specialItem.textContent = "At least one * or # ❌";
        specialItem.style.color = "rgb(0,255,200)";
        valid = false;
    }

    if(val[0] && val[0] === val[0].toUpperCase()){
        capitalItem.textContent = "First letter capital ✅";
        capitalItem.style.color = "lightgreen";
    } else {
        capitalItem.textContent = "First letter capital ❌";
        capitalItem.style.color = "rgb(0,255,200)";
        valid = false;
    }

    return valid;
}

// Show hints while typing
passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    hintDiv.style.display = val.length > 0 ? "block" : "none";
    checkPasswordRules(val);
    checkConfirmPassword();
});

function checkConfirmPassword(){
    if(confirmInput.value.length === 0){
        confirmHint.style.display = "none";
        return true;
    }
    if(confirmInput.value === passwordInput.value){
        confirmHint.style.display = "none";
        return true;
    } else {
        confirmHint.style.display = "block";
        return false;
    }
}

confirmInput.addEventListener('input', checkConfirmPassword);

// Prevent form submit if password invalid
form.addEventListener('submit', function(e){
    const val = passwordInput.value;
    if(!checkPasswordRules(val) || !checkConfirmPassword()){
        e.preventDefault();
        alert("Password does not meet all requirements or passwords do not match!");
    }
});
</script>

</body>
</html>
