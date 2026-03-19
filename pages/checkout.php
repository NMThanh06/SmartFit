<?php
include '../includes/header.php';

$shopId = isset($_GET['shop_id']) ? intval($_GET['shop_id']) : 0;
if ($shopId <= 0) {
    echo "<script>alert('Vui lòng chọn Shop từ giỏ hàng để thanh toán!'); window.location.href='../shop.php';</script>";
    exit;
}

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
            <a href="javascript:history.back()" class="back-link"><i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng</a>
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
                                <input type="text" id="fullname" name="fullname" required placeholder="Nhập tên người nhận" value="<?php echo htmlspecialchars($user_fullname); ?>">
                            </div>
                            <div class="form-group col-half">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" required placeholder="example@email.com" value="<?php echo htmlspecialchars($user_email); ?>" <?php echo $is_logged_in && !empty($user_email) ? 'readonly style="background:#e9e9ed;cursor:not-allowed;"' : ''; ?>>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-half">
                                <label for="phone">Số điện thoại <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" required placeholder="09xxxxxxxxx" value="<?php echo htmlspecialchars($user_phone); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Địa chỉ chi tiết <span class="required">*</span></label>
                            <input type="text" id="address" name="address" required placeholder="Số nhà, đường, phường/xã, quận/huyện..." value="<?php echo htmlspecialchars($user_address); ?>">
                        </div>
                        <div class="form-group">
                            <label for="note">Ghi chú (Tùy chọn)</label>
                            <textarea id="note" name="note" rows="3" placeholder="Giao giờ hành chính, gọi trước khi giao..."></textarea>
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
                        <!-- Items sẽ load từ PHP -->
                        <?php
$subtotal = 0;
$sqlCart = "SELECT c.*, o.name, o.price, 
                                           COALESCE(col.image, (SELECT image FROM outfit_colors WHERE outfit_id = o.id LIMIT 1), 'assets/img/default-placeholder.jpg') as image
                                    FROM shopping_cart c 
                                    JOIN outfits o ON c.outfit_id = o.id 
                                    LEFT JOIN outfit_colors col ON (c.outfit_id = col.outfit_id AND c.color_name COLLATE utf8mb4_unicode_ci = col.color_name COLLATE utf8mb4_unicode_ci)
                                    WHERE c.user_id = ? AND o.owner_id = ?";
$stmtCart = mysqli_prepare($conn, $sqlCart);
mysqli_stmt_bind_param($stmtCart, "ii", $_SESSION['user_id'], $shopId);
mysqli_stmt_execute($stmtCart);
$resCart = mysqli_stmt_get_result($stmtCart);

while ($item = mysqli_fetch_assoc($resCart)):
    $lineTotal = $item['price'] * $item['quantity'];
    $subtotal += $lineTotal;
?>
                        <div class="checkout-item">
                            <img src="<?php echo getImageUrl($item['image']); ?>" alt="" class="checkout-item__img" onerror="this.src='<?php echo $root; ?>assets/img/default-placeholder.jpg'">
                            <div class="checkout-item__info">
                                <h4 class="checkout-item__name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="checkout-item__meta">Size: <?php echo $item['size_name']; ?> | SL: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="checkout-item__price"><?php echo number_format($lineTotal, 0, ',', '.'); ?>đ</div>
                        </div>
                        <?php
