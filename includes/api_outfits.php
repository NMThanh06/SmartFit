<?php
// 1. Nhúng file kết nối
require_once 'config.php';

// Cài đặt header để báo cho trình duyệt biết dữ liệu trả về là JSON chuẩn
header('Content-Type: application/json; charset=utf-8');

// 2. Viết câu lệnh SQL lấy dữ liệu từ bảng outfits
// Ở trang chủ này, ta chỉ cần id, name, price và image cho nhẹ
$sql = "SELECT id, name, price, image FROM outfits ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$items = [];

// 3. Đổ dữ liệu từ MySQL vào mảng PHP
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = [
            'id' => (int) $row['id'],
            'name' => $row['name'],
            'price' => (int) $row['price'],
            'image' => $row['image']
        ];
    }
}

// 4. Đóng gói mảng PHP thành chuẩn JSON có bọc trong key "items" cho khớp với code JS của bạn
echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Đóng kết nối
mysqli_close($conn);
?>