-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 18, 2013 at 11:07 PM
-- Server version: 5.5.34
-- PHP Version: 5.3.10-1ubuntu3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `toratan`
--

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE IF NOT EXISTS `folders` (
  `folder_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `parent_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `owner_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `folder_title` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `folder_body` bit(1) DEFAULT NULL,
  `is_public` bit(1) NOT NULL DEFAULT b'0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`folder_id`),
  KEY `user_id` (`owner_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;


--
-- Triggers `folders`
--
DROP TRIGGER IF EXISTS `T_FOLDERS_PREVENT_ROOT_DELETION`;
DELIMITER //
CREATE TRIGGER `T_FOLDERS_PREVENT_ROOT_DELETION` BEFORE DELETE ON `folders`
 FOR EACH ROW BEGIN
  IF OLD.folder_id = 0 OR OLD.owner_id = 0 THEN 
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'cannot DELETE ROOT owing items';
  END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
CREATE TABLE IF NOT EXISTS `links` (
  `link_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `parent_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `owner_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `link_title` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `link_body` text COLLATE utf8_persian_ci NOT NULL,
  `is_public` bit(1) NOT NULL DEFAULT b'0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `parent_id` (`parent_id`,`owner_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `note_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `parent_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `owner_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `note_title` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `note_body` text COLLATE utf8_persian_ci NOT NULL,
  `is_public` bit(1) NOT NULL DEFAULT b'0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`note_id`),
  KEY `parent_id` (`parent_id`,`owner_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `email` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `username` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `username`, `password`, `created_at`, `updated_at`) VALUES
('0', 'NULL', 'ROOT', 'NULL', '2013-11-18 22:22:08', '2013-11-18 22:22:08');

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`folder_id`, `parent_id`, `owner_id`, `folder_title`, `folder_body`, `is_public`, `created_at`, `updated_at`) VALUES
('0', '', '0', 'ROOT', NULL, b'0', '2013-11-18 22:22:30', '2013-11-18 22:22:30');

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `T_USERS_PREVENT_ROOT_DELETION`;
DELIMITER //
CREATE TRIGGER `T_USERS_PREVENT_ROOT_DELETION` BEFORE DELETE ON `users`
 FOR EACH ROW BEGIN
  IF OLD.user_id = 0 THEN 
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'cannot DELETE ROOT user';
  END IF;
END
//
DELIMITER ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `folders`
--
ALTER TABLE `folders`
  ADD CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
