-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2016 at 06:37 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `doit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inf_categories`
--

CREATE TABLE IF NOT EXISTS `inf_categories` (
`id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inf_categories`
--

INSERT INTO `inf_categories` (`id`, `name`) VALUES
(1, 'Shopping'),
(2, 'Intern'),
(3, 'Study'),
(4, 'Gaming'),
(5, 'Read News');

-- --------------------------------------------------------

--
-- Table structure for table `mst_users`
--

CREATE TABLE IF NOT EXISTS `mst_users` (
`id` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `access_token` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mst_users`
--

INSERT INTO `mst_users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `access_token`, `created_at`, `last_login`) VALUES
(1, 'imanuel', 'Imanuel', 'Pardosi', 'imanuel@kurio.co.id', '$2y$13$aacOH1eZgaO9/G6RL5W2suI0URFT./UQH7QSTMtLlupeJcYA.Sn6y', '$2y$13$i6blf0O6hZW2AhpjpGRdkOwx6gqDtIQMBz.w7d8EunOWZfLbdAPOa', '2016-04-12 16:39:05', '2016-04-14 11:37:10'),
(2, 'arie', 'Arie', 'Lizuardi', 'arie@kurio.co.id', '$2y$13$XiYAwo6yj2vhhIDKQM6EuOUptR/V3no1pl66FdMawK3F6HGN81CqS', '$2y$13$ndwOCF1xbHcVh9EodR90oOmcdVgX73LeKFqQxfR0utgeWHqvIG7Tq', '2016-04-12 16:42:27', '2016-04-14 11:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `trx_tasks`
--

CREATE TABLE IF NOT EXISTS `trx_tasks` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `task_status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trx_tasks`
--

INSERT INTO `trx_tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `due_date`, `created_time`, `last_updated`, `task_status`) VALUES
(5, 2, 1, 'Buy Apple', 'Buy 5 kg', '2016-04-14 11:32:37', '2016-04-13 00:00:00', '2016-04-13 00:00:00', 0),
(6, 2, 4, 'Playing Dota 2', 'Party with team', '2016-04-12 00:00:00', '2016-04-13 00:00:00', '2016-04-13 00:00:00', 0),
(7, 1, 1, 'Read news', 'Read news using kurio', '2016-04-12 20:00:00', '2016-04-14 11:33:14', '2016-04-14 11:33:31', 0),
(10, 1, 1, 'Doing intern test', 'Kurio test', '2016-04-14 21:32:44', '2016-04-14 11:33:17', '2016-04-14 11:33:29', 0),
(11, 2, 4, 'Playing Dota 2', 'Party', '2016-04-16 11:32:50', '2016-04-14 11:33:19', '2016-04-14 11:33:27', 0),
(16, 1, 3, 'Doing TA2', 'Meet up', '2016-04-15 11:32:48', '2016-04-14 11:33:22', '2016-04-14 11:33:24', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inf_categories`
--
ALTER TABLE `inf_categories`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_users`
--
ALTER TABLE `mst_users`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trx_tasks`
--
ALTER TABLE `trx_tasks`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inf_categories`
--
ALTER TABLE `inf_categories`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `mst_users`
--
ALTER TABLE `mst_users`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `trx_tasks`
--
ALTER TABLE `trx_tasks`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `trx_tasks`
--
ALTER TABLE `trx_tasks`
ADD CONSTRAINT `trx_tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `mst_users` (`id`),
ADD CONSTRAINT `trx_tasks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `inf_categories` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
