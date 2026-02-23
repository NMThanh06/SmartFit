# SmartFit - AI Outfit Recommender

SmartFit là ứng dụng web thông minh hỗ trợ phối đồ tự động dựa trên thời tiết và dịp sử dụng, ứng dụng sức mạnh của AI để đưa ra những gợi ý trang phục tối ưu nhất cho người dùng.

## 1. Thư viện và Phụ thuộc (Dependencies)
Để chạy dự án này, môi trường của bạn cần đáp ứng các yêu cầu sau:
- **Docker Desktop** đã được cài đặt và đang hoạt động trên máy.
- Trình duyệt web hiện đại (Chrome, Edge, Firefox).
- Kết nối Internet để tải các Image cần thiết và gọi Google Gemini API.

## 2. Hướng dẫn cài đặt môi trường (Installation)

### 2.1. Cấu hình API Key (Google Gemini AI)
1. Truy cập [Google AI Studio](https://aistudio.google.com/app/apikey) để tạo một API Key miễn phí.
2. Trong mã nguồn dự án, đi tới thư mục `includes/`.
3. Tạo một file mới tên là `gemini-config.php` và dán mã sau vào, thay thế bằng key bạn vừa lấy:

```php
<?php
define('GEMINI_API_KEY', 'ĐIỀN_API_KEY_CỦA_BẠN_VÀO_ĐÂY');
?>
```

### 2.2. Khởi tạo Server và Cơ sở dữ liệu bằng Docker:

Ở Terminal (CMD hoặc PowerShell) tại thư mục chứa file docker-compose.yml

Chạy câu lệnh sau:
```
docker-compose -f docker/docker-compose.yml up -d --build
```
Đợi vài phút để quá trình cài đặt hoàn tất.

## 3. Chạy ứng dụng (Usage)
Khi các container của Docker đã ở trạng thái chạy (Running), bạn mở trình duyệt và truy cập:

Ứng dụng SmartFit: http://localhost

Quản lý Database (Tùy chọn): http://localhost:8080 (Đăng nhập với Username: root và Password: root).