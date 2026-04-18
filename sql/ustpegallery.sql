-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2026 at 01:14 PM
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
-- Database: `ustpegallery`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `admin_id`, `action`, `created_at`) VALUES
(1, 3, 'Logged into the system', '2026-04-04 09:00:25'),
(2, 3, 'Logged into the system', '2026-04-04 09:03:46'),
(3, 2, 'Logged into the system', '2026-04-04 09:05:17'),
(4, 8, 'Logged into the system', '2026-04-04 09:58:35'),
(5, 10, 'Logged into the system', '2026-04-04 10:03:31'),
(6, 3, 'Added new Student: 2024304660', '2026-04-04 11:55:12'),
(7, 3, 'Added new Student: 2024304550', '2026-04-04 12:45:00'),
(8, 4, 'Logged into the system', '2026-04-04 13:11:17'),
(9, 10, 'Logged into the system', '2026-04-05 10:15:03'),
(10, 10, 'Logged into the system', '2026-04-06 14:56:51'),
(11, 10, 'Deleted Department (ID: 8)', '2026-04-06 16:03:53'),
(12, 10, 'Deleted Program (ID: 35)', '2026-04-06 16:04:54'),
(13, 10, 'Deleted Program (ID: 36)', '2026-04-06 16:29:54'),
(14, 10, 'Added new Program: CIty BOIII', '2026-04-06 16:30:36'),
(15, 10, 'Added new Program: BAKLA', '2026-04-06 16:32:08'),
(16, 10, 'Deleted Program: BAKLA', '2026-04-06 16:48:16'),
(17, 10, 'Deleted Program: CIty BOIII', '2026-04-06 17:12:27'),
(18, 10, 'Deleted Department: Art Major in City', '2026-04-06 17:12:36'),
(19, 10, 'Added new Section: IT - 4R1', '2026-04-07 13:05:40'),
(20, 10, 'Updated General System Settings', '2026-04-07 14:07:32'),
(21, 10, 'Uploaded photo for Student: Durain, Jussy Jay G.', '2026-04-07 14:15:17'),
(22, 10, 'Added new Section: CS - 4R1', '2026-04-07 14:32:08'),
(23, 10, 'Uploaded photo for Student: Tangarorang, Maui Alenxander', '2026-04-07 14:35:12'),
(24, 10, 'Added new Section: CE - 4R1', '2026-04-07 14:36:49'),
(25, 10, 'Uploaded photo for Student: Pabia, Jared', '2026-04-07 14:38:02'),
(26, 10, 'Updated General System Settings', '2026-04-08 00:17:40'),
(27, 10, 'Updated General System Settings', '2026-04-08 00:17:54'),
(28, 10, 'Updated General System Settings', '2026-04-08 00:19:03'),
(29, 10, 'Updated General System Settings', '2026-04-08 00:29:48'),
(30, 10, 'Added new Section: DS - 4R1', '2026-04-08 00:32:04'),
(31, 10, 'Uploaded photo for Student: Lapinid, Abigail', '2026-04-08 00:33:24'),
(32, 10, 'Added new Student: 2024305222', '2026-04-08 00:49:42'),
(33, 10, 'Added new Section: CpE - 4R1', '2026-04-08 00:51:02'),
(34, 10, 'Uploaded photo for Student: Caroro, Andrei', '2026-04-08 00:52:02'),
(35, 3, 'Logged into the system', '2026-04-08 10:18:59'),
(36, 3, 'Added new Class Year: 2033', '2026-04-08 10:57:53'),
(37, 3, 'Added new Class Year: 2034', '2026-04-08 10:59:59'),
(38, 3, 'Added new Class Year: 2035', '2026-04-08 11:06:37');

-- --------------------------------------------------------

--
-- Table structure for table `class_years`
--

