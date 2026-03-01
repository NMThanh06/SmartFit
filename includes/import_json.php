<?php
require_once 'config.php';

// 1. Đọc nội dung file JSON
$json_data = file_get_contents('outfits.json');
$parsed_data = json_decode($json_data, true);

// Kiểm tra xem có bọc trong "items" không
if (!isset($parsed_data['items']) || !is_array($parsed_data['items'])) {
    die("Lỗi: Không tìm thấy mảng 'items' trong file JSON.");
}

$outfits_data = $parsed_data['items'];

// 2. Lặp qua từng sản phẩm trong mảng items
foreach ($outfits_data as $item) {
    // ---- BƯỚC 1: THÊM VÀO BẢNG CHÍNH (outfits) ----
    
    // Xử lý các trường cơ bản
    $name  = mysqli_real_escape_string($conn, $item['name'] ?? '');
    $type  = mysqli_real_escape_string($conn, $item['type'] ?? '');
    $color = mysqli_real_escape_string($conn, $item['color'] ?? '');
    $price = (int)($item['price'] ?? 0);
    $image = mysqli_real_escape_string($conn, $item['image'] ?? '');

    // SỬA Ở ĐÂY: Dùng json_encode để giữ nguyên định dạng mảng mảng JSON thay vì implode
    $gender   = isset($item['gender']) ? mysqli_real_escape_string($conn, json_encode($item['gender'], JSON_UNESCAPED_UNICODE)) : '[]';
    $occasion = isset($item['occasion']) ? mysqli_real_escape_string($conn, json_encode($item['occasion'], JSON_UNESCAPED_UNICODE)) : '[]';
    $style    = isset($item['style']) ? mysqli_real_escape_string($conn, json_encode($item['style'], JSON_UNESCAPED_UNICODE)) : '[]';
    $weather  = isset($item['weather']) ? mysqli_real_escape_string($conn, json_encode($item['weather'], JSON_UNESCAPED_UNICODE)) : '[]';
    $fit      = isset($item['fit']) ? mysqli_real_escape_string($conn, json_encode($item['fit'], JSON_UNESCAPED_UNICODE)) : '[]';

    $sql_outfit = "INSERT INTO outfits (name, type, color, price, image, style, occasion, weather, fit, gender) 
                   VALUES ('$name', '$type', '$color', $price, '$image', '$style', '$occasion', '$weather', '$fit', '$gender')";

    $sql_outfit = "INSERT INTO outfits (name, type, color, price, image, style, occasion, weather, fit, gender) 
                   VALUES ('$name', '$type', '$color', $price, '$image', '$style', '$occasion', '$weather', '$fit', '$gender')";

    if (mysqli_query($conn, $sql_outfit)) {
        // Lấy ID vừa được tạo tự động cho bảng outfits
        $outfit_id = mysqli_insert_id($conn);
        echo "<p style='color:green;'>Đã thêm: $name (ID: $outfit_id)</p>";

        // ---- BƯỚC 2: THÊM VÀO BẢNG CON (outfit_sizes) ----
        // Cấu trúc JSON: "sizes": {"S": 10, "M": 20}
        if (!empty($item['sizes']) && is_array($item['sizes'])) {
            foreach ($item['sizes'] as $size_name => $quantity) {
                $s_name = mysqli_real_escape_string($conn, $size_name);
                $qty = (int)$quantity;

                $sql_size = "INSERT INTO outfit_sizes (outfit_id, size_name, quantity) 
                             VALUES ($outfit_id, '$s_name', $qty)";
                mysqli_query($conn, $sql_size);
            }
        }

        // ---- BƯỚC 3: THÊM VÀO BẢNG CON (outfit_conflicts) ----
        // Cấu trúc JSON: "conflicts": ["Quần tây nam âu phục"]
        if (!empty($item['conflicts']) && is_array($item['conflicts'])) {
            foreach ($item['conflicts'] as $conflict_name) {
                $c_name = mysqli_real_escape_string($conn, $conflict_name);
                
                $sql_conflict = "INSERT INTO outfit_conflicts (outfit_id, conflict_name) 
                                 VALUES ($outfit_id, '$c_name')";
                mysqli_query($conn, $sql_conflict);
            }
        }

    } else {
        echo "<p style='color:red;'>Lỗi thêm $name: " . mysqli_error($conn) . "</p>";
    }
}

echo "<h3>Hoàn tất quá trình đẩy dữ liệu JSON lên Database!</h3>";
mysqli_close($conn);
?>