endwhile; ?>
                    </div>

                    <div class="summary-details">
                        <div class="summary-line">
                            <span>Tạm tính</span>
                            <span><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="summary-line">
                            <span>Phí vận chuyển</span>
                            <span class="free-shipping">Miễn phí</span>
                        </div>
                        <div class="summary-line summary-line--total">
                            <span>Tổng cộng</span>
                            <span id="totalCheckoutPrice"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
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
    .checkout-page { background: #f5f5f7; padding: 50px 0; min-height: 80vh; color: #1d1d1f; }
    .checkout-header { margin-bottom: 30px; }
    .back-link { font-size: 1.4rem; color: #555; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 10px; }
    .back-link:hover { color: var(--primary-blue); }
    .checkout-title { font-size: 2.8rem; font-weight: 700; color: #1d1d1f; }

    .checkout-section { background: #fff; padding: 28px; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e8e8ed; }
    .checkout-summary { position: sticky; top: 90px; }

    .section-title { font-size: 1.7rem; font-weight: 700; color: #1d1d1f; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #e8e8ed; display: flex; align-items: center; gap: 8px; }
    .mt-40 { margin-top: 30px; }

    /* Form */
    .form-row { display: flex; gap: 16px; }
    .col-half { flex: 1; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; margin-bottom: 7px; font-weight: 600; font-size: 1.35rem; color: #1d1d1f; }
    .required { color: #e53030; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 11px 14px; border: 1px solid #d2d2d7; border-radius: 10px;
        font-size: 1.4rem; color: #1d1d1f; background: #fff; transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary-blue); }
    .form-group textarea { resize: vertical; }

    /* Payment */
    .payment-methods { display: flex; flex-direction: column; gap: 10px; }
    .payment-option {
        display: flex; align-items: center; gap: 14px;
        border: 1.5px solid #e8e8ed; padding: 14px 16px; border-radius: 12px; cursor: pointer;
        transition: all 0.2s;
    }
    .payment-option:hover { border-color: var(--primary-blue); background: #f8f8fa; }
    .payment-option.active { border-color: var(--primary-blue); background: rgba(37,99,235,0.04); }
    .payment-option input[type="radio"] { width: 18px; height: 18px; flex-shrink: 0; accent-color: var(--primary-blue); cursor: pointer; }
    .payment-content { display: flex; align-items: center; gap: 12px; flex: 1; }
    .payment-btn { padding: 5px 12px; border-radius: 6px; font-size: 1.2rem; font-weight: 700; letter-spacing: 0.5px; }
    .payment-btn--cod { background: #22c55e; color: #fff; }
    .payment-btn--vnpay { background: #1d4ed8; color: #fff; }
    .payment-info { display: flex; flex-direction: column; gap: 2px; }
    .payment-name { font-size: 1.4rem; font-weight: 600; color: #1d1d1f; }
    .payment-desc { font-size: 1.2rem; color: #6e6e73; }

    /* Checkout items */
    .checkout-cart-items { margin-bottom: 16px; }
    .checkout-item { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f5f5f7; }
    .checkout-item__img { width: 55px; height: 70px; object-fit: cover; border-radius: 8px; flex-shrink: 0; border: 1px solid #e8e8ed; }
    .checkout-item__info { flex: 1; min-width: 0; }
    .checkout-item__name { font-size: 1.4rem; font-weight: 600; color: #1d1d1f; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .checkout-item__meta { font-size: 1.2rem; color: #6e6e73; }
    .checkout-item__price { font-weight: 700; font-size: 1.4rem; color: #1d1d1f; white-space: nowrap; }

    /* Summary */
    .summary-details { margin-top: 8px; }
    .summary-line { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 1.4rem; color: #1d1d1f; }
    .free-shipping { color: #22c55e; font-weight: 600; }
    .summary-line--total { font-size: 1.8rem; font-weight: 700; border-top: 2px solid #e8e8ed; padding-top: 14px; margin-top: 14px; }
    .summary-note { font-size: 1.15rem; color: #6e6e73; text-align: center; margin-top: 12px; line-height: 1.5; }
    .btn-place-order { width: 100%; padding: 15px; background: #1d1d1f; color: #fff; border: none; border-radius: 12px; font-size: 1.6rem; font-weight: 700; cursor: pointer; margin-top: 16px; transition: background 0.2s; }
    .btn-place-order:hover { background: var(--primary-blue); }
    .payment-instruction { margin-top: 12px; padding: 12px 16px; background: #eff6ff; border-radius: 10px; color: #1d4ed8; font-size: 1.3rem; display: none; }

    @media (max-width: 768px) {
        .form-row { flex-direction: column; gap: 0; }
        .checkout-summary { position: static; margin-top: 20px; }
    }
</style>

<script>
    function showInstruction(method) {
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
        event.currentTarget.classList.add('active');
        const instruction = document.getElementById('payment-instruction');
        if (method === 'vnpay') {
            instruction.style.display = 'block';
            instruction.innerHTML = '<p><i class="fa-solid fa-info-circle"></i> Bạn sẽ được chuyển đến cổng VNPAY để hoàn tất thanh toán.</p>';
        } else {
            instruction.style.display = 'none';
        }
    }

    async function processCheckout(event) {
        event.preventDefault();
        const btn = document.getElementById('btnPlaceOrder');
        btn.disabled = true;
        btn.innerText = 'ĐANG XỬ LÝ...';

        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());
        data.shop_id = <?php echo $shopId; ?>;
        
        // Form controls without a name attribute or disabled ones won't be in FormData
        if (!data.email) {
            data.email = document.getElementById('email').value;
        }
        
        try {
            const response = await fetch('../includes/process_checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                showToast(result.message, 'success');
                setTimeout(() => window.location.href = result.redirect_url || 'order_history.php', 1500);
            } else {
                showToast(result.message, 'error');
                btn.disabled = false;
                btn.innerText = 'ĐẶT HÀNG NGAY';
            }
        } catch (err) {
            console.error(err);
            showToast('Lỗi kết nối máy chủ!', 'error');
            btn.disabled = false;
            btn.innerText = 'ĐẶT HÀNG NGAY';
        }
    }
</script>

<?php include '../includes/footer.php'; ?>