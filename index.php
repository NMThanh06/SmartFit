<?php
include 'includes/header.php';
?>

        <!-- Hero Section -->
        <section class="hero hero--index" id="hero">
            <div class="hero__info">
                <div class="info__greeting"></div> <!--Câu chào-->
                <div class="info__weather">
                    <div class="info__weather__icon"></div> <!-- Icon thời tiết-->

                    <div class="info__weather__text"></div> <!-- Thời tiết hiện tại-->
                    <div class="info__weather__temp"></div> <!-- Nhiệt độ -->
                </div>
                <div class="info__desc">HCM đang khá lạnh đấy, nhớ mặc ấm nhé.</div>
            </div>

            <form id="configForm" class="config-form" action="">

                <div class="config-form__group">
                    <h3 class="config-form__heading">Bạn mặc cho dịp gì ?</h3>

                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="study" name="occasion" value="study">
                        <label class="config-form__label" for="study">Đi học</label>

                        <input class="config-form__input" type="radio" id="goout" name="occasion" value="goout">
                        <label class="config-form__label" for="goout">Đi chơi</label>

                        <input class="config-form__input" type="radio" id="date" name="occasion" value="date">
                        <label class="config-form__label" for="date">Hẹn hò</label>
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Bạn là ?</h3>

                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="male" name="gender" value="male">
                        <label class="config-form__label" for="male">Nam</label>

                        <input class="config-form__input" type="radio" id="female" name="gender" value="female">
                        <label class="config-form__label" for="female">Nữ</label>
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Độ tuổi của bạn ?</h3>
                    <div class="config-form__options">
                        <input class="config-form__input--age" type="number" id="age" name="age" min="1" max="100" placeholder="Số tuổi">
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Phong cách bạn hướng tới ?</h3>

                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="basic" name="style" value="basic">
                        <label class="config-form__label" for="basic">Basic</label>

                        <input class="config-form__input" type="radio" id="street" name="style" value="street">
                        <label class="config-form__label" for="street">Streetwear</label>

                        <input class="config-form__input" type="radio" id="vintage" name="style" value="vintage">
                        <label class="config-form__label" for="vintage">Vintage</label>
                    </div>
                </div>


                <div class="config-form__group">
                    <h3 class="config-form__heading">Tông màu chủ đạo ?</h3>
                    <div class="config-form__options">

                        <input class="config-form__input" type="radio" id="color-dark" name="color" value="dark">
                        <label class="config-form__label config-form__label--color" for="color-dark"
                            style="background-color: #000000;"></label>

                        <input class="config-form__input" type="radio" id="color-light" name="color" value="light">
                        <label class="config-form__label config-form__label--color" for="color-light"
                            style="background-color: #f0f0f0;"></label>

                        <input class="config-form__input" type="radio" id="color-pop" name="color" value="colorful">
                        <label class="config-form__label config-form__label--color" for="color-pop"
                            style="background: linear-gradient(#C21807, #DAA520);"></label>

                        <input class="config-form__input" type="radio" id="color-pastel" name="color" value="pastel">
                        <label class="config-form__label config-form__label--color" for="color-pastel"
                            style="background: linear-gradient(#2C3E50, #3E5E5E);"></label>

                        <input class="config-form__input" type="radio" id="color-neutral" name="color" value="neutral">
                        <label class="config-form__label config-form__label--color" for="color-neutral"
                            style="background: linear-gradient(#fff, #000);"></label>

                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Độ rộng (Fit) ?</h3>
                    <div class="config-form__options">
                        <input class="config-form__input" type="radio" id="fit-oversize" name="fit" value="oversized">
                        <label class="config-form__label" for="fit-oversize">Oversized</label>

                        <input class="config-form__input" type="radio" id="fit-regular" name="fit" value="regular">
                        <label class="config-form__label" for="fit-regular">Vừa vặn</label>

                        <input class="config-form__input" type="radio" id="fit-slim" name="fit" value="slim">
                        <label class="config-form__label" for="fit-slim">Ôm sát</label>
                    </div>
                </div>

                <div class="config-form__group">
                    <h3 class="config-form__heading">Ghi chú cho AI (Tùy chọn)</h3>
                    <textarea class="config-form__textarea" name="note"
                        placeholder="VD: Tôi có đôi Jordan đỏ, tôi không thích mặc váy..."></textarea>
                </div>
            </form>

            <button id="PDN" type="submit" class="confirm__button button" form="configForm">
                Phối đồ ngay ⭐
            </button>
        </section>

        <!-- Result Section -->
        <section class="result" id="result">

            <!-- Loading -->
            <div id="loadingProgress" style="display: none;">
                <div class="result-loading__box">
                    <div class="result-loading__spinner"></div>
                    <p class="result-loading__text">AI đang suy nghĩ set đồ cực chất cho bạn... Vui lòng đợi vài giây nhé! ⏳</p>
                </div>
            </div>

            <div class="result__container">

                <div class="result__visual">
                    <div class="visual-item">
                        <img src="./assets/img/top.jpeg" alt="Áo" id="imgTop">
                    </div>

                    <div class="visual-item">
                        <img src="./assets/img/bottom.jpeg" alt="Quần" id="imgBottom">
                    </div>
                </div>

                <div class="result__content">
                    <div class="result__header">
                        <span class="result__tag">AI Recommendation</span>
                        <h2 class="result__title" id="outfitStyle">Streetwear Năng Động</h2>
                    </div>

                    <p class="result__desc" id="outfitDesc">
                        "Dựa trên thời tiết <b>24°C</b> và dịp <b>Đi chơi</b>, mình chọn cho bạn một set đồ thoải mái,
                        vừa đủ ấm nhưng vẫn cực kỳ cool ngầu."
                    </p>

                    <div class="result__items">
                        <div class="item-box">
                            <i class="fa-brands fa-redhat item-icon"></i>
                            <span id="itemHead">Mũ lưỡi trai đen</span>
                        </div>

                        <div class="item-box">
                            <i class="fa-solid fa-shirt item-icon"></i>
                            <span id="itemTopName">Hoodie Oversized xám</span>
                        </div>

                        <div class="item-box">
                            <i class="fa-solid fa-vials item-icon"></i>
                            <span id="itemBottomName">Quần Cargo túi hộp</span>
                        </div>

                        <div class="item-box">
                            <i class="fa-solid fa-shoe-prints item-icon"></i>
                            <span id="itemShoes">Sneaker Jordan 1 High</span>
                        </div>
                    </div>

                    <div class="result__actions">
                        <button class="button actions__button" onclick="app.resetForm()">
                            <i class="fa-solid fa-rotate-right"></i> Thử lại
                        </button>

                        <button class="button actions__button" 
                                onclick="app.toggleSaveOutfit(this)"
                                data-top=""
                                data-bottom=""
                                data-shoes=""
                                data-acc=""
                                data-style="">
                            <i class="fa-regular fa-bookmark"></i> <span>Lưu set đồ</span>
                        </button>
                    </div>

                </div>
            </div>
        </section>

<?php include 'includes/footer.php'; ?>
</body>

</html>
</body>

</html>