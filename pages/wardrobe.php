<?php
session_start();
require_once '../includes/config.php';

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Xử lý lấy danh sách trang phục đã lưu của User hiện tại
$saved_outfits = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // SQL lấy thông tin từ bảng
    $sql = "SELECT so.id as saved_id, so.style_name,
                   t.name as top_name, t.image as top_img,
                   b.name as bottom_name, b.image as bottom_img,
                   s.name as shoes_name, s.image as shoes_img,
                   a.name as acc_name, a.image as acc_img
            FROM saved_outfits so
            JOIN outfits t ON so.top_id = t.id
            JOIN outfits b ON so.bottom_id = b.id
            JOIN outfits s ON so.shoes_id = s.id
            LEFT JOIN outfits a ON so.acc_id = a.id
            WHERE so.user_id = ?
            ORDER BY so.created_at DESC";
            
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $saved_outfits[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<?php include '../includes/toast.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bộ sưu tập - SmartFit</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/grid.css">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/style.css?=v1">
    <link rel="stylesheet" href="../assets/css/responsive.css">

    <script src="../script.js?v=1<?php echo time(); ?>" defer></script>
</head>

<body>
    <div class="web__background--overlay"></div>

    <main class="web__container">
        <nav class="navbar">
            <a href="../index.php" class="navbar__logo">SmartFit</a>

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

                <?php else: ?>
                    <div id="loginBtn">
                        <i class="fa-solid fa-circle-user"></i>
                        Đăng nhập
                    </div>
                <?php endif; ?>
            </div>

        </nav>

        <section class="wardrobe-page">
            <div class="grid wide">
                <div class="row">
                    <div class="col l-12 m-12 c-12">
                        <div class="wardrobe__header">
                            <h1 class="wardrobe__title">Bộ sưu tập</h1>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if (empty($saved_outfits)): ?>
                        <div class="col l-12" style="text-align: center; padding: 40px 0;">
                            <p>Bạn chưa lưu bộ trang phục nào.</p>
                            <a href="../index.php" class="button" style="margin-top: 10px; display: inline-block;">Phối đồ ngay</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($saved_outfits as $outfit): ?>
                            <div class="col l-3 m-4 c-12">
                                <div class="wardrobe-card">
                                    <div class="wardrobe-card__images">
                                        <img src="../assets/img/<?php echo basename($outfit['top_img']); ?>" alt="Áo">
                                        
                                        <img src="../assets/img/<?php echo basename($outfit['bottom_img']); ?>" alt="Quần">
                                        
                                        <!-- phần hiển thị 2 món còn lại niếu cần *(chưa css/js) -->
                                        <!-- <img src="../assets/img/<?php echo basename($outfit['shoes_img']); ?>" alt="Giày">
                                        
                                        <?php if ($outfit['acc_img']): ?>
                                            <img src="../assets/img/<?php echo basename($outfit['acc_img']); ?>" alt="Phụ kiện">
                                        <?php endif; ?> -->

                                    </div>

                                    <div class="wardrobe-card__info">
                                        <div class="wardrobe-card__header">
                                            <h3 class="wardrobe-card__title"><?php echo htmlspecialchars($outfit['style_name'] ?: 'Style của tôi'); ?></h3>
                                            <button class="wardrobe-card__delete" 
                                                    title="Xóa" 
                                                    onclick="app.deleteSavedOutfit(<?php echo $outfit['saved_id']; ?>, this)">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            
                                        </div>

                                        <ul class="wardrobe-card__items">
                                            <li>
                                            <p><?php echo htmlspecialchars($outfit['top_name']); ?></p>
                                            </li>
                                            <li>
                                                <p><?php echo htmlspecialchars($outfit['bottom_name']); ?></p>
                                            </li>
                                            <li>
                                                <p><?php echo htmlspecialchars($outfit['shoes_name']); ?></p>
                                            </li>
                                            <li>
                                                <p><?php echo $outfit['acc_name'] ? htmlspecialchars($outfit['acc_name']) : 'Không có'; ?></p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

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

    <section id="authOverlay" class="auth-overlay">
        <div class="auth-card">
            <i id="closeAuth" class="fa-solid fa-xmark auth-card__close"></i>

            <div id="loginForm">
                <div class="auth-card__title">Đăng nhập</div>
                <form action="../includes/login.php" method="post" class="auth-card__form">
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
                <form action="../includes/signup-form.php" method="post" class="auth-card__form">
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