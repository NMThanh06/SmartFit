<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartFit</title>

    <!-- Font  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <!-- My Library -->
    <link rel="stylesheet" href="./assets/css/grid.css">
    <link rel="stylesheet" href="./assets/css/base.css">
    <link rel="stylesheet" href="./assets/css/style.css?=v1">
    <link rel="stylesheet" href="./assets/css/responsive.css">

    <!-- Javascript -->
    <script src="script.js?v=1<?php echo time(); ?>" defer></script>
</head>

<body>
    <div class="web__background--overlay"></div>

    <main class="web__container">
        <!-- Navigation -->
        <nav class="navbar">
            <a href="./index.php" class="navbar__logo">SmartFit</a>

            <a href="./index.php" class="navbar__logo">Bộ sưu tập</a>
        </nav>

        <!--  -->
        <div class="col l-3 m-4 c-12">
            <div class="wardrobe-card">
                <div class="wardrobe-card__images">
                    <img src="https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=200&auto=format&fit=crop" alt="Áo">
                    <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=200&auto=format&fit=crop" alt="Quần">
                </div>

                <div class="wardrobe-card__info">
                    <div class="wardrobe-card__header">
                        <h3 class="wardrobe-card__title">Streetwear</h3>
                        <button class="wardrobe-card__delete" title="Xóa"><i class="fa-solid fa-trash-can"></i></button>
                    </div>

                    <ul class="wardrobe-card__items">
                        <li><span>👕</span>
                            <p>Áo thun đen form rộng</p>
                        </li>
                        <li><span>👖</span>
                            <p>Quần Jeans xanh rách gối</p>
                        </li>
                        <li><span>👟</span>
                            <p>Sneaker Nike Air Force 1</p>
                        </li>
                        <li><span>🧢</span>
                            <p>Mũ lưỡi trai đen</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer__author">Made with ❤️ by Cuong & Thanh.</div>

            <div class="footer__contact">
                <a href="https://github.com/NMThanh06/SmartFit" class="footer__contact__github">
                    <i class="fa-brands fa-square-github"></i>
                </a>

                <div class="footer__contact__team">
                    <div class="footer__contact__mail" onclick="app.copyToClipboard(this)">
                        <i class="fa-solid fa-envelope"></i>
                        <span>trungcuong.2006tn@gmail.com</span>
                        <div class="copy-tooltip">Copied!</div>
                    </div>

                    <div class="footer__contact__mail" onclick="app.copyToClipboard(this)">
                        <i class="fa-solid fa-envelope"></i>
                        <span>nguyenminhthanh043216@gmail.com</span>
                        <div class="copy-tooltip">Copied!</div>
                    </div>
                </div>
            </div>
        </footer>
    </main>
</body>

</html>