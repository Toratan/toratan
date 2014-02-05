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
INSERT INTO `executions` VALUES (0.06544,'2014-01-24 12:48:48'),(0.05207,'2014-01-24 12:48:49'),(0.01752,'2014-02-05 15:44:36'),(0.02754,'2014-02-05 15:44:37'),(0.02172,'2014-02-05 15:44:37'),(0.01902,'2014-02-05 15:44:41'),(0.01668,'2014-02-05 15:44:41'),(0.01282,'2014-02-05 15:44:45'),(0.01049,'2014-02-05 15:44:46'),(0.01434,'2014-02-05 15:44:49'),(0.0055,'2014-02-05 15:44:52'),(0.0058,'2014-02-05 15:44:54'),(0.01339,'2014-02-05 15:45:17'),(0.01376,'2014-02-05 15:45:19'),(0.01768,'2014-02-05 15:45:29'),(0.02109,'2014-02-05 15:45:29'),(0.02239,'2014-02-05 15:45:29'),(0.01221,'2014-02-05 15:45:30'),(0.01112,'2014-02-05 15:45:30'),(0.02344,'2014-02-05 15:45:31'),(0.01026,'2014-02-05 15:45:33'),(0.01384,'2014-02-05 15:45:35'),(0.02519,'2014-02-05 15:45:35'),(0.07306,'2014-02-05 15:45:37'),(0.01829,'2014-02-05 15:45:37'),(0.01366,'2014-02-05 15:46:40'),(0.02071,'2014-02-05 15:46:41'),(0.01737,'2014-02-05 15:46:44'),(0.02584,'2014-02-05 15:46:44'),(0.01782,'2014-02-05 15:46:45'),(0.02479,'2014-02-05 15:46:45'),(0.06889,'2014-02-05 15:46:47'),(0.01701,'2014-02-05 15:46:47'),(0.01294,'2014-02-05 15:48:23'),(0.02055,'2014-02-05 15:48:23'),(0.01606,'2014-02-05 15:48:26'),(0.02102,'2014-02-05 15:48:26'),(0.02074,'2014-02-05 15:48:27'),(0.01339,'2014-02-05 15:48:30'),(0.02043,'2014-02-05 15:48:30'),(0.0451,'2014-02-05 15:48:31'),(0.01727,'2014-02-05 15:48:31'),(0.01373,'2014-02-05 15:48:49'),(0.02219,'2014-02-05 15:48:49'),(0.01499,'2014-02-05 15:48:51'),(0.0178,'2014-02-05 15:48:51'),(0.0212,'2014-02-05 15:48:51'),(0.01322,'2014-02-05 15:48:53'),(0.02145,'2014-02-05 15:48:53'),(0.08731,'2014-02-05 15:48:54'),(0.01702,'2014-02-05 15:48:54'),(0.01866,'2014-02-05 15:49:31'),(0.01499,'2014-02-05 15:49:34'),(0.0199,'2014-02-05 15:49:34'),(0.01449,'2014-02-05 15:49:55'),(0.01631,'2014-02-05 15:50:19'),(0.02526,'2014-02-05 15:50:19'),(0.01547,'2014-02-05 15:50:21'),(0.02125,'2014-02-05 15:50:21'),(0.01544,'2014-02-05 15:50:22'),(0.01938,'2014-02-05 15:50:22'),(0.01839,'2014-02-05 15:52:02'),(0.01958,'2014-02-05 15:52:03'),(0.01518,'2014-02-05 15:53:13'),(0.01942,'2014-02-05 15:53:13'),(0.06815,'2014-02-05 15:53:14'),(0.01766,'2014-02-05 15:53:15'),(0.02624,'2014-02-05 15:53:20'),(0.03043,'2014-02-05 15:53:20'),(0.01361,'2014-02-05 15:53:23'),(0.02005,'2014-02-05 15:53:23'),(0.01556,'2014-02-05 15:53:24'),(0.02327,'2014-02-05 15:53:25'),(0.01533,'2014-02-05 15:59:20'),(0.02117,'2014-02-05 15:59:20'),(0.01637,'2014-02-05 16:03:54'),(0.0135,'2014-02-05 16:03:56'),(0.0493,'2014-02-05 16:03:58'),(0.0126,'2014-02-05 16:03:59'),(0.01472,'2014-02-05 16:04:02'),(0.01492,'2014-02-05 16:04:04'),(0.02007,'2014-02-05 16:04:04'),(0.01554,'2014-02-05 16:04:29'),(0.03689,'2014-02-05 16:04:30'),(0.01728,'2014-02-05 16:04:33'),(0.02829,'2014-02-05 16:04:34'),(0.01386,'2014-02-05 16:04:40'),(0.02513,'2014-02-05 16:04:40'),(0.01781,'2014-02-05 16:05:02'),(0.01374,'2014-02-05 16:05:06'),(0.02038,'2014-02-05 16:05:06'),(0.02047,'2014-02-05 16:05:06'),(0.01717,'2014-02-05 16:05:28'),(0.02246,'2014-02-05 16:05:28'),(0.02245,'2014-02-05 16:05:28'),(0.01004,'2014-02-05 16:05:30'),(0.01938,'2014-02-05 16:05:30'),(0.01975,'2014-02-05 16:05:31'),(0.01518,'2014-02-05 16:05:33'),(0.02187,'2014-02-05 16:05:33'),(0.01852,'2014-02-05 16:05:33'),(0.01661,'2014-02-05 16:05:35'),(0.01921,'2014-02-05 16:05:35'),(0.02025,'2014-02-05 16:05:36'),(0.0118,'2014-02-05 16:05:45'),(0.0149,'2014-02-05 16:05:48'),(0.01506,'2014-02-05 16:05:50'),(0.01585,'2014-02-05 16:05:51'),(0.02033,'2014-02-05 16:05:51'),(0.01908,'2014-02-05 16:07:46'),(0.02841,'2014-02-05 16:07:46'),(0.02127,'2014-02-05 16:09:21'),(0.02452,'2014-02-05 16:09:21'),(0.01724,'2014-02-05 16:10:28'),(0.0197,'2014-02-05 16:10:28'),(0.02324,'2014-02-05 16:10:32'),(0.022,'2014-02-05 16:10:32'),(0.01753,'2014-02-05 16:10:35'),(0.02513,'2014-02-05 16:10:35'),(0.01786,'2014-02-05 16:11:02'),(0.01958,'2014-02-05 16:11:02'),(0.01725,'2014-02-05 16:11:34'),(0.01856,'2014-02-05 16:11:34'),(0.01815,'2014-02-05 16:11:48'),(0.01853,'2014-02-05 16:11:48'),(0.01754,'2014-02-05 16:12:13'),(0.02384,'2014-02-05 16:12:14'),(0.0159,'2014-02-05 16:13:03'),(0.01922,'2014-02-05 16:13:03'),(0.02151,'2014-02-05 16:13:22'),(0.02874,'2014-02-05 16:13:22'),(0.01696,'2014-02-05 16:13:35'),(0.01994,'2014-02-05 16:13:35'),(0.02015,'2014-02-05 16:13:57'),(0.02002,'2014-02-05 16:13:57'),(0.02038,'2014-02-05 16:14:04'),(0.0234,'2014-02-05 16:14:04'),(0.02033,'2014-02-05 16:14:14'),(0.02258,'2014-02-05 16:14:15'),(0.01943,'2014-02-05 16:15:54'),(0.03031,'2014-02-05 16:15:54'),(0.01692,'2014-02-05 16:16:07'),(0.02571,'2014-02-05 16:16:07'),(0.0167,'2014-02-05 16:16:08'),(0.022,'2014-02-05 16:16:08'),(0.01867,'2014-02-05 16:16:24'),(0.0201,'2014-02-05 16:16:24'),(0.01611,'2014-02-05 16:16:40'),(0.02027,'2014-02-05 16:16:41'),(0.01113,'2014-02-05 16:16:42'),(0.02003,'2014-02-05 16:16:42'),(0.01215,'2014-02-05 16:16:56'),(0.01083,'2014-02-05 16:17:13'),(0.17545,'2014-02-05 16:17:16'),(0.0149,'2014-02-05 16:17:20'),(0.01593,'2014-02-05 16:17:31'),(0.01601,'2014-02-05 16:18:10'),(0.01794,'2014-02-05 16:18:22'),(0.01728,'2014-02-05 16:18:27'),(0.01674,'2014-02-05 16:19:12'),(0.01709,'2014-02-05 16:19:27'),(0.01747,'2014-02-05 16:19:33'),(0.01893,'2014-02-05 16:19:49'),(0.0157,'2014-02-05 16:20:20'),(0.01508,'2014-02-05 16:20:25'),(0.01977,'2014-02-05 16:20:35'),(0.01612,'2014-02-05 16:20:44'),(0.01599,'2014-02-05 16:20:50'),(0.01496,'2014-02-05 16:20:58'),(0.02013,'2014-02-05 16:21:03'),(0.01497,'2014-02-05 16:21:16'),(0.01795,'2014-02-05 16:21:40'),(0.06166,'2014-02-05 16:21:50'),(0.01705,'2014-02-05 16:22:08'),(0.01417,'2014-02-05 16:22:11'),(0.01554,'2014-02-05 16:22:13'),(0.01813,'2014-02-05 16:22:13'),(0.01558,'2014-02-05 16:22:16'),(0.01543,'2014-02-05 16:22:25'),(0.01777,'2014-02-05 16:22:27'),(0.05927,'2014-02-05 16:22:32'),(0.05142,'2014-02-05 16:22:35'),(0.0157,'2014-02-05 16:22:36'),(0.01529,'2014-02-05 16:22:36'),(0.02157,'2014-02-05 16:24:05'),(0.01953,'2014-02-05 16:24:08'),(0.06094,'2014-02-05 16:24:16'),(0.01685,'2014-02-05 16:24:24'),(0.01462,'2014-02-05 16:24:26'),(0.01457,'2014-02-05 16:25:02'),(0.01964,'2014-02-05 16:25:07'),(0.01943,'2014-02-05 16:25:13'),(0.01512,'2014-02-05 16:25:55'),(0.02622,'2014-02-05 16:26:16'),(0.01689,'2014-02-05 16:26:40'),(0.01842,'2014-02-05 16:26:41'),(0.01948,'2014-02-05 16:27:57'),(0.01661,'2014-02-05 16:28:04'),(0.02014,'2014-02-05 16:28:08'),(0.01506,'2014-02-05 16:28:13'),(0.01706,'2014-02-05 16:28:21'),(0.02307,'2014-02-05 16:28:35'),(0.01711,'2014-02-05 16:28:46'),(0.01658,'2014-02-05 16:29:17'),(0.0186,'2014-02-05 16:29:22'),(0.01573,'2014-02-05 16:29:27'),(0.01469,'2014-02-05 16:29:43'),(0.01579,'2014-02-05 16:30:10'),(0.0167,'2014-02-05 16:30:44'),(0.01478,'2014-02-05 16:30:46'),(0.01588,'2014-02-05 16:30:51'),(0.01738,'2014-02-05 16:30:56'),(0.01745,'2014-02-05 16:31:11'),(0.01592,'2014-02-05 16:31:52'),(0.01923,'2014-02-05 16:31:58'),(0.01736,'2014-02-05 16:32:05'),(0.01475,'2014-02-05 16:32:40'),(0.01368,'2014-02-05 16:32:41'),(0.04226,'2014-02-05 16:32:43'),(0.01533,'2014-02-05 16:32:45'),(0.02077,'2014-02-05 16:33:05'),(0.0191,'2014-02-05 16:33:09'),(0.01736,'2014-02-05 16:33:11'),(0.01465,'2014-02-05 16:33:13'),(0.02133,'2014-02-05 16:33:22'),(0.01568,'2014-02-05 16:33:25'),(0.01573,'2014-02-05 16:33:26'),(0.00976,'2014-02-05 16:34:02'),(0.02068,'2014-02-05 16:34:10'),(0.01295,'2014-02-05 16:34:12'),(0.02002,'2014-02-05 16:34:24'),(0.01842,'2014-02-05 16:34:26'),(0.02158,'2014-02-05 16:34:36'),(0.02116,'2014-02-05 16:34:38'),(0.01495,'2014-02-05 16:34:40'),(0.01698,'2014-02-05 16:36:08'),(0.0224,'2014-02-05 16:39:23'),(0.02047,'2014-02-05 16:39:26'),(0.02072,'2014-02-05 16:39:41'),(0.01603,'2014-02-05 16:39:49'),(0.00602,'2014-02-05 16:40:05'),(0.0057,'2014-02-05 16:40:07'),(0.01321,'2014-02-05 16:40:12'),(0.01565,'2014-02-05 16:40:15'),(0.01586,'2014-02-05 16:41:19'),(0.01357,'2014-02-05 16:41:23'),(0.01426,'2014-02-05 16:41:30'),(0.01528,'2014-02-05 16:41:37'),(0.01771,'2014-02-05 16:41:45'),(0.01411,'2014-02-05 16:41:47'),(0.0152,'2014-02-05 16:43:08'),(0.01383,'2014-02-05 16:43:10'),(0.01738,'2014-02-05 16:43:16'),(0.01485,'2014-02-05 16:44:20'),(0.01469,'2014-02-05 16:45:29'),(0.01585,'2014-02-05 16:45:46'),(0.01488,'2014-02-05 16:46:10'),(0.01633,'2014-02-05 16:46:16'),(0.01526,'2014-02-05 16:46:18'),(0.01465,'2014-02-05 16:46:25'),(0.01352,'2014-02-05 16:46:27'),(0.01591,'2014-02-05 16:46:28'),(0.01365,'2014-02-05 16:46:29'),(0.01246,'2014-02-05 16:46:31'),(0.01366,'2014-02-05 16:46:33'),(0.01586,'2014-02-05 16:46:40'),(0.01408,'2014-02-05 16:46:41'),(0.01415,'2014-02-05 16:46:43'),(0.01863,'2014-02-05 16:46:45'),(0.02014,'2014-02-05 16:47:44'),(0.02056,'2014-02-05 16:47:46'),(0.01841,'2014-02-05 16:48:20'),(0.0221,'2014-02-05 16:48:36'),(0.01695,'2014-02-05 16:48:50'),(0.0189,'2014-02-05 16:51:58'),(0.01902,'2014-02-05 16:52:02'),(0.01745,'2014-02-05 16:52:03'),(0.01634,'2014-02-05 16:52:04'),(0.0156,'2014-02-05 16:52:05'),(0.01498,'2014-02-05 16:52:45'),(0.0163,'2014-02-05 16:53:17'),(0.01656,'2014-02-05 16:53:20'),(0.01634,'2014-02-05 16:53:22'),(0.01504,'2014-02-05 16:53:24'),(0.01642,'2014-02-05 16:53:25'),(0.01357,'2014-02-05 16:53:25'),(0.017,'2014-02-05 16:53:25'),(0.01552,'2014-02-05 16:53:28'),(0.00587,'2014-02-05 16:53:35'),(0.00554,'2014-02-05 16:53:37'),(0.01572,'2014-02-05 16:55:29'),(0.01694,'2014-02-05 16:56:27'),(0.01424,'2014-02-05 16:59:07'),(0.01594,'2014-02-05 16:59:09'),(0.01435,'2014-02-05 16:59:10'),(0.0137,'2014-02-05 16:59:13'),(0.01505,'2014-02-05 16:59:15'),(0.01784,'2014-02-05 17:01:20'),(0.02039,'2014-02-05 17:01:23'),(0.01589,'2014-02-05 17:01:25'),(0.01535,'2014-02-05 17:01:28'),(0.0156,'2014-02-05 17:01:30'),(0.01509,'2014-02-05 17:01:31'),(0.01534,'2014-02-05 17:01:33'),(0.01355,'2014-02-05 17:01:36'),(0.0145,'2014-02-05 17:01:37'),(0.0122,'2014-02-05 17:01:40'),(0.01606,'2014-02-05 17:01:42'),(0.01664,'2014-02-05 17:01:45'),(0.01476,'2014-02-05 17:01:46'),(0.01877,'2014-02-05 17:01:50'),(0.01683,'2014-02-05 17:01:58'),(0.01922,'2014-02-05 17:01:59'),(0.01807,'2014-02-05 17:02:01'),(0.01673,'2014-02-05 17:02:01'),(0.01722,'2014-02-05 17:02:04'),(0.01805,'2014-02-05 17:02:04'),(0.01718,'2014-02-05 17:02:04'),(0.01815,'2014-02-05 17:02:06'),(0.01335,'2014-02-05 17:02:10'),(0.01719,'2014-02-05 17:02:13'),(0.01371,'2014-02-05 17:02:25'),(0.01398,'2014-02-05 17:02:29'),(0.01384,'2014-02-05 17:02:30'),(0.01435,'2014-02-05 17:02:33'),(0.01461,'2014-02-05 17:02:33'),(0.0155,'2014-02-05 17:02:34'),(0.01484,'2014-02-05 17:02:35'),(0.01323,'2014-02-05 17:02:41'),(0.01673,'2014-02-05 17:02:49'),(0.0137,'2014-02-05 17:02:50'),(0.01532,'2014-02-05 17:02:51'),(0.01676,'2014-02-05 17:02:53'),(0.0172,'2014-02-05 17:02:58'),(0.04192,'2014-02-05 17:03:00'),(0.01645,'2014-02-05 17:03:05'),(0.02077,'2014-02-05 17:03:08'),(0.0171,'2014-02-05 17:03:09'),(0.01571,'2014-02-05 17:03:11'),(0.01522,'2014-02-05 17:03:15'),(0.01604,'2014-02-05 17:03:18'),(0.01873,'2014-02-05 17:03:18'),(0.01626,'2014-02-05 17:03:36'),(0.01736,'2014-02-05 17:03:42'),(0.01612,'2014-02-05 17:03:44'),(0.0139,'2014-02-05 17:03:53'),(0.01559,'2014-02-05 17:03:55'),(0.08287,'2014-02-05 17:03:57'),(0.01391,'2014-02-05 17:03:59'),(0.01227,'2014-02-05 17:04:00'),(0.01473,'2014-02-05 17:04:02'),(0.01592,'2014-02-05 17:04:05'),(0.01356,'2014-02-05 17:04:08'),(0.0152,'2014-02-05 17:04:18'),(0.01812,'2014-02-05 17:04:30'),(0.01625,'2014-02-05 17:04:31'),(0.01856,'2014-02-05 17:04:32'),(0.01693,'2014-02-05 17:04:39'),(0.01709,'2014-02-05 17:04:39'),(0.02467,'2014-02-05 17:06:09'),(0.01769,'2014-02-05 17:06:11'),(0.01975,'2014-02-05 17:07:19'),(0.01679,'2014-02-05 17:07:20'),(0.01669,'2014-02-05 17:07:21'),(0.01509,'2014-02-05 17:07:22'),(0.01753,'2014-02-05 17:09:14'),(0.01431,'2014-02-05 17:09:17'),(0.01422,'2014-02-05 17:09:24'),(0.01586,'2014-02-05 17:09:25'),(0.01639,'2014-02-05 17:09:28'),(0.0204,'2014-02-05 17:10:07'),(0.01685,'2014-02-05 17:10:08'),(0.01256,'2014-02-05 17:10:13'),(0.01612,'2014-02-05 17:10:16'),(0.01624,'2014-02-05 17:10:24'),(0.01493,'2014-02-05 17:10:25'),(0.01653,'2014-02-05 17:12:23'),(0.01622,'2014-02-05 17:12:25'),(0.01386,'2014-02-05 17:12:39'),(0.01341,'2014-02-05 17:12:41'),(0.01747,'2014-02-05 17:12:42'),(0.01696,'2014-02-05 17:12:45'),(0.0187,'2014-02-05 17:13:30'),(0.01668,'2014-02-05 17:14:47'),(0.01176,'2014-02-05 17:15:33'),(0.01776,'2014-02-05 17:17:24'),(0.0171,'2014-02-05 17:17:35'),(0.01555,'2014-02-05 17:17:37'),(0.01668,'2014-02-05 17:17:38'),(0.01745,'2014-02-05 17:17:39'),(0.01385,'2014-02-05 17:17:41'),(0.01439,'2014-02-05 17:17:42'),(0.01606,'2014-02-05 17:17:44'),(0.01532,'2014-02-05 17:17:44'),(0.01503,'2014-02-05 17:17:45'),(0.0197,'2014-02-05 17:17:58'),(0.01862,'2014-02-05 17:17:59'),(0.02057,'2014-02-05 17:18:00'),(0.02002,'2014-02-05 17:18:00'),(0.01771,'2014-02-05 17:18:01'),(0.02043,'2014-02-05 17:18:17'),(0.01776,'2014-02-05 17:18:21'),(0.01765,'2014-02-05 17:18:21'),(0.0142,'2014-02-05 17:18:26'),(0.01779,'2014-02-05 17:19:52'),(0.01584,'2014-02-05 17:20:43'),(0.01606,'2014-02-05 17:20:44'),(0.0167,'2014-02-05 17:21:03'),(0.02304,'2014-02-05 17:23:07'),(0.02177,'2014-02-05 17:24:34'),(0.02403,'2014-02-05 17:24:34'),(0.02246,'2014-02-05 17:25:46'),(0.02011,'2014-02-05 17:26:29'),(0.01389,'2014-02-05 17:26:30'),(0.01538,'2014-02-05 17:26:32'),(0.01617,'2014-02-05 17:26:35'),(0.01587,'2014-02-05 17:26:36'),(0.01559,'2014-02-05 17:26:38'),(0.01755,'2014-02-05 17:26:42'),(0.01631,'2014-02-05 17:26:46'),(0.01718,'2014-02-05 17:26:58'),(0.01713,'2014-02-05 17:27:06'),(0.01547,'2014-02-05 17:27:08'),(0.01597,'2014-02-05 17:27:10'),(0.01612,'2014-02-05 17:27:14'),(0.01659,'2014-02-05 17:27:20'),(0.01556,'2014-02-05 17:27:59'),(0.01485,'2014-02-05 17:28:00'),(0.01461,'2014-02-05 17:28:01'),(0.01613,'2014-02-05 17:28:02'),(0.01703,'2014-02-05 17:28:06'),(0.02828,'2014-02-05 17:32:59'),(0.0203,'2014-02-05 17:33:41'),(0.01812,'2014-02-05 17:33:41'),(0.01825,'2014-02-05 17:33:58'),(0.0167,'2014-02-05 17:33:58'),(0.01976,'2014-02-05 17:34:10'),(0.01479,'2014-02-05 17:34:15'),(0.02004,'2014-02-05 17:34:47'),(0.01788,'2014-02-05 17:34:47'),(0.02167,'2014-02-05 17:35:02'),(0.02041,'2014-02-05 17:35:10'),(0.01691,'2014-02-05 17:35:13'),(0.01588,'2014-02-05 17:35:18'),(0.01704,'2014-02-05 17:35:22'),(0.02127,'2014-02-05 17:35:24'),(0.01428,'2014-02-05 17:35:26'),(0.02037,'2014-02-05 17:35:28'),(0.01522,'2014-02-05 17:35:29'),(0.01305,'2014-02-05 17:35:32'),(0.01458,'2014-02-05 17:35:33'),(0.01666,'2014-02-05 17:35:44'),(0.01777,'2014-02-05 17:35:49'),(0.01518,'2014-02-05 17:35:50'),(0.0164,'2014-02-05 17:36:00'),(0.01663,'2014-02-05 17:36:00'),(0.01458,'2014-02-05 17:36:04'),(0.01581,'2014-02-05 17:36:09'),(0.01757,'2014-02-05 17:36:15'),(0.01716,'2014-02-05 17:36:20'),(0.01803,'2014-02-05 17:36:42'),(0.01657,'2014-02-05 17:36:45'),(0.0123,'2014-02-05 17:36:47'),(0.01965,'2014-02-05 17:36:49'),(0.01885,'2014-02-05 17:36:50'),(0.01614,'2014-02-05 17:36:51'),(0.01789,'2014-02-05 17:36:52'),(0.01626,'2014-02-05 17:36:53'),(0.01624,'2014-02-05 17:36:54'),(0.01557,'2014-02-05 17:36:55'),(0.01457,'2014-02-05 17:36:56'),(0.01571,'2014-02-05 17:36:58'),(0.01705,'2014-02-05 17:37:00'),(0.01765,'2014-02-05 17:37:01'),(0.01543,'2014-02-05 17:37:01'),(0.01575,'2014-02-05 17:37:40'),(0.01425,'2014-02-05 17:37:42'),(0.01566,'2014-02-05 17:37:43'),(0.01384,'2014-02-05 17:37:45'),(0.01682,'2014-02-05 17:37:51'),(0.01304,'2014-02-05 17:37:52'),(0.01408,'2014-02-05 17:37:52'),(0.01512,'2014-02-05 17:37:53'),(0.01759,'2014-02-05 17:37:53'),(0.01498,'2014-02-05 17:37:54'),(0.01538,'2014-02-05 17:37:55'),(0.01404,'2014-02-05 17:37:56'),(0.0145,'2014-02-05 17:37:56'),(0.01449,'2014-02-05 17:37:57'),(0.01401,'2014-02-05 17:37:59'),(0.0163,'2014-02-05 17:38:01'),(0.01252,'2014-02-05 17:38:07'),(0.01757,'2014-02-05 17:38:09'),(0.01493,'2014-02-05 17:38:13'),(0.01095,'2014-02-05 17:38:17'),(0.01299,'2014-02-05 17:38:20'),(0.01458,'2014-02-05 17:38:24'),(0.0168,'2014-02-05 17:38:26'),(0.04475,'2014-02-05 17:38:29'),(0.01598,'2014-02-05 17:38:36'),(0.06462,'2014-02-05 17:38:38'),(0.0159,'2014-02-05 17:38:44'),(0.01225,'2014-02-05 17:38:46'),(0.0159,'2014-02-05 17:38:50'),(0.01287,'2014-02-05 17:38:52'),(0.01779,'2014-02-05 17:38:57'),(0.01711,'2014-02-05 17:38:58'),(0.01431,'2014-02-05 17:39:00'),(0.01713,'2014-02-05 17:39:02'),(0.01945,'2014-02-05 17:39:04'),(0.01867,'2014-02-05 17:39:12'),(0.01602,'2014-02-05 17:39:15'),(0.01714,'2014-02-05 17:39:16'),(0.019,'2014-02-05 17:39:18'),(0.017,'2014-02-05 17:39:21'),(0.01547,'2014-02-05 17:39:23'),(0.01761,'2014-02-05 17:39:24'),(0.01405,'2014-02-05 17:39:28'),(0.01799,'2014-02-05 17:39:29'),(0.01529,'2014-02-05 17:39:30'),(0.016,'2014-02-05 17:39:32'),(0.01615,'2014-02-05 17:39:33'),(0.01567,'2014-02-05 17:39:34'),(0.01546,'2014-02-05 17:39:35'),(0.01616,'2014-02-05 17:39:36'),(0.01353,'2014-02-05 17:39:39'),(0.01452,'2014-02-05 17:39:41'),(0.01744,'2014-02-05 17:39:44'),(0.01819,'2014-02-05 17:39:46'),(0.01972,'2014-02-05 17:39:49'),(0.01959,'2014-02-05 17:39:50'),(0.01459,'2014-02-05 17:39:51'),(0.01513,'2014-02-05 17:39:59'),(0.01433,'2014-02-05 17:40:01'),(0.01279,'2014-02-05 17:40:04'),(0.01535,'2014-02-05 17:40:06'),(0.01332,'2014-02-05 17:40:09'),(0.01397,'2014-02-05 17:40:11'),(0.01621,'2014-02-05 17:40:13'),(0.03465,'2014-02-05 17:46:14'),(0.01756,'2014-02-05 17:46:17'),(0.0117,'2014-02-05 17:46:20'),(0.01619,'2014-02-05 17:46:32');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `folders`
--

