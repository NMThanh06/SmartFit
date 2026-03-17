<?php
require_once '../middleware.php'; // Kích hoạt RBAC Middleware
$base_dir = '../';
require_once $base_dir . 'includes/config.php';
require_once $base_dir . 'includes/functions.php';

// --- PHẦN XỬ LÝ PHP (BACKEND) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // Bắt đầu Transaction
    mysqli_begin_transaction($conn);

    try {
        $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
        $current_user_id = $_SESSION['user_id'];
        $is_admin = ($_SESSION['role'] === 'admin');
        
        // 1. Thu thập thông tin chung
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = (int) $_POST['price'];
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $age = mysqli_real_escape_string($conn, $_POST['age']);
        $seller_note = mysqli_real_escape_string($conn, $_POST['seller_note']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $gender = json_encode($_POST['gender'] ?? [], JSON_UNESCAPED_UNICODE);
        $occasion = json_encode($_POST['occasion'] ?? [], JSON_UNESCAPED_UNICODE);
        $style = json_encode($_POST['style'] ?? [], JSON_UNESCAPED_UNICODE);
        $weather = json_encode($_POST['weather'] ?? [], JSON_UNESCAPED_UNICODE);
        $fit = json_encode($_POST['fit'] ?? [], JSON_UNESCAPED_UNICODE);

        if ($edit_id == 0) {
            // Kiểm tra xem đã có sản phẩm trùng tên chưa (Logic Nhập thêm hàng)
            $check_name_sql = "SELECT id FROM outfits WHERE LOWER(name) = LOWER('$name') LIMIT 1";
            $check_res = mysqli_query($conn, $check_name_sql);
            if ($row = mysqli_fetch_assoc($check_res)) {
                $edit_id = (int)$row['id'];
            }
        }

        if ($edit_id > 0) {
            // Kiểm tra quyền sở hữu trước khi cập nhật
            if (!$is_admin) {
                $check_owner = mysqli_query($conn, "SELECT owner_id FROM outfits WHERE id = $edit_id");
                $owner_data = mysqli_fetch_assoc($check_owner);
                if ($owner_data['owner_id'] != $current_user_id) {
                    throw new Exception("Bạn không có quyền chỉnh sửa sản phẩm này!");
                }
            }

            $sql_outfit = "UPDATE outfits SET 
                            name='$name', price=$price, type='$type', 
                            gender='$gender', occasion='$occasion', style='$style', 
                            weather='$weather', fit='$fit', age='$age', 
                            seller_note='$seller_note', description='$description' 
                           WHERE id = $edit_id";
            if (!mysqli_query($conn, $sql_outfit)) throw new Exception("Lỗi cập nhật outfits: " . mysqli_error($conn));
            $outfit_id = $edit_id;

            // Xóa các biến thể cũ (colors & sizes) để chèn lại cái mới từ form
            mysqli_query($conn, "DELETE FROM outfit_sizes WHERE outfit_id = $outfit_id");
            mysqli_query($conn, "DELETE FROM outfit_colors WHERE outfit_id = $outfit_id");
        } else {
            // TRƯỜNG HỢP: THÊM MỚI (INSERT) - Mặc định là sản phẩm của Shop (is_commercial = 1)
            $sql_outfit = "INSERT INTO outfits (name, price, type, gender, occasion, style, weather, fit, age, seller_note, description, created_at, is_commercial, owner_id) 
                           VALUES ('$name', $price, '$type', '$gender', '$occasion', '$style', '$weather', '$fit', '$age', '$seller_note', '$description', NOW(), 1, $current_user_id)";
            if (!mysqli_query($conn, $sql_outfit)) throw new Exception("Lỗi chèn bảng outfits: " . mysqli_error($conn));
            $outfit_id = mysqli_insert_id($conn);
        }

        // 3. Xử lý các khối Màu Sắc (Dùng chung cho cả Thêm/Sửa)
        if (isset($_POST['colors']) && is_array($_POST['colors'])) {
            foreach ($_POST['colors'] as $cIdx => $colorData) {
                $color_name = mysqli_real_escape_string($conn, $colorData['name']);
                $hex_code = mysqli_real_escape_string($conn, $colorData['hex']);
                $image_path = '/SmartFit/assets/img/default-placeholder.jpg';

                // Nếu là Update và không upload ảnh mới, cần giữ ảnh cũ (trong logic đơn giản này ta coi như phải upload hoặc dùng placeholder)
                // Tuy nhiên form hiện tại không gửi link ảnh cũ. Để cải thiện, tôi sẽ cho dùng placeholder nếu không có ảnh mới.
                
                $file_key = "color_images_$cIdx";
                if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
                    $new_filename = time() . "_outfit_" . $outfit_id . "_color_" . $cIdx . "." . $ext;
                    $upload_path = $base_dir . "assets/img/" . $new_filename;

                    if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_path)) {
                        $image_path = "/SmartFit/assets/img/" . $new_filename;
                    }
                }

                $sql_color = "INSERT INTO outfit_colors (outfit_id, color_name, hex_code, image) 
                               VALUES ($outfit_id, '$color_name', '$hex_code', '$image_path')";
                mysqli_query($conn, $sql_color);
                $color_id = mysqli_insert_id($conn);

                if (isset($colorData['sizes']) && is_array($colorData['sizes'])) {
                    foreach ($colorData['sizes'] as $sizeData) {
                        $size_name = mysqli_real_escape_string($conn, $sizeData['name']);
                        $quantity = (int) $sizeData['qty'];
                        $sql_size = "INSERT INTO outfit_sizes (outfit_id, color_id, size_name, quantity) 
                                     VALUES ($outfit_id, $color_id, '$size_name', $quantity)";
                        mysqli_query($conn, $sql_size);
                    }
                }
            }
        }

        mysqli_commit($conn);

        // ĐỒNG BỘ JSON SAU KHI THÀNH CÔNG
        syncOutfitsToJson($conn);

        $success_msg = ($edit_id > 0) ? "Đã cập nhật/nhập thêm hàng thành công sản phẩm: $name!" : "Sản phẩm đã được thêm thành công!";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_msg = "Lỗi: " . $e->getMessage();
    }
}

