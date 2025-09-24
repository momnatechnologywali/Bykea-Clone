<?php
// tracking.php - Real-time Tracking (Polling)
include 'db.php';
 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
 
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';
 
// Fetch latest ride or parcel for user
if ($role === 'user') {
    $rideStmt = $pdo->prepare("SELECT r.*, d.name as driver_name, d.current_lat as driver_lat, d.current_lng as driver_lng FROM rides r LEFT JOIN drivers d ON r.driver_id = d.id WHERE r.user_id = ? ORDER BY r.created_at DESC LIMIT 1");
    $rideStmt->execute([$userId]);
    $currentRide = $rideStmt->fetch();
 
    $parcelStmt = $pdo->prepare("SELECT * FROM parcels WHERE sender_id = ? ORDER BY created_at DESC LIMIT 1");
    $parcelStmt->execute([$userId]);
    $currentParcel = $parcelStmt->fetch();
} else {
    // For driver, fetch assigned
    $rideStmt = $pdo->prepare("SELECT r.*, u.name as user_name FROM rides r JOIN users u ON r.user_id = u.id WHERE r.driver_id = ? AND r.status != 'completed' LIMIT 1");
    $rideStmt->execute([$userId]);
    $currentRide = $rideStmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking - Bykea</title>
    <style>
        /* Same CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem; }
        .tracking-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 20px 60px rgba(0,0,0,0.2); text-align: center; }
        h2 { color: #4f46e5; margin-bottom: 2rem; }
        #map { height: 300px; background: linear-gradient(135deg, #e5e7eb, #d1d5db); border-radius: 15px; margin: 2rem 0; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 1.2rem; }
        .status { font-size: 1.5rem; margin: 1rem 0; padding: 1rem; border-radius: 10px; background: #f0f9ff; }
        .driver-info { background: #f3f4f6; padding: 1rem; border-radius: 10px; margin: 1rem 0; }
        button { background: #6b7280; color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; margin-top: 1rem; transition: transform 0.3s; width: 100%; }
        button:hover { transform: scale(1.02); }
        @media (max-width: 768px) { .tracking-container { margin: 1rem; padding: 2rem; } }
    </style>
</head>
<body>
    <div class="tracking-container">
        <h2>Live Tracking</h2>
        <div id="map">Interactive Map Placeholder (Driver/User Location)</div>
        <?php if (isset($currentRide)): ?>
            <div class="status" style="background: <?php echo $currentRide['status'] === 'ongoing' ? '#d1fae5' : '#fef3c7'; ?>">
                Status: <?php echo ucfirst($currentRide['status']); ?>
            </div>
            <p>Price: Rs. <?php echo $currentRide['price']; ?></p>
            <?php if ($role === 'user' && $currentRide['driver_name']): ?>
                <div class="driver-info">
                    <p>Driver: <?php echo htmlspecialchars($currentRide['driver_name']); ?></p>
                    <p>Location: Lat <?php echo $currentRide['driver_lat']; ?>, Lng <?php echo $currentRide['driver_lng']; ?></p>
                </div>
            <?php endif; ?>
        <?php elseif (isset($currentParcel)): ?>
            <div class="status">Parcel Status: <?php echo ucfirst($currentParcel['status']); ?></div>
            <p>Receiver: <?php echo htmlspecialchars($currentParcel['receiver_name']); ?></p>
        <?php else: ?>
            <p>No active trip. <a href="index.php" style="color: #4f46e5;">Book one</a></p>
        <?php endif; ?>
        <button onclick="redirectTo('index.php')">Back to Home</button>
    </div>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
        // Simulate real-time polling every 30s
        setInterval(function() {
            // Fetch updated data via AJAX, but for simplicity, reload page
            // In production, use fetch('/api/track.php')
            location.reload();
        }, 30000);  // 30 seconds as per table
        // Notification simulation
        if (document.querySelector('.status')) {
            setTimeout(() => alert('Update: Driver is en route!'), 5000);
        }
    </script>
</body>
</html>
