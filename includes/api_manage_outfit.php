<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';
require_once 'functions.php';

$current_user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['role'] ?? 'guest';

if ($current_user_id <= 0 || !in_array($user_role, ['admin', 'sales'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $id = intval($data['id'] ?? 0);
} else {
    $id = intval($_GET['id'] ?? 0);
}

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

// Kiểm tra quyền sở hữu (trừ admin)
if ($user_role !== 'admin') {
    $chk_sql = "SELECT owner_id FROM outfits WHERE id = $id";
    $chk_res = mysqli_query($conn, $chk_sql);
    $chk_row = mysqli_fetch_assoc($chk_res);
    if (!$chk_row || $chk_row['owner_id'] != $current_user_id) {
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền thao tác trên sản phẩm này']);
        exit;
    }
}

// 1. LẤY CHI TIẾT SẢN PHẨM (Để sửa)
if ($action === 'get_details') {
    $sql = "SELECT * FROM outfits WHERE id = $id";
    $res = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($res);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        exit;
    }

    // Lấy danh sách màu sắc
    $colors = [];
    $c_res = mysqli_query($conn, "SELECT * FROM outfit_colors WHERE outfit_id = $id");
    while ($color = mysqli_fetch_assoc($c_res)) {
        $color_id = $color['id'];
        
        // Lấy danh sách size cho màu này
        $sizes = [];
        $s_res = mysqli_query($conn, "SELECT * FROM outfit_sizes WHERE color_id = $color_id");
        while ($size = mysqli_fetch_assoc($s_res)) {
            $sizes[] = $size;
        }
        
        $color['sizes'] = $sizes;
        $colors[] = $color;
    }

    $product['colors'] = $colors;
    echo json_encode(['success' => true, 'product' => $product]);
    exit;
}

// 2. XÓA SẢN PHẨM
if ($action === 'delete') {
    mysqli_begin_transaction($conn);
    try {
        // Lấy danh sách ảnh để xóa vật lý
        $img_res = mysqli_query($conn, "SELECT image FROM outfit_colors WHERE outfit_id = $id");
        while ($row = mysqli_fetch_assoc($img_res)) {
            $img_path = $row['image'];
            // Chỉ xóa nếu không phải placeholder
            if ($img_path && !strpos($img_path, 'default-placeholder')) {
                // Chuyển đường dẫn web sang đường dẫn vật lý (giả sử base là xampp root)
                $physical_path = $_SERVER['DOCUMENT_ROOT'] . $img_path;
                if (file_exists($physical_path)) {
                    unlink($physical_path);
                }
            }
        }

        // Xóa trong DB (Tận dụng CASCADE nếu có, hoặc xóa thủ công)
        mysqli_query($conn, "DELETE FROM outfit_sizes WHERE outfit_id = $id");
        mysqli_query($conn, "DELETE FROM outfit_colors WHERE outfit_id = $id");
        mysqli_query($conn, "DELETE FROM outfits WHERE id = $id");

        mysqli_commit($conn);
        syncOutfitsToJson($conn); // Đồng bộ lại file JSON
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Thao tác không hợp lệ']);
?>
