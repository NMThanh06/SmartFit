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

                    <?php else: ?>
                        <div id="loginBtn">
                            <i class="fa-solid fa-circle-user"></i>
                            Đăng nhập
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </nav>

        <!-- Detail product -->
        <section class="detail-page">
            <div class="grid wide">
                <a href="shop.php" class="detail__back-btn" style="display: inline-block; color: white; margin-bottom: 20px; text-decoration: none; font-size: 1.6rem;"><i class="fa-solid fa-arrow-left"></i> Quay lại cửa hàng</a>

                <div class="row" id="productDetailWrapper">
                    <h2 style="width: 100%; text-align: center; color: white;">Đang tải thông tin sản phẩm...</h2>
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
    // --- BIẾN TOÀN CỤC CHO SẢN PHẨM ---
    let currentProduct = null;
    let selectedSize = null;
    let maxStock = 0;
    let currentQty = 1;

    // 1. Hàm định dạng tiền tệ
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    // 2. Lấy ID sản phẩm từ URL (vd: detail.php?id=1)
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    // 3. HÀM TẢI DỮ LIỆU SẢN PHẨM
    async function loadProductDetail() {
        if (!productId) {
            document.getElementById('productDetailWrapper').innerHTML = '<h2 style="width:100%; text-align:center; color:white;">Không tìm thấy mã sản phẩm!</h2>';
            return;
        }

        try {
            // SỬA LỖI 1: Trỏ đường dẫn về đúng file API Database thật giống trang Shop
            const response = await fetch('includes/api_outfits.php');
            const data = await response.json();
            
            // Tìm sản phẩm khớp ID
            currentProduct = data.items.find(item => item.id == productId);

            if (!currentProduct) {
                document.getElementById('productDetailWrapper').innerHTML = '<h2 style="width:100%; text-align:center; color:white;">Sản phẩm không tồn tại!</h2>';
                return;
            }

            // SỬA LỖI 2: Cứu hộ dữ liệu (Vì API hiện tại của bạn Backend chưa có cột Size, Phong cách...)
            // Nếu không có đoạn này, code sẽ bị lỗi trắng trang khi cố tìm Size
            if (!currentProduct.sizes) {
                currentProduct.sizes = { "S": 10, "M": 15, "L": 5, "XL": 0 }; // Tạo kho hàng giả định
            }
            if (!currentProduct.type) currentProduct.type = "top";
            if (!currentProduct.style) currentProduct.style = ["Streetwear", "Basic"];
            if (!currentProduct.occasion) currentProduct.occasion = ["Đi dạo", "Cà phê"];

            renderProduct();
        } catch (error) {
            console.error("Lỗi:", error);
            document.getElementById('productDetailWrapper').innerHTML = '<h2 style="width:100%; text-align:center; color:red;">Lỗi kết nối dữ liệu!</h2>';
        }
    }

    // 4. HÀM VẼ GIAO DIỆN (Sử dụng đúng CSS của bạn)
    function renderProduct() {
        const container = document.getElementById('productDetailWrapper');
        
        // Tạo các nút Size
        let sizeHtml = '';
        for (const [size, stock] of Object.entries(currentProduct.sizes)) {
            if (stock > 0) {
                sizeHtml += `<button class="size-btn" onclick="selectSize('${size}', ${stock}, this)">${size}</button>`;
            } else {
                sizeHtml += `<button class="size-btn disabled" disabled title="Hết hàng">${size}</button>`;
            }
        }
        if (sizeHtml === '') sizeHtml = '<span style="color:red">Đã hết hàng</span>';

        // Xử lý chữ tiếng Việt
        const styleText = currentProduct.style ? currentProduct.style.join(', ') : 'Cơ bản';
        const occasionText = currentProduct.occasion ? currentProduct.occasion.join(', ') : 'Đa dụng';
        const typeText = currentProduct.type === 'top' ? 'Áo' : currentProduct.type === 'bottom' ? 'Quần/Váy' : currentProduct.type === 'shoes' ? 'Giày' : 'Phụ kiện';

        // Đổ toàn bộ HTML tĩnh của bạn vào đây
        container.innerHTML = `
            <div class="col l-6 m-6 c-12">
                <div class="detail__image">
                    <img src="${currentProduct.image}" alt="${currentProduct.name}" onerror="this.onerror=null; this.src='./assets/img/default-placeholder.jpg'">
                </div>
            </div>

            <div class="col l-6 m-6 c-12">
                <div class="detail__info">
                    <div class="detail__name">${currentProduct.name}</div>
                    <div class="detail__price">${formatPrice(currentProduct.price)}</div>

                    <div class="detail__desc">
                        <p><strong>Loại:</strong> ${typeText}</p>
                        <p><strong>Phong cách:</strong> ${styleText}</p>
                        <p><strong>Phù hợp:</strong> ${occasionText}</p>
                    </div>

                    <div class="detail__size">
                        <span class="detail__label">Chọn Kích Cỡ:</span>
                        <div class="detail__size-options">
                            ${sizeHtml}
                        </div>
                    </div>

                    <div class="detail__qty">
                        <span class="detail__label">Số Lượng:</span>
                        <div class="qty-control">
                            <button class="qty-btn" id="btnMinus" onclick="changeQty(-1)" disabled>-</button>
                            <input type="text" class="qty-input" id="qtyInput" value="1" readonly>
                            <button class="qty-btn" id="btnPlus" onclick="changeQty(1)" disabled>+</button>
                        </div>
                        <span class="stock-info" id="stockInfo">Vui lòng chọn size</span>
                    </div>

                    <button class="detail__buy-btn button" id="btnBuy" onclick="addToCartFromDetail()" disabled>
                        <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                    </button>
                </div>
            </div>
        `;
    }

    // 5. CÁC HÀM XỬ LÝ CHỌN SIZE & SỐ LƯỢNG (Của bạn Backend)
    function selectSize(size, stock, btnElement) {
        selectedSize = size;
        maxStock = stock;
        currentQty = 1;

        document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
        btnElement.classList.add('active');

        document.getElementById('qtyInput').value = currentQty;
        document.getElementById('stockInfo').innerText = `(Còn ${stock} sản phẩm)`;
        document.getElementById('btnBuy').disabled = false;
        updateQtyButtons();
    }

    function changeQty(amount) {
        currentQty += amount;
        if (currentQty < 1) currentQty = 1;
        if (currentQty > maxStock) currentQty = maxStock;
        document.getElementById('qtyInput').value = currentQty;
        updateQtyButtons();
    }

    function updateQtyButtons() {
        document.getElementById('btnMinus').disabled = (currentQty <= 1);
        document.getElementById('btnPlus').disabled = (currentQty >= maxStock);
    }

    // 6. HÀM THÊM VÀO GIỎ TỪ TRANG CHI TIẾT
    function addToCartFromDetail() {
        if (!selectedSize) {
            alert("Vui lòng chọn kích cỡ trước khi mua!");
            return;
        }

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

        showToast('Đã thêm ' + currentProduct.name + ' vào giỏ!', 'success');
        
        // Cập nhật giỏ hàng và tự động mở thanh trượt ra cho đẹp
        renderCart();
        if (typeof app !== 'undefined' && app.openCart) {
            app.openCart();
        }
    }

    // 7. CÁC HÀM CỦA GIỎ HÀNG TRƯỢT (Kế thừa từ shop.php)
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
            // Hiển thị thêm Size trong giỏ hàng nếu có
            const sizeLabel = item.size ? ` | Size: ${item.size}` : '';

            html += `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}" onerror="this.src='./assets/img/default-placeholder.jpg'" class="cart-item__img">
                <div class="cart-item__info">
                    <h4 class="cart-item__name">${item.name}</h4>
                    <div class="cart-item__price">${formatPrice(item.price)} <span style="font-size:1.2rem;color:#ccc;font-weight:normal">${sizeLabel}</span></div>
                    
                    <div class="cart-item__qty">
                        <button class="qty-btn" onclick="updateQty(${index}, ${item.quantity - 1})">-</button>
                        <input type="text" value="${item.quantity}" readonly>
                        <button class="qty-btn" onclick="updateQty(${index}, ${item.quantity + 1})">+</button>
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

    function updateQty(index, newQty) {
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

    // 8. CHẠY KHI VỪA MỞ TRANG
    window.onload = function() {
        loadProductDetail(); // Load thông tin 1 sản phẩm
        renderCart();        // Load giỏ hàng

        <?php if ($success): ?> showToast('<?php echo addslashes($success); ?>', 'success'); <?php endif; ?>
        <?php if ($error): ?> showToast('<?php echo addslashes($error); ?>', 'error'); <?php endif; ?>
    };
</script>

</body>

</html>