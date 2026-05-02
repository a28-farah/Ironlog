-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 26, 2026 at 10:30 AM
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
-- Database: `ironlog`
--

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(10) UNSIGNED NOT NULL,
  `workout_id` int(10) UNSIGNED NOT NULL,
  `muscle_group` varchar(50) NOT NULL,
  `exercise` varchar(100) NOT NULL,
  `sort_order` tinyint(3) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `workout_id`, `muscle_group`, `exercise`, `sort_order`) VALUES
(1, 1, 'Chest', 'Bench Press', 1),
(2, 1, 'Chest', 'Dumbbell Fly', 2),
(3, 1, 'Shoulders', 'Overhead Press', 3),
(4, 1, 'Triceps', 'Tricep Pushdown', 4),
(5, 2, 'Back', 'Barbell Row', 1),
(6, 2, 'Back', 'Lat Pulldown', 2),
(7, 2, 'Biceps', 'Bicep Curl', 3),
(8, 3, 'Legs', 'Barbell Squat', 1),
(9, 3, 'Legs', 'Leg Press', 2),
(10, 3, 'Legs', 'Leg Curl', 3),
(11, 4, 'Chest', 'Bench Press', 1),
(12, 4, 'Chest', 'Dumbbell Fly', 2),
(13, 4, 'Shoulders', 'Overhead Press', 3),
(14, 4, 'Triceps', 'Tricep Pushdown', 4),
(15, 5, 'Back', 'Barbell Row', 1),
(16, 5, 'Back', 'Lat Pulldown', 2),
(17, 5, 'Biceps', 'Bicep Curl', 3),
(18, 6, 'Legs', 'Barbell Squat', 1),
(19, 6, 'Legs', 'Leg Press', 2),
(20, 6, 'Legs', 'Leg Curl', 3),
(21, 7, 'Chest', 'Bench Press', 1),
(22, 7, 'Chest', 'Dumbbell Fly', 2),
(23, 7, 'Shoulders', 'Overhead Press', 3),
(24, 7, 'Triceps', 'Tricep Pushdown', 4),
(25, 8, 'Back', 'Barbell Row', 1),
(26, 8, 'Back', 'Lat Pulldown', 2),
(27, 8, 'Biceps', 'Bicep Curl', 3),
(28, 9, 'Legs', 'Barbell Squat', 1),
(29, 9, 'Legs', 'Leg Press', 2),
(30, 9, 'Legs', 'Leg Curl', 3),
(31, 10, 'Chest', 'Bench Press', 1),
(32, 10, 'Shoulders', 'Overhead Press', 2),
(33, 10, 'Triceps', 'Tricep Pushdown', 3),
(34, 11, 'Back', 'Barbell Row', 1),
(35, 11, 'Back', 'Lat Pulldown', 2),
(36, 11, 'Biceps', 'Bicep Curl', 3),
(37, 12, 'Legs', 'Barbell Squat', 1),
(38, 12, 'Legs', 'Leg Press', 2);

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(200) NOT NULL,
  `target_kg` decimal(5,1) DEFAULT NULL,
  `target_reps` tinyint(4) DEFAULT NULL,
  `target_days` smallint(6) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `done` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `description`, `target_kg`, `target_reps`, `target_days`, `deadline`, `done`, `created_at`) VALUES
