<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/toast.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get value
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['psw'] ?? '';
    $confirm  = $_POST['psw-repeat'] ?? '';

    $errors = [];

    if (empty($name)) {
        $errors[] = 'Vui lòng nhập họ tên';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }

    // check password & validate
    if (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }

    if ($password !== $confirm) {
        $errors[] = 'Mật khẩu nhập lại không khớp';
    }

    // check email
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'Email đã được đăng ký';
        }
        mysqli_stmt_close($stmt);
    }

    
    if (!empty($errors)) {
            $error_msg = implode('\n', $errors);
            showToast($error_msg, 'error');
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

   
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        showToast('Đăng ký thành công!', 'success', '../screen/login.html');
    } else {
        showToast('Lỗi hệ thống: ' . mysqli_error($conn), 'error');
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit;
}

header('Location: signup.html');
exit;
