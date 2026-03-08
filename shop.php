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
    <link rel="stylesheet" href="./assets/css/style.css?=v2">
    <link rel="stylesheet" href="./assets/css/responsive.css">

    <!-- Javascript -->
    <script src="script.js?v=2<?php echo time(); ?>" defer></script>


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
                                <a href="./includes/" class="user-dropdown__item">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>Thông tin cá nhân</span>
                                </a>

                                <a href="pages/wardrobe.php" class="user-dropdown__item">
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

                <div id="productGrid" class="row shop__products">

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

    <!-- Giỏ hàng -->
    <div class="cart-overlay" id="cartOverlay" onclick="app.closeCart()"></div>

    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer__header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0;">Giỏ hàng của bạn</h2>
            <div>
                <button class="cart-close-btn" onclick="app.closeCart()" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>

        <div class="cart-drawer__body">
            <div class="cart-empty" id="cartEmpty" style="display: none;">
                <i class="fa-solid fa-box-open empty-icon"></i>
                <p>Giỏ hàng của bạn đang trống</p>
                <button class="btn-shopping" onclick="app.closeCart()">Tiếp tục mua sắm</button>
            </div>
            <div id="cartItems"></div>
        </div>

        <div class="cart-drawer__footer" id="cartFooter" style="display: none;">
            <div class="cart-total">
                <span>Tổng cộng:</span>
                <span class="total-price">0đ</span>
            </div>
            <button class="btn-checkout" onclick="window.location.href='pages/checkout.php'">Thanh toán ngay</button>
        </div>
    </div>

    <!-- Backend cho trang cửa hàng -->
    <script>
        // 1. Định dạng tiền tệ
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
        }

        // 2. Cập nhật con số màu đỏ (Badge)
        function updateCartUI(count) {
            const badge = document.getElementById('cartBadge') || document.querySelector('.cart-badge');
            if (badge) {
                badge.innerText = count;
                badge.style.display = count > 0 ? 'block' : 'none';
            }
        }

        // 1. Hàm tự động chọn bộ Size dựa vào Loại sản phẩm (Type)
        function getSizeOptions(item) {
    const type = (item.type || '').toLowerCase();
    const name = (item.name || '').toLowerCase();

    // 1. Ưu tiên lấy Size từ Database nếu ông đã nhập (Cái này là chuẩn nhất)
    if (item.sizes) {
        let sList = typeof item.sizes === 'string' 
            ? item.sizes.replace(/[\[\]"]/g, '').split(',') 
            : item.sizes;
        return sList.map(s => `<option value="${s.trim()}">${s.trim()}</option>`).join('');
    }

    // 2. Phân loại thông minh hơn (Nếu DB chưa có size)
    if (type === 'shoes' || name.includes('giày')) {
        return `<option value="39">Size 39</option><option value="40" selected>Size 40</option><option value="41">Size 41</option>`;
    } else if (['accessory', 'glasses', 'hat'].includes(type) || name.includes('kính') || name.includes('mũ')) {
        // Sửa theo ý ông: Mắt kính là size Over
        return `<option value="Oversize" selected>Oversize</option><option value="Freesize">Freesize</option>`;
    } else {
        return `<option value="S">Size S</option><option value="M" selected>Size M</option><option value="L">Size L</option>`;
    }
}

        // 2. Tải danh sách sản phẩm trang Shop
        async function loadProducts() {
            try {
                const response = await fetch('includes/api_outfits.php');
                const data = await response.json();
                const grid = document.getElementById('productGrid');
                grid.innerHTML = '';

                data.items.forEach(item => {
                    // Gọi hàm sinh ra Option Size tương ứng với món đồ này
                    const dynamicSizes = getSizeOptions(item);

                    grid.innerHTML += `
                    <div class="col l-3 m-4 c-6">
                        <div class="product-card">
                            <a href="detail.php?id=${item.id}" class="product-card__img" style="display: block;">
                                <img src="${item.image}" alt="${item.name}" onerror="this.src='./assets/img/default-placeholder.jpg'">
                            </a>
                            <div class="product-card__info">
                                <h3 class="product-card__name">
                                    <a href="detail.php?id=${item.id}" style="text-decoration: none; color: inherit;">${item.name}</a>
                                </h3>
                                <div class="product-card__price">${formatPrice(item.price)}</div>
                                
                                <div class="product-card__size" style="margin-bottom: 10px;">
                                    <select id="size-select-${item.id}" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; outline: none; cursor: pointer;">
                                        ${dynamicSizes}
                                    </select>
                                </div>

                                <div class="product-card__buy" onclick="addToCart(event, ${item.id}, '${item.image}')">
                                    <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } catch (error) { console.error("Lỗi tải sản phẩm:", error); }
        }

        // 3. Thêm vào giỏ hàng
        async function addToCart(event, id, imageSrc) {
            const btn = event.currentTarget; // Lấy đúng nút vừa bấm

            // 1. Lấy size đã chọn từ Dropdown
            const sizeSelect = document.getElementById(`size-select-${id}`);
            const selectedSize = sizeSelect ? sizeSelect.value : 'M'; 
            
            console.log("Đang thêm món đồ:", id, "Ảnh:", imageSrc, "Size:", selectedSize);

            // 2. HIỆU ỨNG BAY (Dùng DOM element để xác định vị trí)
            const cartIcon = document.querySelector('.navbar__cart');
            const productCard = btn.closest('.product-card');
            const imgToFly = productCard ? productCard.querySelector('img') : null;
            
            if (imgToFly && cartIcon) {
                const flyImg = imgToFly.cloneNode();
                const imgRect = imgToFly.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();

                flyImg.classList.add('fly-item');
                flyImg.style.top = `${imgRect.top}px`;
                flyImg.style.left = `${imgRect.left}px`;
                flyImg.style.width = `${imgRect.width}px`;
                flyImg.style.height = `${imgRect.height}px`;

                document.body.appendChild(flyImg);

                setTimeout(() => {
                    flyImg.style.top = `${cartRect.top + 10}px`;
                    flyImg.style.left = `${cartRect.left + 10}px`;
                    flyImg.style.width = '20px'; 
                    flyImg.style.height = '20px'; 
                    flyImg.style.opacity = '0';
                }, 10);

                setTimeout(() => flyImg.remove(), 800);
            }

            // 3. Gửi dữ liệu lên Server
            try {
                const response = await fetch('includes/add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        outfit_id: id, 
                        size: selectedSize, 
                        quantity: 1 
                    })
                });
                const data = await response.json();

                if (data.status === 'success') {
                    // if (typeof showToast === 'function') showToast('Đã thêm vào giỏ!', 'success');
                    updateCartUI(data.cart_count); 
                    renderCart(); 
                } else {
                    if (typeof showToast === 'function') showToast(data.message, 'error');
                }
            } catch (err) { 
                console.error("Lỗi kết nối:", err); 
            }
        }
        // 5. Vẽ lại giỏ hàng (Đồng bộ với detail.php)
        async function renderCart() {
            try {
                const response = await fetch('includes/fetch_cart.php?t=' + new Date().getTime(), { cache: 'no-store' });
                const data = await response.json();
                
                const cartEmpty = document.getElementById('cartEmpty');
                const cartItems = document.getElementById('cartItems');
                const cartFooter = document.getElementById('cartFooter');
                // Ẩn/Hiện nút xóa tất cả tùy trạng thái giỏ
                const clearBtn = document.querySelector('.cart-clear-btn');

                if (!data.items || data.items.length === 0) {
                    cartEmpty.style.display = 'flex';
                    cartItems.style.display = 'none';
                    cartFooter.style.display = 'none';
                    if(clearBtn) clearBtn.style.display = 'none';
                    
                    updateCartUI(0);
                    document.querySelector('.total-price').innerText = formatPrice(0);
                    return;
                }

                cartEmpty.style.display = 'none';
                cartItems.style.display = 'block';
                cartFooter.style.display = 'block';
                if(clearBtn) clearBtn.style.display = 'inline-block';

                let html = '';
                let total = 0;

                data.items.forEach(item => {
                    total += item.price * item.quantity;
                    html += `
                    <div class="cart-item">
                        <img src="${item.image}" class="cart-item__img" onerror="this.src='./assets/img/default-placeholder.jpg'">
                        <div class="cart-item__info">
                            <h4 class="cart-item__name">${item.name}</h4>
                                <div class="cart-item__price">${formatPrice(item.price)} <span style="font-size:0.9rem;color:#999;">| Size: ${item.size || item.size_name || 'Freesize'}</span></div>                            <div class="cart-item__qty">
                                <button onclick="updateCartQty(${item.id}, ${item.quantity - 1})">-</button>
                                <input type="text" value="${item.quantity}" readonly>
                                <button onclick="updateCartQty(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                        </div>
                        <button class="cart-item__remove" onclick="removeCartItem(${item.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>`;
                });

                cartItems.innerHTML = html;
                document.querySelector('.total-price').innerText = formatPrice(total);
                updateCartUI(data.total_quantity);
            } catch (err) { console.log("Lỗi render giỏ hàng:", err); }
        }

        // 6. Tăng/Giảm số lượng
        async function updateCartQty(cartId, newQty) {
            if (newQty < 1) {
                if (confirm("Bạn có chắc muốn xóa món hàng này?")) {
                    removeCartItem(cartId);
                }
                return;
            }
            try {
                const response = await fetch('includes/update_cart_qty.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cart_id: cartId, quantity: newQty })
                });
                const data = await response.json();
                if (data.status === 'success') {
                    updateCartUI(data.cart_count);
                    renderCart();
                }
            } catch (err) { console.error(err); }
        }

        // 7. Xóa 1 món hàng
        async function removeCartItem(cartId) {
            try {
                const response = await fetch('includes/remove_from_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cart_id: cartId })
                });
                const data = await response.json();
                if (data.status === 'success') {
                    updateCartUI(data.cart_count);
                    renderCart();
                }
            } catch (err) { console.error(err); }
        }

        // 8. HÀM MỚI: XÓA TOÀN BỘ GIỎ HÀNG
        async function clearCart() {
            if (!confirm("Bạn có chắc chắn muốn xóa toàn bộ sản phẩm trong giỏ hàng?")) {
                return;
            }
            try {
                const response = await fetch('includes/clear_cart.php', { method: 'POST' });
                const data = await response.json();
                
                if (data.status === 'success') {
                    // showToast("Đã dọn sạch giỏ hàng!", "success");
                    updateCartUI(0);
                    renderCart();
                } else {
                    showToast(data.message, "error");
                }
            } catch (err) {
                console.error("Lỗi xóa toàn bộ giỏ:", err);
                showToast("Lỗi hệ thống, vui lòng thử lại!", "error");
            }
        }

        // Khởi động khi tải trang xong
        window.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            renderCart();
        });
    </script>

</body>

</html>