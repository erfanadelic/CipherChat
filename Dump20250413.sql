-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: cipher
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `ss` int NOT NULL,
  PRIMARY KEY (`ss`)
) ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `MessageUuid` char(36) NOT NULL,
  `SenderUuid` char(36) NOT NULL,
  `ReceiverUuid` char(36) NOT NULL,
  `IsGroup` tinyint(1) DEFAULT '0',
  `Content` text,
  `MessageType` varchar(50) DEFAULT 'text',
  `SendTime` datetime NOT NULL,
  `SeenTime` datetime DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'sent',
  `ReplyTo` char(36) DEFAULT NULL,
  `EditTime` datetime DEFAULT NULL,
  PRIMARY KEY (`MessageUuid`),
  KEY `SenderUuid` (`SenderUuid`),
  KEY `ReceiverUuid` (`ReceiverUuid`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`SenderUuid`) REFERENCES `users` (`Uuid`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`ReceiverUuid`) REFERENCES `users` (`Uuid`)
) ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES ('afb03770-bc76-4d8c-8d2d-804ef95ed4bf','a2a0bc58-1833-11f0-a30d-54e1ad396dd2','d36cc988-1833-11f0-a30d-54e1ad396dd2',0,'dfdfdf','text','1404-01-24 11:01:00',NULL,'sent',NULL,NULL),('bb21938c-1d00-4552-95c4-a1965f047d95','d36cc988-1833-11f0-a30d-54e1ad396dd2','a2a0bc58-1833-11f0-a30d-54e1ad396dd2',0,'sdsdsd','text','1404-01-24 11:01:00',NULL,'sent',NULL,NULL);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Number` varchar(15) NOT NULL,
  `OTP Code` varchar(5) NOT NULL,
  `CreateTime` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
INSERT INTO `otp_codes` VALUES (11,'09982208533','29050','۱۰:۱:۰'),(14,'09158080539','91363','۱۱:۱:۰'),(16,'09158080539','30957','۲۰:۱:۰'),(17,'09158080539','82734','۲۰:۱:۰'),(26,'09982208533','33467','۱۱:۱:۰');
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `Uuid` char(36) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Username` varchar(100) DEFAULT NULL,
  `Number` varchar(20) NOT NULL,
  `PrivateKey` char(36) DEFAULT NULL,
  `LastSeen` datetime DEFAULT NULL,
  `ProfilePicture` varchar(100) DEFAULT 'DefualtProfile.png',
  `Bio` text,
  `Groups` json DEFAULT NULL,
  `Chats` json DEFAULT NULL,
  `Contacts` json DEFAULT NULL,
  `Settings` json DEFAULT NULL,
  `Status` varchar(100) DEFAULT NULL,
  `StoryUuid` char(36) DEFAULT NULL,
  `Information` json DEFAULT NULL,
  PRIMARY KEY (`Uuid`),
  UNIQUE KEY `Number` (`Number`),
  UNIQUE KEY `Username` (`Username`)
) ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('a2a0bc58-1833-11f0-a30d-54e1ad396dd2','09158080539',NULL,'09158080539','237f427d36a347a8f85931033ec2ca42',NULL,'DefualtProfile.png',NULL,NULL,'[\"d36cc988-1833-11f0-a30d-54e1ad396dd2\"]',NULL,NULL,NULL,NULL,NULL),('d36cc988-1833-11f0-a30d-54e1ad396dd2','09982208533',NULL,'09982208533','62a2859d341226864aaf89ca8b2c7a5c',NULL,'DefualtProfile.png',NULL,NULL,'[\"a2a0bc58-1833-11f0-a30d-54e1ad396dd2\"]',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-13 10:42:52
