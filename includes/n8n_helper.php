<?php
/**
 * n8n Webhook Helper — SmartFit
 * Hàm gửi dữ liệu tới n8n Webhook, tự nhận diện môi trường,
 * có log lỗi chi tiết và chống treo trang.
 */

/**
 * Lấy Base URL của n8n dựa trên môi trường hiện tại.
 * - XAMPP (localhost): http://localhost:5678
 * - Docker (VPS):     http://host.docker.internal:5678 hoặc đọc từ .env
 */
function getN8nBaseUrl() {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Nếu đang chạy trên localhost (XAMPP)
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        return 'http://localhost:5678';
    }

    // Nếu trên VPS/Docker: thử đọc từ .env trước
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, 'N8N_URL=') === 0) {
                return rtrim(substr($line, 8), '/');
            }
        }
    }

    // Fallback cho Docker: dùng host.docker.internal
    return 'http://host.docker.internal:5678';
}

/**
 * Gửi dữ liệu tới n8n Webhook.
 *
 * @param string $webhookPath  Đường dẫn webhook (ví dụ: '/webhook/order-email')
 * @param array  $payload      Mảng dữ liệu cần gửi
 * @return array ['success' => bool, 'http_code' => int, 'response' => string, 'error' => string]
 */
function sendDataToN8n($webhookPath, $payload) {
    $baseUrl = getN8nBaseUrl();
    $fullUrl = $baseUrl . $webhookPath;

    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    $ch = curl_init($fullUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $jsonPayload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonPayload)
        ],
        CURLOPT_TIMEOUT        => 5,   // Timeout 5s — không treo trang
        CURLOPT_CONNECTTIMEOUT => 3,   // Chờ kết nối tối đa 3s
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    curl_close($ch);

    // Kết quả trả về
    $result = [
        'success'   => ($curlErrno === 0 && $httpCode >= 200 && $httpCode < 300),
        'http_code' => $httpCode,
        'response'  => $response,
        'error'     => $curlError
    ];

    // Ghi log nếu có lỗi
    if (!$result['success']) {
        $logFile = __DIR__ . '/../n8n_error.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] URL: $fullUrl | HTTP: $httpCode | cURL Error($curlErrno): $curlError | Payload: $jsonPayload\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    return $result;
}
?>