CREATE TABLE `class_years` (
  `id` int(11) NOT NULL,
  `year` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_years`
--

INSERT INTO `class_years` (`id`, `year`) VALUES
(1, '2028'),
(2, '2029'),
(3, '2030'),
(4, '2031'),
(5, '2032'),
(6, '2033'),
(7, '2034'),
(8, '2035');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `abbreviation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `abbreviation`) VALUES
(1, 'Engineering', 'CEA'),
(2, 'Computer Science and Information Systems', 'CSIS'),
(3, 'Technology', 'COT'),
(4, 'Life Sciences', 'CST'),
(5, 'Natural Sciences', 'CNS'),
(6, 'Social Sciences', 'CSS'),
(7, 'Art and Humanities', 'CAH');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `abbreviation` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `department_id`, `name`, `abbreviation`) VALUES
(1, 1, 'BS Civil Engineering', 'CE'),
(2, 1, 'BS Electronic Engineering', 'ECE'),
(3, 1, 'BS Electrical Engineering', 'EE'),
(4, 1, 'BS Environment Engineering', 'EnE'),
(5, 1, 'BS Computer Engineering', 'CpE'),
(6, 1, 'BS Agricultural and Biosystems Engineering', 'ABE'),
(7, 1, 'BS Mechanical Engineering', 'ME'),
(8, 1, 'BS Naval Architecture and Marine Engineering', 'NAME'),
(9, 1, 'BS Geodetic Engineering', 'GE'),
(10, 2, 'BS Computer Science', 'CS'),
(11, 2, 'BS Data Science', 'DS'),
(12, 2, 'BS Technology Communication Management', 'TCM'),
(13, 2, 'BS Information Technology', 'IT'),
(14, 3, 'BS Agricultural Technology', 'AgTech'),
(15, 3, 'BS Autotronics', 'Auto'),
(16, 3, 'BS Electro-Mechanical', 'EM'),
(17, 3, 'BS Electronics Technology', 'EST'),
(18, 3, 'BS Energy System and Management', 'ESM'),
(19, 3, 'BS Food Processing and Management', 'FPM'),
(20, 3, 'BS Manufacturing Engineering Technology', 'MET'),
(21, 4, 'BS Agricultural', 'Agri'),
(22, 4, 'BS Agroforestry', 'AF'),
(23, 4, 'BS Horticulture and Management', 'HM'),
(24, 4, 'BS Marine Biology', 'MarBio'),
(25, 5, 'BS Applied Mathematics', 'AM'),
(26, 5, 'BS Applied Physics', 'AP'),
(27, 5, 'BS Chemistry', 'Chem'),
(28, 5, 'BS Environmental Science', 'ES'),
(29, 6, 'BS Secondary Education (major in Mathematics)', 'SEM'),
(30, 6, 'BS Secondary Education (major in Science)', 'SES'),
(31, 6, 'BS Social Work', 'SW'),
(32, 6, 'BS Technical-Vocational Teacher', 'TVT'),
(33, 6, 'BS Technology and Livelihood Education (major in Industrial Arts, Home Economics)', 'TLE'),
(34, 7, 'BS Architecture', 'Archi');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `program_id`, `name`) VALUES
(1, 13, 'IT - 4R1'),
(2, 10, 'CS - 4R1'),
(3, 1, 'CE - 4R1'),
(4, 11, 'DS - 4R1'),
(5, 5, 'CpE - 4R1');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `department_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `latin_honor` varchar(50) NOT NULL,
  `class_year` year(4) NOT NULL,
  `quote` text NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `full_name`, `department_id`, `program_id`, `section_id`, `latin_honor`, `class_year`, `quote`, `photo_path`, `is_active`, `uploaded_by`, `uploaded_at`) VALUES
(14, NULL, 'Durain, Jussy Jay G.', 2, 13, 1, 'Magna Cum Laude', '2029', 'Work Hard, Play Hard.', 'assets/img/student/student_69d51174edd3c6.75881707.jpg', 1, 10, '2026-04-07 14:15:17'),
(15, NULL, 'Tangarorang, Maui Alenxander', 2, 13, 1, 'Cum Laude', '2029', 'I love my wifi', 'assets/img/student/student_69d516202295c4.60509213.jpg', 1, 10, '2026-04-07 14:35:12'),
(16, NULL, 'Pabia, Jared', 1, 1, 3, 'Summa Cum Laude', '2029', 'AYOOOOO', 'assets/img/student/student_69d516ca27e1f9.09941308.jpg', 1, 10, '2026-04-07 14:38:02'),
(17, NULL, 'Lapinid, Abigail', 2, 11, 4, 'Cum Laude', '2029', 'i love cats, meow', 'assets/img/student/student_69d5a2545fd573.12851099.jpg', 1, 10, '2026-04-08 00:33:24'),
(18, NULL, 'Caroro, Andrei', 1, 5, 5, 'Cum Laude', '2029', 'I love latinas', 'assets/img/student/student_69d5a6b2c198c3.25979147.jpg', 1, 10, '2026-04-08 00:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'system_name', 'USTP E-Gallery'),
(2, 'default_class_year', '2029'),
(3, 'maintenance_mode', '0'),
(4, 'system_name', 'USTP E-Gallery'),
(5, 'default_class_year', '2030'),
(6, 'maintenance_mode', '0'),
(7, 'system_name', 'USTP E-Gallery'),
(8, 'default_class_year', '2030'),
(9, 'maintenance_mode', '0'),
(10, 'system_name', 'USTP E-Gallery'),
(11, 'default_class_year', '2030'),
(12, 'maintenance_mode', '0'),
(13, 'system_name', 'USTP E-Gallery'),
(14, 'default_class_year', '2030'),
(15, 'maintenance_mode', '0'),
(16, 'system_name', 'USTP E-Gallery'),
(17, 'default_class_year', '2031'),
(18, 'maintenance_mode', '0');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `recovery_email` varchar(100) NOT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `uuid`, `username`, `password`, `role`, `recovery_email`, `two_factor_enabled`, `dateCreated`) VALUES
(2, 'd6ec2460-c7ed-4dae-b97a-3c588e4bfa1a', '2024304880', '$2y$10$TP.KoAaiLDsuVG5YVlJOk.Drq8ZYHPRa7NuptUgGZEVmKA1.3UzIG', 'user', '', 0, '2026-03-27 04:31:35'),
(3, '77aea5f2-efca-495e-b9f1-0c8929908a36', 'admin1', '$2y$10$5UmWyhBi93kU.55ug4V0r.uKft/bkwmsJSgA5bTkCxsos3QLZpQYS', 'admin', '', 0, '2026-03-27 07:21:05'),
(4, '85c45634-6ec6-4aa7-9ab2-587ec34360d9', 'admin2', '$2y$10$HUVAhmlyM1zElNiPJk27EeIJFM6leX/i830JbEOK3B0N1/jRFy5Uu', 'admin', '', 0, '2026-03-31 16:02:04'),
(7, '48a4bd7c-ef4c-455f-961c-77c4cc304ed8', '2024304990', '$2y$10$NvuLlJZpb2p3VtGrjZ0I9uE4MBs3lB5aAzjfsYLA3ubjImQnUtUmu', 'user', '', 0, '2026-04-04 09:41:01'),
(8, '244d5612-b20b-4f4b-99bf-6bfa1dc2ab8d', 'admin3', '$2y$10$W7/B2IJ63oiIpLo4vrz4DOMDP34N38MgzpYMxUOpB1hiWEpV7fbTW', 'admin', '', 0, '2026-04-04 09:57:28'),
(9, '67f51324-34dc-436a-a8ae-41e3da80f4aa', '2024304770', '$2y$10$/MXGVZyHhbKwTt43fCfoKuyvZ4RE5QCWpxEPayWoWEmWSszDE8tlS', 'user', '', 0, '2026-04-04 09:57:28'),
(10, 'd90565c2-319b-4939-bb76-768571b079e2', 'admin4', '$2y$10$Vzp7QBC.6DQ9gv8QLYKYC.7cfJNUPLPTyKHNwsPH.IURMOF8kaMmC', 'admin', '', 0, '2026-04-04 10:02:46'),
(11, 'a2756c5f-ab12-4bae-8316-14b30d425447', '2024304660', '$2y$10$.GXkm67E7wj5xsUCK7rDPui8kDcmdzHAEkb8RyOLGo0zL9/9BmSZG', 'user', '', 0, '2026-04-04 11:55:12'),
(12, '1ac2a7ca-cccf-40c6-94fe-563da6abb19f', '2024304550', '$2y$10$jPdH2uRT9lgJJoQbrLw92u6kkWyItbFZyI3ePiW.6.2kGwaQX0V9W', 'user', '', 0, '2026-04-04 12:45:00'),
(13, 'd6afef41-e99f-4c21-8c87-c75a46d31135', '2024305222', '$2y$10$IRmnKgd2tOpiY3E4t7ZVi.g7UhRQz6UrpJWMyd2Z4JPPm2SExEdCe', 'user', '', 0, '2026-04-08 00:49:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_visits`
--

