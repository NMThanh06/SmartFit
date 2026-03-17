<?php
/**
 * middleware.php
 * Lớp trung gian kiểm soát quyền truy cập trang web
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đường dẫn tương đối dựa trên cấu trúc thư mục của dự án
$config_path = __DIR__ . '/config/permissions.php';
if (file_exists($config_path)) {
    require_once $config_path;
}

// 1. Xác định trang hiện tại (tên file)
$current_page = basename($_SERVER['PHP_SELF']);

// 2. Lấy Role hiện tại từ Session (mặc định là guest nếu chưa đăng nhập)
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// 3. Kiểm tra quyền truy cập tập trung
if (!can_access($current_page, $user_role)) {
    // Nếu bị chặn, đuổi về trang chủ hoặc trang thông báo lỗi
    // Lưu ý: Dùng đường dẫn tương đối từ vị trí các trang trong /pages/
    header("Location: home.php"); 
    exit();
}
