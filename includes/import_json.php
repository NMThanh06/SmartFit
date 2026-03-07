
<!-- chạy file này 1 lần trước khi test -->

<?php
require_once 'config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

echo "<h2>BẮT ĐẦU ĐỒNG BỘ HÓA DỮ LIỆU...</h2>";

try {
    // ========================================================
    // DỌN SẠCH DATABASE CŨ (Xóa tận gốc không để lại rác)
    // ========================================================
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0;"); // Tạm thời tắt kiểm tra khóa ngoại
    
    mysqli_query($conn, "TRUNCATE TABLE outfit_conflicts;");
    mysqli_query($conn, "TRUNCATE TABLE outfit_sizes;");
    mysqli_query($conn, "TRUNCATE TABLE outfits;");
    
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1;"); // Bật lại kiểm tra khóa ngoại
    echo "<p style='color:blue;'>Đã dọn sạch Database!.</p>";

    // ========================================================
    // ĐỌC DỮ LIỆU TỪ JSON
    // ========================================================
    $json_data = file_get_contents('outfits.json');
    $parsed_data = json_decode($json_data, true);

    if (!isset($parsed_data['items']) || !is_array($parsed_data['items'])) {
        die("<h3 style='color:red;'>Lỗi: Cấu trúc file JSON sai, không tìm thấy mảng 'items'.</h3>");
    }

    $outfits_data = $parsed_data['items'];
    $count = 0;

    // ========================================================
    // BƠM DỮ LIỆU VÀO MYSQL CHÍNH XÁC TỪNG LI TỪNG TÍ
    // ========================================================
    foreach ($outfits_data as $item) {
        
        $id    = (int)($item['id'] ?? 0);
        $name  = mysqli_real_escape_string($conn, $item['name'] ?? '');
        $type  = mysqli_real_escape_string($conn, $item['type'] ?? '');
        $color = mysqli_real_escape_string($conn, $item['color'] ?? '');
        $price = (int)($item['price'] ?? 0);
        $image = mysqli_real_escape_string($conn, $item['image'] ?? '');

        // Chuyển mảng thành chuỗi JSON an toàn
        $gender   = isset($item['gender']) ? mysqli_real_escape_string($conn, json_encode($item['gender'], JSON_UNESCAPED_UNICODE)) : '[]';
        $occasion = isset($item['occasion']) ? mysqli_real_escape_string($conn, json_encode($item['occasion'], JSON_UNESCAPED_UNICODE)) : '[]';
        $style    = isset($item['style']) ? mysqli_real_escape_string($conn, json_encode($item['style'], JSON_UNESCAPED_UNICODE)) : '[]';
        $weather  = isset($item['weather']) ? mysqli_real_escape_string($conn, json_encode($item['weather'], JSON_UNESCAPED_UNICODE)) : '[]';
        $fit      = isset($item['fit']) ? mysqli_real_escape_string($conn, json_encode($item['fit'], JSON_UNESCAPED_UNICODE)) : '[]';

        // 3.1. THÊM VÀO BẢNG OUTFITS
        $sql_outfit = "INSERT INTO outfits (id, name, type, color, price, image, style, occasion, weather, fit, gender) 
                       VALUES ($id, '$name', '$type', '$color', $price, '$image', '$style', '$occasion', '$weather', '$fit', '$gender')";
        mysqli_query($conn, $sql_outfit);

        // 3.2. THÊM KÍCH CỠ (Nếu có)
        if (!empty($item['sizes']) && is_array($item['sizes'])) {
            foreach ($item['sizes'] as $size_name => $quantity) {
                $s_name = mysqli_real_escape_string($conn, $size_name);
                $qty = (int)$quantity;
                mysqli_query($conn, "INSERT INTO outfit_sizes (outfit_id, size_name, quantity) VALUES ($id, '$s_name', $qty)");
            }
        }

        // 3.3. THÊM ĐỒ XUNG KHẮC (Nếu có)
        if (!empty($item['conflicts']) && is_array($item['conflicts'])) {
            foreach ($item['conflicts'] as $conflict_name) {
                $c_name = mysqli_real_escape_string($conn, $conflict_name);
                mysqli_query($conn, "INSERT INTO outfit_conflicts (outfit_id, conflict_name) VALUES ($id, '$c_name')");
            }
        }

        $count++;
        echo "<p style='color:green;'>Đã Thêm: <strong>$name</strong> (ID JSON: $id)</p>";
    }

    echo "<h3>HOÀN TẤT!$count/".count($outfits_data)."</h3>";

} catch (Exception $e) {
    // NẾU CÓ BẤT KỲ LỖI NÀO (Dù là nhỏ nhất), NÓ SẼ IN RA ĐÂY
    echo "<h3 style='color:red;'>LỖI DATABASE: " . $e->getMessage() . "</h3>";
    echo "<p>Vui lòng kiểm tra lại cấu trúc file JSON hoặc các cột trong MySQL.</p>";
}

mysqli_close($conn);
?>