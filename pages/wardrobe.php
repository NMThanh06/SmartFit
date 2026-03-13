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

<div class="web__background--overlay"></div>

<section class="wardrobe-page">
    <div class="grid wide">
        <div class="row">
            <div class="col l-12 m-12 c-12">
                <div class="wardrobe__header">
                    <h1 class="wardrobe__title">Bộ sưu tập</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (empty($saved_outfits)): ?>
                <div class="col l-12" style="text-align: center; padding: 40px 0;">
                    <p>Bạn chưa lưu bộ trang phục nào.</p>
                    <a href="../index.php" class="button" style="margin-top: 10px; display: inline-block;">Phối đồ ngay</a>
                </div>
            <?php else: ?>
                <?php foreach ($saved_outfits as $outfit): ?>
                    <div class="col l-3 m-4 c-12">
                        <div class="wardrobe-card">
                            <div class="wardrobe-card__images">
                                        <img src="../assets/img/<?php echo basename($outfit['top_img']); ?>" alt="Áo">
                                        
                                        <img src="../assets/img/<?php echo basename($outfit['bottom_img']); ?>" alt="Quần">
                                        
                                        <!-- phần hiển thị 2 món còn lại niếu cần *(chưa css/js) -->
                                        <!-- <img src="../assets/img/<?php echo basename($outfit['shoes_img']); ?>" alt="Giày">
                                        
                                        <?php if ($outfit['acc_img']): ?>
                                            <img src="../assets/img/<?php echo basename($outfit['acc_img']); ?>" alt="Phụ kiện">
                                        <?php endif; ?> -->

                            </div>

                            <div class="wardrobe-card__info">
                                <div class="wardrobe-card__header">
                                    <h3 class="wardrobe-card__title"><?php echo htmlspecialchars($outfit['style_name'] ?: 'Style của tôi'); ?></h3>
                                    <button class="wardrobe-card__delete" 
                                            title="Xóa" 
                                            onclick="app.deleteSavedOutfit(<?php echo $outfit['saved_id']; ?>, this)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                                <ul class="wardrobe-card__items">
                                            <li>
                                            <p><?php echo htmlspecialchars($outfit['top_name']); ?></p>
                                            </li>
                                            <li>
                                                <p><?php echo htmlspecialchars($outfit['bottom_name']); ?></p>
                                            </li>
                                            <li>
                                                <p><?php echo htmlspecialchars($outfit['shoes_name']); ?></p>
                                            </li>
                                            <li>
                                                <p><?php echo $outfit['acc_name'] ? htmlspecialchars($outfit['acc_name']) : 'Không có'; ?></p>
                                            </li>
                                </ul>
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