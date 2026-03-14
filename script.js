window.app = {
    // --- 1. CẤU HÌNH (CONFIG) ---
    config: {
        apiKey: '2cb97f62395b42556d493874d4486859', // Key của bạn
        apiUrl: 'https://api.openweathermap.org/data/2.5/weather',

        videos: {
            Clear: './assets/video/sunny.mp4',
            Clouds: './assets/video/cloudy.mp4',
            Rain: './assets/video/rainy.mp4',
            Drizzle: './assets/video/rainy.mp4',
            Thunderstorm: './assets/video/rainy.mp4',
            Snow: './assets/video/snowy.mp4',
            Default: './assets/video/cloudy.mp4'
        }
    },

    // --- 2. CÁC HÀM XỬ LÝ ---
    start: function () {
        console.log("🚀 Ứng dụng bắt đầu chạy...");

        this.initAuthEvents();
        this.initUserMenu();
        this.startClock();
        this.initFormEvent();
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
        console.log(`📍 Tìm thấy tọa độ: ${lat}, ${lon}`);
        const url = `${this.config.apiUrl}?lat=${lat}&lon=${lon}&appid=${this.config.apiKey}&units=metric&lang=vi`;

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
    },

    handleLocationError: function (error) {
        console.warn("Lỗi định vị:", error.message);

        const mockData = {
            main: { temp: 25 },
            weather: [{ main: "Default" }],
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

        // 2. Thay đổi icon thời tiết
        const iconEl = document.querySelector('.info__weather__icon');
        if (iconEl) {
            let weatherIconMsg = `☁️`;
            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') weatherIconMsg = "🌧️";
            else if (condition === 'Clear') weatherIconMsg = "☀️";
            else if (condition === 'Snow') weatherIconMsg = "🌨️";
            iconEl.innerHTML = weatherIconMsg;
        }

        // 3. Thay đổi chữ thời tiết
        const weatherTextElement = document.querySelector('.info__weather__text');
        if (weatherTextElement) {
            let weatherTextMsg = `Trời mây&nbsp`;
            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') weatherTextMsg = "Trời mưa&nbsp";
            else if (condition === 'Clear') weatherTextMsg = "Trời nắng&nbsp";
            else if (condition === 'Snow') weatherTextMsg = "Trời tuyết&nbsp";
            weatherTextElement.innerHTML = weatherTextMsg;
        }

        // 4. Thay đổi địa điểm và lời khuyên
        const descElement = document.querySelector('.info__desc');
        if (descElement) {
            let descMsg = `<b>${locationName}</b>`;
            if (condition === 'Rain' || condition === 'Drizzle') descMsg += " đang mưa đó ☔, nhớ mang ô nhé!";
            else if (temp > 32) descMsg += " trời đang khá nóng đấy 🥵, nhớ mặc đồ mát chút nhé!";
            else if (temp < 18) descMsg += " trời đang lạnh rồi đấy 🥶, nhớ mặc gì đó ấm nhé!";
            else if (condition === 'Clear') descMsg += " trời đang đẹp đấy ☀️, đi chơi thôi!";
            else descMsg += "✨ Thời tiết ổn, lên đồ thôi!";

            descElement.innerHTML = descMsg;
        }

        // 5. Thay đổi Video nền
        const videoElement = document.querySelector('.web__background');
        if (videoElement) {
            const videoSrc = this.config.videos[condition] || this.config.videos.Default;
            if (videoElement.src && !videoElement.src.includes(videoSrc.substring(2))) {
                videoElement.src = videoSrc;
            }
        }
    },

    // Copy mail
    copyToClipboard: function (element) {
        const emailText = element.querySelector('span').innerText;
        const tooltip = element.querySelector('.copy-tooltip');

        navigator.clipboard.writeText(emailText)
            .then(() => {
                tooltip.classList.add("show");

                setTimeout(() => {
                    tooltip.classList.remove("show");
                }, 2000);
            })
            .catch(err => {
                console.error('Lỗi khi copy: ', err);
                alert("Không thể copy email này!");
            });
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
            if (resultSection) resultSection.style.display = 'flex';
            if (resultContainer) resultContainer.style.display = 'none';
            if (loadingProgress) {
                loadingProgress.style.display = 'flex';
                loadingProgress.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
            resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
    }


};

document.addEventListener('DOMContentLoaded', () => {
    window.app.start();
});
