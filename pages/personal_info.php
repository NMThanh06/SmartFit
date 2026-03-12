<?php
include '../includes/header.php';
?>

<div class="profile">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="profile-title-group">
                <h2><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Khách'; ?></h2>
                <p>Thành viên của SmartFit</p>
            </div>
        </div>

        <form action="">
            <!-- Account Information -->
            <div class="profile-section">
                <div class="profile-section__title">
                    <i class="fa-solid fa-user-circle"></i>
                    Thông tin cơ bản
                </div>
                <div class="profile-form-grid">
                    <div class="profile-input-group">
                        <label for="name">Họ và tên</label>
                        <input type="text" id="name" class="profile-input" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>" placeholder="Họ và tên">
                    </div>
                    <div class="profile-input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" class="profile-input" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>" placeholder="Email">
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="profile-section">
                <div class="profile-section__title">
                    <i class="fa-solid fa-address-book"></i>
                    Thông tin liên hệ
                </div>
                <div class="profile-form-grid">
                    <div class="profile-input-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" class="profile-input" placeholder="Số điện thoại">
                    </div>
                    <div class="profile-input-group">
                        <label for="address">Địa chỉ</label>
                        <input type="text" id="address" class="profile-input" placeholder="Địa chỉ của bạn">
                    </div>
                </div>
            </div>

            <!-- AI Configuration -->
            <div class="profile-section">
                <div class="profile-section__title">
                    <i class="fa-solid fa-sliders"></i>
                    Cấu hình AI Phối đồ
                </div>
                <div class="profile-form-grid">
                    <div class="profile-input-group">
                        <label for="gender">Giới tính</label>
                        <select id="gender" class="profile-input profile-select">
                            <option value="">Chọn giới tính</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="profile-input-group">
                        <label for="age">Độ tuổi</label>
                        <input type="number" id="age" class="profile-input" min="1" max="100" placeholder="Số tuổi">
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <button type="button" class="profile-btn profile-btn--secondary">Hủy bỏ</button>
                <button type="submit" class="profile-btn profile-btn--primary">
                    <i class="fa-solid fa-save"></i>
                    Lưu thông tin
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
