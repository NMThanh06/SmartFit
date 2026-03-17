<?php
include '../includes/header.php';

// Khởi tạo các biến chứa thông tin người dùng (mặc định trống)
$user_fullname = '';
$user_email = '';
$user_phone = '';
$user_address = '';
$is_logged_in = isset($_SESSION['user_id']);

// Nếu đã đăng nhập, lấy dữ liệu thực tế từ DB
if ($is_logged_in) {
    $u_id = $_SESSION['user_id'];
    $sql = "SELECT name, email, fullname, phone, address FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $u_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($u_info = mysqli_fetch_assoc($res)) {
        $user_fullname = $u_info['fullname'] ?: ($u_info['name'] ?: '');
        $user_email = $u_info['email'] ?: '';
        $user_phone = $u_info['phone'] ?: '';
        $user_address = $u_info['address'] ?: '';
    }
}
?>

<div class="checkout-page">
    <div class="grid wide">
        <div class="checkout-header">
            <a href="../shop.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại cửa hàng</a>
            <h1 class="checkout-title">Thanh toán đơn hàng</h1>
        </div>

        <div class="row">
            <!-- Cột trái: Thông tin giao hàng -->
            <div class="col l-8 m-12 c-12">
                <div class="checkout-section">
                    <h2 class="section-title"><i class="fa-solid fa-truck-fast"></i> Thông tin giao hàng</h2>
                    <form id="checkoutForm" onsubmit="processCheckout(event)" class="checkout-form">
                        <div class="form-row">
                            <div class="form-group col-half">
                                <label for="fullname">Họ và Tên người nhận <span class="required">*</span></label>
                                <input type="text" id="fullname" required placeholder="Nhập tên người nhận" value="<?php echo htmlspecialchars($user_fullname); ?>">
                            </div>
                            <div class="form-group col-half">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" required placeholder="example@email.com" value="<?php echo htmlspecialchars($user_email); ?>" <?php echo $is_logged_in && !empty($user_email) ? 'readonly style="background:#e9e9ed;cursor:not-allowed;"' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-half">
                                <label for="phone">Số điện thoại <span class="required">*</span></label>
                                <input type="tel" id="phone" required placeholder="09xxxxxxxxx" value="<?php echo htmlspecialchars($user_phone); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Địa chỉ chi tiết <span class="required">*</span></label>
                            <input type="text" id="address" required placeholder="Số nhà, đường, phường/xã, quận/huyện..." value="<?php echo htmlspecialchars($user_address); ?>">
                        </div>
                        <div class="form-group">
                            <label for="note">Ghi chú (Tùy chọn)</label>
                            <textarea id="note" rows="3" placeholder="Giao giờ hành chính, gọi trước khi giao..."></textarea>
                        </div>

                        <h2 class="section-title mt-40"><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</h2>
                        <div class="payment-methods">
                            <label class="payment-option active" onclick="showInstruction('cod')">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div class="payment-content">
                                    <span class="payment-btn payment-btn--cod">COD</span>
                                    <div class="payment-info">
                                        <span class="payment-name">Thanh toán khi nhận hàng (COD)</span>
                                        <span class="payment-desc">Thanh toán bằng tiền mặt khi shipper giao hàng đến</span>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-option is-disabled" onclick="event.preventDefault(); showToast('Thanh toán MoMo đang bảo trì!', 'error')">
                                <input type="radio" name="payment_method" value="momo" disabled>
                                <div class="payment-content">
                                    <span class="payment-btn payment-btn--momo">MoMo</span>
                                    <div class="payment-info">
                                        <span class="payment-name">Ví MoMo (Đang bảo trì)</span>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-option" onclick="showInstruction('vnpay')">
                                <input type="radio" name="payment_method" value="vnpay">
                                <div class="payment-content">
                                    <span class="payment-btn payment-btn--vnpay">VNPAY</span>
                                    <div class="payment-info">
                                        <span class="payment-name">Thanh toán VNPAY-QR</span>
                                        <span class="payment-desc">Cổng thanh toán điện tử ATM / QR Code</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div id="payment-instruction" class="payment-instruction"></div>
                    </form>
                </div>
            </div>

            <!-- Cột phải: Tóm tắt đơn hàng -->
            <div class="col l-4 m-12 c-12">
                <div class="checkout-summary checkout-section">
                    <h2 class="section-title">Đơn hàng của bạn</h2>
                    <div id="checkoutCartItems" class="checkout-cart-items">
                        <!-- Items sẽ load từ JS -->
                    </div>

                    <div class="summary-details">
                        <div class="summary-line">
                            <span>Tạm tính</span>
                            <span id="subtotalPrice">0đ</span>
                        </div>
                        <div class="summary-line">
                            <span>Phí vận chuyển</span>
                            <span class="free-shipping">Miễn phí</span>
                        </div>
                        <div class="summary-line summary-line--total">
                            <span>Tổng cộng</span>
                            <span id="totalCheckoutPrice">0đ</span>
                        </div>
                    </div>

                    <button type="submit" form="checkoutForm" id="btnPlaceOrder" class="btn-place-order">
                        ĐẶT HÀNG NGAY
                    </button>
                    <p class="summary-note">Bằng cách nhấp vào "Đặt hàng ngay", bạn đồng ý với các điều khoản của SmartFit.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .checkout-page {
        background-color: var(--apple-bg);
        padding: 40px 0 80px;
        min-height: calc(100vh - var(--navbar-height));
    }

    .checkout-header {
        margin-bottom: 30px;
    }

    .back-link {
        text-decoration: none;
        color: var(--apple-grey);
        font-size: 1.4rem;
        font-weight: 500;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--primary-blue);
    }

    .checkout-title {
        font-size: 3.2rem;
        font-weight: 700;
        color: var(--apple-black);
        margin-top: 20px;
        letter-spacing: -1px;
    }

    .checkout-section {
        background: #fff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--apple-black);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title i {
        color: var(--primary-blue);
        font-size: 1.8rem;
    }

    .mt-40 { margin-top: 40px; }

    /* Form Styles */
    .form-row {
        display: flex;
        gap: 20px;
    }

    .col-half { flex: 1; }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
    }

    .form-group label {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--apple-grey);
        margin-left: 4px;
    }

    .required {
        color: var(--error);
        margin-left: 2px;
        font-weight: 700;
    }

    .form-group input, 
    .form-group textarea {
        width: 100%;
        padding: 14px 18px;
        background: #f5f5f7;
        border: 1px solid transparent;
        border-radius: 12px;
        font-size: 1.5rem;
        color: var(--apple-black);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: inherit;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        background: #fff;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 4px rgba(33, 118, 255, 0.1);
    }

    /* Payment Methods */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .payment-option {
        border: 2px solid #f5f5f7;
        border-radius: 16px;
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: block;
        position: relative;
    }

    .payment-option:hover {
        background: #f9f9fb;
        border-color: #e8e8ed;
    }

    .payment-option input {
        position: absolute;
        opacity: 0;
    }

    .payment-option.active {
        border-color: var(--primary-blue);
        background: rgba(33, 118, 255, 0.02);
    }

    .payment-option.is-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .payment-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .payment-btn {
        min-width: 65px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.2rem;
        font-weight: 800;
        color: #fff;
    }

    .payment-btn--cod { background-color: var(--success); }
    .payment-btn--momo { background-color: #a50064; }
    .payment-btn--vnpay { background-color: #005baa; }

    .payment-info {
        display: flex;
        flex-direction: column;
    }

    .payment-name {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--apple-black);
    }

    .payment-desc {
        font-size: 1.3rem;
        color: var(--apple-grey);
    }

    .payment-instruction {
        display: none;
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        font-size: 1.4rem;
        color: var(--apple-black);
        border-left: 4px solid var(--primary-blue);
    }

    /* Summary Items */
    .checkout-cart-items {
        max-height: 350px;
        overflow-y: auto;
        margin-bottom: 25px;
        padding-right: 5px;
    }

    .checkout-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f2;
    }

    .checkout-item:last-child { border-bottom: none; }

    .checkout-item__img {
        width: 60px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        background: #f5f5f7;
    }

    .checkout-item__info { flex: 1; }

    .checkout-item__name {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--apple-black);
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .checkout-item__meta {
        font-size: 1.3rem;
        color: var(--apple-grey);
    }

    .checkout-item__price {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--apple-black);
        text-align: right;
    }

    /* Summary Details */
    .summary-details {
        border-top: 2px solid #f0f0f2;
        padding-top: 20px;
        margin-bottom: 25px;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 1.5rem;
        color: var(--apple-grey);
    }

    .summary-line--total {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f2;
        color: var(--apple-black);
        font-weight: 700;
        font-size: 1.8rem;
    }

    #totalCheckoutPrice { color: var(--primary-blue); }

    .free-shipping {
        color: var(--success);
        font-weight: 600;
    }

    .btn-place-order {
        width: 100%;
        padding: 18px;
        background: var(--apple-black);
        color: #fff;
        border: none;
        border-radius: 16px;
        font-size: 1.7rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-place-order:hover {
        background: #333;
        transform: scale(1.02);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .summary-note {
        font-size: 1.2rem;
        color: var(--apple-grey);
        text-align: center;
        margin-top: 15px;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .form-row { flex-direction: column; gap: 0; }
        .checkout-title { font-size: 2.6rem; }
    }
</style>

<script>
    // 1. Định dạng tiền tệ
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    // 2. Tải giỏ hàng
    function loadCheckoutCart() {
        const cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];
        const container = document.getElementById('checkoutCartItems');
        const btnSubmit = document.getElementById('btnPlaceOrder');
        
        let total = 0;

        if (cart.length === 0) {
            container.innerHTML = `
                <div style="text-align:center; padding: 40px 0;">
                    <i class="fa-solid fa-cart-shopping" style="font-size: 3rem; color: #eee; margin-bottom: 15px;"></i>
                    <p style="font-size: 1.4rem; color: var(--apple-grey);">Giỏ hàng đang trống.</p>
                </div>
            `;
            btnSubmit.disabled = true;
            btnSubmit.style.opacity = '0.5';
            btnSubmit.innerText = 'GIỎ HÀNG TRỐNG';
            return;
        }

        let html = '';
        cart.forEach(item => {
            total += item.price * item.quantity;
            html += `
            <div class="checkout-item">
                <img src="${item.image}" alt="${item.name}" onerror="this.src='../assets/img/default-placeholder.jpg'" class="checkout-item__img">
                <div class="checkout-item__info">
                    <h4 class="checkout-item__name">${item.name}</h4>
                    <p class="checkout-item__meta">Size: ${item.size} | Màu: ${item.color} | SL: ${item.quantity}</p>
                </div>
                <div class="checkout-item__price">${formatPrice(item.price * item.quantity)}</div>
            </div>`;
        });

        container.innerHTML = html;
        document.getElementById('subtotalPrice').innerText = formatPrice(total);
        document.getElementById('totalCheckoutPrice').innerText = formatPrice(total);
    }

    // 3. Hiển thị hướng dẫn thanh toán
    function showInstruction(method) {
        const instruction = document.getElementById('payment-instruction');
        
        // Hủy active các option khác
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
        
        // Thêm active cho option được chọn
        const selectedLabel = event.currentTarget;
        selectedLabel.classList.add('active');

        if (method === 'cod') {
            instruction.style.display = 'none';
        } else if (method === 'vnpay') {
            instruction.style.display = 'block';
            instruction.innerHTML = `
                <p><strong><i class="fa-solid fa-circle-info"></i> Hướng dẫn:</strong> Sau khi nhấn đặt hàng, bạn sẽ được chuyển đến cổng thanh toán VNPAY để quét mã QR hoặc nhập thông tin thẻ ATM.</p>
            `;
        }
    }

    // 4. Xử lý Đặt hàng
    async function processCheckout(event) {
        event.preventDefault(); 
        
        const submitBtn = document.getElementById('btnPlaceOrder');
        const originalText = submitBtn.innerText;
        
        submitBtn.innerText = 'Đang xử lý...';
        submitBtn.disabled = true;

        const cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];
        if (cart.length === 0) {
            showToast('Giỏ hàng trống!', 'error');
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
            return;
        }

        const paymentInput = document.querySelector('input[name="payment_method"]:checked');
        const paymentMethod = paymentInput ? paymentInput.value : 'cod';

        const orderData = {
            fullname: document.getElementById('fullname').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            note: document.getElementById('note').value,
            payment_method: paymentMethod,
            cart_items: cart
        };

        try {
            const response = await fetch('../includes/process_checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            const result = await response.json();

            if (result.status === 'success') {
                showToast(result.message, 'success');
                localStorage.removeItem('smartfit_cart');
                setTimeout(() => {
                    window.location.href = result.redirect_url || "order_history.php"; 
                }, 2000);
            } else {
                showToast(result.message, 'error');
                submitBtn.innerText = 'THỬ LẠI';
                submitBtn.disabled = false;
            }
        } catch (err) {
            console.error(err);
            showToast("Lỗi kết nối máy chủ!", 'error');
            submitBtn.disabled = false;
            submitBtn.innerText = 'THỬ LẠI';
        }
    }

    window.onload = loadCheckoutCart;
</script>

<?php include '../includes/footer.php'; ?>