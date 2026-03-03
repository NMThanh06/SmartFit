<?php
session_start();
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php include 'includes/toast.php'; ?>

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
    <link rel="stylesheet" href="./assets/css/grid.css">
    <link rel="stylesheet" href="./assets/css/base.css">
    <link rel="stylesheet" href="./assets/css/style.css?=v1">
    <link rel="stylesheet" href="./assets/css/responsive.css">

    <!-- Javascript -->
    <script src="script.js?v=1<?php echo time(); ?>" defer></script>


    <style>

    </style>
</head>

<body>
    <div class="web__background--overlay"></div>

    <main class="web__container ">
        <!-- Navigation -->
        <nav class="navbar">
            <a href="" class="navbar__logo">SmartFit</a>

            <div class="navbar__shop">
                <div class="navbar__cart" onclick="app.openCart()">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>

                <div class="navbar__auth">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <div id="userInfoToggle" class="user-info">
                            <div class="user-info__trigger">
                                <span class="user-info__name"> Xin chào, <b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b></span>
                                <i class="fa-solid fa-caret-down user-info__arrow"></i>
                            </div>

                            <div id="userDropdown" class="user-dropdown">
                                <a href="./includes/" class="user-dropdown__item">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>Thông tin cá nhân</span>
                                </a>

                                <a href="wardrobe.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>Bộ sưu tập</span>
                                </a>

                                <a href="shop.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-store"></i>
                                    <span>Cửa hàng</span>
                                </a>

                                <a href="includes/admin-add.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>Thêm trang phục</span>
                                </a>

                                <div class="user-dropdown__divider"></div>

                                <a href="./includes/logout.php" class="user-dropdown__item user-dropdown__item--logout">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Đăng xuất</span>
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        <div id="loginBtn">
                            <i class="fa-solid fa-circle-user"></i>
                            Đăng nhập
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </nav>

        <!-- Shop Section -->
        <section class="shop-page">
            <div class="grid wide">

                <div class="row">
                    <div class="col l-12 m-12 c-12">
                        <div class="shop__header">
                            <h1 class="shop__title">Cửa hàng SmartFit</h1>
                            <div class="shop__filters">
                                <button class="button filter-btn active">Tất cả</button>
                                <button class="button filter-btn">Áo</button>
                                <button class="button filter-btn">Quần</button>
                                <button class="button filter-btn">Giày & Phụ kiện</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row shop__products">
                    <div class="col l-3 m-4 c-6">
                        <a href="#" class="product-card">
                            <div class="product-card__img">
                                <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=300&auto=format&fit=crop" alt="Áo thun">
                            </div>
                            <div class="product-card__info">
                                <h3 class="product-card__name">Áo thun Oversize Basic Trắng</h3>
                                <div class="product-card__price">250.000 ₫</div>
                                <div class="product-card__buy">
                                    <i class="fa-solid fa-cart-shopping"></i> Xem ngay
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer__author">Made with ❤️ by Cuong & Thanh.</div>

            <div class="footer__contact">
                <a href="https://github.com/NMThanh06/SmartFit" class="footer__contact__github">
                    <i class="fa-brands fa-square-github"></i>
                </a>

                <div class="footer__contact__team">
                    <div class="footer__contact__mail" onclick="app.copyToClipboard(this)">
                        <i class="fa-solid fa-envelope"></i>
                        <span>trungcuong.2006tn@gmail.com</span>
                        <div class="copy-tooltip">Copied!</div>
                    </div>

                    <div class="footer__contact__mail" onclick="app.copyToClipboard(this)">
                        <i class="fa-solid fa-envelope"></i>
                        <span>nguyenminhthanh043216@gmail.com</span>
                        <div class="copy-tooltip">Copied!</div>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <!-- Auth Form -->
    <section id="authOverlay" class="auth-overlay">
        <div class="auth-card">
            <i id="closeAuth" class="fa-solid fa-xmark auth-card__close"></i>

            <div id="loginForm">
                <div class="auth-card__title">Đăng nhập</div>
                <form action="includes/login.php" method="post" class="auth-card__form">
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Email :</h4>
                        <input type="text" placeholder="Nhập Email của bạn." class="auth-card__input" name="email" required>
                    </div>
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Mật khẩu :</h4>
                        <input type="password" placeholder="Nhập mật khẩu của bạn." class="auth-card__input" name="psw" required>
                    </div>
                    <button type="submit" class="auth-card__button button">Đăng nhập</button>
                </form>
                <p class="auth-card__switch">Bạn chưa có tài khoản? <a href="#" id="toRegister">Đăng ký ngay</a></p>
            </div>

            <div id="registerForm" style="display: none;">
                <div class="auth-card__title">Đăng ký</div>
                <form action="includes/signup-form.php" method="post" class="auth-card__form">
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Tên :</h4>
                        <input type="text" placeholder="Nhập tên của bạn." class="auth-card__input" name="name" required>
                    </div>
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Email :</h4>
                        <input type="email" placeholder="Nhập email của bạn." class="auth-card__input" name="email" required>
                    </div>
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Mật khẩu :</h4>
                        <input type="password" placeholder="Nhập mật khẩu của bạn." class="auth-card__input" name="psw" required>
                    </div>
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Xác nhận mật khẩu :</h4>
                        <input type="password" placeholder="Nhập lại mật khẩu của bạn." class="auth-card__input" name="psw-repeat" required>
                    </div>
                    <button type="submit" class="auth-card__button button">Đăng ký</button>
                </form>
                <p class="auth-card__switch">Bạn đã có tài khoản? <a href="#" id="toLogin">Đăng nhập ngay</a></p>
            </div>
        </div>
    </section>
    <script>
        // Hàm hiển thị toast
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            toast.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        window.onload = function() {
            <?php if ($success): ?>
                showToast('<?php echo addslashes($success); ?>', 'success');
            <?php elseif ($error): ?>
                showToast('<?php echo addslashes($error); ?>', 'error');
            <?php endif; ?>
        };
    </script>

    <!-- Giỏ hàng -->
    <div class="cart-overlay" id="cartOverlay" onclick="app.closeCart()"></div>

    <div class="cart-drawer" id="cartDrawer">

        <div class="cart-drawer__header">
            <h2>Giỏ hàng của bạn</h2>
            <button class="cart-close-btn" onclick="app.closeCart()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="cart-drawer__body">

            <div class="cart-empty" id="cartEmpty" style="display: none;">
                <i class="fa-solid fa-box-open empty-icon"></i>
                <p>Giỏ hàng của bạn đang trống</p>
                <button class="btn-shopping" onclick="app.closeCart()">Tiếp tục mua sắm</button>
            </div>

            <div class="cart-items" id="cartItems">
                <div class="cart-item">
                    <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=200&auto=format&fit=crop" alt="Áo" class="cart-item__img">
                    <div class="cart-item__info">
                        <h4 class="cart-item__name">Áo thun Oversize Basic Trắng</h4>
                        <div class="cart-item__meta">Size: L</div>
                        <div class="cart-item__price">250.000 ₫</div>

                        <div class="cart-item__qty">
                            <button class="qty-btn">-</button>
                            <input type="text" value="1" readonly>
                            <button class="qty-btn">+</button>
                        </div>
                    </div>
                    <button class="cart-item__remove" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                </div>

            </div>

        </div>

        <div class="cart-drawer__footer" id="cartFooter">
            <div class="cart-total">
                <span>Tổng cộng:</span>
                <span class="total-price">250.000 ₫</span>
            </div>
            <button class="btn-checkout">Thanh toán ngay</button>
        </div>

    </div>

</body>

</html>