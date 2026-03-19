<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Lấy dữ liệu ID món hàng gửi lên
$data = json_decode(file_get_contents('php://input'), true);
$cartId = $data['cart_id'] ?? 0;
$userId = $_SESSION['user_id'] ?? 0;

if ($userId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}

if ($cartId > 0) {
    // Xóa sản phẩm khỏi bảng shopping_cart
    $sql = "DELETE FROM shopping_cart WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $cartId, $userId);
        if (mysqli_stmt_execute($stmt)) {
            
            // Tính lại tổng số lượng còn lại trong giỏ để cập nhật con số màu đỏ (Badge)
            $countSql = "SELECT SUM(quantity) as total FROM shopping_cart WHERE user_id = ?";
            $cStmt = mysqli_prepare($conn, $countSql);
            mysqli_stmt_bind_param($cStmt, "i", $userId);
            mysqli_stmt_execute($cStmt);
            $cRes = mysqli_stmt_get_result($cStmt);
            $cRow = mysqli_fetch_assoc($cRes);
            
            // Nếu giỏ hàng trống không còn gì, SUM sẽ trả về null, ta set về 0
            $totalCount = $cRow['total'] ?? 0; 

            echo json_encode(['status' => 'success', 'cart_count' => $totalCount]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi MySQL không thể xóa']);
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID giỏ hàng không hợp lệ']);
}