<?php
session_start();

// Xóa các thông tin người dùng cụ thể
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['role']);

// Thiết lập thông báo thành công
$_SESSION['success'] = 'Đăng xuất thành công! Hẹn gặp lại bạn.';

// Luôn chuyển hướng về trang chủ index.php
header('Location: ../index.php');
exit;
?>