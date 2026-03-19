-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2026 at 06:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webtest_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `note` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `vnp_transaction_no` varchar(50) DEFAULT NULL,
  `total_amount` int(11) NOT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `shop_id`, `fullname`, `phone`, `address`, `note`, `payment_method`, `payment_status`, `vnp_transaction_no`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(20260319175204339, 20, 6, 'Thành', '0908377239', 'Thủ đức, TPHCM', '', 'cod', 'pending', NULL, 250000, 'pending', '2026-03-19 16:52:04', '2026-03-19 16:52:04'),
(20260319180055135, 21, 6, 'Thành', '0908377239', 'Thủ đức, TPHCM', '', 'cod', 'pending', NULL, 250000, 'pending', '2026-03-19 17:00:55', '2026-03-19 17:00:55');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `size_name` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `outfit_id`, `size_name`, `quantity`, `price`) VALUES
(37, 20260319175204339, 21, 'L', 1, 250000),
(38, 20260319180055135, 21, 'L', 1, 250000);

-- --------------------------------------------------------

--
-- Table structure for table `outfits`
--

CREATE TABLE `outfits` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `image` text DEFAULT NULL,
  `gender` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gender`)),
  `occasion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`occasion`)),
  `style` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`style`)),
  `fit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fit`)),
  `weather` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`weather`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `age` varchar(50) DEFAULT NULL COMMENT 'Khoảng độ tuổi (VD: 15-20, 21-30, All)',
  `seller_note` text DEFAULT NULL COMMENT 'Ghi chú tự do, gợi ý phối đồ của người bán',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết sản phẩm',
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_commercial` tinyint(1) DEFAULT 1,
  `total_sold` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng số lượng đã bán',
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Điểm đánh giá trung bình (1.00 - 5.00)',
  `review_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng số lượt đánh giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outfits`
--

INSERT INTO `outfits` (`id`, `name`, `type`, `price`, `image`, `gender`, `occasion`, `style`, `fit`, `weather`, `created_at`, `age`, `seller_note`, `description`, `owner_id`, `is_commercial`, `total_sold`, `avg_rating`, `review_count`) VALUES
(1, 'Áo thun trơn form boxy', 'top', 200000, NULL, '[\"male\",\"female\"]', '[\"study\",\"goout\",\"date\",\"event\"]', '[\"basic\"]', '[\"oversized\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-15 16:13:57', 'All', '', 'Chất liệu: Cotton\r\nĐộ dày vải: 250gsm\r\nHọa tiết: Trơn', 2, 1, 0, 0.00, 0),
(2, 'Giày Nike Air Force 1', 'shoes', 1000000, NULL, '[\"male\",\"female\"]', '[\"study\",\"goout\",\"date\",\"event\"]', '[\"basic\",\"street\"]', '[\"regular\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-15 16:14:59', 'All', '', '', 2, 1, 0, 0.00, 0),
(3, 'Quần jean ống rộng', 'bottom', 199000, NULL, '[\"male\"]', '[\"study\",\"goout\",\"date\",\"event\"]', '[\"basic\",\"street\"]', '[\"oversized\"]', '[\"mild\",\"cold\"]', '2026-03-16 15:38:22', 'All', '', '', 2, 1, 0, 0.00, 0),
(4, 'Áo Hoodie', 'top', 370000, NULL, '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"street\"]', '[\"oversized\"]', '[\"mild\",\"cold\"]', '2026-03-16 19:21:07', 'All', '', '', 2, 1, 0, 0.00, 0),
(8, 'Quần Parachute túi hộp', 'bottom', 200000, NULL, '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"street\"]', '[\"oversized\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-17 17:09:17', '25', '', 'Chất liệu: Vải dù', 4, 1, 0, 0.00, 0),
(9, 'Quần Kaki ống cong', 'bottom', 300000, NULL, '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"street\"]', '[\"oversized\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-17 17:15:27', '25', '', 'Chất liệu: Vải Khaki', 4, 1, 0, 0.00, 0),
(10, 'Quần Jean slim fit', 'bottom', 350000, NULL, '[\"male\"]', '[\"study\",\"goout\",\"date\",\"event\"]', '[\"basic\",\"vintage\"]', '[\"slim\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-17 17:23:46', '30', '', 'Chất liệu: 98% Cotton & 2% Spandex', 4, 1, 0, 0.00, 0),
(11, 'Quần Jeans basic', 'bottom', 400000, NULL, '[\"male\"]', '[\"study\",\"goout\",\"date\",\"event\"]', '[\"basic\",\"vintage\"]', '[\"regular\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-17 19:51:24', '30', '', 'Chất liệu: Denim', 4, 1, 0, 0.00, 0),
(12, 'Áo Dài Hạc Vũ', 'one-piece', 699000, NULL, '[\"female\"]', '[\"goout\",\"event\"]', '[\"street\"]', '[\"regular\",\"slim\"]', '[\"mild\"]', '2026-03-17 22:17:24', '15-25', 'bộ này phù hợp cho dịp tết', '✨ Áo Dài Hạc Vũ Tag Tua Rua – Sang Trọng, Thanh Lịch, Tôn Dáng ✨\r\n\r\n\r\n\r\nMẫu áo dài Hạc Vũ được thiết kế theo phong cách truyền thống pha hiện đại, nổi bật với màu đỏ chủ đạo vô cùng nổi bật và may lớp lụa trắng bên trong tạo hiệu ứng mềm mại, tinh tế. Áo được phối họa tiết thêu hạc, biểu tượng của may mắn – thanh cao – cát tường.\r\n\r\nĐiểm nhấn đặc biệt nằm ở tag tua rua ngọc trai trước ngực giúp tổng thể thêm sang, phù hợp khi đi chùa – đi lễ – đi Tết – chụp ảnh – sự kiện.\r\n\r\nChất vải mềm mại, đứng form, bay nhẹ nhàng khi di chuyển. Tay áo lỡ tinh tế giúp che khuyết điểm cực tốt, phù hợp nhiều dáng người. Dễ kết hợp cùng túi ren, phụ kiện truyền thống hoặc phong cách hiện đại.', 8, 1, 0, 0.00, 0),
(13, 'Set Đồ Bộ Nữ Quần Lửng Chất Thun Gân Áo Cotton', 'one-piece', 199000, NULL, '[\"female\"]', '[\"goout\"]', '[\"basic\"]', '[\"regular\",\"slim\",\"oversized\"]', '[\"hot\",\"mild\"]', '2026-03-17 22:24:00', 'All', 'bộ này thích hợp khi đi ngủ ', 'Set Đồ Bộ Nữ Quần Lửng Chất Thun Gân Áo Cotton Thêu Cô Gái Bigsize Mặc Nhà Dễ Thương\r\n\r\n????Tất cả sản phẩm đều do xưởng của Xưởng tự sản xuất và tự phân phối trên toàn quốc, nên có thể bán được với giá thấp nhất trên Shopee, nên các chị không lo hàng giá rẻ thì chất lượng không ok, các chị xem thật nhiều đánh giá của khách nhé!\r\n\r\n\r\n????Shop xin phép mô tả sản phẩm và tư vấn cách chọn size cho mình để mình chọn được sản phẩm phù hợp và đẹp nhất nhé ạ.\r\n\r\n✔️Chất liệu áo thun cotton, quần tăm lạnh co giãn 4c.\r\n\r\n✔️Không xù lông, không nhăn.\r\n\r\n✔️Vải mềm mịn, rất đẹp, tất cả đều được xưởng Xưởng may, ủi đẹp đến từng đường kim mũi chỉ không khác gì hàng cao cấp như trong video mô tả do xưởng tự quay. \r\n\r\n✔️Tất cả mẫu đều là phom đúng chuẩn phom vừa hay phom rộng tay lỡ, nên các Chị đừng tăng thêm size nữa sẽ rất rộng nhé ạ, trừ các Chị có cân nặng ở cuối size như 54, 55kg có thể mặc size L, hoặc 64, 65kg có thể mặc size XL…\r\n\r\n✔️   Hỗ trợ giao hàng toàn quốc\r\n\r\n✔️  Không bán hàng kém chất lượng tới tay người tiêu dùng.\r\n\r\n✔️  Hàng luôn sẵn kho.Giá luôn tốt tuyệt đối.\r\n\r\n✔️  Thời gian giao hàng đến khách hàng là do bên vận chuyển giao, shop không phải trực tiếp giao hàng, các vấn đề về chậm trễ đơn hàng vui lòng liên hệ Shop -  Khách đọc mã số đơn hàng sẽ được nhân viên hỗ trợ ạ.\r\n\r\n✅SHOP CAM KẾT:\r\n\r\n✔️Về sản phẩm: Shop cam kết cả về CHẤT LIỆU cũng như HÌNH ẢNH ( đúng với những gì được giới thiệu trong phần mô tả sản phẩm).\r\n\r\n✔️Về giá cả : Shop tự sản xuất với số lượng lớn và trực tiếp nên chi phí sẽ là RẺ NHẤT nhé. \r\n\r\n✔️Về dịch vụ: Shop sẽ cố gắng trả lời hết những thắc mắc xoay quanh sản phẩm, giải quyết ngay lập tức những khiếu nại, phản hồi nhé ạ. \r\n\r\n✔️Thời gian chuẩn bị hàng: Hàng có sẵn, thời gian chuẩn bị tối ưu nhất. ????Quyền Lợi của Khách Hàng???????????? (Genz studio)', 8, 1, 0, 0.00, 0),
(14, 'Sandal cao gót nữ', 'shoes', 299000, NULL, '[\"female\"]', '[\"date\",\"event\"]', '[\"basic\",\"vintage\"]', '[]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-17 22:38:03', 'All', 'món này phù hợp với trang phục lịch sự , tránh những việc liên quan đến thể thao đặc biệt lao dốc , núi.', 'Thông tin sản phẩm:\r\n\r\nKích thước size: 35--->39\r\nChiều cao gót: 7cm\r\nMàu sắc : Màu đen - trắng\r\nChất liệu; Xi mờ cao cấp\r\n=> Vì góc độ ánh sáng, nên màu sắc thực tế có thể sẽ chênh lệch 1 chút so với ảnh chụp, nhưng vẫn đảm bảo chất lượng, nên nàng hãy đừng ngại ngần nha.\r\n\r\n\r\nBẢNG CHỌN SIZE (đo chân theo chiều dài từ gót đến ngón chân thứ 2):\r\n\r\n      Size 35: Chiều dài từ 22.1 đến 22.5cm\r\n\r\n      Size 36: Chiều dài từ 22.6 đến 23cm\r\n\r\n      Size 37: Chiều dài từ 23.1 đến 23.5cm\r\n\r\n      Size 38: Chiều dài từ 23.6 đến 24cm\r\n\r\n      Size 39: Chiều dài từ 24.1 đến 24.5cm\r\n\r\n=> Công thức tính size = (Chiều dài chân x 2) - 10cm\r\nCHÍNH SÁCH ĐỔI TRẢ: \r\nBảo Hành cho Nàng 100% giá trị nếu sản phẩm Nàng nhận được có vấn đề, lỗi keo đế/nhầm lẫn từ NSX.\r\nHỗ trợ Nàng đổi trả nếu Nàng đi không vừa với kích thước size của Nàng. \r\nLuôn hỗ trợ cho Nàng 24/7, nên nàng đừng ngại nhắn tin cho shop nha. \r\n(Ngọc Trinh Store????????)', 8, 1, 0, 5.00, 1),
(15, 'ĐỒ BỘ LỤA', 'one-piece', 149000, NULL, '[\"female\"]', '[\"goout\"]', '[\"basic\",\"street\",\"vintage\"]', '[\"regular\",\"slim\",\"oversized\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-17 22:53:34', '25-45', 'đồ này thích hợp cho người thuộc lứa tuổi trung niên', 'Đồ bộ lụa cao cấp được thiết kế đặc biệt dành riêng cho mẹ và bà. Với chất liệu lụa cao cấp, đồ bộ mang lại sự thoải mái và êm ái cho người mặc.\r\n\r\nThích hợp mặc đi làm hoặc đi chơi: Với kiểu dáng thanh lịch và sang trọng, đồ bộ này là sự lựa chọn hoàn hảo cho những buổi đi làm hay tham gia các sự kiện quan trọng.\r\n\r\nForm suông phù hợp với nhiều kích thước: Được thiết kế với form suông, sản phẩm phù hợp với nhiều kích thước từ 40-85kg.\r\n\r\nBên cạnh những tính năng tiện ích ở trên, sản phẩm này còn có các thuộc tính sau:\r\nChất liệu cao cấP: Sử dụng chất liệu lụa cao cấp mang lại sức khỏe tốt nhất cho người tiêu dung.(Shop ThuĐặng)', 9, 1, 0, 0.00, 0),
(16, 'Chân váy ngắn xếp ly nữ', 'bottom', 99000, NULL, '[\"female\"]', '[\"study\",\"goout\",\"date\"]', '[\"basic\",\"street\"]', '[\"regular\",\"slim\",\"oversized\"]', '[\"hot\",\"mild\"]', '2026-03-18 13:07:27', '15-25', 'đồ này thích hợp cho người thuộc lứa tuổi trung niên', '- Chất liệu: vải for.\r\n- Size : \r\nSize S  : eo dưới 64cm  & Dưới 45kg ( tùy chiều cao, đo eo chính xác nhất)\r\nSize M : eo dưới 68cm  & Dưới 48kg  ( tùy chiều cao, đo eo chính xác nhất)\r\nSize L  : eo dưới 70cm  & Dưới 52kg  ( tùy chiều cao, đo eo chính xác nhất)\r\nChiều dài : khoảng 37cm -40cm.\r\n- Dây kéo bên hông , dáng ngắn có quần bảo hộ bên trong.\r\n------------------------------------------\r\n\r\nLƯU Ý: \r\n- Do sự khác nhau giữa các màn hình khác nhau, hình ảnh có thể không phản ánh được màu sắc thực tế của sản phẩm.\r\n\r\n- Hình không phải là kích thước thực tế của mặt hàng, Nó là có thể lớn hơn so với thực tế, nên Đọc các mô tả, kích thước và số lượng cẩn thận để tránh nhầm lẫn !\r\n\r\n- Mỗi máy tính sẽ hiển thị màu sắc khác nhau, màu sắc của thực tế của mặt hàng có thể thay đổi chút ít so với hình ảnh trên. Cảm ơn vì đã hiểu cho chúng tôi.\r\n\r\nHỗ trợ đổi trả theo quy định của Shopee \r\n1. Trường hợp được chấp nhận: \r\n- Quay lại video khui hàng.\r\n2. Trường hợp không đủ điều kiện áp dụng chính sách: \r\n- Không có video khui hàng\r\n- Gửi lại hàng không đúng mẫu mã, không phải sản phẩm của THIÊN YẾT\r\n- Không thích, không hợp, đặt nhầm mã, nhầm màu. ( NÀY TUYỆT ĐỐI KHÔNG NHA). \r\n------------------------------------------\r\n\r\nDưới đây là các bước để sử dụng mã giảm giá bạn đã lưu cho đơn hàng của bạn : \r\n - Bước 1: Bấm vào sp chọn đặt hàng, nhấn vào ô “Mã giảm giá\" ở phía cuối cùng\r\n - Bước 2: Nhập mã giảm giá bạn muốn áp dụng vào ô và nhấn Áp dụng \r\n- Bước 3: Chọn mã giảm giá & mã miễn phí vận chuyển và nhấn \"OK\". \r\n\r\nBạn có thể chọn tối đa 1 Mã giảm giá và 1 Mã miễn phí vận chuyển cho đơn. \r\n------------------------------------------\r\n\r\n❗ Những lưu ý cần thiết để giữ độ bền về chất lượng và màu sắc:\r\n▪️ Giặt ở nhiệt độ bình thường, với đồ có màu tương tự.\r\n▪️ Hạn chế sử dụng máy sấy và ủi (nếu có) thì ở nhiệt độ thích hợp.\r\n▪️ Không sử dụng chất tẩy rửa mạnh.\r\n----------------------------------------\r\n???? THIÊN YẾT SỈ ( = lẻ ) \r\n', 8, 1, 0, 0.00, 0),
(21, 'Áo Thun Basketball Jersey Số 23 ', 'top', 250000, NULL, '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"street\"]', '[\"oversized\"]', '[\"hot\",\"mild\"]', '2026-03-19 07:02:20', '25', '', '- Chất liệu: Vải lưới thể thao\r\n- Kỹ Thuật: In lụa', 6, 1, 0, 0.00, 0),
(24, 'Áo polo hoa cúc', 'top', 0, NULL, '[\"male\"]', '[\"study\",\"goout\"]', '[\"basic\"]', '[\"regular\"]', '[\"mild\",\"cold\"]', '2026-03-19 08:13:45', NULL, NULL, NULL, 3, 0, 0, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `outfit_colors`
--

CREATE TABLE `outfit_colors` (
  `id` int(11) NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `color_name` varchar(50) NOT NULL COMMENT 'Tên màu (VD: Đen, Trắng)',
  `image` text NOT NULL COMMENT 'Đường dẫn ảnh riêng của màu này'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outfit_colors`
