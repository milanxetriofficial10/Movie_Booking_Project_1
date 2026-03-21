<!-- NEW FONT AWESOME FIX -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<footer class="main-footer">
    <div class="footer-container">

        <!-- LEFT SIDE -->
        <div class="footer-left">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="#">Support</a></li>
                <li><a href="#">Contact</a></li>
            </ul>

            <br>
            <br>
            <br>
            <div class="wallets">
              <h3>Digital Wallets</h3>
                <img src="imgs/esewa_logo.png">
                <img src="imgs/logo1.png">
                <img src="imgs/global-ime-logo-svg.svg">
                <img src="imgs/new logo.jpeg">
            </div>
        </div>

        <!-- CENTER -->
        <div class="footer-center">
            <img src="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" class="footer-logo">

            <p class="center-text">
                डिजिटल यात्रामा तपाइँसँगै—  
                हाम्रो सेवा, तपाइँको भरोसा।
            </p>

        

            <!-- SOCIAL ICONS -->
            <div class="social-icons">
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="footer-right">
            <h3>About Our Service</h3>
            <p>
                समयसँगै बदलिंदै गएको डिजिटल संसारमा,  
                हामीले ल्याएका छौँ सहज, सरल र आधुनिक सेवाहरू।
            </p>
             <!-- ⭐ NEW CONTACT ROW -->
              <br>
    
               
    <div class="contact-info">
       <h3>About Our Service</h3>
        <p><i class="fa-solid fa-envelope"></i> milan@milan.com</p>
        <p><i class="fa-solid fa-phone"></i> +977 9818220754</p>
        <p><i class="fa-solid fa-location-dot"></i> Kathmandu, Nepal</p>
        </div>

    </div>
    


    <div class="footer-bottom">
        © 2025 All Rights Reserved | Developed with ♥ by Milan & Tej
    </div>
</footer>

<!-- FontAwesome Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<style>
  /* CONTACT INFO */
.contact-info{
    margin-top:15px;
    line-height:1.8;
}

.contact-info p{
    opacity:0.9;
    font-size:15px;
    display:flex;
    align-items:center;
    gap:10px;
}

.contact-info i{
    color:#4d9fff;
    font-size:18px;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Poppins",sans-serif;
}

.main-footer{
     background:
        linear-gradient(rgba(26, 8, 8, 0.99), rgba(0, 0, 0, 0.95)),
        url("https://i.pinimg.com/1200x/2a/47/dd/2a47dd95666a2d396df9afe1d3ff3641.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    color:white;
    background-size: cover;
    background-position: center;px 0 20px;
     border-radius:10px;
     border: 1px solid #3af307; 
     height: 400px;

}

.footer-container{
    width:90%;
    margin-top: 20px;
    margin-left: 70px;
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:40px;
}

/* LEFT */
.footer-left h3{
    margin-bottom:10px;
    color: rgba(29, 255, 9, 1);
}
.footer-left ul{
    list-style:none;
    display:grid;
    grid-template-columns: repeat(2, auto);  /* 2 columns */
    gap:8px 20px;  /* row gap , column gap */
    padding-left:5px;
}

.footer-left ul li a{
    color:#08f3ccff;
    font-size:17px;
    font-weight:500;
    text-decoration: none;  /* underline हटाउन यो line */
    transition:0.3s;
}

.footer-left ul li a:hover{
    color:white;
    transform:translateX(4px);
}


/* Wallet */
.wallets img{
    width:70px;
    margin-right:10px;
    margin-top:10px;
    transition:0.3s;
}
.wallets img:hover{
    transform:scale(1.1);
}

/* CENTER */
.footer-center{
    text-align:center;
    flex:1;
}
.footer-logo{
    width:120px;
    margin-bottom:10px;
     border-radius:30px;
    animation:float 3s infinite ease-in-out;
}
@keyframes float{
    0%{transform:translateY(0);}
    50%{transform:translateY(-8px);}
    100%{transform:translateY(0);}
}

.center-text{
    margin-top:30px;
    opacity:0.9;
}

/* ⭐ Social Icons */
.social-icons{
    margin-top:20px;
}
.social-icons a{
    color: #082192ff;
    font-size:26px;
    margin:0 10px;
    transition:0.3s;
}
.social-icons a:hover{
    color:#64b5ff;
    transform:scale(1.2);
}

/* RIGHT */
.footer-right{
    max-width:260px;
}
.footer-right h3{
    margin-bottom:10px;
    color: rgba(29, 255, 9, 1);
}
.footer-right p{
    opacity:0.9;
}

/* Bottom */
.footer-bottom{
    width:100%;
    text-align:center !important;
    display:block;
    margin-top: 45px;
}


/* Responsive */
@media (max-width:768px){
    .footer-container{
        flex-direction:column;
        text-align:center;
    }
}

</style>
<script>
function subscribeEmail(){
    let email = document.getElementById("subEmail").value;
    let msg = document.getElementById("subMessage");

    if(email === ""){
        msg.style.color = "orange";
        msg.innerHTML = "Please enter your email!";
    } else {
        msg.style.color = "#4dff73";
        msg.innerHTML = "Thank you for subscribing! 💌";
    }
}

</script>