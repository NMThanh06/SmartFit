<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['psw'] ?? '';
    $confirm = $_POST['psw-repeat'] ?? '';

    $errors = [];
    if (empty($name)) $errors[] = 'Vui lòng nhập họ tên';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    if ($password !== $confirm) $errors[] = 'Mật khẩu nhập lại không khớp';

    if (empty($errors)) {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $errors[] = 'Email đã được đăng ký';
        }
        mysqli_stmt_close($check);
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode('\n', $errors);
        header('Location: ../index.php');
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insert = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($insert, 'sss', $name, $email, $hashed);

    if (mysqli_stmt_execute($insert)) {
        $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
        header('Location: ../index.php');
    } else {
        $_SESSION['error'] = 'Lỗi hệ thống: ' . mysqli_error($conn);
        header('Location: ../index.php');
    }
    exit;
}

header('Location:../index.php');
exit;