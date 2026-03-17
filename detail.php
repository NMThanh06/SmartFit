<?php
include 'includes/header.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productId <= 0) {
    header("Location: shop.php");
    exit;
}

// Lấy thông tin sản phẩm
$sql = "SELECT o.*, 
        (SELECT c.image FROM outfit_colors c WHERE c.outfit_id = o.id LIMIT 1) as color_image 
        FROM outfits o WHERE o.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Nếu ảnh chính trong bảng outfits trống, dùng ảnh từ outfit_colors
if (empty($product['image']) && !empty($product['color_image'])) {
    $product['image'] = $product['color_image'];
}

if (!$product) {
    die("Không tìm thấy sản phẩm!");
}

// Kiểm tra quyền riêng tư cho đồ cá nhân (is_commercial = 0)
// Chỉ chủ sở hữu (owner_id) mới được xem
if (isset($product['is_commercial']) && $product['is_commercial'] == 0) {
    if (!isset($_SESSION['user_id']) || $product['owner_id'] != $_SESSION['user_id']) {
        // Nếu không phải chủ sở hữu, chuyển hướng về shop hoặc báo lỗi
        header("Location: shop.php");
        exit;
    }
}

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
                <div class="col l-6 m-12 c-12">
                    <div class="product-detail__gallery">
                        <div class="product-detail__image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" id="mainProductImg" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='./assets/img/default-placeholder.jpg'">
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Thông tin sản phẩm -->
                <div class="col l-6 m-12 c-12">
                    <div class="product-detail__content">
                        <h1 class="product-detail__title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        
                        <div class="product-detail__price">
                            <?php echo number_format($product['price'], 0, ',', '.'); ?> đ
                        </div>

                        <div class="product-detail__meta">
                            <div class="product-detail__meta-item">
                                <span class="label">Loại:</span>
                                <span class="value"><?php echo $displayType; ?></span>
                            </div>
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

                        <?php if (!empty($product['description'])): ?>
                        <div class="product-detail__description">
                            <h3 class="product-detail__label">Mô tả sản phẩm</h3>
                            <p class="product-detail__text">
                                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <!-- Chọn Màu sắc -->
                        <div class="product-detail__option">
                            <h3 class="product-detail__label">Màu sắc</h3>
                            <div class="product-detail__colors">
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
                                    <span class="color-btn-item <?php echo $isOut ? 'out-of-stock' : ''; ?>" 
                                          style="background-color: <?php echo htmlspecialchars($color['hex_code']); ?>"
                                          title="<?php echo htmlspecialchars($color['color_name']) . ($isOut ? ' (Hết hàng)' : ''); ?>"
                                          onclick="<?php echo $isOut ? '' : "selectColorOnDetail(this, " . htmlspecialchars(json_encode($color)) . ")"; ?>">
                                    </span>
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
                                    <input type="text" id="qtyDisplay" value="1" readonly class="qty-input">
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
        </div>
    </div>
</section>


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

        // Push vào mảng cart toàn cục (đã khai báo ở footer.php)
        if (existingIndex !== -1) {
            cart[existingIndex].quantity += currentQty;
        } else {
            cart.push({
                id: currentProduct.id,
                name: currentProduct.name,
                image: currentProduct.image,
                price: currentProduct.price,
                size: selectedSize,
                color: selectedColor,
                allColors: currentProduct.allColors,
                allSizes: currentProduct.allSizes,
                quantity: currentQty
            });
        }

        // GỌI HÀM DÙNG CHUNG: lưu localStorage + render lại
        saveCart();

        // Tự động mở thanh trượt giỏ hàng
        if (typeof app !== 'undefined' && app.openCart) {
            app.openCart();
        }
    }
</script>

</body>

</html>