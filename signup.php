<?php
// signup.php - User Signup
include 'db.php';
 
if ($_POST) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $phone = $_POST['phone'] ?? '';
 
    if ($name && $email && $password && $phone) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $phone]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['role'] = 'user';
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Signup failed: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Bykea</title>
    <style>
        /* Same amazing CSS as index, adapted for form */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .form-container { background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #4f46e5; margin-bottom: 2rem; }
        input { width: 100%; padding: 1rem; margin-bottom: 1rem; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s; }
        input:focus { outline: none; border-color: #4f46e5; }
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
        <h2>Sign Up</h2>
        <?php if (isset($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="tel" name="phone" placeholder="Phone" required>
            <button type="submit">Sign Up</button>
        </form>
        <p class="link"><a href="login.php">Already have an account? Login</a></p>
        <p class="link"><a href="driver_register.php">Sign up as Driver</a></p>
    </div>
    <script>
        function redirectTo(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
