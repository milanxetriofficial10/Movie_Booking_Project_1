<div class="card">
  <div class="card2">
    <form class="form" action="process_login.php" method="POST">
      <p id="heading">Login</p>

      <div class="field">
        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
          <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
        </svg>
        <input type="text" name="email_or_contact" class="input-field" placeholder="Email or Contact" autocomplete="off" required>
      </div>

      <div class="field">
        <svg viewBox="0 0 16 16" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg" class="input-icon">
          <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
        </svg>
        <input type="password" name="password" class="input-field" placeholder="Password" required>
      </div>

      <div class="btn">
        <button type="submit" name="login" class="button1">Login</button>
        <button type="button" onclick="window.location='signup.php';" class="button2">Sign Up</button>
      </div>

      <button type="button" onclick="alert('Contact admin to reset password');" class="button3">Forgot Password</button>
    </form>
  </div>
</div>

<style>
/* Body and card styling */
body {
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

/* Card container */
.card {
    background: linear-gradient(163deg, #00ff75 0%, #3700ff 100%);
    border-radius: 25px;
    max-width: 400px;
    width: 100%;
    padding: 25px;
    box-shadow: 0 12px 50px rgba(0, 0, 0, 0.5);
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
}

/* Inner card */
.card2 {
    background:  #814545ff;
    padding: 40px 35px;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Form heading */
#heading {
    text-align: center;
    font-size: 1.8em;
    color: #00ff75;
    font-weight: 700;
    margin-bottom: 15px;
}

/* Field container with icon inside */
.field {
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #2b2b2b;
    padding: 12px 15px;
    border-radius: 12px;
    box-shadow: inset 3px 5px 12px rgba(0,0,0,0.5);
    transition: all 0.3s ease;
    height: 28px;
    margin-top: 20px;
}

/* Input icon */
.input-icon {
    flex-shrink: 0;
    width: 1.5em;
    height: 1.5em;
    fill: #00ff75;
}

/* Input box styling */
.input-field {
    flex: 1;
    background: none;
    border: none;
    outline: none;
    font-size: 1em;
    color: #00ff75;
    padding: 8px 0;

}

.input-field::placeholder {
    color: rgba(0,255,117,0.6);
}

/* Input focus effect */
.field:focus-within {
    box-shadow: 0 0 10px #00ff75;
}

/* Buttons container */
.btn {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px;
}

/* Buttons styling */
.button1, .button2, .button3 {
    padding: 12px 28px;
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
    background: linear-gradient(163deg, #00642f 0%, #13034b 100%);
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
    display: block;
    width: 100%;
    margin-top: 20px;
    background: linear-gradient(163deg, #ff3366 0%, #ff66cc 100%);
    color: #fff;
}
.button3:hover {
    background: linear-gradient(163deg, #a00000 0%, #d10050 100%);
    color: #fff;
}

/* Responsive */
@media (max-width: 600px) {
    .card {
        width: 90%;
        padding: 20px;
    }
    .card2 {
        padding: 25px;
    }
    .btn {
        flex-direction: column;
        gap: 15px;
    }
    .button1, .button2, .button3 {
        width: 100%;
        text-align: center;
    }
}

</style>
