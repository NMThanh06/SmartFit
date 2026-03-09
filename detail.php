<?php
session_start();
require_once 'includes/config.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productId <= 0) {
    header("Location: shop.php");
    exit;
}

// Lấy thông tin sản phẩm và số lượng (quantity)
$sql = "SELECT * FROM outfits WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    die("Không tìm thấy sản phẩm!");
}

// Lấy Size và số lượng từ bảng outfit_sizes
$sqlSizes = "SELECT size_name, quantity FROM outfit_sizes WHERE outfit_id = ?";
$stmtSizes = mysqli_prepare($conn, $sqlSizes);
mysqli_stmt_bind_param($stmtSizes, "i", $productId);
mysqli_stmt_execute($stmtSizes);
$resSizes = mysqli_stmt_get_result($stmtSizes);
$sizeList = [];
while ($row = mysqli_fetch_assoc($resSizes)) {
    $sizeList[] = $row; // mỗi phần tử chứa 'size_name' và 'quantity'
}

// Hàm Việt hóa (Giữ nguyên logic dịch của ông)
function translateFitData($data)
{
    if (empty($data))
        return 'Cơ bản';
    $map = [
        'basic' => 'Cơ bản', 'street' => 'Đường phố', 'vintage' => 'Cổ điển',
        'study' => 'Đi học', 'goout' => 'Đi chơi', 'date' => 'Hẹn hò',
        'regular' => 'Vừa vặn', 'oversized' => 'Rộng', 'slim' => 'Ôm',
        'top' => 'Áo', 'bottom' => 'Quần/Váy', 'shoes' => 'Giày', 'accessory' => 'Phụ kiện'
    ];
    $clean = str_replace(['[', ']', '"'], '', $data);
    $items = explode(',', $clean);
    $results = [];
    foreach ($items as $val) {
        $k = trim(strtolower($val));
        $results[] = isset($map[$k]) ? $map[$k] : ucfirst($val);
    }
    return implode(', ', $results);
}
$displayType = (in_array($product['type'], ['accessory', 'glasses'])) ? 'Phụ kiện' : translateFitData($product['type']);
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
            <a href="index.php" class="navbar__logo">SmartFit</a>

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

                                <a href="pages/add-outfit.php" class="user-dropdown__item">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Thêm trang phục</span>
                                </a>

                                <div class="user-dropdown__divider"></div>

                                <a href="./includes/logout.php" class="user-dropdown__item user-dropdown__item--logout">
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

        <!-- Detail product -->
        <section class="detail-page">
            <div class="grid wide">
                <a href="shop.php" class="detail__back-btn" style="display: inline-block; color: white; margin-bottom: 20px; text-decoration: none; font-size: 1.6rem;"><i class="fa-solid fa-arrow-left"></i> Quay lại cửa hàng</a>

                <div class="row" id="productDetailWrapper">
    <div class="col l-6 m-6 c-12">
        <div class="detail__image">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" id="mainProductImg" style="width: 100%; border-radius: 8px;" onerror="this.src='./assets/img/default-placeholder.jpg'">
        </div>
    </div>

    <div class="col l-6 m-6 c-12">
        <div class="detail__info" style="color: white;">
            <h1 class="detail__name" style="font-size: 3.2rem; margin-bottom: 10px;"><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="detail__price" style="color: #ff4d4f; font-size: 2.6rem; font-weight: bold; margin-bottom: 25px;">
                <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
            </div>

            <div class="detail__desc" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 8px; margin-bottom: 25px; font-size: 1.5rem; line-height: 2;">
                <div><strong>Loại:</strong> <?php echo $displayType; ?></div>
                <div><strong>Phong cách:</strong> <?php echo translateFitData($product['style']); ?></div>
                <div><strong>Phù hợp:</strong> <?php echo translateFitData($product['occasion']); ?></div>
                <div><strong>Độ rộng:</strong> <?php echo translateFitData($product['fit'] ?? ''); ?></div>
                <div style="color: #ffcc00;"><strong>Kho còn:</strong> <span id="stockInfo">Vui lòng chọn size</span></div>
            </div>

            <div class="detail__size" style="margin-bottom: 25px;">
                <p style="margin-bottom: 12px; font-size: 1.6rem;">Chọn Kích Cỡ:</p>
                <div class="detail__size-options" style="display: flex; gap: 12px;">
                    <?php foreach ($sizeList as $size): ?>
                        <button class="size-btn-item" onclick="selectSize(this)" data-size="<?php echo htmlspecialchars($size['size_name']); ?>" data-quantity="<?php echo intval($size['quantity']); ?>"
                            style="min-width: 60px; height: 45px; border: 1px solid #888; background: transparent; color: white; cursor: pointer; border-radius: 4px; font-weight: bold;">
                            <?php echo htmlspecialchars($size['size_name']); ?>
                        </button>
                    <?php
