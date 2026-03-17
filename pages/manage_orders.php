<?php
/**
 * pages/manage_orders.php
 * Hệ thống Quản lý Đơn hàng SmartFit (Dành cho Admin/Sales)
 */

require_once '../middleware.php'; // Kích hoạt RBAC (Chỉ Admin/Sales mới được vào)
$base_dir = '../';
require_once $base_dir . 'includes/config.php';
require_once $base_dir . 'includes/functions.php';

// ========================================
// XỬ LÝ CẬP NHẬT TRẠNG THÁI (POST)
// ========================================
$notification = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Sử dụng Prepared Statement để bảo mật
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    
    // Nếu là sales, chỉ cho phép update nế là shop của mình
    if ($role === 'sales') {
        $update_sql .= " AND shop_id = ?";
    }

    $stmt = mysqli_prepare($conn, $update_sql);
    
    if ($role === 'sales') {
        mysqli_stmt_bind_param($stmt, "sii", $new_status, $order_id, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $notification = ['type' => 'success', 'msg' => "Đã cập nhật trạng thái đơn hàng #$order_id thành công!"];
    } else {
        $notification = ['type' => 'error', 'msg' => "Lỗi hệ thống khi cập nhật đơn hàng: " . mysqli_error($conn)];
    }
    mysqli_stmt_close($stmt);
}

// ========================================
// TRUY VẤN DANH SÁCH ĐƠN HÀNG (Có hỗ trợ lọc)
// ========================================
$searchQuery = $_GET['search'] ?? '';
$sortOrder = $_GET['sort'] ?? 'desc';
$allowedSort = ['asc', 'desc'];
if (!in_array($sortOrder, $allowedSort)) $sortOrder = 'desc';

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$orders_sql = "SELECT * FROM orders WHERE 1=1 ";

// Nếu là Sales, chỉ xem đơn của mình
if ($user_role === 'sales') {
    $orders_sql .= " AND shop_id = $current_user_id ";
}

if (!empty($searchQuery)) {
    $searchParam = mysqli_real_escape_string($conn, $searchQuery);
    $orders_sql .= " AND (id LIKE '%$searchParam%' OR fullname LIKE '%$searchParam%' OR phone LIKE '%$searchParam%') ";
}
$orders_sql .= " ORDER BY created_at " . ($sortOrder === 'asc' ? 'ASC' : 'DESC');

$orders_result = mysqli_query($conn, $orders_sql);

include $base_dir . 'includes/header.php';
?>

<div class="web__background--overlay"></div>

