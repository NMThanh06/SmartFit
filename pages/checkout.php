<?php include '../includes/toast.php'; ?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - SmartFit Shop</title>
    <style>
        /* CSS Cơ bản & Layout */
        :root {
            --primary-color: #4CAF50;
            --bg-color: #f8f9fa;
            --text-color: #333;
            --border-color: #ddd;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
        }
        .section {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .left-col { flex: 2; }
        .right-col { flex: 1; height: fit-content; }
        h2 { margin-top: 0; font-size: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; }

        /* Form Giao Hàng */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid var(--border-color);
            border-radius: 5px; box-sizing: border-box; font-family: inherit;
        }

        /* Phương thức thanh toán (Radio Buttons) */
        .payment-methods { margin-top: 20px; }
        .payment-option {
            display: flex; align-items: center; padding: 15px;
            border: 1px solid var(--border-color); border-radius: 5px;
            margin-bottom: 10px; cursor: pointer; transition: 0.3s;
        }
        .payment-option:hover { border-color: var(--primary-color); background: #f0fdf4; }
        .payment-option input { margin-right: 15px; transform: scale(1.2); }
        .payment-option img { width: 40px; height: 40px; object-fit: contain; margin-right: 15px; }

        /* Khung hướng dẫn thanh toán (Mock UI) */
        #payment-instruction {
            display: none; background: #e9ecef; padding: 15px;
            border-radius: 5px; margin-top: 10px; font-size: 0.9rem; text-align: center;
        }

        /* Giỏ hàng (Order Summary) */
        .cart-item { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px;}
        .cart-item p { margin: 0; }
        .item-name { font-weight: 500; }
        .item-price { color: #888; }
        .summary-row { display: flex; justify-content: space-between; margin-top: 15px; font-weight: bold; }
        .total-row { font-size: 1.2rem; color: #d32f2f; margin-top: 20px; border-top: 2px solid var(--border-color); padding-top: 15px;}

        /* Nút Thanh toán */
        .btn-submit {
            width: 100%; padding: 15px; background-color: var(--primary-color);
            color: white; border: none; border-radius: 5px; font-size: 1.1rem;
            font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s;
        }
        .btn-submit:hover { background-color: #45a049; }

        /* Responsive cho Mobile */
        @media (max-width: 768px) {
            .container { flex-direction: column-reverse; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left-col section">
        <h2>Thông tin giao hàng</h2>
        <form id="checkoutForm" onsubmit="processCheckout(event)">
            <div class="form-group">
                <label for="fullname">Họ và Tên *</label>
                <input type="text" id="fullname" required placeholder="Nhập tên người nhận">
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại *</label>
                <input type="tel" id="phone" required placeholder="09xxxxxxxxx">
            </div>
            <div class="form-group">
                <label for="address">Địa chỉ chi tiết *</label>
                <input type="text" id="address" required placeholder="Số nhà, đường, phường/xã, quận/huyện...">
            </div>
            <div class="form-group">
                <label for="note">Ghi chú (Tùy chọn)</label>
                <textarea id="note" rows="3" placeholder="Giao giờ hành chính, gọi trước khi giao..."></textarea>
            </div>

            <h2 style="margin-top: 30px;">Phương thức thanh toán</h2>
            <div class="payment-methods">
                <label class="payment-option" onclick="showInstruction('cod')">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span>Thanh toán khi nhận hàng (COD)</span>
                </label>

                <label class="payment-option" onclick="showInstruction('momo')">
                    <input type="radio" name="payment_method" value="momo">
                    <span style="background: #a50064; color: white; padding: 5px 10px; border-radius: 5px; margin-right: 15px; font-weight: bold;">MoMo</span>
                    <span>Thanh toán qua Ví MoMo</span>
                </label>

                <label class="payment-option" onclick="showInstruction('vnpay')">
                    <input type="radio" name="payment_method" value="vnpay">
                    <span style="background: #005baa; color: white; padding: 5px 10px; border-radius: 5px; margin-right: 15px; font-weight: bold;">VNPAY</span>
                    <span>Thanh toán qua VNPAY-QR / ATM</span>
                </label>
            </div>

            <div id="payment-instruction">
                </div>

            <button type="submit" class="btn-submit">ĐẶT HÀNG: 550.000 VNĐ</button>
        </form>
    </div>

    <div class="right-col section">
        <h2>Đơn hàng của bạn</h2>
        <div class="cart-items">
            <div class="cart-item">
                <div>
                    <p class="item-name">Áo Thun Trắng Oversized</p>
                    <p class="item-price">Size: M | SL: 1</p>
                </div>
                <p class="item-price">250.000đ</p>
            </div>
            <div class="cart-item">
                <div>
                    <p class="item-name">Quần Jean Baggy Xanh</p>
                    <p class="item-price">Size: 30 | SL: 1</p>
                </div>
                <p class="item-price">300.000đ</p>
            </div>
        </div>

        <div class="summary-row">
            <span>Tạm tính:</span>
            <span>550.000đ</span>
        </div>
        <div class="summary-row">
            <span>Phí giao hàng:</span>
            <span>Miễn phí</span>
        </div>
        <div class="summary-row total-row">
            <span>Tổng cộng:</span>
            <span>550.000 VNĐ</span>
        </div>
    </div>
</div>
<script>
    // KHÔNG CẦN định nghĩa showToast ở đây nữa vì đã có trong toast.php rồi!

    // 1. Hàm định dạng tiền tệ
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    // 2. Tải giỏ hàng từ Database
    async function loadCheckoutCart() {
        try {
            // 1. Thêm ?t=... để chống Cache giống hệt trang Shop
            const response = await fetch('../includes/fetch_cart.php?t=' + new Date().getTime(), {
                cache: 'no-store'
            }); 
            const data = await response.json();
            
            const cartItemsContainer = document.querySelector('.cart-items');
            let total = 0;

            // 2. NẾU GIỎ RỖNG -> ÉP GIÁ TIỀN VỀ 0
            if (!data.items || data.items.length === 0) {
                cartItemsContainer.innerHTML = '<p>Giỏ hàng trống.</p>';
                document.querySelector('.btn-submit').disabled = true;
                
                const zeroPrice = formatPrice(0);
                document.querySelectorAll('.summary-row')[0].lastElementChild.innerText = zeroPrice;
                document.querySelector('.total-row').lastElementChild.innerText = zeroPrice;
                document.querySelector('.btn-submit').innerText = `ĐẶT HÀNG: ${zeroPrice}`;
                return;
            }

            // 3. NẾU CÓ HÀNG -> TÍNH TOÁN LẠI TỪ ĐẦU
            let html = '';
            data.items.forEach(item => {
                total += item.price * item.quantity;
                html += `
                <div class="cart-item">
                    <div>
                        <p class="item-name">${item.name}</p>
                        <p class="item-price">Size: ${item.size_name || 'M'} | SL: ${item.quantity}</p>
                    </div>
                    <p class="item-price">${formatPrice(item.price * item.quantity)}</p>
                </div>`;
            });

            cartItemsContainer.innerHTML = html;
            
            // Cập nhật tổng tiền chuẩn xác
            const formattedTotal = formatPrice(total);
            document.querySelectorAll('.summary-row')[0].lastElementChild.innerText = formattedTotal;
            document.querySelector('.total-row').lastElementChild.innerText = formattedTotal;
            document.querySelector('.btn-submit').innerText = `ĐẶT HÀNG: ${formattedTotal}`;
            
        } catch (err) {
            console.error("Lỗi tải giỏ hàng:", err);
        }
    }
    // 3. Đổi giao diện chọn thanh toán
    function showInstruction(method) {
        // ... (Giữ nguyên code đổi phương thức thanh toán) ...
    }

    // 4. Xử lý Đặt hàng (Gọi thẳng hàm showToast từ file kia)
    async function processCheckout(event) {
        event.preventDefault(); 
        
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.innerText = 'Đang xử lý...';
        submitBtn.style.opacity = '0.7';
        submitBtn.disabled = true;

        const orderData = {
            fullname: document.getElementById('fullname').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            note: document.getElementById('note').value,
            payment_method: document.querySelector('input[name="payment_method"]:checked').value
        };

        try {
            const response = await fetch('../includes/process_checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            const result = await response.json();

            if (result.status === 'success') {
                // GỌI HÀM SHOWTOAST TỪ FILE TOAST.PHP BẠN VỪA NHÚNG
                showToast(result.message, 'success');
                
                // Đợi 2 giây để người dùng đọc Toast rồi mới chuyển sang lịch sử đơn hàng
                setTimeout(() => {
                    window.location.href = "order_history.php"; 
                }, 2000);
            } else {
                showToast(result.message, 'error');
                submitBtn.innerText = 'THỬ LẠI';
                submitBtn.style.opacity = '1';
                submitBtn.disabled = false;
            }
        } catch (err) {
            console.error(err);
            showToast("Lỗi hệ thống, vui lòng thử lại sau.", 'error');
            submitBtn.style.opacity = '1';
            submitBtn.disabled = false;
        }
    }

    window.onload = loadCheckoutCart;
</script>

</body>
</html>