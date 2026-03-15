        <!-- Footer -->
        <footer class="footer">
            <div class="grid wide">
                <div class="row footer__content">
                    <!-- Brand Column -->
                    <div class="col l-4 m-12 c-12">
                        <div class="footer__brand">
                            <div class="footer__logo">
                                <img src="<?php echo $root; ?>assets/img/logo_smartfit.jpg" alt="SmartFit Logo" class="footer__logo-img">
                                <span>SmartFit</span>
                            </div>
                            <p class="footer__slogan">Nâng tầm phong cách cá nhân cùng trí tuệ nhân tạo.</p>
                            <div class="footer__socials">
                                <a href="https://github.com/NMThanh06/SmartFit" class="footer__social-item" target="_blank" title="GitHub Repository">
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
                                    <a href="<?php echo $root; ?>index.php" class="footer__link">Phối đồ AI<span>Gợi ý trang phục thông minh theo thời tiết.</span></a>
                                </li>
                                <li class="footer__item">
                                    <a href="<?php echo $root; ?>pages/wardrobe.php" class="footer__link">Tủ đồ<span>Quản lý và lưu trữ bộ sưu tập cá nhân.</span></a>
                                </li>
                                <li class="footer__item">
                                    <a href="<?php echo $root; ?>shop.php" class="footer__link">Cửa hàng<span>Khám phá những món đồ thời trang mới nhất.</span></a>
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
                                    <a href="<?php echo $root; ?>pages/privacy.php" class="footer__link">Chính sách bảo mật<span>Bảo vệ thông tin người dùng.</span></a>
                                </li>
                                <li class="footer__item">
                                    <a href="<?php echo $root; ?>pages/terms.php" class="footer__link">Điều khoản dịch vụ<span>Quy định sử dụng trang web.</span></a>
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
                <form action="<?php echo $root; ?>includes/signup-form.php" method="post" class="auth-card__form">
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
            <button class="btn-checkout" onclick="window.location.href='<?php echo $root; ?>pages/checkout.php'">Thanh toán ngay</button>
        </div>

    </div>

    <!-- ========== SHARED CART JS (Single Source of Truth: localStorage) ========== -->
    <script>
    // --- BIẾN TOÀN CỤC: Giỏ hàng từ localStorage ---
    let cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];

    // --- Hàm format tiền VNĐ (dùng chung) ---
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    // --- Lưu giỏ hàng xuống localStorage + render lại UI ---
    function saveCart() {
        localStorage.setItem('smartfit_cart', JSON.stringify(cart));
        renderCart();
        updateCartIconQty();
    }

    // --- Cập nhật badge số lượng trên icon giỏ hàng ---
    function updateCartIconQty() {
        const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
        // Hỗ trợ cả cart-badge class và id
        document.querySelectorAll('.cart-badge, #cartBadge').forEach(badge => {
            badge.innerText = totalQty;
            badge.style.display = totalQty > 0 ? 'block' : 'none';
        });
    }

    // --- Vẽ lại drawer giỏ hàng từ mảng cart toàn cục ---
    function renderCart() {
        const cartEmpty = document.getElementById('cartEmpty');
        const cartItems = document.getElementById('cartItems');
        const cartFooter = document.getElementById('cartFooter');

        if (!cartEmpty || !cartItems || !cartFooter) return;

        if (cart.length === 0) {
            cartEmpty.style.display = 'flex';
            cartItems.style.display = 'none';
            cartFooter.style.display = 'none';
            const totalEl = document.querySelector('.total-price');
            if (totalEl) totalEl.innerText = formatPrice(0);
            updateCartIconQty();
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
                <img src="${item.image}" alt="${item.name}" onerror="this.src='<?php echo $root; ?>assets/img/default-placeholder.jpg'" class="cart-item__img">
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
        updateCartIconQty();
    }

    // --- Tăng/giảm số lượng item trong giỏ ---
    function updateCartQty(index, newQty) {
        if (newQty < 1) {
            removeItem(index);
            return;
        }
        cart[index].quantity = newQty;
        saveCart();
    }

    // --- Xóa 1 item khỏi giỏ ---
    function removeItem(index) {
        cart.splice(index, 1);
        saveCart();
    }

    // --- Xóa toàn bộ giỏ hàng ---
    function clearEntireCart() {
        if (!confirm("Bạn có chắc chắn muốn xóa toàn bộ sản phẩm trong giỏ hàng?")) return;
        cart = [];
        saveCart();
    }

    // --- Khởi tạo khi trang vừa load ---
    document.addEventListener('DOMContentLoaded', () => {
        renderCart();
        updateCartIconQty();
    });
    </script>
