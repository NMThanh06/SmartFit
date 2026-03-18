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
// --- 2. XỬ LÝ THAM SỐ LỌC ---
$type = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : 'all';
$sort = isset($_GET['sort']) && $_GET['sort'] !== '' ? mysqli_real_escape_string($conn, $_GET['sort']) : 'popular';
$size = isset($_GET['size']) ? mysqli_real_escape_string($conn, $_GET['size']) : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
$q = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

$owner_id = isset($_GET['owner_id']) ? intval($_GET['owner_id']) : 0;

// --- 3. XÂY DỰNG SQL QUERY ĐỘNG ---
$where_clauses = ["is_commercial = 1"];

if ($owner_id > 0) {
    $where_clauses[] = "owner_id = $owner_id";
}

if ($q !== '') {
    $where_clauses[] = "(name LIKE '%$q%' OR description LIKE '%$q%' OR seller_note LIKE '%$q%')";
}

if ($type !== 'all') {
    if ($type === 'accessory_shoes') {
        $where_clauses[] = "(type = 'accessory' OR type = 'shoes')";
    } else {
        $where_clauses[] = "type = '$type'";
    }
}

if ($size !== '') {
    // Lọc theo size: Sản phẩm phải có ít nhất một màu có size này và số lượng > 0
    $where_clauses[] = "id IN (SELECT DISTINCT outfit_id FROM outfit_sizes WHERE size_name = '$size' AND quantity > 0)";
}

if ($min_price > 0) {
    $where_clauses[] = "price >= $min_price";
}
if ($max_price > 0) {
    $where_clauses[] = "price <= $max_price";
}

$where_str = implode(" AND ", $where_clauses);

// Sắp xếp
$order_by = "( (IFNULL(avg_rating, 0) * 10) + LOG10(IFNULL(review_count, 0) + 1) * 5 + LOG10(IFNULL(total_sold, 0) + 1) * 5 ) DESC, created_at DESC"; // Mặc định: Phổ biến
if ($sort === 'newest') $order_by = "created_at DESC";
elseif ($sort === 'price-asc') $order_by = "price ASC";
elseif ($sort === 'price-desc') $order_by = "price DESC";
elseif ($sort === 'oldest') $order_by = "created_at ASC";

$sql = "SELECT id, name, price, type FROM outfits WHERE $where_str ORDER BY $order_by";
$result = mysqli_query($conn, $sql);

$items = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $outfit_id = $row['id'];
        
        // Lấy danh sách màu sắc và ảnh tương ứng
        $colors = [];
        $color_res = mysqli_query($conn, "SELECT id, color_name, image FROM outfit_colors WHERE outfit_id = $outfit_id");
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
            'image' => (isset($colors[0]) ? $colors[0]['image'] : '/assets/img/default-placeholder.jpg'),
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