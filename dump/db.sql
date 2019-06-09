-- MySQL dump 10.17  Distrib 10.3.15-MariaDB, for Linux (x86_64)
--
-- Host: 95.46.44.23    Database: planetsbook
-- ------------------------------------------------------
-- Server version	5.7.26-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `pub_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `views` int(11) NOT NULL DEFAULT '0',
  `verifier_id` int(11) DEFAULT NULL,
  `section_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `articles_sections_id_fk` (`section_id`),
  KEY `articles_users_id_fk` (`verifier_id`),
  KEY `articles_users_id_fk_2` (`author_id`),
  CONSTRAINT `articles_sections_id_fk` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `articles_users_id_fk` FOREIGN KEY (`verifier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `articles_users_id_fk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (7,'Из чего состоит Солнечная система?','2019-06-09 14:31:21',3,1,11,1),(8,'О солнечных пятнах','2019-06-09 14:22:07',2,1,1,1),(10,'Судьба спутника Марса Фобоса','2019-06-09 14:57:01',2,1,13,1),(11,'Терраформирование Марса осложняется','2019-06-09 15:16:47',3,1,5,1),(12,'44 факта о Земле','2019-06-07 20:48:40',6,1,4,1),(13,'Тайна \"сердца\" Плутона','2019-06-09 14:35:33',2,1,10,1),(14,'Ученые открыли новый объект Солнечной системы','2019-06-09 14:22:07',2,1,11,1),(15,'Аппарат MAVEN установил скорость потери газа атмосферой Марса','2019-06-09 15:19:38',2,1,5,1),(16,'Зима на Титане','2019-06-09 14:49:00',2,1,15,1),(17,'NASA анонсировали пресс-конференцию о судьбе атмосферы Марса','2019-06-09 15:23:03',2,1,5,1),(18,'Существование экзопланеты в системе Альфа Центавра B опровержено','2019-06-09 15:01:51',3,1,17,1),(19,'Ученые выяснили происхождение органики в пробах лунного грунта','2019-06-09 15:07:40',3,1,12,1),(20,'NASA завершила первый этап работ над супертяжелой ракетой-носителем для полетов на Марс','2019-06-09 15:25:16',2,1,5,1),(21,'На Хароне обнаружен аммиачный кратер','2019-06-09 15:10:15',2,1,16,1),(22,'Новые изображения Цербера - спутника Плутона','2019-06-09 14:37:51',3,1,10,1),(23,'В составе кометы Лавджоя обнаружен этиловый спирт','2019-06-09 15:04:28',2,1,17,1),(24,'Исследования происхождения воды на Марсе','2019-06-09 15:28:21',3,1,5,1),(25,'Зонд \"Кассини\" получил изображение поверхности Титана','2019-06-09 14:52:32',10,1,15,1);
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comm_text` text,
  `add_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_articles_id_fk` (`article_id`),
  KEY `comments_users_id_fk` (`user_id`),
  CONSTRAINT `comments_articles_id_fk` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER after_comments_insert AFTER INSERT ON comments FOR EACH ROW
BEGIN
    UPDATE users u SET comments_cnt = comments_cnt + 1 WHERE u.id = NEW.user_id;
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
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 trigger after_comments_update
after UPDATE on comments
for each row
BEGIN
    IF OLD.user_id <> NEW.user_id THEN
        UPDATE users u SET comments_cnt = comments_cnt - 1 WHERE u.id = OLD.user_id;
        UPDATE users u SET comments_cnt = comments_cnt + 1 WHERE u.id = NEW.user_id;
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
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 trigger after_comments_delete
after DELETE on comments
for each row
BEGIN
    UPDATE users u SET comments_cnt = comments_cnt - 1 WHERE u.id = OLD.user_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `rates`
--

DROP TABLE IF EXISTS `rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rates` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`comment_id`,`user_id`),
  KEY `rates_users_id_fk` (`user_id`),
  CONSTRAINT `rates_comments_id_fk` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rates_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rates`
--

LOCK TABLES `rates` WRITE;
/*!40000 ALTER TABLE `rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `rates` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER after_rates_insert AFTER INSERT ON rates FOR EACH ROW
BEGIN
    UPDATE users u SET rating = rating + NEW.value WHERE u.id = NEW.user_id;
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
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER after_rates_update AFTER UPDATE ON rates FOR EACH ROW
BEGIN
    UPDATE users u SET rating = rating - OLD.value WHERE u.id = OLD.user_id;
    UPDATE users u SET rating = rating + NEW.value WHERE u.id = NEW.user_id;
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
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER after_rates_delete AFTER DELETE ON rates FOR EACH ROW
BEGIN
    UPDATE users u SET rating = rating - OLD.value WHERE u.id = OLD.user_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `data_folder` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `allow_user_articles` int(11) NOT NULL DEFAULT '0',
  `show_main` int(11) NOT NULL DEFAULT '1',
  `description` text,
  `big_image` int(11) DEFAULT NULL,
  `small_image` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `creator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sections_sections_id_fk` (`parent_id`),
  KEY `sections_users_id_fk` (`creator_id`),
  CONSTRAINT `sections_sections_id_fk` FOREIGN KEY (`parent_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sections_users_id_fk` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES (1,'Солнце','sun',NULL,1,1,'<h3>Солнце</h3>',1,2,0,'2019-06-09 12:22:20',1),(2,'Меркурий','mercury',NULL,1,1,'Меркурий',3,4,1,'2019-06-09 12:22:20',1),(3,'Венера','venus',NULL,1,1,'Венера',5,6,1,'2019-06-09 12:22:20',1),(4,'Земля','earth',NULL,1,1,'Земля',7,8,1,'2019-06-09 12:22:20',1),(5,'Марс','mars',NULL,1,1,'Марс',12,13,1,'2019-06-09 12:22:20',1),(6,'Юпитер','jupiter',NULL,1,1,'Юпитер',16,17,1,'2019-06-09 12:22:20',1),(7,'Сатурн','saturn',NULL,1,1,'Сатурн',18,19,1,'2019-06-09 12:22:20',1),(8,'Уран','uranus',NULL,1,1,'Уран',23,24,1,'2019-06-09 12:22:20',1),(9,'Нептун','neptune',NULL,1,1,'Нептун',23,24,1,'2019-06-09 12:22:20',1),(10,'Плутон','pluto',NULL,1,1,'Плутон',25,26,1,'2019-06-09 12:22:20',1),(11,'Солнечная система','solar_sys',NULL,1,0,'Солнечная система',NULL,11,0,'2019-06-09 12:22:20',1),(12,'Луна','moon',4,1,1,'Луна',9,10,2,'2019-06-09 12:22:20',1),(13,'Фобос и Деймос','phobos_deimos',5,1,1,'Фобос и Деймос',14,15,2,'2019-06-09 12:22:20',1),(14,'Ио','io',6,1,1,'Ио',30,31,2,'2019-06-09 12:22:20',1),(15,'Титан','titan',7,1,1,'Титан',20,22,2,'2019-06-09 12:22:20',1),(16,'Харон','charon',10,1,1,'Харон',27,28,2,'2019-06-09 12:22:20',1),(17,'Другое','other',NULL,1,1,'Другое',NULL,29,3,'2019-06-09 12:22:20',1);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `storage`
--

DROP TABLE IF EXISTS `storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(20) NOT NULL,
  `file_size` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` text,
  `add_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `storage_users_id_fk` (`user_id`),
  CONSTRAINT `storage_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `storage`
--

LOCK TABLES `storage` WRITE;
/*!40000 ALTER TABLE `storage` DISABLE KEYS */;
INSERT INTO `storage` VALUES (1,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(2,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(3,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(4,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(5,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(6,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(7,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(8,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(9,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(10,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(11,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(12,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(13,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(14,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(15,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(16,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(17,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(18,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(19,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(20,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(22,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(23,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(24,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(25,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(26,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(27,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(28,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(29,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(30,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(31,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(32,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(33,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(34,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(35,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(36,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(37,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(38,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(39,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(40,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(41,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(42,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(43,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(44,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(45,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(46,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(47,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(48,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(49,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(50,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(51,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(52,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(53,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(54,'png',0,NULL,NULL,'2019-06-09 12:24:16'),(55,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(56,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(57,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(58,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(59,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(60,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(61,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(62,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(63,'jpg',0,NULL,NULL,'2019-06-09 12:24:16'),(64,'jpg',7905,1,NULL,'2019-06-09 12:26:23');
/*!40000 ALTER TABLE `storage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `psw_hash` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `real_name` varchar(255) DEFAULT NULL,
  `avatar` int(11) DEFAULT NULL,
  `is_admin` int(11) NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_visit` datetime DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `comments_cnt` int(11) NOT NULL DEFAULT '0',
  `skype` varchar(100) DEFAULT NULL,
  `vk` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `site` varchar(100) DEFAULT NULL,
  `from_where` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'blurrybloop','blDL..AyTMmdA','blurrybloop@gmail.com','Стас Шевцов',64,1,'2019-06-07 20:29:52','2019-06-09 12:23:05',0,0,'shevtsov29','id10343432434','nope','nope','http://planetsbook.com','Украина'),(2,'vasia','vaQ/hE1aox1Vs','vasia@test.com','Вася Пупкин',NULL,0,'2019-06-09 15:29:40','2019-06-09 15:29:54',0,0,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


create procedure show_storage_usage(IN fromdate date, IN todate date, IN resolution int)
BEGIN
    select dayofmonth(gen_date) as day, month(gen_date) as month, year(gen_date) as year, coalesce(sum(s.file_size), 0) as balance from
    (select
        case resolution
            when 1 then adddate('1970-01-01', interval t4*10000 + t3*1000 + t2*100 + t1*10 + t0 day)
            else adddate('1970-01-01', interval t4*10000 + t3*1000 + t2*100 + t1*10 + t0 month)
        end gen_date
     from
     (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
     (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
     (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
     (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
     (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
    left join storage s ON case resolution
            when 1 then cast(s.add_date as date) <= gen_date
            else year(s.add_date) <= year(gen_date) && month(s.add_date) <= month(gen_date)
        end
    where gen_date between case resolution
            when 1 then fromdate
            else date_add(fromdate,interval -DAY(fromdate)+1 DAY)
        end and todate
    group by gen_date;
END;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-06-09 16:02:23
