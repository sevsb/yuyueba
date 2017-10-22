-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: yyba
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

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
-- Table structure for table `yyba_activity`
--

DROP TABLE IF EXISTS `yyba_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `title` text NOT NULL,
  `info` text NOT NULL,
  `createtime` int(11) NOT NULL,
  `modifytime` int(11) NOT NULL,
  `begintime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `repeattype` int(11) NOT NULL,
  `repeatcount` int(11) NOT NULL,
  `deadline` int(11) NOT NULL,
  `address` text NOT NULL,
  `content` text NOT NULL,
  `participants` int(11) NOT NULL,
  `sheet` text NOT NULL,
  `type` int(11) NOT NULL,
  `clickcount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_activity`
--

LOCK TABLES `yyba_activity` WRITE;
/*!40000 ALTER TABLE `yyba_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_comment`
--

DROP TABLE IF EXISTS `yyba_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity` (`activity`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_comment`
--

LOCK TABLES `yyba_comment` WRITE;
/*!40000 ALTER TABLE `yyba_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_invite`
--

DROP TABLE IF EXISTS `yyba_invite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_invite`
--

LOCK TABLES `yyba_invite` WRITE;
/*!40000 ALTER TABLE `yyba_invite` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_invite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_organization`
--

DROP TABLE IF EXISTS `yyba_organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `avatar` text NOT NULL,
  `owner` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_organization`
--

LOCK TABLES `yyba_organization` WRITE;
/*!40000 ALTER TABLE `yyba_organization` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_organization_member`
--

DROP TABLE IF EXISTS `yyba_organization_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_organization_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_organization_member`
--

LOCK TABLES `yyba_organization_member` WRITE;
/*!40000 ALTER TABLE `yyba_organization_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_organization_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_organization_vip`
--

DROP TABLE IF EXISTS `yyba_organization_vip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_organization_vip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_organization_vip`
--

LOCK TABLES `yyba_organization_vip` WRITE;
/*!40000 ALTER TABLE `yyba_organization_vip` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_organization_vip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_sign`
--

DROP TABLE IF EXISTS `yyba_sign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_sign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `sheet` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity` (`activity`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_sign`
--

LOCK TABLES `yyba_sign` WRITE;
/*!40000 ALTER TABLE `yyba_sign` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_sign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_tempuser`
--

DROP TABLE IF EXISTS `yyba_tempuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_tempuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `openid` text NOT NULL,
  `uid` int(11) NOT NULL,
  `nickname` text NOT NULL,
  `avatar` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_tempuser`
--

LOCK TABLES `yyba_tempuser` WRITE;
/*!40000 ALTER TABLE `yyba_tempuser` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_tempuser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_tipoff`
--

DROP TABLE IF EXISTS `yyba_tipoff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_tipoff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity` (`activity`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_tipoff`
--

LOCK TABLES `yyba_tipoff` WRITE;
/*!40000 ALTER TABLE `yyba_tipoff` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_tipoff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_token`
--

DROP TABLE IF EXISTS `yyba_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` text NOT NULL,
  `timeout` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_token`
--

LOCK TABLES `yyba_token` WRITE;
/*!40000 ALTER TABLE `yyba_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `yyba_user`
--

DROP TABLE IF EXISTS `yyba_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `yyba_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text CHARACTER SET latin1 NOT NULL,
  `password` text CHARACTER SET latin1 NOT NULL,
  `nickname` text CHARACTER SET latin1 NOT NULL,
  `avatar` text CHARACTER SET latin1 NOT NULL,
  `phonenumber` text CHARACTER SET latin1 NOT NULL,
   `verifycode` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `yyba_user`
--

LOCK TABLES `yyba_user` WRITE;
/*!40000 ALTER TABLE `yyba_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `yyba_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-08-31 13:11:31
