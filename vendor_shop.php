<?php
include 'includes/header.php';

$vendorId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($vendorId <= 0) {
    echo "<script>alert('Shop không tồn tại!'); window.location.href='shop.php';</script>";
    exit;
}

// Lấy thông tin Vendor
$sqlVendor = "SELECT fullname, avatar FROM users WHERE id = ? AND role IN ('sales', 'admin')";
$stmtVendor = mysqli_prepare($conn, $sqlVendor);
mysqli_stmt_bind_param($stmtVendor, "i", $vendorId);
mysqli_stmt_execute($stmtVendor);
$resVendor = mysqli_stmt_get_result($stmtVendor);
$vendor = mysqli_fetch_assoc($resVendor);

if (!$vendor) {
    echo "<script>alert('Shop không tồn tại hoặc không có quyền bán hàng!'); window.location.href='shop.php';</script>";
    exit;
}

// Đếm tổng sản phẩm của vendor
$sqlCount = "SELECT COUNT(*) as total FROM outfits WHERE owner_id = ? AND is_commercial = 1";
$stmtCount = mysqli_prepare($conn, $sqlCount);
mysqli_stmt_bind_param($stmtCount, "i", $vendorId);
mysqli_stmt_execute($stmtCount);
$totalProducts = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount))['total'];
?>

