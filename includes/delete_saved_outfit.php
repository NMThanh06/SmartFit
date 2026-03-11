<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}

// Lấy dữ liệu ID từ Fetch API gửi lên
$data = json_decode(file_get_contents('php://input'), true);
$saved_id = $data['id'] ?? null;

if (!$saved_id) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu ID bộ đồ cần xóa.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Thực hiện xóa (Chỉ xóa nếu đúng là của User đó để bảo mật)
$sql = "DELETE FROM saved_outfits WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $saved_id, $userId);
    
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Đã xóa bộ đồ khỏi bộ sưu tập.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy bộ đồ hoặc bạn không có quyền xóa.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi MySQL: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị lệnh SQL.']);
}

mysqli_close($conn);
?>