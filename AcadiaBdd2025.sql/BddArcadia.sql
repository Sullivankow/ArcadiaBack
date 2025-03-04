-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: arcadia
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `animal`
--

DROP TABLE IF EXISTS `animal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `habitat_id` int DEFAULT NULL,
  `race_id` int DEFAULT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6AAB231FAFFE2D26` (`habitat_id`),
  KEY `IDX_6AAB231F6E59D40D` (`race_id`),
  CONSTRAINT `FK_6AAB231F6E59D40D` FOREIGN KEY (`race_id`) REFERENCES `race` (`id`),
  CONSTRAINT `FK_6AAB231FAFFE2D26` FOREIGN KEY (`habitat_id`) REFERENCES `habitat` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animal`
--

LOCK TABLES `animal` WRITE;
/*!40000 ALTER TABLE `animal` DISABLE KEYS */;
INSERT INTO `animal` VALUES (7,1,1,'Simba','En bonne santé'),(8,2,3,'Titi','Blessé'),(9,3,2,'Aquaman','En bonne santé'),(10,4,4,'Scorpio','En bonne santé'),(11,5,5,'Max','Malade'),(12,1,3,'Luna','En bonne santé'),(13,2,2,'Coco','En bonne santé'),(14,3,5,'Zoe','Blessé'),(16,5,4,'Fluffy','En bonne santé'),(17,1,1,'Test','Test'),(19,2,15,'Ajout Form Test','Ceci est un test d\'ajout formulaire'),(28,1,7,'teeeeest2','sain'),(30,1,7,'Lion','sain'),(31,1,7,'Lion','sain'),(32,1,7,'Lion','sain'),(33,1,7,'Lion','sain'),(34,1,7,'Lion','sain'),(35,1,7,'Lion','sain'),(36,1,7,'Lion','sain'),(41,3,18,'testtest','test'),(42,1,18,'test 2','Encore un test');
/*!40000 ALTER TABLE `animal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habitat`
--

DROP TABLE IF EXISTS `habitat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habitat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire_habitat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habitat`
--

LOCK TABLES `habitat` WRITE;
/*!40000 ALTER TABLE `habitat` DISABLE KEYS */;
INSERT INTO `habitat` VALUES (1,'Savane Africainee','Une vaste plaine herbeuse habitée par des lions, des éléphants et des girafes.','Commentaire sur la savane africaine'),(2,'Forêt tropicale','Un écosystème dense avec une grande biodiversité, abritant des singes, des tigres et des oiseaux exotiques.','Commentaire sur la forêt tropicale'),(3,'Habitat aquatique','Un environnement humide, avec des poissons, des tortues et des plantes aquatiques.','Commentaire sur l\'habitat aquatique'),(4,'Désertique','Un environnement aride avec des cactus, des serpents et des lézards.','Commentaire sur l\'habitat désertique'),(5,'Montagneux','Un habitat caractérisé par des pentes escarpées et des animaux comme les chèvres de montagne et les aigles.','Commentaire sur l\'habitat montagneux');
/*!40000 ALTER TABLE `habitat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habitat_image`
--

DROP TABLE IF EXISTS `habitat_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habitat_image` (
  `habitat_id` int NOT NULL,
  `image_id` int NOT NULL,
  PRIMARY KEY (`habitat_id`,`image_id`),
  KEY `IDX_9AD7E031AFFE2D26` (`habitat_id`),
  KEY `IDX_9AD7E0313DA5256D` (`image_id`),
  CONSTRAINT `FK_9AD7E0313DA5256D` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9AD7E031AFFE2D26` FOREIGN KEY (`habitat_id`) REFERENCES `habitat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habitat_image`
--

