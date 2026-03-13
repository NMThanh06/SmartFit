<?php include '../includes/header.php'; ?>

        <!-- Hero Section -->
        <section class="hero">
            <h1 class="hero__title">Mặc gì hôm nay? Để AI lo!</h1>
            <p class="hero__subtitle">Giải pháp quản lý tủ đồ thông minh và gợi ý trang phục cá nhân hóa dựa trên thời tiết và phong cách của riêng bạn.</p>
            <a href="../index.php" class="button hero__btn">Trải nghiệm phối đồ ngay</a>
        </section>

        <!-- Divider -->
        <div class="section-divider"></div>

        <!-- Features Section -->
        <section class="features">
            <h2 class="section-title">Tính năng nổi bật</h2>
            <div class="row">
                <div class="col l-4 m-6 c-12">
                    <div class="feature-card">
                        <i class="fa-solid fa-robot feature-card__icon"></i>
                        <h3 class="feature-card__title">Trợ lý phối đồ AI</h3>
                        <p class="feature-card__desc">Tự động đề xuất các bộ cánh thời thượng dựa trên nhiệt độ, thời tiết thực tế tại vị trí của bạn và mục đích sử dụng (đi học, đi chơi, hẹn hò).</p>
                    </div>
                </div>
                <div class="col l-4 m-6 c-12">
                    <div class="feature-card">
                        <i class="fa-solid fa-shirt feature-card__icon"></i>
                        <h3 class="feature-card__title">Tủ đồ thông minh</h3>
                        <p class="feature-card__desc">Lưu trữ và quản lý những set đồ bạn yêu thích. Không còn mất thời gian lục tìm hay quên mất mình có những món đồ nào.</p>
                    </div>
                </div>
                <div class="col l-4 m-12 c-12">
                    <div class="feature-card">
                        <i class="fa-solid fa-store feature-card__icon"></i>
                        <h3 class="feature-card__title">Cửa hàng thời trang</h3>
                        <p class="feature-card__desc">Khám phá và sở hữu ngay những item mới nhất để bổ sung vào bộ sưu tập cá nhân với trải nghiệm mua sắm mượt mà.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works Section -->
        <section class="how-it-works">
            <h2 class="section-title">Cách thức hoạt động</h2>
            <div class="how-it-works__container">
                <!-- Wavy Line Background -->
                <svg class="how-it-works__line" viewBox="-300 0 1600 200" preserveAspectRatio="none">
                    <path d="M -300,100 C -50,250 250,-50 500,100 C 750,250 1050,-50 1300,100" 
                        fill="transparent" 
                        stroke="#6C63FF" 
                        stroke-width="4" 
                        stroke-dasharray="10, 10"></path>
                </svg>

                <i class="fa fa-paper-plane how-it-works__icon"></i>

                <div class="row how-it-works__steps">
                    <div class="col l-4 m-4 c-12">
                        <div class="step-card">
                            <div class="step-card__number">1</div>
                            <h3 class="step-card__title">Cung cấp thông tin</h3>
                            <p class="step-card__desc">Chọn dịp bạn mặc, phong cách và tông màu bạn thích.</p>
                        </div>
                    </div>
                    <div class="col l-4 m-4 c-12">
                        <div class="step-card step-card--middle">
                            <div class="step-card__number">2</div>
                            <h3 class="step-card__title">AI Phân tích</h3>
                            <p class="step-card__desc">Hệ thống kết hợp sở thích của bạn với dữ liệu thời tiết thực tế.</p>
                        </div>
                    </div>
                    <div class="col l-4 m-4 c-12">
                        <div class="step-card">
                            <div class="step-card__number">3</div>
                            <h3 class="step-card__title">Nhận kết quả</h3>
                            <p class="step-card__desc">Nhận ngay gợi ý phối đồ hoàn hảo kèm hình ảnh trực quan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="why-choose-us">
            <h2 class="section-title">Tại sao chọn SmartFit?</h2>
            <div class="row">
                <div class="col l-4 m-4 c-12">
                    <div class="reason-card">
                        <i class="fa-solid fa-clock reason-card__icon"></i>
                        <h3 class="reason-card__title">Tiết kiệm thời gian</h3>
                        <p class="reason-card__desc">Chỉ mất 5 giây để có một bộ đồ đẹp thay vì đứng 30 phút trước gương.</p>
                    </div>
                </div>
                <div class="col l-4 m-4 c-12">
                    <div class="reason-card">
                        <i class="fa-solid fa-cloud-sun reason-card__icon"></i>
                        <h3 class="reason-card__title">Luôn phù hợp thời tiết</h3>
                        <p class="reason-card__desc">Không còn tình trạng "thời trang phang thời tiết" nhờ dữ liệu Real-time.</p>
                    </div>
                </div>
                <div class="col l-4 m-4 c-12">
                    <div class="reason-card">
                        <i class="fa-solid fa-box-open reason-card__icon"></i>
                        <h3 class="reason-card__title">Tối ưu tủ đồ</h3>
                        <p class="reason-card__desc">Tận dụng tối đa những gì bạn đang có và chỉ mua những thứ thực sự cần thiết.</p>
                    </div>
                </div>
            </div>
        </section>
         
<?php include '../includes/footer.php'; ?>
</body>
</html>