--

INSERT INTO `outfit_colors` (`id`, `outfit_id`, `color_name`, `image`) VALUES
(1, 1, 'Đen', 'assets/img/1773591237_outfit_1_color_1.webp'),
(2, 1, 'Trắng', 'assets/img/1773591237_outfit_1_color_2.webp'),
(3, 1, 'Xám', 'assets/img/1773591237_outfit_1_color_3.webp'),
(4, 1, 'Kaki', 'assets/img/1773591237_outfit_1_color_4.webp'),
(6, 3, 'Xanh dương', 'assets/img/1773675502_outfit_3_color_1.jpg'),
(7, 4, 'Đen', 'assets/img/1773688867_outfit_4_color_1.webp'),
(8, 4, 'Xám', 'assets/img/1773688867_outfit_4_color_2.webp'),
(11, 2, 'Trắng', 'assets/img/1773753186_outfit_2_color_1.avif'),
(13, 8, 'Đen', 'assets/img/1773767357_outfit_8_color_1.webp'),
(14, 9, 'Đen', 'assets/img/1773767727_outfit_9_color_1.webp'),
(15, 9, 'Xanh rêu', 'assets/img/1773767727_outfit_9_color_2.webp'),
(16, 9, 'Nâu', 'assets/img/1773767727_outfit_9_color_3.webp'),
(17, 9, 'Kem', 'assets/img/1773767727_outfit_9_color_4.webp'),
(18, 10, 'Đen', 'assets/img/1773768226_outfit_10_color_1.webp'),
(19, 10, 'Xanh đậm', 'assets/img/1773768226_outfit_10_color_2.webp'),
(20, 11, 'Đen', 'assets/img/1773777084_outfit_11_color_1.webp'),
(21, 11, 'Xanh đậm', 'assets/img/1773777084_outfit_11_color_2.webp'),
(22, 12, 'hồng', 'assets/img/1773785844_outfit_12_color_1.png'),
(28, 13, 'đen', 'assets/img/1773787206_outfit_13_color_1.png'),
(29, 13, 'trắng', 'assets/img/1773787206_outfit_13_color_2.png'),
(30, 13, 'nâu ', 'assets/img/1773787206_outfit_13_color_3.png'),
(31, 14, 'đen', 'assets/img/1773787278_outfit_14_color_1.png'),
(32, 14, 'trắng', 'assets/img/1773787278_outfit_14_color_2.png'),
(33, 15, 'vàng', 'assets/img/1773788014_outfit_15_color_1.png'),
(34, 15, 'tím', 'assets/img/1773788014_outfit_15_color_2.png'),
(35, 15, 'hồng', 'assets/img/1773788014_outfit_15_color_3.png'),
(36, 15, 'xanh dương', 'assets/img/1773788014_outfit_15_color_4.png'),
(37, 16, 'Trắng', 'assets/img/1773839247_outfit_16_color_1.png'),
(38, 16, 'Đen', 'assets/img/1773839247_outfit_16_color_2.png'),
(39, 16, 'Xám', 'assets/img/1773839247_outfit_16_color_3.png'),
(45, 24, 'Xanh dương đậm', 'assets/img/outfits/1773908025_user_3_closet.webp'),
(48, 21, 'Đen', 'assets/img/1773910907_outfit_21_color_1.webp'),
(49, 21, 'Trắng', 'assets/img/1773910907_outfit_21_color_2.webp');

