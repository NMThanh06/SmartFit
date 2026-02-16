const app = {
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
        const temp = Math.round(data.main.temp);
        const condition = data.weather[0].main;
        const locationName = data.name;

        // --- CẬP NHẬT GIAO DIỆN ---
        // 1. Thay đổi số Độ
        const tempElement = document.querySelector('.info__weather__temp');
        if (tempElement) {
            tempElement.innerHTML = `${temp}<span>°C</span>`;
        }

        // 2. Thay đổi icon thời tiết
        const weatherIconElement = document.querySelector('.info__weather__icon');
        if (weatherIconElement) {
            let weatherIconMsg = `☁️`;

            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') weatherIconMsg = "🌧️";
            else if (condition === 'Clear') weatherIconMsg = "☀️";
            else if (condition === 'Snow') weatherIconMsg = "🌨️";
            else if (condition === 'Clouds' || condition === 'Mist' || condition === 'Haze' || condition === 'Fog') weatherIconMsg = "☁️";

            weatherIconElement.innerHTML = weatherIconMsg;
        }

        // 3. Thay đổi thời tiết
        const weatherTextElement = document.querySelector('.info__weather__text');
        if (weatherTextElement) {
            let weatherTextMsg = `Trời mây&nbsp`;

            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') weatherTextMsg = "Trời mưa&nbsp";
            else if (condition === 'Clear') weatherTextMsg = "Trời nắng&nbsp";
            else if (condition === 'Snow') weatherTextMsg = "Trời tuyết&nbsp";
            else if (condition === 'Clouds' || condition === 'Mist' || condition === 'Haze' || condition === 'Fog') weatherTextMsg = "Trời mây&nbsp";

            weatherTextElement.innerHTML = weatherTextMsg;
        }

        // 4. Thay đổi câu mô tả (Desc)
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

            if (!videoElement.src.includes(videoSrc.substring(2))) {
                videoElement.src = videoSrc;
                videoElement.load();
                videoElement.play().catch(e => console.log("Video autoplay blocked"));
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
            // 1. Sự kiện click vào tên user
            userInfo.onclick = (e) => {
                e.stopPropagation(); // Ngăn chặn sự kiện nổi bọt (để không bị tính là click ra ngoài)
                userDropdown.classList.toggle('show'); // Bật/Tắt class show
                userInfo.classList.toggle('active');   // Để xoay mũi tên
            };

            // 2. Sự kiện click ra ngoài thì đóng menu
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
    }
};

app.start();