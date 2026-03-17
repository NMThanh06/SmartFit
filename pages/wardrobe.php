<?php
session_start();
require_once '../includes/config.php';

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Xử lý lấy danh sách trang phục đã lưu của User hiện tại
$saved_outfits = [];
$my_items = [];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // 1. SQL lấy bộ đồ đã phối (saved_outfits)
    $sql = "SELECT so.id as saved_id, so.style_name,
                   t.name as top_name, (SELECT image FROM outfit_colors WHERE outfit_id = t.id LIMIT 1) as top_img,
                   b.name as bottom_name, (SELECT image FROM outfit_colors WHERE outfit_id = b.id LIMIT 1) as bottom_img,
                   s.name as shoes_name, (SELECT image FROM outfit_colors WHERE outfit_id = s.id LIMIT 1) as shoes_img,
                   a.name as acc_name, (SELECT image FROM outfit_colors WHERE outfit_id = a.id LIMIT 1) as acc_img,
                   op.name as onepiece_name, (SELECT image FROM outfit_colors WHERE outfit_id = op.id LIMIT 1) as onepiece_img
            FROM saved_outfits so
            LEFT JOIN outfits t ON so.top_id = t.id
            LEFT JOIN outfits b ON so.bottom_id = b.id
            JOIN outfits s ON so.shoes_id = s.id
            LEFT JOIN outfits a ON so.acc_id = a.id
            LEFT JOIN outfits op ON so.onepiece_id = op.id
            WHERE so.user_id = ?
            ORDER BY so.created_at DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $saved_outfits[] = $row;
        }
        mysqli_stmt_close($stmt);
    }

    // 2. SQL lấy món đồ cá nhân (My Items)
    $sql_my_items = "SELECT o.*, (SELECT c.image FROM outfit_colors c WHERE c.outfit_id = o.id LIMIT 1) as image 
                     FROM outfits o 
                     WHERE o.owner_id = ? AND o.is_commercial = 0 
                     ORDER BY o.id DESC";
    $stmt_my = mysqli_prepare($conn, $sql_my_items);
    if ($stmt_my) {
        mysqli_stmt_bind_param($stmt_my, "i", $userId);
        mysqli_stmt_execute($stmt_my);
        $res_my = mysqli_stmt_get_result($stmt_my);
        while ($row = mysqli_fetch_assoc($res_my)) {
            $my_items[] = $row;
        }
        mysqli_stmt_close($stmt_my);
    }
}

