<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waumini Link - Welcome</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(0,0,0,0.4);
            position: fixed;
            width: 100%;
            z-index: 10;
            padding: 15px 30px;
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
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
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
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        }
        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 6px rgba(0,0,0,0.7);
        }
        .btn-welcome {
            background-color: #ffffff;
            color: #0084d6;
            font-weight: bold;
            padding: 12px 40px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-welcome:hover {
            background-color: #25D71B;
            color: white;
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
        }
        .features h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #25D71B;
            margin: 10px auto 0;
            border-radius: 2px;
        }
        .feature-card {
            border: 2px solid #0084d6;
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
            padding: 40px 20px;
            background-color: #fff;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-color: #006aad;
        }
        .feature-card i {
            font-size: 3rem;
            color: #0084d6;
            margin-bottom: 20px;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #0a1929 0%, #001529 100%);
            color: #fff;
            padding: 60px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        /* Footer Top Bar */
        .footer-top-bar {
            height: 5px;
            background: linear-gradient(90deg, #0084d6 0%, #006aad 50%, #0084d6 100%);
            background-size: 200% 100%;
            animation: gradientShift 3s ease infinite;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0, 132, 214, 0.3);
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
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
            margin-bottom: 40px;
        }
        
        .footer-section {
            position: relative;
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
            background: linear-gradient(90deg, #0084d6 0%, #006aad 100%);
            border-radius: 2px;
            box-shadow: 0 2px 5px rgba(0, 132, 214, 0.4);
        }
        
        .footer-section p {
            color: #b0b0b0;
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .footer-logo {
            margin-bottom: 20px;
        }
        
        .footer-logo img {
            height: 50px;
            transition: transform 0.3s ease;
        }
        
        .footer-logo:hover img {
            transform: scale(1.05);
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 12px;
        }
        
        .footer-links a {
            color: #b0b0b0;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .footer-links a i {
            margin-right: 10px;
            width: 20px;
            color: #0084d6;
            transition: transform 0.3s ease, color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #0084d6;
            transform: translateX(5px);
        }
        
        .footer-links a:hover i {
            transform: scale(1.2);
            color: #25D71B;
        }
        
        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            color: #b0b0b0;
        }
        
        .footer-contact-item i {
            margin-right: 12px;
            margin-top: 5px;
            color: #0084d6;
            font-size: 1.1rem;
            width: 20px;
            flex-shrink: 0;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        
        .footer-contact-item:hover i {
            color: #25D71B;
            transform: scale(1.1);
        }
        
        .footer-contact-item span {
            line-height: 1.6;
        }
        
        .footer-contact-item span a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-contact-item span a:hover {
            color: #0084d6;
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .footer-social a {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 132, 214, 0.15);
            border-radius: 50%;
            color: #0084d6;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid rgba(0, 132, 214, 0.3);
        }
        
        .footer-social a:hover {
            background: linear-gradient(135deg, #0084d6 0%, #006aad 100%);
            color: #fff;
            transform: translateY(-5px) scale(1.1);
            border-color: #0084d6;
            box-shadow: 0 5px 20px rgba(0, 132, 214, 0.5);
        }
        
        .footer-social a i {
            font-size: 1.2rem;
        }
        
        .footer-bottom {
            background: rgba(0, 0, 0, 0.3);
            padding: 25px 0;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
        }
        
        .footer-bottom-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .footer-bottom p {
            color: #b0b0b0;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .footer-bottom .powered-by {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #b0b0b0;
            font-size: 0.9rem;
        }
        
        .footer-bottom .powered-by img {
            height: 25px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .footer-bottom .powered-by:hover img {
            opacity: 1;
        }
        
        .footer-bottom .powered-by:hover {
            color: #0084d6;
        }
        
        /* Decorative Elements */
        .footer-decoration {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(0, 132, 214, 0.15) 0%, rgba(0, 132, 214, 0.05) 50%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, 30%);
            pointer-events: none;
        }
        
        /* Primary Color Accents */
        .footer-section h5 {
            text-shadow: 0 0 10px rgba(0, 132, 214, 0.2);
        }
        
        .footer-logo img {
            opacity: 0.9;
        }
        
        .footer-logo:hover img {
            opacity: 1;
            filter: drop-shadow(0 0 8px rgba(0, 132, 214, 0.6));
        }
        
        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }
            
            .footer-section {
                text-align: center;
            }
            
            .footer-section h5::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer-logo {
                display: flex;
                justify-content: center;
            }
            
            .footer-links {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            
            .footer-links li {
                text-align: center;
            }
            
            .footer-links a {
                justify-content: center;
            }
            
            .footer-contact-item {
                justify-content: center;
            }
            
            .footer-bottom-content {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-social {
                justify-content: center;
            }
        }

        @keyframes fadeIn {
            0% {opacity: 0; transform: translateY(-20px);}
            100% {opacity: 1; transform: translateY(0);}
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }
            .hero-content p {
                font-size: 1.1rem;
            }
            .navbar-custom .navbar-brand img {
                height: 40px;
            }
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }
            .footer-section {
                text-align: center;
            }
            .footer-section h5::after {
                left: 50%;
                transform: translateX(-50%);
            }
            .footer-logo {
                display: flex;
                justify-content: center;
            }
            .footer-links {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .footer-links li {
                text-align: center;
            }
            .footer-links a {
                justify-content: center;
            }
            .footer-contact-item {
                justify-content: center;
            }
            .footer-bottom-content {
                flex-direction: column;
                text-align: center;
            }
            .footer-social {
                justify-content: center;
            }
            .footer-decoration {
                width: 200px;
                height: 200px;
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
        <video id="heroVideo" autoplay muted loop playsinline preload="auto" poster="{{ asset('assets/images/church.jpg') }}">
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
        <div class="footer-decoration"></div>
        
        <div class="footer-container">
            <div class="footer-content">
                <!-- About Section -->
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo">
                    </div>
                    <h5>About Waumini Link</h5>
                    <p>Your comprehensive church management system designed to streamline member administration, financial tracking, and community engagement.</p>
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
                        <li><a href="#"><i class="fas fa-file-contract"></i> Terms of Service</a></li>
                    </ul>
                </div>
                
                <!-- Contact Section -->
                <div class="footer-section">
                    <h5>Contact Us</h5>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><a href="mailto:emca@emca.tech" style="color: inherit; text-decoration: none;">emca@emca.tech</a></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span><a href="tel:+255749719998" style="color: inherit; text-decoration: none;">+255 749 719 998</a></span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Moshi, Kilimanjaro</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                    </div>
                </div>
                
                <!-- Services Section -->
                <div class="footer-section">
                    <h5>Our Services</h5>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-users"></i> Member Management</a></li>
                        <li><a href="#"><i class="fas fa-chart-line"></i> Financial Reports</a></li>
                        <li><a href="#"><i class="fas fa-calendar-check"></i> Attendance Tracking</a></li>
                        <li><a href="#"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                        <li><a href="#"><i class="fas fa-id-card"></i> Identity Cards</a></li>
                        <li><a href="#"><i class="fas fa-bell"></i> SMS Notifications</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; {{ date('Y') }} Waumini Link. All rights reserved.</p>
                <div class="powered-by">
                    <span>Powered by</span>
                    <img src="{{ asset('assets/images/emca_logo.png') }}" alt="EmCa Technologies">
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- JS to ensure full video playback -->
    <script>
        const video = document.getElementById('heroVideo');
        video.addEventListener('ended', () => {
            video.currentTime = 0;
            video.play();
        });
    </script>
</body>
</html>
