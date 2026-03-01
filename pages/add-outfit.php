<?php
require_once '../includes/config.php';
$message = '';

// 1. SỬA LỖI ĐƯỜNG DẪN: Trỏ đúng vào thư mục includes của dự án
$jsonFile = '../includes/outfits.json'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Đọc dữ liệu JSON hiện tại
    $currentData = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : ['items' => []];

    // Lấy dữ liệu cơ bản
    $name = trim($_POST['name']);
    $price = (int)$_POST['price'];
    $type = $_POST['type'];
    
    // 3. LẤY CÁC THUỘC TÍNH MỚI TỪ FORM
    // Dùng toán tử ?? để nếu người dùng quên tick thì gán mảng rỗng hoặc giá trị mặc định
    $gender = $_POST['gender'] ?? [];
    $occasion = $_POST['occasion'] ?? [];
    $style = $_POST['style'] ?? [];
    $fit = $_POST['fit'] ?? [];
    $color = $_POST['color'] ?? 'neutral'; // Color là chuỗi (string) không phải mảng

    $conflicts = [];
    if (!empty($_POST['conflicts'])) {
        $conflicts = array_filter(array_map('trim', explode(',', $_POST['conflicts'])));
    }

    $sizes = [];
    if (!empty($_POST['size_name']) && !empty($_POST['size_qty'])) {
        foreach ($_POST['size_name'] as $index => $sName) {
            $sName = strtoupper(trim($sName)); 
            $qty = (int)$_POST['size_qty'][$index];
            if ($sName !== '') {
                $sizes[$sName] = $qty;
            }
        }
    }

    // Xử lý Upload Hình ảnh
    $imagePath = '';
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        // Đảm bảo thư mục lưu ảnh tồn tại (tùy chỉnh lại đường dẫn nếu cần)
        $uploadDir = '../assets/img/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['imageFile']['name'], PATHINFO_EXTENSION);
        $newFileName = time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetFilePath)) {
            // Lưu đường dẫn tuyệt đối vào JSON (cập nhật lại thư mục gốc nếu cần)
            $imagePath = '/SmartFit/assets/img/' . $newFileName;
        } else {
            $message = "<div class='alert' style='background:#f8d7da; color:#721c24;'>❌ Lỗi: Không thể lưu file ảnh vào hệ thống!</div>";
        }
    }

    if (strpos($message, 'Lỗi') === false) {
        $existsIndex = -1;
        foreach ($currentData['items'] as $index => $item) {
            if (strtolower($item['name']) === strtolower($name)) {
                $existsIndex = $index;
                break;
            }
        }

        if ($existsIndex !== -1) {
            // CẬP NHẬT
            $currentData['items'][$existsIndex]['price'] = $price;
            // Cập nhật lại các thuộc tính nếu người dùng có sửa form
            $currentData['items'][$existsIndex]['gender'] = $gender;
            $currentData['items'][$existsIndex]['occasion'] = $occasion;
            $currentData['items'][$existsIndex]['style'] = $style;
            $currentData['items'][$existsIndex]['color'] = $color;
            $currentData['items'][$existsIndex]['fit'] = $fit;

            if ($imagePath !== '') {
                $currentData['items'][$existsIndex]['image'] = $imagePath; 
            }
            
            foreach ($sizes as $sName => $qty) {
                if (isset($currentData['items'][$existsIndex]['sizes'][$sName])) {
                    $currentData['items'][$existsIndex]['sizes'][$sName] += $qty;
                } else {
                    $currentData['items'][$existsIndex]['sizes'][$sName] = $qty;
                }
            }
            $message = "<div class='alert success'>✅ Đã cập nhật & cộng dồn số lượng cho: <strong>$name</strong></div>";
        } else {
            // THÊM MỚI
            if ($imagePath === '') {
                $imagePath = '/SmartFit/assets/img/default-placeholder.jpg';
            }

            $newItem = [
                'id' => time(),
                'type' => $type,
                'name' => $name,
                'gender' => $gender, 
                'occasion' => $occasion,
                'style' => $style,
                'color' => $color,
                'fit' => $fit,
                'weather' => ['hot', 'mild', 'cold'], // Thời tiết bạn có thể thêm checkbox tương tự nếu cần
                'image' => $imagePath, 
                'price' => $price,
                'sizes' => $sizes,
                'conflicts' => array_values($conflicts)
            ];
            $currentData['items'][] = $newItem;
            $message = "<div class='alert success'>✨ Đã thêm mới sản phẩm: <strong>$name</strong></div>";
        }

        // --- CODE THÊM VÀO DATABASE ---
            $name_esc = mysqli_real_escape_string($conn, $name);
            $type_esc = mysqli_real_escape_string($conn, $type);
            $color_esc = mysqli_real_escape_string($conn, $color);
            $image_esc = mysqli_real_escape_string($conn, $imagePath);
            $gender_esc = mysqli_real_escape_string($conn, json_encode($gender, JSON_UNESCAPED_UNICODE));
            $occasion_esc = mysqli_real_escape_string($conn, json_encode($occasion, JSON_UNESCAPED_UNICODE));
            $style_esc = mysqli_real_escape_string($conn, json_encode($style, JSON_UNESCAPED_UNICODE));
            $fit_esc = mysqli_real_escape_string($conn, json_encode($fit, JSON_UNESCAPED_UNICODE));
            $weather_esc = mysqli_real_escape_string($conn, json_encode(['hot', 'mild', 'cold'], JSON_UNESCAPED_UNICODE));

            $sql_insert = "INSERT INTO outfits (name, type, color, price, image, style, occasion, weather, fit, gender) 
                           VALUES ('$name_esc', '$type_esc', '$color_esc', $price, '$image_esc', '$style_esc', '$occasion_esc', '$weather_esc', '$fit_esc', '$gender_esc')";
            
            if (mysqli_query($conn, $sql_insert)) {
                $outfit_id = mysqli_insert_id($conn); // Lấy ID vừa tạo
                
                // Lưu bảng sizes
                foreach ($sizes as $sName => $qty) {
                    $sName_esc = mysqli_real_escape_string($conn, $sName);
                    mysqli_query($conn, "INSERT INTO outfit_sizes (outfit_id, size_name, quantity) VALUES ($outfit_id, '$sName_esc', $qty)");
                }
                
                // Lưu bảng conflicts
                foreach ($conflicts as $conflict_name) {
                    $c_name_esc = mysqli_real_escape_string($conn, $conflict_name);
                    mysqli_query($conn, "INSERT INTO outfit_conflicts (outfit_id, conflict_name) VALUES ($outfit_id, '$c_name_esc')");
                }
            }
            // ------------------------------

        // 2. SỬA LỖI ĐƯỜNG DẪN ẢNH: Thêm JSON_UNESCAPED_SLASHES
        file_put_contents($jsonFile, json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm / Cập Nhật Kho Quần Áo</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f4f4f9; 
            padding: 20px; 
        }
        .container { 
            max-width: 700px; 
            margin: 0 auto; background: #fff; 
            padding: 25px; border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }

        h2 { 
            text-align: center; 
            color: #333; 
            border-bottom: 2px solid #eee; 
            padding-bottom: 10px; 
            margin-bottom: 20px;}
        .form-group { 
            margin-bottom: 18px; 
        }

        label.main-label { 
            display: block; 
            font-weight: bold; 
            margin-bottom: 8px; 
            color: #2c3e50; 
        }

        input[type="text"], input[type="number"], select, input[type="file"] { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
        
        /* Style cho Checkbox Group */
        .checkbox-group { 
            background: #fafafa; 
            border: 1px solid #e0e0e0; 
            padding: 10px; 
            border-radius: 4px; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 15px; 
        }

        .checkbox-group label { 
            display: flex; 
            align-items: center; 
            font-weight: normal; 
            cursor: pointer; 
        }

        .checkbox-group input[type="checkbox"] { 
            margin-right: 5px; 
            width: auto; 
        }

        .alert { 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 4px; 
        }

        .alert.success { 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }

        .size-box { 
            border: 1px dashed #aaa; 
            padding: 15px; 
            border-radius: 4px; 
            background: #fafafa; 
        }

        .size-row { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 10px; 
            align-items: center; 
        }

        .size-row input { 
            flex: 1; 
        }

        .btn-remove { 
            background: #ff4d4f; 
            color: white; 
            border: none; 
            padding: 10px; 
            cursor: pointer; 
            border-radius: 4px; 
        }

        .btn-add-size { 
            background: #17a2b8; 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            cursor: pointer; 
            border-radius: 4px; 
            margin-top: 5px; 
        }

        .btn-submit { 
            width: 100%; 
            padding: 15px; 
            background: #28a745; 
            color: white; 
            border: none; 
            font-size: 1.1rem; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-top: 20px; 
            font-weight: bold; 
        }

        .btn-submit:hover { 
            background: #218838; 
        }

        .help-text { 
            font-size: 0.85rem; 
            color: #666; 
            margin-top: 4px; 
            display: block; 
        }

        .btn-back {
                display: inline-block;
                margin-bottom: 15px;
                color: #495057;
                text-decoration: none;
                font-weight: bold;
                font-size: 0.95rem;
                padding: 8px 12px;
                background: #e9ecef;
                border-radius: 4px;
                transition: 0.2s;
            }
        .btn-back:hover {
            background: #ced4da;
            color: #212529;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="/SmartFit/index.php" class="btn-back">⬅ Quay lại Trang chủ</a>
    <h2>Thêm Mới / Nhập Thêm Hàng</h2>
    
    <?= $message ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label class="main-label">Tên trang phục (*)</label>
            <input type="text" name="name" required placeholder="Nhập tên chính xác để cộng dồn nếu đã có...">
        </div>

        <div style="display: flex; gap: 15px;">
            <div class="form-group" style="flex: 1;">
                <label class="main-label">Loại trang phục</label>
                <select name="type">
                    <option value="top">Áo (Top)</option>
                    <option value="bottom">Quần/Váy (Bottom)</option>
                    <option value="shoes">Giày (Shoes)</option>
                    <option value="accessories">Phụ kiện (Accessories)</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label class="main-label">Giá tiền (VNĐ)</label>
                <input type="number" name="price" required placeholder="VD: 250000">
            </div>
        </div>

        <div class="form-group">
            <label class="main-label">Bạn là? (Giới tính)</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="gender[]" value="male" checked> Nam</label>
                <label><input type="checkbox" name="gender[]" value="female"> Nữ</label>
            </div>
        </div>

        <div class="form-group">
            <label class="main-label">Mặc cho dịp gì?</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="occasion[]" value="study" checked> Đi học</label>
                <label><input type="checkbox" name="occasion[]" value="goout"> Đi chơi</label>
                <label><input type="checkbox" name="occasion[]" value="date"> Hẹn hò</label>
            </div>
        </div>

        <div class="form-group">
            <label class="main-label">Phong cách (Style)</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="style[]" value="basic" checked> Basic (Cơ bản)</label>
                <label><input type="checkbox" name="style[]" value="street"> Street (Đường phố)</label>
                <label><input type="checkbox" name="style[]" value="vintage"> Vintage (Cổ điển)</label>
            </div>
        </div>

        <div class="form-group">
            <label class="main-label">Độ rộng (Fit)</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="fit[]" value="regular" checked> Regular (Vừa vặn)</label>
                <label><input type="checkbox" name="fit[]" value="oversized"> Oversized (Rộng)</label>
                <label><input type="checkbox" name="fit[]" value="slim"> Slim (Ôm)</label>
            </div>
        </div>

        <div class="form-group">
            <label class="main-label">Tông màu chủ đạo (Color)</label>
            <select name="color">
                <option value="light">Sáng (Trắng, kem...)</option>
                <option value="dark">Tối (Đen, navy...)</option>
                <option value="neutral" selected>Trung tính (Xám, nâu...)</option>
                <option value="pastel">Pastel (Màu nhạt)</option>
                <option value="colorful">Đa sắc (Sặc sỡ)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="main-label">Tải lên hình ảnh</label>
            <input type="file" name="imageFile" accept="image/*">
            <small class="help-text">Để trống nếu đang "Cập nhật" và muốn giữ nguyên ảnh hiện tại.</small>
        </div>

        <div class="form-group">
            <label class="main-label">Các món xung khắc (Không phối chung)</label>
            <input type="text" name="conflicts" placeholder="VD: Quần jean xanh, Áo thun đỏ...">
            <small class="help-text">Ngăn cách các tên bằng dấu phẩy (,). Bỏ trống nếu không có.</small>
        </div>

        <div class="form-group size-box">
            <label class="main-label">Kích cỡ & Số lượng nhập kho</label>
            <div id="size-container">
                <div class="size-row">
                    <input type="text" name="size_name[]" placeholder="Size (VD: M, XL, 42)" required>
                    <input type="number" name="size_qty[]" placeholder="Số lượng (VD: 10)" required min="1">
                    <button type="button" class="btn-remove" onclick="removeSize(this)">X</button>
                </div>
            </div>
            <button type="button" class="btn-add-size" onclick="addSize()">+ Thêm Size Khác</button>
        </div>

        <button type="submit" class="btn-submit">LƯU VÀO KHO</button>
    </form>
</div>

<script>
    function addSize() {
        const container = document.getElementById('size-container');
        const row = document.createElement('div');
        row.className = 'size-row';
        row.innerHTML = `
            <input type="text" name="size_name[]" placeholder="Size (VD: M, XL, 42)" required>
            <input type="number" name="size_qty[]" placeholder="Số lượng (VD: 10)" required min="1">
            <button type="button" class="btn-remove" onclick="removeSize(this)">X</button>
        `;
        container.appendChild(row);
    }

    function removeSize(btn) {
        const container = document.getElementById('size-container');
        if (container.children.length > 1) {
            btn.parentElement.remove();
        } else {
            alert("Phải có ít nhất 1 size!");
        }
    }
</script>

</body>
</html>