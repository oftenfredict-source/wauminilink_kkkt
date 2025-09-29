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
            border: 2px solid #25D71B;
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
            padding: 40px 20px;
            background-color: #fff;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-color: #0084d6;
        }
        .feature-card i {
            font-size: 3rem;
            color: #25D71B;
            margin-bottom: 20px;
        }

        /* Footer */
        footer {
            background-color: #111;
            color: #fff;
            padding: 50px 0 20px;
        }
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: auto;
        }
        .footer-section {
            flex: 1 1 300px;
            margin: 15px;
        }
        .footer-section h5 {
            font-weight: bold;
            margin-bottom: 15px;
            color: #25D71B;
        }
        .footer-section p {
            color: #ccc;
            line-height: 1.6;
        }
        .footer-section a {
            color: #ccc;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: color 0.3s;
        }
        .footer-section a:hover {
            color: #25D71B;
        }
        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #aaa;
            border-top: 1px solid #333;
            padding-top: 15px;
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
            .footer-container {
                flex-direction: column;
                align-items: center;
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
        <div class="footer-container">
            <div class="footer-section">
                <h5>Waumini Link</h5>
                <p>Your central system to manage all church members efficiently.</p>
            </div>
            <div class="footer-section">
                <h5>Contact</h5>
                <p>Email: info@emcatech.com</p>
                <p>Phone: +255 7XX XXX XXX</p>
            </div>
        </div>
        <div class="footer-bottom">
            Powered by EmCa Technologies &copy; 2025
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
