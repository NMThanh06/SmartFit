-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 07:01 AM
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
(4, 1, NULL, 'Nguyễn Minh Thành', '0971996942', 'ấp thới thuận', '', 'vnpay', 'failed', NULL, 2200000, 'pending', '2026-03-16 15:15:31', '2026-03-16 15:16:41'),
(5, 1, NULL, 'Nguyễn Minh Thành', '0971996942', 'ấp thới thuận', '', 'vnpay', 'success', NULL, 1200000, 'pending', '2026-03-16 15:31:47', '2026-03-16 15:34:28'),
(6, 1, NULL, 'Nguyễn Minh Thành', '0971996942', 'ấp thới thuận', '', 'vnpay', 'success', NULL, 398000, 'pending', '2026-03-16 15:40:46', '2026-03-16 15:41:34'),
(7, 1, NULL, 'Nguyễn Minh Thành', '0971996942', 'ấp thới thuận', '', 'cod', 'pending', NULL, 199000, 'pending', '2026-03-16 15:45:43', '2026-03-16 15:45:43'),
(8, 2, NULL, 'Phan Lê Trung Cương', '0908377239', 'thủ đức', '', 'cod', 'pending', NULL, 199000, 'pending', '2026-03-16 16:03:46', '2026-03-16 16:03:46'),
(9, 2, NULL, 'Phan Lê Trung Cương', '0908377239', 'Thủ đức, TPHCM', '', 'vnpay', 'pending', NULL, 199000, 'pending', '2026-03-16 17:07:19', '2026-03-16 17:07:19'),
(10, 2, NULL, 'Phan Lê Trung Cương', '0908377239', '176 đường Nguyễn Văn ABC phường Hòa CDE kp Tăng OPD thành phố hcm', '', 'cod', 'pending', NULL, 1000000, 'completed', '2026-03-16 17:17:17', '2026-03-16 19:31:40'),
(11, 2, 4, 'Phan Lê Trung Cương', '0908377239', 'Thủ đức, TPHCM', '', 'cod', 'pending', NULL, 400000, 'pending', '2026-03-17 21:04:32', '2026-03-17 21:04:32'),
(20260317230245663, 7, 4, 'NMT', '0877905490', 'ấp thới thuận', '', 'cod', 'pending', NULL, 350000, 'pending', '2026-03-17 22:02:45', '2026-03-17 22:02:45');

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
(13, 4, 2, '38', 1, 1000000),
(14, 4, 1, 'M', 1, 200000),
(15, 4, 2, '39', 1, 1000000),
(16, 5, 2, '40', 1, 1000000),
(17, 5, 1, 'M', 1, 200000),
(18, 6, 3, 'XL', 2, 199000),
(19, 7, 3, 'XXL', 1, 199000),
(20, 8, 3, 'XL', 1, 199000),
(21, 9, 3, 'XXL', 1, 199000),
(22, 10, 2, '39', 1, 1000000),
(23, 11, 11, 'M', 1, 400000),
(27, 20260317230245663, 10, 'L', 1, 350000);

-- --------------------------------------------------------

--
-- Table structure for table `outfits`
--
-- Error reading structure for table webtest_db.outfits: #1030 - Got error 194 &quot;Tablespace is missing for a table&quot; from storage engine InnoDB
-- Error reading data for table webtest_db.outfits: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `webtest_db`.`outfits`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `outfit_colors`
--
-- Error reading structure for table webtest_db.outfit_colors: #1030 - Got error 194 &quot;Tablespace is missing for a table&quot; from storage engine InnoDB
-- Error reading data for table webtest_db.outfit_colors: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `webtest_db`.`outfit_colors`&#039; at line 1

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
-- Error reading structure for table webtest_db.outfit_sizes: #1030 - Got error 194 &quot;Tablespace is missing for a table&quot; from storage engine InnoDB
-- Error reading data for table webtest_db.outfit_sizes: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `webtest_db`.`outfit_sizes`&#039; at line 1

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
(14, 2, NULL, 1, 3, 2, NULL, 'Modern Streetwear', '2026-03-16 20:41:11'),
(15, 2, NULL, 7, 10, 2, NULL, 'Smart-Casual Date', '2026-03-17 21:08:34');

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
(5, 'Nguyễn Minh Thành', 'Nguyễn Minh Thành', '0971996942', 19, 'male', 'ấp thới thuận', '9z3a5z7a@gmail.com', NULL, '$2y$10$Ke81Nbxtem2sTlnAjamOY.tSEbVoOKZGS5I3sEbI0e20wk/EIfWHO', '2026-03-17 10:08:56', 'admin'),
(6, 'sale2', NULL, NULL, NULL, NULL, NULL, 'sale2@gmail.com', NULL, '$2y$10$ACRQE3wda5n8RMupMUjdY.3i/tsMAtS1C85jBhF9mpFrne.DiXYsa', '2026-03-17 20:06:18', 'sales'),
(7, 'NMT', NULL, NULL, NULL, NULL, NULL, 'nguyenminhthanh043216@gmail.com', NULL, '$2y$10$BUTVNvaIPWwA6AfSbdU2S.Jngm8lfj7csL9Y4R.tND/hxQb7QmAAK', '2026-03-17 21:53:31', 'admin'),
(8, 'Hoa', 'Hoa', '', 6, '', '', 'hoa@gmail.com', NULL, '$2y$10$EuWjaHqYDiRrJazYOfo2eOCEul1StAi2/coNDGrldhi5sg4scAtJi', '2026-03-17 22:12:34', 'sales'),
(9, 'Trung', NULL, NULL, NULL, NULL, NULL, 'trung@gmail.com', NULL, '$2y$10$tk5zYpZoSYBbF3zBgzGdD.tSYbuX8iGBpQOACkQv1nqh3WgPJVMf.', '2026-03-17 22:42:15', 'sales');

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
-- Indexes for table `outfit_requests`
--
ALTER TABLE `outfit_requests`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20260317230245664;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `outfit_requests`
--
ALTER TABLE `outfit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `saved_outfits`
--
ALTER TABLE `saved_outfits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
