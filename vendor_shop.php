<?php
include 'includes/header.php';

$vendorId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($vendorId <= 0) {
    echo "<script>alert('Shop không tồn tại!'); window.location.href='shop.php';</script>";
    exit;
}

// Lấy thông tin Vendor
$sqlVendor = "SELECT fullname FROM users WHERE id = ? AND role IN ('sales', 'admin')";
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
    <div class="vendor-hero__overlay"></div>
    <div class="grid wide vendor-hero__content">
        <div class="vendor-avatar">
            <i class="fa-solid fa-store"></i>
        </div>
        <h1 class="vendor-hero__name"><?php echo htmlspecialchars($vendor['fullname'] ?: 'SmartFit Shop'); ?></h1>
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
            <span class="config-modal__label">Màu sắc</span>
            <div id="modalColorOptions" class="config-modal__colors"></div>
        </div>
        <div class="config-modal__group">
            <span class="config-modal__label">Kích cỡ</span>
            <div id="modalSizeOptions" class="config-modal__sizes"></div>
            <span id="modalStockInfo" class="config-modal__stock"></span>
        </div>
        <div class="config-modal__group">
            <span class="config-modal__label">Số lượng</span>
            <div class="config-modal__qty">
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeModalQty(-1)"><i class="fa-solid fa-minus"></i></button>
                    <input type="number" id="modalQtyDisplay" value="1" class="qty-input" onchange="validateModalQty(this)" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    <button class="qty-btn" onclick="changeModalQty(1)"><i class="fa-solid fa-plus"></i></button>
                </div>
                <span class="config-modal__stock" id="modalStockInfo">Vui lòng chọn size</span>
            </div>
        </div>
        <button id="btnConfirmAdd" class="config-modal__btn">
            <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ hàng
        </button>
    </div>
</div>

