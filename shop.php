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

        <?php include 'includes/footer.php'; ?>

    <!-- Backend cho trang cửa hàng -->
    <script>
        // ========================================
        // SHOP-SPECIFIC FUNCTIONS
        // ========================================

        // 1. Hàm tự động chọn bộ Size dựa vào Loại sản phẩm (Type)
        function getSizeOptions(item) {
            const type = (item.type || '').toLowerCase();
            const name = (item.name || '').toLowerCase();

            // 1. Ưu tiên lấy Size từ Database nếu đã nhập
            if (item.sizes) {
                let sList = typeof item.sizes === 'string' 
                    ? item.sizes.replace(/[\[\]"]/g, '').split(',') 
                    : item.sizes;
                return sList.map(s => `<option value="${s.trim()}">${s.trim()}</option>`).join('');
            }

            // 2. Phân loại thông minh hơn (Nếu DB chưa có size)
            if (type === 'shoes' || name.includes('giày')) {
                return `<option value="39">Size 39</option><option value="40" selected>Size 40</option><option value="41">Size 41</option>`;
            } else if (['accessory', 'glasses', 'hat'].includes(type) || name.includes('kính') || name.includes('mũ')) {
                return `<option value="Oversize" selected>Oversize</option><option value="Freesize">Freesize</option>`;
            } else {
                return `<option value="S">Size S</option><option value="M" selected>Size M</option><option value="L">Size L</option>`;
            }
        }

        // 2. Tải danh sách sản phẩm trang Shop
        async function loadProducts() {
            try {
                const response = await fetch('includes/api_outfits.php');
                const data = await response.json();
                const grid = document.getElementById('productGrid');
                if (!grid) return;
                grid.innerHTML = '';

                data.items.forEach(item => {
                    const dynamicSizes = getSizeOptions(item);

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
                                
                                <div class="product-card__size" style="margin-bottom: 10px;">
                                    <select id="size-select-${item.id}" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; outline: none; cursor: pointer;">
                                        ${dynamicSizes}
                                    </select>
                                </div>

                                <div class="product-card__buy" onclick="addToCart(event, ${item.id}, '${item.name}', '${item.image}', ${item.price})">
                                    <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
            } catch (error) { console.error("Lỗi tải sản phẩm:", error); }
        }

        // 3. Thêm vào giỏ hàng (Sử dụng hàm từ shared footer)
        function addToCart(event, id, name, imageSrc, price) {
            const btn = event.currentTarget;

            // 1. Lấy size đã chọn từ Dropdown
            const sizeSelect = document.getElementById(`size-select-${id}`);
            const selectedSize = sizeSelect ? sizeSelect.value : 'M'; 

            // 2. HIỆU ỨNG BAY
            const cartIcon = document.querySelector('.navbar__cart');
            const productCard = btn.closest('.product-card');
            const imgToFly = productCard ? productCard.querySelector('img') : null;
            
            if (imgToFly && cartIcon) {
                const flyImg = imgToFly.cloneNode();
                const imgRect = imgToFly.getBoundingClientRect();
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

            // 3. Push vào mảng cart toàn cục (Đã khai báo trong footer.php)
            const existingIndex = cart.findIndex(item => item.id === id && item.size === selectedSize);

            if (existingIndex !== -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    image: imageSrc,
                    price: price,
                    size: selectedSize,
                    quantity: 1
                });
            }

            // 4. Lưu + render lại (Hàm từ footer.php)
            saveCart();
        }

        // Khởi động khi tải trang xong
        window.addEventListener('DOMContentLoaded', () => {
            loadProducts();
        });
    </script>
</body>
</html>

</body>

</html>