(1, 1, 'Bench Press 100 kg', 100.0, NULL, NULL, '2026-06-30', 0, '2026-04-25 10:32:23'),
(2, 1, 'Squat 150 kg', 150.0, NULL, NULL, '2026-07-30', 0, '2026-04-25 10:32:23'),
(3, 1, 'Overhead Press 60 kg', 60.0, NULL, NULL, '2026-06-15', 0, '2026-04-25 10:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `sets`
--

CREATE TABLE `sets` (
  `id` int(10) UNSIGNED NOT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `set_no` tinyint(3) UNSIGNED NOT NULL,
  `reps` tinyint(3) UNSIGNED NOT NULL,
  `weight_kg` decimal(5,1) NOT NULL DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sets`
--

INSERT INTO `sets` (`id`, `exercise_id`, `set_no`, `reps`, `weight_kg`) VALUES
(1, 1, 1, 10, 60.0),
(2, 1, 2, 8, 65.0),
(3, 1, 3, 6, 70.0),
(4, 2, 1, 12, 14.0),
(5, 2, 2, 10, 16.0),
(6, 2, 3, 8, 18.0),
(7, 3, 1, 10, 35.0),
(8, 3, 2, 8, 40.0),
(9, 3, 3, 6, 45.0),
(10, 4, 1, 12, 25.0),
(11, 4, 2, 12, 27.5),
(12, 4, 3, 10, 30.0),
(13, 5, 1, 10, 50.0),
(14, 5, 2, 8, 55.0),
(15, 5, 3, 6, 60.0),
(16, 6, 1, 12, 45.0),
(17, 6, 2, 10, 50.0),
(18, 6, 3, 8, 55.0),
(19, 7, 1, 12, 15.0),
(20, 7, 2, 10, 17.5),
(21, 7, 3, 10, 20.0),
(22, 8, 1, 8, 80.0),
(23, 8, 2, 6, 90.0),
(24, 8, 3, 5, 100.0),
(25, 9, 1, 12, 100.0),
(26, 9, 2, 10, 110.0),
(27, 9, 3, 8, 120.0),
(28, 10, 1, 12, 35.0),
(29, 10, 2, 10, 40.0),
(30, 10, 3, 10, 42.5),
(31, 11, 1, 10, 65.0),
(32, 11, 2, 8, 70.0),
(33, 11, 3, 6, 75.0),
(34, 12, 1, 12, 16.0),
(35, 12, 2, 10, 18.0),
(36, 12, 3, 8, 20.0),
(37, 13, 1, 10, 37.5),
(38, 13, 2, 8, 42.5),
(39, 13, 3, 6, 47.5),
(40, 14, 1, 12, 27.5),
(41, 14, 2, 12, 30.0),
(42, 14, 3, 10, 32.5),
(43, 15, 1, 10, 55.0),
(44, 15, 2, 8, 60.0),
(45, 15, 3, 6, 67.5),
(46, 16, 1, 12, 47.5),
(47, 16, 2, 10, 52.5),
(48, 16, 3, 8, 57.5),
(49, 17, 1, 12, 17.5),
(50, 17, 2, 10, 20.0),
(51, 17, 3, 8, 22.5),
(52, 18, 1, 8, 90.0),
(53, 18, 2, 6, 100.0),
(54, 18, 3, 5, 115.0),
(55, 19, 1, 12, 110.0),
(56, 19, 2, 10, 125.0),
(57, 19, 3, 8, 140.0),
(58, 20, 1, 12, 37.5),
(59, 20, 2, 10, 42.5),
(60, 20, 3, 10, 45.0),
(61, 21, 1, 10, 70.0),
(62, 21, 2, 8, 75.0),
(63, 21, 3, 5, 80.0),
(64, 22, 1, 12, 18.0),
(65, 22, 2, 10, 20.0),
(66, 22, 3, 8, 22.0),
(67, 23, 1, 10, 40.0),
(68, 23, 2, 8, 45.0),
(69, 23, 3, 5, 50.0),
(70, 24, 1, 12, 30.0),
(71, 24, 2, 12, 32.5),
(72, 24, 3, 10, 35.0),
(73, 25, 1, 10, 60.0),
(74, 25, 2, 8, 67.5),
(75, 25, 3, 5, 75.0),
(76, 26, 1, 12, 50.0),
(77, 26, 2, 10, 55.0),
(78, 26, 3, 8, 60.0),
(79, 27, 1, 12, 20.0),
(80, 27, 2, 10, 22.5),
(81, 27, 3, 8, 25.0),
(82, 28, 1, 8, 100.0),
(83, 28, 2, 6, 112.5),
(84, 28, 3, 4, 125.0),
(85, 29, 1, 12, 130.0),
(86, 29, 2, 10, 150.0),
(87, 29, 3, 8, 170.0),
(88, 30, 1, 12, 40.0),
(89, 30, 2, 10, 45.0),
(90, 30, 3, 8, 47.5),
(91, 31, 1, 8, 75.0),
(92, 31, 2, 6, 80.0),
(93, 31, 3, 4, 85.0),
(94, 32, 1, 8, 42.5),
(95, 32, 2, 6, 47.5),
(96, 32, 3, 4, 52.5),
(97, 33, 1, 10, 32.5),
(98, 33, 2, 10, 35.0),
(99, 33, 3, 8, 37.5),
(100, 34, 1, 8, 65.0),
(101, 34, 2, 6, 72.5),
(102, 34, 3, 4, 80.0),
(103, 35, 1, 10, 55.0),
(104, 35, 2, 8, 60.0),
(105, 35, 3, 6, 65.0),
(106, 36, 1, 10, 22.5),
(107, 36, 2, 8, 25.0),
(108, 36, 3, 6, 27.5),
(109, 37, 1, 6, 110.0),
(110, 37, 2, 4, 120.0),
(111, 37, 3, 3, 130.0),
(112, 38, 1, 10, 150.0),
(113, 38, 2, 8, 170.0),
(114, 38, 3, 6, 190.0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `created_at`) VALUES
(1, 'Demo User', 'demo', '$2y$10$epgUV8PZfaXrr5lRo24O6O/fqKvmyUd1HMcp5BUCTPo1anRna/Ofi', '2026-04-25 10:32:20'),
(2, 'ali', 'ali1', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIUNLq1HFWbKFSW', '2026-04-25 09:33:55'),
(3, 'khan', 'user1', '$2y$10$NZuaue32sv7nbUk2Wy2ROeuPjhTdEprjAS1ltPYHC072Uo4c6i.E2', '2026-04-25 10:21:29');

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `workout_date` date NOT NULL,
  `duration_min` smallint(5) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`id`, `user_id`, `title`, `workout_date`, `duration_min`, `notes`, `created_at`) VALUES
(1, 1, 'Day 1 - Push', '2026-04-01', 50, 'First day of program', '2026-04-25 10:32:20'),
(2, 1, 'Day 2 - Pull', '2026-04-02', 55, 'Good back pump', '2026-04-25 10:32:20'),
(3, 1, 'Day 3 - Legs', '2026-04-04', 60, 'Legs are sore', '2026-04-25 10:32:20'),
(4, 1, 'Day 1 - Push', '2026-04-08', 50, 'Getting stronger', '2026-04-25 10:32:20'),
(5, 1, 'Day 2 - Pull', '2026-04-09', 55, 'Added weight', '2026-04-25 10:32:20'),
(6, 1, 'Day 3 - Legs', '2026-04-11', 58, 'Squats improving', '2026-04-25 10:32:20'),
(7, 1, 'Day 1 - Push', '2026-04-15', 50, 'Hit 80kg bench', '2026-04-25 10:32:20'),
(8, 1, 'Day 2 - Pull', '2026-04-16', 55, 'Rows at 75kg', '2026-04-25 10:32:20'),
(9, 1, 'Day 3 - Legs', '2026-04-18', 60, 'Leg press 180kg', '2026-04-25 10:32:20'),
(10, 1, 'Day 1 - Push', '2026-04-22', 50, 'Bench 85kg PR', '2026-04-25 10:32:20'),
(11, 1, 'Day 2 - Pull', '2026-04-23', 55, 'Deadlift 130kg', '2026-04-25 10:32:20'),
(12, 1, 'Day 3 - Legs', '2026-04-25', 65, 'Squat 130kg PR!', '2026-04-25 10:32:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_id` (`workout_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sets`
--
ALTER TABLE `sets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_date` (`user_id`,`workout_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sets`
--
ALTER TABLE `sets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sets`
--
ALTER TABLE `sets`
  ADD CONSTRAINT `sets_ibfk_1` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
