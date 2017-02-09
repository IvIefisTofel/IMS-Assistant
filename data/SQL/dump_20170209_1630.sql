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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
INSERT INTO `ims_users` VALUES (1,'MefisTofel','Новоселов Александр','iviefistofel@gmail.com','ae50a96b5a07506d45ddb5bf66348d2d',1,1,'2015-08-08 00:00:00',1),(2,'Moderator','Moderator','test@test.ru','e10adc3949ba59abbe56e057f20f883e',2,1,'2016-01-28 23:37:51',0);
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

-- Dump completed on 2017-02-09 16:30:00
