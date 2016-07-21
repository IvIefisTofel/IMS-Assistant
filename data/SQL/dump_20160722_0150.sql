-- MySQL dump 10.16  Distrib 10.1.13-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: ims_assistant
-- ------------------------------------------------------
-- Server version	10.1.13-MariaDB

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
  `clientName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `clientDescription` text COLLATE utf8_unicode_ci,
  `clientAdditions` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_clients`
--

LOCK TABLES `ims_clients` WRITE;
/*!40000 ALTER TABLE `ims_clients` DISABLE KEYS */;
INSERT INTO `ims_clients` VALUES (1,'Кудрин',NULL,NULL),(2,'Гранат',NULL,NULL),(3,'Кама Сервис',NULL,NULL);
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
  `detailGroup` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detailCode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `detailName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `detailPattern` int(11) DEFAULT NULL,
  `detailModel` int(11) DEFAULT NULL,
  `detailProject` int(11) DEFAULT NULL,
  `detailCreationDate` date NOT NULL,
  `detailEndDate` date DEFAULT NULL,
  PRIMARY KEY (`detailId`,`detailOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_details`
--

LOCK TABLES `ims_details` WRITE;
/*!40000 ALTER TABLE `ims_details` DISABLE KEYS */;
INSERT INTO `ims_details` VALUES (1,1,NULL,'asd','TestName',1,NULL,NULL,'2016-07-21',NULL),(2,1,'PressForm','ewq','Форма на звено',1,NULL,NULL,'2016-07-21',NULL);
/*!40000 ALTER TABLE `ims_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_files`
--

DROP TABLE IF EXISTS `ims_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_files` (
  `fileId` int(11) NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fileExtension` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`fileId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_files`
--

LOCK TABLES `ims_files` WRITE;
/*!40000 ALTER TABLE `ims_files` DISABLE KEYS */;
INSERT INTO `ims_files` VALUES (1,'test','txt'),(2,'pressform','txt');
/*!40000 ALTER TABLE `ims_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ims_fileversion`
--

DROP TABLE IF EXISTS `ims_fileversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ims_fileversion` (
  `versionId` int(11) NOT NULL AUTO_INCREMENT,
  `versionFileId` int(11) NOT NULL,
  `versionPath` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `versionDate` date NOT NULL,
  PRIMARY KEY (`versionId`,`versionFileId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_fileversion`
--

LOCK TABLES `ims_fileversion` WRITE;
/*!40000 ALTER TABLE `ims_fileversion` DISABLE KEYS */;
INSERT INTO `ims_fileversion` VALUES (1,1,'D:\\asd_1.txt','2016-07-21'),(2,1,'D:\\asd_2.txt','2016-07-20'),(3,1,'D:\\asd_3.txt','2016-07-19'),(4,2,'D:\\pressform_1.txt','2016-07-19'),(5,2,'D:\\pressform_2.txt','2016-07-17');
/*!40000 ALTER TABLE `ims_fileversion` ENABLE KEYS */;
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
  `orderCode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `orderStatus` int(11) NOT NULL,
  `orderCreationDate` date NOT NULL,
  `orderStartDate` date DEFAULT NULL,
  `orderDeadline` date DEFAULT NULL,
  PRIMARY KEY (`orderId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_orders`
--

LOCK TABLES `ims_orders` WRITE;
/*!40000 ALTER TABLE `ims_orders` DISABLE KEYS */;
INSERT INTO `ims_orders` VALUES (1,1,'22193',1,'2016-05-18',NULL,NULL),(2,1,'22632',1,'2016-07-21',NULL,NULL);
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
  `userName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userFullName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userEmail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userPassword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userRoleId` int(11) NOT NULL,
  `userActive` tinyint(1) NOT NULL,
  `userRegistrationDate` datetime NOT NULL,
  `userEmailConfirmed` tinyint(1) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ims_users`
--

LOCK TABLES `ims_users` WRITE;
/*!40000 ALTER TABLE `ims_users` DISABLE KEYS */;
INSERT INTO `ims_users` VALUES (1,'MefisTofel','Новоселов Александр','iviefistofel@gmail.com','ae50a96b5a07506d45ddb5bf66348d2d',1,1,'2015-08-08 00:00:00',1),(2,'Moderator','Moderator','test@test.ru','e10adc3949ba59abbe56e057f20f883e',2,1,'2016-01-28 23:37:51',0);
/*!40000 ALTER TABLE `ims_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-22  1:59:01
