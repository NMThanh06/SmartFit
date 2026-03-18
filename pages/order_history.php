<?php
session_start();
$root = '../';
require_once '../includes/config.php';

// Kiểm tra đăng nhập
$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) {
    header("Location: ../style_outfits.php");
    exit;
}

// Xử lý Tìm kiếm và Sắp xếp
$searchQuery = $_GET['q'] ?? '';
$sortOrder = $_GET['sort'] ?? 'desc';
$allowedSort = ['asc', 'desc'];
if (!in_array($sortOrder, $allowedSort)) $sortOrder = 'desc';

// Lấy toàn bộ đơn hàng của User này, có hỗ trợ tìm kiếm và sắp xếp
$sql = "SELECT * FROM orders WHERE user_id = ? ";
if (!empty($searchQuery)) {
    $sql .= " AND id LIKE ? ";
}
$sql .= " ORDER BY created_at " . ($sortOrder === 'asc' ? 'ASC' : 'DESC');

$stmt = mysqli_prepare($conn, $sql);
if (!empty($searchQuery)) {
    $searchParam = "%$searchQuery%";
    mysqli_stmt_bind_param($stmt, "is", $userId, $searchParam);
} else {
    mysqli_stmt_bind_param($stmt, "i", $userId);
}
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
            <!-- Thanh tìm kiếm và lọc -->
            <div class="order-toolbar">
                <form action="" method="GET" class="order-search">
                    <i class="fa-solid fa-magnifying-glass order-search__icon"></i>
                    <input type="text" name="q" class="order-search__input" 
                           placeholder="Tìm theo mã đơn hàng..." 
                           value="<?php echo htmlspecialchars($searchQuery); ?>">
                </form>

                <div class="order-filter">
                    <span class="order-filter__label">Sắp xếp:</span>
                    <select class="order-filter__select" onchange="window.location.href='?q=<?php echo urlencode($searchQuery); ?>&sort=' + this.value">
                        <option value="desc" <?php echo $sortOrder === 'desc' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="asc" <?php echo $sortOrder === 'asc' ? 'selected' : ''; ?>>Cũ nhất</option>
                    </select>
                </div>
            </div>

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
                                    $detailSql = "
                                        SELECT d.*, o.name, 
                                               (SELECT image FROM outfit_colors WHERE outfit_id = o.id LIMIT 1) as image 
                                        FROM order_details d 
                                        JOIN outfits o ON d.outfit_id = o.id 
                                        WHERE d.order_id = ?";
                                    $detailStmt = mysqli_prepare($conn, $detailSql);
                                    mysqli_stmt_bind_param($detailStmt, "i", $order['id']);
                                    mysqli_stmt_execute($detailStmt);
                                    $detailsResult = mysqli_stmt_get_result($detailStmt);
                                    
                                    while ($item = mysqli_fetch_assoc($detailsResult)):
                                ?>
                                    <div class="order-item">
                                        <div class="order-item__img-wrapper">
                                            <img src="<?php echo htmlspecialchars($item['image'] ?? '/SmartFit/assets/img/default-placeholder.jpg'); ?>" class="order-item__img" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                                        </div>
                                        <div class="order-item__info">
                                            <h4 class="order-item__name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <div class="order-item__meta">
                                                <span>Size: <?php echo htmlspecialchars($item['size_name']); ?></span>
                                                <span class="order-item__separator">|</span>
                                                <span>Số lượng: <?php echo $item['quantity']; ?></span>
                                            </div>
                                        </div>
                                        <div class="order-item__price" style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                                            <div><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</div>
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <a href="<?php echo $root; ?>pages/review.php?order_id=<?php echo $order['id']; ?>&outfit_id=<?php echo $item['outfit_id']; ?>" class="button" style="padding: 5px 10px; font-size: 1.2rem; background: #ee4d2d; color: white; border-radius: 4px; text-decoration: none; border: none;">Đánh giá sản phẩm</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                                <div class="order-card__footer">
                                    <div class="order-card__total">
                                        <span class="order-card__total-label">Tổng cộng:</span>
                                        <span class="order-card__total-value"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫</span>
                                    </div>
                                    <button class="order-card__btn-detail" 
                                        onclick="openOrderDetail(<?php echo htmlspecialchars(json_encode($order)); ?>, this)">
                                        <i class="fa-solid fa-circle-info"></i> Xem chi tiết
                                    </button>
                                </div>
                            </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Order Detail Modal -->
