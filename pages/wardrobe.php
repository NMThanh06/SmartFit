<?php
session_start();
require_once '../includes/config.php';

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Xử lý lấy danh sách trang phục đã lưu của User hiện tại
$saved_outfits = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // SQL lấy thông tin từ bảng
    $sql = "SELECT so.id as saved_id, so.style_name,
                   t.name as top_name, t.image as top_img,
                   b.name as bottom_name, b.image as bottom_img,
                   s.name as shoes_name, s.image as shoes_img,
                   a.name as acc_name, a.image as acc_img
            FROM saved_outfits so
            JOIN outfits t ON so.top_id = t.id
            JOIN outfits b ON so.bottom_id = b.id
            JOIN outfits s ON so.shoes_id = s.id
            LEFT JOIN outfits a ON so.acc_id = a.id
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
}

// Include Header (Đã bao gồm session_start, config, toast và head/navbar)
include '../includes/header.php';
?>

<section class="wardrobe-page">
    <div class="grid wide">
        <div class="wardrobe__header">
            <h1 class="wardrobe__title">Bộ sưu tập của tôi</h1>
            <p class="wardrobe__subtitle">Lưu trữ những phong cách phối đồ yêu thích của bạn</p>
        </div>

        <div class="row">
            <?php if (empty($saved_outfits)): ?>
                <div class="col l-12">
                    <div class="wardrobe-empty">
                        <i class="fa-solid fa-shirt"></i>
                        <p>Bạn chưa lưu bộ trang phục nào.</p>
                        <a href="../index.php" class="btn-primary">Phối đồ ngay</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($saved_outfits as $outfit): ?>
                    <div class="col l-3 m-6 c-12">
                        <div class="wardrobe-card">
                            <div class="wardrobe-card__gallery">
                                <div class="wardrobe-card__img">
                                    <img src="../assets/img/<?php echo basename($outfit['top_img']); ?>" alt="Áo">
                                </div>
                                <div class="wardrobe-card__img">
                                    <img src="../assets/img/<?php echo basename($outfit['bottom_img']); ?>" alt="Quần">
                                </div>
                            </div>

                            <div class="wardrobe-card__body">
                                <div class="wardrobe-card__main">
                                    <h3 class="wardrobe-card__name">
                                        <?php echo htmlspecialchars($outfit['style_name'] ?: 'Style của tôi'); ?>
                                    </h3>
                                    <div class="wardrobe-card__desc">
                                        <p><span>Áo:</span> <?php echo htmlspecialchars($outfit['top_name']); ?></p>
                                        <p><span>Quần:</span> <?php echo htmlspecialchars($outfit['bottom_name']); ?></p>
                                    </div>
                                    <span class="wardrobe-card__date">Đã phối sản phẩm</span>
                                </div>
                                <button class="wardrobe-card__btn-delete" 
                                        onclick="app.deleteSavedOutfit(<?php echo $outfit['saved_id']; ?>, this)"
                                        title="Xóa bộ đồ này">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
</body>
</html>