<?php
// book_ride.php - Ride Booking
include 'db.php';
 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
 
$userId = $_SESSION['user_id'];
 
if ($_POST) {
    $pickup_lat = floatval($_POST['pickup_lat'] ?? 0);
    $pickup_lng = floatval($_POST['pickup_lng'] ?? 0);
    $pickup_address = $_POST['pickup_address'] ?? '';
    $dropoff_lat = floatval($_POST['dropoff_lat'] ?? 0);
    $dropoff_lng = floatval($_POST['dropoff_lng'] ?? 0);
    $dropoff_address = $_POST['dropoff_address'] ?? '';
 
    // Simple distance calculation (Haversine approx)
    $earth_radius = 6371;  // km
    $dlat = deg2rad($dropoff_lat - $pickup_lat);
    $dlng = deg2rad($dropoff_lng - $pickup_lng);
    $a = sin($dlat/2) * sin($dlat/2) + cos(deg2rad($pickup_lat)) * cos(deg2rad($dropoff_lat)) * sin($dlng/2) * sin($dlng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earth_radius * $c;
 
    $price = $distance * 10;  // Rs 10 per km
 
    $stmt = $pdo->prepare("INSERT INTO rides (user_id, pickup_lat, pickup_lng, pickup_address, dropoff_lat, dropoff_lng, dropoff_address, distance_km, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $pickup_lat, $pickup_lng, $pickup_address, $dropoff_lat, $dropoff_lng, $dropoff_address, $distance, $price]);
    $rideId = $pdo->lastInsertId();
 
    // Simulate notification
    echo "<script>alert('Ride booked! Price: Rs. $price. Tracking ID: $rideId');</script>";
    // Assign driver (simple: first available)
    $driverStmt = $pdo->prepare("SELECT id FROM drivers WHERE status = 'available' LIMIT 1");
    $driverStmt->execute();
    $driver = $driverStmt->fetch();
    if ($driver) {
        $pdo->prepare("UPDATE rides SET driver_id = ?, status = 'accepted' WHERE id = ?")->execute([$driver['id'], $rideId]);
        $pdo->prepare("UPDATE drivers SET status = 'busy' WHERE id = ?")->execute([$driver['id']]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Ride - Bykea</title>
    <style>
        /* Same amazing CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem; }
        .form-container { max-width: 500px; margin: 0 auto; background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        h2 { color: #4f46e5; margin-bottom: 2rem; text-align: center; }
        input, textarea, select { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; }
        button { width: 100%; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; transition: transform 0.3s; }
        button:hover { transform: scale(1.02); }
        .back-btn { background: #6b7280; margin-top: 1rem; }
        #map { height: 200px; background: #e5e7eb; border-radius: 10px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; color: #6b7280; }  /* Placeholder map */
        @media (max-width: 768px) { .form-container { margin: 1rem; padding: 2rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Book a Ride</h2>
        <div id="map">Map Placeholder - Enter locations below</div>
        <form method="POST">
            <input type="text" name="pickup_address" placeholder="Pickup Address" required>
            <input type="number" step="any" name="pickup_lat" placeholder="Pickup Lat (e.g., 31.5204)" required>
            <input type="number" step="any" name="pickup_lng" placeholder="Pickup Lng (e.g., 74.3587)" required>
            <input type="text" name="dropoff_address" placeholder="Dropoff Address" required>
            <input type="number" step="any" name="dropoff_lat" placeholder="Dropoff Lat" required>
            <input type="number" step="any" name="dropoff_lng" placeholder="Dropoff Lng" required>
            <button type="submit">Confirm Booking</button>
        </form>
        <button class="back-btn" onclick="redirectTo('index.php')">Back</button>
    </div>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
        // Simulate geolocation for demo
        document.querySelector('input[name="pickup_lat"]').value = '31.5204';
        document.querySelector('input[name="pickup_lng"]').value = '74.3587';
        document.querySelector('input[name="dropoff_lat"]').value = '31.5775';
        document.querySelector('input[name="dropoff_lng"]').value = '74.3094';
    </script>
</body>
</html>
