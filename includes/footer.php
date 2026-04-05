<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
body {
    font-family: 'Poppins', Arial, sans-serif;
    background:
        linear-gradient(rgba(26,8,8,0.80), rgba(0,0,0,0.80)),
        url("./imgs/moviebackgrund.jpg");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    color: #fff;
}
    

        .demo-space {
            flex: 1;
            min-height: 80px;
        }

        /* Main Footer */
        .main-footer {
            position: relative;
            background:
                linear-gradient(125deg, rgba(8, 5, 5, 0.97), rgba(0, 0, 0, 0.96)),
                url("https://i.pinimg.com/1200x/2a/47/dd/2a47dd95666a2d396df9afe1d3ff3641.jpg");
            background-size: cover;
            background-position: center 30%;
            background-attachment: fixed;
            color: #fff;
            border-top: 1px solid rgba(58, 243, 7, 0.3);
            overflow: hidden;
            transition: all 0.2s ease;
        }

        /* footer topline */
        .footer-topline {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3af307, #00e5ff, #ff4c60, #ffb347, #3af307, transparent);
            background-size: 300% 100%;
            animation: topline-move 4s linear infinite;
        }

        @keyframes topline-move {
            0% { background-position: 0% 0%; }
            100% { background-position: 300% 0%; }
        }

        .footer-container {
            width: 90%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 48px 0 32px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 40px 30px;
        }



        .footer-left h3,
        .footer-right h3,
        .wallets h3,
        .contact-info h3 {
            margin-bottom: 18px;
            color: #3af307;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .footer-left h3::after,
        .footer-right h3::after,
        .wallets h3::after,
        .contact-info h3::after {
            content: '';
            width: 28px;
            height: 2px;
            background: #3af307;
            display: inline-block;
            border-radius: 4px;
            opacity: 0.7;
        }

        /* left section */
        .footer-left {
            min-width: 200px;
        }

        .footer-left ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(2, auto);
            gap: 12px 28px;
        }

        .footer-left ul li a {
            color: #caf0f8;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.25s ease-in-out;
        }

        /* dynamic icon color */
        .footer-left ul li:nth-child(1) a i { color: #ffb347; }
        .footer-left ul li:nth-child(2) a i { color: #4d9fff; }
        .footer-left ul li:nth-child(3) a i { color: #ff4c60; }
        .footer-left ul li:nth-child(4) a i { color: #3af307; }
        .footer-left ul li:nth-child(5) a i { color: #e1306c; }

        .footer-left ul li a i {
            font-size: 1rem;
            width: 24px;
            text-align: center;
            transition: transform 0.25s ease, color 0.2s;
        }

        .footer-left ul li a:hover {
            color: #ffffff;
            transform: translateX(6px);
        }

        .footer-left ul li a:hover i {
            transform: scale(1.2);
            filter: drop-shadow(0 0 4px currentColor);
        }

        /* digital section */
        .wallets {
            margin-top: 32px;
        }

        .wallet-logos {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 10px;
        }

        .wallet-logos img {
            width: 68px;
            height: 40px;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 6px 8px;
            border: 1px solid rgba(58, 243, 7, 0.2);
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .wallet-logos img:hover {
            transform: translateY(-6px) scale(1.07);
            border-color: #3af307;
            box-shadow: 0 12px 22px rgba(58, 243, 7, 0.35);
            background: #fff;
        }

        /* center section */
        .footer-center {
            text-align: center;
            flex: 1;
            min-width: 240px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-glow-wrap {
            display: inline-block;
            transition: filter 0.3s;
        }

        .footer-logo {
            width: 118px;
            height: 118px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            border: 2px solid rgba(58, 243, 7, 0.6);
            box-shadow: 0 0 10px rgba(58, 243, 7, 0.2);
            transition: all 0.4s ease;
            background: #111;
        }

        .footer-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 0 22px #3af307, 0 0 8px #00e5ff;
            border-color: #fff;
        }

        .center-text {
            margin-top: 22px;
            font-size: 0.9rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
            max-width: 260px;
            backdrop-filter: blur(2px);
        }

        /* social icons  */
        .social-icons {
            margin-top: 28px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: 1.5px solid rgba(255, 255, 255, 0.2);
            background: rgba(20, 20, 30, 0.6);
            transition: all 0.35s cubic-bezier(0.2, 1.2, 0.4, 1);
            backdrop-filter: blur(2px);
        }

        .social-icons svg {
            width: 22px;
            height: 22px;
            transition: transform 0.2s;
        }

        .soc-fb { color: #1877f2; }
        .soc-fb:hover { background: #1877f2; color: white; border-color: #1877f2; box-shadow: 0 8px 20px rgba(24, 119, 242, 0.5); transform: translateY(-6px) rotate(-3deg) scale(1.12); }

        .soc-ig { color: #e1306c; }
        .soc-ig:hover { background: radial-gradient(circle at 30% 110%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); color: white; border-color: #fff; box-shadow: 0 8px 22px rgba(225,48,108,0.6); transform: translateY(-6px) rotate(3deg) scale(1.12); }

        .soc-yt { color: #ff0000; }
        .soc-yt:hover { background: #ff0000; color: white; border-color: #ff0000; box-shadow: 0 8px 22px rgba(255,0,0,0.55); transform: translateY(-6px) rotate(-3deg) scale(1.12); }

        .soc-tt { color: #ffffff; }
        .soc-tt:hover { background: #010101; color: #69c9d0; border-color: #69c9d0; box-shadow: 0 8px 22px rgba(105,201,208,0.5); transform: translateY(-6px) rotate(3deg) scale(1.12); }

        .social-icons a:hover svg {
            transform: scale(1.1);
        }

        /* right */
        .footer-right {
            max-width: 290px;
        }

        .about-text {
            font-size: 0.85rem;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.78);
            margin-bottom: 22px;
            font-weight: 400;
        }

        .contact-info {
            margin-top: 10px;
        }

        .contact-info p {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.9rem;
            color: rgba(245, 245, 255, 0.85);
            margin-bottom: 14px;
            transition: all 0.25s;
            cursor: default;
        }

        .contact-info p:hover {
            color: #ffffff;
            transform: translateX(5px);
        }

        .ci-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }

        .contact-info p:hover .ci-icon {
            transform: scale(1.12);
            filter: brightness(1.2);
        }

        .ci-icon.mail {
            background: rgba(77, 159, 255, 0.2);
            color: #4d9fff;
            border: 1px solid rgba(77, 159, 255, 0.5);
            box-shadow: 0 0 6px rgba(77, 159, 255, 0.2);
        }

        .ci-icon.phone {
            background: rgba(58, 243, 7, 0.18);
            color: #3af307;
            border: 1px solid rgba(58, 243, 7, 0.5);
        }

        .ci-icon.loc {
            background: rgba(255, 76, 96, 0.2);
            color: #ff4c60;
            border: 1px solid rgba(255, 76, 96, 0.5);
        }

        .contact-info a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }

        .contact-info a:hover {
            text-decoration: underline;
            color: #3af307;
        }

        /* footer bottom  this */
        .footer-bottom {
            text-align: center;
            padding: 20px 0 24px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.45);
            border-top: 1px solid rgba(58, 243, 7, 0.2);
            margin-top: 12px;
            letter-spacing: 0.4px;
            font-weight: 400;
        }

        .heart {
            color: #ff4c60;
            display: inline-block;
            animation: heartbeat 1.2s ease-in-out infinite;
            margin: 0 2px;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            14% { transform: scale(1.35); }
            28% { transform: scale(1); }
            42% { transform: scale(1.2); }
            70% { transform: scale(1); }
        }

        /* resp css */
        @media (max-width: 880px) {
            .footer-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 48px;
            }
            .footer-left ul {
                justify-content: center;
                grid-template-columns: repeat(2, minmax(100px, auto));
            }
            .footer-left ul li a {
                justify-content: center;
            }
            .wallet-logos {
                justify-content: center;
            }
            .contact-info p {
                justify-content: center;
            }
            .footer-right {
                max-width: 100%;
                text-align: center;
            }
            .about-text {
                max-width: 90%;
                margin-left: auto;
                margin-right: auto;
            }
            .footer-left h3::after,
            .footer-right h3::after,
            .wallets h3::after,
            .contact-info h3::after {
                display: none;
            }
            .footer-center .center-text {
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 500px) {
            .footer-left ul {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .wallet-logos img {
                width: 60px;
                height: 36px;
            }
            .social-icons a {
                width: 42px;
                height: 42px;
            }
        }
    </style>
</head>
<body>
<div class="demo-space"></div>

<!-- main footer  -->
<footer class="main-footer">
    <div class="footer-topline"></div>

    <div class="footer-container">
        <!-- Navbar bar feature -->
        <div class="footer-left">
            <h3><i class="fas fa-bolt" style="font-size: 0.9rem;"></i> Quick Links</h3>
            <ul>
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fas fa-info-circle"></i> About Us</a></li>
                <li><a href="#"><i class="fas fa-tags"></i> Products</a></li>
                <li><a href="#"><i class="fas fa-headset"></i> Support</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>

            <div class="wallets">
                <h3><i class="fas fa-wallet"></i> Digital Wallets</h3>
                <div class="wallet-logos">
                    <img src="imgs/esewa_logo.png" alt="eSewa" title="eSewa">
                    <img src="imgs/logo1.png" alt="Wallet" title="Digital Wallet">
                    <img src="imgs/global-ime-logo-svg.svg" alt="Global IME" title="Global IME Pay">
                    <img src="imgs/new logo.jpeg" alt="Wallet Partner" title="Partner Wallet">
                </div>
            </div>
        </div>

        <!-- logo and social icon -->
        <div class="footer-center">
            <div class="logo-glow-wrap">
                <img src="imgs/40b3a7667c57b37bb66735d67609798e-modified.png" class="footer-logo" alt="Brand Logo">
            </div>
            <p class="center-text">
                डिजिटल यात्रामा तपाइँसँगै —<br>
                हाम्रो सेवा, तपाइँको भरोसा।
            </p>

            <div class="social-icons">
                <a href="#" class="soc-fb" title="Facebook" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.898v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                </a>
                <a href="#" class="soc-ig" title="Instagram" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="#" class="soc-yt" title="YouTube" aria-label="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a href="#" class="soc-tt" title="TikTok" aria-label="TikTok">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.19 8.19 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/></svg>
                </a>
            </div>
        </div>

        <!-- service this -->
        <div class="footer-right">
            <h3><i class="fas fa-globe"></i> About Our Service</h3>
            <p class="about-text">
                समयसँगै बदलिंदै गएको डिजिटल संसारमा,<br>
                हामीले ल्याएका छौँ सहज, सरल र आधुनिक सेवाहरू।
            </p>

            <div class="contact-info">
                <h3><i class="fas fa-address-card"></i> Contact Us</h3>
                <p>
                    <span class="ci-icon mail">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M1.5 4.5a3 3 0 0 1 3-3h15a3 3 0 0 1 3 3v15a3 3 0 0 1-3 3h-15a3 3 0 0 1-3-3v-15zm3-1.5a1.5 1.5 0 0 0-1.5 1.5v.667l9 5.25 9-5.25V4.5A1.5 1.5 0 0 0 19.5 3h-15zM21 7.434l-8.535 4.979a1.5 1.5 0 0 1-1.93 0L3 7.434V19.5A1.5 1.5 0 0 0 4.5 21h15a1.5 1.5 0 0 0 1.5-1.5V7.434z"/></svg>
                    </span>
                    <a href="mailto:milan@milan.com" style="text-decoration: none; border-bottom: 1px dotted rgba(58,243,7,0.4);">milan@milan.com</a>
                </p>
                <p>
                    <span class="ci-icon phone">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/></svg>
                    </span>
                    <a href="tel:+9779818220754" style="text-decoration: none; border-bottom: 1px dotted rgba(58,243,7,0.4);">+977 9818220754</a>
                </p>
                <p>
                    <span class="ci-icon loc">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-2.003 3.5-4.697 3.5-8.327a8.25 8.25 0 0 0-16.5 0c0 3.63 1.556 6.324 3.5 8.327a19.583 19.583 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.144.742zM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" clip-rule="evenodd"/></svg>
                    </span>
                    Kathmandu, Nepal
                </p>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        © 2025 All Rights Reserved &nbsp;|&nbsp; Developed with <span class="heart">♥</span> by Milan &amp; Tej
    </div>
</footer>

</body>
</html>