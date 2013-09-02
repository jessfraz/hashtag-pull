# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.13)
# Database: hashtag_pull
# Generation Time: 2013-09-02 01:01:52 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `media`;

CREATE TABLE `media` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `time_now` bigint(20) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `source_id` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `name` text,
  `screen_name` varchar(255) DEFAULT NULL,
  `user_location` varchar(255) DEFAULT NULL,
  `text` text,
  `media_url` varchar(255) DEFAULT NULL,
  `media_url_https` text,
  `source` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT '0',
  `hashtag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_id` (`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
