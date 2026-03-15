window.app = {
    // --- 1. CẤU HÌNH (CONFIG) ---
    config: {
        apiKey: '2cb97f62395b42556d493874d4486859', // Key của bạn
        apiUrl: 'https://api.openweathermap.org/data/2.5/weather',

        // Bảng dịch mã thời tiết WMO (Open-Meteo) sang tiếng Việt
        weatherCodeMap: {
            0: 'Trời quang',
            1: 'Chủ yếu quang', 2: 'Mây rải rác', 3: 'U ám',
            45: 'Sương mù', 48: 'Sương mù đóng băng',
            51: 'Mưa phùn nhẹ', 53: 'Mưa phùn vừa', 55: 'Mưa phùn dày',
            56: 'Mưa phùn đóng băng nhẹ', 57: 'Mưa phùn đóng băng dày',
            61: 'Mưa nhẹ', 63: 'Mưa vừa', 65: 'Mưa to',
            66: 'Mưa đóng băng nhẹ', 67: 'Mưa đóng băng to',
            71: 'Tuyết rơi nhẹ', 73: 'Tuyết rơi vừa', 75: 'Tuyết rơi dày',
            77: 'Hạt tuyết',
            80: 'Mưa rào nhẹ', 81: 'Mưa rào vừa', 82: 'Mưa rào to',
            85: 'Mưa tuyết nhẹ', 86: 'Mưa tuyết to',
            95: 'Giông', 96: 'Giông kèm mưa đá nhẹ', 99: 'Giông kèm mưa đá to'
        }
    },

    // --- 2. CÁC HÀM XỬ LÝ ---
    start: function () {
        console.log("🚀 Ứng dụng bắt đầu chạy...");

        this.initAuthEvents();
        this.initUserMenu();
        this.startClock();
        this.initFormEvent();
        this.initForecastDropdown();
        this.initScrollBtn();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                this.getWeatherByPosition.bind(this),
                this.handleLocationError.bind(this)
            );
        } else {
            alert("Trình duyệt không hỗ trợ định vị.");
            this.handleLocationError({ message: "Not supported" });
        }
    },

    startClock: function () {
        const greetingElement = document.querySelector('.info__greeting');

        const updateTime = () => {
            if (!greetingElement) return;

            const now = new Date();
            const hour = now.getHours();
            const minutes = now.getMinutes();
            const minuteString = minutes < 10 ? `0${minutes}` : minutes;

            let session = "";
            if (hour >= 5 && hour < 12) session = "day";
            else if (hour >= 12 && hour < 17) session = "afternoon";
            else session = "night";

            let greetingMsg = `<span style="margin-right: 15px; font-weight: bold;">${hour}:${minuteString} -</span>`;

            if (session === "day") greetingMsg += "Chào buổi sáng 🌅, hôm nay bạn thấy thế nào ?";
            else if (session === "afternoon") greetingMsg += "Chào buổi trưa ☀️, hôm nay bạn thấy thế nào ?";
            else greetingMsg += "Chào buổi tối 🌙, hôm nay bạn thấy thế nào ?";

            greetingElement.innerHTML = greetingMsg;
        };

        updateTime();

        setInterval(updateTime, 1000);
    },

    getWeatherByPosition: function (position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        console.log(`📍 Tìm thấy tọa độ từ Geolocation: ${lat}, ${lon}`);
        this.updateCurrentWeather(lat, lon);
    },

    // Hàm mới: Gọi API thời tiết bằng tọa độ (lat, lng)
    updateCurrentWeather: function (lat, lng) {
        console.log(`🌍 Đang lấy thời tiết cho: ${lat}, ${lng}`);
        const url = `${this.config.apiUrl}?lat=${lat}&lon=${lng}&appid=${this.config.apiKey}&units=metric&lang=vi`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error("Không lấy được dữ liệu thời tiết");
                return response.json();
            })
            .then(data => {
                console.log("🌤 Dữ liệu thời tiết:", data);
                this.updateUI(data);
            })
            .catch(error => {
                console.error("Lỗi API:", error);
                this.handleLocationError(error);
            });

        // Đồng thời lấy dự báo 7 ngày từ Open-Meteo
        this.fetch7DaysForecast(lat, lng);
    },

    handleLocationError: function (error) {
        console.warn("Lỗi định vị:", error.message);

        const mockData = {
            main: { temp: 25 },
            weather: [{ main: "Default", description: "không xác định", icon: "03d" }],
            name: "Trái Đất",
            dt: Date.now() / 1000,
            timezone: 0
        };

        this.updateUI(mockData);
    },

    updateUI: function (data) {
        if (!data.main) return;

        const temp = Math.round(data.main.temp);
        const condition = data.weather ? data.weather[0].main : 'Clouds';
        const locationName = data.name || "Nơi này";

        // 1. Thay đổi số Độ
        const tempEl = document.querySelector('.info__weather__temp');
        if (tempEl) tempEl.innerHTML = `${temp}<span>°C</span>`;

        // 2. Thay đổi icon thời tiết (dùng icon từ OpenWeatherMap)
        const iconEl = document.querySelector('.info__weather__icon');
        if (iconEl) {
            const iconCode = data.weather && data.weather[0].icon ? data.weather[0].icon : '03d';
            iconEl.innerHTML = `<img src="https://openweathermap.org/img/wn/${iconCode}@2x.png" alt="weather icon" style="width: 48px; height: 48px; vertical-align: middle;">`;
        }

        // 3. Thay đổi chữ thời tiết (dùng mô tả tiếng Việt từ OWM)
        const weatherTextElement = document.querySelector('.info__weather__text');
        if (weatherTextElement) {
            const description = data.weather && data.weather[0].description
                ? data.weather[0].description
                : 'không xác định';
            // Viết hoa chữ cái đầu
            const capitalDesc = description.charAt(0).toUpperCase() + description.slice(1);
            weatherTextElement.innerHTML = capitalDesc + '&nbsp;';
        }

        // 4. Thay đổi địa điểm và lời khuyên
        const descElement = document.querySelector('.info__desc');
        if (descElement) {
            let descMsg = `<b>${locationName}</b> — `;
            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') {
                descMsg += 'Trời đang mưa, nhớ mang ô nhé ☔';
            } else if (temp < 20) {
                descMsg += 'Trời khá lạnh, nhớ mặc ấm nhé 🥶';
            } else if (temp >= 30) {
                descMsg += 'Trời nắng nóng, chọn đồ mát mẻ nhé 🥵';
            } else if (condition === 'Clear') {
                descMsg += 'Trời đang đẹp, đi chơi thôi ☀️';
            } else {
                descMsg += 'Thời tiết ổn, lên đồ thôi ✨';
            }
            descElement.innerHTML = descMsg;
        }
    },

    //Auth
    initAuthEvents: function () {
        const loginBtn = document.getElementById('loginBtn');
        const authOverlay = document.getElementById('authOverlay');
        const closeBtn = document.getElementById('closeAuth');

        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const toRegister = document.getElementById('toRegister');
        const toLogin = document.getElementById('toLogin');

        if (loginBtn) {
            loginBtn.onclick = () => {
                authOverlay.style.display = 'flex';
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            };
        }

        if (closeBtn) {
            closeBtn.onclick = () => authOverlay.style.display = 'none';
        }

        if (toRegister) {
            toRegister.onclick = (e) => {
                e.preventDefault();
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            };
        }

        if (toLogin) {
            toLogin.onclick = (e) => {
                e.preventDefault();
                registerForm.style.display = 'none';
                loginForm.style.display = 'block';
            };
        }

        authOverlay.onclick = (e) => {
            if (e.target === authOverlay) authOverlay.style.display = 'none';
        };
    },

    // Xử lý gửi form Đăng nhập/Đăng ký qua AJAX
    initAuthFormSubmit: function () {
        const loginForm = document.querySelector('#loginForm form');
        const registerForm = document.querySelector('#registerForm form');
        const self = this;

        const handleAuthSubmit = async (e, formType) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const action = form.getAttribute('action');

            try {
                const response = await fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    self.showNotification(data.message, 'success');
                    document.getElementById('authOverlay').style.display = 'none';
                    form.reset();

                    if (formType === 'login') {
                        // Cập nhật Navbar sau khi đăng nhập thành công
                        self.updateNavbarAfterLogin(data.user_name);
                    }
                } else {
                    self.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Auth Error:', error);
                self.showNotification('Có lỗi xảy ra, vui lòng thử lại!', 'error');
            }
        };

        if (loginForm) loginForm.addEventListener('submit', (e) => handleAuthSubmit(e, 'login'));
        if (registerForm) registerForm.addEventListener('submit', (e) => handleAuthSubmit(e, 'register'));
    },

    // Cập nhật Navbar dynamic mà không reload trang
    updateNavbarAfterLogin: function (userName) {
        const authContainer = document.querySelector('.navbar__auth');
        if (!authContainer) return;

        // Nội dung HTML mới cho phần User (thay thế nút Đăng nhập)
        // Lưu ý: Root path ở đây là tương đối, nên dùng window.location.origin hoặc tương đương nếu phức tạp hơn
        // Tuy nhiên dựa trên header.php, chúng ta sẽ xây dựng cấu trúc tương tự.
        const userHtml = `
            <div id="userInfoToggle" class="user-info">
                <div class="user-info__trigger">
                    <span class="user-info__name"> Xin chào, <b>${userName}</b></span>
                    <i class="fa-solid fa-caret-down user-info__arrow"></i>
                </div>
                <div id="userDropdown" class="user-dropdown">
                    <a href="pages/personal_info.php" class="user-dropdown__item">
                        <i class="fa-solid fa-id-card"></i>
                        <span>Thông tin cá nhân</span>
                    </a>
                    <a href="pages/order_history.php" class="user-dropdown__item">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Lịch sử đơn hàng</span>
                    </a>
                    <a href="pages/add-outfit.php" class="user-dropdown__item">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm trang phục</span>
                    </a>
                    <div class="user-dropdown__divider"></div>
                    <a href="includes/logout.php" class="user-dropdown__item user-dropdown__item--logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
        `;

        authContainer.innerHTML = userHtml;

        // Khởi tạo lại sự kiện cho menu user mới tạo
        this.initUserMenu();
        
        // Tự động khôi phục dữ liệu lên nút "Lưu set đồ" nếu đang có kết quả
        const savedData = localStorage.getItem('smartfit_last_outfit');
        if (savedData) {
            const data = JSON.parse(savedData);
            const btnSave = document.querySelector('button[onclick="app.toggleSaveOutfit(this)"]');
            if (btnSave) {
                btnSave.setAttribute('data-top', data.topId);
                btnSave.setAttribute('data-bottom', data.bottomId);
                btnSave.setAttribute('data-shoes', data.shoesId);
                btnSave.setAttribute('data-acc', data.accId || 'null');
                btnSave.setAttribute('data-style', data.style);
            }
        }
    },

    // Submenu User
    initUserMenu: function () {
        const userInfo = document.getElementById('userInfoToggle');
        const userDropdown = document.getElementById('userDropdown');

        if (userInfo && userDropdown) {
            userInfo.onclick = (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
                userInfo.classList.toggle('active');
            };

            document.addEventListener('click', (e) => {
                if (!userInfo.contains(e.target)) {
                    userDropdown.classList.remove('show');
                    userInfo.classList.remove('active');
                }
            });
        }
    },


    resetForm: function () {
        const resultSection = document.getElementById('result');
        const configForm = document.querySelector('.config-form');

        if (resultSection) resultSection.style.display = 'none';
        if (configForm) {
            configForm.reset();
            configForm.scrollIntoView({ behavior: 'smooth' });
        }
        // Xóa kết quả lưu trữ
        localStorage.removeItem('smartfit_last_outfit');
    },

    initFormEvent: function () {
        const submitBtn = document.querySelector('.confirm__button');
        const configForm = document.getElementById('configForm');

        if (!submitBtn || !configForm) {
            // Chỉ log nếu đang ở trang có form (index.php)
            if (window.location.pathname.includes('index.php')) {
                console.warn("⚠️ Không tìm thấy Form hoặc Nút bấm cấu hình.");
            }
            return;
        }

        const self = this;

        submitBtn.addEventListener('click', async function (e) {
            e.preventDefault();
            console.log("👆 Đã bấm nút 'Phối đồ ngay'!");

            const formData = self.collectFormData();
            if (!formData) return;

            const loadingProgress = document.getElementById('loadingProgress');
            const resultSection = document.getElementById('result');
            const resultContainer = document.querySelector('.result__container');

            // Ẩn kết quả cũ, hiện loading
            if (resultSection) {
                resultSection.style.display = 'flex';
                resultSection.classList.add('is-loading');
                resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            if (resultContainer) resultContainer.style.display = 'none';
            if (loadingProgress) {
                loadingProgress.style.display = 'flex';
            }

            //Gọi API thật
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000); // Timeout
                const response = await fetch('includes/suggest-outfit.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                const textResponse = await response.text();
                let data;
                try {
                    data = JSON.parse(textResponse);
                } catch (err) {
                    throw new Error("Lỗi Server trả về không phải JSON");
                }

                console.log("✅ Kết quả trả về:", data);

                if (loadingProgress) loadingProgress.style.display = 'none';
                if (resultSection) resultSection.classList.remove('is-loading');

                if (data.success) {
                    self.displayResult(data.data);
                    self.showNotification('Đã phối đồ xong!', 'success');
                } else {
                    console.error("🔥 LỖI TỪ PHP BÁO VỀ:", data.error);
                    self.showNotification(data.error || 'Có lỗi xảy ra', 'error');
                }

                if (loadingProgress) loadingProgress.style.display = 'none';

                if (data.success) {
                    if (resultContainer) resultContainer.style.display = 'flex';

                    self.displayResult(data.data);
                    self.showNotification('Đã phối đồ xong!', 'success');

                    if (resultSection) resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

                    //================ save ofits
                    const btnSave = document.querySelector('button[onclick="app.toggleSaveOutfit(this)"]');
                    if (btnSave && data.data) {
                        btnSave.setAttribute('data-top', data.data.topId);
                        btnSave.setAttribute('data-bottom', data.data.bottomId);
                        btnSave.setAttribute('data-shoes', data.data.shoesId);
                        btnSave.setAttribute('data-acc', data.data.accId || 'null');
                        btnSave.setAttribute('data-style', data.data.style);

                        // Reset icon về rỗng
                        const icon = btnSave.querySelector('i');
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        btnSave.querySelector('span').innerText = 'Lưu set đồ';
                    }
                }

            } catch (error) {
                console.error("❌ Lỗi:", error);
                if (loadingProgress) loadingProgress.style.display = 'none';
                if (resultSection) resultSection.classList.remove('is-loading');
                self.showNotification(error.message, 'error');
            }

        });
    },

    // Chuyển đổi giá trị 
    displayResult: function (data) {
        // 1. Cập nhật Tiêu đề và Mô tả (Caption từ AI)
        const styleEl = document.getElementById('outfitStyle');
        const descEl = document.getElementById('outfitDesc');

        if (styleEl) styleEl.innerText = data.style;
        if (descEl) descEl.innerHTML = data.explanation;

        // 2. Cập nhật Hình ảnh (Chỉ Áo và Quần)
        const setImg = (id, src) => {
            const el = document.getElementById(id);
            if (el) el.src = src;
        };
        setImg('imgTop', data.topImage);
        setImg('imgBottom', data.bottomImage);

        // 3. Cập nhật danh sách items
        const setText = (id, text) => {
            const el = document.getElementById(id);
            if (el) el.innerText = text;
        };
        setText('itemTopName', data.top);
        setText('itemBottomName', data.bottom);
        setText('itemShoes', data.shoes);
        setText('itemHead', data.accessories);

        // 4. Hiển thị Section kết quả
        const resultSection = document.getElementById('result');
        if (resultSection) {
            resultSection.style.display = 'flex';
            // Chỉ scroll nếu không phải đang khôi phục từ localStorage
            if (!data.isRestored) {
                resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // 5. Lưu vào localStorage
        if (!data.isRestored) {
            localStorage.setItem('smartfit_last_outfit', JSON.stringify(data));
        }
    },

    // Khôi phục kết quả từ localStorage
    restoreOutfit: function () {
        const savedData = localStorage.getItem('smartfit_last_outfit');
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                console.log("🔄 Đang khôi phục kết quả phối đồ từ localStorage...");
                data.isRestored = true;
                this.displayResult(data);
                
                // Cập nhật dữ liệu cho nút lưu (quan trọng nếu người dùng đăng nhập sau khi khôi phục)
                const btnSave = document.querySelector('button[onclick="app.toggleSaveOutfit(this)"]');
                if (btnSave) {
                    btnSave.setAttribute('data-top', data.topId);
                    btnSave.setAttribute('data-bottom', data.bottomId);
                    btnSave.setAttribute('data-shoes', data.shoesId);
                    btnSave.setAttribute('data-acc', data.accId || 'null');
                    btnSave.setAttribute('data-style', data.style);
                }
            } catch (e) {
                console.error("Lỗi khi khôi phục outfit:", e);
                localStorage.removeItem('smartfit_last_outfit');
            }
        }
    },

    // Lấy thông tin 
    collectFormData: function () {
        const occasion = document.querySelector('input[name="occasion"]:checked')?.value;
        const gender = document.querySelector('input[name="gender"]:checked')?.value;
        const style = document.querySelector('input[name="style"]:checked')?.value;
        const color = document.querySelector('input[name="color"]:checked')?.value;
        const fit = document.querySelector('input[name="fit"]:checked')?.value;
        const note = document.querySelector('.config-form__textarea')?.value || '';

        if (!occasion || !gender || !style || !color || !fit) {
            this.showNotification('Vui lòng chọn đầy đủ thông tin!', 'error');
            return null;
        }

        const tempText = document.querySelector('.info__weather__temp')?.innerText || '25';
        return {
            occasion, gender, style, color, fit, note,
            weather: { temp: parseInt(tempText), condition: 'cloudy' },
            timeOfDay: 'day'
        };
    },

    resetForm: function () {
        const configForm = document.getElementById('configForm');
        const resultSection = document.getElementById('result');
        if (configForm) configForm.reset();
        if (resultSection) resultSection.style.display = 'none';
        document.getElementById('hero')?.scrollIntoView({ behavior: 'smooth' });
    },

    showNotification: function (msg, type) {
        if (window.showToast) window.showToast(msg, type);
        else alert(msg);
    },

    // Mở / đóng giỏ hàng
    openCart: function () {
        document.getElementById('cartOverlay').classList.add('show');
        document.getElementById('cartDrawer').classList.add('open');

        document.body.style.overflow = 'hidden';
    },

    closeCart: function () {
        document.getElementById('cartOverlay').classList.remove('show');
        document.getElementById('cartDrawer').classList.remove('open');

        document.body.style.overflow = '';
    },

    // lưu trang phục
    toggleSaveOutfit: function (btnElement) {
        const self = this;
        const isAlreadySaved = btnElement.classList.contains('is-saved');

        // 1. CHỈ ĐỌC dữ liệu từ các thuộc tính data-* của nút bấm
        const topId = btnElement.getAttribute('data-top');
        const bottomId = btnElement.getAttribute('data-bottom');
        const shoesId = btnElement.getAttribute('data-shoes');
        const accId = btnElement.getAttribute('data-acc');
        const styleName = btnElement.getAttribute('data-style');

        // Chuyển đổi giữa save và unsave dựa trên trạng thái nút
        const url = isAlreadySaved ? 'includes/unsave_outfit.php' : 'includes/save_outfit.php';

        // 2. Gom dữ liệu gửi đi 
        const dataToSend = {
            top_id: topId,
            bottom_id: bottomId,
            shoes_id: shoesId,
            acc_id: (accId && accId !== 'null' && accId !== '') ? accId : null,
            style_name: styleName || "Phong cách gợi ý"
        };

        // 3. Gửi xuống PHP
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dataToSend)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const icon = btnElement.querySelector('i');
                    const span = btnElement.querySelector('span');

                    if (isAlreadySaved) {
                        // Logic giống phần xóa: đưa về trạng thái chưa lưu
                        btnElement.classList.remove('is-saved');
                        icon.classList.replace('fa-solid', 'fa-regular');
                        span.innerText = 'Lưu set đồ';
                        self.showNotification('Đã bỏ lưu set đồ!', 'success');
                    } else {
                        // Logic lưu thành công
                        btnElement.classList.add('is-saved');
                        icon.classList.replace('fa-regular', 'fa-solid');
                        span.innerText = 'Đã lưu';
                        self.showNotification('Lưu thành công!', 'success');
                    }
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi mạng xảy ra khi lưu.');
            });
    },

    deleteSavedOutfit: function (id, btnElement) {
        // if (!confirm('Bạn có chắc chắn muốn xóa không?')) return;

        fetch('../includes/delete_saved_outfit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const cardItem = btnElement.closest('.col');
                    if (cardItem) cardItem.remove();
                    if (window.showToast) showToast('Đã xóa thành công!', 'success');
                } else {
                    alert('Lỗi: ' + data.message);
                }
            });
    },

    // Hiệu ứng bay tới giỏ hàng (giả lập)
    flyToCart: function (imgElement, cartIconElement) {
        if (!imgElement || !cartIconElement) return;

        // Tạo bản sao của ảnh
        const flyImg = imgElement.cloneNode();
        const imgRect = imgElement.getBoundingClientRect();
        const cartRect = cartIconElement.getBoundingClientRect();

        // Thêm class css cho hiệu ứng
        flyImg.classList.add('fly-item');

        // Vị trí bắt đầu (Tại vị trí ảnh gốc)
        flyImg.style.top = `${imgRect.top}px`;
        flyImg.style.left = `${imgRect.left}px`;
        flyImg.style.width = `${imgRect.width}px`;
        flyImg.style.height = `${imgRect.height}px`;

        document.body.appendChild(flyImg);

        // Vị trí kết thúc (Bay tới icon giỏ hàng)
        setTimeout(() => {
            flyImg.style.top = `${cartRect.top + 10}px`;
            flyImg.style.left = `${cartRect.left + 10}px`;
            flyImg.style.width = '20px';
            flyImg.style.height = '20px';
            flyImg.style.opacity = '0';
        }, 10);

        // Xóa ảnh sau khi bay xong (0.8 giây)
        setTimeout(() => flyImg.remove(), 800);
    },

    // ===== DỰ BÁO 7 NGÀY (Open-Meteo) =====

    // Dịch mã WMO sang tiếng Việt
    translateWeatherCode: function (code) {
        return this.config.weatherCodeMap[code] || 'Không xác định';
    },

    // Gọi API Open-Meteo và render dropdown
    fetch7DaysForecast: function (lat, lng) {
        const self = this;
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_probability_max&timezone=auto`;

        console.log('📅 Đang lấy dự báo 7 ngày...');

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Không lấy được dự báo 7 ngày');
                return response.json();
            })
            .then(data => {
                console.log('📅 Dữ liệu dự báo 7 ngày:', data);
                const daily = data.daily;
                if (!daily || !daily.time) return;

                const dropdown = document.getElementById('forecastDropdown');
                if (!dropdown) return;

                // Xóa các option cũ
                dropdown.innerHTML = '';

                // Tạo 7 option mới
                daily.time.forEach(function (dateStr, i) {
                    const weatherCode = daily.weathercode[i];
                    const tempMax = Math.round(daily.temperature_2m_max[i]);
                    const tempMin = Math.round(daily.temperature_2m_min[i]);
                    const rainProb = daily.precipitation_probability_max[i];
                    const weatherText = self.translateWeatherCode(weatherCode);

                    // Format ngày tháng (VD: 13/03)
                    const dateObj = new Date(dateStr);
                    const dayMonth = dateObj.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                    const label = i === 0 ? 'Hôm nay' : dayMonth;

                    // Value chứa toàn bộ thông tin ngày đó dạng JSON
                    const optionValue = JSON.stringify({
                        date: dateStr,
                        weatherCode: weatherCode,
                        weatherText: weatherText,
                        tempMax: tempMax,
                        tempMin: tempMin,
                        rainProbability: rainProb
                    });

                    const option = document.createElement('option');
                    option.value = optionValue;
                    option.textContent = `${label}: ${weatherText} - ${tempMax}°C (Mưa: ${rainProb}%)`;
                    dropdown.appendChild(option);
                });

                // Mặc định lưu dữ liệu "Hôm nay" vào localStorage
                if (dropdown.options.length > 0) {
                    localStorage.setItem('smartfit_target_weather', dropdown.options[0].value);
                    console.log('💾 Đã lưu dự báo Hôm nay vào localStorage');
                }
            })
            .catch(error => {
                console.error('❌ Lỗi lấy dự báo 7 ngày:', error);
            });
    },

    // Khởi tạo sự kiện change cho dropdown dự báo
    initForecastDropdown: function () {
        const dropdown = document.getElementById('forecastDropdown');
        if (!dropdown) return;

        dropdown.addEventListener('change', function () {
            const selectedValue = dropdown.value;
            if (selectedValue) {
                localStorage.setItem('smartfit_target_weather', selectedValue);
                console.log('💾 Đã lưu dự báo ngày đã chọn:', JSON.parse(selectedValue));
            }
        });
    },

      // ---------------------------------------------------------
    // Nút cuộn trang (Scroll Button)
    // ---------------------------------------------------------
    initScrollBtn: function () {
        const scrollBtn = document.getElementById('scrollBtn');
        const heroSection = document.getElementById('hero');
        const featuresSection = document.querySelector('.features');

        if (!scrollBtn) return;

        console.log("🖱️ Khởi tạo Nút cuộn trang...");

        // Xử lý xoay mũi tên: Xoay lên khi ra khỏi Hero
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // Nếu Hero KHÔNG còn thấy được (đã cuộn xuống qua khỏi nó)
                if (!entry.isIntersecting) {
                    scrollBtn.classList.add('up');
                } else {
                    // Nếu đang ở Hero
                    scrollBtn.classList.remove('up');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '-80px 0px 0px 0px' // Bỏ qua phần navbar sticky
        });

        observer.observe(heroSection);

        // Xử lý sự kiện click
        scrollBtn.addEventListener('click', () => {
            if (scrollBtn.classList.contains('up')) {
                // Quay lên đầu trang
                heroSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                // Cuộn xuống
                if (featuresSection) {
                    featuresSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    window.scrollBy({
                        top: window.innerHeight,
                        behavior: 'smooth'
                    });
                }
            }
        });
    },

};

document.addEventListener('DOMContentLoaded', () => {
    window.app.start();
});
