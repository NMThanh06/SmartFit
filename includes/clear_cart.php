<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? 0;

if ($userId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}

// Xóa tất cả các món đồ của User này trong bảng shopping_cart
$sql = "DELETE FROM shopping_cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    if (mysqli_stmt_execute($stmt)) {
        // Trả về số lượng 0 để reset Badge ngay lập tức
        echo json_encode(['status' => 'success', 'cart_count' => 0]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống, không thể xóa giỏ hàng']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối CSDL']);
}
?>