LOCK TABLES `habitat_image` WRITE;
/*!40000 ALTER TABLE `habitat_image` DISABLE KEYS */;
INSERT INTO `habitat_image` VALUES (1,9),(2,8),(3,6),(3,7),(4,2),(5,3),(5,4),(5,5);
/*!40000 ALTER TABLE `habitat_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image`
--

LOCK TABLES `image` WRITE;
/*!40000 ALTER TABLE `image` DISABLE KEYS */;
INSERT INTO `image` VALUES (2,'desert-habitat.jpg'),(3,'montagne-habitat.jpg'),(4,'montagne-habitat.jpg'),(5,'montagne-habitat.jpg'),(6,'aquatique-habitat.jpg'),(7,'aquatique-habitat.jpg'),(8,'foret-habitat.jpg'),(9,'savane-habitat.jpg');
/*!40000 ALTER TABLE `image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `race`
--

DROP TABLE IF EXISTS `race`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `race` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `race`
--

LOCK TABLES `race` WRITE;
/*!40000 ALTER TABLE `race` DISABLE KEYS */;
INSERT INTO `race` VALUES (1,'Lion Test'),(2,'Éléphant'),(3,'Girafe'),(4,'Zèbre'),(5,'Gazelle'),(6,'Tigre'),(7,'Panthère'),(8,'Crocodile'),(9,'Ours'),(10,'Aigle'),(11,'Hippopotame'),(12,'Kangourou'),(13,'Gorille'),(14,'Chimpanzé'),(15,'Bison'),(16,'Rhinocéros'),(17,'Loup'),(18,'Ours polaire'),(19,'Jaguar'),(20,'Lynx'),(21,'Lama'),(22,'Coyote'),(23,'Baleine'),(24,'Dauphin'),(25,'Cheetah'),(26,'Serpent'),(27,'Tortue'),(28,'Panda'),(29,'Koala'),(30,'Jaguarondi'),(31,'Morse'),(32,'Moufette'),(33,'Gnou'),(34,'Pangolin'),(35,'Wombat'),(36,'Orang-outan'),(37,'Chacal'),(38,'Cobra'),(39,'Perroquet'),(40,'Corbeau'),(41,'Flamant rose'),(42,'Paon'),(43,'Perdrix'),(44,'Pélican'),(45,'Vautour'),(46,'Autruche'),(47,'Canard'),(48,'Faucon'),(49,'Aigle royal'),(50,'Vison');
/*!40000 ALTER TABLE `race` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rapport_veterinaire`
--

DROP TABLE IF EXISTS `rapport_veterinaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rapport_veterinaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `animal_id` int DEFAULT NULL,
  `date` date NOT NULL,
  `detail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CE729CDEA76ED395` (`user_id`),
  KEY `IDX_CE729CDE8E962C16` (`animal_id`),
  CONSTRAINT `FK_CE729CDE8E962C16` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`),
  CONSTRAINT `FK_CE729CDEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rapport_veterinaire`
--

LOCK TABLES `rapport_veterinaire` WRITE;
/*!40000 ALTER TABLE `rapport_veterinaire` DISABLE KEYS */;
INSERT INTO `rapport_veterinaire` VALUES (2,NULL,30,'2025-02-20','Bandage sur la patte droite'),(4,2,32,'2025-02-24','Ceci est un test de modification de rapport, Lion a mangé un menu maxi best of big mac et 700 kg de vande hachée'),(6,NULL,34,'2025-02-20','Bandage sur la patte droite'),(7,NULL,35,'2025-02-20','Bandage sur la patte droite'),(8,2,11,'2025-02-22','Détail du rapport vétérinaire,Bandage sur la patte droite'),(10,2,10,'2025-02-24','Test de modif de rapport'),(14,2,12,'2025-02-22','Examen de routine');
/*!40000 ALTER TABLE `rapport_veterinaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  KEY `IDX_8D93D649D60322AC` (`role_id`),
  CONSTRAINT `FK_8D93D649D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,NULL,'admin@email.com','[\"ROLE_ADMIN\"]','$2y$13$lYn/LihTAju9DpT9AA9ecu.VL9K7uCEZsBDqTZH1TTqrIV1C.KIFm',NULL,NULL,'cd3a26c17a07ba03acdb9b4378ac278f00fae2ab'),(2,NULL,'veterinaire@email.com','[\"ROLE_VETERINAIRE\"]','$2y$13$LUx0v42V4CxxsZW2JXy6YO6AMJC8K7klrobvErXQoDqlNbCJbuh5G',NULL,NULL,'d1028f099b8ad1acde002131fef2dc4b49069afc'),(3,NULL,'employe@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$uUaUXKaOOzl4jKdt9/J6buImsv0ADopZqOFbYwPaNHjO8Kcrzi6ju',NULL,NULL,'b3eb038f2bc3f425cbb917ce444a7ad424852353'),(5,NULL,'employe2@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$tz41qviGx7R1hpCLfXFCoulWjmu7lrTpCw1eZ/ENWL0MWq1964r02',NULL,NULL,'00218758e98749e77f834c7cebf469d9bbd219b3'),(7,NULL,'employe3@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$mLaFztKaymXHVniRSviCRea.JeDMFW1LfjPVi2NolKvll6HuFyBUW',NULL,NULL,'ecfd049ff479e730a2067e8a3e709f5962414677'),(8,NULL,'employe4@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$73ju0iHrh.cWnTfZUIpmu.TW12zPdxOx36.p6dD.IVn4UNbnst9Em',NULL,NULL,'9b4bf5d07e8f6b2dbe795faae17a07e7327188c5'),(9,NULL,'employe5@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$fQGNZMuOrGQN5GcN7goO4uvMXN.cL4j0f6FYGpPAEx1auA7g/gdp2',NULL,NULL,'d7d38436e804003873ebf2ff23694e7f9cfa09be'),(10,NULL,'employe6@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$p2pLnl3dTvl1vs/PaKkzC.Keeva4HFJkgCmHzYcmGl2/NgQCa1TFK',NULL,NULL,'efce7794511cb9eab3811427ca9af0069adf4ecd'),(11,NULL,'employe7@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$FvgqQRr4Kcszp24o91ke..SfWOTTcgY559u6LF/cDxuPtlCqv3./.',NULL,NULL,'5d0cf808de3f977577f8116d7ca5510d32fd12f3'),(12,NULL,'employe8@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$GvKEP2B2VrmupZQd1jYQde90yn6SSF7/1rYrzuyxTl9ZbhfjfIt3y',NULL,NULL,'e7acc67a08d7a17989563f35a4b321e4868f259b'),(13,NULL,'employe9@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$nqECk.MWuT/iRi84Y.RR2uRNvBCzsIrBy2Tkh4AnsvBN45qnms04O',NULL,NULL,'a20195b80e9347e588bbc86656509c4c306f7887'),(16,NULL,'employe10@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$vnS1X.K7WQz.tp5fWEs6w.SXoq3Qya.XriLsqgihqLZLKWdouEPVu',NULL,NULL,'ac47481e52fa962549534fb64f3042fb9568d176'),(17,NULL,'employe11@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$YDO6DS9JmwRznHLl52POMuKaEO5uM.VvBQMq9QLpNh8VxbUg5hbhq',NULL,NULL,'b91495606f8fc389b6d8b9e81c9b08d65a3279ac'),(18,NULL,'employe12@email.com','[\"ROLE_EMPLOYE\"]','$2y$13$9ECRzFgbSNWd3WbfDuyVfuTFMn/qfRpkQkoNVUrndfZEREeLNcNTG',NULL,NULL,'c8b89d0b4ffd7bf8ad8828646e57d3c602b16025');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-04 17:16:13
