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
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';
$note = $data['note'] ?? '';
$payment_method = $data['payment_method'] ?? 'cod';
$cartItems = $data['cart_items'] ?? []; // Giỏ hàng gửi từ localStorage

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
    $orderSql = "INSERT INTO orders (user_id, fullname, phone, address, note, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "isssssi", $userId, $fullname, $phone, $address, $note, $payment_method, $totalAmount);
    mysqli_stmt_execute($orderStmt);
    
    $orderId = mysqli_insert_id($conn);

    // ========================================
    // BƯỚC 3: LƯU CHI TIẾT ĐƠN HÀNG + TRỪ TỒN KHO
    // ========================================
    $detailSql = "INSERT INTO order_details (order_id, outfit_id, size_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $detailStmt = mysqli_prepare($conn, $detailSql);

    $deductSql = "UPDATE outfit_sizes SET quantity = quantity - ? WHERE outfit_id = ? AND size_name = ?";
    $deductStmt = mysqli_prepare($conn, $deductSql);

    foreach ($validatedItems as $item) {
        // 3a. Lưu chi tiết đơn hàng
        mysqli_stmt_bind_param($detailStmt, "iisii", $orderId, $item['outfit_id'], $item['size_name'], $item['quantity'], $item['price']);
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
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Đặt hàng thành công! Mã đơn: #' . $orderId,
        'order_id' => $orderId
    ]);

} catch (Exception $e) {
    // ========================================
    // ROLLBACK — HOÀN TÁC TẤT CẢ NẾU CÓ BẤT KỲ LỖI NÀO
    // ========================================
    mysqli_rollback($conn); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>