<div class="grid wide">
    <!-- Thông báo Toast (Sử dụng CSS/JS có sẵn trong project) -->
    <?php if ($notification): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                app.showNotification('<?= $notification['msg'] ?>', '<?= $notification['type'] ?>');
            });
        </script>
    <?php endif; ?>

    <div class="manage-orders">
        <div class="manage-orders__header">
            <h1 class="manage-orders__title">Quản Lý Đơn Hàng</h1>
            <p class="manage-orders__subtitle">Theo dõi và cập nhật trạng thái xử lý đơn hàng SmartFit</p>
        </div>

        <div class="manage-orders__card">
            <!-- Toolbar Tìm kiếm & Lọc -->
            <div class="order-toolbar" style="margin-top: 0; margin-bottom: 25px; border-radius: 12px;">
                <form action="" method="GET" class="order-search">
                    <i class="fa-solid fa-magnifying-glass order-search__icon"></i>
                    <input type="text" name="search" class="order-search__input" 
                           placeholder="Mã đơn, tên khách, SĐT..." 
                           value="<?= htmlspecialchars($searchQuery) ?>">
                </form>

                <div class="order-filter">
                    <span class="order-filter__label">Sắp xếp:</span>
                    <select class="order-filter__select" onchange="window.location.href='?search=<?= urlencode($searchQuery) ?>&sort=' + this.value">
                        <option value="desc" <?= $sortOrder === 'desc' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="asc" <?= $sortOrder === 'asc' ? 'selected' : '' ?>>Cũ nhất</option>
                    </select>
                </div>
            </div>

            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái & Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($orders_result) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                <tr>
                                    <td>
                                        <div class="order-id-wrapper">
                                            <strong>#<?= $order['id'] ?></strong>
                                            <button type="button" class="btn-view-detail" 
                                                onclick="openOrderDetail(<?= htmlspecialchars(json_encode($order)) ?>, this)">
                                                <i class="fa-solid fa-eye"></i> Xem
                                            </button>
                                        </div>

                                        <!-- Hidden Items for Modal -->
                                        <div class="order-items-hidden" style="display: none;">
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
                                                        <img src="<?= htmlspecialchars($item['image'] ?? '/SmartFit/assets/img/default-placeholder.jpg') ?>" class="order-item__img">
                                                    </div>
                                                    <div class="order-item__info">
                                                        <h4 class="order-item__name"><?= htmlspecialchars($item['name']) ?></h4>
                                                        <div class="order-item__meta">
                                                            <span>Size: <?= htmlspecialchars($item['size_name']) ?></span>
                                                            <span class="order-item__separator">|</span>
                                                            <span>SL: <?= $item['quantity'] ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="order-item__price">
                                                        <?= number_format($item['price'], 0, ',', '.') ?>đ
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <span class="customer-name"><?= htmlspecialchars($order['fullname']) ?></span>
                                            <span class="customer-phone"><?= htmlspecialchars($order['phone']) ?></span>
                                        </div>
                                    </td>
                                    <td class="total-amount">
                                        <?= number_format($order['total_amount'], 0, ',', '.') ?>đ
                                    </td>
                                    <td>
                                        <div class="payment-info">
                                            <span class="payment-method badge-method"><?= strtoupper($order['payment_method']) ?></span>
                                            <?php 
                                                $status_class = '';
                                                $status_text = '';
                                                switch ($order['payment_status']) {
                                                    case 'success': $status_class = 'badge--success'; $status_text = 'Đã thanh toán'; break;
                                                    case 'pending': $status_class = 'badge--warning'; $status_text = 'Chờ xử lý'; break;
                                                    case 'failed': $status_class = 'badge--error'; $status_text = 'Thất bại'; break;
                                                    default: $status_class = 'badge--dark'; $status_text = $order['payment_status'];
                                                }
                                            ?>
                                            <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                        </div>
                                    </td>
                                    <td class="order-date">
                                        <?= date('H:i - d/m/Y', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="status-update-form">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="new_status" class="status-select">
                                                <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Chờ xử lý</option>
                                                <option value="processing" <?= ($order['status'] == 'processing') ? 'selected' : '' ?>>Đang chuẩn bị hàng</option>
                                                <option value="shipped" <?= ($order['status'] == 'shipped') ? 'selected' : '' ?>>Đang giao</option>
                                                <option value="completed" <?= ($order['status'] == 'completed') ? 'selected' : '' ?>>Đã hoàn thành</option>
                                                <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-update-status" title="Cập nhật">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 50px;">
                                    <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 10px; display: block;"></i>
                                    Chưa có đơn hàng nào trong hệ thống.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal (Tái sử dụng cấu trúc từ order_history.php) -->
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
                        <span class="label">Ghi chú từ khách:</span>
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
                        <span class="label">Trạng thái thanh toán:</span>
                        <span id="modalPaymentStatus" class="value uppercase"></span>
                    </div>
                </div>
            </div>

            <div class="order-modal__section">
                <h3 class="order-modal__section-title"><i class="fa-solid fa-list-ul"></i> Danh sách sản phẩm</h3>
                <div id="modalOrderItems" class="order-modal__items">
                    <!-- Danh sách sản phẩm sẽ được copy vào đây bằng JS -->
                </div>
            </div>
        </div>

        <div class="order-modal__footer">
            <div class="order-modal__total">
                <span>Tổng doanh thu:</span>
                <span id="modalTotalValue"></span>
            </div>
            <button class="order-modal__btn-close" onclick="closeOrderDetail()">Đóng cửa sổ</button>
        </div>
    </div>
</div>

<style>
    /* CSS cho trang Quản lý đơn hàng (Đồng bộ với manage_products.php) */
    .manage-orders {
        padding: 80px 0 60px;
        animation: fadeIn 0.8s ease;
    }

    .order-id-wrapper {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .btn-view-detail {
        background: #f0f0f5;
        color: #555;
        border: 1px solid #ddd;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        width: fit-content;
        transition: 0.2s;
    }

    .btn-view-detail:hover {
        background: var(--apple-black);
        color: #fff;
        border-color: var(--apple-black);
    }

    .manage-orders__header {
        text-align: center;
        margin-bottom: 40px;
    }

    .manage-orders__title {
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--apple-black);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .manage-orders__subtitle {
        font-size: 1.6rem;
        color: var(--apple-grey);
    }

    .manage-orders__card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    /* Bảng dữ liệu */
    .manage-table-wrapper {
        overflow-x: auto;
    }

    .manage-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1.4rem;
    }

    .manage-table th {
        text-align: left;
        padding: 18px 15px;
        background: #f8f9fa;
        color: #777;
        font-weight: 700;
        border-bottom: 2px solid #eee;
    }

    .manage-table td {
        padding: 20px 15px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .manage-table tr:hover {
        background-color: #fafafa;
    }

    /* Thông tin khách hàng */
    .customer-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .customer-name {
        font-weight: 700;
        color: var(--apple-black);
        font-size: 1.5rem;
    }

    .customer-phone {
        font-size: 1.3rem;
        color: var(--apple-grey);
    }

    /* Tiền và ngày */
    .total-amount {
        font-weight: 800;
        color: var(--primary-purple);
        font-size: 1.6rem;
    }

    .order-date {
        font-size: 1.3rem;
        color: #666;
    }

    /* Badge phong cách Monochrome Premium */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 1.2rem;
        font-weight: 700;
        display: inline-block;
    }

    .badge--success { background: #e6f9f1; color: #12b76a; }
    .badge--warning { background: #fff8eb; color: #f79009; }
    .badge--error { background: #fff1f3; color: #f04438; }
    .badge--dark { background: #f2f4f7; color: #475467; }

    .badge-method {
        font-size: 1.1rem;
        color: #888;
        background: #f5f5f7;
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .payment-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }

    /* Form trạng thái */
    .status-update-form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .status-select {
        padding: 10px 15px;
        border-radius: 10px;
        border: 1.5px solid #eee;
        background: #f9f9f9;
        font-size: 1.4rem;
        outline: none;
        transition: 0.3s;
        cursor: pointer;
        min-width: 180px;
    }

    .status-select:focus {
        border-color: var(--primary-purple);
        background: #fff;
    }

    .btn-update-status {
        background: var(--apple-black);
        color: #fff;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .btn-update-status:hover {
        background: var(--primary-purple);
        transform: translateY(-2px);
    }

    /* Modal Styles (Đồng bộ với order_history.php) */
    .order-modal {
        display: none;
        position: fixed;
        z-index: 10001;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
    }

    .order-modal.active { display: flex; }

    .order-modal__content {
        background-color: #fff;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalSlideUp 0.4s ease;
    }

    @keyframes modalSlideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .order-modal__header {
        padding: 20px 30px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-modal__title { font-size: 2rem; font-weight: 800; }
    .order-modal__title span { color: var(--primary-purple); }
    .order-modal__close { background: none; border: none; font-size: 3rem; color: #999; cursor: pointer; }

    .order-modal__body { padding: 30px; overflow-y: auto; flex: 1; }
    .order-modal__section { margin-bottom: 30px; }
    .order-modal__section-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f5f5f7;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .order-modal__section-title i { color: var(--primary-purple); }

    .order-modal__info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .order-modal__info-item { display: flex; flex-direction: column; gap: 5px; }
    .order-modal__info-item--full { grid-column: span 2; }
    .order-modal__info-item .label { font-size: 1.3rem; color: #888; font-weight: 500; }
    .order-modal__info-item .value { font-size: 1.5rem; color: #222; font-weight: 700; }
    .order-modal__info-item .italic { font-style: italic; color: #666; font-weight: 400; }

    /* Items in Modal */
    .order-modal__items { background: #f8f8fa; border-radius: 15px; padding: 15px; }
    .order-item {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }
    .order-item:last-child { border-bottom: none; }
    .order-item__img-wrapper { width: 60px; height: 60px; border-radius: 10px; overflow: hidden; border: 1px solid #eee; }
    .order-item__img { width: 100%; height: 100%; object-fit: cover; }
    .order-item__info { flex: 1; }
    .order-item__name { font-size: 1.5rem; font-weight: 700; color: #333; margin-bottom: 5px; }
    .order-item__meta { font-size: 1.3rem; color: #777; display: flex; gap: 10px; }
    .order-item__price { font-size: 1.5rem; font-weight: 700; color: var(--primary-purple); }

    .order-modal__footer {
        padding: 25px 30px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .order-modal__total { font-size: 1.8rem; font-weight: 800; color: #222; }
    #modalTotalValue { color: var(--primary-purple); margin-left: 10px; }
    .order-modal__btn-close {
        background: var(--apple-black);
        color: #fff;
        padding: 12px 25px;
        border-radius: 12px;
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }
    .order-modal__btn-close:hover { opacity: 0.9; transform: translateY(-2px); }

    /* Tablet/Mobile Responsive */
    @media (max-width: 768px) {
        .manage-orders__title { font-size: 2.8rem; }
        .manage-table { font-size: 1.2rem; }
        .status-select { min-width: 140px; font-size: 1.2rem; }
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
        
        // Định dạng ngày đặt
        const date = new Date(order.created_at);
        const dateString = date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) + ' - ' + date.toLocaleDateString('vi-VN');
        document.getElementById('modalOrderDate').innerText = dateString;

        document.getElementById('modalAddress').innerText = order.address || 'Chưa cập nhật';
        document.getElementById('modalNote').innerText = order.note ? '"' + order.note + '"' : 'Không có ghi chú từ khách';
        document.getElementById('modalPaymentMethod').innerText = order.payment_method;
        document.getElementById('modalPaymentStatus').innerText = order.payment_status;
        
        const totalFormatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount);
        document.getElementById('modalTotalValue').innerText = totalFormatted;

        // Lấy danh sách sản phẩm từ div hidden gần nút bấm nhất
        const tr = btn.closest('tr');
        const hiddenItems = tr.querySelector('.order-items-hidden').cloneNode(true);
        hiddenItems.style.display = 'block'; // Hiển thị list
        
        const modalItemsContainer = document.getElementById('modalOrderItems');
        modalItemsContainer.innerHTML = '';
        modalItemsContainer.appendChild(hiddenItems);

        // Hiện modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeOrderDetail() {
        const modal = document.getElementById('orderDetailModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Đóng khi click ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('orderDetailModal');
        if (event.target == modal) closeOrderDetail();
    }
</script>

<?php include $base_dir . 'includes/footer.php'; ?>
