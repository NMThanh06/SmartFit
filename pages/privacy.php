<?php
session_start();
$root = '../';
$title = "Chính sách bảo mật - SmartFit";
include $root . 'includes/header.php';
?>

<div class="privacy-page">
    <div class="grid wide">
        <div class="privacy-container">
            <header class="privacy-header">
                <h1 class="privacy-title">CHÍNH SÁCH BẢO MẬT</h1>
                <p class="privacy-subtitle">(PRIVACY POLICY)</p>
            </header>

            <div class="privacy-content">
                <section class="privacy-intro">
                    <p>Chào mừng bạn đến với <strong>SmartFit</strong>. Chúng tôi cam kết bảo vệ quyền riêng tư và thông tin cá nhân của bạn. Chính sách này giải thích cách chúng tôi thu thập, sử dụng và bảo vệ dữ liệu của bạn khi bạn sử dụng dịch vụ phối đồ AI của chúng tôi.</p>
                </section>

                <div class="privacy-sections">
                    <section class="privacy-section">
                        <h2>1. Thông tin chúng tôi thu thập</h2>
                        <p>Để cung cấp trải nghiệm phối đồ tốt nhất, chúng tôi thu thập các loại dữ liệu sau:</p>
                        <ul>
                            <li><strong>Thông tin tài khoản:</strong> Tên người dùng, địa chỉ Email khi bạn đăng ký tài khoản.</li>
                            <li><strong>Dữ liệu vị trí:</strong> Chúng tôi truy cập vị trí địa lý của bạn (với sự cho phép) để cập nhật dữ liệu thời tiết thời gian thực nhằm đưa ra gợi ý trang phục phù hợp.</li>
                            <li><strong>Dữ liệu tủ đồ:</strong> Hình ảnh và thông tin về các loại trang phục bạn tải lên hệ thống để quản lý và phối đồ.</li>
                        </ul>
                    </section>

                    <section class="privacy-section">
                        <h2>2. Cách chúng tôi sử dụng thông tin</h2>
                        <p>Dữ liệu của bạn được sử dụng cho các mục đích:</p>
                        <ul>
                            <li><strong>Cung cấp dịch vụ:</strong> Xử lý thuật toán AI để gợi ý bộ phối đồ dựa trên sở thích và thời tiết.</li>
                            <li><strong>Lưu trữ cá nhân:</strong> Lưu lại lịch sử phối đồ và quản lý tủ đồ ảo của riêng bạn.</li>
                            <li><strong>Cải thiện trải nghiệm:</strong> Gửi thông báo (Toast) về trạng thái lưu đồ hoặc cập nhật tính năng mới.</li>
                        </ul>
                    </section>

                    <section class="privacy-section">
                        <h2>3. Bảo mật dữ liệu</h2>
                        <p>Chúng tôi áp dụng các biện pháp kỹ thuật để đảm bảo an toàn dữ liệu:</p>
                        <ul>
                            <li>Dữ liệu mật khẩu được mã hóa an toàn trong cơ sở dữ liệu.</li>
                            <li>Chỉ những nhân viên quản trị (Admin) có thẩm quyền mới được truy cập dữ liệu hệ thống để bảo trì.</li>
                        </ul>
                    </section>

                    <section class="privacy-section">
                        <h2>4. Chia sẻ thông tin</h2>
                        <p>SmartFit không bán, trao đổi hoặc cho thuê thông tin cá nhân của bạn cho bên thứ ba. Chúng tôi chỉ chia sẻ dữ liệu vị trí với dịch vụ thời tiết bên thứ ba (như OpenWeatherMap) để lấy dữ liệu khí tượng mà không kèm theo danh tính của bạn.</p>
                    </section>

                    <section class="privacy-section">
                        <h2>5. Quyền của người dùng</h2>
                        <p>Bạn có toàn quyền:</p>
                        <ul>
                            <li>Truy cập và chỉnh sửa thông tin cá nhân bất cứ lúc nào qua trang Thông tin cá nhân.</li>
                            <li>Xóa các set đồ đã lưu trong Bộ sưu tập.</li>
                            <li>Ngừng cung cấp quyền truy cập vị trí trên trình duyệt.</li>
                        </ul>
                    </section>
                </div>

                <div class="privacy-footer-note">
                    <p>Nếu bạn có bất kỳ câu hỏi nào về chính sách này, vui lòng liên hệ với chúng tôi qua email hỗ trợ.</p>
                    <a href="<?php echo $root; ?>pages/home.php" class="button back-home-btn">Quay lại Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .privacy-page {
        padding: calc(var(--navbar-height) + 20px) 0 40px;
        background-color: var(--apple-bg);
        min-height: 100vh;
    }

    .privacy-container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 40px 50px;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-color);
    }

    .privacy-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 1px solid var(--apple-bg);
    }

    .privacy-title {
        font-size: 3.2rem;
        font-weight: 800;
        color: var(--apple-black);
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .privacy-subtitle {
        font-size: 1.6rem;
        color: var(--apple-grey);
        font-weight: 500;
    }

    .privacy-intro {
        min-height: 0;
        font-size: 1.6rem;
        line-height: 1.7;
        color: var(--text-main-grey);
        margin-bottom: 30px;
        text-align: justify;
        padding: 0;
    }

    .privacy-section {
        margin-bottom: 25px;
        padding: 0;
        min-height: auto;
        display: block;
    }

    .privacy-section h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--apple-black);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
    }

    .privacy-section p, .privacy-section ul {
        font-size: 1.6rem;
        line-height: 1.7;
        color: var(--text-sub-grey);
        margin-bottom: 5px;
    }

    .privacy-section ul {
        padding-left: 20px;
        list-style: none;
    }

    .privacy-section li {
        margin-bottom: 5px;
        position: relative;
    }

    .privacy-section li::before {
        content: "•";
        color: var(--primary-blue);
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }

    .privacy-footer-note {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid var(--border-color);
        text-align: center;
    }

    .privacy-footer-note p {
        font-size: 1.4rem;
        color: var(--apple-grey);
        margin-bottom: 25px;
    }

    .back-home-btn {
        display: inline-flex;
        background-color: var(--apple-black);
        color: #fff;
        text-decoration: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .back-home-btn:hover {
        background-color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(33, 118, 255, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .privacy-container {
            padding: 30px 20px;
            margin: 0 15px;
        }

        .privacy-title {
            font-size: 2.4rem;
        }

        .privacy-section h2 {
            font-size: 1.9rem;
        }
    }
</style>

<?php include '../includes/footer.php'; ?>
