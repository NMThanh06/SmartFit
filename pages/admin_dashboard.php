<?php
/**
 * pages/admin_dashboard.php
 * Bảng điều khiển Tổng quan (Stats Dashboard) cho Admin & Sales
 */

require_once '../middleware.php'; // Kích hoạt RBAC
$base_dir = '../';
require_once $base_dir . 'includes/config.php';
require_once $base_dir . 'includes/functions.php';

// 1. Bảo mật: Chỉ cho phép admin và sales
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'sales'])) {
    header("Location: ../index.php");
    exit();
}

// 2. Xử lý Thống kê (Queries)
// --- Thống kê Đơn hàng ---
$order_total_query = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending FROM orders";
$order_res = mysqli_query($conn, $order_total_query);
$order_data = mysqli_fetch_assoc($order_res);
$total_orders = $order_data['total'];
$pending_orders = $order_data['pending'];

// --- Thống kê Doanh thu (Chỉ tính đơn thành công) ---
$revenue_query = "SELECT SUM(total_amount) as total_rev FROM orders WHERE status IN ('completed', 'success')";
$revenue_res = mysqli_query($conn, $revenue_query);
$revenue_data = mysqli_fetch_assoc($revenue_res);
$total_revenue = $revenue_data['total_rev'] ?? 0;

// --- Thống kê Người dùng ---
$user_query = "SELECT COUNT(*) as total_users FROM users";
$user_res = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_res);
$total_users = $user_data['total_users'];

// --- Thống kê Sản phẩm ---
$product_query = "SELECT COUNT(*) as total_products FROM outfits WHERE is_commercial = 1";
$product_res = mysqli_query($conn, $product_query);
$product_data = mysqli_fetch_assoc($product_res);
$total_products = $product_data['total_products'];

include $base_dir . 'includes/header.php';
?>

<div class="web__background--overlay"></div>

