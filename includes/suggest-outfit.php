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
            $pairingsStr = !empty($item['pairings']) ? implode(', ', $item['pairings']) : 'Không có';
            $wardrobeBrief .= "- ID: {$item['id']} | Loại: {$item['type']} | Tên: {$item['name']} | Gợi ý phối cùng: {$pairingsStr}\n";
        }
    }

    $prompt = "Bạn là một chuyên gia thời trang (Stylist). Dựa vào danh sách sản phẩm và các món được gợi ý phối chung (pairings) mà tôi cung cấp, hãy tạo ra một bộ trang phục hoàn hảo.

Thông tin khách hàng: Giới tính " . $input['gender'] . ", Dịp " . ($input['occasion'] ?? 'đi học') . ", Nhiệt độ " . ($input['weather']['temp'] ?? 25) . "°C. Ghi chú: '" . ($input['note'] ?? '') . "'.

Kho đồ (chỉ chọn ID từ đây):
$wardrobeBrief
YÊU CẦU BẮT BUỘC:

Chọn 1 áo (top), 1 quần (bottom), 1 giày (shoes), 1 phụ kiện (acc - nếu có, không thì null).

Giải thích: CHỈ VIẾT ĐÚNG 2 CÂU NGẮN GỌN lý do chọn set đồ này. (Rất quan trọng).
TRẢ VỀ JSON TUYỆT ĐỐI THEO ĐỊNH DẠNG SAU, KHÔNG KÈM TEXT GIẢI THÍCH:
{\"styleName\": \"Tên style\", \"caption\": \"2 câu giải thích\", \"ids\": {\"top\": ID, \"bottom\": ID, \"shoes\": ID, \"acc\": ID}}";

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

    $gender = $_POST['gender'] ?? 'male'; 

// Thiết lập ảnh mặc định dựa trên giới tính
if ($gender === 'female') {
    $defaultTop = './assets/img/female-default-top.jpg';
    $defaultBottom = './assets/img/female-default-bottom.jpg';
} else {
    // Giữ nguyên trang phục mặc định cho nam như cũ
    $defaultTop = './assets/img/default-top.jpg';
    $defaultBottom = './assets/img/default-bottom.jpg';
}

// Trả về JSON với các biến mặc định đã được phân loại
echo json_encode([
    'success' => true,
    'data' => [
        // lấy ID
        'topId' => $top['id'] ?? null,
        'bottomId' => $bottom['id'] ?? null,
        'shoesId' => $shoes['id'] ?? null,
        'accId' => $acc['id'] ?? null,
        // trả về dao diện gợi ý
        'top' => $top['name'] ?? 'Chưa xác định',
        'topImage' => $top['image'] ?? $defaultTop,
        'bottom' => $bottom['name'] ?? 'Chưa xác định',
        'bottomImage' => $bottom['image'] ?? $defaultBottom,
        'shoes' => $shoes['name'] ?? 'Chưa xác định',
        'shoesImage' => $shoes['image'] ?? '',
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