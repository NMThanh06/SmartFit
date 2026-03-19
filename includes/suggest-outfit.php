<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    session_start();
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/gemini-config.php';

    // 1. LÀM SẠCH KEY
    $cleanApiKey = trim(GEMINI_API_KEY);

    // 1. Nhận mọi dữ liệu đầu vào (dù là JSON hay Form Data)
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if (!$input) {
        $input = $_POST; // Nếu không phải JSON thì lấy POST bình thường
    }

    // 2. Chặn lại và in ra xem rốt cuộc JS gửi cái gì lên
    if (empty($input)) {
        echo json_encode([
            "success" => false,
            "error" => "JS chưa gửi dữ liệu gì lên cả!",
            "raw_data" => $inputJSON
        ]);
        exit;
    }

    // 3. Lấy các biến cơ bản (Dùng ?? để gán mặc định nếu lỡ bị thiếu)
    $occasion = $input['occasion'] ?? 'đi học';
    $gender = $input['gender'] ?? 'male';
    $style = $input['style'] ?? 'basic';
    $weatherTemp = $input['weather']['temp'] ?? 25;
    $weatherCond = $input['weather']['condition'] ?? 'mild';
    $weatherStr = $weatherTemp . '°C, ' . $weatherCond;
    $age = $input['age'] ?? 'Không rõ';
    $location = $input['location'] ?? 'Không rõ';
    $targetDate = $input['targetDate'] ?? 'Hôm nay';

    // 2. ĐỊNH NGHĨA MODEL VÀ URL (gemini-flash-latest đã chạy thành công)
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $cleanApiKey;

    // 3. ĐỌC WARDROBE TRỰC TIẾP TỪ DATABASE (Mệnh lệnh: Không dùng file JSON)
    $wardrobeData = [];
    $currentUserId = $_SESSION['user_id'] ?? null;
    
    // Lấy đồ Shop (commercial) + Đồ cá nhân của user hiện tại
    $sqlData = "SELECT * FROM outfits WHERE is_commercial = 1";
    if ($currentUserId) {
        $sqlData .= " OR owner_id = " . intval($currentUserId);
    }
    $sqlData .= " ORDER BY id DESC";

    $resData = mysqli_query($conn, $sqlData);
    if (!$resData) {
        throw new Exception("Lỗi truy vấn Database: " . mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($resData)) {
        // Lấy tên màu + ảnh từ bảng outfit_colors (nguồn ảnh chính thức)
        $oid = $row['id'];
        $colorRes = mysqli_query($conn, "SELECT color_name, image FROM outfit_colors WHERE outfit_id = $oid LIMIT 1");
        $colorRow = mysqli_fetch_assoc($colorRes);
        $mainColor = $colorRow['color_name'] ?? 'Mặc định';
        $mainImage = $colorRow['image'] ?? ($row['image'] ?: 'assets/img/default-placeholder.jpg');

        $wardrobeData[] = [
            'id' => (string)$row['id'],
            'type' => $row['type'],
            'name' => $row['name'],
            'gender' => json_decode($row['gender'], true) ?: [],
            'occasion' => json_decode($row['occasion'], true) ?: [],
            'style' => json_decode($row['style'], true) ?: [],
            'color' => $mainColor,
            'fit' => json_decode($row['fit'], true) ?: [],
            'weather' => json_decode($row['weather'], true) ?: [],
            'image' => $mainImage,
            'price' => (int)$row['price'],
            'sizes' => [],
            'age' => $row['age'] ?? 'All',
            'seller_note' => $row['seller_note'] ?? ($row['is_commercial'] ? '' : '(Đồ cá nhân của khách)')
        ];
    }

    $wardrobeBrief = "";

    foreach ($wardrobeData as $item) {
        // Kiểm tra isset trước khi truy xuất
        if (!is_array($item))
            continue;
        if (!isset($item['gender']) || !is_array($item['gender']))
            continue;

        $inputGender = $input['gender'] ?? 'male';
        if (in_array($inputGender, $item['gender'])) {
            $itemId = $item['id'] ?? '??';
            $itemType = $item['type'] ?? '??';
            $itemName = $item['name'] ?? '??';
            $sellerNote = !empty($item['seller_note']) ? $item['seller_note'] : 'Không có';
            $itemAge = !empty($item['age']) ? $item['age'] : 'All';
            $isPersonal = !empty($item['seller_note']) && strpos($item['seller_note'], 'closet') !== false || $item['price'] == 0;
            $wardrobeBrief .= "- ID: {$itemId} | Loại: {$itemType} | Tên: {$itemName} | Age: {$itemAge} | Note: {$sellerNote}" . ($isPersonal ? " (Đồ của khách)" : "") . "\n";
        }
    }

    // 4. CHUẨN BỊ CONTEXT CHO AI PROMPT
    $age = $input['age'] ?? 'Không rõ';
    $location = $input['location'] ?? 'Không rõ';
    $weatherTemp = $input['weather']['temp'] ?? 25;
    $weatherCond = $input['weather']['condition'] ?? '';
    $weatherStr = $weatherTemp . '°C, ' . $weatherCond;
    $targetDate = $input['targetDate'] ?? 'Hôm nay';

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
2. Lọc Logic: Khớp loại đồ với hoàn cảnh, thời tiết và độ tuổi của khách. RẤT QUAN TRỌNG: Hãy dành khoảng 30-40% tỷ lệ chọn loại trang phục liền thân (onepiece như váy liền, đầm, đồ bộ áo dài, jumpsuit) nếu có trong danh sách và phù hợp thời tiết. NẾU CHỌN onepiece (vd: đầm, đồ bộ, áo dài) thì BẮT BUỘC phải bỏ trống (null) áo (top) và quần (bottom). Ngược lại NẾU KHÔNG CHỌN onepiece (tức là chọn áo quần) thì onepiece sẽ là null.
3. Nội dung Giải thích (RẤT QUAN TRỌNG): 
   - TUYỆT ĐỐI KHÔNG liệt kê tên toàn bộ các món đồ đã chọn (ví dụ: không viết 'gợi ý phối áo khoác cùng quần jean...').
   - TUYỆT ĐỐI KHÔNG hiển thị ID của món đồ ra văn bản.
   - CHỈ NHẮC ĐẾN tên của một món đồ cụ thể NẾU món đồ đó thật sự ĐẶC BIỆT (phù hợp với 'Ghi chú từ khách' hoặc có 'Seller note' ấn tượng). 
   - Tập trung giải thích lý do chung vì sao set đồ phong cách này mang lại sự thoải mái, phù hợp với thời tiết và ghi chú của khách.
   - Viết thật ngắn gọn, súc tích, tối đa 2-3 câu để người dùng dễ đọc.
4. LƯU Ý QUAN TRỌNG VỀ DỮ LIỆU: Hệ thống hiện tại lưu trữ màu sắc bằng Tên màu tiếng Việt (cột color_name) như: Đen, Trắng, Xanh rêu, Xanh dương... Tuyệt đối KHÔNG dùng mã màu HEX. Khi người dùng yêu cầu màu sắc (ví dụ: \"thích màu sáng\", \"màu tối\", \"áo đỏ\"), bạn hãy tự động suy luận ngữ nghĩa và trả về các món đồ có color_name khớp hoặc tương đương nhất.

TRẢ VỀ JSON TUYỆT ĐỐI THEO ĐỊNH DẠNG SAU, KHÔNG KÈM TEXT GIẢI THÍCH Ở NGOÀI JSON:
{\"styleName\": \"Tên style ngắn gọn\", \"caption\": \"Toàn bộ nội dung Mở đầu và Trình bày (gồm 2-3 câu như yêu cầu)\", \"ids\": {\"top\": ID hoặc null, \"bottom\": ID hoặc null, \"onepiece\": ID hoặc null, \"shoes\": ID, \"acc\": ID hoặc null}}";

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

    if (!$cleanJson)
        throw new Exception("AI không trả về JSON hợp lệ");

    // 7. Lấy ĐẦY ĐỦ các món đồ từ Database (wardrobeData đã load từ DB)
    function findItem($id, $list)
    {
        if (!$id)
            return null;
        foreach ($list as $i) {
            if (!is_array($i))
                continue;
            if (isset($i['id']) && $i['id'] == $id)
                return $i;
        }
        return null;
    }

    $top = findItem($cleanJson['ids']['top'] ?? null, $wardrobeData);
    $bottom = findItem($cleanJson['ids']['bottom'] ?? null, $wardrobeData);
    $onepiece = findItem($cleanJson['ids']['onepiece'] ?? null, $wardrobeData);
    $shoes = findItem($cleanJson['ids']['shoes'] ?? null, $wardrobeData);
    $acc = findItem($cleanJson['ids']['acc'] ?? null, $wardrobeData);

    // 8. Thiết lập ảnh mặc định dựa trên giới tính (lấy từ JSON input, không phải $_POST)
    $gender = $input['gender'] ?? 'male';

    if ($gender === 'female') {
        $defaultTop = './assets/img/female-default-top.jpg';
        $defaultBottom = './assets/img/female-default-bottom.jpg';
    }
    else {
        $defaultTop = './assets/img/default-top.jpg';
        $defaultBottom = './assets/img/default-bottom.jpg';
    }
    // 9. Kiểm tra xem bộ đồ này đã được người dùng lưu chưa
    $isSaved = false;
    $topId = isset($top['id']) ? (int)$top['id'] : null;
    $bottomId = isset($bottom['id']) ? (int)$bottom['id'] : null;
    $onepieceId = isset($onepiece['id']) ? (int)$onepiece['id'] : null;
    $shoesId = isset($shoes['id']) ? (int)$shoes['id'] : null;
    $accId = isset($acc['id']) ? (int)$acc['id'] : null;

    if (isset($_SESSION['user_id']) && (($topId && $bottomId) || $onepieceId) && $shoesId) {
        $userId = $_SESSION['user_id'];

        // Logic so sánh chính xác kể cả trường hợp null
        $checkSql = "SELECT COUNT(*) as cnt FROM saved_outfits 
                     WHERE user_id = ? 
                     AND (top_id = ? OR (top_id IS NULL AND ? IS NULL)) 
                     AND (bottom_id = ? OR (bottom_id IS NULL AND ? IS NULL))
                     AND (onepiece_id = ? OR (onepiece_id IS NULL AND ? IS NULL))
                     AND shoes_id = ? 
                     AND (acc_id = ? OR (acc_id IS NULL AND ? IS NULL))";

        $checkStmt = mysqli_prepare($conn, $checkSql);
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, "iiiiiiiiii", $userId, $topId, $topId, $bottomId, $bottomId, $onepieceId, $onepieceId, $shoesId, $accId, $accId);
            mysqli_stmt_execute($checkStmt);
            $checkRes = mysqli_stmt_get_result($checkStmt);
            $checkData = mysqli_fetch_assoc($checkRes);
            $isSaved = ($checkData['cnt'] > 0);
            mysqli_stmt_close($checkStmt);
        }
    }

    // 10. Trả về JSON với các biến mặc định đã được phân loại
    echo json_encode([
        'success' => true,
        'data' => [
            // lấy ID
            'topId' => $top['id'] ?? null,
            'bottomId' => $bottom['id'] ?? null,
            'onepieceId' => $onepiece['id'] ?? null,
            'shoesId' => $shoes['id'] ?? null,
            'accId' => $acc['id'] ?? null,
            // trạng thái đã lưu
            'isSaved' => $isSaved,
            // trả về giao diện gợi ý
            'top' => $top['name'] ?? 'Chưa xác định',
            'topImage' => $top['image'] ?? $defaultTop,
            'bottom' => $bottom['name'] ?? 'Chưa xác định',
            'bottomImage' => $bottom['image'] ?? $defaultBottom,
            'onepiece' => $onepiece['name'] ?? null,
            'onepieceImage' => $onepiece['image'] ?? null,
            'shoes' => $shoes['name'] ?? 'Chưa xác định',
            'shoesImage' => $shoes['image'] ?? '',
            'accessories' => $acc['name'] ?? 'Không có',
            'accImage' => $acc['image'] ?? '',
            'style' => $cleanJson['styleName'] ?? 'Basic',
            'explanation' => $cleanJson['caption'] ?? 'Set đồ thoải mái, phù hợp thời tiết.'
        ]
    ]);

}
catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>