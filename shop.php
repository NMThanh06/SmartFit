<?php
$page_extra_body = '<div class="web__background--overlay"></div>';
include 'includes/header.php';
?>

        <!-- Shop Section -->
        <section class="shop-page">
            <div class="grid wide">
                <div class="shop-page__header">
                    <h1 class="shop-page__title">Cửa hàng SmartFit</h1>
                    <p class="shop-page__subtitle">Khám phá phong cách thời trang dẫn đầu xu hướng</p>
                </div>

                <div class="shop-page__content">
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
                                <button class="shop-filter__clear" onclick="resetSizeFilter()" title="Xóa bộ lọc kích cỡ">Xóa</button>
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

                    <div class="shop-main">
                        <div class="shop-toolbar">
                            <div class="shop-search">
                                <i class="fa-solid fa-magnifying-glass shop-search__icon"></i>
                                <input type="text" class="shop-search__input" placeholder="Tìm kiếm sản phẩm...">
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
                            <!-- Products will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Configuration Modal -->
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

        <?php include 'includes/footer.php'; ?>
        
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
        </style>

    <!-- Backend cho trang cửa hàng -->
    <script>
        // ========================================
        // SHOP-SPECIFIC FUNCTIONS
        // ========================================


        // 2. Tải danh sách sản phẩm trang Shop
        // 2. Tải danh sách sản phẩm trang Shop
        async function loadProducts(params = '') {
            try {
                const response = await fetch(`includes/api_outfits.php${params}`);
                const data = await response.json();
                const grid = document.getElementById('productGrid');
                if (!grid) return;
                grid.innerHTML = '';

                if (data.items.length === 0) {
                    grid.innerHTML = '<div class="col l-12" style="text-align:center; padding: 40px; font-size:1.6rem; color:#888;">Không tìm thấy sản phẩm nào phù hợp.</div>';
                    return;
                }

                data.items.forEach(item => {
                    // Đảm bảo item.sizes luôn là mảng
                    if (typeof item.sizes === 'string') {
                        item.sizes = item.sizes.split(',').filter(s => s.trim() !== '');
                    }
                    
                    grid.innerHTML += `
                    <div class="col l-3 m-4 c-6">
                        <div class="product-card">
                            <a href="detail.php?id=${item.id}" class="product-card__img" style="display: block;">
                                <img src="${item.image}" alt="${item.name}" onerror="this.src='./assets/img/default-placeholder.jpg'">
                            </a>
                            <div class="product-card__info">
                                <h3 class="product-card__name">
                                    <a href="detail.php?id=${item.id}" style="text-decoration: none; color: inherit;">${item.name}</a>
                                </h3>
                                <div class="product-card__price">${formatPrice(item.price)}</div>
                                
                                <div class="product-card__buy" onclick='${isLoggedIn ? `openConfigModal(${JSON.stringify(item).replace(/'/g, "\\'")})` : "window.location.href=\"#\"; showToast(\"Vui lòng đăng nhập để mua hàng!\", \"info\");"}'>
                                    <i class="fa-solid ${isLoggedIn ? 'fa-cart-shopping' : 'fa-circle-user'}"></i> 
                                    ${isLoggedIn ? 'Thêm vào giỏ' : 'Đăng nhập để mua'}
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } catch (error) { console.error("Lỗi tải sản phẩm:", error); }
        }

        // --- HÀM LỌC TỔNG HỢP ---
        function applyFilters() {
            const type = document.querySelector('#categoryFilter .shop-filter__item.active')?.dataset.type || 'all';
            const size = document.querySelector('#sizeFilter .shop-filter__size.active')?.dataset.size || '';
            const sort = document.getElementById('sortFilter').value;
            const query = document.querySelector('.shop-search__input').value.trim();
            
            const selectedPrice = document.querySelector('input[name="price_range"]:checked');
            const minPrice = selectedPrice?.dataset.min || 0;
            const maxPrice = selectedPrice?.dataset.max || 0;

            const params = new URLSearchParams({
                type: type,
                sort: sort,
                size: size,
                min_price: minPrice,
                max_price: maxPrice,
                q: query
            });

            loadProducts(`?${params.toString()}`);
        }

        // --- HÀM RESET RIÊNG CHO SIZE ---
        function resetSizeFilter() {
            document.querySelectorAll('#sizeFilter .shop-filter__size').forEach(i => i.classList.remove('active'));
            applyFilters();
        }

        // --- KHỞI TẠO SỰ KIỆN LỌC ---
        function initShopFilters() {
            // 0. Tìm kiếm theo từ khóa (Debounce 300ms)
            let searchTimeout;
            document.querySelector('.shop-search__input').addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 300);
            });
            // 1. Lọc theo Danh mục
            document.querySelectorAll('#categoryFilter .shop-filter__item').forEach(item => {
                item.addEventListener('click', () => {
                    document.querySelectorAll('#categoryFilter .shop-filter__item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                    applyFilters();
                });
            });

            // 2. Lọc theo Kích cỡ
            document.querySelectorAll('#sizeFilter .shop-filter__size').forEach(item => {
                item.addEventListener('click', () => {
                    if (item.classList.contains('active')) {
                        item.classList.remove('active'); // Bỏ chọn
                    } else {
                        document.querySelectorAll('#sizeFilter .shop-filter__size').forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                    }
                    applyFilters();
                });
            });

            // 3. Sắp xếp
            document.getElementById('sortFilter').addEventListener('change', applyFilters);

            // 4. Lọc theo Giá
            document.querySelectorAll('input[name="price_range"]').forEach(input => {
                input.addEventListener('change', applyFilters);
            });
        }

        // ========================================
        // MODAL CONFIGURATION LOGIC
        // ========================================
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
                    btn.style.background = c.hex_code;
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

        function selectColor(btn, colorName) {
            document.querySelectorAll('.config-color-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedConfigColor = colorName;
        }

        function confirmAddToCart() {
            if (!currentSelectedItem) return;
            
            if (!selectedConfigSize || !selectedConfigColor) {
                if (window.showToast) {
                    showToast('Vui lòng chọn đầy đủ Kích cỡ và Màu sắc!', 'error');
                } else {
                    alert('Vui lòng chọn đầy đủ Kích cỡ và Màu sắc!');
                }
                return;
            }
            
            // Gọi logic thêm vào giỏ hàng thực sự với số lượng chọn
            performAddToCart(
                currentSelectedItem.id, 
                currentSelectedItem.name, 
                currentSelectedItem.image, 
                currentSelectedItem.price, 
                selectedConfigSize, 
                selectedConfigColor,
                modalCurrentQty
            );
            
            closeConfigModal();
            
            // Animation hiệu ứng bay từ modal
            const modalImg = document.getElementById('modalProductImg');
            animateFly(modalImg);
        }

        function performAddToCart(id, name, imageSrc, price, size, color, qty = 1) {
            // --- KIỂM TRA TỒN KHO TRƯỚC KHI THÊM ---
            const existingIndex = cart.findIndex(item => item.id === id && item.size === size && item.color === color);
            const qtyInCart = existingIndex !== -1 ? cart[existingIndex].quantity : 0;
            const totalExpected = qtyInCart + qty;

            if (totalExpected > modalMaxStock) {
                const remaining = modalMaxStock - qtyInCart;
                if (remaining <= 0) {
                    showToast(`Sản phẩm ${name} (Size ${size}, ${color}) đã đạt giới hạn tồn kho trong giỏ hàng!`, 'error');
                } else {
                    showToast('Chỉ có thể thêm tối đa ' + remaining + ' sản phẩm nữa!', 'error');
                }
                return;
            }

            // Gửi xuống Database Cart API
            fetch('includes/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    outfit_id: id,
                    size: size,
                    color: color,
                    quantity: qty
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    // Đồng bộ lại UI
                    if (typeof syncCart === 'function') {
                        syncCart();
                    }
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

        function animateFly(targetImg) {
            const cartIcon = document.querySelector('.navbar__cart');
            if (!targetImg || !cartIcon) return;

            const flyImg = targetImg.cloneNode();
            const imgRect = targetImg.getBoundingClientRect();
            const cartRect = cartIcon.getBoundingClientRect();

            flyImg.classList.add('fly-item');
            flyImg.style.top = `${imgRect.top}px`;
            flyImg.style.left = `${imgRect.left}px`;
            flyImg.style.width = `${imgRect.width}px`;
            flyImg.style.height = `${imgRect.height}px`;

            document.body.appendChild(flyImg);

            setTimeout(() => {
                flyImg.style.top = `${cartRect.top + 10}px`;
                flyImg.style.left = `${cartRect.left + 10}px`;
                flyImg.style.width = '20px'; 
                flyImg.style.height = '20px'; 
                flyImg.style.opacity = '0';
            }, 10);

            setTimeout(() => flyImg.remove(), 800);
        }

        // Khởi động khi tải trang xong
        window.addEventListener('DOMContentLoaded', () => {
            loadProducts();
            initShopFilters();
            
            // Close modal when clicking outside container
            window.onclick = (event) => {
                const modal = document.getElementById('productConfigModal');
                if (event.target == modal) {
                    closeConfigModal();
                }
            };
        });
    </script>
</body>
</html>

</body>

</html>