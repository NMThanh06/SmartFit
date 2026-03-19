<!-- Footer -->
<footer class="footer">
    <div class="grid wide">
        <div class="row footer__content">
            <!-- Brand Column -->
            <div class="col l-4 m-12 c-12">
                <div class="footer__brand">
                    <div class="footer__logo">
                        <img src="<?php echo $root; ?>assets/img/logo_smartfit.jpg" alt="SmartFit Logo"
                            class="footer__logo-img">
                        <span>SmartFit</span>
                    </div>
                    <p class="footer__slogan">Nâng tầm phong cách cá nhân cùng trí tuệ nhân tạo.</p>
                    <div class="footer__socials">
                        <a href="https://github.com/NMThanh06/SmartFit" class="footer__social-item" target="_blank"
                            title="GitHub Repository">
                            <i class="fa-brands fa-github"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product Column -->
            <div class="col l-4 m-6 c-12">
                <div class="footer__section">
                    <h3 class="footer__heading">Tính năng</h3>
                    <ul class="footer__list">
                        <li class="footer__item">
                            <a href="<?php echo $root; ?>style_outfits.php" class="footer__link">Phối đồ AI<span>Gợi ý trang
                                    phục thông minh theo thời tiết.</span></a>
                        </li>
                        <li class="footer__item">
                            <a href="<?php echo $root; ?>pages/wardrobe.php" class="footer__link">Tủ đồ<span>Quản lý và
                                    lưu trữ bộ sưu tập cá nhân.</span></a>
                        </li>
                        <li class="footer__item">
                            <a href="<?php echo $root; ?>shop.php" class="footer__link">Cửa hàng<span>Khám phá những món
                                    đồ thời trang mới nhất.</span></a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Legal Column -->
            <div class="col l-4 m-6 c-12">
                <div class="footer__section">
                    <h3 class="footer__heading">Chính sách</h3>
                    <ul class="footer__list">
                        <li class="footer__item">
                            <a href="<?php echo $root; ?>pages/privacy.php" class="footer__link">Chính sách bảo
                                mật<span>Bảo vệ thông tin người dùng.</span></a>
                        </li>
                        <li class="footer__item">
                            <a href="<?php echo $root; ?>pages/terms.php" class="footer__link">Điều khoản dịch
                                vụ<span>Quy định sử dụng trang web.</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright Row -->
        <div class="footer__bottom">
            <div class="footer__copyright">© 2026 SmartFit Inc. All rights reserved.</div>
            <div class="footer__author">Made with ❤️ by Cuong & Thanh.</div>
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
            <form action="<?php echo $root; ?>includes/login.php" method="post" class="auth-card__form">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Email hoặc Tên đăng nhập</h4>
                    <input type="text" placeholder="Nhập Email hoặc Tên đăng nhập" class="auth-card__input" name="email" required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Mật khẩu</h4>
                    <input type="password" placeholder="Nhập mật khẩu" class="auth-card__input" name="psw"
                        required>
                </div>
                <button type="submit" class="auth-card__button button">Đăng nhập</button>
            </form>
            <p class="auth-card__switch">Bạn chưa có tài khoản? <a href="#" id="toRegister">Đăng ký ngay</a></p>
        </div>

        <div id="registerForm" style="display: none;">
            <div class="auth-card__title">Đăng ký</div>
            <form action="<?php echo $root; ?>includes/signup-form.php" method="post" class="auth-card__form">
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Tên đăng nhập</h4>
                    <input type="text" placeholder="Nhập tên đăng nhập" class="auth-card__input" name="name" required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Email</h4>
                    <input type="email" placeholder="Nhập email" class="auth-card__input" name="email"
                        required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Mật khẩu</h4>
                    <input type="password" placeholder="Tạo mật khẩu" class="auth-card__input" name="psw"
                        required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Xác nhận mật khẩu</h4>
                    <input type="password" placeholder="Nhập lại mật khẩu" class="auth-card__input"
                        name="psw-repeat" required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Vai trò (Dành cho BGK)</h4>
                    <select name="role" class="auth-card__input" style="background-color: #f0f0f0; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
                        <option value="customer" selected>Khách hàng (customer)</option>
                        <option value="support">Nhân viên CSKH (support)</option>
                        <option value="sales">Nhân viên Bán hàng (sales)</option>
                        <option value="admin">Quản trị viên (admin)</option>
                    </select>
                </div>
                <button type="submit" class="auth-card__button button">Đăng ký</button>
            </form>
            <p class="auth-card__switch">Bạn đã có tài khoản? <a href="#" id="toLogin">Đăng nhập ngay</a></p>
        </div>
    </div>
