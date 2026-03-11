<?php
session_start();
require_once 'config.php'; // Gọi trực tiếp vì nằm cùng thư mục

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}

// Đọc và giải mã dữ liệu JSON
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Không đọc được dữ liệu gửi lên.']);
    exit;
}

// Kiểm tra các ID bắt buộc
if (empty($data['top_id']) || empty($data['bottom_id']) || empty($data['shoes_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu món đồ chính (áo, quần, giày).']);
    exit;
}

// Xử lý phụ kiện và lưu DB
$userId    = $_SESSION['user_id'];
$topId     = $data['top_id'];
$bottomId  = $data['bottom_id'];
$shoesId   = $data['shoes_id'];

// Ép kiểu phụ kiện
$accId = (isset($data['acc_id']) && $data['acc_id'] !== "" && $data['acc_id'] !== 'null') ? $data['acc_id'] : null;
$styleName = !empty($data['style_name']) ? $data['style_name'] : 'Outfit của tôi';

$sql = "INSERT INTO saved_outfits (user_id, top_id, bottom_id, shoes_id, acc_id, style_name) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
        
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iiiiis", $userId, $topId, $bottomId, $shoesId, $accId, $styleName);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Đã lưu trang phục thành công!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi lưu DB: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị lệnh SQL.']);
}

mysqli_close($conn);
?>