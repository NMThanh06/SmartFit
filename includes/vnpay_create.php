<?php
// File này sẽ được require_once từ process_checkout.php
require_once 'vnpay_config.php';

// Các dữ liệu này đã được set trong $_SESSION từ process_checkout.php
$vnp_TxnRef = $_SESSION['order_id']; // Mã đơn hàng trong database
$vnp_OrderInfo = "Thanh toan don hang #$vnp_TxnRef tai SmartFit Shop";
$vnp_OrderType = 'billpayment';
$vnp_Amount = $_SESSION['total_amount'] * 100; // Số tiền phải nhân với 100 theo chuẩn VNPAY
$vnp_Locale = 'vn';
$vnp_BankCode = ''; // Để rỗng để khách hàng chọn ngân hàng tại cổng VNPAY
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
// vnp_CreateDate phải là Time zone GMT+7
date_default_timezone_set('Asia/Ho_Chi_Minh'); 
$vnp_CreateDate = date('YmdHis');
$vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes')); // Hết hạn 15 phút

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => $vnp_CreateDate,
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_ExpireDate" => $vnp_ExpireDate
);

if (isset($vnp_BankCode) && $vnp_BankCode != "") {
    $inputData['vnp_BankCode'] = $vnp_BankCode;
}

// Sắp xếp dữ liệu theo thứ tự A-Z trước khi tạo hash
ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";

foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = $vnp_Url . "?" . $query;

// Tạo chữ ký (hash) bằng dữ liệu đã sắp xếp và Secret Key
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// Output đường dẫn redirect dạng JSON để fetch() bên Javascript bên frontend xử lý thành công
echo json_encode([
    'status' => 'success',
    'message' => 'Đang chuyển hướng sang cổng thanh toán VNPAY...',
    'order_id' => $vnp_TxnRef,
    'redirect_url' => $vnp_Url
]);
exit;
?>