-- --------------------------------------------------------

--
-- Table structure for table `outfit_requests`
--

CREATE TABLE `outfit_requests` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL COMMENT 'Dùng để nhận diện user nếu chưa đăng nhập',
  `occasion` varchar(50) NOT NULL COMMENT 'Dịp: study, goout, date',
  `gender` varchar(20) NOT NULL COMMENT 'Giới tính: male, female',
  `age` int(11) NOT NULL COMMENT 'Độ tuổi',
  `style` varchar(50) NOT NULL COMMENT 'Phong cách: basic, street, vintage',
  `color` varchar(50) NOT NULL COMMENT 'Màu sắc: dark, light, colorful, pastel, neutral',
  `fit` varchar(50) NOT NULL COMMENT 'Độ rộng: oversized, regular, slim',
  `note` text DEFAULT NULL COMMENT 'Ghi chú thêm cho AI',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outfit_requests`
--

INSERT INTO `outfit_requests` (`id`, `session_id`, `occasion`, `gender`, `age`, `style`, `color`, `fit`, `note`, `created_at`) VALUES
(1, 'r5ovoqrmdqoenhkiin2vpim0h0', 'study', 'male', 12, 'basic', 'dark', 'oversized', '', '2026-03-14 20:03:19'),
(2, 'r5ovoqrmdqoenhkiin2vpim0h0', 'study', 'male', 22, 'basic', 'dark', 'oversized', 'tôi muốn mặc houddi', '2026-03-14 20:05:01');

-- --------------------------------------------------------

--
-- Table structure for table `outfit_sizes`
--

CREATE TABLE `outfit_sizes` (
  `id` int(11) NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `color_id` int(11) DEFAULT NULL,
  `size_name` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outfit_sizes`
