<?php
// db.php
// Database connection file - Include this in all PHP pages
 
$host = 'localhost';  // Assuming local MySQL
$dbname = 'dbfnaxmbbvboqh';
$username = 'um4u5gpwc3dwc';
$password = 'neqhgxo10ioe';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
