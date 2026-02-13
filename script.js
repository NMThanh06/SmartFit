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

        // --- 1. XỬ LÝ THỜI GIAN ---
        const localTime = new Date((data.dt + data.timezone) * 1000);
        const hour = localTime.getUTCHours();
        const minutes = localTime.getUTCMinutes();
        const minuteString = minutes < 10 ? `0${minutes}` : minutes;

        // Xác định buổi
        let session = "";
        if (hour >= 5 && hour < 12) session = "day";
        else if (hour >= 12 && hour < 17) session = "afternoon";
        else session = "night";

        // --- 2. CẬP NHẬT GIAO DIỆN ---
        // A. Thay đổi số Độ
        const tempElement = document.querySelector('.info__weather__temp');
        if (tempElement) {
            tempElement.innerHTML = `${temp}<span>°C</span>`;
        }

        // B. Thay đổi câu chào
        const greetingElement = document.querySelector('.info__greeting');
        let greetingMsg = `<span style="margin-right: 15px; font-weight: bold;">${hour}:${minuteString} -</span>`;

        if (session === "day") greetingMsg += "Chào buổi sáng 🌅, hôm nay bạn thấy thế nào ?";
        else if (session === "afternoon") greetingMsg += "Chào buổi trưa ☀️, hôm nay bạn thấy thế nào ?";
        else greetingMsg += "Chào buổi tối 🌙, hôm nay bạn thấy thế nào ?";

        if (greetingElement) greetingElement.innerHTML = greetingMsg;

        // C. Thay đổi icon thời tiết
        const weatherIconElement = document.querySelector('.info__weather__icon');
        if (weatherIconElement) {
            let weatherIconMsg = `☁️`;

            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') weatherIconMsg = "🌧️";
            else if (condition === 'Clear') weatherIconMsg = "☀️";
            else if (condition === 'Snow') weatherIconMsg = "🌨️";
            else if (condition === 'Clouds' || condition === 'Mist' || condition === 'Haze' || condition === 'Fog') weatherIconMsg = "☁️";

            weatherIconElement.innerHTML = weatherIconMsg;
        }

        // D. Thay đổi thời tiết
        const weatherTextElement = document.querySelector('.info__weather__text');
        if (weatherTextElement) {
            let weatherTextMsg = `Trời mây&nbsp`;

            if (condition === 'Rain' || condition === 'Drizzle' || condition === 'Thunderstorm') weatherTextMsg = "Trời mưa&nbsp";
            else if (condition === 'Clear') weatherTextMsg = "Trời nắng&nbsp";
            else if (condition === 'Snow') weatherTextMsg = "Trời tuyết&nbsp";
            else if (condition === 'Clouds' || condition === 'Mist' || condition === 'Haze' || condition === 'Fog') weatherTextMsg = "Trời mây&nbsp";

            weatherTextElement.innerHTML = weatherTextMsg;
        }

        // E. Thay đổi câu mô tả (Desc)
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

        // F. Thay đổi Video nền
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
    }
};

app.start();