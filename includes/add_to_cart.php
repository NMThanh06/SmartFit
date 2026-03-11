<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để thêm vào giỏ hàng!']);
    exit;
}

// 2. Lấy dữ liệu từ Fetch API
$data = json_decode(file_get_contents('php://input'), true);
$userId   = $_SESSION['user_id'];
$outfitId = $data['outfit_id'] ?? null;
$size     = $data['size'] ?? 'M'; // Mặc định là M nếu không chọn
$qty      = $data['quantity'] ?? 1;

if (!$outfitId) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ!']);
    exit;
}

// 3. Logic: Kiểm tra xem món này (cùng size) đã có trong giỏ chưa
$checkSql = "SELECT id, quantity FROM shopping_cart WHERE user_id = ? AND outfit_id = ? AND size_name = ?";
$stmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($stmt, "iis", $userId, $outfitId, $size);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row) {
    // NẾU CÓ RỒI -> CẬP NHẬT CỘNG DỒN SỐ LƯỢNG
    $newQty = $row['quantity'] + $qty;
    $updateSql = "UPDATE shopping_cart SET quantity = ? WHERE id = ?";
    $updStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updStmt, "ii", $newQty, $row['id']);
    $success = mysqli_stmt_execute($updStmt);
} else {
    // NẾU CHƯA CÓ -> THÊM MỚI DÒNG DỮ LIỆU
    $insertSql = "INSERT INTO shopping_cart (user_id, outfit_id, size_name, quantity) VALUES (?, ?, ?, ?)";
    $insStmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($insStmt, "iisi", $userId, $outfitId, $size, $qty);
    $success = mysqli_stmt_execute($insStmt);
}

if ($success) {
    // Lấy lại tổng số lượng trong giỏ để cập nhật Badge ngay lập tức
    $countSql = "SELECT SUM(quantity) as total FROM shopping_cart WHERE user_id = ?";
    $cStmt = mysqli_prepare($conn, $countSql);
    mysqli_stmt_bind_param($cStmt, "i", $userId);
    mysqli_stmt_execute($cStmt);
    $cRes = mysqli_stmt_get_result($cStmt);
    $cRow = mysqli_fetch_assoc($cRes);
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Đã thêm vào giỏ hàng!',
        'cart_count' => $cRow['total']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống, vui lòng thử lại!']);
}