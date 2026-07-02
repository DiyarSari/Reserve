-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: Reserve
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `address_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` smallint(5) unsigned NOT NULL,
  `district_id` int(10) unsigned NOT NULL,
  `neighborhood_id` int(10) unsigned DEFAULT NULL,
  `street_name` varchar(120) NOT NULL,
  `building_number` varchar(20) NOT NULL DEFAULT '',
  `floor_number` varchar(20) NOT NULL DEFAULT '',
  `unit_number` varchar(20) NOT NULL DEFAULT '',
  `direction_city_id` smallint(5) unsigned DEFAULT NULL,
  `direction_district_id` int(10) unsigned DEFAULT NULL,
  `direction_neighborhood_id` int(10) unsigned DEFAULT NULL,
  `direction_note` varchar(255) DEFAULT NULL,
  `postal_code` varchar(12) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  UNIQUE KEY `uq_addresses_full` (`city_id`,`district_id`,`neighborhood_id`,`street_name`,`building_number`,`floor_number`,`unit_number`,`postal_code`),
  KEY `fk_model_addresses_neighborhood` (`neighborhood_id`),
  KEY `idx_addresses_direction_city` (`direction_city_id`),
  KEY `idx_addresses_direction_district` (`direction_district_id`),
  KEY `idx_addresses_direction_neighborhood` (`direction_neighborhood_id`),
  KEY `idx_addresses_city_district` (`city_id`,`district_id`),
  KEY `idx_addresses_district_neighborhood` (`district_id`,`neighborhood_id`),
  CONSTRAINT `fk_model_addresses_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_model_addresses_direction_city` FOREIGN KEY (`direction_city_id`) REFERENCES `cities` (`city_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_model_addresses_direction_district` FOREIGN KEY (`direction_district_id`) REFERENCES `districts` (`district_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_model_addresses_direction_neighborhood` FOREIGN KEY (`direction_neighborhood_id`) REFERENCES `neighborhoods` (`neighborhood_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_model_addresses_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_model_addresses_neighborhood` FOREIGN KEY (`neighborhood_id`) REFERENCES `neighborhoods` (`neighborhood_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `city_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` smallint(5) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `plate_code` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`city_id`),
  UNIQUE KEY `uq_city_country_name` (`country_id`,`name`),
  UNIQUE KEY `uq_city_country_plate` (`country_id`,`plate_code`),
  KEY `idx_cities_country` (`country_id`),
  CONSTRAINT `fk_model_cities_country` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `country_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `iso_code` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `iso_code` (`iso_code`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuisine_types`
--

DROP TABLE IF EXISTS `cuisine_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuisine_types` (
  `cuisine_type_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`cuisine_type_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `day_of_weeks`
--

DROP TABLE IF EXISTS `day_of_weeks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `day_of_weeks` (
  `day_of_week_id` tinyint(3) unsigned NOT NULL,
  `code` varchar(12) NOT NULL,
  `label` varchar(20) NOT NULL,
  PRIMARY KEY (`day_of_week_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `district_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` smallint(5) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `uq_district_city_name` (`city_id`,`name`),
  KEY `idx_districts_city_id` (`city_id`),
  CONSTRAINT `fk_model_districts_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_categories`
--

DROP TABLE IF EXISTS `menu_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `display_order` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_menu_category_restaurant_name` (`restaurant_id`,`name`),
  KEY `idx_menu_categories_restaurant_order` (`restaurant_id`,`display_order`),
  CONSTRAINT `fk_menu_categories_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_menu_items_category` (`category_id`),
  KEY `idx_menu_items_restaurant_category` (`restaurant_id`,`category_id`),
  KEY `idx_menu_items_active` (`is_active`),
  CONSTRAINT `fk_menu_items_category` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_menu_items_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `neighborhoods`
--

DROP TABLE IF EXISTS `neighborhoods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `neighborhoods` (
  `neighborhood_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `district_id` int(10) unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY (`neighborhood_id`),
  UNIQUE KEY `uq_neighborhood_district_name` (`district_id`,`name`),
  KEY `idx_neighborhoods_district_id` (`district_id`),
  CONSTRAINT `fk_model_neighborhoods_district` FOREIGN KEY (`district_id`) REFERENCES `districts` (`district_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `partner_application_statuses`
--

DROP TABLE IF EXISTS `partner_application_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partner_application_statuses` (
  `partner_application_status_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `label` varchar(60) NOT NULL,
  PRIMARY KEY (`partner_application_status_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservation_statuses`
--

DROP TABLE IF EXISTS `reservation_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_statuses` (
  `reservation_status_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `label` varchar(60) NOT NULL,
  PRIMARY KEY (`reservation_status_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_code` varchar(40) NOT NULL,
  `restaurant_id` int(10) unsigned NOT NULL,
  `restaurant_name` varchar(160) NOT NULL,
  `table_id` int(10) unsigned DEFAULT NULL,
  `table_number` varchar(30) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_email` varchar(190) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `guest_count` tinyint(3) unsigned NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `status` enum('pending','confirmed','checked_in','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `qr_token` varchar(120) NOT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `owner_email` varchar(190) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservation_code` (`reservation_code`),
  UNIQUE KEY `qr_token` (`qr_token`),
  KEY `fk_reservations_table` (`table_id`),
  KEY `idx_reservations_customer_email` (`customer_email`),
  KEY `idx_reservations_owner_date` (`owner_email`,`reservation_date`),
  KEY `idx_reservations_restaurant_datetime` (`restaurant_id`,`reservation_date`,`reservation_time`),
  KEY `idx_reservations_status` (`status`),
  CONSTRAINT `fk_reservations_owner_email` FOREIGN KEY (`owner_email`) REFERENCES `users` (`email`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reservations_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reservations_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restaurant_open_hours`
--

DROP TABLE IF EXISTS `restaurant_open_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurant_open_hours` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `day_of_week` tinyint(3) unsigned NOT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL,
  `reservation_duration_minutes` smallint(5) unsigned NOT NULL DEFAULT 90,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_restaurant_day` (`restaurant_id`,`day_of_week`),
  CONSTRAINT `fk_open_hours_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restaurant_partner_applications`
--

DROP TABLE IF EXISTS `restaurant_partner_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurant_partner_applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_name` varchar(160) NOT NULL,
  `contact_name` varchar(150) NOT NULL,
  `restaurant_email` varchar(190) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `city` varchar(80) NOT NULL,
  `district` varchar(80) NOT NULL DEFAULT '',
  `neighborhood` varchar(120) NOT NULL DEFAULT '',
  `street` varchar(120) NOT NULL DEFAULT '',
  `avenue` varchar(120) NOT NULL DEFAULT '',
  `building_number` varchar(20) NOT NULL DEFAULT '',
  `floor_number` varchar(20) NOT NULL DEFAULT '',
  `apartment_number` varchar(20) NOT NULL DEFAULT '',
  `door_number` varchar(20) NOT NULL DEFAULT '',
  `postal_code` varchar(10) NOT NULL DEFAULT '',
  `address_notes` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL,
  `cuisine_type` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `linked_restaurant_id` int(10) unsigned DEFAULT NULL,
  `review_notes` varchar(500) DEFAULT NULL,
  `reviewed_by_email` varchar(190) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `address_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_partner_application_restaurant` (`linked_restaurant_id`),
  KEY `fk_partner_application_reviewer` (`reviewed_by_email`),
  KEY `idx_partner_application_status_created` (`status`,`created_at`),
  KEY `idx_partner_application_email` (`restaurant_email`),
  KEY `idx_partner_applications_location_full` (`city`,`district`,`neighborhood`,`postal_code`),
  CONSTRAINT `fk_partner_application_restaurant` FOREIGN KEY (`linked_restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_partner_application_reviewer` FOREIGN KEY (`reviewed_by_email`) REFERENCES `users` (`email`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restaurant_reviews`
--

DROP TABLE IF EXISTS `restaurant_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurant_reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(10) unsigned NOT NULL,
  `restaurant_id` int(10) unsigned NOT NULL,
  `user_email` varchar(190) NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_review_reservation` (`reservation_id`),
  KEY `idx_reviews_restaurant` (`restaurant_id`),
  KEY `idx_reviews_user_email` (`user_email`),
  CONSTRAINT `fk_reviews_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user_email` FOREIGN KEY (`user_email`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restaurant_statuses`
--

DROP TABLE IF EXISTS `restaurant_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurant_statuses` (
  `restaurant_status_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `label` varchar(60) NOT NULL,
  PRIMARY KEY (`restaurant_status_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `cuisine_type` varchar(80) NOT NULL,
  `city` varchar(80) NOT NULL,
  `district` varchar(80) NOT NULL DEFAULT '',
  `neighborhood` varchar(120) NOT NULL DEFAULT '',
  `street` varchar(120) NOT NULL DEFAULT '',
  `avenue` varchar(120) NOT NULL DEFAULT '',
  `building_number` varchar(20) NOT NULL DEFAULT '',
  `floor_number` varchar(20) NOT NULL DEFAULT '',
  `apartment_number` varchar(20) NOT NULL DEFAULT '',
  `door_number` varchar(20) NOT NULL DEFAULT '',
  `postal_code` varchar(10) NOT NULL DEFAULT '',
  `address_notes` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `price_range` enum('$','$$','$$$','$$$$') NOT NULL DEFAULT '$$',
  `cover_image` varchar(255) DEFAULT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL,
  `reservation_duration_minutes` smallint(5) unsigned NOT NULL DEFAULT 90,
  `owner_email` varchar(190) NOT NULL,
  `status` enum('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reservations` int(10) unsigned NOT NULL DEFAULT 0,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_restaurants_city` (`city`),
  KEY `idx_restaurants_cuisine` (`cuisine_type`),
  KEY `idx_restaurants_status` (`status`),
  KEY `idx_restaurants_featured_rating` (`is_featured`,`rating`),
  KEY `idx_restaurants_owner_email` (`owner_email`),
  KEY `idx_restaurants_location_full` (`city`,`district`,`neighborhood`,`postal_code`),
  CONSTRAINT `fk_restaurants_owner_email` FOREIGN KEY (`owner_email`) REFERENCES `users` (`email`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_types`
--

DROP TABLE IF EXISTS `role_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_types` (
  `role_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `label` varchar(60) NOT NULL,
  PRIMARY KEY (`role_type_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `level` enum('info','warning','error') NOT NULL DEFAULT 'info',
  `message` varchar(255) NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_logs_level` (`level`),
  KEY `idx_logs_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(10) unsigned NOT NULL,
  `table_number` varchar(30) NOT NULL,
  `capacity` tinyint(3) unsigned NOT NULL,
  `location` varchar(80) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_restaurant_table_number` (`restaurant_id`,`table_number`),
  KEY `idx_tables_capacity_active` (`capacity`,`is_active`),
  CONSTRAINT `fk_tables_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','host','user') NOT NULL DEFAULT 'user',
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_created_date` (`created_date`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-11 16:05:15
