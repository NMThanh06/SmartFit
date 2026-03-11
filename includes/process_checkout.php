<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'] ?? 0;

if ($userId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thanh toán!']);
    exit;
}

// Lấy thông tin từ form khách hàng gửi lên
$fullname = $data['fullname'] ?? '';
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';
$note = $data['note'] ?? '';
$payment_method = $data['payment_method'] ?? 'cod';

if (empty($fullname) || empty($phone) || empty($address)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc!']);
    exit;
}

// Bắt đầu Transaction (Đảm bảo an toàn dữ liệu)
mysqli_begin_transaction($conn);

try {
    // 1. Tính tổng tiền từ giỏ hàng (Không lấy từ Frontend để tránh bị hack đổi giá)
    $cartSql = "SELECT c.*, o.price FROM shopping_cart c JOIN outfits o ON c.outfit_id = o.id WHERE c.user_id = ?";
    $cartStmt = mysqli_prepare($conn, $cartSql);
    mysqli_stmt_bind_param($cartStmt, "i", $userId);
    mysqli_stmt_execute($cartStmt);
    $cartResult = mysqli_stmt_get_result($cartStmt);

    $totalAmount = 0;
    $cartItems = [];
    while ($row = mysqli_fetch_assoc($cartResult)) {
        $totalAmount += $row['price'] * $row['quantity'];
        $cartItems[] = $row;
    }

    if (count($cartItems) == 0) {
        throw new Exception("Giỏ hàng của bạn đang trống!");
    }

    // 2. Tạo hóa đơn chính (Bảng orders)
    $orderSql = "INSERT INTO orders (user_id, fullname, phone, address, note, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "isssssi", $userId, $fullname, $phone, $address, $note, $payment_method, $totalAmount);
    mysqli_stmt_execute($orderStmt);
    
    // Lấy mã hóa đơn vừa tạo thành công
    $orderId = mysqli_insert_id($conn); 

    // 3. Sao chép đồ từ Giỏ hàng sang Chi tiết hóa đơn (Bảng order_details)
    $detailSql = "INSERT INTO order_details (order_id, outfit_id, size_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $detailStmt = mysqli_prepare($conn, $detailSql);

    foreach ($cartItems as $item) {
        mysqli_stmt_bind_param($detailStmt, "iisii", $orderId, $item['outfit_id'], $item['size_name'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($detailStmt);
    }

    // 4. Xóa sạch giỏ hàng của User này sau khi mua xong
    $clearCartSql = "DELETE FROM shopping_cart WHERE user_id = ?";
    $clearStmt = mysqli_prepare($conn, $clearCartSql);
    mysqli_stmt_bind_param($clearStmt, "i", $userId);
    mysqli_stmt_execute($clearStmt);

    // Chốt giao dịch, lưu vĩnh viễn vào Database
    mysqli_commit($conn);
    
    echo json_encode(['status' => 'success', 'message' => 'Đặt hàng thành công!', 'order_id' => $orderId]);

} catch (Exception $e) {
    // Nếu có bất kỳ lỗi gì xảy ra, quay ngược thời gian (Hủy toàn bộ thao tác)
    mysqli_rollback($conn); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>