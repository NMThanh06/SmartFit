<?php
// === Tắt hiển thị lỗi HTML rác, chỉ log lỗi vào server ===
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/gemini-config.php';

    // 1. LÀM SẠCH KEY
    $cleanApiKey = trim(GEMINI_API_KEY);

    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if (!is_array($input)) {
        throw new Exception("Dữ liệu đầu vào không hợp lệ.");
    }

    // 2. ĐỊNH NGHĨA MODEL VÀ URL (gemini-flash-latest đã chạy thành công)
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $cleanApiKey;

    // 3. ĐỌC WARDROBE TỪ outfits.json
    $rawJson = file_get_contents(__DIR__ . '/outfits.json');
    $decodedJson = json_decode($rawJson, true);

    if (!is_array($decodedJson) || !isset($decodedJson['items'])) {
        throw new Exception("Không đọc được file outfits.json hoặc thiếu key 'items'.");
    }

    $wardrobeData = $decodedJson['items'];
    $wardrobeBrief = "";

    foreach ($wardrobeData as $item) {
        // Kiểm tra isset trước khi truy xuất
        if (!is_array($item)) continue;
        if (!isset($item['gender']) || !is_array($item['gender'])) continue;

        $inputGender = $input['gender'] ?? 'male';
        if (in_array($inputGender, $item['gender'])) {
            $itemId     = $item['id'] ?? '??';
            $itemType   = $item['type'] ?? '??';
            $itemName   = $item['name'] ?? '??';
            $sellerNote = !empty($item['seller_note']) ? $item['seller_note'] : 'Không có';
            $itemAge    = !empty($item['age']) ? $item['age'] : 'All';
            $wardrobeBrief .= "- ID: {$itemId} | Loại: {$itemType} | Tên: {$itemName} | Age: {$itemAge} | Seller note: {$sellerNote}\n";
        }
    }

    // 4. CHUẨN BỊ CONTEXT CHO AI PROMPT
    $age          = $input['age'] ?? 'Không rõ';
    $location     = $input['location'] ?? 'Không rõ';
    $weatherTemp  = $input['weather']['temp'] ?? 25;
    $weatherCond  = $input['weather']['condition'] ?? '';
    $weatherStr   = $weatherTemp . '°C, ' . $weatherCond;
    $targetDate   = $input['targetDate'] ?? 'Hôm nay';

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $currentTime = date('Y-m-d H:i:s');

    $prompt = "Bạn là một Stylist AI chuyên nghiệp của SmartFit. Nhiệm vụ của bạn là đọc dữ liệu khách hàng và danh sách trang phục để phối ra 1 bộ đồ hoàn chỉnh.

Dữ liệu đầu vào bạn nhận được:
- Tuổi khách hàng: $age
- Thời gian hiện tại: $currentTime
- Ngày khách hàng cần mặc đồ: $targetDate
- Địa điểm khách hàng chọn: $location
- Thời tiết tại địa điểm đó (vào ngày đã chọn): $weatherStr
- Dịp: " . ($input['occasion'] ?? 'đi học') . "
- Ghi chú từ khách: '" . ($input['note'] ?? '') . "'

Danh sách quần áo:
$wardrobeBrief

Quy tắc phối đồ & Trả kết quả (BẮT BUỘC TUÂN THỦ):
1. Mở đầu (Greeting & Context): Chào hỏi thân thiện, nhắc ngắn gọn đến ĐỊA ĐIỂM, THỜI TIẾT và NGÀY đã chọn. (Ví dụ: 'Vào $targetDate ở $location trời $weatherStr, rất lý tưởng để dạo phố...').
2. Lọc Logic: Khớp loại đồ với hoàn cảnh, thời tiết và độ tuổi của khách.
3. Nội dung Giải thích (RẤT QUAN TRỌNG): 
   - TUYỆT ĐỐI KHÔNG liệt kê tên toàn bộ các món đồ đã chọn (ví dụ: không viết 'gợi ý phối áo khoác cùng quần jean...').
   - TUYỆT ĐỐI KHÔNG hiển thị ID của món đồ ra văn bản.
   - CHỈ NHẮC ĐẾN tên của một món đồ cụ thể NẾU món đồ đó thật sự ĐẶC BIỆT (phù hợp với 'Ghi chú từ khách' hoặc có 'Seller note' ấn tượng). 
   - Tập trung giải thích lý do chung vì sao set đồ phong cách này mang lại sự thoải mái, phù hợp với thời tiết và ghi chú của khách.
   - Viết thật ngắn gọn, súc tích, tối đa 2-3 câu để người dùng dễ đọc.

