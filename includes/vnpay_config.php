<?php
// Cấu hình mã merchant và secret key từ VNPAY (Sandbox)
$vnp_TmnCode = "6Q5T869F"; // Mã website tại VNPAY
$vnp_HashSecret = "J3L95KZGEAF8653INVTJZ8S6Y5BLL493"; // Chuỗi bí mật
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; // URL thanh toán sandbox

// URL hiển thị cho người dùng sau khi thanh toán trên cổng VNPAY thành công/thất bại
// Tự động nhận diện Domain/Port
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$vnp_Returnurl = $protocol . $host . "/vnpay_return.php"; 

// API để query (dành cho tính năng truy vấn đơn hàng hoặc dùng IPN sau này, tùy chọn)
$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
?>
