# SmartFit - AI Outfit Recommender & E-Commerce Platform

## 1. Giới thiệu dự án

SmartFit là một nền tảng thương mại điện tử đa gian hàng (Multi-vendor) được tích hợp sâu trí tuệ nhân tạo nhằm giải quyết bài toán lựa chọn trang phục hàng ngày và tối ưu hóa trải nghiệm mua sắm trực tuyến của người dùng. 

Thay vì cung cấp các danh sách sản phẩm rời rạc theo cách truyền thống, hệ thống được thiết kế để hoạt động như một trợ lý phong cách cá nhân. Thuật toán cốt lõi tự động phân tích các biến số đầu vào như điều kiện thời tiết tại địa điểm người dùng cung cấp, mục đích sử dụng, độ tuổi và sở thích cá nhân để xuất ra các đề xuất phối đồ hoàn chỉnh. Điểm khác biệt của dự án nằm ở khả năng phân tích vòng lặp tủ đồ: người dùng có thể cung cấp thông tin về những trang phục họ đã sở hữu, AI sẽ tính toán và đề xuất các sản phẩm mua bổ sung phù hợp nhất từ kho hàng để hoàn thiện bộ trang phục.

Mục tiêu cốt lõi của SmartFit là tự động hóa và đơn giản hóa quy trình mua sắm. Việc tích hợp từ khâu tra cứu thời tiết, nhận tư vấn AI cho đến thao tác chọn màu sắc, kích cỡ và thanh toán được thực hiện liền mạch trên một nền tảng duy nhất. Điều này giúp rút ngắn đáng kể vòng đời ra quyết định của khách hàng, đồng thời gia tăng tỷ lệ chuyển đổi chéo (Cross-sell) cho các nhà bán hàng tham gia vào hệ thống.

## 2. Kiến trúc và Công nghệ sử dụng

Dự án được xây dựng dựa trên kiến trúc hướng dịch vụ, kết hợp giữa mô hình MVC truyền thống và các công cụ tự động hóa hiện đại. Các công nghệ cốt lõi được chia thành các phân lớp như sau:

### 2.1. Frontend (Giao diện người dùng)
- **HTML5, CSS3 và JavaScript thuần (Vanilla JS):** Xây dựng giao diện người dùng tương thích đa thiết bị.
- **Kiến trúc Bất đồng bộ (AJAX):** Toàn bộ các tương tác gọi dữ liệu từ AI và truy xuất sản phẩm đều được xử lý ẩn, đảm bảo luồng giao tiếp giữa Client và Server diễn ra liên tục mà không làm gián đoạn trải nghiệm thông qua việc tải lại trang.
- **Dynamic UI Logic:** Tích hợp thuật toán tự động nhận diện kết quả JSON từ Backend để điều chỉnh cấu trúc DOM trên trình duyệt. Cụ thể, hệ thống sẽ tự động gộp các khung hình ảnh hiển thị nếu AI đề xuất trang phục liền thân (One-piece) và phân tách khung hình nếu đề xuất là trang phục phối rời (Áo và Quần).

### 2.2. Backend (Xử lý logic máy chủ)
- **PHP:** Ngôn ngữ xử lý luồng nghiệp vụ chính, cấu trúc theo mô hình Model-View-Controller (MVC) để duy trì tính toàn vẹn và dễ dàng mở rộng mã nguồn.
- **Tích hợp API Thanh toán:** Triển khai cổng thanh toán điện tử VNPAY (môi trường Sandbox) để xử lý và xác thực các giao dịch tài chính theo thời gian thực một cách an toàn.

### 2.3. Hệ quản trị Cơ sở dữ liệu
- **MySQL / MariaDB:** Quản lý toàn bộ cấu trúc dữ liệu liên quan đến người dùng, hệ thống đa gian hàng (vendor), đơn hàng và hàng tồn kho theo các biến thể chi tiết (size, tên màu).
- **Thuật toán Bayesian Average:** Việc xếp hạng sản phẩm không phụ thuộc đơn thuần vào điểm số sao tĩnh. Hệ thống triển khai công thức toán học nội suy trực tiếp trong các truy vấn SQL, kết hợp trọng số giữa điểm đánh giá trung bình, tổng số lượt đánh giá và số lượng sản phẩm bán ra thực tế nhằm ngăn chặn tình trạng thao túng đánh giá ảo.

### 2.4. Trí tuệ nhân tạo (AI Engine)
- **Google Gemini 1.5 Flash API:** Đóng vai trò là lõi xử lý ngôn ngữ tự nhiên và tư duy logic.
- **Kỹ thuật Prompt Engineering và Context Injection:** Dữ liệu kho hàng thực tế được chèn ẩn vào các chuỗi truy vấn để điều hướng AI. Hệ thống ép buộc AI phải hiểu và nhận diện màu sắc bằng ngôn ngữ tự nhiên (tiếng Việt) thay vì mã màu HEX định dạng sẵn. Kết quả trả về được chuẩn hóa dưới định dạng JSON nghiêm ngặt để đảm bảo Backend phân tích cú pháp không xảy ra lỗi.

### 2.5. Tự động hóa, Triển khai và DevOps
- **Docker và Docker Compose:** Ứng dụng công nghệ container hóa để đóng gói toàn bộ môi trường phát triển (bao gồm máy chủ web, PHP, cơ sở dữ liệu và phpMyAdmin). Việc này đảm bảo tính nhất quán tuyệt đối giữa môi trường phát triển cục bộ và môi trường máy chủ sản xuất (Production).
- **n8n (Workflow Automation):** Triển khai kiến trúc Webhook độc lập để xử lý các tác vụ nền. Khi có giao dịch thành công, hệ thống PHP sẽ gửi tín hiệu (payload) đến n8n để kích hoạt luồng tự động biên dịch và gửi email hóa đơn điện tử cho khách hàng, giúp giảm tải trực tiếp cho máy chủ web chính.
- **Git và MobaXterm:** Sử dụng Git để kiểm soát phiên bản mã nguồn và MobaXterm để quản trị kết nối SSH trực tiếp với các máy chủ triển khai hệ thống.

## 3. Hướng dẫn cài đặt môi trường (Installation)

### 3.1. Cấu hình API Key (Google Gemini AI)
1. Truy cập [Google AI Studio](https://aistudio.google.com/app/apikey) để tạo một API Key miễn phí.
2. Trong mã nguồn dự án, đi tới thư mục `includes/`.
3. Tạo một file mới tên là `gemini-config.php` và dán mã sau vào, thay thế bằng key bạn vừa lấy:

```php
<?php
define('GEMINI_API_KEY', 'ĐIỀN_API_KEY_CỦA_BẠN_VÀO_ĐÂY');
?>
```

### 3.2. Khởi tạo Server và Cơ sở dữ liệu bằng Docker:

Mở Terminal (CMD hoặc PowerShell)
Chạy câu lệnh sau:
```
cd SmartFit-main
docker-compose -f docker/docker-compose.yml up -d --build
```
Đợi vài phút để quá trình cài đặt hoàn tất.

## 4. Chạy ứng dụng (Usage)
Khi các container của Docker đã ở trạng thái chạy (Running), bạn mở trình duyệt và truy cập:

Ứng dụng SmartFit: http://localhost

Quản lý Database (Tùy chọn): http://localhost:8080 (Đăng nhập với Username: root và Password: root).
