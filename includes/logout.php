<?php
session_start();

// Xóa các thông tin người dùng cụ thể thay vì hủy toàn bộ session
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

// Thiết lập thông báo thành công
$_SESSION['success'] = 'Đăng xuất thành công! Hẹn gặp lại bạn.';

header('Location: ../index.php');
exit;
?>