<?php
// 1. Nhúng file kết nối
require_once 'config.php';

// Cài đặt header để báo cho trình duyệt biết dữ liệu trả về là JSON chuẩn
header('Content-Type: application/json; charset=utf-8');

/**
 * SQL Lấy dữ liệu sản phẩm:
 * - Ta sẽ lấy ảnh ĐẦU TIÊN tìm thấy trong bảng outfit_colors để làm ảnh đại diện.
 * - GROUP_CONCAT để lấy tất cả các size của sản phẩm đó.
 */
$sql = "SELECT id, name, price, type FROM outfits ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$items = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $outfit_id = $row['id'];
        
        // Lấy danh sách màu sắc và ảnh tương ứng
        $colors = [];
        $color_res = mysqli_query($conn, "SELECT id, color_name, hex_code, image FROM outfit_colors WHERE outfit_id = $outfit_id");
        while ($c = mysqli_fetch_assoc($color_res)) {
            $colors[] = $c;
        }

        // Lấy danh sách kích cỡ và số lượng, nhóm theo color_id
        $sizes = [];
        $size_res = mysqli_query($conn, "SELECT color_id, size_name, quantity FROM outfit_sizes WHERE outfit_id = $outfit_id");
        while ($s = mysqli_fetch_assoc($size_res)) {
            $sizes[] = $s;
        }

        $items[] = [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'price' => (int) $row['price'],
            'type' => $row['type'],
            'image' => (isset($colors[0]) ? $colors[0]['image'] : '/SmartFit/assets/img/default-placeholder.jpg'),
            'colors' => $colors,
            'sizes' => $sizes
        ];
    }
}

// 4. Đóng gói mảng PHP thành chuẩn JSON
echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Đóng kết nối
mysqli_close($conn);
?>