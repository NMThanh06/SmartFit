<?php
session_start();
$root = '../';
$title = "Điều khoản dịch vụ - SmartFit";
include $root . 'includes/header.php';
?>

<div class="terms-page">
    <div class="grid wide">
        <div class="terms-container">
            <header class="terms-header">
                <h1 class="terms-title">ĐIỀU KHOẢN DỊCH VỤ</h1>
                <p class="terms-subtitle">(TERMS OF SERVICE)</p>
            </header>

            <div class="terms-content">
                <section class="terms-intro">
                    <p>Chào mừng bạn đến với <strong>SmartFit</strong>. Bằng cách truy cập và sử dụng dịch vụ của chúng tôi, bạn đồng ý tuân thủ các điều khoản và điều kiện dưới đây.</p>
                </section>

                <div class="terms-sections">
                    <section class="terms-section">
                        <h2>1. Chấp nhận điều khoản</h2>
                        <p>Việc bạn đăng ký tài khoản hoặc sử dụng tính năng phối đồ đồng nghĩa với việc bạn đã đọc, hiểu và đồng ý bị ràng buộc bởi các điều khoản này. Nếu bạn không đồng ý, vui lòng ngừng sử dụng dịch vụ.</p>
                    </section>

                    <section class="terms-section">
                        <h2>2. Tài khoản người dùng</h2>
                        <ul>
                            <li>Bạn có trách nhiệm bảo mật mật khẩu và thông tin tài khoản của mình.</li>
                            <li>Bạn cam kết cung cấp thông tin chính xác khi đăng ký và cập nhật thông tin khi có thay đổi.</li>
                            <li>Chúng tôi có quyền tạm khóa hoặc xóa tài khoản nếu phát hiện hành vi gian lận hoặc vi phạm các điều khoản này.</li>
                        </ul>
                    </section>

                    <section class="terms-section">
                        <h2>3. Quyền sở hữu trí tuệ và Nội dung người dùng</h2>
                        <ul>
                            <li><strong>Dịch vụ:</strong> Toàn bộ mã nguồn, thiết kế, logo và thuật toán phối đồ AI là tài sản trí tuệ của đội ngũ SmartFit.</li>
                            <li><strong>Ảnh tải lên:</strong> Bạn giữ quyền sở hữu đối với hình ảnh trang phục bạn tải lên. Tuy nhiên, bằng cách tải lên, bạn cấp cho SmartFit quyền sử dụng hình ảnh đó để thực hiện các chức năng phối đồ và cải thiện thuật toán AI.</li>
                            <li><strong>Nghiêm cấm:</strong> Bạn không được tải lên các nội dung vi phạm pháp luật, hình ảnh nhạy cảm hoặc vi phạm bản quyền của bên thứ ba.</li>
                        </ul>
                    </section>

                    <section class="terms-section">
                        <h2>4. Dịch vụ phối đồ và cửa hàng</h2>
                        <ul>
                            <li><strong>Độ chính xác:</strong> Thuật toán AI đưa ra gợi ý dựa trên dữ liệu thời tiết và thông tin bạn cung cấp. Các gợi ý này chỉ mang tính chất tham khảo về phong cách.</li>
                            <li><strong>Giao dịch:</strong> Đối với các món đồ trong Shop, chúng tôi cam kết cung cấp thông tin về màu sắc, kích cỡ và kho hàng chính xác nhất có thể. Tuy nhiên, màu sắc thực tế có thể hơi khác do hiển thị của màn hình thiết bị.</li>
                        </ul>
                    </section>

                    <section class="terms-section">
                        <h2>5. Giới hạn trách nhiệm</h2>
                        <ul>
                            <li>SmartFit không chịu trách nhiệm cho bất kỳ thiệt hại nào phát sinh từ việc sử dụng dịch vụ hoặc do sự gián đoạn kết nối internet.</li>
                            <li>Chúng tôi không đảm bảo rằng dịch vụ sẽ hoàn toàn không có lỗi 100%, nhưng chúng tôi cam kết nỗ lực khắc phục sự cố sớm nhất.</li>
                        </ul>
                    </section>

                    <section class="terms-section">
                        <h2>6. Thay đổi điều khoản</h2>
                        <p>Chúng tôi có quyền cập nhật các điều khoản này bất cứ lúc nào để phù hợp với sự phát triển của ứng dụng. Thông báo về các thay đổi lớn sẽ được cập nhật trên trang chủ hoặc qua email.</p>
                    </section>
                </div>

                <div class="terms-footer-note">
                    <p>Nếu bạn có bất kỳ câu hỏi nào về các điều khoản này, vui lòng liên hệ với chúng tôi qua email hỗ trợ.</p>
                    <a href="<?php echo $root; ?>pages/home.php" class="button back-home-btn">Quay lại Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .terms-page {
        padding: calc(var(--navbar-height) + 20px) 0 40px;
        background-color: var(--apple-bg);
        min-height: 100vh;
    }

    .terms-container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 40px 50px;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid var(--border-color);
    }

    .terms-header {
        text-align: center;
        margin-bottom: 30px;
        border-bottom: 1px solid var(--apple-bg);
    }

    .terms-title {
        font-size: 3.2rem;
        font-weight: 800;
        color: var(--apple-black);
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .terms-subtitle {
        font-size: 1.6rem;
        color: var(--apple-grey);
        font-weight: 500;
        margin-bottom: 5px;
    }

    .terms-intro {
        min-height: 0;
        font-size: 1.6rem;
        line-height: 1.7;
        color: var(--text-main-grey);
        margin-bottom: 30px;
        text-align: justify;
        padding: 0;
    }

    .terms-section {
        margin-bottom: 25px;
        padding: 0;
        min-height: auto;
        display: block;
    }

    .terms-section h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--apple-black);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
    }

    .terms-section p, .terms-section ul {
        font-size: 1.6rem;
        line-height: 1.7;
        color: var(--text-sub-grey);
        margin-bottom: 5px;
    }

    .terms-section ul {
        padding-left: 20px;
        list-style: none;
    }

    .terms-section li {
        margin-bottom: 5px;
        position: relative;
    }

    .terms-section li::before {
        content: "•";
        color: var(--primary-blue);
        font-weight: bold;
        display: inline-block;
        width: 1em;
        margin-left: -1em;
    }

    .terms-footer-note {
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid var(--border-color);
        text-align: center;
    }

    .terms-footer-note p {
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
        .terms-container {
            padding: 30px 20px;
            margin: 0 15px;
        }

        .terms-title {
            font-size: 2.4rem;
        }

        .terms-section h2 {
            font-size: 1.9rem;
        }
    }
</style>

<?php include '../includes/footer.php'; ?>