</section>

<!-- Giỏ hàng -->
<?php if (isset($_SESSION['user_id'])): ?>
<div class="cart-overlay" id="cartOverlay" onclick="cartDrawerApp.closeCart()"></div>

<div class="cart-drawer" id="cartDrawer">

    <div class="cart-drawer__header">
        <h2>Giỏ hàng của bạn</h2>
        <button class="cart-close-btn" onclick="cartDrawerApp.closeCart()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="cart-drawer__body">

        <div class="cart-empty" id="cartEmpty" style="display: none;">
            <i class="fa-solid fa-box-open empty-icon"></i>
            <p>Giỏ hàng của bạn đang trống</p>
            <button class="btn-shopping" onclick="cartDrawerApp.closeCart()">Tiếp tục mua sắm</button>
        </div>

        <div class="cart-items" id="cartItems">
            <!-- Grouped by Shop -->
        </div>

    </div>

    <div class="cart-drawer__footer" id="cartFooter">
        <div class="cart-total">
            <span>Tổng thanh toán:</span>
            <span class="total-price" id="drawerTotalPrice">0đ</span>
        </div>
        <button id="btnDrawerCheckout" class="btn-checkout disabled" style="opacity: 0.5; pointer-events: none;" onclick="goToCheckout()">
            Thanh toán
        </button>
    </div>

</div>
<?php endif; ?>

