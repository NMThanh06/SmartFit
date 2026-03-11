<?php
session_start();
require_once 'includes/config.php'; 

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId <= 0) {
    die("ID sản phẩm không hợp lệ.");
}

// 1. Truy vấn lấy thông tin sản phẩm từ bảng outfits
$sqlProduct = "SELECT * FROM outfits WHERE id = ?";
$stmtProduct = mysqli_prepare($conn, $sqlProduct);
mysqli_stmt_bind_param($stmtProduct, "i", $productId);
mysqli_stmt_execute($stmtProduct);
$resProduct = mysqli_stmt_get_result($stmtProduct);
$product = mysqli_fetch_assoc($resProduct);

if (!$product) {
    die("Không tìm thấy sản phẩm.");
}

// 2. Truy vấn lấy danh sách Size từ bảng outfits_sizes
$sqlSizes = "SELECT size_name FROM outfit_sizes WHERE outfit_id = ?";
$stmtSizes = mysqli_prepare($conn, $sqlSizes);
mysqli_stmt_bind_param($stmtSizes, "i", $productId);
mysqli_stmt_execute($stmtSizes);
$resSizes = mysqli_stmt_get_result($stmtSizes);

$sizes = [];
while ($row = mysqli_fetch_assoc($resSizes)) {
    $sizes[] = $row['size_name'];
}

// 3. Hàm Việt hóa dữ liệu (Translate)
function translateData($data) {
    if (empty($data)) return 'Chưa xác định';

    // Bảng từ điển dịch thuật theo yêu cầu của bạn
    $dictionary = [
        // Phong cách (Style)
        'basic'     => 'Cơ bản',
        'street'    => 'Đường phố',
        'vintage'   => 'Cổ điển',
        
        // Phù hợp (Occasion)
        'study'     => 'Đi học',
        'goout'     => 'Đi chơi',
        'date'      => 'Hẹn hò',
        
        // Độ rộng (Fit)
        'regular'   => 'Vừa vặn',
        'oversized' => 'Rộng',
        'slim'      => 'Ôm',

        // Giới tính
        'male'      => 'Nam',
        'female'    => 'Nữ'
    ];

    // Làm sạch chuỗi (xóa ngoặc vuông, dấu nháy)
    $cleanStr = str_replace(['[', ']', '"'], '', $data);
    $items = explode(',', $cleanStr);
    
    $translatedItems = [];
    foreach ($items as $item) {
        $key = trim(strtolower($item));
        // Nếu có trong từ điển thì dịch, không thì giữ nguyên và viết hoa chữ cái đầu
        $translatedItems[] = isset($dictionary[$key]) ? $dictionary[$key] : ucfirst($item);
    }

    return implode(', ', $translatedItems);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - SmartFit</title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .size-btn { margin-right: 10px; padding: 8px 15px; border: 1px solid #ccc; background: #fff; cursor: pointer; }
        .info-box { background: #f4f4f4; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>

    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <p>Giá: <b style="color:red;"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</b></p>

    <div class="info-box">
        <p><strong>Phong cách:</strong> <?php echo translateData($product['style']); ?></p>
        <p><strong>Phù hợp:</strong> <?php echo translateData($product['occasion']); ?></p>
        <p><strong>Độ rộng (Fit):</strong> <?php echo translateData($product['fit'] ?? ''); ?></p>
    </div>

    <div class="size-section">
        <p><strong>Danh sách Size:</strong></p>
        <?php if (count($sizes) > 0): ?>
            <?php foreach ($sizes as $size): ?>
                <button class="size-btn"><?php echo htmlspecialchars($size); ?></button>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: gray;">Sản phẩm này hiện chưa có size trong hệ thống.</p>
        <?php endif; ?>
    </div>

</body>
</html>