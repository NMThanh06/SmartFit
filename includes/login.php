<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['psw'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập email và mật khẩu';
        header('Location: ../index.php');
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60);
            $update = "UPDATE users SET remember_token = ? WHERE id = ?";
            $stmt2 = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt2, 'si', $token, $user['id']);
            mysqli_stmt_execute($stmt2);
            setcookie('remember_token', $token, $expiry, '/', '', false, true);
        }

        $_SESSION['success'] = 'Đăng nhập thành công!';
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Email hoặc mật khẩu không đúng';
        header('Location: ../index.php');
        exit;
    }
}

header('Location:../index.php');
exit;