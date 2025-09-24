<?php
// index.php - Homepage
include 'db.php';
 
// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
 
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? null;  // 'user' or 'driver'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bykea Clone - Ride & Delivery</title>
    <style>
        /* Amazing CSS - Modern, Responsive, Realistic Bykea-like Design */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: #333; }
        header { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        nav { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; }
        .logo { font-size: 2rem; font-weight: bold; color: #4f46e5; text-decoration: none; }
        .nav-links { display: flex; list-style: none; gap: 2rem; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s; }
        .nav-links a:hover { color: #4f46e5; }
        .auth-btn { background: #4f46e5; color: white; padding: 0.5rem 1rem; border-radius: 25px; text-decoration: none; transition: transform 0.3s; }
        .auth-btn:hover { transform: scale(1.05); }
        main { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .hero { text-align: center; padding: 4rem 0; background: rgba(255,255,255,0.1); border-radius: 20px; margin-bottom: 3rem; backdrop-filter: blur(10px); }
        .hero h1 { font-size: 3.5rem; color: white; margin-bottom: 1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .hero p { font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 2rem; }
        .services { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
        .service-card { background: white; border-radius: 15px; padding: 2rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .service-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
        .service-card h3 { color: #4f46e5; font-size: 1.8rem; margin-bottom: 1rem; }
        .service-card button { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; padding: 1rem 2rem; border-radius: 25px; cursor: pointer; font-size: 1rem; transition: transform 0.3s; }
        .service-card button:hover { transform: scale(1.05); }
        .dashboard { background: white; border-radius: 15px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .dashboard h2 { color: #4f46e5; margin-bottom: 1rem; }
        .quick-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
        .quick-btn { background: #4f46e5; color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; transition: background 0.3s; }
        .quick-btn:hover { background: #3730a3; }
        footer { background: rgba(0,0,0,0.8); color: white; text-align: center; padding: 2rem; margin-top: 3rem; }
        @media (max-width: 768px) { .hero h1 { font-size: 2.5rem; } .nav-links { display: none; } .quick-actions { flex-direction: column; } }
        /* Animations */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .service-card { animation: fadeIn 0.6s ease-out; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Bykea</a>
            <ul class="nav-links">
                <li><a href="#services">Services</a></li>
                <li><a href="tracking.php">Track</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="?logout=1">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Signup</a></li>
                <?php endif; ?>
            </ul>
            <?php if (!$isLoggedIn): ?>
                <a href="login.php" class="auth-btn">Get Started</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h1>Ride & Delivery Made Easy</h1>
            <p>Book a bike ride or send parcels anywhere, anytime with Bykea.</p>
            <?php if (!$isLoggedIn): ?>
                <a href="signup.php" style="background: white; color: #4f46e5; padding: 1rem 2rem; border-radius: 25px; text-decoration: none; font-weight: bold;">Join Now</a>
            <?php endif; ?>
        </section>
        <section id="services" class="services">
            <div class="service-card">
                <h3>Book a Ride</h3>
                <p>Quick and affordable bike rides to your destination.</p>
                <button onclick="redirectTo('book_ride.php')">Book Now</button>
            </div>
            <div class="service-card">
                <h3>Send Parcel</h3>
                <p>Deliver packages safely and on time.</p>
                <button onclick="redirectTo('book_parcel.php')">Send Now</button>
            </div>
            <?php if ($isLoggedIn && $userRole === 'driver'): ?>
                <div class="service-card">
                    <h3>Driver Dashboard</h3>
                    <p>Accept rides and deliveries.</p>
                    <button onclick="redirectTo('driver_dashboard.php')">Go to Dashboard</button>
                </div>
            <?php endif; ?>
        </section>
        <?php if ($isLoggedIn): ?>
            <section class="dashboard">
                <h2>Welcome Back!</h2>
                <div class="quick-actions">
                    <button class="quick-btn" onclick="redirectTo('book_ride.php')">Book Ride</button>
                    <button class="quick-btn" onclick="redirectTo('book_parcel.php')">Send Parcel</button>
                    <button class="quick-btn" onclick="redirectTo('profile.php')">My Profile</button>
                    <?php if ($userRole === 'driver'): ?>
                        <button class="quick-btn" onclick="redirectTo('driver_dashboard.php')">My Rides</button>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2025 Bykea Clone. All rights reserved.</p>
    </footer>
    <script>
        // Embedded JS - No separate files
        function redirectTo(url) {
            window.location.href = url;
        }
        // Smooth scroll for anchors
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
