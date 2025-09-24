<?php
// driver_dashboard.php - Driver Management
include 'db.php';
 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit;
}
 
$driverId = $_SESSION['user_id'];
 
// Update driver location (simulate)
if (isset($_POST['update_location'])) {
    $lat = floatval($_POST['lat'] ?? 0);
    $lng = floatval($_POST['lng'] ?? 0);
    $pdo->prepare("UPDATE drivers SET current_lat = ?, current_lng = ?, status = 'available' WHERE id = ?")->execute([$lat, $lng, $driverId]);
}
 
// Fetch pending requests
$ridesStmt = $pdo->prepare("SELECT r.*, u.name as user_name FROM rides r JOIN users u ON r.user_id = u.id WHERE r.status = 'requested' AND r.driver_id IS NULL LIMIT 5");
$ridesStmt->execute();
$pendingRides = $ridesStmt->fetchAll();
 
$parcelsStmt = $pdo->prepare("SELECT p.*, u.name as sender_name FROM parcels p JOIN users u ON p.sender_id = u.id WHERE p.status = 'requested' LIMIT 5");
$parcelsStmt->execute();
$pendingParcels = $parcelsStmt->fetchAll();
 
// Accept ride
if (isset($_POST['accept_ride'])) {
    $rideId = intval($_POST['ride_id'] ?? 0);
    $pdo->prepare("UPDATE rides SET driver_id = ?, status = 'accepted' WHERE id = ?")->execute([$driverId, $rideId]);
    $pdo->prepare("UPDATE drivers SET status = 'busy' WHERE id = ?")->execute([$driverId]);
    header("Location: driver_dashboard.php");
    exit;
}
 
// Accept parcel similar
if (isset($_POST['accept_parcel'])) {
    $parcelId = intval($_POST['parcel_id'] ?? 0);
    $pdo->prepare("UPDATE parcels SET status = 'accepted' WHERE id = ?")->execute([$parcelId]);
    $pdo->prepare("UPDATE drivers SET status = 'busy' WHERE id = ?")->execute([$driverId]);
    header("Location: driver_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Bykea</title>
    <style>
        /* Same CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem; }
        .dashboard { max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; padding: 3rem; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        h2 { color: #4f46e5; margin-bottom: 2rem; text-align: center; }
        .section { margin-bottom: 3rem; }
        .location-form { display: flex; gap: 1rem; margin-bottom: 2rem; }
        .location-form input { flex: 1; padding: 1rem; border: 1px solid #ddd; border-radius: 10px; }
        button { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; transition: transform 0.3s; }
        button:hover { transform: scale(1.02); }
        .request-list { display: grid; gap: 1rem; }
        .request-card { border: 1px solid #ddd; border-radius: 10px; padding: 1rem; background: #f9f9f9; }
        .accept-btn { background: #10b981; padding: 0.5rem 1rem; border-radius: 5px; color: white; border: none; cursor: pointer; }
        .back-btn { background: #6b7280; width: 100%; margin-top: 1rem; }
        @media (max-width: 768px) { .location-form { flex-direction: column; } .dashboard { margin: 1rem; padding: 2rem; } }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Driver Dashboard</h2>
        <div class="section">
            <h3>Update Location</h3>
            <form method="POST" class="location-form">
                <input type="number" step="any" name="lat" placeholder="Latitude" required>
                <input type="number" step="any" name="lng" placeholder="Longitude" required>
                <button type="submit" name="update_location">Update & Go Online</button>
            </form>
        </div>
        <div class="section">
            <h3>Pending Rides</h3>
            <div class="request-list">
                <?php foreach ($pendingRides as $ride): ?>
                    <div class="request-card">
                        <p>User: <?php echo htmlspecialchars($ride['user_name']); ?></p>
                        <p>From: <?php echo htmlspecialchars($ride['pickup_address']); ?> to <?php echo htmlspecialchars($ride['dropoff_address']); ?></p>
                        <p>Price: Rs. <?php echo $ride['price']; ?></p>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="ride_id" value="<?php echo $ride['id']; ?>">
                            <button type="submit" name="accept_ride" class="accept-btn">Accept Ride</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="section">
            <h3>Pending Parcels</h3>
            <div class="request-list">
                <?php foreach ($pendingParcels as $parcel): ?>
                    <div class="request-card">
                        <p>Sender: <?php echo htmlspecialchars($parcel['sender_name']); ?></p>
                        <p>Receiver: <?php echo htmlspecialchars($parcel['receiver_name']); ?></p>
                        <p>Weight: <?php echo $parcel['weight_kg']; ?>kg, Price: Rs. <?php echo $parcel['price']; ?></p>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="parcel_id" value="<?php echo $parcel['id']; ?>">
                            <button type="submit" name="accept_parcel" class="accept-btn">Accept Parcel</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="back-btn" onclick="redirectTo('index.php')">Back to Home</button>
    </div>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
        // Demo location
        document.querySelector('input[name="lat"]').value = '31.5204';
        document.querySelector('input[name="lng"]').value = '74.3587';
    </script>
</body>
</html>
