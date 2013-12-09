-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: datatrack
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu1

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset` int(11) DEFAULT NULL,
  `category` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dataset` (`dataset`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`dataset`) REFERENCES `datasets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'Transportation'),(2,2,'Crime');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datasets`
--

DROP TABLE IF EXISTS `datasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api` varchar(25) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `updated` date DEFAULT NULL,
  `selects` varchar(512) DEFAULT NULL,
  `groups` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datasets`
--

LOCK TABLES `datasets` WRITE;
/*!40000 ALTER TABLE `datasets` DISABLE KEYS */;
INSERT INTO `datasets` VALUES (1,'w8km-9pzd','CTA Annual Ridership','2013-10-20',NULL,NULL),(2,'ijzp-q8t2','Crimes, 2001-Present','2013-10-29','year,primary_type,count(id)','year,primary_type');
/*!40000 ALTER TABLE `datasets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fields`
--

DROP TABLE IF EXISTS `fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset` int(11) DEFAULT NULL,
  `field` varchar(100) DEFAULT NULL,
  `text` varchar(100) NOT NULL,
  `major` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `dataset` (`dataset`),
  CONSTRAINT `fields_ibfk_1` FOREIGN KEY (`dataset`) REFERENCES `datasets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fields`
--

LOCK TABLES `fields` WRITE;
/*!40000 ALTER TABLE `fields` DISABLE KEYS */;
INSERT INTO `fields` VALUES (4,1,'total','Total Ridership',1),(5,1,'bus','Bus Ridership',1),(6,1,'rail','Elevated Train Ridership',1),(7,2,'OFFENSES INVOLVING CHILDREN','Offenses involving children',0),(8,2,'DOMESTIC VIOLENCE','Domestic violence',0),(9,2,'RITUALISM','Ritualism',0),(10,2,'INTERFERE WITH PUBLIC OFFICER','Interfere with public officer',0),(11,2,'NON-CRIMINAL (SUBJECT SPECIFIED)','Non-criminal (subject specified)',0),(12,2,'OTHER NARCOTIC VIOLATION','Other narcotic violation',0),(13,2,'NON-CRIMINAL','Non-criminal',0),(14,2,'OTHER OFFENSE ','Other offense ',0),(15,2,'OBSCENITY','Obscenity',0),(16,2,'PUBLIC INDECENCY','Public indecency',0),(17,2,'NON - CRIMINAL','Non - criminal',0),(18,2,'STALKING','Stalking',0),(19,2,'HOMICIDE','Homicide',1),(20,2,'KIDNAPPING','Kidnapping',0),(21,2,'INTIMIDATION','Intimidation',0),(22,2,'ARSON','Arson',1),(23,2,'SEX OFFENSE','Sex offense',0),(24,2,'PROSTITUTION','Prostitution',0),(25,2,'LIQUOR LAW VIOLATION','Liquor law violation',0),(26,2,'INTERFERENCE WITH PUBLIC OFFICER','Interference with public officer',0),(27,2,'CRIM SEXUAL ASSAULT','Criminal Sexual Assault',1),(28,2,'GAMBLING','Gambling',0),(29,2,'MOTOR VEHICLE THEFT','Motor vehicle theft',0),(30,2,'BURGLARY','Burglary',1),(31,2,'DECEPTIVE PRACTICE','Deceptive practice',0),(32,2,'OFFENSE INVOLVING CHILDREN','Offense involving children',0),(33,2,'ROBBERY','Robbery',1),(34,2,'WEAPONS VIOLATION','Weapons violation',0),(35,2,'NARCOTICS','Narcotics',0),(36,2,'PUBLIC PEACE VIOLATION','Public peace violation',0),(37,2,'CRIMINAL DAMAGE','Criminal damage',0),(38,2,'CRIMINAL TRESPASS','Criminal trespass',0),(39,2,'BATTERY','Battery',0),(40,2,'ASSAULT','Assault',1),(41,2,'OTHER OFFENSE','Other offense',0),(42,2,'THEFT','Theft',0),(43,2,'TOTAL','Total',0);
/*!40000 ALTER TABLE `fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `foldSort`
--

DROP TABLE IF EXISTS `foldSort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `foldSort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset` int(11) NOT NULL,
  `keyfield` varchar(255) DEFAULT NULL,
  `valuefield` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dataset` (`dataset`),
  CONSTRAINT `foldSort_ibfk_1` FOREIGN KEY (`dataset`) REFERENCES `datasets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foldSort`
--

LOCK TABLES `foldSort` WRITE;
/*!40000 ALTER TABLE `foldSort` DISABLE KEYS */;
INSERT INTO `foldSort` VALUES (1,2,'primary_type','count_id');
/*!40000 ALTER TABLE `foldSort` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proportions`
--

DROP TABLE IF EXISTS `proportions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proportions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dataset` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `bottom` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dataset` (`dataset`),
  KEY `top` (`top`),
  KEY `bottom` (`bottom`),
  CONSTRAINT `proportions_ibfk_1` FOREIGN KEY (`dataset`) REFERENCES `datasets` (`id`),
  CONSTRAINT `proportions_ibfk_2` FOREIGN KEY (`top`) REFERENCES `fields` (`id`),
  CONSTRAINT `proportions_ibfk_3` FOREIGN KEY (`bottom`) REFERENCES `fields` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proportions`
--

LOCK TABLES `proportions` WRITE;
/*!40000 ALTER TABLE `proportions` DISABLE KEYS */;
INSERT INTO `proportions` VALUES (1,1,5,4,'Percentage of bus riders'),(2,1,6,4,'Percentage of train riders');
/*!40000 ALTER TABLE `proportions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-12-08 18:22:00
