-- MySQL dump 10.16  Distrib 10.1.14-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ims_assistant
-- ------------------------------------------------------
-- Server version	10.1.14-MariaDB

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
-- Table structure for table `ims_clients`
--

DROP TABLE IF EXISTS `ims_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_clients` (
  `clientId` int(11) NOT NULL AUTO_INCREMENT,
  `clientName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `clientDescription` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `clientFilesGroup` int(11) DEFAULT NULL,
  PRIMARY KEY (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_clients`
--

LOCK TABLES `ims_clients` WRITE;
/*!40000 ALTER TABLE `ims_clients` DISABLE KEYS */;
INSERT INTO `ims_clients` VALUES (1,'Кудрин',NULL,NULL),(2,'Гранат',NULL,1),(3,'Кама Сервис',NULL,NULL);
/*!40000 ALTER TABLE `ims_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_details`
--

DROP TABLE IF EXISTS `ims_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_details` (
  `detailId` int(11) NOT NULL AUTO_INCREMENT,
  `detailOrderId` int(11) NOT NULL,
  `detailGroup` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `detailCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `detailName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `detailPattern` int(11) DEFAULT NULL,
  `detailModel` int(11) DEFAULT NULL,
  `detailProject` int(11) DEFAULT NULL,
  `detailCreationDate` date NOT NULL,
  `detailEndDate` date DEFAULT NULL,
  PRIMARY KEY (`detailId`,`detailOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_details`
--

LOCK TABLES `ims_details` WRITE;
/*!40000 ALTER TABLE `ims_details` DISABLE KEYS */;
INSERT INTO `ims_details` VALUES
  (1,1,NULL,'asd','TestName',1,2,3,'2016-07-21','2016-08-10'),
  (2,1,'Форма','ewq','Сборка',NULL,NULL,NULL,'2016-07-21',NULL),
  (3,1,'Форма','ewq1','Плита подвижная',NULL,NULL,NULL,'2016-09-01','2016-09-02'),
  (4,1,'Форма на звено 1','ewq2','Плита толкателей',NULL,NULL,NULL,'2016-07-21','2016-10-02'),
  (5,1,'Форма на звено 1','ewq3','Плита толкателей',NULL,NULL,NULL,'2016-07-21','2016-09-01'),
  (7,1,NULL,'New detail code','New detail',NULL,NULL,NULL,'2017-01-30',NULL),
  (8,1,NULL,'qwerty','Detail',6,4,5,'2017-01-30',NULL),
  (9,2,NULL,'112233','Деталь',NULL,NULL,NULL,'2016-12-21','2017-01-31'),
  (10,2,NULL,'445566','Деталь 2',NULL,NULL,NULL,'2016-12-22','2017-01-31'),
  (11,2,NULL,'778899','Деталь 3',NULL,NULL,NULL,'2017-01-31','2017-01-31');
/*!40000 ALTER TABLE `ims_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_errors`
--

DROP TABLE IF EXISTS `ims_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_errors` (
  `errId` int(11) NOT NULL AUTO_INCREMENT,
  `errHash` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreation` date NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `readed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`errId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ims_file_versions`
--

DROP TABLE IF EXISTS `ims_file_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_file_versions` (
  `versionId` int(11) NOT NULL AUTO_INCREMENT,
  `versionFileId` int(11) NOT NULL,
  `versionPath` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `versionDate` date NOT NULL,
  PRIMARY KEY (`versionId`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_file_versions`
--

LOCK TABLES `ims_file_versions` WRITE;
/*!40000 ALTER TABLE `ims_file_versions` DISABLE KEYS */;
INSERT INTO `ims_file_versions` VALUES
  (1,1,'img7E40.tmp','2017-01-23'),(2,2,'img811F.tmp','2017-01-23'),(3,3,'img333B.tmp','2017-01-23'),
  (4,4,'img42DC.tmp','2017-01-23'),(5,5,'img4D8C.tmp','2017-01-23'),(6,6,'img5780.tmp','2017-01-23'),
  (7,7,'img623F.tmp','2017-01-23'),(8,8,'img681C.tmp','2017-01-23'),(9,9,'img7396.tmp','2017-01-23'),
  (10,10,'img875E.tmp','2017-01-23'),(11,11,'img8D3B.tmp','2017-01-23'),(12,12,'img9308.tmp','2017-01-23'),
  (13,13,'imgB527.tmp','2017-01-23'),(14,14,'imgBDE3.tmp','2017-01-23'),(15,15,'imgC882.tmp','2017-01-23'),
  (16,16,'imgCBA0.tmp','2017-01-23'),(17,17,'imgCE80.tmp','2017-01-23'),(18,18,'imgD16F.tmp','2017-01-23'),
  (19,19,'imgD44E.tmp','2017-01-23'),(150,150,'phpC65F_58860660743a1.tmp','2017-01-23'),
  (151,151,'phpC660_5886066074b1b.tmp','2017-01-23'),(152,152,'phpC661_5886066075218.tmp','2017-01-23'),
  (153,153,'phpC672_58860660758dc.tmp','2017-01-23'),(154,154,'phpC673_5886066075f17.tmp','2017-01-23'),
  (155,155,'phpC674_5886066076578.tmp','2017-01-23'),(156,156,'phpC675_5886066076c63.tmp','2017-01-23'),
  (157,157,'phpC686_58860660772cf.tmp','2017-01-23'),(158,158,'phpC687_58860660778f4.tmp','2017-01-23'),
  (159,159,'phpC688_5886066077eb5.tmp','2017-01-23'),(160,160,'phpC698_588606607854f.tmp','2017-01-23'),
  (161,161,'phpC699_5886066078b61.tmp','2017-01-23'),(162,162,'phpC69A_588606607911d.tmp','2017-01-23'),
  (163,163,'phpC69B_5886066079665.tmp','2017-01-23'),(164,164,'phpC69C_5886066079c2c.tmp','2017-01-23'),
  (165,165,'phpC6AD_588606607a38f.tmp','2017-01-23'),(166,166,'phpC6AE_588606607aab9.tmp','2017-01-23'),
  (167,167,'phpC6AF_588606607b09f.tmp','2017-01-23'),(168,168,'phpC6C0_588606607b671.tmp','2017-01-23'),
  (169,169,'phpC6C1_588606607bd34.tmp','2017-01-23'),(170,170,'phpC6C2_588606607c433.tmp','2017-01-23'),
  (171,171,'phpC6C3_588606607ca4e.tmp','2017-01-23'),(172,172,'phpC6D3_588606607d02d.tmp','2017-01-23'),
  (173,173,'phpC6D4_588606607d557.tmp','2017-01-23'),(174,174,'phpC6D5_588606607dab6.tmp','2017-01-23'),
  (175,175,'phpC6D6_588606607e144.tmp','2017-01-23'),(176,176,'phpC6D7_588606607e84b.tmp','2017-01-23'),
  (177,177,'phpC6E8_588606607eff8.tmp','2017-01-23'),(178,178,'phpC6E9_588606607f63e.tmp','2017-01-23'),
  (179,179,'phpC6EA_588606607fc2a.tmp','2017-01-23'),(180,180,'phpC6EB_58860660802ff.tmp','2017-01-23'),
  (181,181,'phpC6FB_588606608094b.tmp','2017-01-23'),(182,182,'phpC6FC_5886066080ee1.tmp','2017-01-23'),
  (183,183,'phpC6FD_5886066081420.tmp','2017-01-23'),(184,184,'phpC6FE_588606608196a.tmp','2017-01-23'),
  (185,185,'phpC6FF_5886066081eb3.tmp','2017-01-23'),(186,186,'phpC700_5886066082405.tmp','2017-01-23'),
  (187,187,'phpABB0_588f4565c7747.tmp','2017-01-30'),(188,188,'phpABB1_588f4565cba78.tmp','2017-01-30'),
  (189,189,'phpABC2_588f4565cd1d9.tmp','2017-01-30'),(190,190,'phpABC3_588f4565ce75f.tmp','2017-01-30'),
  (191,191,'imgB0D5.tmp','2017-01-30'),(192,192,'imgB21E.tmp','2017-01-30'),(195,192,'imgBB8E.tmp','2017-01-31'),
  (193,188,'phpB4A7_589023df4b31b.tmp','2017-01-31'),(194,193,'phpB4A6_589023df4da68.tmp','2017-01-31');
/*!40000 ALTER TABLE `ims_file_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_files`
--

DROP TABLE IF EXISTS `ims_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_files` (
  `fileId` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`fileId`)
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_files`
--

LOCK TABLES `ims_files` WRITE;
/*!40000 ALTER TABLE `ims_files` DISABLE KEYS */;
INSERT INTO `ims_files` VALUES
  (1,'asd.jpeg'),(2,'asd.jpeg'),(3,'asd.jpeg'),(4,'asd.jpeg'),(5,'asd.jpeg'),(6,'asd.jpeg'),(7,'asd.jpeg'),
  (8,'asd.jpeg'),(9,'asd.jpeg'),(10,'asd.jpeg'),(11,'asd.jpeg'),(12,'asd.jpeg'),(13,'asd.jpeg'),(14,'asd.jpeg'),
  (15,'asd.jpeg'),(16,'asd.jpeg'),(17,'asd.jpeg'),(18,'asd.jpeg'),(19,'asd.jpeg'),(150,'asd.SLDPRT'),(151,'asd.SLDPRT'),
  (152,'asd.SLDPRT'),(153,'asd.SLDPRT'),(154,'asd.SLDPRT'),(155,'asd.SLDPRT'),(156,'asd.SLDPRT'),(157,'asd.SLDPRT'),
  (158,'asd.SLDPRT'),(159,'asd.SLDPRT'),(160,'asd.SLDPRT'),(161,'asd.SLDPRT'),(162,'asd.SLDPRT'),(163,'asd.SLDPRT'),
  (164,'asd.SLDPRT'),(165,'asd.SLDPRT'),(166,'asd.SLDPRT'),(167,'asd.SLDPRT'),(168,'asd.SLDPRT'),(169,'asd.rar'),
  (170,'asd.rar'),(171,'asd.rar'),(172,'asd.rar'),(173,'asd.rar'),(174,'asd.rar'),(175,'asd.rar'),(176,'asd.rar'),
  (177,'asd.rar'),(178,'asd.rar'),(179,'asd.rar'),(180,'asd.rar'),(181,'asd.rar'),(182,'asd.rar'),(183,'asd.rar'),
  (184,'asd.rar'),(185,'asd.rar'),(186,'asd.rar'),(187,'2.jpeg'),(188,'2.jpg'),(189,'2.jpg'),(190,'2.jpg'),
  (191,'2.jpeg'),(192,'2.jpeg'),(193,'qwerty.jpg');
/*!40000 ALTER TABLE `ims_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_files_collections`
--

DROP TABLE IF EXISTS `ims_files_collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_files_collections` (
  `id` int(11) NOT NULL,
  `fileId` int(11) NOT NULL,
  PRIMARY KEY (`id`,`fileId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_files_collections`
--

LOCK TABLES `ims_files_collections` WRITE;
/*!40000 ALTER TABLE `ims_files_collections` DISABLE KEYS */;
INSERT INTO `ims_files_collections` VALUES
  (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),
  (1,19),(2,150),(2,151),(2,152),(2,153),(2,154),(2,155),(2,156),(2,157),(2,158),(2,159),(2,160),(2,161),(2,162),
  (2,163),(2,164),(2,165),(2,166),(2,167),(2,168),(3,169),(3,170),(3,171),(3,172),(3,173),(3,174),(3,175),(3,176),
  (3,177),(3,178),(3,179),(3,180),(3,181),(3,182),(3,183),(3,184),(3,185),(3,186),(4,187),(4,188),(5,189),(5,190),
  (5,193),(6,191),(6,192);
/*!40000 ALTER TABLE `ims_files_collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_orders`
--

DROP TABLE IF EXISTS `ims_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_orders` (
  `orderId` int(11) NOT NULL AUTO_INCREMENT,
  `orderClientId` int(11) NOT NULL,
  `orderCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `orderStatus` int(11) NOT NULL,
  `orderCreationDate` date NOT NULL,
  `orderStartDate` date DEFAULT NULL,
  `orderEndDate` date DEFAULT NULL,
  `orderDeadline` date DEFAULT NULL,
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_orders`
--

LOCK TABLES `ims_orders` WRITE;
/*!40000 ALTER TABLE `ims_orders` DISABLE KEYS */;
INSERT INTO `ims_orders` VALUES
  (1,1,'22193',1,'2016-05-18',NULL,NULL,NULL),
  (2,2,'22632',3,'2016-07-21','2016-12-20','2017-02-14','2017-02-15');
/*!40000 ALTER TABLE `ims_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_users`
--

DROP TABLE IF EXISTS `ims_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userFullName` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userEmail` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userPassword` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `userRoleId` int(11) NOT NULL,
  `userActive` tinyint(1) NOT NULL,
  `userRegistrationDate` datetime NOT NULL,
  `userEmailConfirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_users`
--

LOCK TABLES `ims_users` WRITE;
/*!40000 ALTER TABLE `ims_users` DISABLE KEYS */;
INSERT INTO `ims_users` VALUES
  (1,'MefisTofel','Новоселов Александр','iviefistofel@gmail.com','ae50a96b5a07506d45ddb5bf66348d2d',1,1,'2015-08-08 00:00:00',1),
  (2,'Moderator','Moderator','test@test.ru','e10adc3949ba59abbe56e057f20f883e',2,1,'2016-01-28 23:37:51',0);
/*!40000 ALTER TABLE `ims_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `ims_view_details`
--

DROP TABLE IF EXISTS `ims_view_details`;
/*!50001 DROP VIEW IF EXISTS `ims_view_details`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ims_view_details` (
  `detailId` tinyint NOT NULL,
  `detailOrderId` tinyint NOT NULL,
  `detailGroup` tinyint NOT NULL,
  `detailCode` tinyint NOT NULL,
  `orderCode` tinyint NOT NULL,
  `detailName` tinyint NOT NULL,
  `detailPattern` tinyint NOT NULL,
  `detailModel` tinyint NOT NULL,
  `detailProject` tinyint NOT NULL,
  `detailCreationDate` tinyint NOT NULL,
  `detailEndDate` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ims_view_files_collections`
--

DROP TABLE IF EXISTS `ims_view_files_collections`;
/*!50001 DROP VIEW IF EXISTS `ims_view_files_collections`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ims_view_files_collections` (
  `id` tinyint NOT NULL,
  `fileId` tinyint NOT NULL,
  `fileName` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ims_view_groups`
--

DROP TABLE IF EXISTS `ims_view_groups`;
/*!50001 DROP VIEW IF EXISTS `ims_view_groups`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ims_view_groups` (
  `group` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ims_view_hot_orders`
--

DROP TABLE IF EXISTS `ims_view_hot_orders`;
/*!50001 DROP VIEW IF EXISTS `ims_view_hot_orders`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ims_view_hot_orders` (
  `orderId` tinyint NOT NULL,
  `orderClientId` tinyint NOT NULL,
  `clientName` tinyint NOT NULL,
  `orderCode` tinyint NOT NULL,
  `orderStatus` tinyint NOT NULL,
  `orderCreationDate` tinyint NOT NULL,
  `orderStartDate` tinyint NOT NULL,
  `orderEndDate` tinyint NOT NULL,
  `orderDeadline` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ims_view_orders`
--

DROP TABLE IF EXISTS `ims_view_orders`;
/*!50001 DROP VIEW IF EXISTS `ims_view_orders`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ims_view_orders` (
  `orderId` tinyint NOT NULL,
  `orderClientId` tinyint NOT NULL,
  `clientName` tinyint NOT NULL,
  `orderCode` tinyint NOT NULL,
  `orderStatus` tinyint NOT NULL,
  `orderCreationDate` tinyint NOT NULL,
  `orderStartDate` tinyint NOT NULL,
  `orderEndDate` tinyint NOT NULL,
  `orderDeadline` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `ims_view_details`
--

/*!50001 DROP TABLE IF EXISTS `ims_view_details`*/;
/*!50001 DROP VIEW IF EXISTS `ims_view_details`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `ims_view_details` AS select `ims_details`.`detailId` AS `detailId`,`ims_details`.`detailOrderId` AS `detailOrderId`,`ims_details`.`detailGroup` AS `detailGroup`,`ims_details`.`detailCode` AS `detailCode`,`ims_orders`.`orderCode` AS `orderCode`,`ims_details`.`detailName` AS `detailName`,`ims_details`.`detailPattern` AS `detailPattern`,`ims_details`.`detailModel` AS `detailModel`,`ims_details`.`detailProject` AS `detailProject`,`ims_details`.`detailCreationDate` AS `detailCreationDate`,`ims_details`.`detailEndDate` AS `detailEndDate` from (`ims_details` join `ims_orders`) where (`ims_orders`.`orderId` = `ims_details`.`detailOrderId`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ims_view_files_collections`
--

/*!50001 DROP TABLE IF EXISTS `ims_view_files_collections`*/;
/*!50001 DROP VIEW IF EXISTS `ims_view_files_collections`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `ims_view_files_collections` AS select `ims_files_collections`.`id` AS `id`,`ims_files_collections`.`fileId` AS `fileId`,`ims_files`.`fileName` AS `fileName` from (`ims_files_collections` join `ims_files`) where (`ims_files_collections`.`fileId` = `ims_files`.`fileId`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ims_view_groups`
--

/*!50001 DROP TABLE IF EXISTS `ims_view_groups`*/;
/*!50001 DROP VIEW IF EXISTS `ims_view_groups`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `ims_view_groups` AS select distinct `ims_details`.`detailGroup` AS `group` from `ims_details` where (`ims_details`.`detailGroup` is not null) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ims_view_hot_orders`
--

/*!50001 DROP TABLE IF EXISTS `ims_view_hot_orders`*/;
/*!50001 DROP VIEW IF EXISTS `ims_view_hot_orders`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `ims_view_hot_orders` AS select `ims_view_orders`.`orderId` AS `orderId`,`ims_view_orders`.`orderClientId` AS `orderClientId`,`ims_view_orders`.`clientName` AS `clientName`,`ims_view_orders`.`orderCode` AS `orderCode`,`ims_view_orders`.`orderStatus` AS `orderStatus`,`ims_view_orders`.`orderCreationDate` AS `orderCreationDate`,`ims_view_orders`.`orderStartDate` AS `orderStartDate`,`ims_view_orders`.`orderEndDate` AS `orderEndDate`,`ims_view_orders`.`orderDeadline` AS `orderDeadline` from `ims_view_orders` where ((`ims_view_orders`.`orderDeadline` > (now() - interval 1 day)) and isnull(`ims_view_orders`.`orderEndDate`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ims_view_orders`
--

/*!50001 DROP TABLE IF EXISTS `ims_view_orders`*/;
/*!50001 DROP VIEW IF EXISTS `ims_view_orders`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `ims_view_orders` AS select `ims_orders`.`orderId` AS `orderId`,`ims_orders`.`orderClientId` AS `orderClientId`,`ims_clients`.`clientName` AS `clientName`,`ims_orders`.`orderCode` AS `orderCode`,`ims_orders`.`orderStatus` AS `orderStatus`,`ims_orders`.`orderCreationDate` AS `orderCreationDate`,`ims_orders`.`orderStartDate` AS `orderStartDate`,`ims_orders`.`orderEndDate` AS `orderEndDate`,`ims_orders`.`orderDeadline` AS `orderDeadline` from (`ims_orders` join `ims_clients`) where (`ims_clients`.`clientId` = `ims_orders`.`orderClientId`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-07  10:00:00
