<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-add.php');
    exit;
}

// Thu thập dữ liệu từ Form
$name = $_POST['name'] ?? '';
$type = $_POST['type'] ?? '';
$gender = $_POST['gender'] ?? [];
$occasion = $_POST['occasion'] ?? [];
$style = $_POST['style'] ?? [];
$color = $_POST['color'] ?? '';
$fit = $_POST['fit'] ?? [];
$weather = $_POST['weather'] ?? [];

// Xử lý tải hình ảnh
$targetDir = "../assets/img/";
// Tạo tên file duy nhất bằng timestamp
$fileName = time() . '_' . basename($_FILES["image"]["name"]);
$targetFilePath = $targetDir . $fileName;
$dbPath = "../assets/img/" . $fileName; // link to json 

if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
    
    // Đọc file JSON hiện có
    $jsonFile = 'outfits.json';
    $currentData = json_decode(file_get_contents($jsonFile), true);

    // Tạo ID mới (ID cao nhất + 1)
    $maxId = 0;
    foreach ($currentData['items'] as $item) {
        if ($item['id'] > $maxId) $maxId = $item['id'];
    }

    // Tạo đối tượng món đồ mới
    $newItem = [
        "id" => $maxId + 1,
        "type" => $type,
        "name" => $name,
        "gender" => $gender,
        "occasion" => $occasion,
        "style" => $style,
        "color" => $color,
        "fit" => $fit,
        "weather" => $weather,
        "image" => $dbPath
    ];

    // Thêm vào mảng và ghi lại vào file
    $currentData['items'][] = $newItem;
    
    if (file_put_contents($jsonFile, json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $_SESSION['success'] = "Thêm trang phục thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi ghi vào file dữ liệu.";
    }

} else {
    $_SESSION['error'] = "Lỗi khi tải hình ảnh lên server.";
}

// Quay lại trang admin
header('Location: admin-add.php');
exit;