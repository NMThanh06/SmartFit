<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/gemini-config.php';

    // 1. LÀM SẠCH KEY
    $cleanApiKey = trim(GEMINI_API_KEY);

    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    
    // 2. ĐỊNH NGHĨA MODEL VÀ URL
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . trim(GEMINI_API_KEY);    $wardrobeData = json_decode(file_get_contents(__DIR__ . '/outfits.json'), true)['items'] ?? [];
    $wardrobeBrief = "";
    foreach ($wardrobeData as $item) {
        if (in_array($input['gender'], $item['gender'])) {
            $wardrobeBrief .= "- ID: {$item['id']} | Loại: {$item['type']} | Tên: {$item['name']}\n";
        }
    }

    $prompt = "Bạn là Stylist. Phối đồ: Giới tính " . $input['gender'] . ", Dịp " . ($input['occasion'] ?? 'đi học') . ", " . ($input['weather']['temp'] ?? 25) . "°C. Ghi chú: '" . ($input['note'] ?? '') . "'.
    Kho đồ (chỉ chọn ID từ đây):
    $wardrobeBrief
    YÊU CẦU:
    - Chọn 1 áo (top), 1 quần (bottom), 1 giày (shoes), 1 phụ kiện (acc - nếu có, không thì null).
    - Giải thích: CHỈ VIẾT ĐÚNG 2 CÂU NGẮN GỌN lý do chọn set đồ này. (Rất quan trọng).
    TRẢ VỀ JSON: {\"styleName\": \"Tên style\", \"caption\": \"2 câu giải thích\", \"ids\": {\"top\": ID, \"bottom\": ID, \"shoes\": ID, \"acc\": ID}}";

    $data = ["contents" => [["parts" => [["text" => $prompt]]]]];

    // Cấu hình cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Đặt timeout
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        $errObj = json_decode($response, true);
        throw new Exception("Google API Lỗi ($httpCode): " . ($errObj['error']['message'] ?? 'Unknown error'));
    }

    $aiResult = json_decode($response, true);
    $rawText = $aiResult['candidates'][0]['content']['parts'][0]['text'] ?? '';
    
    preg_match('/\{.*\}/s', $rawText, $matches);
    $cleanJson = json_decode($matches[0] ?? '', true);

    if (!$cleanJson) throw new Exception("AI không trả về JSON hợp lệ");

    // Lấy ĐẦY ĐỦ 4 món đồ từ file outfits.json
    function findItem($id, $list) {
        if (!$id) return null;
        foreach ($list as $i) if ($i['id'] == $id) return $i;
        return null;
    }

    $top = findItem($cleanJson['ids']['top'] ?? null, $wardrobeData);
    $bottom = findItem($cleanJson['ids']['bottom'] ?? null, $wardrobeData);
    $shoes = findItem($cleanJson['ids']['shoes'] ?? null, $wardrobeData);
    $acc = findItem($cleanJson['ids']['acc'] ?? null, $wardrobeData);

    // Trả về JSON với đầy đủ các key
    echo json_encode([
        'success' => true,
        'data' => [
            'top' => $top['name'] ?? 'Chưa xác định',
            'topImage' => $top['image'] ?? './assets/img/default-top.jpg',
            'bottom' => $bottom['name'] ?? 'Chưa xác định',
            'bottomImage' => $bottom['image'] ?? './assets/img/default-bottom.jpg',
            'shoes' => $shoes['name'] ?? 'Chưa xác định',
            'shoesImage' => $shoes['image'] ?? './assets/img/default-shoes.jpg',
            'accessories' => $acc['name'] ?? 'Không có',
            'accImage' => $acc['image'] ?? '',
            'style' => $cleanJson['styleName'] ?? 'Basic',
            'explanation' => $cleanJson['caption'] ?? 'Set đồ thoải mái, phù hợp thời tiết.'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>