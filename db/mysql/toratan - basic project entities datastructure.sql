-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: toratan
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `executions`
--

DROP TABLE IF EXISTS `executions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `executions` (
  `time` float NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `executions`
--

LOCK TABLES `executions` WRITE;
/*!40000 ALTER TABLE `executions` DISABLE KEYS */;
INSERT INTO `executions` VALUES (0.06544,'2014-01-24 12:48:48'),(0.05207,'2014-01-24 12:48:49');
/*!40000 ALTER TABLE `executions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `folders` (
  `folder_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL,
  `owner_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `folder_title` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `folder_body` bit(1) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_trash` tinyint(1) NOT NULL DEFAULT '0',
  `is_archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`folder_id`),
  KEY `user_id` (`owner_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `folders_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
INSERT INTO `folders` VALUES (0,0,'0','ROOT',NULL,0,0,0,'2013-11-20 20:19:40','2013-11-20 20:19:40'),(2,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','dsad','\0',1,0,0,'2014-01-07 19:33:42','2014-01-08 23:17:21'),(3,0,'ad179f7f4255acfa37a62521f248839ec5fe3f57','lkkm','\0',1,0,0,'2014-01-08 23:58:15','2014-01-08 23:58:16');
/*!40000 ALTER TABLE `folders` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER T_FOLDERS_SHARE AFTER UPDATE ON folders FOR EACH ROW
BEGIN
    -- check if any publicity flag has been changed
    IF OLD.is_public <> NEW.is_public THEN 
        -- if so ?
        -- update the publicity of the sub-notes of current folder
        UPDATE `notes` SET `is_public`=NEW.is_public, `updated_at`=NOW() WHERE parent_id = NEW.folder_id;
        -- update the publicity of the sub-links of current folder
        UPDATE `links` SET `is_public`=NEW.is_public, `updated_at`=NOW() WHERE parent_id = NEW.folder_id;
		-- insert an notification
		IF NEW.is_public = 1 THEN
			INSERT INTO `toratan`.`notifications` 
				(`user_id`, `notification_type`, `item_table`, `item_id`, `created_at`) 
			VALUES 
				(NEW.owner_id, '0', 'folder', NEW.folder_id, NOW());
		END IF;
		IF NEW.is_public = 0 THEN
			DELETE FROM `toratan`.`notifications` WHERE `user_id` = NEW.owner_id AND `item_id` = NEW.folder_id AND `notification_type` = '0';
		END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER T_FOLDERS_PREVENT_ROOT_DELETION BEFORE DELETE ON folders FOR EACH ROW
BEGIN
  IF OLD.owner_id = '0' THEN 
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'CANNOT DELETE ROOT OWNING ITEMS';
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links` (
  `link_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL,
  `owner_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `link_title` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `link_body` text COLLATE utf8_persian_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_trash` tinyint(1) NOT NULL DEFAULT '0',
  `is_archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `parent_id` (`parent_id`,`owner_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `links_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `links_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
/*!40000 ALTER TABLE `links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `reciever_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `is_archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`,`reciever_id`),
  KEY `reciever_id` (`reciever_id`),
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `messages_ibfk_4` FOREIGN KEY (`reciever_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `note_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL,
  `owner_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `note_title` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `note_body` text COLLATE utf8_persian_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_trash` tinyint(1) NOT NULL DEFAULT '0',
  `is_archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`note_id`),
  KEY `parent_id` (`parent_id`,`owner_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `folders` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `notification_type` tinyint(1) NOT NULL DEFAULT '0',
  `item_table` varchar(50) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `triggered_user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'folder',2,'2014-01-08 23:17:21'),(2,'ad179f7f4255acfa37a62521f248839ec5fe3f57',0,'folder',3,'2014-01-08 23:58:16');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles` (
  `user_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `settings` text COLLATE utf8_persian_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `nick_name` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `birth_day` smallint(2) unsigned NOT NULL DEFAULT '0',
  `birth_month` smallint(2) unsigned NOT NULL DEFAULT '0',
  `birth_year` mediumint(4) unsigned NOT NULL DEFAULT '0',
  `is_male` tinyint(1) NOT NULL DEFAULT '-1',
  `intro` varchar(400) COLLATE utf8_persian_ci NOT NULL,
  `occu` varchar(400) COLLATE utf8_persian_ci NOT NULL,
  `edu` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `city` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `country` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `public_email` tinytext COLLATE utf8_persian_ci NOT NULL,
  `phone` tinytext COLLATE utf8_persian_ci NOT NULL,
  `site` tinytext COLLATE utf8_persian_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` VALUES ('65e24b6874a4da6aea4413ca2fab916dd72083bd','O:8:\"stdClass\":2:{s:13:\"notifications\";O:8:\"stdClass\":1:{s:4:\"pull\";O:8:\"stdClass\":1:{s:9:\"last_time\";s:20:\"Jan-24-2014 12:48:50\";}}s:7:\"profile\";O:8:\"stdClass\":2:{s:6:\"status\";i:1;s:6:\"avatar\";O:8:\"stdClass\":2:{s:9:\"activated\";s:6:\"custom\";s:6:\"custom\";O:8:\"stdClass\":3:{s:3:\"set\";i:1;s:12:\"origin_image\";s:63:\"/access/img/upload/21238d71524a70c4ef436e2645e438eaad2a95d1.jpg\";s:11:\"thumb_image\";s:73:\"/access/img/upload/thumbnail/7ccb178860d287bebee80ee396944d4c58351f6a.jpg\";}}}}','','','',0,0,0,1,'','','','','','','','','2014-01-07 19:33:34','2014-01-24 12:48:50'),('ad179f7f4255acfa37a62521f248839ec5fe3f57','','','','',0,0,0,-1,'','','','','','','','','2014-01-07 20:00:27','2014-01-07 20:00:27'),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','','','','',0,0,0,-1,'','','','','','','','','2014-01-07 20:00:46','2014-01-07 20:00:46');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscribes`
--

DROP TABLE IF EXISTS `subscribes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribes` (
  `followed` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `follower` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `last_notification_read_at` datetime DEFAULT NULL,
  UNIQUE KEY `pair_subscription` (`follower`,`followed`),
  KEY `followed` (`followed`,`follower`),
  CONSTRAINT `subscribes_ibfk_1` FOREIGN KEY (`followed`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subscribes_ibfk_2` FOREIGN KEY (`follower`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribes`
--

LOCK TABLES `subscribes` WRITE;
/*!40000 ALTER TABLE `subscribes` DISABLE KEYS */;
INSERT INTO `subscribes` VALUES ('ad179f7f4255acfa37a62521f248839ec5fe3f57','65e24b6874a4da6aea4413ca2fab916dd72083bd',NULL),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','65e24b6874a4da6aea4413ca2fab916dd72083bd','2014-01-07 20:17:00'),('65e24b6874a4da6aea4413ca2fab916dd72083bd','ad179f7f4255acfa37a62521f248839ec5fe3f57',NULL),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','ad179f7f4255acfa37a62521f248839ec5fe3f57','2014-01-07 20:17:00');
/*!40000 ALTER TABLE `subscribes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `email` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `username` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `deactivate_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('0','NULL','ROOT','NULL',NULL,'2013-11-20 20:19:41','2013-11-20 20:19:41'),('65e24b6874a4da6aea4413ca2fab916dd72083bd','b.g.dariush@gmail.com','dariush','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 19:33:34','2014-01-07 19:33:34'),('ad179f7f4255acfa37a62521f248839ec5fe3f57','a@m.com','a','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 20:00:27','2014-01-07 20:00:27'),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','b@b.com','b','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 20:00:46','2014-01-07 20:00:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER users_create_profile AFTER INSERT ON users
    FOR EACH ROW 
BEGIN
    INSERT INTO profiles (user_id, created_at, updated_at)
    VALUES (NEW.user_id, NOW(), NOW());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER T_USERS_PREVENT_ROOT_DELETION BEFORE DELETE ON users FOR EACH ROW
BEGIN
  IF OLD.user_id = '0' THEN 
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'CANNOT DELETE ROOT USER';
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-24 12:51:48
