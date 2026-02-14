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

    <!-- Font Outfit -->
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
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">

    <!-- Javascript -->
    <script src="script.js" defer></script>

</head>

<body>

    <video src="./assets/video/cloudy.mp4" autoplay muted loop class="web__background"></video>

    <div class="web__background--overlay"></div>

    <main class="web__container">
        <!-- Navigation -->
        <nav class="navbar">
            <a href="" class="navbar__logo">SmartFit</a>
            
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="https://github.com/NMThanh06/SmartFit" class="navbar__github">
                    <i class="fa-brands fa-square-github"></i>
                </a>

                <?php if (isset($_SESSION['user_name'])): ?>
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <button class="navbar__auth" onclick="if(confirm('Đăng xuất?')) window.location.href='includes/logout.php'">Đăng xuất</button>
                <?php else: ?>
                    <div id="loginBtn" class="navbar__auth">
                        <i class="fa-solid fa-circle-user"></i>
                        Đăng nhập
                    </div>
                    <!-- <button class="logout-btn" onclick="window.location.href='assets/screen/login.html'">Đăng nhập</button> -->
                <?php endif; ?>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero" id="hero">
            <div class="hero__info">
                <div class="info__greeting"></div> <!--Câu chào-->
                <div class="info__weather">
                    <div class="info__weather__icon"></div> <!-- Icon thời tiết-->

                    <div class="info__weather__text"></div> <!-- Thời tiết hiện tại-->
                    <div class="info__weather__temp"></div> <!-- Nhiệt độ -->
                </div>
                <div class="info__desc">HCM đang khá lạnh đấy, nhớ mặc ấm nhé.</div>
            </div>

            <form class="config-form" action="">

                <div class="config-form__group">
                    <h3 class="config-form__heading">Bạn mặc cho dịp gì ?</h3>

                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="study" name="occasion" value="study">
                        <label class="config-form__label" for="study">Đi học</label>

                        <input class="config-form__input" type="radio" id="goout" name="occasion" value="goout">
                        <label class="config-form__label" for="goout">Đi chơi</label>

                        <input class="config-form__input" type="radio" id="date" name="occasion" value="date">
                        <label class="config-form__label" for="date">Hẹn hò</label>
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Bạn là ?</h3>

                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="male" name="gender" value="male">
                        <label class="config-form__label" for="male">Nam</label>

                        <input class="config-form__input" type="radio" id="female" name="gender" value="female">
                        <label class="config-form__label" for="female">Nữ</label>
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Phong cách bạn hướng tới ?</h3>

                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="basic" name="style" value="basic">
                        <label class="config-form__label" for="basic">Basic</label>

                        <input class="config-form__input" type="radio" id="street" name="style" value="street">
                        <label class="config-form__label" for="street">Streetwear</label>

                        <input class="config-form__input" type="radio" id="vintage" name="style" value="vintage">
                        <label class="config-form__label" for="vintage">Vintage</label>
                    </div>
                </div>


                <div class="config-form__group">
                    <h3 class="config-form__heading">Tông màu chủ đạo ?</h3>
                    <div class="config-form__options">

                        <input class="config-form__input" type="radio" id="color-dark" name="color" value="dark">
                        <label class="config-form__label config-form__label--color" for="color-dark"
                            style="background-color: #000000;"></label>

                        <input class="config-form__input" type="radio" id="color-light" name="color" value="light">
                        <label class="config-form__label config-form__label--color" for="color-light"
                            style="background-color: #f0f0f0;"></label>

                        <input class="config-form__input" type="radio" id="color-pop" name="color" value="colorful">
                        <label class="config-form__label config-form__label--color" for="color-pop"
                            style="background: linear-gradient(#C21807, #DAA520);"></label>

                        <input class="config-form__input" type="radio" id="color-pastel" name="color" value="pastel">
                        <label class="config-form__label config-form__label--color" for="color-pastel"
                            style="background: linear-gradient(#2C3E50, #3E5E5E);"></label>

                        <input class="config-form__input" type="radio" id="color-neutral" name="color" value="pastel">
                        <label class="config-form__label config-form__label--color" for="color-neutral"
                            style="background: linear-gradient(#fff, #000);"></label>

                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Độ rộng (Fit) ?</h3>
                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="fit-oversize" name="fit" value="oversized">
                        <label class="config-form__label" for="fit-oversize">Oversized</label>

                        <input class="config-form__input" type="radio" id="fit-regular" name="fit" value="regular">
                        <label class="config-form__label" for="fit-regular">Vừa vặn</label>

                        <input class="config-form__input" type="radio" id="fit-slim" name="fit" value="slim">
                        <label class="config-form__label" for="fit-slim">Ôm sát</label>
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Ghi chú cho AI (Tùy chọn)</h3>
                    <textarea class="config-form__textarea" name="note"
                        placeholder="VD: Tôi có đôi Jordan đỏ, tôi không thích mặc váy..."></textarea>
                </div>

            </form>

            <button type="submit" class="confirm__button button">
                Phối đồ ngay ⭐
            </button>

        </section>

        <!-- Result Section -->
        <section class="result" id="result">

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
                        <h4 class="auth-card__heading">Tài khoản :</h4>
                        <input type="text" placeholder="Nhập email của bạn." class="auth-card__input" name="email" required>
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
                        <h4 class="auth-card__heading">Tên đăng nhập :</h4>
                        <input type="text" placeholder="Nhập tên đăng nhập của bạn." class="auth-card__input" name="name" required>
                    </div>
                    <div class="auth-card__group">
                        <h4 class="auth-card__heading">Gmail: </h4>
                        <input type="email" placeholder="Nhập gmail của bạn." class="auth-card__input" name="email" required>
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
</body>

</html>