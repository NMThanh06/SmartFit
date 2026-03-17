<?php
/**
 * pages/404.php
 * Trang thông báo lỗi 404 - Không tìm thấy trang
 * Thiết kế theo phong cách Monochrome Premium của SmartFit
 */

// Xác định đường dẫn gốc để nhúng header/footer chính xác
$base_dir = '../';
require_once $base_dir . 'includes/config.php';

// Nhúng header chung của hệ thống
include $base_dir . 'includes/header.php';
?>

<!-- Lớp phủ nền mờ đặc trưng của SmartFit -->
<div class="web__background--overlay"></div>

<div class="grid wide">
    <div class="error-404">
        <div class="error-404__container">
            <!-- Phần hiển thị số 404 lớn -->
            <div class="error-404__badge">
                <h1 class="error-404__number">404</h1>
                <div class="error-404__circle"></div>
            </div>

            <!-- Nội dung thông báo -->
            <div class="error-404__content">
                <h2 class="error-404__title">Trang Không Tìm Thấy</h2>
                <p class="error-404__desc">
                    Rất tiếc, trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.
                    Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
                </p>
            </div>

            <!-- Nút hành động -->
            <div class="error-404__actions">
                <a href="<?= $root ?>index.php" class="btn btn--primary error-404__btn">
                    <i class="fa-solid fa-house"></i>
                    Quay về Trang Chủ
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* 
       CSS cho trang 404 
       Sử dụng biến root và phong cách Monochrome Premium
    */
    .error-404 {
        padding: 100px 0 150px;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
        animation: fadeIn 0.8s ease;
    }

    .error-404__container {
        text-align: center;
        max-width: 600px;
        position: relative;
    }

    /* Hiệu ứng số 404 */
    .error-404__badge {
        position: relative;
        margin-bottom: 40px;
        display: inline-block;
    }

    .error-404__number {
        font-size: 15rem;
        font-weight: 900;
        color: var(--apple-black);
        line-height: 1;
        letter-spacing: -5px;
        position: relative;
        z-index: 2;
        margin: 0;
        /* Tạo hiệu ứng gradient nhẹ cho số */
        background: linear-gradient(180deg, var(--apple-black) 0%, #555 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .error-404__circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 250px;
        height: 250px;
        background: var(--primary-purple);
        opacity: 0.05;
        border-radius: 50%;
        z-index: 1;
        filter: blur(40px);
    }

    /* Nội dung văn bản */
    .error-404__title {
        font-size: 3.2rem;
        font-weight: 800;
        color: var(--apple-black);
        margin-bottom: 15px;
    }

    .error-404__desc {
        font-size: 1.6rem;
        color: var(--apple-grey);
        line-height: 1.6;
        margin-bottom: 40px;
    }

    /* Nút bấm */
    .error-404__btn {
        padding: 15px 35px;
        font-size: 1.6rem;
        font-weight: 600;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        text-decoration: none;
        color: var(--primary-blue);
    }

    .error-404__btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    /* Responsive cho màn hình nhỏ */
    @media (max-width: 768px) {
        .error-404__number {
            font-size: 10rem;
        }

        .error-404__title {
            font-size: 2.4rem;
        }

        .error-404__circle {
            width: 180px;
            height: 180px;
        }
    }

    /* Animation keyframes (nếu chưa có trong base.css) */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<?php
// Nhúng footer chung của hệ thống
include $base_dir . 'includes/footer.php';
?>