// 3. Xử lý Form thêm/sửa đồ cá nhân (Submit POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_personal_item'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Bạn cần đăng nhập để thực hiện tính năng này!";
    }
    else {
        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $color_name = mysqli_real_escape_string($conn, $_POST['color_name']);
        $hex_code = mysqli_real_escape_string($conn, $_POST['hex_code']);

        // Phân loại (JSON)
        $gender = json_encode($_POST['gender'] ?? [], JSON_UNESCAPED_UNICODE);
        $occasion = json_encode($_POST['occasion'] ?? [], JSON_UNESCAPED_UNICODE);
        $style = json_encode($_POST['style'] ?? [], JSON_UNESCAPED_UNICODE);
        $weather = json_encode($_POST['weather'] ?? [], JSON_UNESCAPED_UNICODE);
        $fit = json_encode($_POST['fit'] ?? [], JSON_UNESCAPED_UNICODE);

        mysqli_begin_transaction($conn);
        try {
            if ($itemId > 0) {
                // UPDATE: Kiểm tra xem item có thuộc về user hiện tại không
                $checkSql = "SELECT id FROM outfits WHERE id = ? AND owner_id = ?";
                $checkStmt = mysqli_prepare($conn, $checkSql);
                mysqli_stmt_bind_param($checkStmt, "ii", $itemId, $userId);
                mysqli_stmt_execute($checkStmt);
                if (mysqli_num_rows(mysqli_stmt_get_result($checkStmt)) === 0) {
                    throw new Exception("Bạn không có quyền chỉnh sửa mục này!");
                }

                // Cập nhật bảng outfits
                $sql_upd = "UPDATE outfits SET name = ?, type = ?, gender = ?, occasion = ?, style = ?, weather = ?, fit = ? WHERE id = ?";
                $stmt_upd = mysqli_prepare($conn, $sql_upd);
                mysqli_stmt_bind_param($stmt_upd, "sssssssi", $name, $type, $gender, $occasion, $style, $weather, $fit, $itemId);
                if (!mysqli_stmt_execute($stmt_upd))
                    throw new Exception("Lỗi cập nhật thông tin: " . mysqli_error($conn));

                // Cập nhật bảng outfit_colors (Tên màu và Hex)
                $sql_col_upd = "UPDATE outfit_colors SET color_name = ?, hex_code = ? WHERE outfit_id = ?";
                $stmt_col_upd = mysqli_prepare($conn, $sql_col_upd);
                mysqli_stmt_bind_param($stmt_col_upd, "ssi", $color_name, $hex_code, $itemId);
                mysqli_stmt_execute($stmt_col_upd);

                // Nếu có upload ảnh mới
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $new_filename = time() . "_user_" . $userId . "_closet." . $ext;
                    $upload_dir = "../assets/img/outfits/";
                    if (!is_dir($upload_dir))
                        mkdir($upload_dir, 0777, true);

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                        $image_path = "/SmartFit/assets/img/outfits/" . $new_filename;
                        $sql_img = "UPDATE outfit_colors SET image = ? WHERE outfit_id = ?";
                        $stmt_img = mysqli_prepare($conn, $sql_img);
                        mysqli_stmt_bind_param($stmt_img, "si", $image_path, $itemId);
                        mysqli_stmt_execute($stmt_img);
                    }
                }
                $msg = "Đã cập nhật thông tin món đồ!";
            }
            else {
                // INSERT mới
                $sql_ins = "INSERT INTO outfits (name, type, gender, occasion, style, weather, fit, price, is_commercial, owner_id, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?, NOW())";
                $stmt_ins = mysqli_prepare($conn, $sql_ins);
                mysqli_stmt_bind_param($stmt_ins, "sssssssi", $name, $type, $gender, $occasion, $style, $weather, $fit, $userId);
                if (!mysqli_stmt_execute($stmt_ins))
                    throw new Exception("Lỗi lưu thông tin: " . mysqli_error($conn));
                $outfit_id = mysqli_insert_id($conn);

                $image_path = '/SmartFit/assets/img/default-placeholder.jpg';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $new_filename = time() . "_user_" . $userId . "_closet." . $ext;
                    $upload_dir = "../assets/img/outfits/";
                    if (!is_dir($upload_dir))
                        mkdir($upload_dir, 0777, true);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                        $image_path = "/SmartFit/assets/img/outfits/" . $new_filename;
                    }
                }

                $sql_col = "INSERT INTO outfit_colors (outfit_id, color_name, hex_code, image) VALUES (?, ?, ?, ?)";
                $stmt_col = mysqli_prepare($conn, $sql_col);
                mysqli_stmt_bind_param($stmt_col, "isss", $outfit_id, $color_name, $hex_code, $image_path);
                mysqli_stmt_execute($stmt_col);
                $msg = "Đã thêm món đồ mới vào tủ đồ!";
            }

            mysqli_commit($conn);
            $_SESSION['success'] = $msg;
            header("Location: wardrobe.php");
            exit;
        }
        catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}

// Map loại đồ sang tiếng Việt
$typeMap = [
    'top' => 'Áo',
    'bottom' => 'Quần',
    'one-piece' => 'Trang phục nguyên bộ',
    'shoes' => 'Giày',
    'accessory' => 'Phụ kiện'
];

// Include Header (Đã bao gồm session_start, config, toast và head/navbar)
include '../includes/header.php';
?>

<main class="wardrobe-page">
    <div class="grid wide">
        <div class="wardrobe-page__header">
            <a href="../shop.php" class="wardrobe-page__back">
                <i class="fa-solid fa-chevron-left"></i>
                Quay lại cửa hàng
            </a>
            <div class="wardrobe-page__header-flex">
                <div>
                    <h1 class="wardrobe-page__title">Tủ đồ của tôi</h1>
                    <p class="wardrobe-page__subtitle">Quản lý phong cách và tủ đồ cá nhân của bạn</p>
                </div>
                <button class="btn-primary" onclick="openAddModal()" style="background-color: #007aff;">
                    <i class="fa-solid fa-plus"></i> Thêm món đồ cá nhân
                </button>
            </div>
        </div>

        <!-- Tab Switcher -->
        <div class="wardrobe-tabs">
            <div class="wardrobe-tab active" onclick="switchTab(this, 'saved-section')">
                <i class="fa-solid fa-layer-group"></i> Bộ đồ đã phối
            </div>
            <div class="wardrobe-tab" onclick="switchTab(this, 'personal-section')">
                <i class="fa-solid fa-shirt"></i> Tủ đồ cá nhân
            </div>
        </div>

        <!-- Section 1: Saved Outfits -->
        <div id="saved-section" class="wardrobe-section active">
            <div class="row">
                <?php if (empty($saved_outfits)): ?>
                    <div class="col l-12">
                        <div class="wardrobe-empty">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <p>Bạn chưa lưu bộ trang phục nào từ công cụ phối đồ.</p>
                            <a href="../style_outfits.php" class="btn-primary">Thử phối đồ ngay</a>
                        </div>
                    </div>
                <?php
