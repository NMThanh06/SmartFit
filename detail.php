<?php
// Di chuyển logic xử lý lên đầu để tránh lỗi "Headers already sent"
require_once 'includes/config.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productId <= 0) {
    header("Location: shop.php");
    exit;
}

// Lấy thông tin sản phẩm
$sql = "SELECT o.*, u.fullname as vendor_name, 
        (SELECT c.image FROM outfit_colors c WHERE c.outfit_id = o.id LIMIT 1) as color_image 
        FROM outfits o 
        LEFT JOIN users u ON o.owner_id = u.id
        WHERE o.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Nếu ảnh chính trong bảng outfits trống, dùng ảnh từ outfit_colors
if ($product && empty($product['image']) && !empty($product['color_image'])) {
    $product['image'] = $product['color_image'];
}

// Nếu không tìm thấy sản phẩm, chuyển hướng sang trang 404
if (!$product) {
    header("Location: pages/404.php");
    exit;
}

// Kiểm tra quyền riêng tư cho đồ cá nhân (is_commercial = 0)
// Chặn hoàn toàn không cho xem chi tiết đối với đồ cá nhân
if (isset($product['is_commercial']) && $product['is_commercial'] == 0) {
    header("Location: pages/404.php");
    exit;
}

include 'includes/header.php';


// Lấy Size và số lượng từ bảng outfit_sizes
$sqlSizes = "SELECT color_id, size_name, quantity FROM outfit_sizes WHERE outfit_id = ?";
$stmtSizes = mysqli_prepare($conn, $sqlSizes);
mysqli_stmt_bind_param($stmtSizes, "i", $productId);
mysqli_stmt_execute($stmtSizes);
$resSizes = mysqli_stmt_get_result($stmtSizes);
$sizeList = [];
while ($row = mysqli_fetch_assoc($resSizes)) {
    $sizeList[] = $row;
}

// Lấy danh sách màu sắc
$sqlColors = "SELECT * FROM outfit_colors WHERE outfit_id = ?";
$stmtColors = mysqli_prepare($conn, $sqlColors);
mysqli_stmt_bind_param($stmtColors, "i", $productId);
mysqli_stmt_execute($stmtColors);
$resColors = mysqli_stmt_get_result($stmtColors);
$colorList = [];
while ($row = mysqli_fetch_assoc($resColors)) {
    $colorList[] = $row;
}

// Lấy danh sách đánh giá
$sqlReviews = "SELECT r.*, u.fullname, u.avatar FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.outfit_id = ? ORDER BY r.created_at DESC";
$stmtReviews = mysqli_prepare($conn, $sqlReviews);
mysqli_stmt_bind_param($stmtReviews, "i", $productId);
mysqli_stmt_execute($stmtReviews);
$resReviews = mysqli_stmt_get_result($stmtReviews);
$reviewsList = [];
while ($row = mysqli_fetch_assoc($resReviews)) {
    $reviewsList[] = $row;
}

function formatHiddenName($name) {
    if (mb_strlen($name) <= 3) return $name . "***";
    $first = mb_substr($name, 0, 3);
    $last = mb_substr($name, -2);
    return $first . "***" . $last;
}

// Hàm Việt hóa (Giữ nguyên logic dịch của ông)
function translateFitData($data)
{
    if (empty($data))
        return 'Cơ bản';
    $map = [
        'basic' => 'Cơ bản', 'street' => 'Đường phố', 'vintage' => 'Cổ điển',
        'study' => 'Đi học', 'goout' => 'Đi chơi', 'date' => 'Hẹn hò',
        'regular' => 'Vừa vặn', 'oversized' => 'Rộng', 'slim' => 'Ôm',
        'top' => 'Áo', 'bottom' => 'Quần/Váy', 'shoes' => 'Giày', 'accessory' => 'Phụ kiện'
    ];
    $clean = str_replace(['[', ']', '"'], '', $data);
    $items = explode(',', $clean);
    $results = [];
    foreach ($items as $val) {
        $k = trim(strtolower($val));
        $results[] = isset($map[$k]) ? $map[$k] : ucfirst($val);
    }
    return implode(', ', $results);
}
$displayType = (in_array($product['type'], ['accessory', 'glasses'])) ? 'Phụ kiện' : translateFitData($product['type']);
?>

