<?php
// Tự động tính toán thư mục gốc (Xử lý vụ /SmartFit/ trên XAMPP hoặc / trên Domain)
$script_name = $_SERVER['SCRIPT_NAME'];
$baseUrl = (strpos($script_name, '/SmartFit/') !== false) ? '/SmartFit/' : '/';

/**
 * Hàm lấy URL ảnh chính xác (Tương thích XAMPP & Domain)
 * Mệnh lệnh: Không đổi CSS/JS, chỉ chuẩn hóa IMG.
 */
function getImageUrl($path) {
    global $baseUrl;
    if (empty($path)) return $baseUrl . 'assets/img/default-placeholder.jpg';
    if (strpos($path, 'http') === 0) return $path;
    
    // Đảm bảo không bị lặp /SmartFit/SmartFit
    $cleanPath = ltrim($path, '/');
    if (strpos($cleanPath, 'SmartFit/') === 0) {
        $cleanPath = substr($cleanPath, 9); // 'SmartFit/' is 9 chars
    }

    return $baseUrl . $cleanPath;
}
?>