else: ?>
                    <?php foreach ($saved_outfits as $outfit): ?>
                        <div class="col l-3 m-6 c-12">
                            <div class="wardrobe-card">
                                <div class="wardrobe-card__gallery">
                                    <?php if (!empty($outfit['onepiece_name'])): ?>
                                        <div class="wardrobe-card__img" style="flex: 1;">
                                            <img src="<?php echo htmlspecialchars($outfit['onepiece_img'] ?? '/SmartFit/assets/img/default-placeholder.jpg'); ?>" alt="One-piece" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                                        </div>
                                    <?php else: ?>
                                        <div class="wardrobe-card__img">
                                            <img src="<?php echo htmlspecialchars($outfit['top_img'] ?? '/SmartFit/assets/img/default-placeholder.jpg'); ?>" alt="Áo" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                                        </div>
                                        <div class="wardrobe-card__img">
                                            <img src="<?php echo htmlspecialchars($outfit['bottom_img'] ?? '/SmartFit/assets/img/default-placeholder.jpg'); ?>" alt="Quần" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="wardrobe-card__body">
                                    <div class="wardrobe-card__main">
                                        <h3 class="wardrobe-card__name">
                                            <?php echo htmlspecialchars($outfit['style_name'] ?: 'Style của tôi'); ?>
                                        </h3>
                                        <div class="wardrobe-card__desc">
                                            <?php if (!empty($outfit['onepiece_name'])): ?>
                                                <p><span>Đồ bộ:</span> <?php echo htmlspecialchars($outfit['onepiece_name']); ?></p>
                                            <?php else: ?>
                                                <p><span>Áo:</span> <?php echo htmlspecialchars($outfit['top_name']); ?></p>
                                                <p><span>Quần:</span> <?php echo htmlspecialchars($outfit['bottom_name']); ?></p>
                                            <?php endif; ?>
                                            <p><span>Giày:</span> <?php echo htmlspecialchars($outfit['shoes_name']); ?></p>
                                            <?php if (!empty($outfit['acc_name'])): ?>
                                                <p><span>Phụ kiện:</span> <?php echo htmlspecialchars($outfit['acc_name']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <button class="wardrobe-card__btn-delete" 
                                            onclick="app.deleteSavedOutfit(<?php echo $outfit['saved_id']; ?>, this)"
                                            title="Xóa bộ đồ đã lưu">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                <?php
endif; ?>
            </div>
        </div>

        <!-- Section 2: My Personal Items -->
        <div id="personal-section" class="wardrobe-section">
            <div class="row">
                <?php if (empty($my_items)): ?>
                    <div class="col l-12">
                        <div class="wardrobe-empty">
                            <i class="fa-solid fa-camera-retro"></i>
                            <p>Bạn chưa tải lên món đồ cá nhân nào.</p>
                            <button class="btn-primary" onclick="openAddModal()">Tải lên món đồ đầu tiên</button>
                        </div>
                    </div>
                <?php
else: ?>
                    <?php foreach ($my_items as $item): ?>
                        <div class="col l-2-4 m-4 c-6">
                            <div class="personal-item-card" onclick='openEditModal(<?php echo json_encode($item); ?>)'>
                                <div class="personal-item-card__img">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" onerror="this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                                </div>
                                <div class="personal-item-card__info">
                                    <h3 class="personal-item-card__name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <span class="personal-item-card__type"><?php echo $typeMap[$item['type']] ?? 'Khác'; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                <?php
endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Thêm đồ cá nhân -->
<div id="addPersonalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle"><i class="fa-solid fa-plus-circle"></i> Thêm món đồ cá nhân</h2>
            <span class="close-modal" onclick="closeAddModal()">&times;</span>
        </div>
        <form action="wardrobe.php" method="POST" enctype="multipart/form-data" class="personal-form">
            <input type="hidden" name="item_id" id="item_id" value="0">
            <div class="row">
                <div class="col l-6 m-12 c-12">
                    <div class="form-group">
                        <label>Tên món đồ <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="VD: Áo thun trắng của tôi" required>
                    </div>
                    <div class="form-group">
                        <label>Loại sản phẩm <span class="required">*</span></label>
                        <select name="type" required>
                            <option value="top">Áo (Top)</option>
                            <option value="bottom">Quần (Bottom)</option>
                            <option value="one-piece">Trang phục nguyên bộ (One-piece)</option>
                            <option value="shoes">Giày (Shoes)</option>
                            <option value="accessory">Phụ kiện (Accessory)</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col l-6">
                            <div class="form-group">
                                <label>Tên màu</label>
                                <input type="text" name="color_name" placeholder="VD: Trắng" oninput="suggestHex(this)">
                            </div>
                        </div>
                        <div class="col l-6">
                            <div class="form-group">
                                <label>Mã màu (Hex)</label>
                                <div class="color-picker-group">
                                    <input type="text" name="hex_code" id="hex_code_text" value="#ffffff" placeholder="#FFFFFF" oninput="syncColorPicker(this.value)">
                                    <input type="color" id="hex_code_picker" value="#ffffff" oninput="syncColorText(this.value)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col l-6 m-12 c-12">
                    <div class="form-group">
                        <label>Ảnh sản phẩm (Chụp hoặc tải lên) <span class="required">*</span></label>
                        <div class="upload-box" id="uploadBox" onclick="document.getElementById('personal-file').click()">
                            <div class="upload-box__placeholder" id="uploadPlaceholder">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <p>Nhấn để chọn ảnh hoặc chụp</p>
                            </div>
                            <div class="upload-box__preview" id="uploadPreview" style="display:none;">
                                <img src="" alt="Preview" id="uploadPreviewImg">
                            </div>
                            <p class="upload-box__filename" id="file-name"></p>
                            <input type="file" id="personal-file" name="image" accept="image/*" capture="environment" hidden required onchange="previewUploadImage(this)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-divider">Phân loại thông minh (Cho AI gợi ý)</div>
            
            <div class="row">
                <div class="col l-4 m-6 c-12">
                    <label class="form-label-small">Giới tính</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="gender[]" value="male"> Nam</label>
                        <label><input type="checkbox" name="gender[]" value="female"> Nữ</label>
                    </div>
                </div>
                <div class="col l-4 m-6 c-12">
                    <label class="form-label-small">Dịp mặc</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="occasion[]" value="study"> Đi học</label>
                        <label><input type="checkbox" name="occasion[]" value="goout"> Đi chơi</label>
                        <label><input type="checkbox" name="occasion[]" value="date"> Hẹn hò</label>
                    </div>
                </div>
                <div class="col l-4 m-6 c-12">
                    <label class="form-label-small">Phong cách</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="style[]" value="basic"> Basic</label>
                        <label><input type="checkbox" name="style[]" value="street"> Street</label>
                        <label><input type="checkbox" name="style[]" value="vintage"> Vintage</label>
                    </div>
                </div>
                <div class="col l-6 m-6 c-12">
                    <label class="form-label-small">Thời tiết</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="weather[]" value="hot"> Nóng</label>
                        <label><input type="checkbox" name="weather[]" value="mild"> Mát</label>
                        <label><input type="checkbox" name="weather[]" value="cold"> Lạnh</label>
                    </div>
                </div>
                <div class="col l-6 m-6 c-12">
                    <label class="form-label-small">Độ rộng</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="fit[]" value="regular"> Vừa</label>
                        <label><input type="checkbox" name="fit[]" value="slim"> Ôm</label>
                        <label><input type="checkbox" name="fit[]" value="oversized"> Rộng</label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">Hủy bỏ</button>
                <button type="submit" name="save_personal_item" class="btn-primary" id="btnSubmit" style="background-color: #007aff;">Lưu món đồ</button>
            </div>
        </form>
    </div>
</div>

<style>
    .wardrobe-page { padding: 30px 0 60px; background-color: #f8f9fa; min-height: 100vh; }
    .wardrobe-page__header { margin-bottom: 25px; }
    .wardrobe-page__back { display: flex; align-items: center; gap: 8px; color: #86868b; text-decoration: none; font-size: 1.4rem; font-weight: 500; margin-bottom: 30px; transition: color 0.2s; }
    .wardrobe-page__back:hover { color: #1d1d1f; }
    .wardrobe-page__header-flex { display: flex; justify-content: space-between; align-items: flex-end; }
    .wardrobe-page__title { font-size: 3.2rem; font-weight: 800; color: #1d1d1f; margin-bottom: 15px; }
    .wardrobe-page__subtitle { font-size: 1.6rem; color: #86868b; }
    
    /* Tabs và các phần khác giữ nguyên logic nhưng có thể tinh chỉnh màu sắc */
    .wardrobe-tabs { display: flex; gap: 10px; margin: 30px 0; border-bottom: 1px solid #e5e5e5; }
    .wardrobe-tab { padding: 12px 25px; font-size: 1.5rem; font-weight: 600; color: #86868b; cursor: pointer; transition: 0.3s; position: relative; display: flex; align-items: center; gap: 8px; }
    .wardrobe-tab:hover { color: #1d1d1f; }
    .wardrobe-tab.active { color: #007aff; }
    .wardrobe-tab.active::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 100%; height: 3px; background: #007aff; border-radius: 3px; }

    .wardrobe-section { display: none; }
    .wardrobe-section.active { display: block; animation: fadeIn 0.4s ease; }

    /* Personal Item Card */
    .personal-item-card { background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #f0f0f0; transition: 0.3s; margin-bottom: 20px; cursor: pointer; }
    .personal-item-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #007aff; }
    .personal-item-card__link { text-decoration: none; color: inherit; }
    .personal-item-card__img { width: 100%; aspect-ratio: 1; overflow: hidden; background: #f9f9f9; }
    .personal-item-card__img img { width: 100%; height: 100%; object-fit: cover; }
    .personal-item-card__info { padding: 12px; text-align: center; }
    .personal-item-card__name { font-size: 1.4rem; font-weight: 600; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .personal-item-card__type { font-size: 1.2rem; color: #86868b; display: block; }

    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    .modal-content { background: #fff; margin: 5% auto; padding: 30px; border-radius: 24px; width: 700px; max-width: 95%; box-shadow: 0 20px 40px rgba(0,0,0,0.2); animation: scaleUp 0.3s ease; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .modal-header h2 { font-size: 2.2rem; font-weight: 700; color: #1d1d1f; }
    .close-modal { font-size: 2.8rem; cursor: pointer; color: #86868b; }
    
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-size: 1.4rem; font-weight: 600; margin-bottom: 8px; color: #1d1d1f; }
    .form-group input, .form-group select { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid #ddd; font-size: 1.4rem; outline: none; transition: 0.3s; }
    .form-group input:focus { border-color: #007aff; box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1); }
    
    .color-picker-group { display: flex; gap: 8px; align-items: center; }
    .color-picker-group input[type="text"] { flex: 1; }
    .color-picker-group input[type="color"] { width: 45px; height: 45px; padding: 2px; border: 1px solid #ddd; border-radius: 10px; cursor: pointer; background: #fff; }
    .color-picker-group input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    .color-picker-group input[type="color"]::-webkit-color-swatch { border: none; border-radius: 8px; }
    
    .upload-box { border: 2px dashed #ddd; border-radius: 15px; padding: 40px 20px; text-align: center; cursor: pointer; transition: 0.3s; background: #fafafa; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .upload-box:hover { border-color: #007aff; background: #f0f7ff; }
    .upload-box i { font-size: 3rem; color: #007aff; margin-bottom: 10px; }
    .upload-box p { font-size: 1.3rem; color: #86868b; margin: 0; }
    .upload-box__preview { display: flex; justify-content: center; align-items: center; }
    .upload-box__preview img { max-width: 100%; max-height: 180px; border-radius: 12px; object-fit: cover; border: 2px solid #e0e0e0; }
    .upload-box__filename { font-size: 1.2rem; color: #86868b; margin-top: 8px; word-break: break-all; }

    .form-divider { margin: 20px 0 15px; font-size: 1.4rem; font-weight: 700; color: #86868b; border-bottom: 1px solid #eee; padding-bottom: 5px; }
    .form-label-small { font-size: 1.3rem; font-weight: 700; margin-bottom: 8px; display: block; }
    .checkbox-group { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    .checkbox-group label { font-size: 1.3rem; background: #f5f5f7; padding: 8px 16px; border-radius: 20px; cursor: pointer; transition: 0.2s; border: 1px solid #e5e5e7; user-select: none; }
    .checkbox-group input { display: none; }
    .checkbox-group label:has(input:checked) { background: #007aff; color: #fff; border-color: #007aff; border: none; }

    .modal-footer { margin-top: 30px; display: flex; justify-content: flex-end; gap: 15px; border-top: 1px solid #eee; padding-top: 20px; }
    .btn-cancel { padding: 12px 25px; border: none; background: #f5f5f7; color: #1d1d1f; border-radius: 30px; font-weight: 600; cursor: pointer; }
    .required { color: #ff3b30; }

    @keyframes scaleUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<script>
    const colorMap = {
        'đen': '#000000', 'trắng': '#FFFFFF', 'đỏ': '#FF0000', 'vàng': '#FFFF00',
        'xanh lá': '#008000', 'xanh dương': '#0000FF', 'xanh lam': '#0000FF',
        'cam': '#FFA500', 'tím': '#800080', 'hồng': '#FFC0CB', 'xám': '#808080',
        'nâu': '#3f2929ff', 'kem': '#d8d7c7ff', 'be': '#F5F5DC'
    };

    function suggestHex(input) {
        const hexText = document.getElementById('hex_code_text');
        const hexPicker = document.getElementById('hex_code_picker');
        const colorName = input.value.toLowerCase().trim();
        if (colorMap[colorName]) {
            hexText.value = colorMap[colorName];
            hexPicker.value = colorMap[colorName];
        }
    }

    function syncColorPicker(val) {
        const picker = document.getElementById('hex_code_picker');
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            picker.value = val;
        }
    }

    function syncColorText(val) {
        const text = document.getElementById('hex_code_text');
        text.value = val.toUpperCase();
    }

    function switchTab(btn, sectionId) {
        document.querySelectorAll('.wardrobe-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.wardrobe-section').forEach(s => s.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(sectionId).classList.add('active');
    }

    function openAddModal() {
        const form = document.querySelector('.personal-form');
        form.reset();
        document.getElementById('item_id').value = '0';
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-plus-circle"></i> Thêm món đồ cá nhân';
        document.getElementById('btnSubmit').innerText = 'Lưu món đồ';
        resetUploadPreview();
        document.getElementById('addPersonalModal').style.display = 'block';
    }

    function openEditModal(item) {
        const form = document.querySelector('.personal-form');
        form.reset();
        
        document.getElementById('item_id').value = item.id;
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa món đồ';
        document.getElementById('btnSubmit').innerText = 'Cập nhật món đồ';
        
        form.querySelector('[name="name"]').value = item.name;
        form.querySelector('[name="type"]').value = item.type;
        form.querySelector('[name="color_name"]').value = item.color_name || '';
        
        const hexVal = item.hex_code || '#ffffff';
        document.getElementById('hex_code_text').value = hexVal.toUpperCase();
        document.getElementById('hex_code_picker').value = hexVal;
        
        // Populate JSON fields (gender, occasion, style, weather, fit)
        ['gender', 'occasion', 'style', 'weather', 'fit'].forEach(key => {
            try {
                const values = JSON.parse(item[key] || '[]');
                values.forEach(v => {
                    const cb = form.querySelector(`input[name="${key}[]"][value="${v}"]`);
                    if (cb) cb.checked = true;
                });
            } catch(e) {}
        });

        // Hiển ảnh hiện tại của món đồ làm preview
        if (item.image) {
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('uploadPreview').style.display = 'flex';
            document.getElementById('uploadPreviewImg').src = item.image;
            document.getElementById('file-name').innerText = 'Để trống nếu không muốn đổi ảnh';
        } else {
            resetUploadPreview();
            document.getElementById('file-name').innerText = 'Để trống nếu không muốn đổi ảnh';
        }
        form.querySelector('#personal-file').required = false;

        document.getElementById('addPersonalModal').style.display = 'block';
    }

    function closeAddModal() {
        document.getElementById('addPersonalModal').style.display = 'none';
    }

    // Reset upload preview về trạng thái ban đầu
    function resetUploadPreview() {
        document.getElementById('uploadPlaceholder').style.display = '';
        document.getElementById('uploadPreview').style.display = 'none';
        document.getElementById('uploadPreviewImg').src = '';
        document.getElementById('file-name').innerText = '';
    }

    // Xem trước ảnh khi chọn file
    function previewUploadImage(input) {
        if (!input.files || !input.files[0]) {
            resetUploadPreview();
            return;
        }

        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('uploadPlaceholder').style.display = 'none';
            document.getElementById('uploadPreview').style.display = 'flex';
            document.getElementById('uploadPreviewImg').src = e.target.result;
            document.getElementById('file-name').innerText = file.name;
        };
        reader.readAsDataURL(file);
    }

    // Đóng modal khi click ra ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('addPersonalModal');
        if (event.target == modal) closeAddModal();
    }
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>