<?php
/**
 * pages/manage_users.php
 * Hệ thống Quản trị Người dùng SmartFit (Dành riêng cho Admin)
 */

require_once '../middleware.php'; // Kích hoạt RBAC
$base_dir = '../';
require_once $base_dir . 'includes/config.php';
require_once $base_dir . 'includes/functions.php';

// 1. Kiểm tra quyền Admin tối cao
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$notification = null;

// 2. Xử lý Cập nhật Quyền hạn (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

    // Khối an toàn: Không cho phép Admin tự sửa quyền của chính mình
    if ($user_id === intval($_SESSION['user_id'])) {
        $notification = ['type' => 'error', 'msg' => 'Bạn không thể tự thay đổi quyền hạn của chính mình!'];
    } else {
        // Sử dụng Prepared Statement để bảo mật
        $update_sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "si", $new_role, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $notification = ['type' => 'success', 'msg' => 'Cập nhật quyền hạn thành công!'];
        } else {
            $notification = ['type' => 'error', 'msg' => 'Lỗi hệ thống: ' . mysqli_error($conn)];
        }
        mysqli_stmt_close($stmt);
    }
}

// 3. Truy vấn danh sách người dùng
$sql = "SELECT id, name, fullname, email, role FROM users ORDER BY id DESC";
$users_result = mysqli_query($conn, $sql);

include $base_dir . 'includes/header.php';
?>

<div class="web__background--overlay"></div>

<div class="grid wide">
    <!-- Hiển thị thông báo nếu có -->
    <?php if ($notification): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                app.showNotification('<?= $notification['msg'] ?>', '<?= $notification['type'] ?>');
            });
        </script>
    <?php endif; ?>

    <div class="manage-users">
        <div class="manage-users__header">
            <h1 class="manage-users__title">Quản Lý Người Dùng</h1>
            <p class="manage-users__subtitle">Quản trị viên có thể điều chỉnh vai trò và cấp quyền cho thành viên</p>
        </div>

        <div class="manage-users__card">
            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Cấp quyền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <?php 
                                $isSelf = (intval($user['id']) === intval($_SESSION['user_id']));
                            ?>
                            <tr>
                                <td><strong>#<?= $user['id'] ?></strong></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['fullname']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                        $role_class = '';
                                        switch($user['role']) {
                                            case 'admin': $role_class = 'badge--admin'; break;
                                            case 'sales': $role_class = 'badge--sales'; break;
                                            case 'support': $role_class = 'badge--support'; break;
                                            default: $role_class = 'badge--customer';
                                        }
                                    ?>
                                    <span class="badge <?= $role_class ?>"><?= strtoupper($user['role']) ?></span>
                                </td>
                                <td>
                                    <form method="POST" class="role-update-form">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <div class="form-action-group">
                                            <select name="new_role" class="role-select" <?= $isSelf ? 'disabled' : '' ?>>
                                                <option value="customer" <?= ($user['role'] == 'customer') ? 'selected' : '' ?>>Khách hàng</option>
                                                <option value="support" <?= ($user['role'] == 'support') ? 'selected' : '' ?>>Hỗ trợ (Support)</option>
                                                <option value="sales" <?= ($user['role'] == 'sales') ? 'selected' : '' ?>>Bán hàng (Sales)</option>
                                                <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Quản trị viên</option>
                                            </select>
                                            
                                            <?php if (!$isSelf): ?>
                                            <button type="submit" name="update_role" class="btn-update-role">
                                                <i class="fa-solid fa-user-shield"></i> Cập nhật
                                            </button>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fa-solid fa-lock"></i> Tài khoản của bạn</span>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS đồng bộ với các trang quản trị Premium Monochrome */
    .manage-users {
        padding: 50px 0 80px;
        animation: fadeIn 0.8s ease;
    }

    .manage-users__header {
        text-align: center;
        margin-bottom: 40px;
    }

    .manage-users__title {
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--apple-black);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .manage-users__subtitle {
        font-size: 1.6rem;
        color: var(--apple-grey);
    }

    .manage-users__card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    /* Bảng dữ liệu */
    .manage-table-wrapper { overflow-x: auto; }
    .manage-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1.4rem;
    }
    .manage-table th {
        text-align: left;
        padding: 18px 15px;
        background: #f8f9fa;
        color: #777;
        font-weight: 700;
        border-bottom: 2px solid #eee;
    }
    .manage-table td {
        padding: 20px 15px;
        border-bottom: 1px solid #f2f2f5;
        vertical-align: middle;
    }
    .manage-table tr:hover { background: #fafafa; }

    /* Badge phong cách Admin */
    .badge {
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        display: inline-block;
    }
    .badge--admin { background: #1a1a1a; color: #fff; }
    .badge--sales { background: #e0e7ff; color: #4338ca; }
    .badge--support { background: #fef3c7; color: #d97706; }
    .badge--customer { background: #f3f4f6; color: #4b5563; }

    /* Form thao tác */
    .form-action-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .role-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1.5px solid #eee;
        background: #fdfdfd;
        font-size: 1.3rem;
        outline: none;
        transition: 0.3s;
        cursor: pointer;
    }
    .role-select:focus { border-color: var(--primary-purple); }
    .role-select:disabled { background: #f5f5f5; cursor: not-allowed; color: #aaa; }

    .btn-update-role {
        background: var(--apple-black);
        color: #fff;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.3rem;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-update-role:hover {
        background: var(--primary-purple);
        transform: translateY(-2px);
    }

    .text-muted { color: #aaa; font-size: 1.3rem; }
    .italic { font-style: italic; }

    @media (max-width: 768px) {
        .manage-users__title { font-size: 2.8rem; }
        .form-action-group { flex-direction: column; align-items: flex-start; }
    }
</style>

<?php include $base_dir . 'includes/footer.php'; ?>
