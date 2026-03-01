<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng - SmartFit</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f9; padding: 20px; color: #333; }
        h1 { text-align: center; margin-bottom: 30px; }

        /* Cấu trúc Lưới (Grid) cho sản phẩm */
        .product-grid {
            display: grid;
            /* Tự động chia cột, mỗi cột tối thiểu 220px, tối đa 1 phần đều nhau */
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Thẻ hiển thị từng sản phẩm (Card) */
        .product-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover; /* Giữ tỷ lệ ảnh không bị méo */
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #eaeaea;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            white-space: nowrap; /* Cắt chữ nếu tên quá dài */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            font-size: 1.2rem;
            color: #e74c3c;
            font-weight: bold;
        }
        .btn-back {
                display: inline-block;
                margin-bottom: 15px;
                color: #495057;
                text-decoration: none;
                font-weight: bold;
                font-size: 0.95rem;
                padding: 8px 12px;
                background: #e9ecef;
                border-radius: 4px;
                transition: 0.2s;
            }
        .btn-back:hover {
            background: #ced4da;
            color: #212529;
        }

        /* Nút Giỏ hàng nổi ở góc trên bên phải */
        .cart-icon-container {
            position: fixed;
            top: 20px;
            right: 30px;
            background-color: #fff;
            padding: 15px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.2s;
        }
        .cart-icon-container:hover { transform: scale(1.1); }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        /* CSS cho bức ảnh lúc đang bay */
        .flying-img {
            position: fixed;
            z-index: 9999;
            object-fit: cover;
            border-radius: 8px;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Hiệu ứng bay mượt */
            opacity: 1;
        }
    
    </style>
</head>
<body>
    <a href="/SmartFit/index.php" class="btn-back">⬅ Quay lại Trang chủ</a>
    <div class="cart-icon-container" id="cart-icon" onclick="window.location.href='cart.html'">
        🛒
        <span class="cart-badge" id="cart-count">0</span>
    </div>
    <h1>Bộ Sưu Tập SmartFit</h1>

    <div id="productGrid" class="product-grid">
        </div>

    <script>
        // Hàm định dạng số tiền VND
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
        }

        // Hàm lấy dữ liệu từ file JSON và hiển thị
        async function loadProducts() {
            try {
                // Gọi file JSON
                const response = await fetch('../includes/api_outfits.php');
                const data = await response.json();
                
                const grid = document.getElementById('productGrid');
                grid.innerHTML = ''; // Xóa chữ "Đang tải dữ liệu..."

                // Duyệt qua từng sản phẩm trong mảng items
                data.items.forEach(item => {
                    // Tạo thẻ div cho sản phẩm
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    
                    // Sự kiện click chuyển sang trang Chi tiết
                    card.onclick = () => {
                        window.location.href = `detail.html?id=${item.id}`;
                    };

                    // Đổ dữ liệu HTML vào thẻ
                    card.innerHTML = `
                        <img src="${item.image}" alt="${item.name}" class="product-image" onerror="this.onerror=null; this.src='/SmartFit/assets/img/default-placeholder.jpg'">
                        <div class="product-name" title="${item.name}">${item.name}</div>
                        <div class="product-price">${formatPrice(item.price)}</div>
                        <button class="btn-add-cart" onclick="addToCart(event, ${item.id}, '${item.name}', ${item.price}, '${item.image}')">🛒 Thêm vào giỏ</button>
                    `;

                    // Thêm thẻ vào lưới
                    grid.appendChild(card);
                });
            } catch (error) {
                console.error("Lỗi khi tải dữ liệu:", error);
                document.getElementById('productGrid').innerHTML = '<p style="color:red; text-align:center;">Không thể tải dữ liệu sản phẩm. Vui lòng kiểm tra lại file JSON.</p>';
            }
        }

       function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];
            let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cart-count').innerText = totalItems;
        }

        // Chạy hàm đếm ngay khi vừa load trang
        updateCartCount();

        // Hàm xử lý Thêm vào giỏ & Hiệu ứng bay
        function addToCart(event, id, name, price, image) {
            event.stopPropagation(); // Ngăn click nhầm vào thẻ

            // 1. LƯU VÀO LOCALSTORAGE
            let cart = JSON.parse(localStorage.getItem('smartfit_cart')) || [];
            let existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id: id, name: name, price: price, image: image, quantity: 1 });
            }
            localStorage.setItem('smartfit_cart', JSON.stringify(cart));
            
            // Cập nhật lại con số giỏ hàng
            updateCartCount();

            // 2. HIỆU ỨNG BAY (FLY TO CART)
            const button = event.target;
            const card = button.closest('.product-card');
            const imgToFly = card.querySelector('.product-image'); // Tìm tấm ảnh trong thẻ vừa bấm
            const cartIcon = document.getElementById('cart-icon');

            if (imgToFly && cartIcon) {
                // Tạo một bản sao của tấm ảnh
                const flyingImg = imgToFly.cloneNode();
                flyingImg.classList.add('flying-img');

                // Lấy tọa độ của ảnh gốc và giỏ hàng
                const imgRect = imgToFly.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();

                // Đặt vị trí bắt đầu cho ảnh bản sao (nằm đè lên ảnh gốc)
                flyingImg.style.left = imgRect.left + 'px';
                flyingImg.style.top = imgRect.top + 'px';
                flyingImg.style.width = imgRect.width + 'px';
                flyingImg.style.height = imgRect.height + 'px';

                document.body.appendChild(flyingImg);

                // Kích hoạt hiệu ứng bay sau 10 mili-giây (để CSS kịp nhận diện vị trí ban đầu)
                setTimeout(() => {
                    flyingImg.style.left = (cartRect.left + 10) + 'px'; // Bay đến giỏ hàng
                    flyingImg.style.top = (cartRect.top + 10) + 'px';
                    flyingImg.style.width = '20px'; // Thu nhỏ lại
                    flyingImg.style.height = '20px';
                    flyingImg.style.opacity = '0.2'; // Mờ dần
                    flyingImg.style.transform = 'scale(0.1) rotate(360deg)'; // Xoay vòng vòng
                }, 10);

                // Dọn dẹp rác (xóa ảnh bản sao) sau khi bay xong (0.8s)
                setTimeout(() => {
                    flyingImg.remove();
                    
                    // Hiệu ứng giật giật cho giỏ hàng khi nhận được đồ
                    cartIcon.style.transform = 'scale(1.3)';
                    setTimeout(() => cartIcon.style.transform = 'scale(1)', 200);
                }, 800);
            }
        }
     
        window.onload = loadProducts;
    </script>

</body>
</html>