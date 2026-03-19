<?php
session_start();
// Require kết nối Database để cập nhật trạng thái đơn hàng (ĐƯỜNG DẪN CÓ THỂ CẦN CHỈNH SỬA TÙY VỊ TRÍ FILE)
require_once 'includes/config.php'; 
require_once 'includes/vnpay_config.php';

$vnp_SecureHash = $_GET['vnp_SecureHash'];
$inputData = array();

foreach ($_GET as $key => $value) {
    // Chỉ lấy các tham số có prefix 'vnp_'
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

// Bỏ hash ra khỏi data để tính toán lại hash bằng secret key
unset($inputData['vnp_SecureHash']);
ksort($inputData);
$i = 0;
$hashData = "";

foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
}

// Tạo chữ ký dựa trên dữ liệu VNPAY gửi về
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

$orderId = intval($_GET['vnp_TxnRef'] ?? 0);
$vnp_Amount = $_GET['vnp_Amount'] / 100; // Chia 100 lại để lấy giá trị thực
$vnp_ResponseCode = $_GET['vnp_ResponseCode'];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết Quả Thanh Toán VNPAY - SmartFit</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 100%; }
        .success { color: #4CAF50; }
        .error { color: #f44336; }
        h1 { margin-bottom: 20px; }
        p { font-size: 1.1rem; color: #555; line-height: 1.6; }
        .details { margin-top: 20px; padding: 15px; background: #f1f3f5; border-radius: 5px; text-align: left; font-size: 0.95rem; }
        .btn { display: inline-block; margin-top: 25px; padding: 12px 25px; background-color: #333; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; }
        .btn:hover { background-color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // 1. Kiểm tra chữ ký hợp lệ
        if ($secureHash == $vnp_SecureHash) {
            
            // 2. Kiểm tra mã phản hồi từ VNPAY - '00' là thành công
            if ($vnp_ResponseCode == '00') {
                $status = 'success';
                
                // Cập nhật Database: Đơn hàng thanh toán thành công
                $updateSql = "UPDATE orders SET payment_status = 'success' WHERE id = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "i", $orderId);
                mysqli_stmt_execute($updateStmt);

                // ========================================
                // GỬI WEBHOOK EMAIL HÓA ĐƠN SAU KHI VNPAY XÁC NHẬN THÀNH CÔNG
                // ========================================
                $webhookUrl = 'http://host.docker.internal:5678/webhook/order-email';
                $webhookData = json_encode([
                    'order_id'       => $orderId,
                    'email'          => $_SESSION['order_email'] ?? '',
                    'fullname'       => $_SESSION['order_fullname'] ?? '',
                    'total_amount'   => $_SESSION['total_amount'] ?? $vnp_Amount,
                    'payment_method' => $_SESSION['order_payment_method'] ?? 'vnpay',
                    'address'        => $_SESSION['order_address'] ?? ''
                ]);

                $ch = curl_init($webhookUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $webhookData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                @curl_exec($ch);
                curl_close($ch);

                // Dọn dẹp session đơn hàng
                unset($_SESSION['order_email'], $_SESSION['order_fullname'], $_SESSION['order_address'], $_SESSION['order_payment_method']);

                echo "<h1 class='success'>Thanh Toán Thành Công! 🎉</h1>";
                echo "<p>Cảm ơn bạn đã mua sắm tại SmartFit. Đơn hàng của bạn đã được thanh toán.</p>";
            } else {
                $status = 'failed';
                
                // Cập nhật Database: Đơn hàng bị lỗi thanh toán/Hủy
                $updateSql = "UPDATE orders SET payment_status = 'failed' WHERE id = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "i", $orderId);
                mysqli_stmt_execute($updateStmt);

                echo "<h1 class='error'>Thanh Toán Thất Bại ❌</h1>";
                echo "<p>Giao dịch của bạn đã bị hủy hoặc xảy ra lỗi (Mã lỗi: $vnp_ResponseCode).</p>";
            }

            // Hiển thị tóm tắt thông tin GD
            echo "<div class='details'>";
            echo "Mã đơn hàng: <strong>#" . htmlspecialchars($orderId) . "</strong><br>";
            echo "Số tiền GD: <strong>" . number_format($vnp_Amount, 0, ',', '.') . " VNĐ</strong><br>";
            echo "Ngân hàng GD: <strong>" . htmlspecialchars($_GET['vnp_BankCode']) . "</strong><br>";
            echo "Mã GD VNPAY: <strong>" . htmlspecialchars($_GET['vnp_TransactionNo']) . "</strong><br>";
            echo "Nội dung: " . htmlspecialchars($_GET['vnp_OrderInfo']);
            echo "</div>";

        } else {
            // Chữ ký bị sai lệch, có thể do can thiệp URL
            echo "<h1 class='error'>Dữ Liệu Không Hợp Lệ ⚠️</h1>";
            echo "<p>Chữ ký dữ liệu không khớp. Vui lòng liên hệ hỗ trợ nếu nhận thấy bất thường.</p>";
        }
        ?>
        
        <!-- Tuỳ thuộc vào frontend của bạn, bạn có thể redirect về các trang tương ứng -->
        <a href="pages/order_history.php" class="btn">Xem Lịch Sử Đơn Hàng</a>
    </div>
</body>
</html>
