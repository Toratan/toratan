-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: toratan
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.12.04.2

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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
INSERT INTO `folders` VALUES (0,0,'0','ROOT',NULL,0,0,0,'2013-11-20 20:19:40','2013-11-20 20:19:40'),(3,0,'ad179f7f4255acfa37a62521f248839ec5fe3f57','lkkm','\0',0,0,0,'2014-01-08 23:58:15','2014-01-08 23:58:16'),(6,0,'c4abcdce541b22950fdcee3ab88240094da914fc','l;smla','\0',1,0,0,'2014-02-05 17:01:42','2014-03-07 08:01:33'),(11,0,'c4abcdce541b22950fdcee3ab88240094da914fc','Public','\0',1,0,0,'2014-02-05 17:38:20','2014-03-07 08:01:33'),(12,11,'c4abcdce541b22950fdcee3ab88240094da914fc','smkmnsa','\0',0,0,0,'2014-02-05 17:39:41','2014-03-07 14:31:27'),(13,0,'c4abcdce541b22950fdcee3ab88240094da914fc','la','\0',0,0,1,'2014-02-05 17:55:52','2014-04-11 17:53:59'),(15,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','lsmalmsa','\0',1,0,0,'2014-02-11 06:21:25','2014-03-07 12:25:02'),(18,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','testing-pull-interval','\0',1,0,0,'2014-02-11 07:12:14','2014-02-11 08:17:26'),(20,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','new folder','\0',1,0,0,'2014-02-11 08:29:34','2014-02-11 08:36:48'),(21,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','knk','\0',0,0,0,'2014-02-11 08:36:55','2014-02-11 12:26:08'),(23,12,'c4abcdce541b22950fdcee3ab88240094da914fc','KLNNKA','\0',1,0,0,'2014-03-06 20:28:13','2014-03-07 08:01:33'),(24,23,'c4abcdce541b22950fdcee3ab88240094da914fc','xkxnslna','\0',1,0,0,'2014-03-06 20:28:22','2014-03-07 08:01:33'),(25,0,'c4abcdce541b22950fdcee3ab88240094da914fc','ksnksan','\0',0,0,0,'2014-03-07 08:30:27','2014-03-07 08:30:27'),(26,0,'c4abcdce541b22950fdcee3ab88240094da914fc','lsmlmsa','\0',0,1,0,'2014-03-07 09:04:09','2014-03-07 09:04:34'),(27,0,'c4abcdce541b22950fdcee3ab88240094da914fc','NEW FOLDER','\0',0,0,0,'2014-04-11 18:21:24','2014-04-11 18:21:24');
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
    
    IF OLD.is_public <> NEW.is_public THEN 
        
        
        UPDATE `notes` SET `is_public`=NEW.is_public, `updated_at`=NOW() WHERE parent_id = NEW.folder_id;
        
        UPDATE `links` SET `is_public`=NEW.is_public, `updated_at`=NOW() WHERE parent_id = NEW.folder_id;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
