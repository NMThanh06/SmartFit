<?php
// ========================================
// Cấu hình kết nối Database
// Tương thích cả XAMPP (Local) và Docker
// ========================================

// Nếu có biến môi trường (Docker) thì dùng, không thì fallback về XAMPP
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'webtest_db';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('Kết nối thất bại: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8');
?>