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
$shopId = intval($data['shop_id'] ?? 0);

if ($shopId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Thông tin Shop không hợp lệ!']);
    exit;
}

if (empty($fullname) || empty($phone) || empty($address)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc!']);
    exit;
}

// Lấy giỏ hàng từ DB thay vì localStorage
$sqlFetchCart = "SELECT c.*, o.name, o.price 
                 FROM shopping_cart c 
                 JOIN outfits o ON c.outfit_id = o.id 
                 WHERE c.user_id = ? AND o.owner_id = ?";
$stmtFetch = mysqli_prepare($conn, $sqlFetchCart);
mysqli_stmt_bind_param($stmtFetch, "ii", $userId, $shopId);
mysqli_stmt_execute($stmtFetch);
$resCart = mysqli_stmt_get_result($stmtFetch);
$cartItems = mysqli_fetch_all($resCart, MYSQLI_ASSOC);

if (count($cartItems) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng của Shop này đang trống!']);
    exit;
}

// ========================================
// BẮT ĐẦU DATABASE TRANSACTION
// ========================================
mysqli_begin_transaction($conn);

try {
    $totalAmount = 0;
    $validatedItems = [];

    foreach ($cartItems as $item) {
        $outfitId = intval($item['outfit_id']);
        $sizeName = $item['size_name'];
        $quantity = intval($item['quantity']);

        // Kiểm tra tồn kho
        $stockSql = "SELECT quantity FROM outfit_sizes WHERE outfit_id = ? AND size_name = ? FOR UPDATE";
        $stockStmt = mysqli_prepare($conn, $stockSql);
        mysqli_stmt_bind_param($stockStmt, "is", $outfitId, $sizeName);
        mysqli_stmt_execute($stockStmt);
        $stockRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stockStmt));

        if (!$stockRow || $stockRow['quantity'] < $quantity) {
            throw new Exception("Sản phẩm '{$item['name']}' size $sizeName không đủ tồn kho!");
        }

        $totalAmount += $item['price'] * $quantity;
        $validatedItems[] = [
            'outfit_id' => $outfitId,
            'name' => $item['name'],
            'size_name' => $sizeName,
            'quantity' => $quantity,
            'price' => $item['price']
        ];
    }

    // ========================================
    // BƯỚC 2: TẠO HÓA ĐƠN (Thêm shop_id)
    // ========================================
    $payment_status = 'pending';
    $orderSql = "INSERT INTO orders (user_id, shop_id, fullname, phone, address, note, payment_method, payment_status, total_amount) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $orderStmt = mysqli_prepare($conn, $orderSql);
    mysqli_stmt_bind_param($orderStmt, "iissssssi", $userId, $shopId, $fullname, $phone, $address, $note, $payment_method, $payment_status, $totalAmount);
    mysqli_stmt_execute($orderStmt);
    
    $orderId = mysqli_insert_id($conn);

    // ========================================
    // BƯỚC 3: LƯU CHI TIẾT + TRỪ KHO
    // ========================================
    $detailSql = "INSERT INTO order_details (order_id, outfit_id, size_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $detailStmt = mysqli_prepare($conn, $detailSql);

    $deductSql = "UPDATE outfit_sizes SET quantity = quantity - ? WHERE outfit_id = ? AND size_name = ?";
    $deductStmt = mysqli_prepare($conn, $deductSql);

    foreach ($validatedItems as $item) {
        mysqli_stmt_bind_param($detailStmt, "iisii", $orderId, $item['outfit_id'], $item['size_name'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($detailStmt);

        mysqli_stmt_bind_param($deductStmt, "iis", $item['quantity'], $item['outfit_id'], $item['size_name']);
        mysqli_stmt_execute($deductStmt);
    }

    // ========================================
    // BƯỚC 4: XÓA GIỎ HÀNG (Chỉ xóa các item đã thanh toán)
    // ========================================
    $deleteCartSql = "DELETE FROM shopping_cart WHERE user_id = ? AND outfit_id IN (SELECT id FROM outfits WHERE owner_id = ?)";
    $deleteCartStmt = mysqli_prepare($conn, $deleteCartSql);
    mysqli_stmt_bind_param($deleteCartStmt, "ii", $userId, $shopId);
    mysqli_stmt_execute($deleteCartStmt);

    mysqli_commit($conn);
    
    // ========================================
    // BƯỚC 5: XỬ LÝ THEO TỪNG PHƯƠNG THỨC THANH TOÁN
    // ========================================
    switch ($payment_method) {
        case 'cod':
            echo json_encode([
                'status' => 'success', 
                'message' => 'Đặt hàng thành công! Mã đơn: #' . $orderId,
                'order_id' => $orderId,
                'redirect_url' => 'order_history.php'
            ]);
            break;
            
        case 'vnpay':
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            $_SESSION['order_id'] = $orderId;
            $_SESSION['total_amount'] = $totalAmount;
            require_once 'vnpay_create.php';
            break;
            
        case 'momo':
            echo json_encode(['status' => 'error', 'message' => 'Ví MoMo đang bảo trì!']);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ!']);
            break;
    }

} catch (Exception $e) {
    mysqli_rollback($conn); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>