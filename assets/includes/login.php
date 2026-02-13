<?php
session_start();
require_once __DIR__ . '/config.php'; 
require_once __DIR__ . '/toast.php';     

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['psw'] ?? '';

    if (empty($email) || empty($password)) {
        showToast('Vui lòng nhập email và mật khẩu', 'error');
    }


    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        showToast('Đăng nhập thành công!', 'success', '../../index.php');    } else {
        showToast('Email hoặc mật khẩu không đúng', 'error');
    }
}

header('Location: login.html');
exit;