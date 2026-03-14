<?php
include 'includes/header.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productId <= 0) {
    header("Location: shop.php");
    exit;
}

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM outfits WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$product) {
    die("Không tìm thấy sản phẩm!");
}

// Lấy Size và số lượng từ bảng outfit_sizes
$sqlSizes = "SELECT size_name, quantity FROM outfit_sizes WHERE outfit_id = ?";
$stmtSizes = mysqli_prepare($conn, $sqlSizes);
mysqli_stmt_bind_param($stmtSizes, "i", $productId);
mysqli_stmt_execute($stmtSizes);
$resSizes = mysqli_stmt_get_result($stmtSizes);
$sizeList = [];
while ($row = mysqli_fetch_assoc($resSizes)) {
    $sizeList[] = $row; // mỗi phần tử chứa 'size_name' và 'quantity'
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
                            <div class="product-detail__meta-item">
                                <span class="label">Cân nặng:</span>
                                <span class="value"><?php echo translateFitData($product['fit'] ?? ''); ?></span>
                            </div>
                            <div class="product-detail__meta-item">
                                <span class="label">Tình trạng:</span>
                                <span class="value status" id="stockInfo">Vui lòng chọn size</span>
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
                            <button onclick="addToCartFromDetail()" class="btn-add-cart">
                                <i class="fa-solid fa-cart-plus"></i>
                                Thêm vào giỏ hàng
                            </button>
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
        price: <?php echo intval($product['price']); ?>
    };

    // Bản đồ tồn kho theo size (dùng để kiểm tra giới hạn trong giỏ hàng)
    const sizeStockMap = <?php echo json_encode(array_column($sizeList, 'quantity', 'size_name')); ?>;

    // 1. HÀM CHỌN SIZE
    function selectSize(btnElement) {
        const size = btnElement.getAttribute('data-size');
        const sizeQuantity = parseInt(btnElement.getAttribute('data-quantity')) || 0;
        selectedSize = size;
        maxStock = sizeQuantity;

        // Nếu số lượng đang chọn lớn hơn maxStock mới, reset về 1
        if (currentQty > maxStock) {
            currentQty = 1;
        }

        // Highlight nút được chọn bằng class 'selected'
        document.querySelectorAll('.size-btn-item').forEach(btn => {
            btn.classList.remove('selected');
        });
        btnElement.classList.add('selected');

        document.getElementById('qtyDisplay').value = currentQty;
        
        // Hiển thị tồn kho chuyên nghiệp
        const stockEl = document.getElementById('stockInfo');
        if (maxStock > 0) {
            stockEl.innerText = `Còn ${maxStock} sản phẩm`;
            stockEl.style.color = 'var(--success)';
        } else {
            stockEl.innerText = 'Hết hàng';
            stockEl.style.color = 'var(--error)';
        }
    }

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
        if (!selectedSize) {
            showToast('Vui lòng chọn kích cỡ trước khi mua!', 'error');
            return;
        }

        // --- KIỂM TRA TỒN KHO TRƯỚC KHI THÊM (CHẶN CỘNG DỒN) ---
        const existingItem = cart.find(item => item.id === currentProduct.id && item.size === selectedSize);
        const qtyInCart = existingItem ? existingItem.quantity : 0;
        const totalExpected = qtyInCart + currentQty;

        if (totalExpected > maxStock) {
            const remaining = maxStock - qtyInCart;
            if (remaining <= 0) {
                showToast('Size ' + selectedSize + ' đã đạt giới hạn tồn kho trong giỏ hàng! (Đang có ' + qtyInCart + '/' + maxStock + ')', 'error');
            } else {
                showToast('Chỉ có thể thêm tối đa ' + remaining + ' sản phẩm nữa cho size ' + selectedSize + '!', 'error');
            }
            return; // DỪNG — không chạy animation, không cập nhật giỏ
        }
        // --- KẾT THÚC KIỂM TRA TỒN KHO ---

        // --- Hiệu ứng ảnh bay vào giỏ hàng (GIỮ NGUYÊN) ---
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
            const existingItemIndex = cart.findIndex(item => item.id === currentProduct.id && item.size === selectedSize);

            if (existingItemIndex !== -1) {
                cart[existingItemIndex].quantity += currentQty;
            } else {
                cart.push({
                    id: currentProduct.id,
                    name: currentProduct.name,
                    image: currentProduct.image,
                    price: currentProduct.price,
                    size: selectedSize,
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