LOCK TABLES `folders` WRITE;
/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
INSERT INTO `folders` VALUES (0,0,'0','ROOT',NULL,0,0,0,'2013-11-20 20:19:40','2013-11-20 20:19:40'),(2,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','dsad','\0',1,0,0,'2014-01-07 19:33:42','2014-02-05 15:53:24'),(3,0,'ad179f7f4255acfa37a62521f248839ec5fe3f57','lkkm','\0',1,0,0,'2014-01-08 23:58:15','2014-01-08 23:58:16'),(5,2,'c4abcdce541b22950fdcee3ab88240094da914fc','lmslam','\0',1,0,0,'2014-02-05 16:46:33','2014-02-05 16:46:33'),(6,0,'c4abcdce541b22950fdcee3ab88240094da914fc','l;smla','\0',0,1,0,'2014-02-05 17:01:42','2014-02-05 17:03:53'),(11,0,'c4abcdce541b22950fdcee3ab88240094da914fc','Public','\0',1,0,0,'2014-02-05 17:38:20','2014-02-05 17:40:11'),(12,11,'c4abcdce541b22950fdcee3ab88240094da914fc','smkmnsa','\0',0,0,0,'2014-02-05 17:39:41','2014-02-05 17:39:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
INSERT INTO `links` VALUES (1,0,'65e24b6874a4da6aea4413ca2fab916dd72083bd','lnlsaln','knkn',1,0,0,'2014-02-05 16:05:33','2014-02-05 16:05:35'),(2,0,'c4abcdce541b22950fdcee3ab88240094da914fc','lsmnamn','lml',1,0,0,'2014-02-05 16:05:48','2014-02-05 16:05:49'),(4,11,'c4abcdce541b22950fdcee3ab88240094da914fc','goo','',0,0,0,'2014-02-05 17:38:50','2014-02-05 17:39:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,0,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ...','<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>\r\n\r\n<p>Your note&#39;s body here ...</p>',1,0,0,'2014-02-05 16:03:59','2014-02-05 16:24:24'),(2,2,'65e24b6874a4da6aea4413ca2fab916dd72083bd','Your note\'s title here ...','<p>Your note&#39;s body here ...</p>',1,0,0,'2014-02-05 16:32:45','2014-02-05 16:32:45'),(4,11,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ... [PUBLIC]','<p>Your note&#39;s body here ...</p>',1,0,0,'2014-02-05 17:38:35','2014-02-05 17:38:35'),(5,11,'c4abcdce541b22950fdcee3ab88240094da914fc','Your note\'s title here ... [PRIVATE]','<p>Your note&#39;s body here ...</p>',0,0,0,'2014-02-05 17:38:44','2014-02-05 17:39:15');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (2,'ad179f7f4255acfa37a62521f248839ec5fe3f57',0,'folder',3,1,'2014-01-08 23:58:16'),(4,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'folder',2,1,'2014-02-05 15:53:24'),(5,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'note',1,1,'2014-02-05 16:04:02'),(6,'65e24b6874a4da6aea4413ca2fab916dd72083bd',0,'link',1,1,'2014-02-05 16:05:35'),(7,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'link',2,1,'2014-02-05 16:05:50'),(8,'c4abcdce541b22950fdcee3ab88240094da914fc',0,'folder',11,1,'2014-02-05 17:38:24');
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
INSERT INTO `profiles` VALUES ('65e24b6874a4da6aea4413ca2fab916dd72083bd','O:8:\"stdClass\":3:{s:13:\"notifications\";O:8:\"stdClass\":1:{s:4:\"pull\";O:8:\"stdClass\":1:{s:9:\"last_time\";s:20:\"Feb-05-2014 17:46:32\";}}s:7:\"profile\";O:8:\"stdClass\":2:{s:6:\"status\";i:1;s:6:\"avatar\";O:8:\"stdClass\":2:{s:9:\"activated\";s:6:\"custom\";s:6:\"custom\";O:8:\"stdClass\":3:{s:3:\"set\";i:1;s:12:\"origin_image\";s:63:\"/access/img/upload/8c8c53fef0a8059d7e194aa6061e4d85c7db9924.jpg\";s:11:\"thumb_image\";s:73:\"/access/img/upload/thumbnail/c018c0bda8571d5aa679bd9dc4fc431a1a4e8fdc.jpg\";}}}s:3:\"rte\";O:8:\"stdClass\":1:{s:4:\"type\";s:7:\"classic\";}}','','','',0,0,0,1,'','','','','','','','','2014-01-07 19:33:34','2014-02-05 17:46:32'),('ad179f7f4255acfa37a62521f248839ec5fe3f57','','','','',0,0,0,-1,'','','','','','','','','2014-01-07 20:00:27','2014-01-07 20:00:27'),('c4abcdce541b22950fdcee3ab88240094da914fc','O:8:\"stdClass\":3:{s:7:\"profile\";O:8:\"stdClass\":2:{s:6:\"avatar\";O:8:\"stdClass\":2:{s:6:\"custom\";O:8:\"stdClass\":3:{s:3:\"set\";i:1;s:12:\"origin_image\";s:55:\"https://s.yimg.com/dh/ap/social/profile/profile_b48.png\";s:11:\"thumb_image\";s:55:\"https://s.yimg.com/dh/ap/social/profile/profile_b48.png\";}s:9:\"activated\";s:6:\"custom\";}s:6:\"status\";i:1;}s:13:\"notifications\";O:8:\"stdClass\":1:{s:4:\"pull\";O:8:\"stdClass\":1:{s:9:\"last_time\";s:20:\"Feb-05-2014 17:40:12\";}}s:3:\"rte\";O:8:\"stdClass\":1:{s:4:\"type\";s:7:\"classic\";}}','đᶐᵲ!ʯʃɧ','','ϦɐϨ₰αϰϸʘϑʁ',0,0,0,1,'','','','','','','','','2014-02-05 15:45:17','2014-02-05 17:40:12'),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','','','','',0,0,0,-1,'','','','','','','','','2014-01-07 20:00:46','2014-01-07 20:00:46');
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
INSERT INTO `users` VALUES ('0','NULL','ROOT','NULL',NULL,'2013-11-20 20:19:41','2013-11-20 20:19:41'),('65e24b6874a4da6aea4413ca2fab916dd72083bd','b.g.dariush@gmail.com','dariush','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 19:33:34','2014-01-07 19:33:34'),('ad179f7f4255acfa37a62521f248839ec5fe3f57','a@m.com','a','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 20:00:27','2014-01-07 20:00:27'),('c4abcdce541b22950fdcee3ab88240094da914fc','badguy2580@yahoo.com','badguy2580yahoocom','7d024a27ab00ae473d8196b0860d82ae3b322e36',NULL,'2014-02-05 15:45:17','2014-02-05 15:45:17'),('d7e7bd6979eb244436f69e5c81aca930fb142ce3','b@b.com','b','48b98fe6d3bac3fafd60362b151136c7a8e6c72f',NULL,'2014-01-07 20:00:46','2014-01-07 20:00:46');
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

-- Dump completed on 2014-02-05 17:47:27
