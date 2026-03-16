<?php
// 1. Khởi động session và kiểm tra đăng nhập
session_start();
require_once '../includes/config.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về home
if (!isset($_SESSION['user_id'])) {
    header("Location: ../home.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// 2. Xử lý cập nhật thông tin khi có request POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $age      = (int)$_POST['age'];
    $gender   = mysqli_real_escape_string($conn, $_POST['gender']);

    // Sử dụng Prepared Statement để bảo mật
    $sql_update = "UPDATE users SET fullname = ?, phone = ?, address = ?, age = ?, gender = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql_update)) {
        mysqli_stmt_bind_param($stmt, "sssisi", $fullname, $phone, $address, $age, $gender, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Cập nhật thông tin thành công!";
            $message_type = "success";
        } else {
            $message = "Có lỗi xảy ra: " . mysqli_error($conn);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

// 3. Lấy thông tin mới nhất của người dùng từ CSDL
$sql_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($result_user);

// Gán giá trị mặc định cho các trường chưa cập nhật để hiển thị
$display_fullname = $user_data['fullname'] ? $user_data['fullname'] : "Chưa cập nhật";
$display_phone    = $user_data['phone']    ? $user_data['phone']    : "Chưa cập nhật";
$display_address  = $user_data['address']  ? $user_data['address']  : "Chưa cập nhật";
$display_age      = $user_data['age']      ? $user_data['age']      : "Chưa cập nhật";
$display_gender   = ($user_data['gender'] == 'male') ? "Nam" : (($user_data['gender'] == 'female') ? "Nữ" : "Chưa cập nhật");

// Include header (đảm bảo đường dẫn đúng)
require_once '../includes/header.php';
?>

<div class="personal-page">
    <div class="grid wide">
        <div class="personal-container">
            <!-- Header thông tin -->
            <div class="personal-header">
                <div class="personal-avatar">
                    <i class="fa-solid fa-circle-user"></i>
                </div>
                <div class="personal-welcome">
                    <h1 class="personal-title">Thông tin cá nhân</h1>
                    <p class="personal-subtitle">Quản lý thông tin tài khoản và sở thích thời trang của bạn</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert--<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Form thông tin -->
            <form id="infoForm" method="POST" action="">
                <div class="info-grid">
                    <!-- Tên đăng nhập (Read-only) -->
                    <div class="info-item">
                        <label class="info-label">Tên đăng nhập</label>
                        <div class="info-content info-content--static">
                            <span><?php echo $user_data['name']; ?></span>
                        </div>
                    </div>

                    <!-- Email (Read-only) -->
                    <div class="info-item">
                        <label class="info-label">Địa chỉ Email</label>
                        <div class="info-content info-content--static">
                            <span><?php echo $user_data['email']; ?></span>
                        </div>
                    </div>

                    <!-- Họ và tên (Editable) -->
                    <div class="info-item">
                        <label class="info-label">Họ và tên</label>
                        <div class="info-content">
                            <span class="view-mode"><?php echo $display_fullname; ?></span>
                            <input type="text" name="fullname" class="edit-mode info-input" value="<?php echo $user_data['fullname']; ?>" placeholder="Nhập họ tên đầy đủ">
                        </div>
                    </div>

                    <!-- Số điện thoại (Editable) -->
                    <div class="info-item">
                        <label class="info-label">Số điện thoại</label>
                        <div class="info-content">
                            <span class="view-mode"><?php echo $display_phone; ?></span>
                            <input type="text" name="phone" class="edit-mode info-input" value="<?php echo $user_data['phone']; ?>" placeholder="Nhập số điện thoại">
                        </div>
                    </div>

                    <!-- Tuổi (Editable) -->
                    <div class="info-item">
                        <label class="info-label">Tuổi</label>
                        <div class="info-content">
                            <span class="view-mode"><?php echo $display_age; ?></span>
                            <input type="number" name="age" class="edit-mode info-input" value="<?php echo $user_data['age']; ?>" placeholder="Nhập tuổi">
                        </div>
                    </div>

                    <!-- Giới tính (Editable) -->
                    <div class="info-item">
                        <label class="info-label">Giới tính</label>
                        <div class="info-content">
                            <span class="view-mode"><?php echo $display_gender; ?></span>
                            <select name="gender" class="edit-mode info-select">
                                <option value="" <?php echo is_null($user_data['gender']) ? 'selected' : ''; ?>>Chọn giới tính</option>
                                <option value="male" <?php echo ($user_data['gender'] == 'male') ? 'selected' : ''; ?>>Nam</option>
                                <option value="female" <?php echo ($user_data['gender'] == 'female') ? 'selected' : ''; ?>>Nữ</option>
                            </select>
                        </div>
                    </div>

                    <!-- Địa chỉ (Editable - Textarea) -->
                    <div class="info-item info-item--full">
                        <label class="info-label">Địa chỉ giao hàng</label>
                        <div class="info-content">
                            <span class="view-mode"><?php echo $display_address; ?></span>
                            <textarea name="address" class="edit-mode info-textarea" placeholder="Nhập địa chỉ chi tiết (Số nhà, đường, phường/xã, quận/huyện...)"><?php echo $user_data['address']; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Điều khiển -->
                <div class="info-actions">
                    <button type="button" id="btnEdit" class="info-btn info-btn--edit">
                        <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa thông tin
                    </button>
                    
                    <div class="edit-mode info-btn-group">
                        <button type="submit" name="update_info" class="info-btn info-btn--save">Lưu thay đổi</button>
                        <button type="button" id="btnCancel" class="info-btn info-btn--cancel">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    :root {
        --personal-bg: #f8f9fa;
        --personal-card: #ffffff;
        --personal-border: #e9ecef;
        --personal-text-main: #1d1d1f;
        --personal-text-sub: #6e6e73;
        --personal-accent: #0071e3;
    }

    .personal-page {
        background-color: var(--personal-bg);
        min-height: calc(100vh - 80px);
        padding: 60px 0;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .personal-container {
        max-width: 900px;
        margin: 0 auto;
        background: var(--personal-card);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 40px;
    }

    .personal-header {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid var(--personal-border);
    }

    .personal-avatar {
        font-size: 80px;
        color: #d2d2d7;
    }

    .personal-title {
        font-size: 3.2rem;
        font-weight: 700;
        color: var(--personal-text-main);
        margin: 0 0 15px 0;
    }

    .personal-subtitle {
        font-size: 1.6rem;
        color: var(--personal-text-sub);
        margin: 0;
    }

    /* Alert styles */
    .alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        font-size: 1.5rem;
        font-weight: 500;
        text-align: center;
    }
    .alert--success { background-color: #e3f9e5; color: #1f7a1f; border: 1px solid #c1efc5; }
    .alert--error { background-color: #ffe6e6; color: #cc0000; border: 1px solid #ffcccc; }

    /* Grid Layout for info */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }

    .info-item--full {
        grid-column: span 2;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .info-label {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--personal-text-sub);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-content {
        font-size: 1.7rem;
        color: var(--personal-text-main);
        min-height: 45px;
        display: flex;
        align-items: center;
    }

    .info-content--static {
        background-color: #f5f5f7;
        padding: 0 15px;
        border-radius: 8px;
        color: #86868b;
        justify-content: space-between;
    }

    .info-note {
        font-size: 1.2rem;
        font-style: italic;
    }

    /* Toggle visibility base on state */
    .personal-container:not(.is-editing) .edit-mode {
        display: none !important;
    }
    
    .personal-container.is-editing .view-mode {
        display: none !important;
    }

    .personal-container.is-editing .info-btn--edit {
        display: none !important;
    }

    .info-input, .info-select, .info-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--personal-border);
        border-radius: 10px;
        font-size: 1.6rem;
        outline: none;
        transition: all 0.2s;
        background: #fff;
    }

    .info-input:focus, .info-select:focus, .info-textarea:focus {
        border-color: var(--personal-accent);
        box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
    }

    .info-textarea {
        min-height: 100px;
        resize: vertical;
    }

    /* Actions */
    .info-actions {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid var(--personal-border);
        display: flex;
        justify-content: center;
    }

    .info-btn {
        padding: 14px 30px;
        font-size: 1.6rem;
        font-weight: 600;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-btn--edit {
        background-color: var(--personal-text-main);
        color: white;
    }

    .info-btn--edit:hover {
        background-color: #333;
        transform: translateY(-2px);
    }

    .info-btn-group {
        display: flex;
        gap: 15px;
    }

    .info-btn--save {
        background-color: var(--personal-accent);
        color: white;
    }

    .info-btn--save:hover {
        background-color: #0066cc;
    }

    .info-btn--cancel {
        background-color: #e9ecef;
        color: #495057;
    }

    .info-btn--cancel:hover {
        background-color: #dee2e6;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        .info-item--full {
            grid-column: span 1;
        }
        .personal-container {
            padding: 25px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.personal-container');
        const btnEdit = document.getElementById('btnEdit');
        const btnCancel = document.getElementById('btnCancel');

        // Chuyển sang chế độ sửa
        btnEdit.addEventListener('click', () => {
            container.classList.add('is-editing');
        });

        // Hủy quay lại chế độ xem
        btnCancel.addEventListener('click', () => {
            container.classList.remove('is-editing');
            
            // Có thể thêm logic reset form nếu muốn
            // document.getElementById('infoForm').reset();
        });

        // Tự động ẩn thông báo sau 3 giây
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
