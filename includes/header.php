<?php
// ========================================
// includes/header.php — Component dùng chung
// Chứa: session, config, toast, <head>, navbar
// ========================================

// Khởi tạo session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tính toán root path động
$current_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$project_root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$public_path = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

// Tìm số cấp thư mục so với root
$relative_path = str_replace($public_path, '', $project_root);
$root = rtrim($relative_path, '/') . '/';

// Kết nối Database
require_once __DIR__ . '/config.php';
?>
<?php include __DIR__ . '/toast.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFit</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- My Library -->
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/grid.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/base.css">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $root; ?>assets/css/responsive.css">

    <!-- Javascript -->
    <script src="<?php echo $root; ?>script.js?v=<?php echo time(); ?>" defer></script>


    <style>

    </style>
</head>

<body>
    <?php if (isset($page_extra_body)) echo $page_extra_body; ?>
    
    <main class="web__container ">
        <!-- Navigation -->
        <nav class="navbar">
            <a href="<?php echo $root; ?>./pages/home.php" class="navbar__logo">
                <img src="<?php echo $root; ?>assets/img/logo_smartfit.jpg" alt="Logo">
            </a>

            <div class="navbar__shop">
                <div class="navbar__cart" onclick="app.openCart()">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                </div>

                <div class="navbar__auth">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <div id="userInfoToggle" class="user-info">
                            <div class="user-info__trigger">
                                <span class="user-info__name"> Xin chào, <b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b></span>
                                <i class="fa-solid fa-caret-down user-info__arrow"></i>
                            </div>

                            <div id="userDropdown" class="user-dropdown">
                                <a href="<?php echo $root; ?>pages/personal_info.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>Thông tin cá nhân</span>
                                </a>

                                <a href="<?php echo $root; ?>index.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-shirt"></i>
                                    <span>Phối đồ</span>
                                </a>

                                <a href="<?php echo $root; ?>pages/wardrobe.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>Bộ sưu tập</span>
                                </a>

                                <a href="<?php echo $root; ?>shop.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-store"></i>
                                    <span>Cửa hàng</span>
                                </a>

                                <a href="<?php echo $root; ?>pages/order_history.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-receipt"></i>
                                    <span>Lịch sử đơn hàng</span>
                                </a>

                                <a href="<?php echo $root; ?>pages/add-outfit.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Thêm trang phục</span>
                                </a>

                                <div class="user-dropdown__divider"></div>

                                <a href="<?php echo $root; ?>includes/logout.php" class="user-dropdown__item user-dropdown__item--logout">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Đăng xuất</span>
                                </a>
                            </div>
                        </div>

                    <?php
else: ?>
                        <div id="loginBtn">
                            <i class="fa-solid fa-circle-user"></i>
                            Đăng nhập
                        </div>
                    <?php
endif; ?>
                </div>

            </div>

        </nav>
