<?php
// === Chatbot AI Backend - SmartFit ===
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

// Session: chống lỗi nếu đã start rồi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/gemini-config.php';

try {
    // 1. Nhận dữ liệu từ frontend
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if (!is_array($input) || empty($input['message'])) {
        throw new Exception("Tin nhắn không hợp lệ.");
    }

    $userMessage = trim($input['message']);
    $weather = $input['weather'] ?? 'Không rõ';
    $location = $input['location'] ?? 'Không rõ';

    // 2. Thu thập Context từ Database (có bảo vệ lỗi)
    $profileContext = "[Profile: Trống]";
    $historyContext = "[History: Trống]";

    $userId = $_SESSION['user_id'] ?? null;

    if ($userId) {
        // --- [Ưu tiên 1] Profile: Thử lấy age, gender từ bảng users ---
        try {
            $stmtProfile = @mysqli_prepare($conn, "SELECT age, gender FROM users WHERE id = ?");
            if ($stmtProfile) {
                mysqli_stmt_bind_param($stmtProfile, "i", $userId);
                mysqli_stmt_execute($stmtProfile);
                $profileResult = mysqli_stmt_get_result($stmtProfile);
                $profile = mysqli_fetch_assoc($profileResult);
                mysqli_stmt_close($stmtProfile);

                if ($profile && (!empty($profile['age']) || !empty($profile['gender']))) {
                    $pAge = $profile['age'] ?? 'Chưa rõ';
                    $pGender = $profile['gender'] ?? 'Chưa rõ';
                    $profileContext = "[Profile: Tuổi=$pAge, Giới tính=$pGender]";
                }
            }
        } catch (Exception $e) {
            // Bảng users không có cột age/gender → bỏ qua
        }

        // --- [Ưu tiên 2] History: Thử lấy từ outfit_requests ---
        if ($profileContext === "[Profile: Trống]") {
            try {
                $stmtHistory = @mysqli_prepare($conn,
                    "SELECT occasion, gender, style, color, fit, note FROM outfit_requests WHERE session_id = ? ORDER BY created_at DESC LIMIT 1"
                );
                if ($stmtHistory) {
                    $sessionId = session_id();
                    mysqli_stmt_bind_param($stmtHistory, "s", $sessionId);
                    mysqli_stmt_execute($stmtHistory);
                    $historyResult = mysqli_stmt_get_result($stmtHistory);
                    $history = mysqli_fetch_assoc($historyResult);
                    mysqli_stmt_close($stmtHistory);

                    if ($history) {
                        $hGender = $history['gender'] ?? '?';
                        $hOccasion = $history['occasion'] ?? '?';
                        $hStyle = $history['style'] ?? '?';
                        $hColor = $history['color'] ?? '?';
                        $hFit = $history['fit'] ?? '?';
                        $hNote = $history['note'] ?? '';
                        $historyContext = "[History: Giới tính=$hGender, Dịp=$hOccasion, Phong cách=$hStyle, Màu=$hColor, Fit=$hFit, Ghi chú='$hNote']";
                    }
                }
            } catch (Exception $e) {
                // Bảng outfit_requests chưa tồn tại → bỏ qua
            }
        }
    }

    // --- [Môi trường] ---
    $envContext = "[Môi trường: Thời tiết=$weather, Địa điểm=$location]";

    // 3. Xây dựng chuỗi Context hoàn chỉnh
    $dataContext = "--- [Bối cảnh Dữ liệu] ---\n$profileContext\n$historyContext\n$envContext\n--- [Kết thúc Bối cảnh] ---";

    // 4. System Prompt
    $systemPrompt = "Bạn là Trợ lý ảo thời trang SmartFit, một người tư vấn thân thiện, lịch sự, chuyên nghiệp và có gu thẩm mỹ cao.

Quy tắc lấy thông tin (BẮT BUỘC TUÂN THỦ):
Khi tư vấn phối đồ, bạn phải đọc phần [Bối cảnh Dữ liệu] được cung cấp kèm theo tin nhắn.

BƯỚC 1: Ưu tiên sử dụng thông tin ở phần [Profile].

BƯỚC 2: Nếu [Profile] báo Trống, hãy dùng thông tin ở phần [History]. Nếu có đủ thông tin từ [History], hãy gợi ý phối đồ luôn và nói rõ là 'Dựa trên lần phối đồ gần nhất của bạn, mình gợi ý...' thay vì hỏi thêm.

BƯỚC 3: Nếu cả hai đều Trống, hoặc thiếu các thông tin cốt lõi (như Giới tính, Độ tuổi, Phong cách muốn hướng tới), bạn BẮT BUỘC phải đặt câu hỏi trực tiếp, lịch sự để yêu cầu người dùng cung cấp thêm trước khi đưa ra gợi ý. (Ví dụ: 'Dạ để SmartFit tư vấn chuẩn nhất cho mình, bạn có thể cho mình biết bạn đang tìm đồ cho nam hay nữ và khoảng độ tuổi của bạn được không ạ?').

Quy tắc giao tiếp & Xử lý ngoại lệ:

Luôn xưng hô lịch sự (Dạ/Vâng/Mình/Bạn).

Nếu người dùng hỏi các kiến thức ngoài lề (không liên quan đến thời trang, thời tiết, hệ thống SmartFit), hãy trả lời thật lịch sự, ngắn gọn và khéo léo dẫn dắt họ quay lại chủ đề thời trang. (Ví dụ: 'Dạ, câu hỏi của bạn thú vị quá! Tuy nhiên hiện tại chuyên môn chính của mình là tư vấn phối đồ và thời trang tại SmartFit. Bạn có muốn mình gợi ý một bộ trang phục cho ngày hôm nay không ạ?').

Không bao giờ được cáu gắt, dùng từ ngữ thô tục hay tranh cãi với người dùng.

Khi gợi ý phối đồ, hãy trình bày đẹp và rõ ràng: liệt kê từng món đồ (Áo, Quần, Giày, Phụ kiện) trên mỗi dòng riêng biệt, kèm mô tả ngắn gọn. Sau đó giải thích lý do phối đồ trong 1-2 câu.

Trả lời bằng văn bản thuần túy, KHÔNG sử dụng Markdown (không dùng **, ##, -, * hay bất kỳ ký tự định dạng nào). Viết ngắn gọn, dễ đọc trên khung chat nhỏ.";

    // 5. Quản lý lịch sử hội thoại (Multi-turn) trong Session
    if (!isset($_SESSION['chat_history'])) {
        $_SESSION['chat_history'] = [];
    }

    // Thêm tin nhắn người dùng vào lịch sử
    $_SESSION['chat_history'][] = [
        "role" => "user",
        "parts" => [["text" => $dataContext . "\n\nTin nhắn người dùng: " . $userMessage]]
    ];

    // Giới hạn lịch sử: giữ tối đa 20 lượt (10 cặp user-model)
    if (count($_SESSION['chat_history']) > 20) {
        $_SESSION['chat_history'] = array_slice($_SESSION['chat_history'], -20);
    }

    // 6. Gọi Gemini API (giống suggest-outfit.php)
    $cleanApiKey = trim(GEMINI_API_KEY);
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $cleanApiKey;

    $requestBody = [
        "system_instruction" => [
            "parts" => [["text" => $systemPrompt]]
        ],
        "contents" => $_SESSION['chat_history']
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
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
        $errMsg = 'Lỗi không xác định';
        if (is_array($errObj) && isset($errObj['error']['message'])) {
            $errMsg = $errObj['error']['message'];
        }
        throw new Exception("AI lỗi ($httpCode): $errMsg");
    }

    // 7. Parse kết quả
    $aiResult = json_decode($response, true);

    if (!is_array($aiResult) || !isset($aiResult['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception("AI không trả về dữ liệu hợp lệ.");
    }

    $aiReply = $aiResult['candidates'][0]['content']['parts'][0]['text'];

    // Lưu câu trả lời AI vào lịch sử
    $_SESSION['chat_history'][] = [
        "role" => "model",
        "parts" => [["text" => $aiReply]]
    ];

    // 8. Trả về JSON cho Frontend
    echo json_encode([
        'success' => true,
        'reply' => $aiReply
    ]);

} catch (Exception $e) {
    // Trả lỗi nhưng KHÔNG set http 500 để frontend xử lý được
    echo json_encode([
        'success' => false,
        'reply' => 'Xin lỗi bạn, có lỗi xảy ra: ' . $e->getMessage() . '. Vui lòng thử lại sau nhé! 🙏'
    ]);
}
?>
