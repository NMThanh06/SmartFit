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

    startClock: function() {
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
        if(!data.main) return;
        
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
    initUserMenu: function() {
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

    initFormEvent: function() {
        const configForm = document.querySelector('.config-form');
        const resultSection = document.getElementById('result');

        if (configForm) {
            configForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                if (resultSection) {
                    resultSection.style.display = 'flex';
                    
                    resultSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            });
        }
    },

    resetForm: function() {
        const resultSection = document.getElementById('result');
        const configForm = document.querySelector('.config-form');
        
        if (resultSection) resultSection.style.display = 'none';
        if (configForm) {
            configForm.reset();
            configForm.scrollIntoView({ behavior: 'smooth' });
        }
    },

    // Xử lý nút bấm 
    initFormEvent: function() {
        const submitBtn = document.querySelector('.confirm__button');
        const configForm = document.getElementById('configForm');

        if (!submitBtn || !configForm) {
            console.error("❌ Không tìm thấy Form hoặc Nút bấm");
            return;
        }

        const self = this;

        submitBtn.addEventListener('click', async function(e) {
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
        //     try {
        //         const controller = new AbortController();
        //         const timeoutId = setTimeout(() => controller.abort(), 60000); // Timeout
        //         const response = await fetch('includes/suggest-outfit.php', {
        //             method: 'POST',
        //             headers: { 'Content-Type': 'application/json' },
        //             body: JSON.stringify(formData),
        //             signal: controller.signal
        //         });

        //         clearTimeout(timeoutId);

        //         const textResponse = await response.text();
        //         let data;
        //         try {
        //             data = JSON.parse(textResponse);
        //         } catch (err) {
        //             throw new Error("Lỗi Server trả về không phải JSON");
        //         }

        //         console.log("✅ Kết quả trả về:", data);
                
        //         if (loadingProgress) loadingProgress.style.display = 'none';

        //         if (data.success) {
        //             self.displayResult(data.data);
        //             self.showNotification('Đã phối đồ xong!', 'success');
        //         } else {
        //             console.error("🔥 LỖI TỪ PHP BÁO VỀ:", data.error); 
        //             self.showNotification(data.error || 'Có lỗi xảy ra', 'error');
        //         }

        //         if (loadingProgress) loadingProgress.style.display = 'none';

        //         if (data.success) {
        //             if (resultContainer) resultContainer.style.display = 'flex'; 
                    
        //             self.displayResult(data.data);
        //             self.showNotification('Đã phối đồ xong!', 'success');
                    
        //             if (resultSection) resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        //         }

        //     } catch (error) {
        //         console.error("❌ Lỗi:", error);
        //         if (loadingProgress) loadingProgress.style.display = 'none';
        //         self.showNotification(error.message, 'error');
        //     }

            //Dữ liệu giả
            // --- BẮT ĐẦU: DÙNG DATA GIẢ (MOCK DATA) ---
            
            // 1. Tạo cục dữ liệu giả
            const mockData = {
                success: true,
                data: {
                    style: "Streetwear Năng Động (Test Data)",
                    explanation: "Dựa trên thời tiết 24°C và dịp Đi chơi, mình chọn cho bạn một set đồ thoải mái, vừa đủ ấm nhưng vẫn cực kỳ cool ngầu. (Đây là data giả nhé!)",
                    // Lấy tạm 2 cái link ảnh trên mạng để test UI
                    topImage: "https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=400&auto=format&fit=crop", 
                    bottomImage: "https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=400&auto=format&fit=crop",
                    top: "Áo thun đen form rộng",
                    bottom: "Quần Jeans xanh ống suông",
                    shoes: "Sneaker Nike Air Force 1",
                    accessories: "Mũ lưỡi trai Balenciaga"
                }
            };

            // 2. Giả lập thời gian chờ AI suy nghĩ (Ví dụ: 2 giây)
            setTimeout(() => {
                // Tắt vòng xoay loading
                if (loadingProgress) loadingProgress.style.display = 'none';

                // Bật thẻ kết quả lên
                if (resultContainer) resultContainer.style.display = 'flex'; 
                
                // Đổ data giả vào giao diện
                self.displayResult(mockData.data);
                self.showNotification('Đã phối đồ xong (Data giả)!', 'success');
                
            }, 2000); // 2000 = 2 giây

            // --- KẾT THÚC: DÙNG DATA GIẢ ---
            
        });
    },

    // Chuyển đổi giá trị 
    displayResult: function(data) {
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
            resultSection.style.display = 'block';
            resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    },

    // Lấy thông tin 
    collectFormData: function() {
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

    resetForm: function() {
        const configForm = document.getElementById('configForm');
        const resultSection = document.getElementById('result');
        if (configForm) configForm.reset();
        if (resultSection) resultSection.style.display = 'none';
        document.getElementById('hero')?.scrollIntoView({ behavior: 'smooth' });
    },

    showNotification: function(msg, type) {
        if (window.showToast) window.showToast(msg, type);
        else alert(msg);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.app.start();
});