<!-- ========== SHARED CART JS (Single Source of Truth: localStorage) ========== -->
<script>
    // --- BIẾN TOÀN CỤC ---
    let cart = []; // Mảng phẳng chứa tất cả item trong giỏ
    let cartGroups = {}; // Để lưu trữ dữ liệu giỏ hàng đã gom nhóm
    let selectedShopId = null;

    // --- Hàm format tiền VNĐ ---
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    // --- Lấy dữ liệu giỏ hàng từ DB ---
    async function syncCart() {
        try {
            const response = await fetch('<?php echo $root; ?>includes/fetch_cart.php');
            const data = await response.json();
            
            if (data.status === 'error') {
                console.error('Lỗi Backend Giỏ hàng:', data.message);
                return;
            }

            const items = data.items || [];
            
            // Cập nhật mảng phẳng cart (để dùng cho các trang như detail.php)
            cart = items.map(item => ({
                id: item.outfit_id,
                size: item.size_name,
                color: item.color_name,
                quantity: parseInt(item.quantity)
            }));

            // Gom nhóm theo Shop (owner_id)
            cartGroups = {};
            items.forEach(item => {
                const shopId = item.owner_id || 0;
                if (!cartGroups[shopId]) {
                    cartGroups[shopId] = {
                        shopName: item.vendor_name || 'SmartFit Shop',
                        items: [],
                        total: 0
                    };
                }
                cartGroups[shopId].items.push(item);
                cartGroups[shopId].total += item.price * item.quantity;
            });

            renderCart();
            updateCartIconQty(data.total_quantity || 0);
        } catch (error) {
            console.error('Lỗi lấy giỏ hàng:', error);
        }
    }

    // --- Cập nhật badge số lượng ---
    function updateCartIconQty(totalQty) {
        document.querySelectorAll('.cart-badge, #cartBadge').forEach(badge => {
            badge.innerText = totalQty;
            badge.style.display = totalQty > 0 ? 'block' : 'none';
        });
    }

    // --- Vẽ lại drawer giỏ hàng (Gom nhóm theo Shop) ---
    function renderCart() {
        const cartEmpty = document.getElementById('cartEmpty');
        const cartItems = document.getElementById('cartItems');
        const cartFooter = document.getElementById('cartFooter');
        const totalPriceEl = document.getElementById('drawerTotalPrice');

        if (!cartEmpty || !cartItems || !cartFooter) return;

        const shopIds = Object.keys(cartGroups);

        if (shopIds.length === 0) {
            cartEmpty.style.display = 'flex';
            cartItems.style.display = 'none';
            cartFooter.style.display = 'none';
            return;
        }

        cartEmpty.style.display = 'none';
        cartItems.style.display = 'block';
        cartFooter.style.display = 'block';

        let html = '';
        shopIds.forEach(shopId => {
            const group = cartGroups[shopId];
            html += `
                <div class="cart-shop-group" style="margin-bottom: 20px; border: 1px solid #e8e8ed; border-radius: 12px; overflow: hidden; background: #fff;">
                    <div class="cart-shop-header" style="background: var(--glass-bg); padding: 12px 16px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #e8e8ed;">
                        <input type="radio" name="selected_shop" value="${shopId}" 
                               style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary-blue);"
                               onchange="onSelectShop(this)" 
                               data-total="${group.total}"
                               ${selectedShopId == shopId ? 'checked' : ''}>
                        <span style="font-weight: 600; font-size: 1.4rem; color: var(--text-main);">
                            <i class="fa-solid fa-store" style="color: var(--primary-blue); margin-right: 6px;"></i>${group.shopName}
                        </span>
                    </div>
                    <div class="cart-shop-products" style="padding: 12px;">
                        ${group.items.map(item => `
                            <div class="cart-item" style="display: flex; gap: 12px; align-items: flex-start;">
                                <img src="${item.image}" alt="${item.name}" style="width: 65px; height: 80px; object-fit: cover; border-radius: 8px; flex-shrink: 0;" onerror="this.src='<?php echo $root; ?>assets/img/default-placeholder.jpg'">
                                <div class="cart-item__info" style="flex: 1; min-width: 0;">
                                    <h4 class="cart-item__name" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</h4>
                                    <div style="font-size: 1.3rem; color: #6e6e73; margin-bottom: 4px;">Size: <b>${item.size_name}</b> | Màu: <b>${item.color_name || 'Mặc định'}</b></div>
                                    <div class="cart-item__price">${formatPrice(item.price)} <span style="font-size:1.2rem; font-weight:400; color:#6e6e73;">x ${item.quantity}</span></div>
                                </div>
                                <button onclick="removeFromCartDB(${item.id})" style="background: none; border: none; color: #e53030; cursor: pointer; padding: 4px; flex-shrink: 0; font-size: 1.6rem;" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        `).join('')}
                        <div style="text-align: right; font-size: 1.3rem; color: #6e6e73; padding-top: 8px; border-top: 1px dashed #e8e8ed; margin-top: 8px;">
                            Tổng Shop: <b style="color: #1d1d1f; font-size: 1.4rem;">${formatPrice(group.total)}</b>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItems.innerHTML = html;
        
        // Cập nhật lại tổng thanh toán nếu đang chọn shop
        if (selectedShopId && cartGroups[selectedShopId]) {
            totalPriceEl.innerText = formatPrice(cartGroups[selectedShopId].total);
        } else {
            totalPriceEl.innerText = '0đ';
        }
    }

    // --- Khi chọn Shop để thanh toán ---
    function onSelectShop(radio) {
        selectedShopId = radio.value;
        const total = radio.getAttribute('data-total');
        document.getElementById('drawerTotalPrice').innerText = formatPrice(total);
        
        const btn = document.getElementById('btnDrawerCheckout');
        btn.classList.remove('disabled');
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    }

    // --- Chuyển hướng thanh toán ---
    function goToCheckout() {
        if (!selectedShopId) return;
        window.location.href = `<?php echo $root; ?>pages/checkout.php?shop_id=${selectedShopId}`;
    }

    // --- Xóa item khỏi DB ---
    async function removeFromCartDB(cartId) {
        if (!confirm('Bạn muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
        try {
            const response = await fetch('<?php echo $root; ?>includes/remove_from_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId })
            });
            const data = await response.json();
            if (data.status === 'success') {
                syncCart();
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Lỗi xóa item:', error);
        }
    }

    // Thay thế logic localStorage bằng syncCart khi nạp trang
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
            syncCart();
        }
    });

    const cartDrawerApp = {
        openCart: function() {
            document.getElementById('cartOverlay').classList.add('show');
            document.getElementById('cartDrawer').classList.add('open');
            
            // Ẩn Chatbot
            const chatToggle = document.getElementById('chatbotToggleBtn');
            const chatWindow = document.getElementById('smartfit-chatbot');
            if (chatToggle) chatToggle.style.display = 'none';
            if (chatWindow) chatWindow.classList.remove('active');

            syncCart();
        },
        closeCart: function() {
            document.getElementById('cartOverlay').classList.remove('show');
            document.getElementById('cartDrawer').classList.remove('open');

            // Hiện lại Chatbot
            const chatToggle = document.getElementById('chatbotToggleBtn');
            if (chatToggle) chatToggle.style.display = 'flex';
        }
    };
</script>

<?php include 'chatbot.php'; ?>