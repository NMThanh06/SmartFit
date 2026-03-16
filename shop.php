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
                            <ul class="shop-filter__list">
                                <li class="shop-filter__item active" data-type="all">Tất cả sản phẩm</li>
                                <li class="shop-filter__item" data-type="top">Áo</li>
                                <li class="shop-filter__item" data-type="bottom">Quần</li>
                                <li class="shop-filter__item" data-type="accessory">Giày & Phụ kiện</li>
                            </ul>
                        </div>

                        <div class="shop-filter">
                            <h3 class="shop-filter__title">Kích cỡ</h3>
                            <div class="shop-filter__sizes">
                                <span class="shop-filter__size">S</span>
                                <span class="shop-filter__size">M</span>
                                <span class="shop-filter__size">L</span>
                                <span class="shop-filter__size">XL</span>
                                <span class="shop-filter__size">Oversize</span>
                            </div>
                        </div>

                        <div class="shop-filter">
                            <h3 class="shop-filter__title">Khoảng giá</h3>
                            <div class="shop-filter__price">
                                <label class="shop-filter__checkbox">
                                    <input type="checkbox"> 0đ - 200.000đ
                                </label>
                                <label class="shop-filter__checkbox">
                                    <input type="checkbox"> 200.000đ - 500.000đ
                                </label>
                                <label class="shop-filter__checkbox">
                                    <input type="checkbox"> Trên 500.000đ
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
                                <select class="shop-sort__select">
                                    <option value="newest">Mới nhất</option>
                                    <option value="price-asc">Giá tăng dần</option>
                                    <option value="price-desc">Giá giảm dần</option>
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

                <button id="btnConfirmAdd" class="config-modal__btn-confirm">Xác nhận thêm vào giỏ</button>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>

    <!-- Backend cho trang cửa hàng -->
    <script>
        // ========================================
        // SHOP-SPECIFIC FUNCTIONS
        // ========================================


        // 2. Tải danh sách sản phẩm trang Shop
        async function loadProducts() {
            try {
                const response = await fetch('includes/api_outfits.php');
                const data = await response.json();
                const grid = document.getElementById('productGrid');
                if (!grid) return;
                grid.innerHTML = '';

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
                                
                                <div class="product-card__buy" onclick='openConfigModal(${JSON.stringify(item).replace(/'/g, "\\'")})'>
                                    <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } catch (error) { console.error("Lỗi tải sản phẩm:", error); }
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

        // Hàm render size dựa trên màu sắc được chọn
        function renderSizesForColor(colorId) {
            const sizeContainer = document.getElementById('modalSizeOptions');
            sizeContainer.innerHTML = '';
            selectedConfigSize = null;

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
                    
                    if (!sizeData || parseInt(sizeData.quantity) <= 0) {
                        btn.classList.add('out-of-stock');
                        btn.title = "Hết hàng";
                    } else {
                        btn.onclick = () => {
                            document.querySelectorAll('.config-size-btn').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                            selectedConfigSize = sizeName;
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
            
            // Gọi logic thêm vào giỏ hàng thực sự
            performAddToCart(currentSelectedItem.id, currentSelectedItem.name, currentSelectedItem.image, currentSelectedItem.price, selectedConfigSize, selectedConfigColor);
            
            closeConfigModal();
            
            // Animation hiệu ứng bay từ modal
            const modalImg = document.getElementById('modalProductImg');
            animateFly(modalImg);
        }

        function performAddToCart(id, name, imageSrc, price, size, color) {
            // Lấy tồn kho của biến thể này
            const allSizes = currentSelectedItem.sizes || [];
            const colorObj = (currentSelectedItem.colors || []).find(c => c.color_name === color);
            const colorId = colorObj ? colorObj.id : null;
            const sizeData = allSizes.find(s => s.size_name === size && s.color_id == colorId);
            const stock = sizeData ? parseInt(sizeData.quantity) : 0;

            const existingIndex = cart.findIndex(item => item.id === id && item.size === size && item.color === color);
            
            if (existingIndex !== -1) {
                const nextQty = cart[existingIndex].quantity + 1;
                if (nextQty > stock) {
                    showToast(`Sản phẩm ${name} (Size ${size}, ${color}) đã đạt giới hạn tồn kho trong giỏ hàng!`, 'error');
                    return;
                }
                cart[existingIndex].quantity = nextQty;
            } else {
                if (stock < 1) {
                    showToast('Sản phẩm này hiện đã hết hàng!', 'error');
                    return;
                }
                cart.push({
                    id: id,
                    name: name,
                    image: imageSrc,
                    price: price,
                    size: size,
                    color: color,
                    allColors: currentSelectedItem.colors, 
                    allSizes: currentSelectedItem.sizes,   
                    quantity: 1
                });
            }
            saveCart();
            if (window.showToast) showToast(`Đã thêm ${name} vào giỏ hàng!`, 'success');
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