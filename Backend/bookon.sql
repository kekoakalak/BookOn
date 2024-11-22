-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 03:04 AM
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
-- Database: `bookon`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `appointment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `service_id` int(11) DEFAULT NULL,
  `provider_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('Completed','Pending','Cancelled') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `code`, `appointment_date`, `service_id`, `provider_id`, `user_id`, `status`) VALUES
(1, 'DXNHDW', '2024-11-02 09:00:00', 34, 1, 1, 'Pending'),
(12, 'K44XGB', '2024-11-14 16:21:00', 63, 1, 1, 'Completed'),
(14, '2QNRTT', '2024-11-19 15:27:00', 63, 1, 1, 'Completed'),
(15, '780TVE', '2024-11-13 14:23:00', 36, 1, 1, 'Pending'),
(16, 'L1BC84', '2024-12-18 20:38:00', 36, 1, 1, 'Pending'),
(17, 'TK5ECD', '2024-11-08 10:58:00', 34, 2, 2, 'Pending'),
(18, 'PZ3SM6', '2024-11-12 21:33:00', 43, 1, 1, 'Pending'),
(19, 'MHAD0J', '2024-11-08 17:29:00', 34, 2, 1, 'Pending'),
(20, 'OF4ORN', '2024-11-14 15:12:00', 68, 1, 1, 'Completed'),
(21, 'MB16I3', '2024-11-14 09:00:00', 63, 1, 1, 'Pending'),
(22, 'WURA5E', '0000-00-00 00:00:00', 34, 2, 1, 'Pending'),
(23, 'BYSBH7', '0000-00-00 00:00:00', 34, 2, 1, 'Pending'),
(24, 'FW7XY2', '2024-11-12 10:00:00', 34, 2, 2, 'Pending'),
(25, '9O0HYX', '2024-11-12 01:00:00', 65, 2, 1, 'Pending'),
(26, 'DSBULZ', '2024-11-13 11:00:00', 34, 2, 2, 'Pending'),
(27, '9T5Z18', '2024-11-13 04:30:00', 68, 1, 1, 'Pending'),
(28, 'JSYA1H', '2024-11-13 02:30:00', 34, 2, 1, 'Pending'),
(29, 'FRBFBJ', '2024-11-13 03:30:00', 65, 2, 1, 'Completed'),
(30, 'PGRH99', '2024-11-21 12:00:00', 68, 1, 1, 'Pending'),
(31, '4XVAOT', '2024-11-14 08:00:00', 34, 2, 1, 'Pending'),
(32, 'HJ3L2J', '2024-11-14 10:00:00', 68, 1, 1, 'Pending'),
(33, '3YKNYW', '2024-11-14 09:30:00', 63, 1, 2, 'Pending'),
(34, 'GTOREK', '2024-11-14 01:30:00', 49, 1, 2, 'Pending'),
(35, '8E8GO1', '2024-11-14 03:30:00', 68, 1, 1, 'Completed'),
(36, 'TN8ECS', '2024-11-20 10:30:00', 34, 2, 1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_details`
--

CREATE TABLE `appointment_details` (
  `details_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `notes` text NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `appointment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `duration` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointment_details`
--

INSERT INTO `appointment_details` (`details_id`, `appointment_id`, `first_name`, `last_name`, `mobile`, `notes`, `payment_method`, `appointment_date`, `duration`) VALUES
(1, 1, 'Nonito', 'Odjinar', '09100000001', 'I need an iPad during appointment.', 'GCash', '2024-11-07 15:43:43', ''),
(5, 5, 'Nonito', 'Odjinar', '09100000001', 'I need an excellent whitening service!', 'GCash', '2024-11-07 15:43:43', ''),
(6, 6, 'Nonito', 'Odjinar', '09100000001', 'Need to schedule it immediately', 'GCash', '2024-11-07 15:43:43', ''),
(7, 7, 'Nonito', 'Donaire', '12312123123', 'Please be gentle', 'GCash', '2024-11-07 15:43:43', ''),
(8, 8, 'Nonito', 'Donaire', '12312123123', 'Touch me softly tenaenenene', 'GCash', '2024-11-07 15:43:43', ''),
(9, 9, 'Nonito', 'Donaire', '12312123123', 'sdasdasdasdsad', 'GCash', '2024-11-07 15:43:43', ''),
(10, 10, 'Mackenzie', 'Dela Cruz', '19196095826', 'sda', 'Visa', '2024-11-14 01:14:00', '30 mins to1 hr'),
(11, 11, 'Mackenzie', 'Dela Cruz', '8989898989', 'please be gentle', 'Visa', '2024-11-13 12:15:00', '30 mins to 1hr'),
(12, 12, 'raksasha', 'ramzes', '77777777', 'do u know dewae', 'Visa', '2024-11-14 16:21:00', '30 mins to 1hr'),
(13, 13, 'john bryant', 'molina', '123123213', 'hey hey yoyo', 'GCash', '2024-11-20 17:30:00', '2hrs '),
(14, 14, 'john', 'brown', '2222222222', 'sdad', 'PayPal', '2024-11-19 15:27:00', '30 mins to 1hr'),
(15, 15, 'Ramza', 'Samara', '111111111', 'hello po ', 'PayPal', '2024-11-13 14:23:00', '120'),
(16, 16, 'Nonito', 'Donaire', '12312123123', 'tapos ang boxing EE SAMAAAA', 'Visa', '2024-12-18 20:38:00', '120'),
(17, 17, 'Test', 'Test', '0912128912', 'ahsudygasd', 'GCash', '2024-11-08 10:58:00', '90'),
(18, 18, 'Nonito', 'Bonito', '12312123123', 'awdasdwa', 'Visa', '2024-11-12 21:33:00', '30 mins to1 hr'),
(19, 19, 'Hey', 'Ho', '72736372', 'Hhhhhhhh', 'GCash', '2024-11-08 17:29:00', '90'),
(20, 20, 'Pakalo', 'Papito', '12312123123', 'please be gentle with my car', 'PayPal', '2024-11-14 15:12:00', '1 hr'),
(21, 21, 'john brownskie', 'ssarapina', '2222222222', 'PLease fix my dentures', 'GCash', '2024-11-14 09:00:00', '30 mins to 1hr'),
(22, 22, 'Laurence', 'Cayabyab', '09252525252', 'tenkljfdhkgdslg', 'GCash', '0000-00-00 00:00:00', '90'),
(23, 23, 'Laurence', 'Cayabyab', '09252525252', 'dfgdsfgdf', 'GCash', '0000-00-00 00:00:00', '90'),
(24, 24, 'test', 'test', 'test', 'test', 'GCash', '2024-11-12 10:00:00', '90'),
(25, 25, 'Hin', 'sample', '09310332564', 'sample notes', 'GCash', '2024-11-12 01:00:00', '1hour'),
(26, 26, 'test', 'test', '812398178', 'test', 'GCash', '2024-11-13 11:00:00', '90'),
(27, 27, 'sample', 'test', '09310332564', 'sample sample sample test', 'GCash', '2024-11-13 04:30:00', '1 hr'),
(28, 28, 'this is', 'sample', '09123654789', 'sample notes/ test', 'GCash', '2024-11-13 02:30:00', '90'),
(29, 29, 'brendon', 'urie', '09456123648', 'sample testing', 'GCash', '2024-11-13 03:30:00', '1hour'),
(30, 30, 'john brownskie', 'ssarapina', '2222222222', 'weswaswesweas', 'Visa', '2024-11-21 12:00:00', '1 hr'),
(31, 31, 'John', 'Doe', '09172538487266388383', 'Add limit to phone number. Set it to 11 digits only', 'GCash', '2024-11-14 08:00:00', '90'),
(32, 32, 'Bruce', 'Banner', '09252525252', 'jasdfhkjlsdng', 'PayPal', '2024-11-14 10:00:00', '1 hr'),
(33, 33, 'Test', 'Test', '91239120921', 'asdasduasidi', 'PayPal', '2024-11-14 09:30:00', '30 mins to 1hr'),
(34, 34, 'Testing', 'Testing', '12345678901', 'Testing', 'GCash', '2024-11-14 01:30:00', '30 mins to 1 hr'),
(35, 35, 'test', 'test', '09468564564', 'test lang', 'GCash', '2024-11-14 03:30:00', '1 hr'),
(36, 36, 'johny', 'bravo', '2222222222', 'sdasd', 'PayPal', '2024-11-20 10:30:00', '90');

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE `app_users` (
  `id` int(7) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Admin','User','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_users`
--

INSERT INTO `app_users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'admin', '$2y$10$V/UGyxSyWGiLw0vSMPV2AOvWxkYX1CRSF6djzhpAR5jtnhp/P3bte', 'admin@domain.com', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `blog_post`
--

CREATE TABLE `blog_post` (
  `post_id` int(7) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `author_id` int(5) NOT NULL,
  `category_id` int(7) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `blog_post`
--

INSERT INTO `blog_post` (`post_id`, `title`, `content`, `author_id`, `category_id`, `created_at`) VALUES
(1, 'OpenAI is dominating', 'OpenAi is crushing like a storm.', 1, 4, '2024-09-11 15:03:46'),
(2, 'Lighthouse in Liloan', 'Visit the historical landmark of Liloan, the Parola Lighthouse.', 1, 2, '2024-09-13 09:16:27'),
(4, 'test post', 'test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post test post ', 1, 4, '2024-09-13 21:40:00'),
(5, 'test category', 'category category category category category category category category category category category category category category category category category category category category category category category category category category category category ', 1, 1, '2024-09-13 21:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(7) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Lifestyle'),
(2, 'Travel'),
(4, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(7) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `name`, `content`, `created_at`) VALUES
(2, 1, 'Martin', 'Interesting insights! Thanks for sharing.', '2024-09-13 10:05:00'),
(3, 2, 'Richard', 'I disagree with the points made in the article.', '2024-09-13 10:10:00'),
(4, 2, 'Kevin', 'Can you provide more details?', '2024-09-13 10:15:00'),
(5, 2, 'Dawn', 'It is nice to be able to write a comment in a blog post.', '2024-09-13 12:50:52'),
(7, 1, 'Joeric', 'Really helpful to Web Development', '2024-09-14 03:05:59'),
(8, 2, 'Mark', 'Nice place to relax.', '2024-09-14 01:28:46'),
(9, 5, 'James', 'dfyhfgjhgjk', '2024-09-16 08:30:06');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('Cash','Credit Card') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` enum('Paid','Unpaid') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `appointment_id`, `user_id`, `provider_id`, `amount`, `payment_date`, `payment_method`, `status`) VALUES
(1, 1, 1, 1, 7000.00, '2024-10-18 10:11:47', 'Cash', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `provider_id` int(11) NOT NULL,
  `first_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`provider_id`, `first_name`, `last_name`, `password`, `email`, `mobile`, `created_at`) VALUES
(1, 'Matilda', 'Ike', 'password', 'matilda@domain.com', '09110011110', '2024-10-22 01:41:49'),
(2, 'Ramon', 'Ong', 'password', 'ramon@domain.com', '09991111222', '2024-10-22 02:00:14');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `star_rating` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `appointment_id`, `user_id`, `service_id`, `provider_id`, `feedback`, `star_rating`, `created_at`, `updated_at`) VALUES
(8, 1, 1, 34, 1, 'Good work!', 4, '2024-10-27 09:29:35', '2024-10-27 09:29:35'),
(10, 1, 1, 34, 0, 'Meh is fine.', 3, '2024-11-05 07:27:06', '2024-11-05 07:27:06'),
(11, 1, 1, 34, 1, 'fdjhjfh', 5, '2024-11-07 02:36:45', '2024-11-07 02:36:45'),
(12, 1, 1, 34, 1, 'sample', 3, '2024-11-08 04:36:47', '2024-11-08 04:36:47'),
(13, 1, 1, 34, 1, 'nice', 5, '2024-11-12 02:38:19', '2024-11-12 02:38:19'),
(14, 1, 1, 34, 1, 'nice', 5, '2024-11-12 02:38:19', '2024-11-12 02:38:19'),
(15, 1, 1, 34, 1, 'masakit', 1, '2024-11-12 06:53:02', '2024-11-12 06:53:02'),
(16, 12, 1, 63, 1, 'salamat doc', 4, '2024-11-13 06:55:50', '2024-11-13 06:55:50'),
(17, 1, 1, 34, 1, '', 1, '2024-11-13 06:58:37', '2024-11-13 06:58:37'),
(18, 1, 1, 34, 1, '', 5, '2024-11-13 07:13:00', '2024-11-13 07:13:00'),
(19, 1, 1, 34, 1, 'k', 3, '2024-11-13 08:21:23', '2024-11-13 08:21:23'),
(20, 12, 1, 63, 1, 'goods', 3, '2024-11-13 08:39:05', '2024-11-13 08:39:05'),
(21, 12, 1, 63, 1, 'kakalurkey may pa dentures na ampeg~~', 4, '2024-11-13 09:02:18', '2024-11-13 09:02:18'),
(22, 29, 1, 65, 2, 'sample review for brendon urie', 4, '2024-11-14 01:26:04', '2024-11-14 01:26:04'),
(23, 29, 1, 65, 2, 'Okay', 3, '2024-11-14 01:44:16', '2024-11-14 01:44:16'),
(24, 12, 1, 63, 1, 'awts', 3, '2024-11-14 03:33:46', '2024-11-14 03:33:46');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `duration` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `availability` text NOT NULL,
  `media` varchar(255) DEFAULT NULL,
  `provider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `duration`, `price`, `start_time`, `end_time`, `availability`, `media`, `provider_id`) VALUES
(34, 'Implants', 'Smile again with Dental Implants', '90', 7000.00, '08:00:00', '05:00:00', 'M', '6710dec56a0a6-66e501cb4958718874d3037d.jpeg', 2),
(36, 'Whitening', 'Effectively whiten your teeth', '120', 2000.00, '08:00:00', '05:00:00', 'M, T, W', '6711a828b6f65-received_1566367937616463.jpeg', 1),
(43, 'Sword Training', 'Go on an Adventure and train the way of the sword with Meowrro Sensei. ', '30 mins to1 hr', 10000.00, '08:00:00', '05:00:00', 'T, M, TH, F', '672da85199521-meowrro.jpg', 1),
(49, 'AromaTherapy', 'Smell therapy to feel good ', '30 mins to 1 hr', 400.00, '10:16:00', '04:28:00', 'W, T', '672da86270661-67140d73a9245-IMG_20241010_103230.jpg', 1),
(63, 'Dentures', 'Dentures with Kris ', '30 mins to 1hr', 3500.00, '10:00:00', '04:00:00', 'M, T, W, TH, F', '672d68e5da102-672c5b0bd2a4a-672c587da9da2-Dentures.jpg', 1),
(65, 'Sample Service Edited', 'Sample for testing', '1hour', 500.00, '12:30:00', '05:30:00', 'M, W, F', '672da69f42714-2c80de5e9918a8d1e959c302e20900ba.jpg', 2),
(67, 'Crusade', 'Krusada', '20 years', 0.00, '05:26:00', '05:23:00', 'T, TH', 'Moloc.jpg', 1),
(68, 'Car Wash', 'Wash your car with out Carwash Services!', '1 hr', 3500.00, '10:00:00', '05:00:00', 'T, M, W, TH, F, S', '673151af78129-398478942_640569324955648_7992276780882324294_n.jpg', 1),
(69, 'Not Found', 'Notification page not found', '1', 123.00, '12:30:00', '01:30:00', 'Su, T, W', '673549155f377-Screenshot_20241114_084708.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `password`, `email`, `mobile`, `created_at`) VALUES
(1, 'Nonito', 'Odjinar', '$2y$10$V/UGyxSyWGiLw0vSMPV2AOvWxkYX1CRSF6djzhpAR5jtnhp/P3bte', 'nonito@domain.com', '09100000001', '2024-10-21 02:10:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `appointment_details`
--
ALTER TABLE `appointment_details`
  ADD PRIMARY KEY (`details_id`);

--
-- Indexes for table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_post`
--
ALTER TABLE `blog_post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `post_fk1` (`category_id`),
  ADD KEY `post_fk2` (`author_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `comments_fk1` (`post_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`provider_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `rating_fk1` (`appointment_id`),
  ADD KEY `rating_fk3` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `appointment_details`
--
ALTER TABLE `appointment_details`
  MODIFY `details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_post`
--
ALTER TABLE `blog_post`
  MODIFY `post_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_post`
--
ALTER TABLE `blog_post`
  ADD CONSTRAINT `post_fk1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `post_fk2` FOREIGN KEY (`author_id`) REFERENCES `app_users` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_fk1` FOREIGN KEY (`post_id`) REFERENCES `blog_post` (`post_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