include $base_dir . 'includes/header.php';
?>

<div class="web__background--overlay"></div>

<div class="grid wide">
    <?php if (isset($success_msg)): ?>
        <script>document.addEventListener('DOMContentLoaded', () => app.showNotification('<?= $success_msg ?>', 'success'));</script>
    <?php endif; ?>
    <?php if (isset($error_msg)): ?>
        <script>document.addEventListener('DOMContentLoaded', () => app.showNotification('<?= $error_msg ?>', 'error'));</script>
    <?php endif; ?>

    <div class="add-product">
        <div class="add-product__header">
            <h1 class="add-product__title">Quản Lý Sản Phẩm</h1>
            <p class="add-product__subtitle">Hệ thống quản lý thời trang SmartFit AI</p>
        </div>

        <form id="addProductForm" action="manage_products.php" method="POST" enctype="multipart/form-data"
            class="add-product__form">

            <!-- KHỐI 1: THÔNG TIN CHUNG -->
            <div class="add-product__section">
                <h2 class="add-product__section-title"><i class="fa-solid fa-circle-info"></i> Thông tin chung</h2>
                <div class="row">
                    <div class="col l-6 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Tên sản phẩm <span class="required">*</span></label>
                            <input type="text" name="name" class="config-form__input--text"
                                placeholder="VD: Áo Hoodie Streetwear" required>
                        </div>
                    </div>
                    <div class="col l-6 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Giá sản phẩm (VNĐ) <span class="required">*</span></label>
                            <input type="number" name="price" class="config-form__input--text" placeholder="VD: 250000"
                                required>
                        </div>
                    </div>
                    <div class="col l-6 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Khoảng tuổi (VD: 15-20, 21-30, All)</label>
                            <input type="text" name="age" class="config-form__input--text" 
                                placeholder="Nhập số tuổi hoặc 'All'" value="All">
                        </div>
                    </div>
                    <div class="col l-6 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Ghi chú gợi ý phối đồ (AI gợi ý)</label>
                            <input type="text" name="seller_note" class="config-form__input--text"
                                placeholder="VD: Phối hợp đẹp nhất với quần jeans ống rộng">
                        </div>
                    </div>
                    <div class="col l-12 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Mô tả sản phẩm</label>
                            <textarea name="description" class="config-form__textarea" style="height: 100px;"
                                placeholder="Mô tả chất liệu, form dáng..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KHỐI 2: PHÂN LOẠI AI -->
            <div class="add-product__section">
                <h2 class="add-product__section-title"><i class="fa-solid fa-robot"></i> Phân loại thông minh (AI
                    Labels)</h2>
                <div class="row">
                    <div class="col l-4 m-6 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Loại sản phẩm <span class="required">*</span></label>
                            <select name="type" id="productType" class="config-form__select"
                                onchange="handleTypeChange()" required>
                                <option value="" disabled selected>Chọn loại sản phẩm...</option>
                                <option value="top">Áo (Top)</option>
                                <option value="bottom">Quần (Bottom)</option>
                                <option value="one-piece">Trang phục nguyên bộ (One-piece)</option>
                                <option value="shoes">Giày (Shoes)</option>
                                <option value="accessory">Phụ kiện (Accessory)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col l-8 m-6 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Giới tính (Chọn nhiều)</label>
                            <div class="add-product__checkbox-group">
                                <label class="checkbox-container">Nam <input type="checkbox" name="gender[]"
                                        value="male"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Nữ <input type="checkbox" name="gender[]"
                                        value="female"><span class="checkmark"></span></label>
                            </div>
                        </div>
                    </div>

                    <div class="col l-12 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Dịp mặc (Occasion)</label>
                            <div class="add-product__checkbox-group">
                                <label class="checkbox-container">Đi học <input type="checkbox" name="occasion[]"
                                        value="study"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Đi chơi <input type="checkbox" name="occasion[]"
                                        value="goout"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Hẹn hò <input type="checkbox" name="occasion[]"
                                        value="date"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Sự kiện <input type="checkbox" name="occasion[]"
                                        value="event"><span class="checkmark"></span></label>
                            </div>
                        </div>
                    </div>

                    <div class="col l-12 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Phong cách (Style)</label>
                            <div class="add-product__checkbox-group">
                                <label class="checkbox-container">Basic <input type="checkbox" name="style[]"
                                        value="basic"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Streetwear <input type="checkbox" name="style[]"
                                        value="street"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Vintage <input type="checkbox" name="style[]"
                                        value="vintage"><span class="checkmark"></span></label>
                            </div>
                        </div>
                    </div>

                    <div class="col l-12 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Thời tiết (Weather)</label>
                            <div class="add-product__checkbox-group">
                                <label class="checkbox-container">Nóng <input type="checkbox" name="weather[]"
                                        value="hot"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Mát mẻ <input type="checkbox" name="weather[]"
                                        value="mild"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Lạnh <input type="checkbox" name="weather[]"
                                        value="cold"><span class="checkmark"></span></label>
                            </div>
                        </div>
                    </div>

                    <div id="fitSection" class="col l-12 m-12 c-12">
                        <div class="config-form__group">
                            <label class="add-product__label">Độ rộng (Fit)</label>
                            <div class="add-product__checkbox-group">
                                <label class="checkbox-container">Regular <input type="checkbox" name="fit[]"
                                        value="regular"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Slim <input type="checkbox" name="fit[]"
                                        value="slim"><span class="checkmark"></span></label>
                                <label class="checkbox-container">Oversized <input type="checkbox" name="fit[]"
                                        value="oversized"><span class="checkmark"></span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KHỐI 3: BIẾN THỂ MÀU SẮC & KÍCH CỠ -->
            <div class="add-product__section">
                <div class="add-product__section-header">
                    <h2 class="add-product__section-title"><i class="fa-solid fa-palette"></i> Biến thể sản phẩm</h2>
                    <button type="button" class="btn-add-variant" onclick="addColorBlock()"><i
                            class="fa-solid fa-plus"></i> Thêm Màu Mới</button>
                </div>

                <div id="variantContainer" class="variant-container">
                    <!-- Các khối màu sắc sẽ được thêm vào đây bằng Javascript -->
                </div>
            </div>

            <div class="add-product__actions">
                <button type="button" class="btn-secondary" onclick="resetFormToNormal()">Làm mới Form / Thêm mới</button>
                <button type="submit" name="submit" class="btn-primary">Lưu Sản Phẩm Ngay <i
                        class="fa-solid fa-cloud-arrow-up"></i></button>
            </div>
        </form>
    </div>

    <!-- KHỐI 4: DANH SÁCH SẢN PHẨM ĐÃ CÓ -->
    <div class="add-product" style="padding-top: 0;">
        <div class="add-product__section">
            <h2 class="add-product__section-title"><i class="fa-solid fa-list-check"></i> Quản lý sản phẩm hệ thống</h2>
            
            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th> <!-- Column for Expand Chevron -->
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Loại</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="manageProductList">
                        <?php
                        // Lấy danh sách sản phẩm thực tế từ DB - Chỉ lấy đồ của Shop (is_commercial = 1)
                        $current_user_id = $_SESSION['user_id'];
                        $is_admin = ($_SESSION['role'] === 'admin');
                        
                        $list_sql = "SELECT o.id, o.name, o.price, o.type, 
                                    (SELECT c.image FROM outfit_colors c WHERE c.outfit_id = o.id LIMIT 1) as image 
                                    FROM outfits o WHERE o.is_commercial = 1 ";
                        
                        if (!$is_admin) {
                            $list_sql .= " AND o.owner_id = $current_user_id ";
                        }
                        
                        $list_sql .= " ORDER BY o.id DESC";
                        $list_result = mysqli_query($conn, $list_sql);
                        while ($p = mysqli_fetch_assoc($list_result)):
                        ?>
                        <tr id="row_<?= $p['id'] ?>" class="manage-table__row">
                            <td>
                                <button type="button" class="btn-expand-variants" onclick="toggleProductVariants(<?= $p['id'] ?>, this)">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </td>
                            <td>#<?= $p['id'] ?></td>
                            <td><img src="<?= $p['image'] ?: '/SmartFit/assets/img/default-placeholder.jpg' ?>" class="manage-table__img"></td>
                            <td class="manage-table__name"><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= number_format($p['price'], 0, ',', '.') ?>đ</td>
                            <td><?= ucfirst($p['type']) ?></td>
                            <td class="manage-table__actions">
                                <button type="button" class="btn-edit" onclick="editProduct(<?= $p['id'] ?>)">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn-delete" onclick="deleteProduct(<?= $p['id'] ?>)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <!-- Chi tiết Biến thể (Màu & Size) -->
                        <tr id="variants_<?= $p['id'] ?>" class="variants-detail-row" style="display: none;">
                            <td colspan="7">
                                <div class="variants-detail-wrapper">
                                    <table class="variants-subtable">
                                        <thead>
                                            <tr>
                                                <th>Ảnh màu</th>
                                                <th>Tên màu & Mã HEX</th>
                                                <th>Kích cỡ & Tồn kho</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $color_sql = "SELECT id, color_name, hex_code, image FROM outfit_colors WHERE outfit_id = " . $p['id'];
                                            $color_res = mysqli_query($conn, $color_sql);
                                            while ($c = mysqli_fetch_assoc($color_res)):
                                            ?>
                                            <tr>
                                                <td style="width: 80px;">
                                                    <img src="<?= $c['image'] ?>" class="variants-subtable__img">
                                                </td>
                                                <td style="width: 150px;">
                                                    <div class="color-preview-item">
                                                        <span class="color-preview-box" style="background-color: <?= $c['hex_code'] ?>;"></span>
                                                        <strong><?= htmlspecialchars($c['color_name']) ?></strong>
                                                        <small><?= strtoupper($c['hex_code']) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="variant-sizes-list">
                                                        <?php
                                                        $size_sql = "SELECT size_name, quantity FROM outfit_sizes WHERE color_id = " . $c['id'];
                                                        $size_res = mysqli_query($conn, $size_sql);
                                                        while ($s = mysqli_fetch_assoc($size_res)):
                                                        ?>
                                                        <span class="size-badge">
                                                            <strong><?= $s['size_name'] ?></strong>: <?= $s['quantity'] ?>
                                                        </span>
                                                        <?php endwhile; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
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
    /* CSS cho giao diện Thêm sản phẩm - Phong cách Monochrome Premium */
    .add-product {
        padding: 100px 0 60px;
        animation: fadeIn 0.8s ease;
    }

    .add-product__header {
        text-align: center;
        margin-bottom: 40px;
    }

    .add-product__title {
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--apple-black);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .add-product__subtitle {
        font-size: 1.6rem;
        color: var(--apple-grey);
    }

    .add-product__section {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }

    .config-form__group {
        margin-bottom: 25px; /* Cách đều các ô */
    }

    .config-form__input--text,
    .config-form__select,
    .config-form__textarea {
        width: 100%;
        padding: 15px 20px;
        border: 1.5px solid #f0f0f0;
        border-radius: 12px;
        font-size: 1.5rem;
        color: var(--apple-black);
        background-color: #fafafa;
        transition: all 0.3s ease;
        outline: none;
    }

    .config-form__input--text:focus,
    .config-form__select:focus,
    .config-form__textarea:focus {
        border-color: var(--primary-purple);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(124, 92, 252, 0.1);
    }

    .add-product__section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--apple-black);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .add-product__section-title i {
        color: var(--primary-purple);
    }

    .add-product__label {
        display: block;
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--apple-grey);
        margin-bottom: 10px;
    }

    .required {
        color: var(--error);
    }

    /* Custom Checkbox styles */
    .add-product__checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 10px 0;
    }

    .checkbox-container {
        display: block;
        position: relative;
        padding-left: 30px;
        cursor: pointer;
        font-size: 1.4rem;
        user-select: none;
        color: var(--apple-black);
    }

    .checkbox-container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #eee;
        border-radius: 4px;
        transition: all 0.3s;
    }

    .checkbox-container:hover input~.checkmark {
        background-color: #ccc;
    }

    .checkbox-container input:checked~.checkmark {
        background-color: var(--apple-black);
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .checkbox-container input:checked~.checkmark:after {
        display: block;
    }

    .checkbox-container .checkmark:after {
        left: 7px;
        top: 3px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Variant Styles */
    .variant-block {
        border: 1px solid var(--border-color);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        background: #fafafa;
        position: relative;
        animation: slideUp 0.4s ease;
    }

    .variant-block__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 15px;
    }

    .variant-block__title {
        font-size: 1.6rem;
        font-weight: 700;
    }

    .btn-remove {
        background: none;
        border: none;
        color: var(--error);
        cursor: pointer;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .size-row {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
        align-items: flex-end;
    }

    .size-input-group {
        flex: 1;
    }

    .btn-add-size {
        background: none;
        border: 1px solid var(--apple-black);
        color: var(--apple-black);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 1.2rem;
        cursor: pointer;
        margin-top: 10px;
        transition: all 0.3s;
    }

    .btn-add-size:hover {
        background: var(--apple-black);
        color: #fff;
    }

    .add-product__section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-add-variant {
        background: var(--apple-black);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 30px;
        font-size: 1.4rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-add-variant:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .add-product__actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 40px;
    }

    .btn-primary {
        background: var(--primary-purple);
        color: #fff;
        padding: 15px 40px;
        border-radius: 40px;
        border: none;
        font-size: 1.6rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-secondary {
        background: #eee;
        color: #333;
        padding: 15px 40px;
        border-radius: 40px;
        border: none;
        font-size: 1.6rem;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #333;
        transform: translateY(-3px);
    }

    /* Color Picker Styles */
    .color-picker-group { display: flex; gap: 8px; align-items: center; }
    .color-picker-group input[type="text"] { flex: 1; }
    .color-picker-group input[type="color"] { width: 45px; height: 45px; padding: 2px; border: 1px solid #ddd; border-radius: 10px; cursor: pointer; background: #fff; }
    .color-picker-group input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    .color-picker-group input[type="color"]::-webkit-color-swatch { border: none; border-radius: 8px; }
        opacity: 0.9;
        transform: scale(1.05);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Quản lý bảng sản phẩm */
    .manage-table-wrapper {
        overflow-x: auto;
        margin-top: 10px;
    }
    .manage-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1.4rem;
    }
    .manage-table th {
        text-align: left;
        padding: 15px;
        background: #f8f9fa;
        color: var(--apple-grey);
        font-weight: 700;
        border-bottom: 2px solid var(--border-color);
    }
    .manage-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .manage-table__img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #eee;
    }
    .manage-table__name {
        font-weight: 600;
        color: var(--apple-black);
    }
    .manage-table__actions {
        display: flex;
        gap: 10px;
    }
    .btn-edit, .btn-delete {
        padding: 8px 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: 0.3s;
    }
    .btn-edit { background: #e0e7ff; color: #4338ca; }
    .btn-delete { background: #fee2e2; color: #b91c1c; }
    .btn-edit:hover { background: #c7d2fe; }
    .btn-delete:hover { background: #fecaca; }

    /* Dropdown Biến thể (Variants) */
    .btn-expand-variants {
        background: none;
        border: none;
        color: var(--apple-grey);
        cursor: pointer;
        padding: 8px;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-expand-variants:hover { color: var(--apple-black); }
    .btn-expand-variants.active { transform: rotate(180deg); color: var(--primary-purple); }

    .variants-detail-row {
        background: #fdfdfd;
    }
    .variants-detail-wrapper {
        padding: 0 40px 20px 60px;
        overflow: hidden;
        animation: slideDown 0.3s ease;
    }
    .variants-subtable {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        font-size: 1.3rem;
    }
    .variants-subtable th {
        background: #fafafa;
        padding: 10px 15px;
        font-size: 1.2rem;
        color: #999;
        font-weight: 600;
        border-bottom: 1px solid #f0f0f0;
    }
    .variants-subtable td {
        padding: 12px 15px;
        border-bottom: 1px solid #f9f9f9;
        vertical-align: middle;
    }
    .variants-subtable tr:last-child td { border-bottom: none; }
    
    .variants-subtable__img {
        width: 45px;
        height: 45px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #eee;
    }

    .color-preview-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .color-preview-box {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid rgba(0,0,0,0.1);
        margin-bottom: 2px;
    }

    .variant-sizes-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .size-badge {
        background: #f3f3f3;
        color: #555;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 1.2rem;
    }
    .size-badge strong { color: var(--apple-black); }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Trạng thái sửa */
    .edit-mode .add-product__title { color: var(--primary-purple); }
    .edit-mode .btn-primary { background: var(--apple-black); }
</style>

<script>
    // --- LOGIC JAVASCRIPT XỬ LÝ FORM ĐỘNG ---

    let colorCount = 0;

    // Từ điển màu sắc phổ biến (Tiếng Việt & Tiếng Anh)
    const colorMap = {
        'đen': '#000000',
        'trắng': '#FFFFFF',
        'đỏ': '#FF0000',
        'xanh lá': '#008000',
        'xanh dương': '#0000FF',
        'xanh lam': '#0000FF',
        'vàng': '#FFFF00',
        'cam': '#FFA500',
        'tím': '#800080',
        'hồng': '#FFC0CB',
        'xám': '#808080',
        'ghi': '#808080',
        'nâu': '#3f2929ff',
        'kem': '#d8d7c7ff',
        'be': '#F5F5DC',
        'xanh đen': '#000080',
        'than': '#36454F',
        'kaki': '#C3B091',
        'khaki': '#F0E68C',
        'xanh nhạt': '#BCD4E6',
        'xanh đậm': '#1A2A6C',
        'xanh rêu': '#4F633B',
        'đen xám': '#4A4A4A',
        'màu wash': '#91A3B0',
        'black': '#000000',
        'white': '#FFFFFF',
        'red': '#FF0000',
        'green': '#008000',
        'blue': '#0000FF',
        'yellow': '#FFFF00'
    };

    // Hàm tự động điền mã Hex khi nhập tên màu
    function suggestHex(input, cIdx) {
        const hexText = document.getElementById(`hexInput_${cIdx}`);
        const hexPicker = document.getElementById(`hexPicker_${cIdx}`);
        const colorName = input.value.toLowerCase().trim();
        
        if (colorMap[colorName]) {
            hexText.value = colorMap[colorName];
            hexPicker.value = colorMap[colorName];
        }
    }

    function syncColorPicker(cIdx, val) {
        const picker = document.getElementById(`hexPicker_${cIdx}`);
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            picker.value = val;
        }
    }

    function syncColorText(cIdx, val) {
        const text = document.getElementById(`hexInput_${cIdx}`);
        text.value = val.toUpperCase();
    }

    // Hàm thêm một khối màu sắc mới
    function addColorBlock() {
        colorCount++;
        const container = document.getElementById('variantContainer');
        const block = document.createElement('div');
        block.className = 'variant-block';
        block.id = `colorBlock_${colorCount}`;

        // HTML cho khối màu sắc (bao gồm input tên màu, upload ảnh và container chứa size)
        block.innerHTML = `
        <div class="variant-block__header">
            <span class="variant-block__title">Màu sắc #${colorCount}</span>
            <button type="button" class="btn-remove" onclick="removeBlock('colorBlock_${colorCount}')">Xóa màu này</button>
        </div>
        <div class="row">
            <div class="col l-4 m-12 c-12">
                <div class="config-form__group">
                    <label class="add-product__label">Tên màu (VD: Đen, Trắng...)</label>
                    <input type="text" name="colors[${colorCount}][name]" 
                        oninput="suggestHex(this, ${colorCount})"
                        class="config-form__input--text" required>
                </div>
            </div>
            <div class="col l-4 m-12 c-12">
                <div class="config-form__group">
                    <label class="add-product__label">Mã màu HEX (Nhập/Chọn)</label>
                    <div class="color-picker-group">
                        <input type="text" name="colors[${colorCount}][hex]" id="hexInput_${colorCount}" 
                            class="config-form__input--text" placeholder="#000000"
                            oninput="syncColorPicker(${colorCount}, this.value)">
                        <input type="color" id="hexPicker_${colorCount}" value="#ffffff"
                            oninput="syncColorText(${colorCount}, this.value)">
                    </div>
                </div>
            </div>
            <div class="col l-4 m-12 c-12">
                <div class="config-form__group">
                    <label class="add-product__label">Ảnh minh họa màu</label>
                    <input type="file" name="color_images_${colorCount}" class="config-form__input--text" accept="image/*" required>
                </div>
            </div>
        </div>
        <div class="variant-block__sizes">
            <label class="add-product__label">Cấu hình kích cỡ và kho:</label>
            <div id="sizeContainer_${colorCount}">
                <!-- Các dòng size sẽ được thêm vào đây -->
            </div>
            <button type="button" class="btn-add-size" id="btnAddSize_${colorCount}" onclick="addSizeRow(${colorCount})">+ Thêm Size Khác</button>
        </div>
    `;

        container.appendChild(block);

        // Tự động thêm một dòng size đầu tiên
        addSizeRow(colorCount);

        // Cập nhật giao diện theo loại sản phẩm hiện tại
        handleTypeChange();
    }

    // Hàm thêm một dòng kích cỡ cho một màu cụ thể
    function addSizeRow(cIdx) {
        const sContainer = document.getElementById(`sizeContainer_${cIdx}`);
        const sIdx = sContainer.children.length;
        const row = document.createElement('div');
        row.className = 'size-row';
        row.id = `sizeRow_${cIdx}_${sIdx}`;

        const productType = document.getElementById('productType').value;
        let defaultValue = "";

        // Logic tự động tăng size
        if (sIdx > 0) {
            const prevRow = sContainer.children[sIdx - 1];
            const prevInput = prevRow.querySelector('input[name*="[name]"]');
            if (prevInput && !isNaN(prevInput.value) && prevInput.value !== "") {
                defaultValue = parseInt(prevInput.value) + 1;
            }
        } else if (productType === 'shoes') {
            defaultValue = "38";
        } else if (productType === 'accessory') {
            defaultValue = "Oversize";
        }

        let sizeInputHtml = '';
        if (productType === 'shoes') {
            sizeInputHtml = `<input type="number" name="colors[${cIdx}][sizes][${sIdx}][name]" value="${defaultValue}" min="1" class="config-form__input--text">`;
        } else {
            sizeInputHtml = `<input type="text" name="colors[${cIdx}][sizes][${sIdx}][name]" value="${defaultValue}" placeholder="S, M, L, XL, 1, 2..." class="config-form__input--text">`;
        }

        row.innerHTML = `
        <div class="size-input-group">
            <label class="add-product__label" style="font-size:1.1rem">Tên Size</label>
            ${sizeInputHtml}
        </div>
        <div class="size-input-group">
            <label class="add-product__label" style="font-size:1.1rem">Số lượng kho</label>
            <input type="number" name="colors[${cIdx}][sizes][${sIdx}][qty]" value="" min="0" class="config-form__input--text">
        </div>
        ${(productType === 'accessory' || productType !== 'accessory') ? `<button type="button" class="btn-remove" style="margin-bottom:10px" onclick="removeBlock('sizeRow_${cIdx}_${sIdx}')"><i class="fa-solid fa-trash"></i></button>` : ''}
    `;

        sContainer.appendChild(row);
    }

    // Xóa một khối/dòng bất kỳ
    function removeBlock(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    // --- LOGIC XỬ LÝ THEO LOẠI SẢN PHẨM (TYPE) ---
    function handleTypeChange() {
        const type = document.getElementById('productType').value;
        const fitSection = document.getElementById('fitSection');

        // 1. Xử lý phần Fit (Độ rộng)
        if (type === 'accessory' || type === 'shoes') {
            fitSection.style.display = 'none';
        } else {
            fitSection.style.display = 'block';
        }

        // 2. Cập nhật các ô nhập Size đang có trên giao diện
        const allSizeInputs = document.querySelectorAll('input[name*="[sizes]["]');
        allSizeInputs.forEach(input => {
            const isQtyInput = input.name.includes('[qty]');
            if (!isQtyInput) {
                const parentBlock = input.closest('.variant-block');
                const cIdx = parentBlock.id.split('_')[1];
                const btnAddSize = document.getElementById(`btnAddSize_${cIdx}`);

                if (type === 'accessory') {
                    input.type = 'text';
                    input.readOnly = false;
                    input.placeholder = "S, M, L, XL, 1, 2...";
                    if (btnAddSize) btnAddSize.style.display = 'inline-block';
                } else if (type === 'shoes') {
                    input.type = 'number';
                    input.min = "1";
                    input.readOnly = false;
                    if (btnAddSize) btnAddSize.style.display = 'inline-block';
                } else {
                    input.type = 'text';
                    input.readOnly = false;
                    input.placeholder = "S, M, L...";
                    if (btnAddSize) btnAddSize.style.display = 'inline-block';
                }
            }
        });
    }

    // --- QUẢN LÝ SỬA / XÓA ---
    async function deleteProduct(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Thao tác này sẽ xóa toàn bộ biến thể và ảnh liên quan.')) return;

        try {
            const response = await fetch('../includes/api_manage_outfit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: id })
            });
            const data = await response.json();
            if (data.success) {
                app.showNotification('Đã xóa sản phẩm thành công!', 'success');
                document.getElementById(`row_${id}`).remove();
            } else {
                app.showNotification(data.message, 'error');
            }
        } catch (e) {
            app.showNotification('Lỗi kết nối server', 'error');
        }
    }

    async function editProduct(id) {
        // Cuộn lên đầu trang (thấy được title)
        document.querySelector('.add-product').scrollIntoView({ behavior: 'smooth', block: 'start' });

        try {
            const response = await fetch(`../includes/api_manage_outfit.php?action=get_details&id=${id}`);
            const data = await response.json();

            if (data.success) {
                const p = data.product;
                const form = document.getElementById('addProductForm');
                
                // Đánh dấu edit mode
                form.parentElement.classList.add('edit-mode');
                document.querySelector('.add-product__title').innerText = 'Chỉnh Sửa Sản Phẩm';
                document.querySelector('.btn-primary').innerHTML = 'Cập Nhật Sản Phẩm <i class="fa-solid fa-check"></i>';
                
                // Thêm input ẩn để gửi ID
                let idInput = document.getElementById('editProductId');
                if (!idInput) {
                    idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'edit_id';
                    idInput.id = 'editProductId';
                    form.appendChild(idInput);
                }
                idInput.value = id;

                // Điền thông tin cơ bản
                form.querySelector('[name="name"]').value = p.name;
                form.querySelector('[name="price"]').value = p.price;
                form.querySelector('[name="age"]').value = p.age;
                form.querySelector('[name="seller_note"]').value = p.seller_note;
                form.querySelector('[name="description"]').value = p.description;
                form.querySelector('[name="type"]').value = p.type;
                handleTypeChange();

                // Checkboxes
                const setChecks = (name, values) => {
                    form.querySelectorAll(`input[name="${name}[]"]`).forEach(ck => {
                        ck.checked = values.includes(ck.value);
                    });
                };
                setChecks('gender', JSON.parse(p.gender));
                setChecks('occasion', JSON.parse(p.occasion));
                setChecks('style', JSON.parse(p.style));
                setChecks('weather', JSON.parse(p.weather));
                setChecks('fit', JSON.parse(p.fit || '[]'));

                // Biến thể (Khó hơn: Cần render lại khối)
                const container = document.getElementById('variantContainer');
                container.innerHTML = '';
                colorCount = 0;

                p.colors.forEach(c => {
                    addColorBlock();
                    const currentCIdx = colorCount;
                    const block = document.getElementById(`colorBlock_${currentCIdx}`);
                    
                    block.querySelector(`[name="colors[${currentCIdx}][name]"]`).value = c.color_name;
                    const hexInput = block.querySelector(`[name="colors[${currentCIdx}][hex]"]`);
                    const hexPicker = document.getElementById(`hexPicker_${currentCIdx}`);
                    hexInput.value = c.hex_code;
                    if (hexPicker) hexPicker.value = c.hex_code;
                    
                    // Hiển thị ảnh cũ (optional - maybe just placeholder for now since file input can't be set)
                    // We'll skip file input value because it's not possible, but we can show preview label nearby if needed.

                    // Render sizes
                    const sContainer = document.getElementById(`sizeContainer_${currentCIdx}`);
                    sContainer.innerHTML = '';
                    c.sizes.forEach((s, sIdx) => {
                        addSizeRow(currentCIdx);
                        const sRow = document.getElementById(`sizeRow_${currentCIdx}_${sIdx}`);
                        sRow.querySelector(`[name="colors[${currentCIdx}][sizes][${sIdx}][name]"]`).value = s.size_name;
                        sRow.querySelector(`[name="colors[${currentCIdx}][sizes][${sIdx}][qty]"]`).value = s.quantity;
                    });
                });

                app.showNotification('Đã tải dữ liệu xong!', 'success');
            }
        } catch (e) {
            console.error(e);
            app.showNotification('Không thể tải dữ liệu sản phẩm', 'error');
        }
    }

    function resetFormToNormal() {
        const form = document.getElementById('addProductForm');
        form.reset();
        form.parentElement.classList.remove('edit-mode');
        document.querySelector('.add-product__title').innerText = 'Thêm Sản Phẩm Mới';
        document.querySelector('.btn-primary').innerHTML = 'Lưu Sản Phẩm Ngay <i class="fa-solid fa-cloud-arrow-up"></i>';
        
        const idInput = document.getElementById('editProductId');
        if (idInput) idInput.remove();

        const container = document.getElementById('variantContainer');
        container.innerHTML = '';
        colorCount = 0;
        addColorBlock();
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        // Hoặc có thể dùng: document.querySelector('.add-product').scrollIntoView({ behavior: 'smooth', block: 'start' });
        app.showNotification('Đã quay lại chế độ thêm mới', 'info');
    }

    function toggleProductVariants(productId, btn) {
        const detailRow = document.getElementById(`variants_${productId}`);
        const isHidden = detailRow.style.display === 'none';
        
        // Đóng các hàng khác (optional, để giao diện gọn gàng)
        // document.querySelectorAll('.variants-detail-row').forEach(row => row.style.display = 'none');
        // document.querySelectorAll('.btn-expand-variants').forEach(b => b.classList.remove('active'));

        if (isHidden) {
            detailRow.style.display = 'table-row';
            btn.classList.add('active');
        } else {
            detailRow.style.display = 'none';
            btn.classList.remove('active');
        }
    }

    // Khởi chạy khi trang sẵn sàng
    document.addEventListener('DOMContentLoaded', () => {
        // Nếu không có edit_id trong URL thì thêm mặc định (Tránh lỗi logic khi load lại trang)
        if (!document.getElementById('editProductId')) {
            addColorBlock();
        }
    });
</script>

<?php include $base_dir . 'includes/footer.php'; ?>
