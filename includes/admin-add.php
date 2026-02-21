<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Thêm Trang Phục</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
        <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ</a>            <h1>Thêm Trang Phục Mới</h1>
        </header>

        <form action="process-add.php" method="POST" enctype="multipart/form-data" class="admin-form">            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" name="name" placeholder="VD: Áo Hoodie Streetwear" required>
            </div>

            <div class="form-group">
                <label>Loại đồ:</label>
                <select name="type" required>
                    <option value="top">Áo (Top)</option>
                    <option value="bottom">Quần (Bottom)</option>
                    <option value="shoes">Giày (Shoes)</option>
                    <option value="accessories">Phụ kiện (Accessories)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Giới tính:</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="gender[]" value="male"> Nam</label>
                    <label><input type="checkbox" name="gender[]" value="female"> Nữ</label>
                </div>
            </div>

            <div class="form-group">
                <label>Dịp sử dụng:</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="occasion[]" value="study"> Đi học</label>
                    <label><input type="checkbox" name="occasion[]" value="goout"> Đi chơi</label>
                    <label><input type="checkbox" name="occasion[]" value="date"> Hẹn hò</label>
                </div>
            </div>

            <div class="form-group">
                <label>Phong cách:</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="style[]" value="basic"> Basic</label>
                    <label><input type="checkbox" name="style[]" value="street"> Streetwear</label>
                    <label><input type="checkbox" name="style[]" value="vintage"> Vintage</label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tông màu:</label>
                    <select name="color" required>
                        <option value="dark">Tối (Dark)</option>
                        <option value="light">Sáng (Light)</option>
                        <option value="colorful">Đa sắc (Colorful)</option>
                        <option value="pastel">Pastel</option>
                        <option value="neutral">Trung tính (Neutral)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Độ rộng (Fit):</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="fit[]" value="oversized"> Oversized</label>
                        <label><input type="checkbox" name="fit[]" value="regular"> Regular</label>
                        <label><input type="checkbox" name="fit[]" value="slim"> Slim</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Thời tiết phù hợp:</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="weather[]" value="hot"> Nóng</label>
                    <label><input type="checkbox" name="weather[]" value="mild"> Mát mẻ</label>
                    <label><input type="checkbox" name="weather[]" value="cold"> Lạnh</label>
                </div>
            </div>

            <div class="form-group">
                <label>Hình ảnh sản phẩm:</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <button type="submit" class="btn-submit">Lưu trang phục ⭐</button>
        </form>
    </div>
</body>
</html>