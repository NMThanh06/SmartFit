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
    $wardrobeData = json_decode(file_get_contents(__DIR__ . '/outfits.json'), true)['items'] ?? [];
    $wardrobeBrief = "";
    foreach ($wardrobeData as $item) {
        if (in_array($input['gender'], $item['gender'])) {
            $sellerNote = !empty($item['seller_note']) ? $item['seller_note'] : 'Không có';
            $itemAge = !empty($item['age']) ? $item['age'] : 'All';
            $wardrobeBrief .= "- ID: {$item['id']} | Loại: {$item['type']} | Tên: {$item['name']} | Age: {$itemAge} | Seller note: {$sellerNote}\n";
        }
    }

    $age = $input['age'] ?? 'Không rõ';
    $location = $input['location'] ?? 'Không rõ';
    $weatherTemp = $input['weather']['temp'] ?? 25;
    $weatherCond = $input['weather']['condition'] ?? '';
    $weatherStr = "$weatherTemp°C, $weatherCond";
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $currentTime = date('Y-m-d H:i:s');

    $prompt = "Bạn là một Stylist AI chuyên nghiệp của SmartFit. Nhiệm vụ của bạn là đọc dữ liệu khách hàng và danh sách trang phục để phối ra 1 bộ đồ hoàn chỉnh.

Dữ liệu đầu vào bạn nhận được:
- Tuổi khách hàng: $age
- Thời gian hiện tại: $currentTime
- Địa điểm khách hàng chọn: $location
- Thời tiết tại địa điểm đó: $weatherStr
- Dịp: " . ($input['occasion'] ?? 'đi học') . "
- Ghi chú từ khách: '" . ($input['note'] ?? '') . "'

Danh sách quần áo:
$wardrobeBrief

Quy tắc phối đồ & Trả kết quả (BẮT BUỘC TUÂN THỦ):
1. Mở đầu (Greeting & Context): Phải chào hỏi và nhắc trực tiếp đến ĐỊA ĐIỂM và THỜI TIẾT khách đã chọn. (Ví dụ: 'Chào bạn, tối nay ở $location trời $weatherStr, rất lý tưởng để dạo phố...').
2. Lọc Tuổi & Logic: Chỉ chọn những món đồ có thuộc tính 'age' bao hàm tuổi thật của khách (hoặc 'All'). Khớp loại đồ với hoàn cảnh/thời tiết.
3. Phân tích Seller Note: Đọc kỹ thuộc tính 'seller_note' của các món được chọn để lấy ý tưởng mix-match.
4. Trình bày: Liệt kê rõ ràng các món đồ được chọn (Áo, Quần, Giày/Phụ kiện). Thêm một đoạn ngắn giải thích tại sao bộ này hợp với độ tuổi, thời tiết ở địa điểm đó và ăn nhập với 'seller note'. Đảm bảo rằng toàn bộ nội dung (Mở đầu + Trình bày) không quá dài, chỉ từ 3-4 câu là vừa.

TRẢ VỀ JSON TUYỆT ĐỐI THEO ĐỊNH DẠNG SAU, KHÔNG KÈM TEXT GIẢI THÍCH Ở NGOÀI JSON:
{\"styleName\": \"Tên style ngắn gọn\", \"caption\": \"Toàn bộ nội dung Mở đầu và Trình bày (gồm 3-4 câu như yêu cầu)\", \"ids\": {\"top\": ID, \"bottom\": ID, \"shoes\": ID, \"acc\": ID}}";

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