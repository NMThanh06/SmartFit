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
                            <a href="<?php echo $root; ?>index.php" class="footer__link">Phối đồ AI<span>Gợi ý trang
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
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Email :</h4>
                    <input type="text" placeholder="Nhập Email của bạn." class="auth-card__input" name="email" required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Mật khẩu :</h4>
                    <input type="password" placeholder="Nhập mật khẩu của bạn." class="auth-card__input" name="psw"
                        required>
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
                    <input type="email" placeholder="Nhập email của bạn." class="auth-card__input" name="email"
                        required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Mật khẩu :</h4>
                    <input type="password" placeholder="Nhập mật khẩu của bạn." class="auth-card__input" name="psw"
                        required>
                </div>
                <div class="auth-card__group">
                    <h4 class="auth-card__heading">Xác nhận mật khẩu :</h4>
                    <input type="password" placeholder="Nhập lại mật khẩu của bạn." class="auth-card__input"
                        name="psw-repeat" required>
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
        <button class="btn-checkout" onclick="window.location.href='<?php echo $root; ?>pages/checkout.php'">Thanh toán
            ngay</button>
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

            // 1. Render Màu sắc
            let colorOptionsHtml = '';
            const colors = item.allColors || [];
            if (colors.length > 0) {
                colorOptionsHtml = colors.map(c => {
                    // Kiểm tra tồn kho tổng của màu này trong allSizes của item
                    const colorStock = (item.allSizes || [])
                        .filter(s => s.color_id == c.id)
                        .reduce((sum, s) => sum + parseInt(s.quantity), 0);
                    
                    const isOut = colorStock <= 0;
                    return `
                        <option value="${c.color_name}" ${item.color === c.color_name ? 'selected' : ''} ${isOut ? 'disabled' : ''}>
                            ${c.color_name}${isOut ? ' (Hết hàng)' : ''}
                        </option>
                    `;
                }).join('');
            } else {
                colorOptionsHtml = `<option value="${item.color}">${item.color}</option>`;
            }

            // 2. Render Kích cỡ (Lọc theo màu hiện tại)
            let sizeOptionsHtml = '';
            const allSizes = item.allSizes || [];
            
            if (allSizes.length > 0) {
                // Lấy danh sách tên size duy nhất và sắp xếp
                const uniqueSizeNames = [...new Set(allSizes.map(s => s.size_name))];
                const sizeOrder = ['S', 'M', 'L', 'XL', '2XL', 'XXL', '3XL', 'Oversize'];
                uniqueSizeNames.sort((a, b) => {
                    const ia = sizeOrder.indexOf(a);
                    const ib = sizeOrder.indexOf(b);
                    if (ia !== -1 && ib !== -1) return ia - ib;
                    return a.localeCompare(b);
                });

                // Tìm colorId của màu hiện tại
                const currentColorObj = colors.find(c => c.color_name === item.color);
                const currentColorId = currentColorObj ? currentColorObj.id : null;

                sizeOptionsHtml = uniqueSizeNames.map(sizeName => {
                    const sizeData = allSizes.find(s => s.size_name === sizeName && s.color_id == currentColorId);
                    const isOut = !sizeData || parseInt(sizeData.quantity) <= 0;
                    return `<option value="${sizeName}" ${item.size === sizeName ? 'selected' : ''} ${isOut ? 'disabled' : ''}>
                        ${sizeName}${isOut ? ' (Hết hàng)' : ''}
                    </option>`;
                }).join('');
            } else {
                // FALLBACK: Nếu không có allSizes (dữ liệu cũ), hiển thị size hiện có
                sizeOptionsHtml = `<option value="${item.size}" selected>${item.size}</option>`;
            }

            html += `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}" onerror="this.src='<?php echo $root; ?>assets/img/default-placeholder.jpg'" class="cart-item__img">
                <div class="cart-item__info">
                    <h4 class="cart-item__name">${item.name}</h4>
                    <div class="cart-item__price">${formatPrice(item.price)}</div>
                    
                    <div class="cart-item__config">
                        <div class="cart-item__select-group">
                            <span class="cart-item__label">Màu:</span>
                            <select class="cart-item__select" onchange="updateCartConfig(${index}, '${item.size}', this.value)">
                                ${colorOptionsHtml}
                            </select>
                        </div>
                        <div class="cart-item__select-group">
                            <span class="cart-item__label">Size:</span>
                            <select class="cart-item__select" onchange="updateCartConfig(${index}, this.value, '${item.color}')">
                                ${sizeOptionsHtml}
                            </select>
                        </div>
                    </div>

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

    // --- Cập nhật cấu hình: Size và Màu sắc ---
    function updateCartConfig(index, newSize, newColor) {
        const item = cart[index];
        const oldSize = item.size;
        const oldColor = item.color;

        if (newSize === oldSize && newColor === oldColor) return;

        let finalImage = item.image;
        if (newColor !== oldColor) {
            const colors = item.allColors || [];
            const newColorObj = colors.find(c => c.color_name === newColor);
            if (newColorObj && newColorObj.image) {
                finalImage = newColorObj.image;
            }
        }

        // Kiểm tra tồn kho của cấu hình mới
        const allSizes = item.allSizes || [];
        const currentColorObj = (item.allColors || []).find(c => c.color_name === newColor);
        const currentColorId = currentColorObj ? currentColorObj.id : null;
        const sizeData = allSizes.find(s => s.size_name === newSize && s.color_id == currentColorId);
        const maxAvailable = sizeData ? parseInt(sizeData.quantity) : 0;

        // Nếu cấu hình mới hết hàng hoàn toàn (không nên xảy ra vì đã disabled option, nhưng vẫn check cho chắc)
        if (maxAvailable <= 0) {
            showToast('Sản phẩm cấu hình này hiện đã hết hàng!', 'error');
            renderCart();
            return;
        }

        // 2. Kiểm tra xem cấu hình mới có trùng với sp nào đã có trong giỏ không
        const existingIndex = cart.findIndex((it, idx) =>
            idx !== index &&
            it.id === item.id &&
            it.size === newSize &&
            it.color === newColor
        );

        if (existingIndex !== -1) {
            // Gộp số lượng
            const totalQty = cart[existingIndex].quantity + item.quantity;
            if (totalQty > maxAvailable) {
                cart[existingIndex].quantity = maxAvailable;
                showToast('Đã gộp và giới hạn theo tồn kho tối đa (' + maxAvailable + ')', 'warning');
            } else {
                cart[existingIndex].quantity = totalQty;
            }
            cart.splice(index, 1);
        } else {
            // Cập nhật cấu hình mới và giới hạn quantity nếu vượt quá stock của config mới
            cart[index].size = newSize;
            cart[index].color = newColor;
            cart[index].image = finalImage;
            if (cart[index].quantity > maxAvailable) {
                cart[index].quantity = maxAvailable;
                showToast('Số lượng đã được điều chỉnh theo tồn kho mới', 'warning');
            }
        }

        saveCart();
    }

    // --- Tăng/giảm số lượng item trong giỏ ---
    function updateCartQty(index, newQty) {
        if (newQty < 1) {
            removeItem(index);
            return;
        }

        const item = cart[index];
        // Tìm tồn kho tối đa
        const colors = item.allColors || [];
        const colorObj = colors.find(c => c.color_name === item.color);
        const colorId = colorObj ? colorObj.id : null;
        
        const allSizes = item.allSizes || [];
        const sizeData = allSizes.find(s => s.size_name === item.size && s.color_id == colorId);
        const stock = sizeData ? parseInt(sizeData.quantity) : 0;

        if (newQty > stock) {
            showToast('Chỉ còn ' + stock + ' sản phẩm trong kho!', 'error');
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

<?php include 'chatbot.php'; ?>