CREATE TABLE `user_visits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_visits`
--

INSERT INTO `user_visits` (`id`, `user_id`, `visit_time`) VALUES
(1, 9, '2026-04-04 11:57:53'),
(2, 7, '2026-04-04 12:07:13'),
(3, 2, '2026-04-04 12:39:21'),
(4, 12, '2026-04-04 12:45:45'),
(5, 2, '2026-04-06 14:57:39'),
(6, 2, '2026-04-06 17:24:59'),
(7, 2, '2026-04-06 18:36:01'),
(8, 2, '2026-04-07 12:37:34'),
(9, 2, '2026-04-07 13:01:52'),
(10, 2, '2026-04-07 13:43:06'),
(11, 2, '2026-04-07 14:18:07'),
(12, 2, '2026-04-07 14:34:42'),
(13, 2, '2026-04-07 14:54:22'),
(14, 7, '2026-04-07 16:39:06'),
(15, 2, '2026-04-08 00:19:56'),
(16, 2, '2026-04-08 00:33:51'),
(17, 9, '2026-04-08 00:48:29'),
(18, 13, '2026-04-08 00:50:09'),
(19, 2, '2026-04-08 01:53:37'),
(20, 2, '2026-04-08 01:57:36'),
(21, 2, '2026-04-08 10:17:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `class_years`
--
ALTER TABLE `class_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_visits`
--
ALTER TABLE `user_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `class_years`
--
ALTER TABLE `class_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_visits`
--
ALTER TABLE `user_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `fk_prog_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `fk_sec_prog` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `fk_student_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_prog` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_sec` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `student_profiles_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`),
  ADD CONSTRAINT `student_profiles_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`),
  ADD CONSTRAINT `student_profiles_ibfk_5` FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_visits`
--
ALTER TABLE `user_visits`
  ADD CONSTRAINT `user_visits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
