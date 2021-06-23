-- MySQL dump 10.13  Distrib 5.6.47-87.0, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: alienvault
-- ------------------------------------------------------
-- Server version	5.6.47-87.0

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
-- Table structure for table `topology_edge`
--

DROP TABLE IF EXISTS `topology_edge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topology_edge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `src` varchar(64) NOT NULL DEFAULT '',
  `dst` varchar(64) NOT NULL DEFAULT '',
  `device` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3226 DEFAULT CHARSET=utf8 COMMENT='网络拓扑连线信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topology_edge`
--

LOCK TABLES `topology_edge` WRITE;
/*!40000 ALTER TABLE `topology_edge` DISABLE KEYS */;
INSERT INTO `topology_edge` VALUES (3220,'10.157.10.210','10.157.1.162','10.157.1.162'),(3221,'10.157.12.4','10.157.1.162','10.157.1.162'),(3222,'10.157.5.203','10.157.1.162','10.157.1.162'),(3223,'10.157.6.205','10.157.1.162','10.157.1.162'),(3224,'10.157.10.211','10.157.1.162','10.157.1.162'),(3225,'10.157.12.3','10.157.1.162','10.157.1.162');
/*!40000 ALTER TABLE `topology_edge` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-12-05 13:52:33