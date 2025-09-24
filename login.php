<?php
// login.php - User Login
include 'db.php';
 
if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
 
    $stmt = $pdo->prepare("SELECT id, password, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
 
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
 
// Driver login check
if (isset($_POST['driver_login'])) {
    // Similar logic for drivers table
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
 
    $stmt = $pdo->prepare("SELECT id, password FROM drivers WHERE email = ?");
    $stmt->execute([$email]);
    $driver = $stmt->fetch();
 
    if ($driver && password_verify($password, $driver['password'])) {
        $_SESSION['user_id'] = $driver['id'];
        $_SESSION['role'] = 'driver';
        header("Location: driver_dashboard.php");
        exit;
    } else {
        $error = "Invalid driver credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bykea</title>
    <style>
        /* Same CSS as signup */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .form-container { background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #4f46e5; margin-bottom: 2rem; }
        input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s; }
        input:focus { outline: none; border-color: #4f46e5; }
        button { width: 100%; background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; border: none; padding: 1rem; border-radius: 10px; cursor: pointer; font-size: 1rem; transition: transform 0.3s; margin-bottom: 1rem; }
        button:hover { transform: scale(1.02); }
        .error { color: red; text-align: center; margin-bottom: 1rem; }
        .link { text-align: center; margin-top: 1rem; }
        .link a { color: #4f46e5; text-decoration: none; }
        .toggle { text-align: center; margin-top: 1rem; cursor: pointer; color: #4f46e5; }
        @media (max-width: 768px) { .form-container { margin: 1rem; padding: 2rem; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>User Login</h2>
        <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="link"><a href="signup.php">Don't have an account? Signup</a></p>
        <p class="toggle" onclick="toggleForm()">Login as Driver</p>
        <form method="POST" id="driverForm" style="display:none;">
            <h2>Driver Login</h2>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="hidden" name="driver_login" value="1">
            <button type="submit">Driver Login</button>
        </form>
    </div>
    <script>
        function toggleForm() {
            const userForm = document.querySelector('form:not(#driverForm)');
            const driverForm = document.getElementById('driverForm');
            userForm.style.display = userForm.style.display === 'none' ? 'block' : 'none';
            driverForm.style.display = driverForm.style.display === 'none' ? 'block' : 'none';
        }
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
