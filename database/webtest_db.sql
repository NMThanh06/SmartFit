-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 03:18 PM
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
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `note` text DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'cod',
  `total_amount` int(11) NOT NULL,
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `phone`, `address`, `note`, `payment_method`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nguyễn Minh Thành', '0971996900', 'ấp thới thuận', 'dahfk a', 'vnpay', 1747000, 'pending', '2026-03-10 07:39:18', '2026-03-10 07:39:18'),
(2, 1, 'g', '0971996942', 'ấp thới thuận', 'cho cuong', 'momo', 560000, 'pending', '2026-03-10 07:40:54', '2026-03-10 07:40:54'),
(3, 1, 'g', '0971996942', 'ấp thới thuận', '', 'cod', 2692000, 'pending', '2026-03-11 08:38:48', '2026-03-11 08:38:48');

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
(1, 1, 9, '40', 2, 250000),
(2, 1, 8, '40', 1, 250000),
(3, 1, 8, '39', 1, 250000),
(4, 1, 10, 'Oversize', 2, 199000),
(5, 1, 5, 'M', 1, 349000),
(6, 2, 7, 'M', 1, 200000),
(7, 2, 3, 'M', 2, 180000),
(8, 3, 10, 'OVER', 8, 199000),
(9, 3, 7, 'M', 1, 200000),
(10, 3, 1, 'M', 1, 150000),
(11, 3, 9, '39', 2, 250000),
(12, 3, 9, '41', 1, 250000);

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
  `color` varchar(50) DEFAULT NULL,
  `gender` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gender`)),
  `occasion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`occasion`)),
  `style` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`style`)),
  `fit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fit`)),
  `weather` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`weather`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outfits`
--

INSERT INTO `outfits` (`id`, `name`, `type`, `price`, `image`, `color`, `gender`, `occasion`, `style`, `fit`, `weather`, `created_at`) VALUES
(1, 'Áo thun nam', 'top', 150000, '/SmartFit/assets/img/default-top.jpg', 'light', '[\"male\"]', '[\"study\",\"goout\"]', '[\"basic\"]', '[\"regular\"]', '[\"hot\",\"mild\"]', '2026-03-09 20:02:30'),
(2, 'Quần jean nam', 'bottom', 250000, '/SmartFit/assets/img/default-bottom.jpg', 'dark', '[\"male\"]', '[\"study\",\"goout\"]', '[\"basic\"]', '[\"regular\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-09 20:02:30'),
(3, 'Áo crop top Nữ ', 'top', 180000, '/SmartFit/assets/img/female-default-top.jpg', 'light', '[\"female\"]', '[\"study\",\"goout\",\"date\"]', '[\"basic\"]', '[\"regular\"]', '[\"hot\",\"mild\"]', '2026-03-09 20:02:30'),
(4, 'Quần tây nữ', 'bottom', 200000, '/SmartFit/assets/img/female-default-bottom.jpg', 'dark', '[\"female\"]', '[\"study\",\"date\"]', '[\"basic\"]', '[\"regular\"]', '[\"hot\",\"mild\"]', '2026-03-09 20:02:30'),
(5, 'Sneaker', 'shoes', 349000, '/SmartFit/assets/img/1772901133_8504.png', 'light', '[\"male\"]', '[\"study\",\"goout\"]', '[\"street\"]', '[]', '[\"hot\",\"mild\"]', '2026-03-09 20:02:30'),
(6, 'Áo thun đen cổ tròn', 'top', 250000, '/SmartFit/assets/img/1772550289_1175.jpeg', 'dark', '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"basic\"]', '[\"oversized\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-09 20:02:30'),
(7, 'Áo hoodie đen', 'top', 200000, '/SmartFit/assets/img/1772836189_1435.jpg', 'dark', '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"basic\",\"street\"]', '[\"oversized\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-09 20:02:30'),
(8, 'Giày sneaker trắng', 'shoes', 250000, '/SmartFit/assets/img/1772898800_5543.jpg', 'neutral', '[\"male\",\"female\"]', '[\"study\",\"goout\"]', '[\"basic\",\"street\"]', '[\"regular\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-09 20:02:30'),
(9, 'Giày nữ trắng', 'shoes', 250000, '/SmartFit/assets/img/1772900681_2634.png', 'pastel', '[\"female\"]', '[\"study\",\"goout\"]', '[\"basic\",\"street\"]', '[\"regular\",\"oversized\",\"slim\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-09 20:02:30'),
(10, 'Kính mát', 'accessories', 199000, '/SmartFit/assets/img/1772900816_6167.png', 'dark', '[\"male\",\"female\"]', '[\"study\",\"goout\",\"date\"]', '[\"basic\",\"street\",\"vintage\"]', '[\"regular\",\"oversized\",\"slim\"]', '[\"hot\",\"mild\",\"cold\"]', '2026-03-09 20:02:30');

-- --------------------------------------------------------

--
-- Table structure for table `outfit_pairings`
--

CREATE TABLE `outfit_pairings` (
  `id` int(11) NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `pairing_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outfit_pairings`
--

INSERT INTO `outfit_pairings` (`id`, `outfit_id`, `pairing_name`) VALUES
(1, 5, 'Quần tây nam âu phục');

-- --------------------------------------------------------

--
-- Table structure for table `outfit_sizes`
--

CREATE TABLE `outfit_sizes` (
  `id` int(11) NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `size_name` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `outfit_sizes`
--

INSERT INTO `outfit_sizes` (`id`, `outfit_id`, `size_name`, `quantity`) VALUES
(1, 1, 'S', 10),
(2, 1, 'M', 19),
(3, 1, 'L', 15),
(4, 1, 'XL', 5),
(5, 2, 'S', 5),
(6, 2, 'M', 10),
(7, 2, 'L', 10),
(8, 2, 'XL', 2),
(9, 3, 'S', 15),
(10, 3, 'M', 10),
(11, 3, 'L', 5),
(12, 4, 'S', 10),
(13, 4, 'M', 12),
(14, 4, 'L', 8),
(15, 5, '39', 5),
(16, 5, '40', 10),
(17, 5, '41', 15),
(18, 5, '42', 8),
(19, 6, 'M', 1),
(20, 7, 'M', 14),
(21, 7, 'XL', 20),
(22, 7, 'XXL', 5),
(23, 8, '41', 10),
(24, 8, '42', 15),
(25, 8, '40', 20),
(26, 9, '41', 14),
(27, 9, '42', 15),
(28, 9, '39', 18),
(29, 9, '40', 5),
(30, 10, 'OVER', 2);

-- --------------------------------------------------------

--
-- Table structure for table `saved_outfits`
--

CREATE TABLE `saved_outfits` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `top_id` int(11) NOT NULL,
  `bottom_id` int(11) NOT NULL,
  `shoes_id` int(11) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `style_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `outfit_id` int(11) NOT NULL,
  `size_name` varchar(10) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'MThanh', 'thanh@gmail.com', '$2y$10$IJJng2c3k/O83qTiOBZb0OM5DalqvS/f6iJCK0z2zfJJ3ogzbe/Py', '2026-03-09 19:59:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outfit_pairings`
--
ALTER TABLE `outfit_pairings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `outfit_id` (`outfit_id`);

--
-- Indexes for table `outfit_sizes`
--
ALTER TABLE `outfit_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `outfit_id` (`outfit_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `outfits`
--
ALTER TABLE `outfits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `outfit_pairings`
--
ALTER TABLE `outfit_pairings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `outfit_sizes`
--
ALTER TABLE `outfit_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `saved_outfits`
--
ALTER TABLE `saved_outfits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_detail_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_outfit` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`);

--
-- Constraints for table `outfit_pairings`
--
ALTER TABLE `outfit_pairings`
  ADD CONSTRAINT `fk_outfit_conflicts` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `outfit_sizes`
--
ALTER TABLE `outfit_sizes`
  ADD CONSTRAINT `fk_outfit_sizes` FOREIGN KEY (`outfit_id`) REFERENCES `outfits` (`id`) ON DELETE CASCADE;

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
