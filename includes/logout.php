<?php
session_start();

// Lấy URL trang hiện tại để redirect về
$redirectUrl = $_SERVER['HTTP_REFERER'] ?? '../index.php';

// Xóa các thông tin người dùng cụ thể thay vì hủy toàn bộ session
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['role']);

// Thiết lập thông báo thành công
$_SESSION['success'] = 'Đăng xuất thành công! Hẹn gặp lại bạn.';

header('Location: ' . $redirectUrl);
exit;
?>