<div class="grid wide">
    <div class="admin-dashboard">
        <!-- Lời chào -->
        <div class="admin-dashboard__welcome">
            <h1 class="admin-dashboard__title">Bảng Điều Khiển Tổng Quan</h1>
            <p class="admin-dashboard__subtitle">Chào mừng trở lại khu vực Quản trị, <b><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['user_name']) ?></b>!</p>
        </div>

        <!-- Các thẻ thống kê (Stats Cards) -->
        <div class="row">
            <!-- Thẻ Doanh Thu -->
            <div class="col l-3 m-6 c-12">
                <div class="stat-card stat-card--revenue">
                    <div class="stat-card__icon">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div class="stat-card__info">
                        <span class="stat-card__label">Tổng Doanh Thu</span>
                        <h2 class="stat-card__value"><?= number_format($total_revenue, 0, ',', '.') ?>đ</h2>
                    </div>
                    <div class="stat-card__status stat-card__status--success">
                        <i class="fa-solid fa-circle-check"></i> Đơn đã hoàn thành
                    </div>
                </div>
            </div>

            <!-- Thẻ Đơn Hàng -->
            <div class="col l-3 m-6 c-12">
                <div class="stat-card stat-card--orders">
                    <div class="stat-card__icon">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <div class="stat-card__info">
                        <span class="stat-card__label">Tổng Đơn Hàng</span>
                        <h2 class="stat-card__value"><?= number_format($total_orders) ?></h2>
                    </div>
                    <?php if ($pending_orders > 0): ?>
                        <div class="stat-card__status stat-card__status--pending">
                            <i class="fa-solid fa-clock"></i> <b><?= $pending_orders ?></b> đơn chờ xử lý
                        </div>
                    <?php else: ?>
                        <div class="stat-card__status stat-card__status--neutral">
                            <i class="fa-solid fa-check-double"></i> Đã xử lý hết
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thẻ Người Dùng -->
            <div class="col l-3 m-6 c-12">
                <div class="stat-card stat-card--users">
                    <div class="stat-card__icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="stat-card__info">
                        <span class="stat-card__label">Khách Hàng</span>
                        <h2 class="stat-card__value"><?= number_format($total_users) ?></h2>
                    </div>
                    <div class="stat-card__status stat-card__status--info">
                        <i class="fa-solid fa-user-plus"></i> Thành viên hệ thống
                    </div>
                </div>
            </div>

            <!-- Thẻ Sản Phẩm -->
            <div class="col l-3 m-6 c-12">
                <div class="stat-card stat-card--products">
                    <div class="stat-card__icon">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <div class="stat-card__info">
                        <span class="stat-card__label">Mẫu Thiết Kế</span>
                        <h2 class="stat-card__value"><?= number_format($total_products) ?></h2>
                    </div>
                    <div class="stat-card__status stat-card__status--neutral">
                        <i class="fa-solid fa-box"></i> Kho hàng SmartFit
                    </div>
                </div>
            </div>
        </div>

        <!-- Placeholder cho các biểu đồ hoặc bảng biểu trong tương lai -->
        <div class="admin-dashboard__placeholder">
            <div class="admin-dashboard__placeholder-inner">
                <i class="fa-solid fa-chart-line"></i>
                <p>Khu vực biểu đồ thống kê tăng trưởng đang được phát triển...</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Dashboard - Monochrome Premium Style */
    .admin-dashboard {
        padding: 60px 0 100px;
        animation: fadeIn 0.8s ease;
    }

    .admin-dashboard__welcome {
        margin-bottom: 60px;
        border-left: 6px solid var(--primary-purple);
        padding: 12px 0 12px 30px;
    }

    .admin-dashboard__title {
        font-size: 3.2rem;
        font-weight: 800;
        color: var(--apple-black);
        margin-bottom: 12px;
        line-height: 1.2;
    }

    .admin-dashboard__subtitle {
        font-size: 1.6rem;
        color: var(--apple-grey);
    }

    /* Stats Cards */
    .stat-card {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 180px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.08);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 150px;
        height: 150px;
        background: rgba(0, 0, 0, 0.02);
        border-radius: 50%;
        z-index: 0;
    }

    .stat-card__icon {
        font-size: 2.4rem;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    /* Card Themes */
    .stat-card--revenue .stat-card__icon { background: #ecfdf5; color: #10b981; }
    .stat-card--orders .stat-card__icon { background: #fffbeb; color: #f59e0b; }
    .stat-card--users .stat-card__icon { background: #eff6ff; color: #3b82f6; }
    .stat-card--products .stat-card__icon { background: #f3f4f6; color: #1f2937; }

    .stat-card__info {
        position: relative;
        z-index: 1;
    }

    .stat-card__label {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--apple-grey);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card__value {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--apple-black);
        margin: 5px 0 15px;
    }

    .stat-card__status {
        font-size: 1.15rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        padding-top: 15px;
        border-top: 1px solid #f5f5f7;
    }

    .stat-card__status--success { color: #10b981; }
    .stat-card__status--pending { color: #f59e0b; }
    .stat-card__status--info { color: #3b82f6; }
    .stat-card__status--neutral { color: #9ca3af; }

    /* Placeholder area */
    .admin-dashboard__placeholder {
        margin-top: 20px;
        background: #fafafa;
        border: 2px dashed #eee;
        border-radius: 20px;
        padding: 50px;
        text-align: center;
    }

    .admin-dashboard__placeholder-inner {
        color: #ddd;
    }

    .admin-dashboard__placeholder-inner i {
        font-size: 4rem;
        margin-bottom: 15px;
    }

    .admin-dashboard__placeholder-inner p {
        font-size: 1.5rem;
        font-weight: 600;
        color: #aaa;
    }

    @media (max-width: 768px) {
        .admin-dashboard__title { font-size: 2.4rem; }
    }
</style>

<?php include $base_dir . 'includes/footer.php'; ?>