endforeach; ?>
                </div>
            </div>

            <div class="detail__qty" style="margin-bottom: 35px; display: flex; align-items: center; gap: 20px;">
                <span style="font-size: 1.6rem;">Số Lượng:</span>
                <div style="display: flex; align-items: center; background: #444; border-radius: 4px;">
                    <button class="qty-btn" onclick="changeQty(-1)" style="width: 35px; height: 35px; border: none; background: none; color: white; cursor: pointer;">-</button>
                    <input type="text" id="qtyDisplay" value="1" readonly style="width: 45px; text-align: center; border: none; background: none; color: white; font-weight: bold;">
                    <button class="qty-btn" onclick="changeQty(1)" style="width: 35px; height: 35px; border: none; background: none; color: white; cursor: pointer;">+</button>
                </div>
            </div>

            <button onclick="addToCartFromDetail()" class="button" style="width: 100%; padding: 18px; background: #ff4d4f; color: white; border: none; font-size: 1.6rem; font-weight: bold; cursor: pointer; border-radius: 6px;">
                <i class="fa-solid fa-cart-shopping"></i> THÊM VÀO GIỎ HÀNG hehe
            </button>
        </div>
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

            </div>

        </div>

        <div class="cart-drawer__footer" id="cartFooter">
            <div class="cart-total">
                <span>Tổng cộng:</span>
                <span class="total-price"></span>
            </div>
            <button class="btn-checkout" onclick="window.location.href='pages/checkout.php'">Thanh toán ngay</button>
        </div>

    </div>

    <!-- Backend cho trang chi tiết sản phẩm -->
    <script>
    // --- BIẾN TOÀN CỤC (Lấy từ PHP Server-Side) ---
    let selectedSize = null;
    let maxStock = 0;
    let currentQty = 1;

    // Thông tin sản phẩm từ PHP (dùng cho addToCart)
    const currentProduct = {
        id: <?php echo intval($product['id']); ?>,
        name: <?php echo json_encode($product['name']); ?>,
        image: <?php echo json_encode($product['image']); ?>,
        price: <?php echo intval($product['price']); ?>
    };

    // 1. Hàm định dạng tiền tệ
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    // 2. HÀM CHỌN SIZE
    function selectSize(btnElement) {
        const size = btnElement.getAttribute('data-size');
        const sizeQuantity = parseInt(btnElement.getAttribute('data-quantity')) || 0;
        selectedSize = size;
        maxStock = sizeQuantity;

        // Nếu số lượng đang chọn lớn hơn maxStock mới, reset về 1
        if (currentQty > maxStock) {
            currentQty = 1;
        }

        // Highlight nút được chọn
        document.querySelectorAll('.size-btn-item').forEach(btn => {
            btn.style.background = 'transparent';
            btn.style.color = 'white';
        });
        btnElement.style.background = 'white';
        btnElement.style.color = 'black';

        document.getElementById('qtyDisplay').value = currentQty;
        document.getElementById('stockInfo').innerText = maxStock + ' sản phẩm';
    }

    // 3. HÀM THAY ĐỔI SỐ LƯỢNG (Giới hạn bởi maxStock)
    function changeQty(amount) {
        if (!selectedSize) {
            showToast('Vui lòng chọn kích cỡ trước!', 'error');
            return;
        }
        let nextQty = currentQty + amount;
        if (nextQty < 1) nextQty = 1;
        if (nextQty > maxStock) {
            showToast('Chỉ còn ' + maxStock + ' sản phẩm trong kho!', 'error');
            nextQty = maxStock;
        }
        currentQty = nextQty;
        document.getElementById('qtyDisplay').value = currentQty;
    }

    // 4. HÀM THÊM VÀO GIỎ TỪ TRANG CHI TIẾT
    function addToCartFromDetail() {
        if (!selectedSize) {
            showToast('Vui lòng chọn kích cỡ trước khi mua!', 'error');
            return;
        }

        // --- Hiệu ứng ảnh bay vào giỏ hàng (GIỮ NGUYÊN) ---
        const imgEl = document.getElementById('mainProductImg');
        const cartIcon = document.querySelector('.navbar__cart');
        if (imgEl && cartIcon) {
            const imgClone = imgEl.cloneNode(true);
            const imgRect = imgEl.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            imgClone.style.position = 'fixed';
            imgClone.style.left = imgRect.left + 'px';
            imgClone.style.top = imgRect.top + 'px';
            imgClone.style.width = imgRect.width + 'px';
            imgClone.style.height = imgRect.height + 'px';
            imgClone.style.zIndex = '9999';
            imgClone.style.transition = 'all 0.7s ease-in-out';
            imgClone.style.borderRadius = '8px';
            imgClone.style.pointerEvents = 'none';
            document.body.appendChild(imgClone);

            requestAnimationFrame(() => {
                imgClone.style.left = cartRect.left + 'px';
                imgClone.style.top = cartRect.top + 'px';
                imgClone.style.width = '30px';
                imgClone.style.height = '30px';
                imgClone.style.opacity = '0.3';
            });

            imgClone.addEventListener('transitionend', () => imgClone.remove());
        }
        // --- Kết thúc hiệu ứng ---

        const cartItem = {
            id: currentProduct.id,
            name: currentProduct.name,
            image: currentProduct.image,
            price: currentProduct.price,
            size: selectedSize,
            quantity: currentQty
        };

        let cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];
        const existingItemIndex = cart.findIndex(item => item.id === cartItem.id && item.size === cartItem.size);

        if (existingItemIndex !== -1) {
            cart[existingItemIndex].quantity += currentQty;
        } else {
            cart.push(cartItem);
        }

        localStorage.setItem('smartfit_cart', JSON.stringify(cart));

        // showToast('Đã thêm ' + currentProduct.name + ' vào giỏ!', 'success');
        
        // Cập nhật giỏ hàng và tự động mở thanh trượt
        renderCart();
        if (typeof app !== 'undefined' && app.openCart) {
            app.openCart();
        }
    }

    // 5. CÁC HÀM CỦA GIỎ HÀNG TRƯỢT (Kế thừa từ shop.php)
    function renderCart() {
        let cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];
        const cartEmpty = document.getElementById('cartEmpty');
        const cartItems = document.getElementById('cartItems');
        const cartFooter = document.getElementById('cartFooter');

        if (cart.length === 0) {
            cartEmpty.style.display = 'flex';
            cartItems.style.display = 'none';
            cartFooter.style.display = 'none';
            return;
        }

        cartEmpty.style.display = 'none';
        cartItems.style.display = 'block';
        cartFooter.style.display = 'block';

        let html = '';
        let totalAmount = 0;

        cart.forEach((item, index) => {
            totalAmount += item.price * item.quantity;
            const sizeLabel = item.size ? ` | Size: ${item.size}` : '';

            html += `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}" onerror="this.src='./assets/img/default-placeholder.jpg'" class="cart-item__img">
                <div class="cart-item__info">
                    <h4 class="cart-item__name">${item.name}</h4>
                    <div class="cart-item__price">${formatPrice(item.price)} <span style="font-size:1.2rem;color:#ccc;font-weight:normal">${sizeLabel}</span></div>
                    
                    <div class="cart-item__qty">
                        <button class="qty-btn" onclick="updateCartQty(${index}, ${item.quantity - 1})">-</button>
                        <input type="text" value="${item.quantity}" readonly>
                        <button class="qty-btn" onclick="updateCartQty(${index}, ${item.quantity + 1})">+</button>
                    </div>
                </div>
                <button class="cart-item__remove" title="Xóa" onclick="removeItem(${index})">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        });

        cartItems.innerHTML = html;
        document.querySelector('.total-price').innerText = formatPrice(totalAmount);
    }

    function updateCartQty(index, newQty) {
        let cart = JSON.parse(localStorage.getItem('smartfit_cart'));
        if (newQty < 1) newQty = 1;
        cart[index].quantity = newQty;
        localStorage.setItem('smartfit_cart', JSON.stringify(cart));
        renderCart();
    }

    function removeItem(index) {
        let cart = JSON.parse(localStorage.getItem('smartfit_cart'));
        cart.splice(index, 1);
        localStorage.setItem('smartfit_cart', JSON.stringify(cart));
        renderCart();
    }

    // 6. CHẠY KHI VỪA MỞ TRANG
    window.onload = function() {
        renderCart();
    };
</script>

</body>

</html>