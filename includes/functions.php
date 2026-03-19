<?php
/**
 * Hàm đồng bộ dữ liệu từ Database (3 bảng) sang file outfits.json
 * Giúp AI Stylist luôn nhận diện được sản phẩm mới nhất.
 */
function syncOutfitsToJson($conn)
{
    $jsonFile = __DIR__ . '/outfits.json';

    // TRUY VẤN LẤY DỮ LIỆU ĐẦY ĐỦ
    // o (Outfits): Thông tin chung + nhãn AI
    // c (Colors): Lấy ảnh đầu tiên của sản phẩm để đại diện
    // s (Sizes): Gom nhóm kích cỡ
    $sql = "SELECT o.*, 
            (SELECT color_name FROM outfit_colors WHERE outfit_id = o.id LIMIT 1) as main_color,
            (SELECT image FROM outfit_colors WHERE outfit_id = o.id LIMIT 1) as main_image
            FROM outfits o 
            WHERE o.is_commercial = 1
            ORDER BY o.id DESC";

    $result = mysqli_query($conn, $sql);
    $items = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $outfit_id = $row['id'];

        // Lấy toàn bộ size và số lượng cho trang phục này (gom từ tất cả các màu)
        $sizes = [];
        $size_res = mysqli_query($conn, "SELECT size_name, SUM(quantity) as total_qty FROM outfit_sizes WHERE outfit_id = $outfit_id GROUP BY size_name");
        while ($s = mysqli_fetch_assoc($size_res)) {
            $sizes[$s['size_name']] = (int)$s['total_qty'];
        }

        // Tạo item theo định dạng JSON cũ mà AI mong đợi
        $items[] = [
            'id' => (string)$row['id'],
            'type' => $row['type'],
            'name' => $row['name'],
            'gender' => json_decode($row['gender'], true) ?: [],
            'occasion' => json_decode($row['occasion'], true) ?: [],
            'style' => json_decode($row['style'], true) ?: [],
            'color' => $row['main_color'] ?: 'neutral',
            'fit' => json_decode($row['fit'], true) ?: [],
            'weather' => json_decode($row['weather'], true) ?: ['hot', 'mild', 'cold'],
            'image' => $row['main_image'] ?: '/SmartFit/assets/img/default-placeholder.jpg',
            'price' => (int)$row['price'],
            'sizes' => $sizes,
            'age' => $row['age'] ?: 'All',
            'seller_note' => $row['seller_note'] ?: ''
        ];
    }

    $finalData = ['items' => $items];
    return file_put_contents($jsonFile, json_encode($finalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}
?>
