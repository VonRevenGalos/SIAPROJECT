-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 23, 2025 at 12:28 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u585057361_shoe`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail1` varchar(255) DEFAULT NULL,
  `thumbnail2` varchar(255) DEFAULT NULL,
  `thumbnail3` varchar(255) DEFAULT NULL,
  `color` enum('Black','Blue','Brown','Green','Gray','Multi-Colour','Orange','Pink','Purple','Red','White','Yellow') DEFAULT 'Black',
  `height` enum('low top','mid top','high top') DEFAULT 'mid top',
  `width` enum('regular','wide','extra wide') DEFAULT 'regular',
  `brand` varchar(100) DEFAULT 'Generic',
  `collection` varchar(100) DEFAULT 'Standard',
  `date_added` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `price`, `stock`, `image`, `category`, `description`, `thumbnail1`, `thumbnail2`, `thumbnail3`, `color`, `height`, `width`, `brand`, `collection`, `date_added`) VALUES
(1, 'Aero Strides', 1999.00, 3, 'assets/img/men/sneakers/aerostride.png', 'sneakers', 'AeroStrides offers superior comfort and support for all-day wear, perfect for casual outings.', 'assets/img/men/sneakers/aerostride.png', 'assets/img/men/sneakers/aerostride1.png', 'assets/img/men/sneakers/aerostride2.png', 'Multi-Colour', 'high top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 16:11:18'),
(2, 'Momentum', 1599.00, 13, 'assets/img/men/sneakers/momentum.png', 'sneakers', 'Momentum provide a lightweight design with excellent ventilation for ultimate comfort during your daily activities.', 'assets/img/men/sneakers/momentum.png', 'assets/img/men/sneakers/momentum1.png', 'assets/img/men/sneakers/momentum2.png', 'Black', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(3, 'PowerMove Ultra', 1899.00, 10, 'assets/img/men/sneakers/powermoveultra.png\r\n', 'sneakers', 'PowerMove shoes blend style and functionality for those who demand performance without sacrificing looks.', 'assets/img/men/sneakers/powermoveultra.png\r\n', 'assets/img/men/sneakers/powermoveultra1.png\r\n', 'assets/img/men/sneakers/powermoveultra2.png\r\n', 'Brown', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(4, 'Sneak Force', 999.00, 19, 'assets/img/men/sneakers/sneakforce.png\r\n', 'sneakers', 'SneakForce shoes are designed with plush cushioning and a soft upper for a luxurious feel on your feet.', 'assets/img/men/sneakers/sneakforce.png\r\n', 'assets/img/men/sneakers/sneakforce1.png\r\n', 'assets/img/men/sneakers/sneakforce2.png\r\n', 'Brown', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(5, 'Stride Master', 2999.00, 20, 'assets/img/men/sneakers/stridemaster.png\r\n', 'sneakers', 'Stride Master shoes offer a flexible design that adapts to your foot movement, making them ideal for active lifestyles.', 'assets/img/men/sneakers/stridemaster.png\r\n', 'assets/img/men/sneakers/stridemaster1.png\r\n', 'assets/img/men/sneakers/stridemaster2.png\r\n', 'Multi-Colour', 'high top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 16:11:18'),
(6, 'Endurance Pro X', 4599.00, 6, 'assets/img/men/running/endurancepro-x.png\r\n', 'running', 'Endurance provide a stylish yet comfortable option for everyday use, with a focus on breathability and durability.', 'assets/img/men/running/endurancepro-x.png', 'assets/img/men/running/endurancepro-x1.png', 'assets/img/men/running/endurancepro-x2.png', 'Black', 'mid top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 16:11:18'),
(7, 'RunTech', 5999.00, 10, 'assets/img/men/running/runtech.png\r\n', 'running', 'Runtech running shoes are engineered for speed, providing lightweight support that helps you go the distance.', 'assets/img/men/running/runtech.png\r\n', 'assets/img/men/running/runtech1.png\r\n', 'assets/img/men/running/runtech2.png\r\n', 'Brown', 'mid top', 'regular', 'Generic', 'Air Rizz', '2025-09-18 16:11:18'),
(8, 'Speed Flex', 1599.00, 7, 'assets/img/men/running/speedflex.png\r\n', 'running', 'SpeedFlex shoes are built for runners who want a responsive feel and excellent traction on any surface.', 'assets/img/men/running/speedflex.png\r\n', 'assets/img/men/running/speedflex1.png\r\n', 'assets/img/men/running/speedflex2.png\r\n', 'White', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(9, 'Swift Step Max', 3999.00, 17, 'assets/img/men/running/swiftstepmax.png', 'running', 'SwiftStep offers a unique design with advanced cushioning technology, ensuring a smooth and comfortable run.', 'assets/img/men/running/swiftstepmax.png', 'assets/img/men/running/swiftstepmax1.png', 'assets/img/men/running/swiftstepmax2.png', 'Green', 'mid top', 'wide', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(10, 'Velocity Runner Pro', 4899.00, 20, 'assets/img/men/running/velocityrunnerpro.png\r\n', 'running', 'Velocity shoes are perfect for competitive runners seeking to improve their performance.', 'assets/img/men/running/velocityrunnerpro.png\r\n', 'assets/img/men/running/velocityrunnerpro1.png\r\n', 'assets/img/men/running/velocityrunnerpro2.png\r\n', 'Black', 'mid top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 16:11:18'),
(11, 'PowerStride\r\n', 5299.00, 4, 'assets/img/men/athletics/powerstride.png\r\n', 'athletics', 'Powerstride shoes are designed for endurance athletes, combining durability with exceptional comfort for long runs.', 'assets/img/men/athletics/powerstride.png\r\n', 'assets/img/men/athletics/powerstride1.png\r\n', 'assets/img/men/athletics/powerstride2.png\r\n', 'Red', 'high top', 'extra wide', 'XRizz', 'Air Rizz', '2025-09-18 16:11:18'),
(12, 'Trackzone Ultra', 6999.00, 10, 'assets/img/men/athletics/trackzoneultra.png\r\n', 'athletics', 'TrackZone shoes provide a snug fit and responsive cushioning, making them perfect for sprinters and fast-paced workouts.', 'assets/img/men/athletics/trackzoneultra.png', 'assets/img/men/athletics/trackzoneultra1.png', 'assets/img/men/athletics/trackzoneultra2.png', 'Red', 'high top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 16:11:18'),
(13, 'Athlo Xtreme', 2599.00, 13, 'assets/img/men/athletics/athloxtreme.png', 'athletics', 'AthloXtreme shoes are engineered for athletes seeking performance and style, offering exceptional grip and support.', 'assets/img/men/athletics/athloxtreme.png', 'assets/img/men/athletics/athloxtreme1.png', 'assets/img/men/athletics/athloxtreme2.png', 'Black', 'mid top', 'wide', 'XRizz', 'Standard', '2025-09-18 16:11:18'),
(14, 'Elite Move', 7599.00, 8, 'assets/img/men/athletics/elitemove.png\n\n', 'athletics', 'EliteMove combines advanced technology with modern design, perfect for serious athletes and fitness enthusiasts.', 'assets/img/men/athletics/elitemove.png\n', 'assets/img/men/athletics/elitemove1.png\n', 'assets/img/men/athletics/elitemove2.png\n', 'Blue', 'low top', 'regular', 'Generic Rizz', 'Standard', '2025-09-18 16:11:18'),
(15, 'MotionFlex Max', 9999.00, 9, 'assets/img/men/athletics/motionflexmax.png\n', 'athletics', 'MotionFlex Max shoes feature innovative cushioning and stability, designed to enhance your athletic performance and comfort.', 'assets/img/men/athletics/motionflexmax.png\n', 'assets/img/men/athletics/motionflexmax1.png\n', 'assets/img/men/athletics/motionflexmax2.png\n', 'Black', 'high top', 'regular', 'XRizz', 'Air Rizz', '2025-09-18 16:11:18'),
(16, 'Athletica X', 2499.00, 4, 'assets/img/women/womenathletics/athleticax.png', 'womenathletics', 'Athletica are designed for active persons offering lightweight comfort and vibrant designs.', 'assets/img/women/womenathletics/athleticax.png', 'assets/img/women/womenathletics/athleticax1.png', 'assets/img/women/womenathletics/athleticax2.png', 'Pink', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(17, 'Core Motion', 1299.00, 11, 'assets/img/women/womenathletics/coremotion.png', 'womenathletics', 'CoreMotion provide a snug fit and are perfect for athletic players.', 'assets/img/women/womenathletics/coremotion.png', 'assets/img/women/womenathletics/coremotion1.png', 'assets/img/women/womenathletics/coremotion2.png', 'Green', 'mid top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(18, 'Flex Fusion', 2299.00, 10, 'assets/img/women/womenathletics/flexfusion.png', 'womenathletics', 'FlexFusion feature rugged soles for adventurous who love outdoor activities.', 'assets/img/women/womenathletics/flexfusion.png', 'assets/img/women/womenathletics/flexfusion1.png', 'assets/img/women/womenathletics/flexfusion2.png', 'White', 'mid top', 'wide', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(19, 'Maxi Step', 2699.00, 17, 'assets/img/women/womenathletics/maxistep.png', 'womenathletics', 'Maxi step offer speed and agility on the go.', 'assets/img/women/womenathletics/maxistep.png', 'assets/img/women/womenathletics/maxistep1.png', 'assets/img/women/womenathletics/maxistep2.png', 'Purple', 'low top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 16:11:18'),
(20, 'Pulse Flex', 3699.00, 20, 'assets/img/women/womenathletics/pulseflex.png', 'womenathletics', 'Maxi step offer speed and agility on the go.', 'assets/img/women/womenathletics/pulseflex.png', 'assets/img/women/womenathletics/pulseflex1.png', 'assets/img/women/womenathletics/pulseflex2.png', 'Red', 'high top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 16:11:18'),
(21, 'Enduro Dash', 1999.00, 17, 'assets/img/women/womenrunning/endurodash.png', 'womenrunning', 'Enduro Dash offer unmatched speed and agility  on the go, with bold colorways.', 'assets/img/women/womenrunning/endurodash.png', 'assets/img/women/womenrunning/endurodash1.png', 'assets/img/women/womenrunning/endurodash2.png', 'Brown', 'mid top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(22, 'PeakRunner', 3999.00, 30, 'assets/img/women/womenrunning/peakrunner.png', 'womenrunning', 'Peak  offer unmatched speed and agility  on the go, with bold colorways.', 'assets/img/women/womenrunning/peakrunner.png', 'assets/img/women/womenrunning/peakrunner1.png', 'assets/img/women/womenrunning/peakrunner2.png', 'Brown', 'mid top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(23, 'Run Wave', 2199.00, 30, 'assets/img/women/womenrunning/runwave.png', 'womenrunning', 'Run Wave', 'assets/img/women/womenrunning/runwave.png', 'assets/img/women/womenrunning/runwave1.png', 'assets/img/women/womenrunning/runwave2.png', 'Black', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 16:11:18'),
(24, 'Velocity Run', 1599.00, 20, 'assets/img/women/womenrunning/velocityrun.png', 'womenrunning', 'VelocityRun', 'assets/img/women/womenrunning/velocityrun.png', 'assets/img/women/womenrunning/velocityrun1.png', 'assets/img/women/womenrunning/velocityrun2.png', 'Pink', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 17:58:45'),
(25, 'Viva Sprint', 1699.00, 30, 'assets/img/women/womenrunning/vivasprint.png', 'womenrunning', 'Viva Sprint', 'assets/img/women/womenrunning/vivasprint.png', 'assets/img/women/womenrunning/vivasprint1.png', 'assets/img/women/womenrunning/vivasprint2.png', 'Red', 'mid top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 18:04:41'),
(26, 'Active Luxe', 7999.00, 21, 'assets/img/women/womensneakers/activeluxe.png', 'womensneakers', 'Active Luxe', 'assets/img/women/womensneakers/activeluxe.png', 'assets/img/women/womensneakers/activeluxe1.png', 'assets/img/women/womensneakers/activeluxe2.png', 'Multi-Colour', 'high top', 'extra wide', 'XRizz', 'Air Rizz', '2025-09-18 18:04:41'),
(27, 'FlexiGlide', 1599.00, 10, 'assets/img/women/womensneakers/flexiglide.png', 'womensneakers', 'FlexiGlide', 'assets/img/women/womensneakers/flexiglide.png', 'assets/img/women/womensneakers/flexiglide1.png', 'assets/img/women/womensneakers/flexiglide2.png', 'Gray', 'mid top', 'wide', 'Generic', 'Standard', '2025-09-18 18:10:12'),
(28, 'Swift Step', 999.00, 10, 'assets/img/women/womensneakers/swiftstep.png', 'womensneakers', '', 'assets/img/women/womensneakers/swiftstep.png', 'assets/img/women/womensneakers/swiftstep1.png', 'assets/img/women/womensneakers/swiftstep2.png', 'Pink', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 18:04:41'),
(29, 'Urban Flow', 1999.00, 10, 'assets/img/women/womensneakers/urbanflow.png', 'womensneakers', '', 'assets/img/women/womensneakers/urbanflow.png', 'assets/img/women/womensneakers/urbanflow1.png', 'assets/img/women/womensneakers/urbanflow2.png', 'Pink', 'mid top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 18:04:41'),
(30, 'Luna Stride', 8999.00, 21, 'assets/img/women/womensneakers/lunastride.png', 'womensneakers', '', 'assets/img/women/womensneakers/lunastride.png', 'assets/img/women/womensneakers/lunastride1.png', 'assets/img/women/womensneakers/lunastride2.png', 'Purple', 'high top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 18:04:41'),
(31, 'Dash', 8999.00, 21, 'assets/img/kids/kidsathletics/dash.jpg', 'kidsathletics', '', 'assets/img/kids/kidsathletics/dash.jpg', 'assets/img/kids/kidsathletics/dash1.jpg', 'assets/img/kids/kidsathletics/dash2.jpg', 'Multi-Colour', 'high top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 18:04:41'),
(32, 'Peak Tots', 1599.00, 10, 'assets/img/kids/kidsathletics/peaktots.jpg', 'kidsathletics', NULL, 'assets/img/kids/kidsathletics/peaktots.jpg', 'assets/img/kids/kidsathletics/peaktots1.jpg', 'assets/img/kids/kidsathletics/peaktots2.jpg', 'Black', 'mid top', 'wide', 'Generic', 'Standard', '2025-09-18 18:18:04'),
(33, 'PowerPaws', 2999.00, 10, 'assets/img/kids/kidsathletics/powerpaws.jpg', 'kidsathletics', NULL, 'assets/img/kids/kidsathletics/powerpaws.jpg', 'assets/img/kids/kidsathletics/powerpaws1.jpg', 'assets/img/kids/kidsathletics/powerpaws2.jpg', 'Gray', 'mid top', 'regular', 'Generic', 'Air Rizz', '2025-09-18 18:18:04'),
(34, 'Vibe Trek', 3999.00, 10, 'assets/img/kids/kidsathletics/vibetrek.jpg', 'kidsathletics', NULL, 'assets/img/kids/kidsathletics/vibetrek.jpg', 'assets/img/kids/kidsathletics/vibetrek1.jpg', 'assets/img/kids/kidsathletics/vibetrek2.jpg', 'Black', 'mid top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 18:18:04'),
(35, 'Vibrant Velocity', 4599.00, 10, 'assets/img/kids/kidsathletics/vibrantvelocity.jpg', 'kidsathletics', NULL, 'assets/img/kids/kidsathletics/vibrantvelocity.jpg', 'assets/img/kids/kidsathletics/vibrantvelocity2.jpg', 'assets/img/kids/kidsathletics/vibrantvelocity1.jpg', 'Blue', 'low top', 'extra wide', 'Generic', 'Standard', '2025-09-18 18:21:53'),
(36, 'Fast Feet', 1999.00, 10, 'assets/img/kids/kidsneakers/fastfeet.png', 'kidsneakers', NULL, 'assets/img/kids/kidsneakers/fastfeet.png', 'assets/img/kids/kidsneakers/fastfeet1.png', 'assets/img/kids/kidsneakers/fastfeet2.png', 'Black', 'low top', 'regular', 'XRizz', 'Standard', '2025-09-18 18:21:53'),
(37, 'Jump Jacks', 2999.00, 10, 'assets/img/kids/kidsneakers/jumpjacks.jpg', 'kidsneakers', NULL, 'assets/img/kids/kidsneakers/jumpjacks.jpg', 'assets/img/kids/kidsneakers/jumpjacks1.jpg', 'assets/img/kids/kidsneakers/jumpjacks2.jpg', 'Blue', 'low top', 'wide', 'Generic', 'Standard', '2025-09-18 18:26:04'),
(38, 'PlayKicks', 1999.00, 10, 'assets/img/kids/kidsneakers/playkicks.jpg', 'kidsneakers', NULL, 'assets/img/kids/kidsneakers/playkicks.jpg', 'assets/img/kids/kidsneakers/playkicks1.jpg', 'assets/img/kids/kidsneakers/playkicks2.jpg', 'Pink', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 18:26:04'),
(39, 'Vivid Vibe', 999.00, 10, 'assets/img/kids/kidsneakers/vividvibe.png', 'kidsneakers', NULL, 'assets/img/kids/kidsneakers/vividvibe.png', 'assets/img/kids/kidsneakers/vividvibe1.png', 'assets/img/kids/kidsneakers/vividvibe2.png', 'White', 'low top', 'regular', 'Generic', 'Air Rizz', '2025-09-18 18:27:24'),
(40, 'Zippy Sneaks', 999.00, 10, 'assets/img/kids/kidsneakers/zippysneaks.png', 'kidsneakers', NULL, 'assets/img/kids/kidsneakers/zippysneaks.png', 'assets/img/kids/kidsneakers/zippysneaks1.png', 'assets/img/kids/kidsneakers/zippysneaks2.png', 'White', 'low top', 'regular', 'Generic', 'Air Rizz', '2025-09-18 18:27:24'),
(41, 'Joy Walk', 599.00, 10, 'assets/img/kids/kidslipon/joywalks.png', 'kidslipon', NULL, 'assets/img/kids/kidslipon/joywalks.png', 'assets/img/kids/kidslipon/joywalks1.png', 'assets/img/kids/kidslipon/joywalks2.png', 'Multi-Colour', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 18:27:24'),
(42, 'Play Ease', 899.00, 10, 'assets/img/kids/kidslipon/playease.png', 'kidslipon', NULL, 'assets/img/kids/kidslipon/playease.png', 'assets/img/kids/kidslipon/playease1.png', 'assets/img/kids/kidslipon/playease2.png', 'Multi-Colour', 'low top', 'regular', 'Generic', 'Standard', '2025-09-18 18:27:24'),
(43, 'SlipSpark', 899.00, 10, 'assets/img/kids/kidslipon/slipsparks.png', 'kidslipon', NULL, 'assets/img/kids/kidslipon/slipsparks.png', 'assets/img/kids/kidslipon/slipsparks1.png', 'assets/img/kids/kidslipon/slipsparks2.png', 'Pink', 'mid top', 'wide', 'Generic', 'Air Rizz', '2025-09-18 18:27:24'),
(44, 'Snap Slip', 899.00, 10, 'assets/img/kids/kidslipon/snapslip.png', 'kidslipon', NULL, 'assets/img/kids/kidslipon/snapslip.png', 'assets/img/kids/kidslipon/snapslip1.png', 'assets/img/kids/kidslipon/snapslip2.png', 'Multi-Colour', 'mid top', 'extra wide', 'Generic', 'Standard', '2025-09-18 18:32:36'),
(45, 'Zoom Tots', 1999.00, 10, 'assets/img/kids/kidslipon/zoomtots.jpg', 'kidslipon', NULL, 'assets/img/kids/kidslipon/zoomtots.jpg', 'assets/img/kids/kidslipon/zoomtots1.jpg', 'assets/img/kids/kidslipon/zoomtots2.jpg', 'Black', 'mid top', 'wide', 'XRizz', 'Air Rizz', '2025-09-18 18:32:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