INSERT INTO `links` VALUES (1,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','lnlsaln','knkn',0,0,0,'2014-02-05 16:05:33','2014-03-06 23:40:40'),(2,0,'c4abcdce541b22950fdcee3ab88240094da914fc','lsmnamn','lml',0,0,0,'2014-02-05 16:05:48','2014-03-06 23:40:40'),(4,11,'c4abcdce541b22950fdcee3ab88240094da914fc','goo','',1,0,0,'2014-02-05 17:38:50','2014-03-07 11:30:31'),(5,0,'c4abcdce541b22950fdcee3ab88240094da914fc','lmnas','knkn',0,0,0,'2014-02-10 18:17:09','2014-03-06 23:40:40'),(6,24,'c4abcdce541b22950fdcee3ab88240094da914fc','title','fuck',1,1,0,'2014-03-07 14:41:56','2014-03-07 14:42:12'),(7,6,'c4abcdce541b22950fdcee3ab88240094da914fc','LINK TITLE','BODY',1,1,0,'2014-04-11 18:30:29','2014-04-11 18:30:34');
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
  `sender_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
  `reciever_id` varchar(250) CHARACTER SET utf8 COLLATE utf8_persian_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL,
  `is_archive` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`,`reciever_id`),
  KEY `reciever_id` (`reciever_id`),
  CONSTRAINT `messages_ibfk_5` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `messages_ibfk_6` FOREIGN KEY (`reciever_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,0,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ... Your note\'s title here ... Your note\'s title here ... Your note\'s title here ... Your note\'s title here ... Your note\'s title here ...','<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>',0,0,0,'2014-02-05 16:03:59','2014-03-07 14:54:39'),(4,11,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ... [PUBLIC]','<p>Your note&#39;s body here ...</p>',1,0,0,'2014-02-05 17:38:35','2014-03-07 11:30:31'),(5,11,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ... [PRIVATE]','<p>Your note&#39;s body here ...</p>',0,0,0,'2014-02-05 17:38:44','2014-03-07 14:54:04'),(6,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','Your note\'s title here ...','<p>Your note&#39;s body here ...</p>',0,0,0,'2014-02-06 07:06:11','2014-03-06 23:40:40'),(7,24,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ...','<p>Your note&#39;s body here ...</p>',1,0,0,'2014-03-06 20:28:38','2014-03-07 14:54:15'),(8,6,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ...','<p>Your note&#39;s body here ...</p>',1,0,0,'2014-04-11 18:24:03','2014-04-11 18:24:03');
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
  `is_visible` int(1) unsigned NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `triggered_user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (2,'ad179f7f4255acfa37a62521f248839ec5fe3f57',0,'folder',3,1,'2014-01-08 23:58:16'),(6,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'link',1,1,'2014-02-05 16:05:35'),(7,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'link',2,1,'2014-02-05 16:05:50'),(12,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'folder',13,1,'2014-02-05 17:55:54'),(14,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'folder',15,1,'2014-02-11 06:22:16'),(15,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'folder',16,0,'2014-02-11 06:40:08'),(17,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'folder',18,1,'2014-02-11 07:13:55'),(19,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'folder',20,1,'2014-02-11 08:29:37'),(21,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'note',6,1,'2014-02-11 08:49:46'),(57,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'folder',6,1,'2014-03-07 08:00:29'),(58,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'folder',11,1,'2014-03-07 08:00:31'),(80,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'note',7,1,'2014-03-07 14:54:15');
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
INSERT INTO `profiles` VALUES ('65e24b6874a4da6aea4413ca2fab916dd72083bd','O:8:\"stdClass\":3:{s:13:\"notifications\";O:8:\"stdClass\":1:{s:4:\"pull\";O:8:\"stdClass\":1:{s:9:\"last_time\";s:20:\"Apr-11-2014 17:53:02\";}}s:7:\"profile\";O:8:\"stdClass\":2:{s:6:\"status\";i:1;s:6:\"avatar\";O:8:\"stdClass\":4:{s:9:\"activated\";s:6:\"custom\";s:8:\"gravatar\";O:8:\"stdClass\":2:{s:3:\"set\";i:1;s:2:\"id\";s:21:\"b.g.dariush@gmail.com\";}s:8:\"facebook\";O:8:\"stdClass\":2:{s:3:\"set\";i:1;s:2:\"id\";s:17:\"dariush.hasanpoor\";}s:6:\"custom\";O:8:\"stdClass\":3:{s:3:\"set\";i:1;s:12:\"origin_image\";s:63:\"/access/img/upload/84e05d8ae2306ed0b83b5756d8451af82257e8be.jpg\";s:11:\"thumb_image\";s:73:\"/access/img/upload/thumbnail/2e8718aad7fd6b4f89525854e611d76f0815532e.jpg\";}}}s:3:\"rte\";O:8:\"stdClass\":1:{s:4:\"type\";s:7:\"classic\";}}','foo','','foo',0,0,0,1,'','','','','','','','','2014-01-07 19:33:34','2014-04-11 17:53:30'),('ad179f7f4255acfa37a62521f248839ec5fe3f57','','','','',0,0,0,-1,'','','','','','','','','2014-01-07 20:00:27','2014-01-07 20:00:27'),('c4abcdce541b22950fdcee3ab88240094da914fc','O:8:\"stdClass\":3:{s:7:\"profile\";O:8:\"stdClass\":2:{s:6:\"avatar\";O:8:\"stdClass\":2:{s:6:\"custom\";O:8:\"stdClass\":3:{s:3:\"set\";i:1;s:12:\"origin_image\";s:63:\"/access/img/upload/bc164218f758cd3a59cd5f1cb98fd0563a327f69.jpg\";s:11:\"thumb_image\";s:73:\"/access/img/upload/thumbnail/e63d85d4ac1f7b4ea17def88436e9c2425b0e991.jpg\";}s:9:\"activated\";s:6:\"custom\";}s:6:\"status\";i:1;}s:13:\"notifications\";O:8:\"stdClass\":1:{s:4:\"pull\";O:8:\"stdClass\":1:{s:9:\"last_time\";s:20:\"Apr-11-2014 18:43:10\";}}s:3:\"rte\";O:8:\"stdClass\":1:{s:4:\"type\";s:7:\"classic\";}}','đᶐᵲ!ʯʃɧ','','ϦɐϨ₰αϰϸʘϑʁ',0,0,0,1,'','','','','','','','','2014-02-05 15:45:17','2014-04-11 18:43:10'),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','','','','',0,0,0,-1,'','','','','','','','','2014-01-07 20:00:46','2014-01-07 20:00:46');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sharing_queues`
--

DROP TABLE IF EXISTS `sharing_queues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sharing_queues` (
  `sharing_queue_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `folder_id` bigint(20) NOT NULL,
  PRIMARY KEY (`sharing_queue_id`),
  UNIQUE KEY `queue_id` (`folder_id`),
  CONSTRAINT `sharing_queues_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`folder_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sharing_queues`
--

LOCK TABLES `sharing_queues` WRITE;
/*!40000 ALTER TABLE `sharing_queues` DISABLE KEYS */;
INSERT INTO `sharing_queues` VALUES (2,12),(1,15);
/*!40000 ALTER TABLE `sharing_queues` ENABLE KEYS */;
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
INSERT INTO `subscribes` VALUES ('ad179f7f4255acfa37a62521f248839ec5fe3f57','65e24b6874a4da6aea4413ca2fab916dd72083bd',NULL),('c4abcdce541b22950fdcee3ab88240094da914fc','65e24b6874a4da6aea4413ca2fab916dd72083bd',NULL),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','65e24b6874a4da6aea4413ca2fab916dd72083bd','2014-01-07 20:17:00'),('65e24b6874a4da6aea4413ca2fab916dd72083bd','ad179f7f4255acfa37a62521f248839ec5fe3f57',NULL),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','ad179f7f4255acfa37a62521f248839ec5fe3f57','2014-01-07 20:17:00'),('65e24b6874a4da6aea4413ca2fab916dd72083bd','c4abcdce541b22950fdcee3ab88240094da914fc',NULL);
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
INSERT INTO `users` VALUES ('0','NULL','ROOT','NULL',NULL,'2013-11-20 20:19:41','2013-11-20 20:19:41'),('65e24b6874a4da6aea4413ca2fab916dd72083bd','b.g.dariush@gmail.com','dariush','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 19:33:34','2014-02-06 12:42:39'),('ad179f7f4255acfa37a62521f248839ec5fe3f57','a@m.com','a','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 20:00:27','2014-01-07 20:00:27'),('c4abcdce541b22950fdcee3ab88240094da914fc','badguy2580@yahoo.com','badguy2580yahoocom','7d024a27ab00ae473d8196b0860d82ae3b322e36',NULL,'2014-02-05 15:45:17','2014-02-05 15:45:17'),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','b@b.com','b','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 20:00:46','2014-01-07 20:00:46');
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

-- Dump completed on 2014-04-11 23:13:18
