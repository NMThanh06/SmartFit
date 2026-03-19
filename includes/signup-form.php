<?php
session_start();
require_once 'config.php';

// Lấy URL trang hiện tại để redirect về sau khi xử lý
$redirectUrl = '../style_outfits.php'; // Mặc định
if (!empty($_POST['redirect_url'])) {
    $redirectUrl = $_POST['redirect_url'];
} elseif (!empty($_SERVER['HTTP_REFERER'])) {
    $redirectUrl = $_SERVER['HTTP_REFERER'];
}

// Kiểm tra xem có phải yêu cầu AJAX (Fetch API) không
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' 
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

function sendResponse($success, $message, $data = []) {
    global $isAjax, $redirectUrl;
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
        header('Location: ' . $redirectUrl);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['psw'] ?? '';
    $confirm = $_POST['psw-repeat'] ?? '';

    $errors = [];
    if (empty($name)) $errors[] = 'Vui lòng nhập họ tên';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu phải có nhất 6 ký tự';
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
        sendResponse(false, implode("\n", $errors));
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'customer';
    $valid_roles = ['customer', 'support', 'sales', 'admin'];
    if (!in_array($role, $valid_roles)) $role = 'customer';

    $insert = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($insert, 'ssss', $name, $email, $hashed, $role);

    if (mysqli_stmt_execute($insert)) {
        // Gửi webhook đến n8n để gửi email chào mừng
        require_once __DIR__ . '/n8n_helper.php';
        sendDataToN8n('/webhook/welcome-email', [
            'name'  => $name,
            'email' => $email
        ]);

        sendResponse(true, 'Đăng ký thành công! Vui lòng đăng nhập.');
    } else {
        sendResponse(false, 'Lỗi hệ thống: ' . mysqli_error($conn));
    }
    exit;
}

header('Location: ' . $redirectUrl);
exit;