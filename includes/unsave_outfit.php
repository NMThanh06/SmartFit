<?php
session_start();
include 'config.php'; // File kết nối DB của bạn

$data = json_decode(file_get_contents('php://input'), true);

if (isset($_SESSION['user_id']) && $data) {
    $user_id = $_SESSION['user_id'];
    $top_id = $data['top_id'];
    $bottom_id = $data['bottom_id'];
    $shoes_id = $data['shoes_id'];
    // acc_id có thể null nên cần xử lý kỹ
    $acc_id = !empty($data['acc_id']) ? $data['acc_id'] : null;

    // Truy vấn xóa bản ghi khớp với user và bộ item này
    $sql = "DELETE FROM saved_outfits 
            WHERE user_id = ? AND top_id = ? AND bottom_id = ? AND shoes_id = ?";
    
    if ($acc_id) {
        $sql .= " AND acc_id = ?";
    } else {
        $sql .= " AND acc_id IS NULL";
    }

    $stmt = $conn->prepare($sql);
    if ($acc_id) {
        $stmt->bind_param("iiiii", $user_id, $top_id, $bottom_id, $shoes_id, $acc_id);
    } else {
        $stmt->bind_param("iiii", $user_id, $top_id, $bottom_id, $shoes_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể xóa']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
}
?>