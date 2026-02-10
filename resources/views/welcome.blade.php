<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waumini Link - Welcome</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --primary-color: #940000;
            --secondary-color: #b30000;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(0, 0, 0, 0.6);
            position: fixed;
            width: 100%;
            z-index: 10;
            padding: 15px 30px;
            backdrop-filter: blur(5px);
        }

        .navbar-custom .navbar-brand img {
            height: 50px;
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }

        .hero video {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            transform: translate(-50%, -50%);
            object-fit: cover;
            z-index: 0;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(148, 0, 0, 0.4), rgba(0, 0, 0, 0.7));
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.7);
        }

        .btn-welcome {
            background-color: #ffffff;
            color: var(--primary-color);
            font-weight: bold;
            padding: 12px 40px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-welcome:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(179, 0, 0, 0.4);
        }

        /* Features Section */
        .features {
            background-color: #f8f9fa;
            padding: 60px 0;
        }

        .features h2 {
            font-weight: bold;
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .features h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: var(--secondary-color);
            margin: 10px auto 0;
            border-radius: 2px;
        }

        .feature-card {
            border: 2px solid var(--primary-color);
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
            padding: 40px 20px;
            background-color: #fff;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(148, 0, 0, 0.15);
            border-color: var(--secondary-color);
            background: linear-gradient(to bottom right, #fff, #fff0f0);
        }

        .feature-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* Footer - Matching Login Page Style */
        footer {
            background-color: #111;
            color: #fff;
            padding: 40px 0 0;
            position: relative;
        }

        /* Footer Top Bar */
        .footer-top-bar {
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            width: 100%;
            margin-bottom: 30px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }

        .footer-section h5 {
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-section h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .footer-section p {
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        .footer-logo img {
            height: 40px;
            margin-bottom: 15px;
            display: block;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .footer-links a i {
            margin-right: 10px;
            width: 20px;
            color: var(--primary-color);
        }

        .footer-links a:hover {
            color: var(--secondary-color);
            transform: translateX(5px);
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            color: #ccc;
        }

        .footer-contact-item i {
            margin-right: 12px;
            margin-top: 5px;
            color: var(--primary-color);
        }

        .footer-social {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .footer-social a {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(148, 0, 0, 0.1);
            border-radius: 50%;
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(148, 0, 0, 0.3);
        }

        .footer-social a:hover {
            background: var(--primary-color);
            color: #fff;
            transform: translateY(-3px);
            border-color: var(--primary-color);
        }

        .footer-bottom {
            background: #000;
            padding: 20px 0;
            margin-top: 20px;
            border-top: 1px solid #222;
        }

        .footer-bottom-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-bottom p {
            color: #888;
            margin: 0;
            font-size: 0.85rem;
        }

        .footer-bottom .powered-by {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #888;
            font-size: 0.85rem;
        }

        .footer-bottom .powered-by a {
            color: var(--primary-color) !important;
            text-decoration: none;
            font-weight: bold;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-content p {
                font-size: 1.1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
                padding-bottom: 20px;
            }

            .footer-section h5::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-links {
                align-items: center;
                display: flex;
                flex-direction: column;
            }

            .footer-contact-item {
                justify-content: center;
            }

            .footer-social {
                justify-content: center;
            }

            .footer-bottom-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-custom">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo">
        </a>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <video id="heroVideo" autoplay muted loop playsinline preload="auto"
            poster="{{ asset('assets/images/church.jpg') }}">
            <source src="{{ asset('assets/videos/waumini_link_video.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="hero-content">
            <h1>Welcome to Waumini Link</h1>
            <p>Your central system to manage all church members efficiently</p>
            <a href="{{ route('login') }}" class="btn btn-welcome">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Why Waumini Link?</h2>
                <p>Manage all your church members efficiently with ease</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <i class="fa-solid fa-users"></i>
                        <h5 class="card-title mt-3">Manage Members</h5>
                        <p>Easily register and track all your church members in one central system.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <i class="fa-solid fa-chart-line"></i>
                        <h5 class="card-title mt-3">Reports & Insights</h5>
                        <p>Generate detailed reports about attendance, contributions, and member activity.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h5 class="card-title mt-3">Secure & Reliable</h5>
                        <p>All your member data is stored securely with role-based access for safety.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-top-bar"></div>

        <div class="footer-container">
            <div class="footer-content">
                <!-- About Section -->
                <div class="footer-section">
                    <h5>About Waumini Link</h5>
                    <p>Your comprehensive church management system designed to streamline member administration,
                        financial tracking, and community engagement.</p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Quick Links Section -->
                <div class="footer-section">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('landing_page') }}"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="#"><i class="fas fa-info-circle"></i> About Us</a></li>
                        <li><a href="#"><i class="fas fa-question-circle"></i> Help & Support</a></li>
                        <li><a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Contact Section -->
                <div class="footer-section">
                    <h5>Contact Us</h5>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><a href="mailto:emca@emca.tech"
                                style="color: inherit; text-decoration: none;">emca@emca.tech</a></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span><a href="tel:+255749719998" style="color: inherit; text-decoration: none;">+255 749 719
                                998</a></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Moshi, Kilimanjaro</span>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="footer-section">
                    <h5>Our Services</h5>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-users"></i> Member Management</a></li>
                        <li><a href="#"><i class="fas fa-chart-line"></i> Financial Reports</a></li>
                        <li><a href="#"><i class="fas fa-calendar-check"></i> Attendance Tracking</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; {{ date('Y') }} Waumini Link. All rights reserved.</p>
                <div class="powered-by">
                    <span>Powered by</span> <a href="https://emca.tech" target="_blank">EmCa Technologies</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- JS to ensure full video playback -->
    <script>
        document.addEventListener('DOMContentLoaded', function ()  {
            const video = document.getElementById('heroVideo');
            if ( video) {
                video.addEventListener('ended', () => {
                    video.currentTime = 0;
                    video.play();
                });
            }
        });
    </script>
</body>

</html>