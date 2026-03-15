<?php
session_start();
require_once 'config.php';

// Kiểm tra xem có phải yêu cầu AJAX (Fetch API) không
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' 
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

function sendResponse($success, $message, $data = []) {
    global $isAjax;
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
        exit;
    } else {
        if ($success) {
            $_SESSION['success'] = $message;
        } else {
            $_SESSION['error'] = $message;
        }
        header('Location: ../index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['psw'] ?? '';

    if (empty($email) || empty($password)) {
        sendResponse(false, 'Vui lòng nhập email và mật khẩu');
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

        sendResponse(true, 'Đăng nhập thành công!', ['user_name' => $user['name']]);
    } else {
        sendResponse(false, 'Email hoặc mật khẩu không đúng');
    }
}

header('Location: ../index.php');
exit;