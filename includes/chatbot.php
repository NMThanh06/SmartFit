<style>
/* Chatbot Container */
#smartfit-chatbot {
    position: fixed;
    bottom: -600px; /* Hidden initially */
    right: 35px;
    width: 380px;
    height: 550px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    z-index: 9999;
    overflow: hidden;
    transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    font-family: inherit;
}

#smartfit-chatbot.active {
    bottom: 25px;
}

/* Chatbot Resize States */
#smartfit-chatbot.enlarged {
    width: 460px;
    height: 650px;
}

#smartfit-chatbot.maximized {
    width: 550px;
    height: 80vh;
    max-height: 800px;
}

/* Chatbot Header */
.chatbot-header {
    background: #003366; /* HUTECH dark blue */
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header__title {
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chatbot-header__actions {
    display: flex;
    gap: 15px;
}

.chatbot-header__icon {
    color: white;
    cursor: pointer;
    font-size: 1.1rem;
    transition: opacity 0.2s;
}

.chatbot-header__icon:hover {
    opacity: 0.8;
}

/* Header Contact Links */
.contact-header-link {
    color: white;
    font-size: 1.15rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, color 0.2s, text-shadow 0.2s;
    text-decoration: none;
    margin-right: 4px;
}

.contact-header-link:hover {
    transform: scale(1.2);
    color: #f8f9fa;
    text-shadow: 0 0 8px rgba(255,255,255,0.6);
}

/* Resize Buttons */
.chatbot-header__resize {
    display: flex;
    gap: 6px;
    margin-right: 4px;
}

.chatbot-header__resize-btn {
    color: white;
    cursor: pointer;
    font-size: 1.25rem;
    opacity: 0.85;
    transition: opacity 0.2s, transform 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    padding: 0;
}

.chatbot-header__resize-btn:hover {
    opacity: 1;
    transform: scale(1.15);
}

/* Chatbot Body */
.chatbot-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

/* Messages */
.chat-msg {
    max-width: 85%;
    font-size: 1.15rem;
    line-height: 1.6;
    animation: fadeInMsg 0.3s ease forwards;
}

.chat-msg--ai {
    align-self: flex-start;
}

.chat-msg--user {
    align-self: flex-end;
    background: #003366;
    color: white;
    padding: 10px 15px;
    border-radius: 18px 18px 0 18px;
}

.chat-bubble {
    background: white;
    padding: 12px 16px;
    border-radius: 18px 18px 18px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    color: #333;
}

/* Weather Card */
.weather-card {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 16px;
    padding: 15px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(0, 242, 254, 0.3);
}

.weather-card__icon {
    font-size: 2.5rem;
}

.weather-card__icon img {
    width: 50px;
    height: 50px;
}

.weather-card__info {
    display: flex;
    flex-direction: column;
}

.weather-card__temp {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}

.weather-card__loc {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-bottom: 5px;
}

.weather-card__desc {
    font-size: 0.9rem;
    font-weight: 500;
}

/* Suggestion Chips */
.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 5px;
}

.chip {
    background: white;
    border: 1px solid #e0e0e0;
    color: #003366;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}

.chip:hover {
    background: #f0f7ff;
    border-color: #003366;
}

/* Chatbot Input Area */
.chatbot-footer {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    background: #f1f3f5;
    border-radius: 30px;
    padding: 5px 15px;
}

.chat-btn {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 5px;
    transition: color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-btn:hover {
    color: #003366;
}

.chat-input-wrapper input[type="text"] {
    flex: 1;
    border: none;
    background: transparent;
    padding: 10px;
    font-size: 1.1rem;
    outline: none;
    color: #333;
}

.chat-btn--send {
    color: #003366;
}

/* Toggle Button */
.chatbot-toggle {
    position: fixed;
    bottom: 30px;
    right: 40px;
    width: 70px;
    height: 70px;
    background: transparent;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 9998;
    transition: transform 0.3s;
}

.chatbot-toggle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.chatbot-toggle:hover {
    transform: scale(1.05);
}

@keyframes fadeInMsg {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Typing Indicator */
.typing-bubble {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 12px 18px !important;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: #999;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(1) { animation-delay: 0s; }
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typingBounce {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}
/* Image Preview Strip */
.chat-image-preview {
    display: none;
    padding: 8px 15px 0;
    position: relative;
}

.chat-image-preview.active {
    display: flex;
    align-items: center;
    gap: 8px;
}

.chat-image-preview__thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #e0e0e0;
    margin-bottom: 5px;
}

.chat-image-preview__remove {
    position: absolute;
    top: 4px;
    left: 62px;
    width: 20px;
    height: 20px;
    background: #ff4444;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 0.7rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: transform 0.15s;
}

.chat-image-preview__remove:hover {
    transform: scale(1.15);
}

/* Chat Image Bubble */
.chat-msg__img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 14px;
    object-fit: cover;
    display: block;
    margin-bottom: 4px;
    cursor: pointer;
}

.chat-msg--user .chat-msg__img {
    border-radius: 14px 14px 0 14px;
    margin-left: auto;
}

</style>

<!-- Nút mở Chatbot -->
<div class="chatbot-toggle" id="chatbotToggleBtn" onclick="toggleChatbot()">
    <img src="/SmartFit/assets/img/chatbox.png" alt="Mở Chatbot AI">
</div>



<!-- Khung Chatbot -->
<div id="smartfit-chatbot">
    <!-- Header -->
    <div class="chatbot-header">
        <div class="chatbot-header__title">
            Trợ lý AI SmartFit
        </div>
        <div class="chatbot-header__actions">
            <!-- Nút thu phóng chatbot -->
            <div class="chatbot-header__resize">
                <button class="chatbot-header__resize-btn" onclick="resizeChatbot('up')" title="Phóng to">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button class="chatbot-header__resize-btn" onclick="resizeChatbot('down')" title="Thu nhỏ">
                    <i class="fa-solid fa-minus"></i>
                </button>
            </div>
            <!-- Nút liên hệ mới nằm trên title header -->
            <a href="https://zalo.me/0971996942" target="_blank" title="Liên hệ Zalo" class="contact-header-link">
                <i class="fa-solid fa-comment-dots"></i>
            </a>
            <a href="mailto:nguyenminhthanh043216@gmail.com" title="Gửi Email" class="contact-header-link">
                <i class="fa-solid fa-envelope"></i>
            </a>
            <i class="fa-solid fa-xmark chatbot-header__icon" onclick="toggleChatbot()" title="Đóng"></i>
        </div>
    </div>

    <!-- Body -->
    <div class="chatbot-body" id="chatBody">
        <!-- Msg 1: Thẻ thời tiết -->
        <div class="chat-msg chat-msg--ai">
            <div class="weather-card" id="chatWeatherCard">
                <div class="weather-card__icon" id="cwIcon">☁️</div>
                <div class="weather-card__info">
                    <span class="weather-card__loc" id="cwLoc">Đang tải...</span>
                    <span class="weather-card__temp" id="cwTemp">--°C</span>
                    <span class="weather-card__desc" id="cwDesc">--</span>
                </div>
            </div>
        </div>

        <!-- Msg 2: Câu chào -->
        <div class="chat-msg chat-msg--ai">
            <div class="chat-bubble">
                Chào bạn! Dựa vào thời tiết trên, AI SmartFit có thể giúp gì cho bạn hôm nay?
            </div>
        </div>

        <!-- Msg 3: Gợi ý -->
        <div class="chat-msg chat-msg--ai">
            <div class="suggestion-chips">
                <div class="chip" onclick="sendSuggestion('Phối đồ với món này')">Phối đồ với món này 📸</div>
            </div>
        </div>
    </div>

    <!-- AI Chatbot Footer -->
    <div class="chatbot-footer" id="aiChatFooter">
        <div class="chat-image-preview" id="chatImagePreview">
            <img src="" alt="Preview" class="chat-image-preview__thumb" id="chatImageThumb">
            <button class="chat-image-preview__remove" onclick="removeChatImagePreview()" title="Hủy ảnh">&times;</button>
        </div>
        <div class="chat-input-wrapper">
            <input type="file" id="chatImageUpload" hidden accept="image/*">
            <button class="chat-btn" onclick="document.getElementById('chatImageUpload').click()" title="Tải ảnh lên">
                <i class="fa-solid fa-image"></i>
            </button>
            <input type="text" id="chatInput" placeholder="Nhập tin nhắn..." onkeypress="handleChatEnter(event)">
            <button class="chat-btn chat-btn--send" onclick="sendUserMessage()">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>


</div>

<script>
// Mở / Đóng Chatbot
function toggleChatbot() {
    const chatbot = document.getElementById('smartfit-chatbot');
    chatbot.classList.toggle('active');

    // Nếu vừa mới mở lên thì clone dữ liệu thời tiết
    if (chatbot.classList.contains('active')) {
        syncWeatherToChatbot();
    }
}

// Phóng to / Thu nhỏ Chatbot
function resizeChatbot(direction) {
    const chatbot = document.getElementById('smartfit-chatbot');
    const states = ['', 'enlarged', 'maximized']; // 3 mức: bình thường → lớn → rất lớn

    // Tìm state hiện tại
    let current = 0;
    if (chatbot.classList.contains('maximized')) current = 2;
    else if (chatbot.classList.contains('enlarged')) current = 1;

    // Tăng hoặc giảm
    if (direction === 'up' && current < 2) current++;
    else if (direction === 'down' && current > 0) current--;

    // Xóa tất cả, gán mới
    chatbot.classList.remove('enlarged', 'maximized');
    if (states[current]) chatbot.classList.add(states[current]);
}

// Lấy dữ liệu thời tiết hiện tại từ trang index đẩy vào thẻ Weather Card
function syncWeatherToChatbot() {
    try {
        const homeTemp = document.querySelector('.info__weather__temp')?.innerText || '25°C';
        const homeLoc = window.app?.currentLocationContext || 'Hồ Chí Minh';
        const homeDesc = document.querySelector('.info__weather__text')?.innerText || 'Trời quang';
        const homeIconHtml = document.querySelector('.info__weather__icon')?.innerHTML || '☁️';

        document.getElementById('cwTemp').innerHTML = homeTemp;
        document.getElementById('cwLoc').innerText = homeLoc;
        document.getElementById('cwDesc').innerText = homeDesc;
        document.getElementById('cwIcon').innerHTML = homeIconHtml;
    } catch (e) {
        console.warn("Không đồng bộ được thời tiết vào chatbot", e);
    }
}

// Lấy context thời tiết + địa điểm hiện tại
function getChatContext() {
    const weather = document.querySelector('.info__weather__text')?.innerText?.trim() || 'Không rõ';
    const temp = document.querySelector('.info__weather__temp')?.innerText?.trim() || '';
    const location = window.app?.currentLocationContext || 'Không rõ';
    return {
        weather: temp ? `${temp}, ${weather}` : weather,
        location: location
    };
}

// Gửi tin nhắn tới Backend AI (hỗ trợ Multimodal)
async function sendToAI(message, imageBase64) {
    const context = getChatContext();

    // Hiển thị typing indicator
    showTypingIndicator();

    // Xây dựng payload
    const payload = {
        message: message,
        weather: context.weather,
        location: context.location
    };

    // Nếu có ảnh, gửi kèm base64
    if (imageBase64) {
        payload.image = imageBase64;
    }

    try {
        const response = await fetch('/SmartFit/includes/chatbot_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        
        // Xóa typing indicator
        hideTypingIndicator();

        if (data.success && data.reply) {
            appendAIMessage(data.reply);
        } else {
            appendAIMessage(data.reply || 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại nhé! 🙏');
        }
    } catch (error) {
        console.error('Chatbot Error:', error);
        hideTypingIndicator();
        appendAIMessage('Kết nối bị gián đoạn, vui lòng thử lại sau nhé! 🙏');
    }
}

// Bấm chip gợi ý
function sendSuggestion(text) {
    appendUserMessage(text);
    sendToAI(text);
}

// Sự kiện bấm Enter
function handleChatEnter(e) {
    if (e.key === 'Enter') {
        sendUserMessage();
    }
}

// Biến lưu ảnh đính kèm hiện tại
let chatPendingImage = null;

// Lắng nghe chọn ảnh
document.getElementById('chatImageUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Kiểm tra định dạng ảnh
    if (!file.type.startsWith('image/')) {
        if (typeof showToast === 'function') showToast('Vui lòng chọn file ảnh!', 'error');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(evt) {
        chatPendingImage = evt.target.result;
        const preview = document.getElementById('chatImagePreview');
        const thumb = document.getElementById('chatImageThumb');
        thumb.src = chatPendingImage;
        preview.classList.add('active');
    };
    reader.readAsDataURL(file);
});

// Hủy ảnh preview
function removeChatImagePreview() {
    chatPendingImage = null;
    const preview = document.getElementById('chatImagePreview');
    preview.classList.remove('active');
    document.getElementById('chatImageUpload').value = '';
}

// Gửi tin nhắn từ input (có hỗ trợ ảnh)
function sendUserMessage() {
    const inputEl = document.getElementById('chatInput');
    const text = inputEl.value.trim();
    const hasImage = !!chatPendingImage;

    if (!text && !hasImage) return;
    
    appendUserMessage(text, chatPendingImage);
    inputEl.value = '';

    // Xóa preview sau khi gửi
    const sentImage = chatPendingImage;
    removeChatImagePreview();

    // Gửi AI với text và/hoặc ảnh (Multimodal)
    if (text) {
        sendToAI(text, sentImage || null);
    } else if (hasImage) {
        sendToAI('', sentImage);
    }
}

// Render tin nhắn của User (hỗ trợ ảnh)
function appendUserMessage(text, imageSrc) {
    const chatBody = document.getElementById('chatBody');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'chat-msg chat-msg--user';

    // Nếu có ảnh, hiển thị ảnh như bong bóng Messenger
    if (imageSrc) {
        const img = document.createElement('img');
        img.src = imageSrc;
        img.className = 'chat-msg__img';
        img.alt = 'Hình ảnh đính kèm';
        img.onclick = function() { window.open(imageSrc, '_blank'); };
        msgDiv.appendChild(img);
    }

    if (text) {
        const textSpan = document.createElement('span');
        textSpan.innerText = text;
        msgDiv.appendChild(textSpan);
    }

    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Render tin nhắn của AI
function appendAIMessage(text) {
    const chatBody = document.getElementById('chatBody');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'chat-msg chat-msg--ai';

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble';
    bubble.innerText = text;

    msgDiv.appendChild(bubble);
    chatBody.appendChild(msgDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Hiệu ứng "Đang gõ..."
function showTypingIndicator() {
    const chatBody = document.getElementById('chatBody');
    
    // Tránh hiện 2 lần
    if (document.getElementById('typingIndicator')) return;

    const typingDiv = document.createElement('div');
    typingDiv.className = 'chat-msg chat-msg--ai';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = `
        <div class="chat-bubble typing-bubble">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
        </div>
    `;
    chatBody.appendChild(typingDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function hideTypingIndicator() {
    const el = document.getElementById('typingIndicator');
    if (el) el.remove();
}
</script>