<div id="orderDetailModal" class="order-modal">
    <div class="order-modal__content">
        <div class="order-modal__header">
            <h2 class="order-modal__title">Chi tiết đơn hàng <span id="modalOrderId"></span></h2>
            <button class="order-modal__close" onclick="closeOrderDetail()">&times;</button>
        </div>
        
        <div class="order-modal__body">
            <div class="order-modal__section">
                <h3 class="order-modal__section-title"><i class="fa-solid fa-truck"></i> Thông tin giao hàng</h3>
                <div class="order-modal__info-grid">
                    <div class="order-modal__info-item">
                        <span class="label">Người nhận:</span>
                        <span id="modalFullname" class="value"></span>
                    </div>
                    <div class="order-modal__info-item">
                        <span class="label">Số điện thoại:</span>
                        <span id="modalPhone" class="value"></span>
                    </div>
                    <div class="order-modal__info-item">
                        <span class="label">Thời gian đặt:</span>
                        <span id="modalOrderDate" class="value"></span>
                    </div>
                    <div class="order-modal__info-item order-modal__info-item--full">
                        <span class="label">Địa chỉ:</span>
                        <span id="modalAddress" class="value"></span>
                    </div>
                    <div class="order-modal__info-item order-modal__info-item--full">
                        <span class="label">Ghi chú:</span>
                        <span id="modalNote" class="value italic"></span>
                    </div>
                </div>
            </div>

            <div class="order-modal__section">
                <h3 class="order-modal__section-title"><i class="fa-solid fa-credit-card"></i> Thanh toán</h3>
                <div class="order-modal__info-grid">
                    <div class="order-modal__info-item">
                        <span class="label">Phương thức:</span>
                        <span id="modalPaymentMethod" class="value uppercase"></span>
                    </div>
                    <div class="order-modal__info-item">
                        <span class="label">Trạng thái:</span>
                        <span id="modalPaymentStatus" class="value uppercase"></span>
                    </div>
                </div>
            </div>

            <div class="order-modal__section">
                <h3 class="order-modal__section-title"><i class="fa-solid fa-list-ul"></i> Sản phẩm đã đặt</h3>
                <div id="modalOrderItems" class="order-modal__items">
                    <!-- Danh sách sản phẩm sẽ được copy vào đây -->
                </div>
            </div>
        </div>

        <div class="order-modal__footer">
            <div class="order-modal__total">
                <span>Tổng tiền:</span>
                <span id="modalTotalValue"></span>
            </div>
            <button class="order-modal__btn-close" onclick="closeOrderDetail()">Đóng</button>
        </div>
    </div>
</div>

<style>
    /* Nút Xem chi tiết */
    .order-card__btn-detail {
        padding: 8px 16px;
        background-color: #f5f5f7;
        color: var(--apple-black);
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 1.3rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .order-card__btn-detail:hover {
        background-color: #eeeef0;
        border-color: #d1d1d6;
        transform: translateY(-1px);
    }

    /* Modal Styles */
    .order-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .order-modal.active {
        display: flex;
        opacity: 1;
    }

    .order-modal__content {
        background-color: #fff;
        width: 90%;
        max-width: 650px;
        max-height: 85vh;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalSlideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes modalSlideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .order-modal__header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fff;
    }

    .order-modal__title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--apple-black);
    }

    .order-modal__title span {
        color: var(--primary-blue);
    }

    .order-modal__close {
        background: none;
        border: none;
        font-size: 2.8rem;
        color: var(--apple-grey);
        cursor: pointer;
        line-height: 1;
    }

    .order-modal__body {
        padding: 25px;
        overflow-y: auto;
        flex: 1;
    }

    .order-modal__section {
        margin-bottom: 25px;
    }

    .order-modal__section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--apple-black);
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f5f5f7;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .order-modal__section-title i {
        color: var(--primary-blue);
    }

    .order-modal__info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .order-modal__info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .order-modal__info-item--full {
        grid-column: span 2;
    }

    .order-modal__info-item .label {
        font-size: 1.2rem;
        color: var(--apple-grey);
        font-weight: 500;
    }

    .order-modal__info-item .value {
        font-size: 1.4rem;
        color: var(--apple-black);
        font-weight: 600;
    }

    .order-modal__info-item .italic {
        font-style: italic;
        color: #555;
        font-weight: 400;
    }

    .order-modal__items {
        background: #f9f9fb;
        border-radius: 12px;
        padding: 10px;
    }

    .order-modal__items .order-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .order-modal__items .order-item:last-child {
        border-bottom: none;
    }

    .order-modal__footer {
        padding: 20px 25px;
        border-top: 1px solid #f0f0f2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fff;
    }

    .order-modal__total {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--apple-black);
    }

    #modalTotalValue {
        color: var(--primary-blue);
        margin-left: 8px;
    }

    .order-modal__btn-close {
        padding: 10px 25px;
        background-color: var(--apple-black);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 1.4rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .order-modal__btn-close:hover {
        opacity: 0.8;
    }

    .uppercase { text-transform: uppercase; }

    @media (max-width: 600px) {
        .order-modal__info-grid { grid-template-columns: 1fr; }
        .order-modal__info-item--full { grid-column: span 1; }
    }
</style>

<script>
    function openOrderDetail(order, btn) {
        const modal = document.getElementById('orderDetailModal');
        
        // Điền thông tin cơ bản
        document.getElementById('modalOrderId').innerText = '#' + order.id;
        document.getElementById('modalFullname').innerText = order.fullname || 'Chưa cập nhật';
        document.getElementById('modalPhone').innerText = order.phone || 'Chưa cập nhật';
        
        // Định dạng ngày tháng từ YYYY-MM-DD HH:MM:SS
        const date = new Date(order.created_at);
        const dateString = date.toLocaleDateString('vi-VN') + ' - ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        document.getElementById('modalOrderDate').innerText = dateString;

        document.getElementById('modalAddress').innerText = order.address || 'Chưa cập nhật';
        document.getElementById('modalNote').innerText = order.note ? '"' + order.note + '"' : 'Không có ghi chú';
        document.getElementById('modalPaymentMethod').innerText = order.payment_method;
        document.getElementById('modalPaymentStatus').innerText = order.payment_status;
        
        const totalFormatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount);
        document.getElementById('modalTotalValue').innerText = totalFormatted;

        // Copy danh sách sản phẩm từ thẻ card hiện tại
        const orderCard = btn.closest('.order-card');
        const itemsList = orderCard.querySelector('.order-card__items').cloneNode(true);
        const modalItemsContainer = document.getElementById('modalOrderItems');
        modalItemsContainer.innerHTML = '';
        modalItemsContainer.appendChild(itemsList);

        // Hiển thị modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Chống scroll body
    }

    function closeOrderDetail() {
        const modal = document.getElementById('orderDetailModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Đóng modal khi click ra ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('orderDetailModal');
        if (event.target == modal) {
            closeOrderDetail();
        }
    }
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>