--

INSERT INTO `outfit_sizes` (`id`, `outfit_id`, `color_id`, `size_name`, `quantity`) VALUES
(1, 1, 1, 'S', 5),
(2, 1, 1, 'M', 5),
(3, 1, 2, 'XL', 10),
(4, 1, 2, 'M', 4),
(5, 1, 3, 'XL', 5),
(6, 1, 3, 'S', 2),
(7, 1, 4, 'M', 3),
(8, 1, 4, 'L', 8),
(14, 3, 6, 'XL', 9),
(15, 3, 6, 'XXL', 31),
(16, 4, 7, 'S', 5),
(17, 4, 7, 'L', 5),
(18, 4, 8, 'M', 2),
(19, 4, 8, 'XL', 10),
(25, 2, 11, '38', 12),
(26, 2, 11, '39', 5),
(27, 2, 11, '44', 10),
(28, 8, 13, 'S', 25),
(29, 8, 13, 'M', 30),
(30, 8, 13, 'L', 40),
(31, 8, 13, 'XL', 15),
(32, 9, 14, 'S', 10),
(33, 9, 14, 'M', 32),
(34, 9, 14, 'L', 15),
(35, 9, 15, 'S', 12),
(36, 9, 15, 'M', 20),
(37, 9, 15, 'L', 32),
(38, 9, 16, 'S', 11),
(39, 9, 16, 'M', 25),
(40, 9, 16, 'L', 16),
(41, 9, 17, 'S', 15),
(42, 9, 17, 'M', 23),
(43, 9, 17, 'L', 36),
(44, 10, 18, 'S', 12),
(45, 10, 18, 'M', 9),
(46, 10, 18, 'L', 19),
(47, 10, 19, 'S', 16),
(48, 10, 19, 'M', 22),
(49, 10, 19, 'L', 35),
(50, 11, 20, 'S', 20),
(51, 11, 20, 'M', 31),
(52, 11, 20, 'L', 42),
(53, 11, 21, 'S', 34),
(54, 11, 21, 'M', 25),
(55, 11, 21, 'L', 36),
(56, 12, 22, 'XL', 232),
(57, 12, 22, 'L', 250),
(71, 13, 28, 'XL', 231),
(72, 13, 28, 'L', 150),
(73, 13, 28, 'M', 45),
(74, 13, 29, 'L', 250),
(75, 13, 29, 'M', 45),
(76, 13, 29, 'XL', 132),
(77, 13, 30, 'XL', 200),
(78, 13, 30, 'M', 120),
(79, 14, 31, '41', 223),
(80, 14, 31, '40', 125),
(81, 14, 31, '39', 821),
(82, 14, 32, '38', 283),
(83, 14, 32, '39', 821),
(84, 15, 33, 'XL', 223),
(85, 15, 33, 'L', 250),
(86, 15, 33, 'M', 45),
(87, 15, 34, 'L', 250),
(88, 15, 34, 'M', 45),
(89, 15, 35, 'M', 45),
(90, 15, 35, 'XL', 200),
(91, 15, 35, 'L', 123),
(92, 15, 36, 'XL', 132),
(93, 15, 36, 'L', 311),
(94, 15, 36, 'M', 234),
(95, 16, 37, 'S', 421),
(96, 16, 37, 'M', 239),
(97, 16, 37, 'L', 140),
(98, 16, 38, 'S', 1234),
(99, 16, 38, 'M', 455),
(100, 16, 38, 'L', 213),
(101, 16, 39, 'M', 43),
(102, 16, 39, 'S', 252),
(103, 16, 39, 'L', 221),
(113, 24, 45, 'Mặc định', 1),
(116, 21, 48, 'S', 25),
(117, 21, 48, 'M', 48),
(118, 21, 48, 'L', 27),
(119, 21, 49, 'S', 30),
(120, 21, 49, 'M', 24),
(121, 21, 49, 'L', 9);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Đơn hàng nào sinh ra đánh giá này',
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_outfits`
--

CREATE TABLE `saved_outfits` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `onepiece_id` int(11) DEFAULT NULL,
  `top_id` int(11) DEFAULT NULL,
  `bottom_id` int(11) DEFAULT NULL,
  `shoes_id` int(11) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `style_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_outfits`
--

INSERT INTO `saved_outfits` (`id`, `user_id`, `onepiece_id`, `top_id`, `bottom_id`, `shoes_id`, `acc_id`, `style_name`, `created_at`) VALUES
(6, 1, NULL, 1, 3, 2, NULL, 'Năng động & Tối giản', '2026-03-16 15:38:55'),
(14, 2, NULL, 1, 3, 2, NULL, 'Modern Streetwear', '2026-03-16 20:41:11');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `size_name` varchar(10) NOT NULL,
  `color_name` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shopping_cart`
