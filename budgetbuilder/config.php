<?php
// config.php - DB connection for XAMPP
session_start();
$db_host = '127.0.0.1';
$db_name = 'budgetbuilder';
$db_user = 'root';
$db_pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
  $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
  // Try to create DB if missing
  try {
    $tmp = new PDO("mysql:host=$db_host;charset=$charset", $db_user, $db_pass, $options);
    $tmp->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
  } catch (PDOException $e2) {
    die('DB Connection failed: ' . $e2->getMessage());
  }
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
