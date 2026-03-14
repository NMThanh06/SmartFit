<?php
session_start();
$root = '../';
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

include '../includes/header.php';
?>

<main class="orders-page">
    <div class="grid wide">
        <div class="orders-page__header">
            <a href="<?php echo $root; ?>shop.php" class="orders-page__back">
                <i class="fa-solid fa-chevron-left"></i>
                Quay lại cửa hàng
            </a>
            <h1 class="orders-page__title">Lịch sử đơn hàng</h1>
            <p class="orders-page__subtitle">Theo dõi và quản lý các đơn hàng của bạn</p>
        </div>

        <div class="orders-page__content">
            <?php if (mysqli_num_rows($ordersResult) == 0): ?>
                <div class="orders-empty">
                    <div class="orders-empty__icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <p class="orders-empty__text">Bạn chưa có đơn hàng nào.</p>
                    <a href="<?php echo $root; ?>shop.php" class="button orders-empty__btn">Mua sắm ngay</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php while ($order = mysqli_fetch_assoc($ordersResult)): ?>
                        <div class="order-card">
                            <div class="order-card__header">
                                <div class="order-card__info">
                                    <span class="order-card__id">Mã đơn: #<?php echo $order['id']; ?></span>
                                    <span class="order-card__date">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?php echo date('d/m/Y - H:i', strtotime($order['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="order-card__status">
                                    <span class="status-badge status-badge--<?php echo strtolower($order['status']); ?>">
                                        <?php 
                                            switch($order['status']) {
                                                case 'pending': echo 'Đang chờ'; break;
                                                case 'processing': echo 'Đang chuẩn bị'; break;
                                                case 'shipped': echo 'Đang giao'; break;
                                                case 'completed': echo 'Hoàn tất'; break;
                                                case 'cancelled': echo 'Đã hủy'; break;
                                                default: echo $order['status'];
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-card__items">
                                <?php
                                    $detailSql = "SELECT d.*, o.name, o.image FROM order_details d JOIN outfits o ON d.outfit_id = o.id WHERE d.order_id = ?";
                                    $detailStmt = mysqli_prepare($conn, $detailSql);
                                    mysqli_stmt_bind_param($detailStmt, "i", $order['id']);
                                    mysqli_stmt_execute($detailStmt);
                                    $detailsResult = mysqli_stmt_get_result($detailStmt);
                                    
                                    while ($item = mysqli_fetch_assoc($detailsResult)):
                                ?>
                                    <div class="order-item">
                                        <div class="order-item__img-wrapper">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="order-item__img" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                                        </div>
                                        <div class="order-item__info">
                                            <h4 class="order-item__name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <div class="order-item__meta">
                                                <span>Size: <?php echo htmlspecialchars($item['size_name']); ?></span>
                                                <span class="order-item__separator">|</span>
                                                <span>Số lượng: <?php echo $item['quantity']; ?></span>
                                            </div>
                                        </div>
                                        <div class="order-item__price">
                                            <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <div class="order-card__footer">
                                <div class="order-card__total">
                                    <span class="order-card__total-label">Tổng cộng:</span>
                                    <span class="order-card__total-value"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>