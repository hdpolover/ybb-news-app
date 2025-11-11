time="2025-11-11T21:03:14+07:00" level=warning msg="The \"MYSQL_EXTRA_OPTIONS\" variable is not set. Defaulting to a blank string."
time="2025-11-11T21:03:14+07:00" level=warning msg="The \"MYSQL_EXTRA_OPTIONS\" variable is not set. Defaulting to a blank string."
mysqldump: [Warning] Using a password on the command line interface can be insecure.
mysqldump: Error: 'Access denied; you need (at least one of) the PROCESS privilege(s) for this operation' when trying to dump tablespaces
-- MySQL dump 10.13  Distrib 8.0.32, for Linux (aarch64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ad_clicks`
--

DROP TABLE IF EXISTS `ad_clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_clicks` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `ad_id` char(36) NOT NULL,
  `impression_id` char(36) DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `click_url` varchar(500) DEFAULT NULL,
  `page_url` varchar(500) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_clicks_tenant_id_foreign` (`tenant_id`),
  KEY `ad_clicks_ad_id_foreign` (`ad_id`),
  KEY `ad_clicks_impression_id_foreign` (`impression_id`),
  KEY `ad_clicks_user_id_foreign` (`user_id`),
  CONSTRAINT `ad_clicks_ad_id_foreign` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ad_clicks_impression_id_foreign` FOREIGN KEY (`impression_id`) REFERENCES `ad_impressions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ad_clicks_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ad_clicks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_clicks`
--

LOCK TABLES `ad_clicks` WRITE;
/*!40000 ALTER TABLE `ad_clicks` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_clicks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ad_impressions`
--

DROP TABLE IF EXISTS `ad_impressions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_impressions` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `ad_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `page_url` varchar(500) DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_impressions_tenant_id_foreign` (`tenant_id`),
  KEY `ad_impressions_ad_id_foreign` (`ad_id`),
  KEY `ad_impressions_user_id_foreign` (`user_id`),
  CONSTRAINT `ad_impressions_ad_id_foreign` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ad_impressions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ad_impressions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_impressions`
--

LOCK TABLES `ad_impressions` WRITE;
/*!40000 ALTER TABLE `ad_impressions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ad_impressions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_tenants`
--

DROP TABLE IF EXISTS `admin_tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_tenants` (
  `id` char(36) NOT NULL,
  `admin_id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_tenants_admin_id_tenant_id_unique` (`admin_id`,`tenant_id`),
  KEY `admin_tenants_admin_id_index` (`admin_id`),
  KEY `admin_tenants_tenant_id_index` (`tenant_id`),
  CONSTRAINT `admin_tenants_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `admin_tenants_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_tenants`
--

LOCK TABLES `admin_tenants` WRITE;
/*!40000 ALTER TABLE `admin_tenants` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','support') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES ('2043841a-a6c6-4e72-8f1a-6137d03008a3','Super Admin','superadmin@gmail.com',NULL,'$2y$12$oGxSnwr1PXDMQYfBhXMGg.VUpI77B4VAv.FedZR.k05VIEbxH0a3O','superadmin',1,NULL,NULL,'{\"theme\": \"dark\", \"notifications\": true}',NULL,'2025-11-06 05:16:27','2025-11-06 05:16:27');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ads` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `type` varchar(255) NOT NULL DEFAULT 'banner',
  `placement` json NOT NULL,
  `content` json NOT NULL,
  `targeting` json DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `priority` int NOT NULL DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `max_impressions` int DEFAULT NULL,
  `max_clicks` int DEFAULT NULL,
  `current_impressions` int NOT NULL DEFAULT '0',
  `current_clicks` int NOT NULL DEFAULT '0',
  `click_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_by` char(36) DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ads_tenant_id_foreign` (`tenant_id`),
  KEY `ads_created_by_foreign` (`created_by`),
  KEY `ads_updated_by_foreign` (`updated_by`),
  CONSTRAINT `ads_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analytics_events`
--

DROP TABLE IF EXISTS `analytics_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analytics_events` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `event_category` varchar(255) DEFAULT NULL,
  `event_action` varchar(255) DEFAULT NULL,
  `event_label` varchar(255) DEFAULT NULL,
  `event_value` decimal(10,2) DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `content_id` char(36) DEFAULT NULL,
  `content_type` varchar(255) DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `utm_params` json DEFAULT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `custom_data` json DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `analytics_events_tenant_id_foreign` (`tenant_id`),
  KEY `analytics_events_user_id_foreign` (`user_id`),
  CONSTRAINT `analytics_events_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `analytics_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytics_events`
--

LOCK TABLES `analytics_events` WRITE;
/*!40000 ALTER TABLE `analytics_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `analytics_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `auditable_type` varchar(255) NOT NULL,
  `auditable_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_tenant_id_created_at_index` (`tenant_id`,`created_at`),
  KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  KEY `audit_logs_event_index` (`event`),
  CONSTRAINT `audit_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('livewire-rate-limiter:0e8367d0c394ea8e8d16fc7f5084e4eddb908906','i:2;',1762869689),('livewire-rate-limiter:0e8367d0c394ea8e8d16fc7f5084e4eddb908906:timer','i:1762869689;',1762869689),('spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:30:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:10:\"view_posts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"create_posts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"edit_posts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:12:\"delete_posts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:13:\"publish_posts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:13:\"view_programs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:15:\"create_programs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"edit_programs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"delete_programs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"publish_programs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:9:\"view_jobs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:11:\"create_jobs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:9:\"edit_jobs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:11:\"delete_jobs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:6;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"publish_jobs\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:10:\"view_media\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:12:\"upload_media\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:12:\"delete_media\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:10:\"view_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:12:\"create_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:10:\"edit_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"delete_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:8:\"view_ads\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:10:\"create_ads\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:8:\"edit_ads\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:10:\"delete_ads\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:10:\"manage_seo\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:14:\"view_analytics\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:7;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:15:\"manage_settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:13:\"manage_tenant\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"Tenant Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:6:\"Editor\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:6:\"Author\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:9:\"Moderator\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:7:\"Analyst\";s:1:\"c\";s:3:\"web\";}}}',1762955375);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_domain_unique` (`domain`),
  KEY `domains_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `domains_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (1,'testbeas.com','aa74790d-0eef-4bd4-9947-427264735577','2025-11-06 06:42:11','2025-11-06 06:42:11');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_campaigns`
--

DROP TABLE IF EXISTS `email_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_campaigns` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `preview_text` text,
  `content` longtext NOT NULL,
  `type` enum('newsletter','digest','announcement','promotional') NOT NULL DEFAULT 'newsletter',
  `status` enum('draft','scheduled','sending','sent','paused','cancelled') NOT NULL DEFAULT 'draft',
  `recipient_criteria` json DEFAULT NULL,
  `estimated_recipients` int unsigned NOT NULL DEFAULT '0',
  `actual_recipients` int unsigned NOT NULL DEFAULT '0',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `emails_sent` int unsigned NOT NULL DEFAULT '0',
  `emails_delivered` int unsigned NOT NULL DEFAULT '0',
  `emails_opened` int unsigned NOT NULL DEFAULT '0',
  `emails_clicked` int unsigned NOT NULL DEFAULT '0',
  `emails_bounced` int unsigned NOT NULL DEFAULT '0',
  `emails_unsubscribed` int unsigned NOT NULL DEFAULT '0',
  `open_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `click_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `bounce_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `template` varchar(255) DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_campaigns_tenant_id_foreign` (`tenant_id`),
  KEY `email_campaigns_created_by_foreign` (`created_by`),
  KEY `email_campaigns_updated_by_foreign` (`updated_by`),
  CONSTRAINT `email_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_campaigns_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_campaigns_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_campaigns`
--

LOCK TABLES `email_campaigns` WRITE;
/*!40000 ALTER TABLE `email_campaigns` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `post_id` char(36) DEFAULT NULL,
  `uploaded_by` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json DEFAULT NULL,
  `custom_properties` json DEFAULT NULL,
  `generated_conversions` json DEFAULT NULL,
  `responsive_images` json DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text,
  `collection_name` varchar(255) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `usage_count` int unsigned NOT NULL DEFAULT '0',
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_post_id_foreign` (`post_id`),
  KEY `media_uploaded_by_foreign` (`uploaded_by`),
  KEY `media_tenant_id_folder_index` (`tenant_id`,`folder`),
  CONSTRAINT `media_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `media_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_09_25_000001_create_tenants_table',1),(2,'2025_09_25_000002_create_admins_table',1),(3,'2025_09_25_000003_create_domains_table',1),(4,'2025_09_25_000004_create_users_table',1),(5,'2025_09_25_000005_create_posts_table',1),(6,'2025_09_25_000006_create_pt_program_table',1),(7,'2025_09_25_000007_create_pt_job_table',1),(8,'2025_09_25_000008_create_terms_table',1),(9,'2025_09_25_000009_create_term_post_table',1),(10,'2025_09_25_000010_create_media_table',1),(11,'2025_09_25_000011_create_ads_table',1),(12,'2025_09_25_000012_create_ad_impressions_table',1),(13,'2025_09_25_000013_create_ad_clicks_table',1),(14,'2025_09_25_000014_create_seo_landings_table',1),(15,'2025_09_25_000015_create_redirects_table',1),(16,'2025_09_25_000016_create_newsletter_subscriptions_table',1),(17,'2025_09_25_000017_create_email_campaigns_table',1),(18,'2025_09_25_000018_create_analytics_events_table',1),(19,'2025_09_25_000019_create_sessions_table',1),(20,'2025_09_25_000020_create_cache_table',1),(21,'2025_09_25_000021_create_permission_tables',1),(22,'2025_09_25_000022_create_failed_jobs_table',1),(23,'2025_10_21_071748_create_personal_access_tokens_table',1),(24,'2025_11_06_045056_create_user_tenants_table',1),(25,'2025_11_06_045103_create_admin_tenants_table',1),(26,'2025_11_06_065240_make_tenant_domain_nullable',2),(27,'2025_11_06_065651_fix_user_tenants_id_generation',3),(28,'2025_11_06_100000_remove_redundant_tenant_id_from_pt_program',4),(29,'2025_11_06_100001_remove_redundant_tenant_id_from_pt_job',4),(30,'2025_11_06_100002_remove_redundant_tenant_id_from_term_post',4),(31,'2025_11_06_081838_create_post_revisions_table',5),(32,'2025_11_06_081853_create_post_comments_table',5),(33,'2025_11_06_104554_create_audit_logs_table',6),(34,'2025_11_06_104736_add_folder_to_media_table',7),(35,'2025_11_06_151548_create_subscriber_segments_table',8);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` char(36) NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User','37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6'),(1,'App\\Models\\User','3e6d4b3a-b81c-4395-ac46-80b613ca0ce1'),(3,'App\\Models\\User','3f0f0f6b-a011-4aec-9655-1184e25ee82a'),(1,'App\\Models\\User','6ee118ba-3396-4013-b988-b1820f5c48ae'),(2,'App\\Models\\User','776e3c19-8e01-4712-8cbb-bfa9a887cd35'),(1,'App\\Models\\User','a04a5cb0-fc55-4154-9a8c-77d02fecc57a'),(2,'App\\Models\\User','a04abbda-c525-4621-91e5-a02647838d09');
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_subscriptions`
--

DROP TABLE IF EXISTS `newsletter_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_subscriptions` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `preferences` json DEFAULT NULL,
  `status` enum('active','unsubscribed','bounced','pending') NOT NULL DEFAULT 'pending',
  `frequency` varchar(255) NOT NULL DEFAULT 'weekly',
  `verification_token` varchar(255) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `unsubscribe_token` varchar(255) NOT NULL,
  `tags` json DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `last_sent_at` datetime DEFAULT NULL,
  `emails_sent` int unsigned NOT NULL DEFAULT '0',
  `emails_opened` int unsigned NOT NULL DEFAULT '0',
  `links_clicked` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_subscriptions_tenant_id_email_unique` (`tenant_id`,`email`),
  CONSTRAINT `newsletter_subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscriptions`
--

LOCK TABLES `newsletter_subscriptions` WRITE;
/*!40000 ALTER TABLE `newsletter_subscriptions` DISABLE KEYS */;
INSERT INTO `newsletter_subscriptions` VALUES ('1825b4c7-6d2a-48fa-922b-e57c5622d0c8','aa74790d-0eef-4bd4-9947-427264735577','subscriber@example.com',NULL,NULL,'active','weekly',NULL,NULL,'05odbJHLcm6J3A4sqxsjm5jzJbjYlaUB',NULL,NULL,NULL,NULL,NULL,0,0,0,'2025-11-06 05:01:10','2025-11-06 05:01:10');
/*!40000 ALTER TABLE `newsletter_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_posts','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(2,'create_posts','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(3,'edit_posts','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(4,'delete_posts','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(5,'publish_posts','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(6,'view_programs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(7,'create_programs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(8,'edit_programs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(9,'delete_programs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(10,'publish_programs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(11,'view_jobs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(12,'create_jobs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(13,'edit_jobs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(14,'delete_jobs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(15,'publish_jobs','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(16,'view_media','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(17,'upload_media','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(18,'delete_media','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(19,'view_users','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(20,'create_users','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(21,'edit_users','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(22,'delete_users','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(23,'view_ads','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(24,'create_ads','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(25,'edit_ads','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(26,'delete_ads','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(27,'manage_seo','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(28,'view_analytics','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(29,'manage_settings','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(30,'manage_tenant','web','2025-11-06 05:01:09','2025-11-06 05:01:09');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_comments`
--

DROP TABLE IF EXISTS `post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_comments` (
  `id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `parent_id` char(36) DEFAULT NULL,
  `comment` text NOT NULL,
  `type` enum('internal','review','approval') NOT NULL DEFAULT 'internal',
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comments_post_id_index` (`post_id`),
  KEY `post_comments_user_id_index` (`user_id`),
  KEY `post_comments_parent_id_index` (`parent_id`),
  KEY `post_comments_type_index` (`type`),
  KEY `post_comments_is_resolved_index` (`is_resolved`),
  KEY `post_comments_created_at_index` (`created_at`),
  CONSTRAINT `post_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_comments`
--

LOCK TABLES `post_comments` WRITE;
/*!40000 ALTER TABLE `post_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_revisions`
--

DROP TABLE IF EXISTS `post_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_revisions` (
  `id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` text,
  `content` longtext,
  `excerpt` text,
  `meta` json DEFAULT NULL,
  `revision_number` int NOT NULL,
  `change_summary` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_revisions_post_id_index` (`post_id`),
  KEY `post_revisions_user_id_index` (`user_id`),
  KEY `post_revisions_created_at_index` (`created_at`),
  CONSTRAINT `post_revisions_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_revisions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_revisions`
--

LOCK TABLES `post_revisions` WRITE;
/*!40000 ALTER TABLE `post_revisions` DISABLE KEYS */;
INSERT INTO `post_revisions` VALUES ('a04acd7d-efe7-45c9-90fc-0f14d8202904','a04ac6da-d306-49ad-94da-6681c9171c5f','a04abbda-c525-4621-91e5-a02647838d09','fef','feffefef','<p>fefef tyty</p>','fefe','{\"kind\": \"news\", \"status\": \"scheduled\", \"published_at\": null}',1,'Post updated','2025-11-06 12:15:56','2025-11-06 12:15:56'),('a04acda8-b4f5-4f39-9c3f-50c4b46ebbd0','a04ac6da-d306-49ad-94da-6681c9171c5f','a04abbda-c525-4621-91e5-a02647838d09','fef','feffefef','<p>fefef tyty</p>','fefe','{\"kind\": \"news\", \"status\": \"scheduled\", \"published_at\": null}',2,'Post updated','2025-11-06 12:16:24','2025-11-06 12:16:24');
/*!40000 ALTER TABLE `post_revisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `kind` enum('page','news','guide','program','job') NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text,
  `content` longtext,
  `status` enum('draft','review','scheduled','published','archived') NOT NULL DEFAULT 'draft',
  `cover_image_url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `og_image_url` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_tenant_id_slug_unique` (`tenant_id`,`slug`),
  KEY `posts_created_by_foreign` (`created_by`),
  KEY `posts_updated_by_foreign` (`updated_by`),
  CONSTRAINT `posts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES ('a04a37c1-b71e-409c-a07c-fe0496321ef3','aa74790d-0eef-4bd4-9947-427264735577','news','Beasiswa LPDP 2025: Panduan Lengkap Pendaftaran','beasiswa-lpdp-2025-panduan-lengkap-pendaftaran','Pelajari syarat, tahapan, dan tips lolos seleksi beasiswa LPDP untuk kuliah di dalam dan luar negeri.','<p>Beasiswa LPDP (Lembaga Pengelola Dana Pendidikan) adalah program beasiswa yang diselenggarakan oleh pemerintah Indonesia untuk mendanai pendidikan lanjutan bagi warga negara Indonesia.</p><p>Program ini mencakup berbagai jenjang pendidikan mulai dari S2, S3, hingga program profesional di dalam maupun luar negeri.</p>','published',NULL,'Beasiswa LPDP 2025: Panduan Lengkap Pendaftaran','Pelajari syarat, tahapan, dan tips lolos seleksi beasiswa LPDP untuk kuliah di dalam dan luar negeri.',NULL,NULL,'2025-11-04 05:17:14',NULL,'6ee118ba-3396-4013-b988-b1820f5c48ae','6ee118ba-3396-4013-b988-b1820f5c48ae','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-bc46-40bd-ada3-8fc37b0dc1f1','aa74790d-0eef-4bd4-9947-427264735577','guide','Beasiswa Unggulan Kemendikbud: Syarat dan Cara Daftar','beasiswa-unggulan-kemendikbud-syarat-dan-cara-daftar','Informasi terbaru tentang Beasiswa Unggulan Kemendikbud untuk mahasiswa berprestasi.','<p>Beasiswa Unggulan Kemendikbud adalah program beasiswa yang diberikan kepada mahasiswa berprestasi untuk melanjutkan pendidikan S1, S2, dan S3.</p>','published',NULL,'Beasiswa Unggulan Kemendikbud: Syarat dan Cara Daftar','Informasi terbaru tentang Beasiswa Unggulan Kemendikbud untuk mahasiswa berprestasi.',NULL,NULL,'2025-11-01 05:17:14',NULL,'6ee118ba-3396-4013-b988-b1820f5c48ae','6ee118ba-3396-4013-b988-b1820f5c48ae','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-bd4a-4923-98b8-1cb8cebbfb9f','aa74790d-0eef-4bd4-9947-427264735577','guide','Tips Menulis Motivation Letter untuk Beasiswa','tips-menulis-motivation-letter-untuk-beasiswa','Panduan praktis menulis motivation letter yang menarik perhatian pemberi beasiswa.','<p>Motivation letter adalah salah satu dokumen penting dalam aplikasi beasiswa. Berikut adalah tips untuk menulis motivation letter yang efektif.</p>','published',NULL,'Tips Menulis Motivation Letter untuk Beasiswa','Panduan praktis menulis motivation letter yang menarik perhatian pemberi beasiswa.',NULL,NULL,'2025-11-05 05:17:14',NULL,'6ee118ba-3396-4013-b988-b1820f5c48ae','6ee118ba-3396-4013-b988-b1820f5c48ae','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-be96-4e48-aa46-63c6c5033a98','6d747b76-0658-4e8b-a412-b68273685097','job','Senior Backend Engineer - Remote','senior-backend-engineer-remote','Join our team as a Senior Backend Engineer. Work remotely with competitive salary.','<p>We are looking for an experienced Backend Engineer to join our growing team.</p>','published',NULL,'Senior Backend Engineer - Remote','Join our team as a Senior Backend Engineer. Work remotely with competitive salary.',NULL,NULL,'2025-11-03 05:17:14',NULL,'37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6','37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-bfcc-42f9-8210-5e6642c452a6','6d747b76-0658-4e8b-a412-b68273685097','job','Frontend Developer - React & Next.js','frontend-developer-react-nextjs','Looking for passionate Frontend Developer with React and Next.js experience.','<p>Join our product team and help build amazing user experiences.</p>','published',NULL,'Frontend Developer - React & Next.js','Looking for passionate Frontend Developer with React and Next.js experience.',NULL,NULL,'2025-11-05 05:17:14',NULL,'37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6','37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-c0de-44f5-87a2-44cbd1e03319','c4bb9895-964a-4376-835b-9697fe592b8f','program','Youth Leadership Summit 2025','youth-leadership-summit-2025','Join the biggest youth leadership conference in Indonesia. Register now!','<p>Youth Breaking Barriers presents the Youth Leadership Summit 2025, a platform for young leaders to connect, learn, and inspire.</p>','published',NULL,'Youth Leadership Summit 2025','Join the biggest youth leadership conference in Indonesia. Register now!',NULL,NULL,'2025-11-02 05:17:14',NULL,'3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-c1f0-4155-9328-e173d33c3e1c','c4bb9895-964a-4376-835b-9697fe592b8f','program','Volunteer Program: Education for All','volunteer-program-education-for-all','Be part of our volunteer program to bring quality education to remote areas.','<p>Help us make a difference by teaching underprivileged children in remote villages across Indonesia.</p>','published',NULL,'Volunteer Program: Education for All','Be part of our volunteer program to bring quality education to remote areas.',NULL,NULL,'2025-11-04 05:17:14',NULL,'3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04ac6da-d306-49ad-94da-6681c9171c5f','a04a5cb0-b2b3-467f-95af-8179a84ced85','news','fef','feffefef','fefe','<p>fefef tyty</p>','published',NULL,'frttr','trtr',NULL,NULL,'2025-11-08 18:57:05',NULL,'a04abbda-c525-4621-91e5-a02647838d09','a04abbda-c525-4621-91e5-a02647838d09','2025-11-06 11:57:22','2025-11-08 18:58:00');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pt_job`
--

DROP TABLE IF EXISTS `pt_job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pt_job` (
  `id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `employment_type` enum('full_time','part_time','contract','internship') NOT NULL,
  `workplace_type` enum('onsite','hybrid','remote') NOT NULL,
  `title_override` varchar(255) DEFAULT NULL,
  `location_city` varchar(255) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `min_salary` decimal(18,2) DEFAULT NULL,
  `max_salary` decimal(18,2) DEFAULT NULL,
  `salary_currency` varchar(3) DEFAULT NULL,
  `salary_period` enum('year','month','day','hour') DEFAULT NULL,
  `experience_level` enum('junior','mid','senior','lead') DEFAULT NULL,
  `responsibilities` longtext,
  `requirements` longtext,
  `benefits` json DEFAULT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `apply_url` varchar(255) NOT NULL,
  `extra` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pt_job_post_id_unique` (`post_id`),
  CONSTRAINT `pt_job_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pt_job`
--

LOCK TABLES `pt_job` WRITE;
/*!40000 ALTER TABLE `pt_job` DISABLE KEYS */;
/*!40000 ALTER TABLE `pt_job` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pt_program`
--

DROP TABLE IF EXISTS `pt_program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pt_program` (
  `id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `program_type` enum('scholarship','opportunity','internship') NOT NULL,
  `organizer_name` varchar(255) NOT NULL,
  `location_text` varchar(255) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `is_rolling` tinyint(1) NOT NULL DEFAULT '0',
  `funding_type` enum('fully_funded','partially_funded','unfunded') DEFAULT NULL,
  `stipend_amount` decimal(18,2) DEFAULT NULL,
  `fee_amount` decimal(18,2) DEFAULT NULL,
  `program_length_text` varchar(255) DEFAULT NULL,
  `eligibility_text` text,
  `apply_url` varchar(255) NOT NULL,
  `extra` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pt_program_post_id_unique` (`post_id`),
  CONSTRAINT `pt_program_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pt_program`
--

LOCK TABLES `pt_program` WRITE;
/*!40000 ALTER TABLE `pt_program` DISABLE KEYS */;
/*!40000 ALTER TABLE `pt_program` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `redirects`
--

DROP TABLE IF EXISTS `redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirects` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `from_url` varchar(255) NOT NULL,
  `to_url` varchar(255) NOT NULL,
  `status_code` enum('301','302','307','308') NOT NULL DEFAULT '301',
  `description` text,
  `hits` int unsigned NOT NULL DEFAULT '0',
  `last_used_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_automatic` tinyint(1) NOT NULL DEFAULT '0',
  `created_reason` varchar(255) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `redirects_tenant_id_from_url_unique` (`tenant_id`,`from_url`),
  KEY `redirects_created_by_foreign` (`created_by`),
  CONSTRAINT `redirects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `redirects_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `redirects`
--

LOCK TABLES `redirects` WRITE;
/*!40000 ALTER TABLE `redirects` DISABLE KEYS */;
/*!40000 ALTER TABLE `redirects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(1,2),(2,2),(3,2),(5,2),(6,2),(7,2),(8,2),(10,2),(11,2),(12,2),(13,2),(15,2),(1,3),(2,3),(3,3),(6,3),(7,3),(8,3),(11,3),(12,3),(13,3),(1,6),(4,6),(11,6),(14,6),(28,7);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Tenant Admin','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(2,'Editor','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(3,'Author','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(4,'Contributor','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(5,'SEO Specialist','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(6,'Moderator','web','2025-11-06 05:01:09','2025-11-06 05:01:09'),(7,'Analyst','web','2025-11-06 05:01:09','2025-11-06 05:01:09');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `segment_subscriber`
--

DROP TABLE IF EXISTS `segment_subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `segment_subscriber` (
  `segment_id` char(36) NOT NULL,
  `subscriber_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`segment_id`,`subscriber_id`),
  KEY `segment_subscriber_subscriber_id_foreign` (`subscriber_id`),
  CONSTRAINT `segment_subscriber_segment_id_foreign` FOREIGN KEY (`segment_id`) REFERENCES `subscriber_segments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `segment_subscriber_subscriber_id_foreign` FOREIGN KEY (`subscriber_id`) REFERENCES `newsletter_subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `segment_subscriber`
--

LOCK TABLES `segment_subscriber` WRITE;
/*!40000 ALTER TABLE `segment_subscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `segment_subscriber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_landings`
--

DROP TABLE IF EXISTS `seo_landings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_landings` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `meta_description` text,
  `meta_title` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `content` text,
  `schema_markup` json DEFAULT NULL,
  `target_keyword` varchar(255) DEFAULT NULL,
  `target_filters` json DEFAULT NULL,
  `content_type` enum('programs','jobs','mixed') NOT NULL DEFAULT 'mixed',
  `items_per_page` int NOT NULL DEFAULT '20',
  `views` int unsigned NOT NULL DEFAULT '0',
  `conversion_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `index_status` enum('index','noindex') NOT NULL DEFAULT 'index',
  `follow_status` enum('follow','nofollow') NOT NULL DEFAULT 'follow',
  `created_by` char(36) DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seo_landings_tenant_id_slug_unique` (`tenant_id`,`slug`),
  KEY `seo_landings_created_by_foreign` (`created_by`),
  KEY `seo_landings_updated_by_foreign` (`updated_by`),
  CONSTRAINT `seo_landings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seo_landings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seo_landings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_landings`
--

LOCK TABLES `seo_landings` WRITE;
/*!40000 ALTER TABLE `seo_landings` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_landings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('bzKFd7Is6h2m7q7vRcnT0r4o39SyRYSltF2iv8WD','2043841a-a6c6-4e72-8f1a-6137d03008a3','192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM292NVo4c0huNDFreFEwVk1jaHg1blhKbThUQ212bXhvcUpyd1FJMCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3QvcGxhdGZvcm0vdGVuYW50cyI7fXM6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MzY6IjIwNDM4NDFhLWE2YzYtNGU3Mi04ZjFhLTYxMzdkMDMwMDhhMyI7czoxOToicGFzc3dvcmRfaGFzaF9hZG1pbiI7czo2MDoiJDJ5JDEyJG9HeFNud3IxUFhETVFZZkJoWE1HZy5WVXBJNzdCNFZBdi5GZWRaUi5rMDVWSUVieEgwYTNPIjt9',1762869674);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriber_segments`
--

DROP TABLE IF EXISTS `subscriber_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriber_segments` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `criteria` json DEFAULT NULL,
  `type` enum('static','dynamic') NOT NULL DEFAULT 'static',
  `subscriber_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriber_segments_tenant_id_type_index` (`tenant_id`,`type`),
  CONSTRAINT `subscriber_segments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriber_segments`
--

LOCK TABLES `subscriber_segments` WRITE;
/*!40000 ALTER TABLE `subscriber_segments` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscriber_segments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `description` text,
  `primary_color` varchar(7) NOT NULL DEFAULT '#3b82f6',
  `secondary_color` varchar(7) NOT NULL DEFAULT '#64748b',
  `accent_color` varchar(7) NOT NULL DEFAULT '#10b981',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `og_image_url` varchar(255) DEFAULT NULL,
  `favicon_url` varchar(255) DEFAULT NULL,
  `google_analytics_id` varchar(255) DEFAULT NULL,
  `google_adsense_id` varchar(255) DEFAULT NULL,
  `google_tag_manager_id` varchar(255) DEFAULT NULL,
  `email_from_name` varchar(255) DEFAULT NULL,
  `email_from_address` varchar(255) DEFAULT NULL,
  `gdpr_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `ccpa_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `privacy_policy_url` text,
  `terms_of_service_url` text,
  `enabled_features` json DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `status` enum('active','suspended','pending') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_domain_unique` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES ('6d747b76-0658-4e8b-a412-b68273685097','Tech Jobs Portal','techjobs.com',NULL,'Find the best tech jobs in Southeast Asia','#10B981','#059669','#8B5CF6','Tech Jobs Portal - Best Tech Careers','Discover amazing tech job opportunities',NULL,NULL,NULL,NULL,NULL,'Tech Jobs','jobs@techjobs.com',0,0,NULL,NULL,'[\"jobs\", \"news\", \"ads\"]',NULL,'active','2025-11-06 05:01:09','2025-11-06 05:01:09'),('a04a5cb0-b2b3-467f-95af-8179a84ced85','test tenant',NULL,NULL,'test','#3b82f6','#64748b','#10b981',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,'[\"programs\", \"jobs\", \"news\"]',NULL,'active','2025-11-06 07:00:31','2025-11-06 07:00:31'),('aa74790d-0eef-4bd4-9947-427264735577','Beasiswa Indonesia','beasiswa.id',NULL,'Platform beasiswa terlengkap di Indonesia','#3B82F6','#1E40AF','#F59E0B','Beasiswa Indonesia - Info Beasiswa Terlengkap','Temukan berbagai informasi beasiswa dalam dan luar negeri',NULL,NULL,NULL,NULL,NULL,'Beasiswa Indonesia','info@beasiswa.id',0,0,NULL,NULL,'[\"programs\", \"news\", \"seo\", \"newsletter\"]',NULL,'active','2025-11-06 05:01:09','2025-11-06 05:01:09'),('c4bb9895-964a-4376-835b-9697fe592b8f','Youth Breaking Barriers','ybb.id',NULL,'Empowering youth through opportunities','#EF4444','#DC2626','#F59E0B','YBB - Youth Breaking Barriers','Programs and opportunities for Indonesian youth',NULL,NULL,NULL,NULL,NULL,'YBB Foundation','info@ybb.id',0,0,NULL,NULL,'[\"programs\", \"jobs\", \"news\", \"seo\", \"newsletter\"]',NULL,'active','2025-11-06 05:01:09','2025-11-06 05:01:09');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `term_post`
--

DROP TABLE IF EXISTS `term_post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `term_post` (
  `id` char(36) NOT NULL,
  `term_id` char(36) NOT NULL,
  `post_id` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term_post_term_id_post_id_unique` (`term_id`,`post_id`),
  KEY `term_post_post_id_foreign` (`post_id`),
  CONSTRAINT `term_post_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `term_post_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `term_post`
--

LOCK TABLES `term_post` WRITE;
/*!40000 ALTER TABLE `term_post` DISABLE KEYS */;
INSERT INTO `term_post` VALUES ('a04a37c1-b90c-4379-99dd-db24c7bc600a','a04a37c1-b0ba-4399-9ec1-a857f84cae48','a04a37c1-b71e-409c-a07c-fe0496321ef3',NULL,NULL),('a04a37c1-bca1-4c62-a40b-d341433d6f25','a04a37c1-b0ba-4399-9ec1-a857f84cae48','a04a37c1-bc46-40bd-ada3-8fc37b0dc1f1',NULL,NULL),('a04a37c1-bde1-455b-85ad-16e253134925','a04a37c1-b0ba-4399-9ec1-a857f84cae48','a04a37c1-bd4a-4923-98b8-1cb8cebbfb9f',NULL,NULL),('a04a37c1-bf21-44d0-ab78-c5a9db685d40','a04a37c1-b52f-4b2a-aada-c52fa70ccd87','a04a37c1-be96-4e48-aa46-63c6c5033a98',NULL,NULL),('a04a37c1-c03c-4b5f-96ed-3cf7d5203408','a04a37c1-b52f-4b2a-aada-c52fa70ccd87','a04a37c1-bfcc-42f9-8210-5e6642c452a6',NULL,NULL),('a04a37c1-c182-49e3-aee5-45649ee12c6c','a04a37c1-b62b-47bd-9308-37b3afc799d7','a04a37c1-c0de-44f5-87a2-44cbd1e03319',NULL,NULL),('a04a37c1-c247-4391-9dd2-1ff2fa53f429','a04a37c1-b62b-47bd-9308-37b3afc799d7','a04a37c1-c1f0-4155-9328-e173d33c3e1c',NULL,NULL);
/*!40000 ALTER TABLE `term_post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terms` (
  `id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `type` enum('category','tag','location','skill','industry') NOT NULL,
  `parent_id` char(36) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `post_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `terms_tenant_id_slug_type_unique` (`tenant_id`,`slug`,`type`),
  KEY `terms_parent_id_foreign` (`parent_id`),
  CONSTRAINT `terms_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `terms_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
INSERT INTO `terms` VALUES ('a04a37c1-b0ba-4399-9ec1-a857f84cae48','aa74790d-0eef-4bd4-9947-427264735577','Beasiswa S1','beasiswa-s1',NULL,'category',NULL,'#3B82F6',NULL,NULL,0,0,'2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-b52f-4b2a-aada-c52fa70ccd87','6d747b76-0658-4e8b-a412-b68273685097','Software Engineering','software-engineering',NULL,'category',NULL,'#10B981',NULL,NULL,0,0,'2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04a37c1-b62b-47bd-9308-37b3afc799d7','c4bb9895-964a-4376-835b-9697fe592b8f','Youth Programs','youth-programs',NULL,'category',NULL,'#F59E0B',NULL,NULL,0,0,'2025-11-06 05:17:14','2025-11-06 05:17:14'),('a04abd7c-d8b3-43a2-8fdb-c6fdee3b1414','a04a5cb0-b2b3-467f-95af-8179a84ced85','cat 1','cat-1','dfddf','category',NULL,'#e34b4b',NULL,NULL,1,0,'2025-11-06 11:31:10','2025-11-06 11:31:10'),('a04ac3b0-367c-49ce-92bd-1ab01a616b08','a04a5cb0-b2b3-467f-95af-8179a84ced85','indonesia','indonesia','fgr','location',NULL,'#7ae84e',NULL,NULL,0,0,'2025-11-06 11:48:31','2025-11-06 11:48:31');
/*!40000 ALTER TABLE `terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tenants`
--

DROP TABLE IF EXISTS `user_tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tenants` (
  `user_id` char(36) NOT NULL,
  `tenant_id` char(36) NOT NULL,
  `role` enum('tenant_admin','editor','author','contributor') NOT NULL DEFAULT 'author',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`tenant_id`),
  UNIQUE KEY `user_tenants_user_id_tenant_id_unique` (`user_id`,`tenant_id`),
  KEY `user_tenants_user_id_index` (`user_id`),
  KEY `user_tenants_tenant_id_index` (`tenant_id`),
  KEY `user_tenants_role_index` (`role`),
  CONSTRAINT `user_tenants_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_tenants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tenants`
--

LOCK TABLES `user_tenants` WRITE;
/*!40000 ALTER TABLE `user_tenants` DISABLE KEYS */;
INSERT INTO `user_tenants` VALUES ('37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6','6d747b76-0658-4e8b-a412-b68273685097','tenant_admin',1,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','aa74790d-0eef-4bd4-9947-427264735577','editor',0,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','c4bb9895-964a-4376-835b-9697fe592b8f','tenant_admin',1,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('3f0f0f6b-a011-4aec-9655-1184e25ee82a','6d747b76-0658-4e8b-a412-b68273685097','author',1,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('6ee118ba-3396-4013-b988-b1820f5c48ae','aa74790d-0eef-4bd4-9947-427264735577','tenant_admin',1,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('776e3c19-8e01-4712-8cbb-bfa9a887cd35','aa74790d-0eef-4bd4-9947-427264735577','editor',1,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('a04a5cb0-fc55-4154-9a8c-77d02fecc57a','a04a5cb0-b2b3-467f-95af-8179a84ced85','tenant_admin',1,'2025-11-06 07:00:31','2025-11-06 07:00:31'),('a04abbda-c525-4621-91e5-a02647838d09','a04a5cb0-b2b3-467f-95af-8179a84ced85','editor',1,'2025-11-06 11:26:37','2025-11-06 11:26:37');
/*!40000 ALTER TABLE `user_tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `bio` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('37ea34a8-bcd9-45b9-9dfe-ceb83e924ad6','Sarah Chen','sarah@techjobs.com',NULL,'$2y$12$y0/BjXJvyoByLHuYo9.R9eLyF.ZAWWdVf5vj8Si1v8Rr8U6HnGM/m',NULL,'Tenant Admin for Tech Jobs Portal',1,NULL,NULL,NULL,NULL,'2025-11-06 05:01:09','2025-11-06 05:01:09'),('3e6d4b3a-b81c-4395-ac46-80b613ca0ce1','Ahmad Rahman','ahmad@ybb.id',NULL,'$2y$12$rsKAS.2OKk/0d5k3bHcMze4ob50pmt4qda3dsCTSn7bo/B7MdOpDa',NULL,'Tenant Admin for YBB - manages multiple tenants',1,NULL,NULL,NULL,'NhV8a2eGBvMVpn3bVlR4aU6ANRCXklsqJNlDQY9LAzVyrECtJj6LUQbvOQxP','2025-11-06 05:01:09','2025-11-06 05:01:09'),('3f0f0f6b-a011-4aec-9655-1184e25ee82a','John Author','john@techjobs.com',NULL,'$2y$12$RFUsIgkdQKUmySSlphej9OciugOIFBoGXjWBKBe6PFMEDHaeX75YW',NULL,'Content Author for Tech Jobs',1,NULL,NULL,NULL,NULL,'2025-11-06 05:01:10','2025-11-06 05:01:10'),('6ee118ba-3396-4013-b988-b1820f5c48ae','Budi Santoso','budi@beasiswa.id',NULL,'$2y$12$Tif1cQWuMvrcQuQUZDXC6eQUxM2yhNLgmgpyTQnQiJLVW0PoDABsu',NULL,'Tenant Admin for Beasiswa Indonesia',1,NULL,NULL,NULL,NULL,'2025-11-06 05:01:09','2025-11-06 05:01:09'),('776e3c19-8e01-4712-8cbb-bfa9a887cd35','Lisa Editor','lisa@beasiswa.id',NULL,'$2y$12$A7qzRm4a33WsxlxBxztDn.tQppqmBffBR0T/sRrWHzRg7VT1frPmm',NULL,'Content Editor for Beasiswa Indonesia',1,NULL,NULL,NULL,NULL,'2025-11-06 05:01:09','2025-11-06 05:01:09'),('a04a5cb0-fc55-4154-9a8c-77d02fecc57a','testtenant','testtenant@gmail.com',NULL,'$2y$12$tN4wr2eEMqHAF3FYKmwO9uTp/aVotRxyvDcHfrh9sUu1EHrKb2SLC',NULL,NULL,1,NULL,NULL,NULL,'GEGpTkTiAbTwVRDujVKqgE429LloDF2wZidwl9MP3qgpNHVMk1GE5xkeM33e','2025-11-06 07:00:31','2025-11-06 07:00:31'),('a04abbda-c525-4621-91e5-a02647838d09','editor','testeditor@gmail.com',NULL,'$2y$12$Org5ky7DHF4HLnJjSQbJnumkVT/0G6Jn1udL8yHWVppyPZkuV65/K',NULL,'ttt',1,NULL,NULL,NULL,'hNZEIBjuLqXQZCySVwqb2SqIdLrLt4CLvjYZ9o33VJvTSqD7B656BaHmfVF2','2025-11-06 11:26:37','2025-11-06 11:26:37');
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

-- Dump completed on 2025-11-11 14:03:14
