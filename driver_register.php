<?php
// driver_register.php - Driver Registration
include 'db.php';
 
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $phone = $_POST['phone'] ?? '';
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $license = $_POST['license'] ?? '';
 
    if ($name && $email && $password && $phone && $vehicle_type && $license) {
        try {
            $stmt = $pdo->prepare("INSERT INTO drivers (name, email, password, phone, vehicle_type, license_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $phone, $vehicle_type, $license]);
            echo "<script>alert('Driver registered! Login now.'); redirectTo('login.php');</script>";
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    } else {
        $error = "All fields required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Signup - Bykea</title>
    <style>
        /* Same as signup */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .form-container { background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #4f46e5; margin-bottom: 2rem; }
        input, select { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s; }
        input:focus, select:focus { outline: none; border-color: #4f46e5; }
        button { width: 100%; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; font-size: 1rem; transition: transform 0.3s; }
        button:hover { transform: scale(1.02); }
        .error { color: red; text-align: center; margin-bottom: 1rem; }
        .link { text-align: center; margin-top: 1rem; }
        .link a { color: #4f46e5; text-decoration: none; }
        @media (max-width: 768px) { .form-container { margin: 1rem; padding: 2rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Driver Sign Up</h2>
        <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="tel" name="phone" placeholder="Phone" required>
            <select name="vehicle_type" required>
                <option value="">Select Vehicle</option>
                <option value="bike">Bike</option>
                <option value="car">Car</option>
                <option value="van">Van</option>
            </select>
            <input type="text" name="license" placeholder="License Number" required>
            <button type="submit">Register as Driver</button>
        </form>
        <p class="link"><a href="signup.php">Sign up as User</a> | <a href="login.php">Login</a></p>
    </div>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
