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

            <!-- Nút Chọn vị trí + Dropdown dự báo -->
            <div class="hero__location-row">
                <button id="btnChooseLocation" class="btn-choose-location">
                    <i class="fa-solid fa-location-dot"></i> Chọn vị trí
                </button>

                <select id="forecastDropdown" class="forecast-dropdown">
                    <option value="">-- Dự báo 7 ngày --</option>
                </select>
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

        <!-- ====== Modal Bản đồ ====== -->
        <div id="mapModal" class="map-modal">
            <div class="map-modal__overlay"></div>
            <div class="map-modal__content">
                <div class="map-modal__header">
                    <h3 class="map-modal__title"><i class="fa-solid fa-map-location-dot"></i> Chọn vị trí trên bản đồ</h3>
                    <button class="map-modal__close" id="btnCloseMap">&times;</button>
                </div>
                <div id="map" class="map-modal__map"></div>
                <div class="map-modal__footer">
                    <span class="map-modal__coords" id="mapCoordsDisplay">Chưa chọn vị trí</span>
                    <button id="btnConfirmLocation" class="map-modal__confirm">
                        <i class="fa-solid fa-check"></i> Xác nhận
                    </button>
                </div>
            </div>
        </div>

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

<!-- ====== CSS + JS cho Bản đồ ====== -->
<style>
    /* --- Nút Chọn vị trí --- */
    .btn-choose-location {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        margin: 12px 0;
        border: none;
        border-radius: 30px;
        background: var(--primary-blue);
        color: #fff;
        font-size: 1.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(33, 118, 255, 0.2);
    }
    .btn-choose-location:hover {
        background: var(--primary-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 118, 255, 0.35);
    }
    .btn-choose-location:active {
        transform: scale(0.97);
    }

    /* --- Hàng chứa nút + dropdown --- */
    .hero__location-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* --- Dropdown dự báo 7 ngày --- */
    .forecast-dropdown {
        padding: 10px 18px;
        border: 2px solid rgba(102, 126, 234, 0.5);
        border-radius: 30px;
        background: rgba(26, 26, 46, 0.85);
        color: #fff;
        font-size: 1.4rem;
        font-weight: 500;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        min-width: 280px;
    }
    .forecast-dropdown:hover,
    .forecast-dropdown:focus {
        border-color: #667eea;
        box-shadow: 0 0 12px rgba(102, 126, 234, 0.35);
    }
    .forecast-dropdown option {
        background: #1a1a2e;
        color: #e2e8f0;
        padding: 8px;
    }

    /* --- Modal --- */
    .map-modal {
        display: none;                /* ẩn mặc định */
        position: fixed;
        inset: 0;
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }
    .map-modal.active {
        display: flex;
    }

    .map-modal__overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(8px);
    }

    .map-modal__content {
        position: relative;
        width: 90%;
        max-width: 850px;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        animation: mapModalIn 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    @keyframes mapModalIn {
        from { opacity: 0; transform: scale(0.95) translateY(20px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .map-modal__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        background: var(--primary-blue);
    }
    .map-modal__title {
        color: #fff;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
    }
    .map-modal__close {
        background: rgba(255,255,255,0.15);
        border: none;
        color: #fff;
        font-size: 2.4rem;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        line-height: 1;
    }
    .map-modal__close:hover { 
        background: rgba(255,255,255,0.3);
        transform: rotate(90deg);
    }

    /* Bản đồ */
    .map-modal__map {
        width: 100%;
        height: 450px;
        background: #f8f9fa;
    }

    /* Footer modal */
    .map-modal__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 25px;
        background: #ffffff;
        border-top: 1px solid #f0f0f0;
    }
    .map-modal__coords {
        color: var(--apple-grey);
        font-size: 1.4rem;
        font-weight: 500;
    }
    .map-modal__confirm {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border: none;
        border-radius: 12px;
        background: var(--primary-blue);
        color: #fff;
        font-size: 1.5rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(33, 118, 255, 0.2);
    }
    .map-modal__confirm:hover {
        background: var(--primary-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(33, 118, 255, 0.35);
    }
    .map-modal__confirm:disabled {
        background: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
</style>

<script>
(function () {
    // ===== Biến lưu tọa độ tạm =====
    let selectedLat = null;
    let selectedLng = null;

    // ===== Tham chiếu DOM =====
    const btnOpen    = document.getElementById('btnChooseLocation');
    const btnConfirm = document.getElementById('btnConfirmLocation');
    const btnClose   = document.getElementById('btnCloseMap');
    const modal      = document.getElementById('mapModal');
    const overlay    = modal.querySelector('.map-modal__overlay');
    const coordsText = document.getElementById('mapCoordsDisplay');

    let map    = null;   // instance Leaflet
    let marker = null;   // marker hiện tại

    // ===== Mở Modal & khởi tạo bản đồ =====
    btnOpen.addEventListener('click', function () {
        modal.classList.add('active');
        btnConfirm.disabled = true;

        // Chỉ khởi tạo bản đồ 1 lần
        if (!map) {
            map = L.map('map').setView([10.7769, 106.7009], 13); // TP.HCM
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Sự kiện click lên bản đồ
            map.on('click', function (e) {
                selectedLat = e.latlng.lat;
                selectedLng = e.latlng.lng;

                // Xóa marker cũ nếu có
                if (marker) {
                    map.removeLayer(marker);
                }

                // Tạo marker mới
                marker = L.marker([selectedLat, selectedLng]).addTo(map);

                // Cập nhật text tọa độ
                coordsText.textContent = selectedLat.toFixed(5) + ', ' + selectedLng.toFixed(5);
                coordsText.style.color = '#4fd1c5';

                // Bật nút Xác nhận
                btnConfirm.disabled = false;
            });
        }

        // Fix bản đồ bị render lỗi khi modal vừa mở
        setTimeout(function () {
            map.invalidateSize();
        }, 300);
    });

    // ===== Đóng Modal =====
    function closeModal() {
        modal.classList.remove('active');
    }
    btnClose.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    // ===== Xác nhận vị trí =====
    btnConfirm.addEventListener('click', function () {
        if (selectedLat !== null && selectedLng !== null) {
            console.log('Tọa độ đã chọn: ', selectedLat, selectedLng);
            // Gọi hàm cập nhật thời tiết với tọa độ mới từ bản đồ
            window.app.updateCurrentWeather(selectedLat, selectedLng);
            closeModal();
        }
    });
})();
</script>

<?php include 'includes/footer.php'; ?>
</body>

</html>
</body>

</html>