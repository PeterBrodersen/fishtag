-- MySQL dump 10.13  Distrib 5.7.31, for Linux (x86_64)
--
-- Host: localhost    Database: fish
-- ------------------------------------------------------
-- Server version	5.7.31

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
-- Table structure for table `antennas`
--

DROP TABLE IF EXISTS `antennas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `antennas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` tinytext COLLATE utf8_danish_ci,
  `code` char(8) COLLATE utf8_danish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` tinytext COLLATE utf8_danish_ci,
  `import_time` datetime DEFAULT NULL,
  `antenna_code` char(6) COLLATE utf8_danish_ci DEFAULT NULL,
  `observations` int(11) DEFAULT NULL,
  `imported` tinyint(4) NOT NULL DEFAULT '0',
  `import_result` tinytext COLLATE utf8_danish_ci NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=599 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fish`
--

DROP TABLE IF EXISTS `fish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fish` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `capture_date` date DEFAULT NULL,
  `river` tinytext COLLATE utf8_danish_ci,
  `section` tinytext COLLATE utf8_danish_ci,
  `fishec` int(11) DEFAULT NULL,
  `species_code` char(6) COLLATE utf8_danish_ci DEFAULT NULL,
  `standard_length` decimal(7,2) DEFAULT NULL,
  `total_length` decimal(7,2) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `pit_id` char(6) COLLATE utf8_danish_ci DEFAULT NULL,
  `comments` tinytext COLLATE utf8_danish_ci,
  `fish_origin` tinytext COLLATE utf8_danish_ci,
  `hatchery` tinytext COLLATE utf8_danish_ci,
  `parental_origin` tinytext COLLATE utf8_danish_ci,
  `sex` tinytext COLLATE utf8_danish_ci,
  `ripeness` tinytext COLLATE utf8_danish_ci,
  `recapture` tinytext COLLATE utf8_danish_ci,
  `laketrout` tinytext COLLATE utf8_danish_ci,
  `original_site_name` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  KEY `pit_id` (`pit_id`),
  KEY `species_code` (`species_code`),
  KEY `river` (`river`(8),`section`(8))
) ENGINE=MyISAM AUTO_INCREMENT=32008 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `observations`
--

DROP TABLE IF EXISTS `observations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `observations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(11) DEFAULT NULL,
  `site_code` char(6) COLLATE utf8_danish_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `time_fraction` tinyint(4) DEFAULT NULL,
  `period` time DEFAULT NULL,
  `period_fraction` tinyint(4) DEFAULT NULL,
  `tag_type` varchar(8) COLLATE utf8_danish_ci DEFAULT NULL,
  `pit_id` char(6) COLLATE utf8_danish_ci DEFAULT NULL,
  `original_pit_id` tinytext COLLATE utf8_danish_ci,
  `antenna_local` tinytext COLLATE utf8_danish_ci,
  `observations` int(11) DEFAULT NULL,
  `last_observation` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_type` (`tag_type`(1)),
  KEY `site_code` (`site_code`),
  KEY `pit_id` (`pit_id`),
  KEY `date` (`date`,`time`,`time_fraction`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8685015 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `species`
--

DROP TABLE IF EXISTS `species`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `species` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(6) COLLATE utf8_danish_ci DEFAULT NULL,
  `name_latin` tinytext COLLATE utf8_danish_ci,
  `name_english` tinytext COLLATE utf8_danish_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-09-06 13:20:39