<style>
    /* Vendor Hero */
    .vendor-hero {
        position: relative;
        background: var(--primary-blue);
        padding: 70px 0 50px;
        text-align: center;
        overflow: hidden;
        margin-bottom: 40px;
    }
    .vendor-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(circle at 30% 50%, rgba(99,102,241,0.3) 0%, transparent 60%),
                    radial-gradient(circle at 70% 50%, rgba(59,130,246,0.2) 0%, transparent 60%);
    }
    .vendor-hero__content { position: relative; z-index: 1; }
    .vendor-avatar {
        width: 90px; height: 90px; border-radius: 50%;
        background: rgba(255,255,255,0.1);
        border: 3px solid rgba(255,255,255,0.3);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 3.5rem; color: #fff;
        backdrop-filter: blur(10px);
    }
    .vendor-hero__name { font-size: 3.5rem; font-weight: 800; color: #fff; margin-bottom: 20px; }
    .vendor-hero__bio { font-size: 1.5rem; color: rgba(255,255,255,0.75); max-width: 550px; margin: 0 auto 24px; line-height: 1.7; }
    .vendor-hero__stats { display: flex; gap: 40px; justify-content: center; }
    .vendor-stat { text-align: center; }
    .vendor-stat__num { display: block; font-size: 2rem; font-weight: 700; color: #fff; }
    .vendor-stat__label { font-size: 1.2rem; color: rgba(255,255,255,0.6); }

    /* Section */
    .vendor-shop-section { padding-bottom: 60px; }

    /* Modal qty control */
    .qty-control { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; width: fit-content; }
    .qty-btn { background: #f8f9fa; border: none; width: 32px; height: 32px; cursor: pointer; transition: background 0.2s; }
    .qty-btn:hover { background: #1d1d1f; color: white; }
    .qty-input { width: 40px; height: 32px; text-align: center; border: none; border-left: 1px solid #ddd; border-right: 1px solid #ddd; font-weight: 600; background: #fff; }
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

    // === MODAL LOGIC (reused from shop.php) ===
    let currentSelectedItem = null, selectedConfigSize = null, selectedConfigColor = null;
    let modalCurrentQty = 1, modalMaxStock = 0;

    function openConfigModal(item) {
        currentSelectedItem = item;
        document.getElementById('modalProductImg').src = item.image;
        document.getElementById('modalProductName').textContent = item.name;
        document.getElementById('modalProductPrice').textContent = formatPrice(item.price);
        selectedConfigSize = null; selectedConfigColor = null;

        const colorContainer = document.getElementById('modalColorOptions');
        colorContainer.innerHTML = '';
        if (item.colors && item.colors.length > 0) {
            item.colors.forEach(c => {
                const btn = document.createElement('span');
                btn.className = 'config-color-btn';
                btn.style.background = c.hex_code;
                btn.title = c.color_name;
                const colorStock = item.sizes.filter(s => s.color_id == c.id).reduce((sum, s) => sum + parseInt(s.quantity), 0);
                if (colorStock <= 0) {
                    btn.classList.add('out-of-stock');
                } else {
                    btn.onclick = () => {
                        document.querySelectorAll('.config-color-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        selectedConfigColor = c.color_name;
                        if (c.image) document.getElementById('modalProductImg').src = c.image;
                        renderSizesForColor(c.id);
                    };
                }
                colorContainer.appendChild(btn);
            });
            const firstAvailable = colorContainer.querySelector('.config-color-btn:not(.out-of-stock)');
            if (firstAvailable) firstAvailable.click();
        } else {
            colorContainer.innerHTML = '<p style="font-size:1.2rem;color:#999;">Không có biến thể màu</p>';
            selectedConfigColor = 'Default';
            renderSizesForColor(null);
        }
        document.getElementById('productConfigModal').classList.add('active');
        document.getElementById('btnConfirmAdd').onclick = () => confirmAddToCart();
    }

    function renderSizesForColor(colorId) {
        const sizeContainer = document.getElementById('modalSizeOptions');
        const stockInfo = document.getElementById('modalStockInfo');
        sizeContainer.innerHTML = '';
        selectedConfigSize = null; modalMaxStock = 0; modalCurrentQty = 1;
        document.getElementById('modalQtyDisplay').value = 1;
        stockInfo.textContent = 'Vui lòng chọn size';

        const allSizes = currentSelectedItem.sizes || [];
        const uniqueSizeNames = [...new Set(allSizes.map(s => s.size_name))];
        const sizeOrder = ['S', 'M', 'L', 'XL', '2XL', 'XXL', '3XL', 'Oversize'];
        uniqueSizeNames.sort((a, b) => { const ia = sizeOrder.indexOf(a), ib = sizeOrder.indexOf(b); if (ia !== -1 && ib !== -1) return ia - ib; return a.localeCompare(b); });

        uniqueSizeNames.forEach(sizeName => {
            const btn = document.createElement('button');
            btn.className = 'config-size-btn';
            btn.textContent = sizeName;
            const sizeData = allSizes.find(s => s.size_name === sizeName && s.color_id == colorId);
            const qty = sizeData ? parseInt(sizeData.quantity) : 0;
            if (!sizeData || qty <= 0) {
                btn.classList.add('out-of-stock');
            } else {
                btn.onclick = () => {
                    document.querySelectorAll('.config-size-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    selectedConfigSize = sizeName; modalMaxStock = qty; modalCurrentQty = 1;
                    document.getElementById('modalQtyDisplay').value = 1;
                    stockInfo.textContent = `Còn ${qty} sản phẩm`;
                };
            }
            sizeContainer.appendChild(btn);
        });
    }

    function changeModalQty(amount) {
        if (!selectedConfigSize) { showToast('Vui lòng chọn kích cỡ trước!', 'error'); return; }
        let next = modalCurrentQty + amount;
        if (next < 1) next = 1;
        if (next > modalMaxStock) { showToast('Chỉ còn ' + modalMaxStock + ' sản phẩm!', 'error'); next = modalMaxStock; }
        modalCurrentQty = next;
        document.getElementById('modalQtyDisplay').value = next;
    }

    function validateModalQty(input) {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > modalMaxStock) { showToast('Chỉ còn ' + modalMaxStock + ' sản phẩm!', 'error'); val = modalMaxStock; }
        modalCurrentQty = val; input.value = val;
    }

    function closeConfigModal() { document.getElementById('productConfigModal').classList.remove('active'); }

    function confirmAddToCart() {
        if (!selectedConfigSize || !selectedConfigColor) {
            showToast('Vui lòng chọn đầy đủ Kích cỡ và Màu sắc!', 'error'); return;
        }
        fetch('includes/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ outfit_id: currentSelectedItem.id, size: selectedConfigSize, color: selectedConfigColor, quantity: modalCurrentQty })
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
        .catch(() => showToast('Lỗi kết nối máy chủ!', 'error'));
        closeConfigModal();
    }

    window.addEventListener('DOMContentLoaded', () => {
        loadProducts();
        initFilters();
        window.onclick = (e) => { if (e.target == document.getElementById('productConfigModal')) closeConfigModal(); };
    });
</script>

<?php include 'includes/footer.php'; ?>
