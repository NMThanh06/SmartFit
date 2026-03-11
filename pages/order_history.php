<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) {
    header("Location: ../index.php");
    exit;
}

// Lấy toàn bộ đơn hàng của User này, sắp xếp mới nhất lên đầu
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$ordersResult = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đơn hàng - SmartFit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; padding: 20px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; }
        h2 { border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        
        .order-card { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 10px; }
        .order-id { font-size: 1.1rem; font-weight: bold; color: #2c3e50; }
        .order-date { color: #7f8c8d; font-size: 0.9rem; }
        
        /* Trạng thái đơn hàng */
        .status { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: #28a745; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }

        /* Chi tiết món hàng */
        .order-item { display: flex; align-items: center; margin-top: 15px; gap: 15px; }
        .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #eee; }
        .item-info { flex: 1; }
        .item-name { font-weight: 500; margin: 0 0 5px 0; }
        .item-meta { color: #666; font-size: 0.9rem; margin: 0; }
        .item-price { font-weight: bold; color: #e74c3c; }

        .order-footer { margin-top: 20px; text-align: right; border-top: 1px solid #eee; padding-top: 15px; }
        .total-label { font-size: 1.1rem; }
        .total-amount { font-size: 1.4rem; color: #e74c3c; font-weight: bold; }
        
        .btn-back { display: inline-block; text-decoration: none; color: #555; font-weight: bold; margin-bottom: 20px; }
        .btn-back:hover { color: #4CAF50; }
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ</a>
    <h2>Lịch sử đơn hàng của bạn</h2>

    <?php if (mysqli_num_rows($ordersResult) == 0): ?>
        <div class="order-card" style="text-align: center; padding: 40px;">
            <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="../shop.php" style="color: #4CAF50; font-weight: bold;">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        
        <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Đơn hàng #<?php echo $order['id']; ?></div>
                        <div class="order-date"><i class="fa-regular fa-clock"></i> <?php echo date('H:i - d/m/Y', strtotime($order['created_at'])); ?></div>
                    </div>
                    <div>
                        <span class="status status-<?php echo strtolower($order['status']); ?>">
                            <?php 
                                switch($order['status']) {
                                    case 'pending': echo 'Đang chờ xử lý'; break;
                                    case 'processing': echo 'Đang chuẩn bị hàng'; break;
                                    case 'shipped': echo 'Đang giao hàng'; break;
                                    case 'completed': echo 'Đã giao thành công'; break;
                                    case 'cancelled': echo 'Đã hủy'; break;
                                    default: echo $order['status'];
                                }
                            ?>
                        </span>
                    </div>
                </div>

                <?php
                    $detailSql = "SELECT d.*, o.name, o.image FROM order_details d JOIN outfits o ON d.outfit_id = o.id WHERE d.order_id = ?";
                    $detailStmt = mysqli_prepare($conn, $detailSql);
                    mysqli_stmt_bind_param($detailStmt, "i", $order['id']);
                    mysqli_stmt_execute($detailStmt);
                    $detailsResult = mysqli_stmt_get_result($detailStmt);
                    
                    while ($item = mysqli_fetch_assoc($detailsResult)):
                ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" class="item-img" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                        <div class="item-info">
                            <p class="item-name"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="item-meta">Size: <?php echo htmlspecialchars($item['size_name']); ?> | Số lượng: <?php echo $item['quantity']; ?></p>
                        </div>
                        <div class="item-price">
                            <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="order-footer">
                    <span class="total-label">Thành tiền:</span>
                    <span class="total-amount"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</span>
                </div>
            </div>
        <?php endwhile; ?>

    <?php endif; ?>
</div>

</body>
</html>