--

INSERT INTO `shopping_cart` (`id`, `user_id`, `outfit_id`, `size_name`, `color_name`, `quantity`, `created_at`) VALUES
(15, 1, 10, 'M', 'Đen', 1, '2026-03-17 21:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('customer','support','sales','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `fullname`, `phone`, `age`, `gender`, `address`, `email`, `avatar`, `password`, `created_at`, `role`) VALUES
(1, 'MThanh', NULL, NULL, NULL, NULL, NULL, 'thanh@gmail.com', NULL, '$2y$10$IJJng2c3k/O83qTiOBZb0OM5DalqvS/f6iJCK0z2zfJJ3ogzbe/Py', '2026-03-09 19:59:55', 'admin'),
(2, 'Itogy', 'Phan Lê Trung Cương', '0908377239', 20, 'male', 'Thủ đức, TPHCM', 'trungcuong.2006tn@gmail.com', 'assets/img/avatars/1773684862_5498.jpg', '$2y$10$2hgl7uE45ig.520550dmzeLX97Cc4fSpKHhJtpbPizTtO3VHr48CG', '2026-03-15 13:57:42', 'admin'),
(3, 'user1', NULL, NULL, NULL, NULL, NULL, 'user1@gmail.com', NULL, '$2y$10$lAeAFlAfpk1mM4GduJyJd.b8HbrKGfnXQ0MZ8r/i2fYUfq8qQATLq', '2026-03-16 19:22:20', 'customer'),
(4, 'sale1', 'Quần áo Vui Vẻ', '123456789', 6, '', '', 'sale1@gmail.com', 'assets/img/avatars/1773777521_9507.jpg', '$2y$10$a2BX8aQj8RWckvxsr6ylEeIE/ZxsXRIJrAjLZZvefo.b6BNlXwZMu', '2026-03-16 19:23:10', 'sales'),
(5, 'Nguyễn Minh Thành', 'Nguyễn Minh Thành', '0971996942', 19, 'male', 'ấp thới thuận', '9z3a5z7a@gmail.com', 'assets/img/avatars/1773838647_7667.webp', '$2y$10$Ke81Nbxtem2sTlnAjamOY.tSEbVoOKZGS5I3sEbI0e20wk/EIfWHO', '2026-03-17 10:08:56', 'admin'),
(6, 'sale2', 'Quần áo Tuổi Trẻ', '', 20, '', '', 'sale2@gmail.com', NULL, '$2y$10$ACRQE3wda5n8RMupMUjdY.3i/tsMAtS1C85jBhF9mpFrne.DiXYsa', '2026-03-17 20:06:18', 'sales'),
(8, 'Hoa', 'Hoa', '', 6, '', '', 'hoa@gmail.com', 'assets/img/avatars/1773838804_2386.png', '$2y$10$EuWjaHqYDiRrJazYOfo2eOCEul1StAi2/coNDGrldhi5sg4scAtJi', '2026-03-17 22:12:34', 'sales'),
(9, 'Trung', NULL, NULL, NULL, NULL, NULL, 'trung@gmail.com', NULL, '$2y$10$tk5zYpZoSYBbF3zBgzGdD.tSYbuX8iGBpQOACkQv1nqh3WgPJVMf.', '2026-03-17 22:42:15', 'sales'),
(20, 'Thành', NULL, NULL, NULL, NULL, NULL, '9z5a5z7a@gmail.com', NULL, '$2y$10$r/YmKTLAqhZ56Y31ik/ISuYCc1eRqzQmiTFmdA0QafyawucODw0mu', '2026-03-19 16:50:34', 'customer'),
(21, 'Thành', NULL, NULL, NULL, NULL, NULL, 'nguyenminhthanh043216@gmail.com', NULL, '$2y$10$X3CP1FNvWLIPza3x6mygsem9PFcMqrIU7/iTC6.VPQ51W6cBwezi.', '2026-03-19 17:00:09', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`),
  ADD KEY `fk_order_shop` (`shop_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_order` (`order_id`),
  ADD KEY `fk_detail_outfit` (`outfit_id`);

--
-- Indexes for table `outfits`
--
ALTER TABLE `outfits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_outfit_owner` (`owner_id`);

--
-- Indexes for table `outfit_colors`
--
ALTER TABLE `outfit_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_outfit_color` (`outfit_id`);

--
-- Indexes for table `outfit_requests`
--
ALTER TABLE `outfit_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outfit_sizes`
--
ALTER TABLE `outfit_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `outfit_id` (`outfit_id`),
  ADD KEY `fk_size_color` (`color_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_review_user` (`user_id`),
  ADD KEY `fk_review_outfit` (`outfit_id`),
  ADD KEY `fk_review_order` (`order_id`);

--
-- Indexes for table `saved_outfits`
--
ALTER TABLE `saved_outfits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_saved_user` (`user_id`),
  ADD KEY `fk_saved_top` (`top_id`),
  ADD KEY `fk_saved_bottom` (`bottom_id`),
  ADD KEY `fk_saved_shoes` (`shoes_id`),
  ADD KEY `fk_saved_acc` (`acc_id`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_outfit` (`outfit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20260319180055136;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `outfits`
--
ALTER TABLE `outfits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `outfit_colors`
--
ALTER TABLE `outfit_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `outfit_requests`
--
ALTER TABLE `outfit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `outfit_sizes`
--
ALTER TABLE `outfit_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saved_outfits`
--
ALTER TABLE `saved_outfits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_shop` FOREIGN KEY (`shop_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_detail_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_outfit` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`);

--
-- Constraints for table `outfits`
--
ALTER TABLE `outfits`
  ADD CONSTRAINT `fk_outfit_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `outfit_colors`
--
ALTER TABLE `outfit_colors`
  ADD CONSTRAINT `fk_outfit_color` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `outfit_sizes`
--
ALTER TABLE `outfit_sizes`
  ADD CONSTRAINT `fk_outfit_sizes` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_size_color` FOREIGN KEY (`color_id`) REFERENCES `outfit_colors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_outfit` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_outfits`
--
ALTER TABLE `saved_outfits`
  ADD CONSTRAINT `fk_saved_acc` FOREIGN KEY (`acc_id`) REFERENCES `outfits` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_saved_bottom` FOREIGN KEY (`bottom_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_saved_shoes` FOREIGN KEY (`shoes_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_saved_top` FOREIGN KEY (`top_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_saved_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `fk_cart_outfit` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