<!-- Hero Banner -->
<div class="vendor-hero">
    <div class="grid wide vendor-hero__content">
        <div class="vendor-hero__left">
            <div class="vendor-avatar">
                <?php if (!empty($vendor['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($vendor['avatar']); ?>" alt="Shop Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="fa-solid fa-store"></i>
                <?php endif; ?>
            </div>
            <h1 class="vendor-hero__name"><?php echo htmlspecialchars($vendor['fullname'] ?: 'SmartFit Shop'); ?></h1>
        </div>
        <div class="vendor-hero__stats">
            <div class="vendor-stat">
                <span class="vendor-stat__num"><?php echo $totalProducts; ?></span>
                <span class="vendor-stat__label">Sản phẩm</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<section class="vendor-shop-section">
    <div class="grid wide">
        <div class="shop-page__content">

            <!-- Sidebar Filter -->
            <aside class="shop-sidebar">
                <div class="shop-filter">
                    <h3 class="shop-filter__title">Danh mục</h3>
                    <ul class="shop-filter__list" id="categoryFilter">
                        <li class="shop-filter__item active" data-type="all">Tất cả sản phẩm</li>
                        <li class="shop-filter__item" data-type="top">Áo</li>
                        <li class="shop-filter__item" data-type="bottom">Quần</li>
                        <li class="shop-filter__item" data-type="one-piece">Trang phục nguyên bộ</li>
                        <li class="shop-filter__item" data-type="accessory_shoes">Giày & Phụ kiện</li>
                    </ul>
                </div>

                <div class="shop-filter">
                    <div class="shop-filter__header">
                        <h3 class="shop-filter__title">Kích cỡ</h3>
                        <button class="shop-filter__clear" onclick="resetSizeFilter()">Xóa</button>
                    </div>
                    <div class="shop-filter__sizes" id="sizeFilter">
                        <span class="shop-filter__size" data-size="S">S</span>
                        <span class="shop-filter__size" data-size="M">M</span>
                        <span class="shop-filter__size" data-size="L">L</span>
                        <span class="shop-filter__size" data-size="XL">XL</span>
                        <span class="shop-filter__size" data-size="Oversize">Oversize</span>
                    </div>
                </div>

                <div class="shop-filter">
                    <h3 class="shop-filter__title">Khoảng giá</h3>
                    <div class="shop-filter__price" id="priceFilter">
                        <label class="shop-filter__checkbox">
                            <input type="radio" name="price_range" data-min="0" data-max="0" checked> Tất cả giá
                        </label>
                        <label class="shop-filter__checkbox">
                            <input type="radio" name="price_range" data-min="0" data-max="200000"> 0đ - 200.000đ
                        </label>
                        <label class="shop-filter__checkbox">
                            <input type="radio" name="price_range" data-min="200000" data-max="500000"> 200.000đ - 500.000đ
                        </label>
                        <label class="shop-filter__checkbox">
                            <input type="radio" name="price_range" data-min="500000" data-max="9999999"> Trên 500.000đ
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Main product area -->
            <div class="shop-main">
                <div class="shop-toolbar">
                    <div class="shop-search">
                        <i class="fa-solid fa-magnifying-glass shop-search__icon"></i>
                        <input type="text" class="shop-search__input" placeholder="Tìm kiếm sản phẩm trong shop...">
                    </div>
                    <div class="shop-sort">
                        <select class="shop-sort__select" id="sortFilter">
                            <option value="newest">Mới nhất</option>
                            <option value="price-asc">Giá tăng dần</option>
                            <option value="price-desc">Giá giảm dần</option>
                            <option value="oldest">Cũ nhất</option>
                        </select>
                    </div>
                </div>

                <div id="productGrid" class="row shop__products shop-grid">
                    <!-- AJAX content -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Config Modal (same as shop.php) -->
<div id="productConfigModal" class="config-modal">
    <div class="config-modal__container">
        <button class="config-modal__close" onclick="closeConfigModal()">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="config-modal__product">
            <img id="modalProductImg" src="" alt="" class="config-modal__img">
            <div class="config-modal__info">
                <h3 id="modalProductName">Tên sản phẩm</h3>
                <div id="modalProductPrice" class="config-modal__price">0đ</div>
            </div>
        </div>
        <div class="config-modal__group">
            <span class="config-modal__label">Kích cỡ</span>
            <div id="modalSizeOptions" class="config-modal__sizes">
                <!-- Sizes will be loaded here -->
            </div>
        </div>

        <div class="config-modal__group">
            <span class="config-modal__label">Màu sắc</span>
            <div id="modalColorOptions" class="config-modal__colors">
                <!-- Colors will be loaded here -->
            </div>
        </div>

        <div class="config-modal__group">
            <span class="config-modal__label">Số lượng</span>
            <div class="config-modal__qty">
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeModalQty(-1)">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <input type="number" id="modalQtyDisplay" value="1" class="qty-input" onchange="validateModalQty(this)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    <button class="qty-btn" onclick="changeModalQty(1)">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <span id="modalStockInfo" class="config-modal__stock">Vui lòng chọn size</span>
            </div>
        </div>

        <button id="btnConfirmAdd" class="config-modal__btn-confirm">Xác nhận thêm vào giỏ</button>
    </div>
</div>

<style>
    /* Vendor Hero - Monochrome Redesign */
    .vendor-hero {
        background: var(--card-bg);
        padding: 40px 0;
        border-bottom: 1px solid var(--border-color);
    }
    .vendor-hero__content { 
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .vendor-hero__left {
        display: flex;
        align-items: center;
        gap: 25px;
    }
    .vendor-avatar {
        width: 100px; height: 100px; border-radius: 50%;
        background: var(--apple-bg);
        border: 1px solid var(--border-color);
        display: flex; align-items: center; justify-content: center;
        font-size: 4rem; color: var(--apple-grey);
        overflow: hidden;
        flex-shrink: 0;
    }
    .vendor-hero__name { 
        font-size: 3rem; 
        font-weight: 700; 
        color: var(--apple-black); 
        letter-spacing: -0.5px;
        margin: 0;
    }
    .vendor-hero__stats { display: flex; gap: 40px; }
    .vendor-stat { text-align: center; }
    .vendor-stat__num { 
        display: block; 
        font-size: 2.4rem; 
        font-weight: 700; 
        color: var(--apple-black); 
        margin-bottom: 4px;
    }
    .vendor-stat__label { 
        font-size: 1.2rem; 
        color: var(--apple-grey); 
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Responsive cho Vendor Hero */
    @media (max-width: 767px) {
        .vendor-hero__content {
            flex-direction: column;
            gap: 25px;
            text-align: center;
        }
        .vendor-hero__left {
            flex-direction: column;
            gap: 15px;
        }
        .vendor-hero__name {
            font-size: 2.4rem;
        }
    }

    /* Section */
    .vendor-shop-section { padding-bottom: 60px; }

        <style>
            .config-modal__qty {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .config-modal__stock {
                font-size: 1.2rem;
                color: #888;
            }
            /* Tái sử dụng hoặc định nghĩa lại qty-control cho modal shop */
            .qty-control {
                display: flex;
                align-items: center;
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                width: fit-content;
            }
            .qty-btn {
                background: #f8f9fa;
                border: none;
                width: 32px;
                height: 32px;
                cursor: pointer;
                transition: background 0.2s;
            }
            .qty-btn:hover { background: black; color: white; }
            .qty-input {
                width: 40px;
                height: 32px;
                text-align: center;
                border: none;
                border-left: 1px solid #ddd;
                border-right: 1px solid #ddd;
                font-weight: 600;
                background: #fff;
            }

            /* CSS mới cho nút màu sắc hiển thị ảnh */
            .config-color-btn {
                width: 45px !important;
                height: 45px !important;
                border-radius: 8px !important;
                border: 2px solid transparent !important;
                cursor: pointer;
                transition: all 0.2s ease;
                background-size: cover !important;
                background-position: center !important;
                position: relative;
                display: inline-block;
                margin-right: 8px;
                margin-bottom: 8px;
            }
            .config-color-btn:hover {
                transform: scale(1.05);
                border-color: #ddd !important;
            }
            .config-color-btn.active {
                border: 2px solid #ee4d2d !important;
            }
            .config-color-btn.active::after {
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
            .config-color-btn.active::before {
                content: '✓';
                position: absolute;
                bottom: -2px;
                right: 0;
                color: white;
                font-size: 8px;
                z-index: 10;
            }

            .config-color-btn.out-of-stock {
                opacity: 0.4;
                cursor: not-allowed;
            }
            .config-color-btn.out-of-stock::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                width: 100%;
                height: 1px;
                background: #ff3b30;
                transform: rotate(45deg);
            }

            /* Style cho nút kích cỡ */
            .config-size-btn {
                min-width: 60px;
                height: 45px;
                border: 1px solid #ddd;
                background-color: #fff;
                color: #333;
                font-size: 1.5rem;
                font-weight: 600;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
                margin-right: 8px;
                margin-bottom: 8px;
            }

            .config-size-btn:hover {
                border-color: #333;
            }

            .config-size-btn.active {
                border-color: #ee4d2d !important;
                background-color: #fff9f8 !important;
                color: #ee4d2d !important;
            }

            .config-size-btn.out-of-stock {
                opacity: 0.4;
                cursor: not-allowed;
                text-decoration: line-through;
            }

            /* Style cho nút Xác nhận thêm vào giỏ trong popup */
            .config-modal__btn-confirm {
                width: 100%;
                padding: 16px;
                background: #1d1d1f;
                color: #fff;
                border: none;
                border-radius: 12px;
                font-size: 1.6rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                margin-top: 20px;
            }

            .config-modal__btn-confirm:hover {
                background: #000;
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            }

            .config-modal__btn-confirm:active {
                transform: translateY(0);
            }
        </style>

<script>
    const VENDOR_ID = <?php echo $vendorId; ?>;

    // Load products filtered by this vendor
    async function loadProducts(extra = '') {
        try {
            const params = new URLSearchParams(extra ? extra.replace('?','') : '');
            params.set('owner_id', VENDOR_ID);
            const response = await fetch(`includes/api_outfits.php?${params.toString()}`);
            const data = await response.json();
            const grid = document.getElementById('productGrid');
            if (!grid) return;
            grid.innerHTML = '';

            if (!data.items || data.items.length === 0) {
                grid.innerHTML = '<div class="col l-12" style="text-align:center; padding: 60px; font-size:1.6rem; color:#888;"><i class="fa-solid fa-box-open" style="font-size:4rem;display:block;margin-bottom:15px;opacity:0.4;"></i>Không tìm thấy sản phẩm nào.</div>';
                return;
            }

            data.items.forEach(item => {
                grid.innerHTML += `
                <div class="col l-3 m-4 c-6">
                    <div class="product-card">
                        <a href="detail.php?id=${item.id}" class="product-card__img" style="display:block;">
                            <img src="${item.image}" alt="${item.name}" onerror="this.src='./assets/img/default-placeholder.jpg'">
                        </a>
                        <div class="product-card__info">
                            <h3 class="product-card__name">
                                <a href="detail.php?id=${item.id}" style="text-decoration:none;color:inherit;">${item.name}</a>
                            </h3>
                            <div class="product-card__price">${formatPrice(item.price)}</div>
                            <div class="product-card__buy" onclick='${isLoggedIn ? `openConfigModal(${JSON.stringify(item).replace(/'/g, "\\'")})` : `showToast("Vui lòng đăng nhập để mua hàng!", "info")`}'>
                                <i class="fa-solid ${isLoggedIn ? 'fa-cart-shopping' : 'fa-circle-user'}"></i>
                                ${isLoggedIn ? 'Thêm vào giỏ' : 'Đăng nhập để mua'}
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        } catch (error) { console.error("Lỗi tải sản phẩm:", error); }
    }

    function applyFilters() {
        const type = document.querySelector('#categoryFilter .shop-filter__item.active')?.dataset.type || 'all';
        const size = document.querySelector('#sizeFilter .shop-filter__size.active')?.dataset.size || '';
        const sort = document.getElementById('sortFilter').value;
        const query = document.querySelector('.shop-search__input').value.trim();
        const selectedPrice = document.querySelector('input[name="price_range"]:checked');
        const minPrice = selectedPrice?.dataset.min || 0;
        const maxPrice = selectedPrice?.dataset.max || 0;

        const params = new URLSearchParams({ type, sort, size, min_price: minPrice, max_price: maxPrice, q: query });
        loadProducts(`?${params.toString()}`);
    }

    function resetSizeFilter() {
        document.querySelectorAll('#sizeFilter .shop-filter__size').forEach(i => i.classList.remove('active'));
        applyFilters();
    }

    function initFilters() {
        let searchTimeout;
        document.querySelector('.shop-search__input').addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });
        document.querySelectorAll('#categoryFilter .shop-filter__item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('#categoryFilter .shop-filter__item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                applyFilters();
            });
        });
        document.querySelectorAll('#sizeFilter .shop-filter__size').forEach(item => {
            item.addEventListener('click', () => {
                if (item.classList.contains('active')) {
                    item.classList.remove('active');
                } else {
                    document.querySelectorAll('#sizeFilter .shop-filter__size').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                }
                applyFilters();
            });
        });
        document.getElementById('sortFilter').addEventListener('change', applyFilters);
        document.querySelectorAll('input[name="price_range"]').forEach(input => {
            input.addEventListener('change', applyFilters);
        });
    }

    // === MODAL CONFIGURATION LOGIC (Synced with shop.php) ===
    let currentSelectedItem = null;
    let selectedConfigSize = null;
    let selectedConfigColor = null;

    function openConfigModal(item) {
        currentSelectedItem = item;
        const modal = document.getElementById('productConfigModal');
        
        // Set basic info
        document.getElementById('modalProductImg').src = item.image;
        document.getElementById('modalProductName').textContent = item.name;
        document.getElementById('modalProductPrice').textContent = formatPrice(item.price);
        
        // Reset selections
        selectedConfigSize = null;
        selectedConfigColor = null;

        // 1. Load Colors
        const colorContainer = document.getElementById('modalColorOptions');
        colorContainer.innerHTML = '';
        
        if (item.colors && item.colors.length > 0) {
            // Đếm tổng stock cho mỗi màu
            item.colors.forEach((c, index) => {
                const btn = document.createElement('span');
                btn.className = 'config-color-btn';
                
                // Ưu tiên hiển thị ảnh nếu có, nếu không thì dùng mã màu
                if (c.image) {
                    btn.style.background = `url('${c.image}')`;
                } else {
                    btn.style.background = c.hex_code;
                }
                
                btn.title = c.color_name;
                
                // Kiểm tra tồn kho tổng của màu này
                const colorStock = item.sizes
                    .filter(s => s.color_id == c.id)
                    .reduce((sum, s) => sum + parseInt(s.quantity), 0);

                if (colorStock <= 0) {
                    btn.classList.add('out-of-stock');
                    btn.title = `${c.color_name} (Hết hàng)`;
                } else {
                    btn.onclick = () => {
                        document.querySelectorAll('.config-color-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        selectedConfigColor = c.color_name;
                        
                        // Đổi ảnh theo màu
                        if (c.image) {
                            document.getElementById('modalProductImg').src = c.image;
                        }

                        // Load lại size theo màu đã chọn
                        renderSizesForColor(c.id);
                    };
                }
                
                colorContainer.appendChild(btn);
            });

            // Tự động chọn màu đầu tiên CÒN HÀNG
            const firstAvailableBtn = colorContainer.querySelector('.config-color-btn:not(.out-of-stock)');
            if (firstAvailableBtn) {
                firstAvailableBtn.click();
            } else {
                // Nếu tất cả màu đều hết hàng
                renderSizesForColor(null);
            }
        } else {
            colorContainer.innerHTML = '<p style="font-size:1.2rem; color:#999;">Không có biến thể màu sắc</p>';
            selectedConfigColor = 'Default';
            renderSizesForColor(null);
        }

        modal.classList.add('active');
        
        // Confirm button action
        document.getElementById('btnConfirmAdd').onclick = () => confirmAddToCart();
    }

    let modalCurrentQty = 1;
    let modalMaxStock = 0;

    function changeModalQty(amount) {
        if (!selectedConfigSize) {
            showToast('Vui lòng chọn kích cỡ trước!', 'error');
            return;
        }
        let nextQty = modalCurrentQty + amount;
        if (nextQty < 1) nextQty = 1;
        if (nextQty > modalMaxStock) {
            showToast('Chỉ còn ' + modalMaxStock + ' sản phẩm trong kho!', 'error');
            nextQty = modalMaxStock;
        }
        modalCurrentQty = nextQty;
        document.getElementById('modalQtyDisplay').value = modalCurrentQty;
    }

    // --- HÀM VALIDATE SỐ LƯỢNG KHI NHẬP THỦ CÔNG TRÊN MODAL ---
    function validateModalQty(input) {
        if (!selectedConfigSize) {
            showToast('Vui lòng chọn kích cỡ trước!', 'error');
            input.value = 1;
            return;
        }
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) {
            val = 1;
        }
        if (val > modalMaxStock) {
            showToast('Chỉ còn ' + modalMaxStock + ' sản phẩm trong kho!', 'error');
            val = modalMaxStock;
        }
        modalCurrentQty = val;
        input.value = val;
    }

    // Hàm render size dựa trên màu sắc được chọn
    function renderSizesForColor(colorId) {
        const sizeContainer = document.getElementById('modalSizeOptions');
        const stockInfo = document.getElementById('modalStockInfo');
        sizeContainer.innerHTML = '';
        selectedConfigSize = null;
        modalMaxStock = 0;
        modalCurrentQty = 1;
        document.getElementById('modalQtyDisplay').value = 1;
        stockInfo.textContent = 'Vui lòng chọn size';
        stockInfo.style.color = '#999';

        // Lấy danh sách tất cả các tên size duy nhất của sản phẩm này (để hiện đầy đủ)
        const allSizes = currentSelectedItem.sizes || [];
        const uniqueSizeNames = [...new Set(allSizes.map(s => s.size_name))];
        
        // Sắp xếp size cơ bản
        const sizeOrder = ['S', 'M', 'L', 'XL', '2XL', 'XXL', '3XL', 'Oversize'];
        uniqueSizeNames.sort((a, b) => {
            const ia = sizeOrder.indexOf(a);
            const ib = sizeOrder.indexOf(b);
            if (ia !== -1 && ib !== -1) return ia - ib;
            return a.localeCompare(b);
        });

        if (uniqueSizeNames.length > 0) {
            uniqueSizeNames.forEach(sizeName => {
                const btn = document.createElement('button');
                btn.className = 'config-size-btn';
                btn.textContent = sizeName;

                // Kiểm tra xem size này có tồn kho cho màu đang chọn không
                const sizeData = allSizes.find(s => s.size_name === sizeName && s.color_id == colorId);
                const qty = sizeData ? parseInt(sizeData.quantity) : 0;

                if (!sizeData || qty <= 0) {
                    btn.classList.add('out-of-stock');
                    btn.title = "Hết hàng";
                } else {
                    btn.onclick = () => {
                        document.querySelectorAll('.config-size-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        selectedConfigSize = sizeName;
                        modalMaxStock = qty;
                        modalCurrentQty = 1;
                        document.getElementById('modalQtyDisplay').value = 1;
                        stockInfo.textContent = `Còn ${qty} sản phẩm`;
                        stockInfo.style.color = 'var(--success)';
                    };
                }
                sizeContainer.appendChild(btn);
            });
        } else {
            sizeContainer.innerHTML = '<p style="font-size:1.2rem; color:#999;">Hết hàng</p>';
        }
    }

    function closeConfigModal() {
        document.getElementById('productConfigModal').classList.remove('active');
    }

    function confirmAddToCart() {
        if (!currentSelectedItem) return;
        
        if (!selectedConfigSize || !selectedConfigColor) {
            showToast('Vui lòng chọn đầy đủ Kích cỡ và Màu sắc!', 'error');
            return;
        }
        
        fetch('includes/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                outfit_id: currentSelectedItem.id,
                size: selectedConfigSize,
                color: selectedConfigColor,
                quantity: modalCurrentQty
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showToast(data.message, 'success');
                if (typeof syncCart === 'function') syncCart();
                if (typeof cartDrawerApp !== 'undefined') cartDrawerApp.openCart();
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error('Lỗi thêm giỏ hàng:', err);
            showToast('Lỗi kết nối máy chủ!', 'error');
        });
        
        closeConfigModal();
    }

    window.addEventListener('DOMContentLoaded', () => {
        loadProducts();
        initFilters();
        window.onclick = (e) => { if (e.target == document.getElementById('productConfigModal')) closeConfigModal(); };
    });
</script>

<?php include 'includes/footer.php'; ?>