TRẢ VỀ JSON TUYỆT ĐỐI THEO ĐỊNH DẠNG SAU, KHÔNG KÈM TEXT GIẢI THÍCH Ở NGOÀI JSON:
{\"styleName\": \"Tên style ngắn gọn\", \"caption\": \"Toàn bộ nội dung Mở đầu và Trình bày (gồm 2-3 câu như yêu cầu)\", \"ids\": {\"top\": ID, \"bottom\": ID, \"shoes\": ID, \"acc\": ID}}";

    $data = ["contents" => [["parts" => [["text" => $prompt]]]]];

    // 5. CẤU HÌNH cURL GỌI GEMINI API
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Lỗi kết nối tới AI: " . $curlError);
    }

    if ($httpCode !== 200) {
        $errObj = json_decode($response, true);
        $errMsg = 'Unknown error';
        if (is_array($errObj) && isset($errObj['error']['message'])) {
            $errMsg = $errObj['error']['message'];
        }
        throw new Exception("Google API Lỗi ($httpCode): $errMsg. Raw: $response");
    }

    // 6. PARSE KẾT QUẢ AI
    $aiResult = json_decode($response, true);

    if (!is_array($aiResult) || !isset($aiResult['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception("AI không trả về dữ liệu hợp lệ (Không có candidates). Raw: " . $response);
    }

    $rawText = $aiResult['candidates'][0]['content']['parts'][0]['text'];
    
    preg_match('/\{.*\}/s', $rawText, $matches);
    $cleanJson = json_decode($matches[0] ?? '', true);

    if (!$cleanJson) throw new Exception("AI không trả về JSON hợp lệ");

    // 7. Lấy ĐẦY ĐỦ 4 món đồ từ file outfits.json
    function findItem($id, $list) {
        if (!$id) return null;
        foreach ($list as $i) {
            if (!is_array($i)) continue;
            if (isset($i['id']) && $i['id'] == $id) return $i;
        }
        return null;
    }

    $top    = findItem($cleanJson['ids']['top'] ?? null, $wardrobeData);
    $bottom = findItem($cleanJson['ids']['bottom'] ?? null, $wardrobeData);
    $shoes  = findItem($cleanJson['ids']['shoes'] ?? null, $wardrobeData);
    $acc    = findItem($cleanJson['ids']['acc'] ?? null, $wardrobeData);

    // 8. Thiết lập ảnh mặc định dựa trên giới tính (lấy từ JSON input, không phải $_POST)
    $gender = $input['gender'] ?? 'male';

    if ($gender === 'female') {
        $defaultTop    = './assets/img/female-default-top.jpg';
        $defaultBottom = './assets/img/female-default-bottom.jpg';
    } else {
        $defaultTop    = './assets/img/default-top.jpg';
        $defaultBottom = './assets/img/default-bottom.jpg';
    }

    // 9. Trả về JSON với các biến mặc định đã được phân loại
    echo json_encode([
        'success' => true,
        'data' => [
            // lấy ID
            'topId'       => $top['id'] ?? null,
            'bottomId'    => $bottom['id'] ?? null,
            'shoesId'     => $shoes['id'] ?? null,
            'accId'       => $acc['id'] ?? null,
            // trả về giao diện gợi ý
            'top'         => $top['name'] ?? 'Chưa xác định',
            'topImage'    => $top['image'] ?? $defaultTop,
            'bottom'      => $bottom['name'] ?? 'Chưa xác định',
            'bottomImage' => $bottom['image'] ?? $defaultBottom,
            'shoes'       => $shoes['name'] ?? 'Chưa xác định',
            'shoesImage'  => $shoes['image'] ?? '',
            'accessories' => $acc['name'] ?? 'Không có',
            'accImage'    => $acc['image'] ?? '',
            'style'       => $cleanJson['styleName'] ?? 'Basic',
            'explanation' => $cleanJson['caption'] ?? 'Set đồ thoải mái, phù hợp thời tiết.'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>