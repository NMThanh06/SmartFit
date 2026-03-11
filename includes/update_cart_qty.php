<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// 1. Lấy dữ liệu từ Fetch gửi lên
$data = json_decode(file_get_contents('php://input'), true);
$cartId = $data['cart_id'] ?? 0;
$newQty = $data['quantity'] ?? 1;
$userId = $_SESSION['user_id'] ?? 0;

if ($userId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}

if ($cartId > 0) {
    // 2. Cập nhật số lượng trong Database
    $sql = "UPDATE shopping_cart SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iii", $newQty, $cartId, $userId);
        if (mysqli_stmt_execute($stmt)) {
            // Lấy lại tổng số lượng mới để trả về cho Badge
            $countSql = "SELECT SUM(quantity) as total FROM shopping_cart WHERE user_id = ?";
            $cStmt = mysqli_prepare($conn, $countSql);
            mysqli_stmt_bind_param($cStmt, "i", $userId);
            mysqli_stmt_execute($cStmt);
            $cRes = mysqli_stmt_get_result($cStmt);
            $cRow = mysqli_fetch_assoc($cRes);

            echo json_encode([
                'status' => 'success',
                'cart_count' => $cRow['total']
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật DB']);
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID giỏ hàng không hợp lệ']);
}