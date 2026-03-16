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
    
    // Sử dụng Prepared Statement để bảo mật
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $notification = ['type' => 'success', 'msg' => "Đã cập nhật trạng thái đơn hàng #$order_id thành công!"];
    } else {
        $notification = ['type' => 'error', 'msg' => "Lỗi hệ thống khi cập nhật đơn hàng: " . mysqli_error($conn)];
    }
    mysqli_stmt_close($stmt);
}

// ========================================
// TRUY VẤN DANH SÁCH ĐƠN HÀNG
// ========================================
$orders_sql = "SELECT * FROM orders ORDER BY created_at DESC";
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
                                    <td><strong>#<?= $order['id'] ?></strong></td>
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

<style>
    /* CSS cho trang Quản lý đơn hàng (Đồng bộ với manage_products.php) */
    .manage-orders {
        padding: 100px 0 60px;
        animation: fadeIn 0.8s ease;
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

    /* Tablet/Mobile Responsive */
    @media (max-width: 768px) {
        .manage-orders__title { font-size: 2.8rem; }
        .manage-table { font-size: 1.2rem; }
        .status-select { min-width: 140px; font-size: 1.2rem; }
    }
</style>

<?php include $base_dir . 'includes/footer.php'; ?>
