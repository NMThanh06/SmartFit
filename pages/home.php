<?php
session_start();
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<?php include '../includes/toast.php'; ?>

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
    <link rel="stylesheet" href="../assets/css/grid.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/style.css?=v1">
    <link rel="stylesheet" href="../assets/css/responsive.css">

    <!-- Javascript -->
    <script src="../script.js?v=1<?php echo time(); ?>" defer></script>

</head>

<body>
    <main class="web__container">
        <!-- Navigation -->
        <nav class="navbar">
            <a href="" class="navbar__logo">
                <img src="../assets/img/logo_smartfit.jpg" alt="Logo">
            </a>

            <div class="navbar__auth">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <div id="userInfoToggle" class="user-info">
                        <div class="user-info__trigger">
                            <span class="user-info__name"> Xin chào, <b><?php echo htmlspecialchars($_SESSION['user_name']); ?></b></span>
                            <i class="fa-solid fa-caret-down user-info__arrow"></i>
                        </div>

                        <div id="userDropdown" class="user-dropdown">
                            <a href="../includes/" class="user-dropdown__item">
                                <i class="fa-solid fa-id-card"></i>
                                <span>Thông tin cá nhân</span>
                            </a>

                            <a href="wardrobe.php" class="user-dropdown__item">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Bộ sưu tập</span>
                            </a>

                            <a href="../shop.php" class="user-dropdown__item">
                                <i class="fa-solid fa-store"></i>
                                <span>Cửa hàng</span>
                            </a>

                            <a href="add-outfit.php" class="user-dropdown__item">
                                <i class="fa-solid fa-plus"></i>
                                <span>Thêm trang phục</span>
                            </a>

                            <div class="user-dropdown__divider"></div>

                            <a href="../includes/logout.php" class="user-dropdown__item user-dropdown__item--logout">
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

        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <h1 class="hero__title">Mặc gì hôm nay? Để AI lo!</h1>
            <p class="hero__subtitle">Giải pháp quản lý tủ đồ thông minh và gợi ý trang phục cá nhân hóa dựa trên thời tiết và phong cách của riêng bạn.</p>
            <a href="../index.php" class="button hero__btn">Trải nghiệm phối đồ ngay</a>
        </section>

        <!-- Divider -->
        <div class="section-divider"></div>

        <!-- Features Section -->
        <section class="features">
            <h2 class="section-title">Tính năng nổi bật</h2>
            <div class="row">
                <div class="col l-4 m-6 c-12">
                    <div class="feature-card">
                        <i class="fa-solid fa-robot feature-card__icon"></i>
                        <h3 class="feature-card__title">Trợ lý phối đồ AI</h3>
                        <p class="feature-card__desc">Tự động đề xuất các bộ cánh thời thượng dựa trên nhiệt độ, thời tiết thực tế tại vị trí của bạn và mục đích sử dụng (đi học, đi chơi, hẹn hò).</p>
                    </div>
                </div>
                <div class="col l-4 m-6 c-12">
                    <div class="feature-card">
                        <i class="fa-solid fa-shirt feature-card__icon"></i>
                        <h3 class="feature-card__title">Tủ đồ thông minh</h3>
                        <p class="feature-card__desc">Lưu trữ và quản lý những set đồ bạn yêu thích. Không còn mất thời gian lục tìm hay quên mất mình có những món đồ nào.</p>
                    </div>
                </div>
                <div class="col l-4 m-12 c-12">
                    <div class="feature-card">
                        <i class="fa-solid fa-store feature-card__icon"></i>
                        <h3 class="feature-card__title">Cửa hàng thời trang</h3>
                        <p class="feature-card__desc">Khám phá và sở hữu ngay những item mới nhất để bổ sung vào bộ sưu tập cá nhân với trải nghiệm mua sắm mượt mà.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works Section -->
        <section class="how-it-works">
            <h2 class="section-title">Cách thức hoạt động</h2>
            <div class="how-it-works__container">
                <!-- Wavy Line Background -->
                <svg class="how-it-works__line" viewBox="-300 0 1600 200" preserveAspectRatio="none">
                    <path d="M -300,100 C -50,250 250,-50 500,100 C 750,250 1050,-50 1300,100" 
                        fill="transparent" 
                        stroke="#6C63FF" 
                        stroke-width="4" 
                        stroke-dasharray="10, 10"></path>
                </svg>

                <i class="fa fa-paper-plane how-it-works__icon"></i>

                <div class="row how-it-works__steps">
                    <div class="col l-4 m-4 c-12">
                        <div class="step-card">
                            <div class="step-card__number">1</div>
                            <h3 class="step-card__title">Cung cấp thông tin</h3>
                            <p class="step-card__desc">Chọn dịp bạn mặc, phong cách và tông màu bạn thích.</p>
                        </div>
                    </div>
                    <div class="col l-4 m-4 c-12">
                        <div class="step-card step-card--middle">
                            <div class="step-card__number">2</div>
                            <h3 class="step-card__title">AI Phân tích</h3>
                            <p class="step-card__desc">Hệ thống kết hợp sở thích của bạn với dữ liệu thời tiết thực tế.</p>
                        </div>
                    </div>
                    <div class="col l-4 m-4 c-12">
                        <div class="step-card">
                            <div class="step-card__number">3</div>
                            <h3 class="step-card__title">Nhận kết quả</h3>
                            <p class="step-card__desc">Nhận ngay gợi ý phối đồ hoàn hảo kèm hình ảnh trực quan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="why-choose-us">
            <h2 class="section-title">Tại sao chọn SmartFit?</h2>
            <div class="row">
                <div class="col l-4 m-4 c-12">
                    <div class="reason-card">
                        <i class="fa-solid fa-clock reason-card__icon"></i>
                        <h3 class="reason-card__title">Tiết kiệm thời gian</h3>
                        <p class="reason-card__desc">Chỉ mất 5 giây để có một bộ đồ đẹp thay vì đứng 30 phút trước gương.</p>
                    </div>
                </div>
                <div class="col l-4 m-4 c-12">
                    <div class="reason-card">
                        <i class="fa-solid fa-cloud-sun reason-card__icon"></i>
                        <h3 class="reason-card__title">Luôn phù hợp thời tiết</h3>
                        <p class="reason-card__desc">Không còn tình trạng "thời trang phang thời tiết" nhờ dữ liệu Real-time.</p>
                    </div>
                </div>
                <div class="col l-4 m-4 c-12">
                    <div class="reason-card">
                        <i class="fa-solid fa-box-open reason-card__icon"></i>
                        <h3 class="reason-card__title">Tối ưu tủ đồ</h3>
                        <p class="reason-card__desc">Tận dụng tối đa những gì bạn đang có và chỉ mua những thứ thực sự cần thiết.</p>
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
            <?php
elseif ($error): ?>
                showToast('<?php echo addslashes($error); ?>', 'error');
            <?php
endif; ?>
        };
    </script>

    <script src="../script.js"></script>
</body>

</html>