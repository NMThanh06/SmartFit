<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'] ?? 0;

if ($userId == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để thanh toán!']);
    exit;
}

// Lấy thông tin từ form khách hàng gửi lên
$fullname = $data['fullname'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';
$note = $data['note'] ?? '';
$payment_method = $data['payment_method'] ?? 'cod';
$cartItems = $data['cart_items'] ?? []; // Giỏ hàng gửi từ localStorage

// Nếu email trống và đã đăng nhập, tự động lấy từ DB
if (empty($email) && $userId > 0) {
    $emailStmt = mysqli_prepare($conn, "SELECT email FROM users WHERE id = ?");
    mysqli_stmt_bind_param($emailStmt, 'i', $userId);
    mysqli_stmt_execute($emailStmt);
    $emailResult = mysqli_stmt_get_result($emailStmt);
    if ($emailRow = mysqli_fetch_assoc($emailResult)) {
        $email = $emailRow['email'];
    }
}

if (empty($fullname) || empty($phone) || empty($address)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc!']);
    exit;
}

if (count($cartItems) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng của bạn đang trống!']);
    exit;
}

// ========================================
// BẮT ĐẦU DATABASE TRANSACTION
// ========================================
mysqli_begin_transaction($conn);

try {
    // ========================================
    // BƯỚC 1: TÍNH TỔNG TIỀN TỪ GIÁ TRONG DATABASE (Chống hack đổi giá)
    // + KIỂM TRA TỒN KHO (SELECT ... FOR UPDATE — Khóa dòng để tránh race condition)
    // ========================================
    $totalAmount = 0;
    $validatedItems = []; // Mảng chứa dữ liệu đã xác thực từ DB

    foreach ($cartItems as $item) {
        $outfitId = intval($item['id'] ?? 0);
        $sizeName = $item['size'] ?? '';
        $quantity = intval($item['quantity'] ?? 0);

        if ($outfitId <= 0 || empty($sizeName) || $quantity <= 0) {
            throw new Exception("Dữ liệu giỏ hàng không hợp lệ!");
        }

        // Lấy giá thực từ DB (không tin giá từ frontend)
        $priceSql = "SELECT price, name FROM outfits WHERE id = ?";
        $priceStmt = mysqli_prepare($conn, $priceSql);
        mysqli_stmt_bind_param($priceStmt, "i", $outfitId);
        mysqli_stmt_execute($priceStmt);
        $priceResult = mysqli_stmt_get_result($priceStmt);
        $outfit = mysqli_fetch_assoc($priceResult);

        if (!$outfit) {
            throw new Exception("Sản phẩm ID $outfitId không tồn tại trong hệ thống!");
        }

        // Kiểm tra tồn kho với SELECT ... FOR UPDATE (Khóa dòng tránh đặt đồng thời)
        $stockSql = "SELECT quantity FROM outfit_sizes WHERE outfit_id = ? AND size_name = ? FOR UPDATE";
        $stockStmt = mysqli_prepare($conn, $stockSql);
        mysqli_stmt_bind_param($stockStmt, "is", $outfitId, $sizeName);
        mysqli_stmt_execute($stockStmt);
        $stockResult = mysqli_stmt_get_result($stockStmt);
        $stockRow = mysqli_fetch_assoc($stockResult);

        if (!$stockRow) {
            throw new Exception("Sản phẩm '{$outfit['name']}' không có size '$sizeName'!");
        }

        if ($stockRow['quantity'] < $quantity) {
            throw new Exception("Sản phẩm '{$outfit['name']}' size $sizeName chỉ còn {$stockRow['quantity']} trong kho, nhưng bạn đặt $quantity!");
        }

        $totalAmount += $outfit['price'] * $quantity;
        $validatedItems[] = [
            'outfit_id' => $outfitId,
            'name' => $outfit['name'],
            'size_name' => $sizeName,
            'quantity' => $quantity,
            'price' => $outfit['price']
        ];
    }

    // ========================================
    // BƯỚC 2: TẠO HÓA ĐƠN CHÍNH (Bảng orders)
    // ========================================
    // Tạo mã đơn hàng tùy chỉnh: YYYYMMDDHHmmss + 3 số ngẫu nhiên
    $orderId = date('YmdHis') . rand(100, 999);

    $payment_status = 'pending';
    $orderSql = "INSERT INTO orders (id, user_id, fullname, phone, address, note, payment_method, payment_status, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "sissssssi", $orderId, $userId, $fullname, $phone, $address, $note, $payment_method, $payment_status, $totalAmount);
    mysqli_stmt_execute($orderStmt);

    // ========================================
    // BƯỚC 3: LƯU CHI TIẾT ĐƠN HÀNG + TRỪ TỒN KHO
    // ========================================
    $detailSql = "INSERT INTO order_details (order_id, outfit_id, size_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $detailStmt = mysqli_prepare($conn, $detailSql);

    $deductSql = "UPDATE outfit_sizes SET quantity = quantity - ? WHERE outfit_id = ? AND size_name = ?";
    $deductStmt = mysqli_prepare($conn, $deductSql);

    foreach ($validatedItems as $item) {
        // 3a. Lưu chi tiết đơn hàng
        mysqli_stmt_bind_param($detailStmt, "sisii", $orderId, $item['outfit_id'], $item['size_name'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($detailStmt);

        // 3b. TRỪ TỒN KHO — Đây là bước quan trọng nhất!
        mysqli_stmt_bind_param($deductStmt, "iis", $item['quantity'], $item['outfit_id'], $item['size_name']);
        mysqli_stmt_execute($deductStmt);

        // Kiểm tra xem UPDATE có thực sự ảnh hưởng dòng nào không
        if (mysqli_stmt_affected_rows($deductStmt) === 0) {
            throw new Exception("Không thể trừ kho cho '{$item['name']}' size {$item['size_name']}!");
        }
    }

    // ========================================
    // BƯỚC 4: CHỐT GIAO DỊCH — TẤT CẢ THÀNH CÔNG
    // ========================================
    mysqli_commit($conn);

    // ========================================
    // BƯỚC 5: XỬ LÝ THEO TỪNG PHƯƠNG THỨC THANH TOÁN
    // ========================================
    switch ($payment_method) {
        case 'cod':
            // ==========================================
            // ĐOẠN CODE GỬI WEBHOOK SANG n8n (DEBUG MODE)
            // ==========================================

            // 1. Gom dữ liệu để gửi
            $data_to_n8n = [
                'order_id' => $orderId,
                'fullname' => $fullname,
                'email' => $email,
                'total_amount' => $totalAmount,
                'payment_method' => 'cod',
                'address' => $address
            ];

            // 2. Setup cURL
            $webhook_url = 'http://127.0.0.1:5678/webhook/order-email';
            $ch = curl_init($webhook_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_to_n8n));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            // 3. Bóp cò gửi đi
            $response = curl_exec($ch);

            // 4. Bắt mạch xem n8n trả lời cái gì
            if (curl_errno($ch)) {
                // Lỗi không gửi được (sai IP, sai cổng, n8n sập...)
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Lỗi mạng cURL: ' . curl_error($ch)
                ]);
                exit;
            }
            else {
                // Sửa lại thành 'success' để website tiếp tục chuyển hướng trang báo thành công
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Đặt hàng và gửi mail thành công!',
                    'order_id' => $orderId,           // Giữ lại để JS có thông tin chuyển trang
                    'redirect_url' => 'order_history.php'
                ]);
                exit;
            }

            curl_close($ch);
            // ==========================================

            echo json_encode([
                'status' => 'success',
                'message' => 'Đặt hàng thành công! Mã đơn: #' . $orderId,
                'order_id' => $orderId,
                'redirect_url' => 'order_history.php'
            ]);
            break;

        case 'vnpay':
            // VNPay/MoMo: KHÔNG gửi webhook ở đây.
            // Webhook sẽ được gửi từ vnpay_return.php SAU KHI cổng thanh toán xác nhận thành công.
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['order_id'] = $orderId;
            $_SESSION['total_amount'] = $totalAmount;
            // Lưu thông tin đơn hàng vào session để vnpay_return.php dùng gửi webhook
            $_SESSION['order_email'] = $email;
            $_SESSION['order_fullname'] = $fullname;
            $_SESSION['order_address'] = $address;
            $_SESSION['order_payment_method'] = $payment_method;

            // File này sẽ phụ trách tạo VNPAY URL và echo JSON để frontend redirect tới VNPay
            require_once 'vnpay_create.php';
            break;

        case 'momo':
            echo json_encode([
                'status' => 'error',
                'message' => 'Chức năng thanh toán qua Ví MoMo đang được phát triển!'
            ]);
            break;

        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Phương thức thanh toán không hợp lệ!'
            ]);
            break;
    }

}
catch (Exception $e) {
    // ========================================
    // ROLLBACK — HOÀN TÁC TẤT CẢ NẾU CÓ BẤT KỲ LỖI NÀO
    // ========================================
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>