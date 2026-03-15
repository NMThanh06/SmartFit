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
                    <span class="config-modal__label">Màu sắc (Preview)</span>
                    <div class="config-modal__colors">
                        <span class="config-color-btn active" style="background: #000;" onclick="selectColor(this, 'Đen')"></span>
                        <span class="config-color-btn" style="background: #fff;" onclick="selectColor(this, 'Trắng')"></span>
                        <span class="config-color-btn" style="background: #808080;" onclick="selectColor(this, 'Xám')"></span>
                        <span class="config-color-btn" style="background: #000080;" onclick="selectColor(this, 'Xanh')"></span>
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

            // Load sizes
            const sizeContainer = document.getElementById('modalSizeOptions');
            sizeContainer.innerHTML = '';
            
            // Lấy kích cỡ từ database. Nếu không có, gán mặc định thông minh theo loại
            let sizeArray = [];
            let raw = item.sizes;
            if (typeof raw === 'string') raw = raw.split(',').filter(x => x.trim());
            
            if (raw && Array.isArray(raw) && raw.length > 0) {
                sizeArray = raw;
            } else if (item.type === 'shoes') {
                sizeArray = ['39', '40', '41'];
            } else if (item.name.toLowerCase().includes('kính') || item.type === 'accessories' || item.type === 'accessory') {
                sizeArray = ['Oversize'];
            } else {
                sizeArray = ['S', 'M', 'L', 'XL'];
            }
            
            console.log("Loading sizes for:", item.name, "Type:", item.type, "Sizes:", sizeArray);
            
            sizeArray.forEach((s, index) => {
                const sClean = s.trim();
                const btn = document.createElement('button');
                btn.className = 'config-size-btn';
                
                // Giả lập: Hết hàng cho 1 vài size ngẫu nhiên (ví dụ size XL hoặc ngẫu nhiên)
                const isOutOfStock = (sClean === 'XL' || (Math.random() < 0.2)); 
                
                if (isOutOfStock) {
                    btn.classList.add('out-of-stock');
                    btn.title = "Hết hàng";
                } else {
                    btn.onclick = () => {
                        document.querySelectorAll('.config-size-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        selectedConfigSize = sClean;
                    };
                }
                
                btn.textContent = sClean;
                sizeContainer.appendChild(btn);
            });

            // Reset color buttons and add mock out-of-stock
            const colorBtns = document.querySelectorAll('.config-color-btn');
            colorBtns.forEach((btn, index) => {
                btn.classList.remove('active', 'out-of-stock');
                
                // Giả lập: Hết hàng cho màu cuối cùng hoặc ngẫu nhiên
                const isColorOut = (index === 3 || (Math.random() < 0.1));
                if (isColorOut) {
                    btn.classList.add('out-of-stock');
                    btn.title = "Màu này đã hết hàng";
                }
            });

            modal.classList.add('active');
            
            // Confirm button action
            document.getElementById('btnConfirmAdd').onclick = () => confirmAddToCart();
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
            // Xác định danh sách size khả dụng tương tự như lúc mở Modal
            let sizeArray = [];
            let raw = currentSelectedItem.sizes;
            if (typeof raw === 'string') raw = raw.split(',').filter(x => x.trim());

            if (raw && Array.isArray(raw) && raw.length > 0) {
                sizeArray = raw;
            } else if (currentSelectedItem.type === 'shoes') {
                sizeArray = ['39', '40', '41'];
            } else if (currentSelectedItem.name.toLowerCase().includes('kính') || currentSelectedItem.type === 'accessories' || currentSelectedItem.type === 'accessory') {
                sizeArray = ['Oversize'];
            } else {
                sizeArray = ['S', 'M', 'L', 'XL'];
            }

            // Thu thập danh sách size/màu hết hàng từ giao diện Modal hiện tại
            const outOfStockSizes = [];
            document.querySelectorAll('.config-size-btn.out-of-stock').forEach(btn => outOfStockSizes.push(btn.textContent.trim()));
            
            const outOfStockColors = [];
            const colorNames = ['Đen', 'Trắng', 'Xám', 'Xanh'];
            document.querySelectorAll('.config-color-btn.out-of-stock').forEach(btn => {
                // Lấy index để suy ra tên màu (giả định theo thứ tự cố định)
                const btns = Array.from(document.querySelectorAll('.config-color-btn'));
                const idx = btns.indexOf(btn);
                if (idx !== -1) outOfStockColors.push(colorNames[idx]);
            });

            const existingIndex = cart.findIndex(item => item.id === id && item.size === size && item.color === color);

            if (existingIndex !== -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    image: imageSrc,
                    price: price,
                    size: size,
                    color: color,
                    availableSizes: sizeArray,
                    outOfStockSizes: outOfStockSizes,
                    outOfStockColors: outOfStockColors,
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