<section class="detail-page">
    <div class="grid wide">
        <div class="detail-back">
            <a href="shop.php" class="detail-back__link">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại cửa hàng
            </a>
        </div>

        <div class="product-detail">
            <div class="row">
                <!-- Cột trái: Ảnh sản phẩm -->
                <div class="col l-6 m-12 c-12 product-detail__left">
                    <div class="product-detail__gallery">
                        <div class="product-detail__image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" id="mainProductImg" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='./assets/img/default-placeholder.jpg'">
                        </div>

                        <!-- Thông tin người bán nằm dưới ảnh -->
                        <div class="product-vendor-box">
                            <div class="product-vendor-info">
                                <span class="vendor-label">Được cung cấp bởi</span>
                                <span class="vendor-name"><?php echo htmlspecialchars($product['vendor_name'] ?? 'SmartFit Shop'); ?></span>
                            </div>
                            <a href="vendor_shop.php?id=<?php echo intval($product['owner_id']); ?>" class="btn-visit-shop">
                                Truy cập <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="product-category-box" style="margin-top: 10px; font-size: 1.4rem; padding: 12px 15px; background: #fafafa; border-radius: 4px; border: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <span class="label" style="color: #767676;">Loại sản phẩm</span>
                            <span class="value" style="font-weight: 500; color: #ee4d2d;"><?php echo $displayType; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Thông tin sản phẩm -->
                <div class="col l-6 m-12 c-12 product-detail__right">
                    <div class="product-detail__content">
                        <h1 class="product-detail__title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        
                        <!-- NEW SECTION FOR RATINGS -->
                        <div class="product-detail__stats" style="display: flex; align-items: center; gap: 15px; margin-top: 10px; margin-bottom: 15px; font-size: 1.4rem;">
                            <div class="rating-box" style="color: #ffc107; display: flex; align-items: center; gap: 5px;">
                                <span class="rating-value" style="color: #ee4d2d; border-bottom: 1px solid #ee4d2d; font-size: 1.6rem; font-weight: 500;"><?php echo number_format($product['avg_rating'] ?? 0, 1); ?></span>
                                <div class="stars" style="display: flex; gap: 2px;">
                                    <?php 
                                    $rating = floatval($product['avg_rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div style="width: 1px; height: 14px; background-color: #e0e0e0;"></div>
                            <div class="review-count">
                                <span style="font-size: 1.6rem; font-weight: 500; border-bottom: 1px solid #555;"><?php echo intval($product['review_count'] ?? 0); ?></span>
                                <span style="color: #767676;">Đánh giá</span>
                            </div>
                            <div style="width: 1px; height: 14px; background-color: #e0e0e0;"></div>
                            <div class="sold-count">
                                <span style="font-size: 1.6rem; font-weight: 500;"><?php echo intval($product['total_sold'] ?? 0); ?></span>
                                <span style="color: #767676;">Đã bán</span>
                            </div>
                        </div>

                        <div class="product-detail__price" style="background: #fafafa; padding: 15px 20px; color: #ee4d2d; font-size: 3rem; font-weight: 500; margin-bottom: 25px; border-radius: 4px;">
                            <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                        </div>



                        <div class="product-detail__meta">
                            <div class="product-detail__meta-item">
                                <span class="label">Phong cách:</span>
                                <span class="value"><?php echo translateFitData($product['style']); ?></span>
                            </div>
                            <?php if (!in_array($product['type'], ['shoes', 'accessory', 'accessories', 'glasses'])): ?>
                            <div class="product-detail__meta-item">
                                <span class="label">Độ rộng:</span>
                                <span class="value"><?php echo translateFitData($product['fit'] ?? ''); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="product-detail__meta-item">
                                <span class="label">Tình trạng:</span>
                                <span class="value status" id="stockInfo">Vui lòng chọn size</span>
                            </div>
                        </div>



                        <!-- Chọn Màu sắc -->
                        <div class="product-detail__option">
                            <h3 class="product-detail__label">Màu sắc</h3>
                            <div class="product-detail__colors" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php foreach ($colorList as $index => $color): 
                                    // Tính tồn kho tổng của màu này
                                    $cid = $color['id'];
                                    $colorStock = 0;
                                    foreach ($sizeList as $s) {
                                        if ($s['color_id'] == $cid) {
                                            $colorStock += intval($s['quantity']);
                                        }
                                    }
                                    $isOut = $colorStock <= 0;
                                ?>
                                    <div class="color-btn-item <?php echo $isOut ? 'out-of-stock' : ''; ?>" 
                                          title="<?php echo htmlspecialchars($color['color_name']) . ($isOut ? ' (Hết hàng)' : ''); ?>"
                                          onclick="<?php echo $isOut ? '' : "selectColorOnDetail(this, " . htmlspecialchars(json_encode($color)) . ")"; ?>"
                                          style="width: 40px; height: 40px; border-radius: 4px; overflow: hidden; border: 1px solid #e0e0e0; cursor: pointer; display: flex; align-items: center; justify-content: center; position: relative; <?php echo $isOut ? 'opacity: 0.5; cursor: not-allowed;' : ''; ?>">
                                        <img src="<?php echo htmlspecialchars($color['image'] ?? '/SmartFit/assets/img/default-placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($color['color_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php if ($isOut): ?>
                                            <div style="position: absolute; width: 100%; height: 100%; background: rgba(255,255,255,0.6); display: flex; align-items: center; justify-content: center; left: 0; top: 0;"><i class="fa-solid fa-xmark" style="color: #666;"></i></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="product-detail__separator"></div>

                        <!-- Chọn Size -->
                        <div class="product-detail__option">
                            <h3 class="product-detail__label">Kích thước</h3>
                            <div class="product-detail__sizes">
                                <?php foreach ($sizeList as $size): ?>
                                    <button class="size-btn-item" onclick="selectSize(this)" 
                                            data-size="<?php echo htmlspecialchars($size['size_name']); ?>" 
                                            data-quantity="<?php echo intval($size['quantity']); ?>">
                                        <?php echo htmlspecialchars($size['size_name']); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Chọn Số lượng -->
                        <div class="product-detail__option">
                            <h3 class="product-detail__label">Số lượng</h3>
                            <div class="product-detail__qty">
                                <div class="qty-control">
                                    <button class="qty-btn" onclick="changeQty(-1)">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                    <input type="number" id="qtyDisplay" value="1" class="qty-input" onchange="validateQty(this)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <button class="qty-btn" onclick="changeQty(1)">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="product-detail__actions">
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <button onclick="addToCartFromDetail()" class="btn-add-cart">
                                <i class="fa-solid fa-cart-plus"></i>
                                Thêm vào giỏ hàng
                            </button>
                            <?php else: ?>
                            <button onclick="showToast('Vui lòng đăng nhập để mua hàng!', 'info')" class="btn-add-cart" style="background: var(--apple-grey); border-radius: 40px; width: 100%; justify-content: center;">
                                <i class="fa-solid fa-circle-user"></i>
                                Đăng nhập để mua hàng
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($product['description'])): ?>
            <!-- Mô tả sản phẩm -->
            <div class="product-description-section" style="margin-top: 40px; background: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
                <h3 class="product-detail__label" style="font-size: 2rem; margin-bottom: 15px;">MÔ TẢ SẢN PHẨM</h3>
                <div class="product-description-content" id="descContent" style="max-height: 200px; overflow: hidden; position: relative; font-size: 1.4rem; color: #444; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?>
                    <div class="desc-fade" id="descFade" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 60px; background: linear-gradient(to bottom, rgba(250,250,250,0), rgba(250,250,250,1));"></div>
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <button id="btnToggleDesc" onclick="toggleDescription()" style="background: none; border: 1px solid #ee4d2d; color: #ee4d2d; padding: 8px 25px; font-size: 1.4rem; border-radius: 4px; cursor: pointer; transition: 0.3s;">Xem thêm <i class="fa-solid fa-chevron-down"></i></button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Bình luận - Đánh giá sản phẩm -->
            <div class="product-reviews-section" style="margin-top: 20px; margin-bottom: 20px;">
                <h3 class="product-detail__label" style="font-size: 2rem; margin-bottom: 15px;">ĐÁNH GIÁ SẢN PHẨM</h3>
                <div class="reviews-container" style="background: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
                    
                    <div class="review-filters" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0;">
                        <button class="review-filter-btn active" onclick="filterReviews('all', this)">Tất cả</button>
                        <button class="review-filter-btn" onclick="filterReviews(5, this)">5 Sao</button>
                        <button class="review-filter-btn" onclick="filterReviews(4, this)">4 Sao</button>
                        <button class="review-filter-btn" onclick="filterReviews(3, this)">3 Sao</button>
                        <button class="review-filter-btn" onclick="filterReviews(2, this)">2 Sao</button>
                        <button class="review-filter-btn" onclick="filterReviews(1, this)">1 Sao</button>
                    </div>

                    <div id="noReviewsMessage" style="display: none; text-align: center; color: #767676; padding: 20px 0;">Không có đánh giá nào phù hợp.</div>

                    <?php if (empty($reviewsList)): ?>
                        <p style="text-align: center; color: #767676; padding: 20px 0;">Chưa có đánh giá nào cho sản phẩm này.</p>
                    <?php else: ?>
                        <?php foreach ($reviewsList as $review): ?>
                            <div class="review-item" data-rating="<?php echo intval($review['rating']); ?>" style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px dashed #e0e0e0;">
                                <div class="review-avatar">
                                    <img src="<?php echo htmlspecialchars($review['avatar'] ?? '/SmartFit/assets/img/default-avatar.png'); ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;" onerror="this.src='/SmartFit/assets/img/default-avatar.png'">
                                </div>
                                <div class="review-content" style="flex: 1;">
                                    <div class="review-author" style="font-size: 1.4rem; font-weight: 500; color: #333; margin-bottom: 4px;">
                                        <?php echo htmlspecialchars(formatHiddenName($review['fullname'] ?? 'User')); ?>
                                    </div>
                                    <div class="review-stars" style="color: #ffc107; font-size: 1.2rem; margin-bottom: 6px;">
                                        <?php 
                                        $rStar = intval($review['rating']);
                                        for ($i=1; $i<=5; $i++) {
                                            echo $i <= $rStar ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <div class="review-date" style="font-size: 1.2rem; color: #888; margin-bottom: 10px;">
                                        <?php echo date('d-m-Y H:i', strtotime($review['created_at'])); ?>
                                    </div>
                                    <div class="review-text" style="font-size: 1.4rem; color: #444; line-height: 1.5;">
                                        <?php echo nl2br(htmlspecialchars($review['comment'] ?? '')); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

</style>
<style>
.color-btn-item.selected {
    border: 2px solid #ee4d2d !important;
}
.color-btn-item.selected::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 0 12px 12px;
    border-color: transparent transparent #ee4d2d transparent;
}
.color-btn-item.selected::before {
    content: '✓';
    position: absolute;
    bottom: -2px;
    right: 0;
    color: white;
    font-size: 8px;
    z-index: 10;
}
.review-filter-btn {
    padding: 8px 20px;
    border: 1px solid #e0e0e0;
    background: #fff;
    border-radius: 4px;
    font-size: 1.4rem;
    cursor: pointer;
    transition: 0.2s;
    color: #555;
}
.review-filter-btn.active {
    border-color: #ee4d2d;
    color: #ee4d2d;
}
</style>


<?php require_once 'includes/footer.php'; ?>

    <!-- Backend cho trang chi tiết sản phẩm -->
    <script>
    // --- BIẾN TOÀN CỤC (Lấy từ PHP Server-Side) ---
    let selectedSize = null;
    let maxStock = 0;
    let currentQty = 1;

    // Thông tin sản phẩm từ PHP (dùng cho addToCart)
    const currentProduct = {
        id: <?php echo intval($product['id']); ?>,
        name: <?php echo json_encode($product['name']); ?>,
        image: <?php echo json_encode($product['image']); ?>,
        price: <?php echo intval($product['price']); ?>,
        owner_id: <?php echo intval($product['owner_id']); ?>,
        vendor_name: <?php echo json_encode($product['vendor_name'] ?? 'SmartFit Shop'); ?>,
        allColors: <?php echo json_encode($colorList); ?>,
        allSizes: <?php echo json_encode($sizeList); ?>
    };

    let selectedColor = currentProduct.allColors.length > 0 ? currentProduct.allColors[0].color_name : 'Default';
    let selectedColorId = currentProduct.allColors.length > 0 ? currentProduct.allColors[0].id : null;

    // 0. HÀM CHỌN MÀU
    function selectColorOnDetail(btnElement, colorObj) {
        selectedColor = colorObj.color_name;
        selectedColorId = colorObj.id;

        // Đổi ảnh
        if (colorObj.image) {
            document.getElementById('mainProductImg').src = colorObj.image;
            currentProduct.image = colorObj.image; // Cập nhật ảnh đại diện để thêm vào giỏ
        }

        // Highlight nút được chọn
        document.querySelectorAll('.color-btn-item').forEach(btn => btn.classList.remove('selected'));
        btnElement.classList.add('selected');

        // Render lại size tương ứng
        renderSizes(selectedColorId);
    }

    function renderSizes(colorId) {
        const sizeContainer = document.querySelector('.product-detail__sizes');
        sizeContainer.innerHTML = '';
        selectedSize = null;
        maxStock = 0;
        document.getElementById('stockInfo').innerText = 'Vui lòng chọn size';
        
        // Lấy danh sách tên size duy nhất và sắp xếp
        const allSizes = currentProduct.allSizes || [];
        const uniqueSizeNames = [...new Set(allSizes.map(s => s.size_name))];
        const sizeOrder = ['S', 'M', 'L', 'XL', '2XL', 'XXL', '3XL', 'Oversize'];
        uniqueSizeNames.sort((a, b) => {
            const ia = sizeOrder.indexOf(a);
            const ib = sizeOrder.indexOf(b);
            if (ia !== -1 && ib !== -1) return ia - ib;
            return a.localeCompare(b);
        });
        
        uniqueSizeNames.forEach(sizeName => {
            // Tìm tồn kho cho size này ứng với màu đang chọn
            const sizeData = allSizes.find(s => s.size_name === sizeName && s.color_id == colorId);
            const qty = sizeData ? parseInt(sizeData.quantity) : 0;
            const isOut = qty <= 0;

            const btn = document.createElement('button');
            btn.className = 'size-btn-item' + (isOut ? ' out-of-stock' : '');
            btn.innerText = sizeName;
            
            if (!isOut) {
                btn.onclick = () => selectSize(btn, sizeName, qty);
            } else {
                btn.title = "Hết hàng cho màu này";
            }
            
            sizeContainer.appendChild(btn);
        });

        if (uniqueSizeNames.length === 0) {
            sizeContainer.innerHTML = '<p style="color:#999;">Hết hàng</p>';
        }
    }

    function selectSize(btnElement, sizeName, quantity) {
        selectedSize = sizeName;
        maxStock = parseInt(quantity);
        currentQty = 1;

        document.querySelectorAll('.size-btn-item').forEach(btn => btn.classList.remove('selected'));
        btnElement.classList.add('selected');
        document.getElementById('qtyDisplay').value = currentQty;

        const stockEl = document.getElementById('stockInfo');
        if (maxStock > 0) {
            stockEl.innerText = `Còn ${maxStock} sản phẩm`;
            stockEl.style.color = 'var(--success)';
        } else {
            stockEl.innerText = 'Hết hàng';
            stockEl.style.color = 'var(--error)';
        }
    }

    // Khởi tạo size lần đầu cho màu mặc định (Ưu tiên màu còn hàng)
    document.addEventListener('DOMContentLoaded', () => {
        const firstAvailableColorBtn = document.querySelector('.color-btn-item:not(.out-of-stock)');
        if (firstAvailableColorBtn) {
            firstAvailableColorBtn.click();
        } else {
            // Nếu tất cả màu đều hết hàng
            const firstColor = document.querySelector('.color-btn-item');
            if (firstColor) firstColor.classList.add('selected');
            renderSizes(null);
        }
    });

    // 2. HÀM THAY ĐỔI SỐ LƯỢNG (Giới hạn bởi maxStock)
    function changeQty(amount) {
        if (!selectedSize) {
            showToast('Vui lòng chọn kích cỡ trước!', 'error');
            return;
        }
        let nextQty = currentQty + amount;
        if (nextQty < 1) nextQty = 1;
        if (nextQty > maxStock) {
            showToast('Chỉ còn ' + maxStock + ' sản phẩm trong kho!', 'error');
            nextQty = maxStock;
        }
        currentQty = nextQty;
        document.getElementById('qtyDisplay').value = currentQty;
    }

    // 2.1 HÀM KIỂM TRA SỐ LƯỢNG KHI NHẬP THỦ CÔNG
    function validateQty(input) {
        if (!selectedSize) {
            showToast('Vui lòng chọn kích cỡ trước!', 'error');
            input.value = 1;
            return;
        }
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) {
            val = 1;
        }
        if (val > maxStock) {
            showToast('Chỉ còn ' + maxStock + ' sản phẩm trong kho!', 'error');
            val = maxStock;
        }
        currentQty = val;
        input.value = val;
    }

    // 3. HÀM THÊM VÀO GIỎ TỪ TRANG CHI TIẾT
    function addToCartFromDetail() {
        if (!selectedSize || !selectedColor) {
            showToast('Vui lòng chọn Kích cỡ và Màu sắc trước khi mua!', 'error');
            return;
        }

        // --- KIỂM TRA TỒN KHO TRƯỚC KHI THÊM (CHẶN CỘNG DỒN) ---
        const existingIndex = cart.findIndex(item => item.id === currentProduct.id && item.size === selectedSize && item.color === selectedColor);
        const qtyInCart = existingIndex !== -1 ? cart[existingIndex].quantity : 0;
        const totalExpected = qtyInCart + currentQty;

        if (totalExpected > maxStock) {
            const remaining = maxStock - qtyInCart;
            if (remaining <= 0) {
                showToast(`Sản phẩm ${currentProduct.name} (Size ${selectedSize}, ${selectedColor}) đã đạt giới hạn tồn kho trong giỏ hàng!`, 'error');
            } else {
                showToast('Chỉ có thể thêm tối đa ' + remaining + ' sản phẩm nữa!', 'error');
            }
            return;
        }
        // --- KẾT THÚC KIỂM TRA TỒN KHO ---

        // --- Hiệu ứng ảnh bay vào giỏ hàng ---
        const imgEl = document.getElementById('mainProductImg');
        const cartIcon = document.querySelector('.navbar__cart');
        if (imgEl && cartIcon) {
            const imgClone = imgEl.cloneNode(true);
            const imgRect = imgEl.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            imgClone.style.position = 'fixed';
            imgClone.style.left = imgRect.left + 'px';
            imgClone.style.top = imgRect.top + 'px';
            imgClone.style.width = imgRect.width + 'px';
            imgClone.style.height = imgRect.height + 'px';
            imgClone.style.zIndex = '9999';
            imgClone.style.transition = 'all 0.7s ease-in-out';
            imgClone.style.borderRadius = '8px';
            imgClone.style.pointerEvents = 'none';
            document.body.appendChild(imgClone);

            requestAnimationFrame(() => {
                imgClone.style.left = cartRect.left + 'px';
                imgClone.style.top = cartRect.top + 'px';
                imgClone.style.width = '30px';
                imgClone.style.height = '30px';
                imgClone.style.opacity = '0.3';
            });

            imgClone.addEventListener('transitionend', () => imgClone.remove());
        }
        // --- Kết thúc hiệu ứng ---

        // Push vào DB thay vì localStorage
        fetch('<?php echo $root; ?>includes/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                outfit_id: currentProduct.id,
                size: selectedSize,
                color: selectedColor,
                quantity: currentQty
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
                // Đồng bộ lại giỏ hàng trong drawer
                if (typeof syncCart === 'function') {
                    syncCart();
                }
                // Tự động mở thanh trượt giỏ hàng
                if (typeof cartDrawerApp !== 'undefined' && cartDrawerApp.openCart) {
                    cartDrawerApp.openCart();
                }
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Lỗi thêm giỏ hàng:', err);
            showToast('Lỗi kết nối máy chủ!', 'error');
        });
    }
        // Hàm toggle Mô tả
        function toggleDescription() {
            const descContent = document.getElementById('descContent');
            const descFade = document.getElementById('descFade');
            const btn = document.getElementById('btnToggleDesc');

            if (descContent.style.maxHeight === '200px') {
                descContent.style.maxHeight = 'none';
                descFade.style.display = 'none';
                btn.innerHTML = 'Ẩn bớt <i class="fa-solid fa-chevron-up"></i>';
            } else {
                descContent.style.maxHeight = '200px';
                descFade.style.display = 'block';
                btn.innerHTML = 'Xem thêm <i class="fa-solid fa-chevron-down"></i>';
            }
        }

        // Hàm filter đánh giá
        function filterReviews(star, btnElement) {
            document.querySelectorAll('.review-filter-btn').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');

            const reviews = document.querySelectorAll('.review-item');
            const noMsg = document.getElementById('noReviewsMessage');
            let count = 0;

            reviews.forEach(review => {
                if (star === 'all' || review.dataset.rating == star) {
                    review.style.display = 'flex';
                    count++;
                } else {
                    review.style.display = 'none';
                }
            });

            if (noMsg) {
                if (count === 0 && reviews.length > 0) {
                    noMsg.style.display = 'block';
                } else {
                    noMsg.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>