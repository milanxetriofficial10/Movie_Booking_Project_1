<?php
// footer.php
?>
</main>

<footer class="site-footer">
  <!-- Simple Divider Line -->
<div class="divider"></div>
  <div class="footer-top">
    <div class="container footer-flex">
      <!-- Logo + Description -->
      <div class="footer-logo">
        <img src="/Movie_Booking_Project_1/imgs/e32e183fd326fd5cd49ab3df467e54a8.jpg" alt="CineMagic Logo" class="logo-img">
        <p>Your ultimate destination for movie booking. Experience cinema like never before!</p>
      </div>

      <!-- Quick Links -->
      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/movie-booking/index.php">Home</a></li>
          <li><a href="/movie-booking/admin/index.php">Admin</a></li>
          <li><a href="/movie-booking/movies.php">Movies</a></li>
          <li><a href="/movie-booking/contact.php">Contact Us</a></li>
        </ul>
      </div>

      <!-- Social Media -->
      <div class="footer-social">
        <h4>Follow Us</h4>
        <div class="social-icons">
          <a href="#"><img src="imgs/download.png" alt="Facebook"></a>
          <a href="#"><img src="imgs/download (3).png" alt="Twitter"></a>
          <a href="#"><img src="imgs/download (1).png" alt="Instagram"></a>
          <a href="#"><img src="imgs/download (2).png" alt="YouTube"></a>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      © <?=date('Y')?> CineMa Ghar. All Rights Reserved.
    </div>
  </div>
</footer>

<style>
/* Footer Styles */
.site-footer {
  background: #0c0e13ff;
  color: #f5f5f5;
  font-family: 'Poppins', sans-serif;
  margin-top: 50px;
}

.site-footer a { color: #f5f5f5; text-decoration: none; transition: 0.3s; }
.site-footer a:hover { color: #93c5fd; }

.footer-top { padding: 40px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
.footer-flex { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px; }

.footer-logo img.logo-img { width: 120px; border-radius: 50%; margin-left: 80px; margin-bottom: 10px; }
.footer-logo p { max-width: 280px; font-size: 0.9rem; line-height: 1.5; color: #e0e7ff; }

.footer-links h4, .footer-social h4 { font-size: 1.1rem; margin-bottom: 12px; font-weight: 600; color: #fff; }
.footer-links ul { list-style: none; padding: 0; }
.footer-links ul li { margin-bottom: 8px; }
.footer-links ul li a { font-size: 0.95rem; }

.footer-social .social-icons { display: flex; gap: 18px;  margin-right: 100px; }
.footer-social .social-icons a img { width: 29px; transition: transform 0.3s; 
  }

.footer-social .social-icons a:hover img { transform: scale(1.2);  }

.footer-bottom { padding: 15px 0; text-align: center; font-size: 0.85rem; color: #c7d2fe; }

/* Responsive */
@media (max-width: 768px) {
  .footer-flex { flex-direction: column; align-items: center; text-align: center; }
  .footer-logo p { max-width: 100%; }
  .footer-social .social-icons { justify-content: center; }
}
/* Divider Line Style */
.divider {
  width: 100%;          /* लाइनको width */
  height: 3px;         /* लाइनको मोटाइ */
  background: #2563eb; /* लाइनको color */
  margin: 30px auto;   /* माथि तल spacing */
  border-radius: 1px;  /* अलिक smooth effect */
}

/* Optional: Hover effect */
.divider:hover {
  background: #d2f50cff;
  height: 4px;
  transition: all 0.3s ease;
}

</style>

<script src="/movie-booking/assets/js/app.js"></script>
</body>
</html>
