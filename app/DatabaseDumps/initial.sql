-- MySQL dump 10.13  Distrib 5.5.53, for osx10.11 (x86_64)
--
-- Host: localhost    Database: efit_app
-- ------------------------------------------------------
-- Server version 5.5.53

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
-- Table structure for table `efit_admin`
--

DROP TABLE IF EXISTS `efit_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9F4A508DF85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_admin`
--

LOCK TABLES `efit_admin` WRITE;
/*!40000 ALTER TABLE `efit_admin` DISABLE KEYS */;
INSERT INTO `efit_admin` VALUES (1,'efsadmin','$2y$13$ZGT7WgHNZYAo1ZJy7Rew/ekOsS8j6gTQyp/tj/uS38s7bEes2M1lq','[\"ROLE_SUPER_ADMIN\"]');
/*!40000 ALTER TABLE `efit_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_migrations`
--

DROP TABLE IF EXISTS `efit_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_migrations` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_migrations`
--

LOCK TABLES `efit_migrations` WRITE;
/*!40000 ALTER TABLE `efit_migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_result`
--

DROP TABLE IF EXISTS `efit_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `json` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:json_array)',
  `in_block` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_pending` tinyint(1) NOT NULL,
  `ticket_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3912AEDAA76ED395` (`user_id`),
  KEY `IDX_3912AEDA8DB60186` (`task_id`),
  CONSTRAINT `FK_3912AEDA8DB60186` FOREIGN KEY (`task_id`) REFERENCES `efit_task` (`id`),
  CONSTRAINT `FK_3912AEDAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `efit_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_result`
--

LOCK TABLES `efit_result` WRITE;
/*!40000 ALTER TABLE `efit_result` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_stats`
--

DROP TABLE IF EXISTS `efit_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `rounds` int(11) NOT NULL,
  `blocks` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  `tasks` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:json_array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_40033A51A76ED395` (`user_id`),
  CONSTRAINT `FK_40033A51A76ED395` FOREIGN KEY (`user_id`) REFERENCES `efit_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_stats`
--

LOCK TABLES `efit_stats` WRITE;
/*!40000 ALTER TABLE `efit_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_task`
--

DROP TABLE IF EXISTS `efit_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workshop_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `block` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A19D63BB1FDCE57C` (`workshop_id`),
  CONSTRAINT `FK_A19D63BB1FDCE57C` FOREIGN KEY (`workshop_id`) REFERENCES `efit_workshop` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_task`
--

LOCK TABLES `efit_task` WRITE;
/*!40000 ALTER TABLE `efit_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_ticket`
--

DROP TABLE IF EXISTS `efit_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BDD8C26AA76ED395` (`user_id`),
  CONSTRAINT `FK_BDD8C26AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `efit_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_ticket`
--

LOCK TABLES `efit_ticket` WRITE;
/*!40000 ALTER TABLE `efit_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_user`
--

DROP TABLE IF EXISTS `efit_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `workshop_id` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7E706ED7F85E0677` (`username`),
  KEY `IDX_7E706ED71FDCE57C` (`workshop_id`),
  CONSTRAINT `FK_7E706ED71FDCE57C` FOREIGN KEY (`workshop_id`) REFERENCES `efit_workshop` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_user`
--

LOCK TABLES `efit_user` WRITE;
/*!40000 ALTER TABLE `efit_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `efit_workshop`
--

DROP TABLE IF EXISTS `efit_workshop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efit_workshop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5C5A38727E3C61F9` (`owner_id`),
  CONSTRAINT `FK_5C5A38727E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `efit_admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `efit_workshop`
--

LOCK TABLES `efit_workshop` WRITE;
/*!40000 ALTER TABLE `efit_workshop` DISABLE KEYS */;
/*!40000 ALTER TABLE `efit_workshop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refresh_tokens`
--

DROP TABLE IF EXISTS `refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refresh_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refresh_token` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valid` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9BACE7E1C74F2195` (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refresh_tokens`
--

LOCK TABLES `refresh_tokens` WRITE;
/*!40000 ALTER TABLE `refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-03 23:07:06
