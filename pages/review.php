<?php
session_start();
require_once '../includes/config.php';

$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) {
    header("Location: ../style_outfits.php");
    exit;
}

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$outfitId = isset($_GET['outfit_id']) ? intval($_GET['outfit_id']) : 0;

if ($orderId <= 0 || $outfitId <= 0) {
    $_SESSION['error'] = "Thông tin không hợp lệ.";
    header("Location: order_history.php");
    exit;
}

// 1. Kiểm tra xem user này đã mua outfit_id trong order_id này chưa và order đã completed chưa
$sqlCheckPurchase = "SELECT o.status FROM orders o JOIN order_details d ON o.id = d.order_id WHERE o.id = ? AND o.user_id = ? AND d.outfit_id = ?";
$stmtPurchase = mysqli_prepare($conn, $sqlCheckPurchase);
mysqli_stmt_bind_param($stmtPurchase, "iii", $orderId, $userId, $outfitId);
mysqli_stmt_execute($stmtPurchase);
$resPurchase = mysqli_stmt_get_result($stmtPurchase);
if (mysqli_num_rows($resPurchase) == 0) {
    $_SESSION['error'] = "Bạn không có quyền đánh giá sản phẩm này.";
    header("Location: order_history.php");
    exit;
}
$orderData = mysqli_fetch_assoc($resPurchase);
if ($orderData['status'] !== 'completed') {
    $_SESSION['error'] = "Chỉ có thể đánh giá khi đơn hàng đã hoàn tất.";
    header("Location: order_history.php");
    exit;
}

// 2. Kiểm tra xem họ đã đánh giá chưa (để tránh spam)
$sqlCheckReview = "SELECT id FROM reviews WHERE user_id = ? AND order_id = ? AND outfit_id = ?";
$stmtReview = mysqli_prepare($conn, $sqlCheckReview);
mysqli_stmt_bind_param($stmtReview, "iii", $userId, $orderId, $outfitId);
mysqli_stmt_execute($stmtReview);
if (mysqli_num_rows(mysqli_stmt_get_result($stmtReview)) > 0) {
    $_SESSION['error'] = "Bạn đã đánh giá sản phẩm này rồi.";
    header("Location: order_history.php");
    exit;
}

// Lấy thông tin sản phẩm để hiển thị
$sqlOutfit = "SELECT name, (SELECT image FROM outfit_colors WHERE outfit_id = outfits.id LIMIT 1) as image FROM outfits WHERE id = ?";
$stmtOutfit = mysqli_prepare($conn, $sqlOutfit);
mysqli_stmt_bind_param($stmtOutfit, "i", $outfitId);
mysqli_stmt_execute($stmtOutfit);
$outfit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtOutfit));

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

// Xử lý POST đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) $rating = 5;

    mysqli_begin_transaction($conn);
    try {
        // 1. Insert review
        $sqlInsert = "INSERT INTO reviews (user_id, outfit_id, order_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmtInsert = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmtInsert, "iiiis", $userId, $outfitId, $orderId, $rating, $comment);
        mysqli_stmt_execute($stmtInsert);

        // 2. Cập nhật avg_rating và review_count trong bảng outfits
        $sqlUpdate = "UPDATE outfits SET 
                      review_count = review_count + 1,
                      avg_rating = (SELECT AVG(rating) FROM reviews WHERE outfit_id = ?)
                      WHERE id = ?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "ii", $outfitId, $outfitId);
        mysqli_stmt_execute($stmtUpdate);

        mysqli_commit($conn);
        $_SESSION['success'] = "Cảm ơn bạn đã đánh giá sản phẩm!";
        header("Location: order_history.php");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Đã xảy ra lỗi khi lưu đánh giá: " . $e->getMessage();
    }
}

$root = '../';
include '../includes/header.php';
?>

<main class="review-page" style="padding: 40px 0; background: #f5f5f5; min-height: 80vh;">
    <div class="grid wide">
        <div class="row" style="justify-content: center;">
            <div class="col l-6 m-8 c-12">
                <div style="background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 2.4rem; margin-bottom: 20px; text-align: center; color: #ee4d2d;">Đánh giá sản phẩm</h2>
                    
                    <?php if ($error): ?>
                        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 1.4rem;">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 15px; margin-bottom: 30px; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                        <img src="<?php echo htmlspecialchars($outfit['image'] ?? '/assets/img/default-placeholder.jpg'); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;" onerror="this.src='/assets/img/default-placeholder.jpg'">
                        <div>
                            <h3 style="font-size: 1.6rem; margin-bottom: 5px;"><?php echo htmlspecialchars($outfit['name']); ?></h3>
                            <p style="font-size: 1.3rem; color: #767676;">Đơn hàng: #<?php echo $orderId; ?></p>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <div style="margin-bottom: 25px; text-align: center;">
                            <label style="display: block; font-size: 1.6rem; font-weight: 500; margin-bottom: 15px;">Chất lượng sản phẩm</label>
                            <div class="star-rating" style="display: flex; justify-content: center; gap: 10px; font-size: 3rem; color: #ddd; cursor: pointer; flex-direction: row-reverse;">
                                <input type="radio" name="rating" id="star5" value="5" required style="display:none;">
                                <label for="star5" class="fa-solid fa-star" title="5 sao"></label>
                                <input type="radio" name="rating" id="star4" value="4" style="display:none;">
                                <label for="star4" class="fa-solid fa-star" title="4 sao"></label>
                                <input type="radio" name="rating" id="star3" value="3" style="display:none;">
                                <label for="star3" class="fa-solid fa-star" title="3 sao"></label>
                                <input type="radio" name="rating" id="star2" value="2" style="display:none;">
                                <label for="star2" class="fa-solid fa-star" title="2 sao"></label>
                                <input type="radio" name="rating" id="star1" value="1" style="display:none;">
                                <label for="star1" class="fa-solid fa-star" title="1 sao"></label>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; font-size: 1.4rem; margin-bottom: 10px;">Bình luận (Tùy chọn)</label>
                            <textarea name="comment" rows="4" style="width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 12px; font-size: 1.4rem; resize: vertical;" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."></textarea>
                        </div>

                        <div style="display: flex; justify-content: space-between; gap: 15px;">
                            <a href="order_history.php" class="button" style="flex: 1; padding: 12px; text-align: center; background: #fff; color: #555; border: 1px solid #ccc; border-radius: 4px; font-size: 1.4rem; text-decoration: none;">Trở lại</a>
                            <button type="submit" name="submit_review" class="button" style="flex: 1; padding: 12px; border: none; background: #ee4d2d; color: white; border-radius: 4px; font-size: 1.4rem; cursor: pointer; text-decoration: none;">Gửi đánh giá</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* CSS cho đánh giá sao */
.star-rating label {
    transition: color 0.2s;
}
.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type="radio"]:checked ~ label {
    color: #ffc107;
}
</style>

<script>
    // Có thể thêm tooltip cho sao nếu cần
    const stars = document.querySelectorAll('.star-rating label');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            // Khi chọn sao, gán input checked bằng CSS ^
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
