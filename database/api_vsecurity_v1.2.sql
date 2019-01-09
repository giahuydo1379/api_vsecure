-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.34-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for api_vsecurity
CREATE DATABASE IF NOT EXISTS `api_vsecurity` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `api_vsecurity`;

-- Dumping structure for table api_vsecurity.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `nick_name` varchar(50) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `token_notification` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_deleted` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table api_vsecurity.customers: ~2 rows (approximately)
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` (`id`, `email`, `nick_name`, `password`, `token_notification`, `created_at`, `updated_at`, `is_deleted`) VALUES
	(6, 'huy1@gmail.com', 'huy', '$2y$10$Syv6pt/yCJJnHgXoEr7yQeJ7MfjWxX8ArQMYIpZLVTq1dTicKXFYC', NULL, '2019-01-04 09:35:44', '2019-01-04 09:35:44', 0),
	(7, 'huy2@gmail.com', 'huy', '$2y$10$7XlHQ4q3JX34za8TwJR5M.itquJxGvyPJ7Njoip0X6O7QUH0Z/xiu', 'huy', '2019-01-08 10:03:14', '2019-01-08 10:03:14', 0);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.device_token
CREATE TABLE IF NOT EXISTS `device_token` (
  `id` int(11) NOT NULL,
  `dooralarm _id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `device_token` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table api_vsecurity.device_token: ~0 rows (approximately)
/*!40000 ALTER TABLE `device_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `device_token` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.dooralarm
CREATE TABLE IF NOT EXISTS `dooralarm` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `location` varchar(250) DEFAULT NULL,
  `mac` varchar(250) DEFAULT NULL,
  `version` float DEFAULT NULL,
  `volume` int(11) DEFAULT '3' COMMENT 'Level 1 - 3',
  `arm_delay` int(11) DEFAULT '5' COMMENT '[ 5 - 255 ]',
  `alarm_delay` int(11) DEFAULT '5' COMMENT '[ 5 - 255 ]',
  `alarm_duration` int(11) DEFAULT '60' COMMENT '[ 5 - 300 ]',
  `self_test_mode` tinyint(2) DEFAULT '0' COMMENT '0: normal, 1: power saving, 2: fast',
  `timing_arm_disarm` int(11) DEFAULT NULL,
  `is_arm` tinyint(2) DEFAULT NULL COMMENT '0: disarm, 1: arm',
  `is_home` tinyint(2) DEFAULT NULL COMMENT '0: home, 1: away',
  `is_alarm` tinyint(2) DEFAULT NULL COMMENT '0: alarm, 1: doorbell',
  `door_status` tinyint(2) DEFAULT NULL COMMENT '0: close, 1: open',
  `battery_capacity_reamaining` tinyint(5) DEFAULT NULL COMMENT '0: <=25%, 1:<=50%, 2:<=75%, 3: <=100%',
  `is_deleted` tinyint(5) DEFAULT NULL COMMENT '0: còn, 1:xóa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Thiết bị trống trộm';

-- Dumping data for table api_vsecurity.dooralarm: ~0 rows (approximately)
/*!40000 ALTER TABLE `dooralarm` DISABLE KEYS */;
/*!40000 ALTER TABLE `dooralarm` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.dooralarm _customer
CREATE TABLE IF NOT EXISTS `dooralarm _customer` (
  `id` int(11) NOT NULL,
  `id_dooralarm` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `is_owner` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table api_vsecurity.dooralarm _customer: ~0 rows (approximately)
/*!40000 ALTER TABLE `dooralarm _customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `dooralarm _customer` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.migrations: ~0 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.password_resets: ~0 rows (approximately)
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.permissions: ~4 rows (approximately)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'role-read', 'Display Role Listing', 'Only Can See List Of Role', NULL, NULL),
	(2, 'role-create', 'Create Role', 'Create new role', NULL, NULL),
	(3, 'role-edit', 'Edit Role', 'Edit Role', NULL, NULL),
	(4, 'role-delete', 'Delete Role', 'Delete Role', NULL, NULL);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.permission_role
CREATE TABLE IF NOT EXISTS `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.permission_role: ~5 rows (approximately)
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
	(1, 1),
	(2, 1),
	(3, 1),
	(4, 1),
	(1, 3);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.roles: ~3 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'Admin', 'Almost do everything', '2018-12-26 09:57:37', '2018-12-26 09:57:37'),
	(2, 'user', 'User', 'Only can do the thing that admn allow to do', '2018-12-26 09:57:49', '2018-12-26 09:57:49'),
	(3, 'manager', 'Manager', 'Only can read list of role', '2018-12-27 01:40:39', '2018-12-27 01:40:39');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.role_user
CREATE TABLE IF NOT EXISTS `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.role_user: ~3 rows (approximately)
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
	(1, 1),
	(2, 2),
	(3, 3);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.setting_notification_dooralarm
CREATE TABLE IF NOT EXISTS `setting_notification_dooralarm` (
  `id` int(11) NOT NULL,
  `id_dooralarm` int(11) NOT NULL,
  `arm_opening_push_switch` tinyint(2) DEFAULT NULL,
  `disarm_opening_push_switch` tinyint(2) DEFAULT NULL,
  `close_push_switch` tinyint(2) DEFAULT NULL,
  `password_change_push_switch` tinyint(2) DEFAULT NULL,
  `arm_push_switch` tinyint(2) DEFAULT NULL,
  `disarm_push_switch` tinyint(2) DEFAULT NULL,
  `mode_change_push_switch` tinyint(2) DEFAULT NULL,
  `boot_push_switch` tinyint(2) DEFAULT NULL,
  `low_power_push_switch` tinyint(2) DEFAULT NULL,
  `offline_push_switch` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table api_vsecurity.setting_notification_dooralarm: ~0 rows (approximately)
/*!40000 ALTER TABLE `setting_notification_dooralarm` DISABLE KEYS */;
/*!40000 ALTER TABLE `setting_notification_dooralarm` ENABLE KEYS */;

-- Dumping structure for table api_vsecurity.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table api_vsecurity.users: ~3 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'admin@gmail.com', '$2y$10$EjZImFxfhokoadOrqkzeJOQkAz6Q4OuaE36T9/Z9zWz3eAddrXx1u', 'DVfEKTns6DBIAUlYZOmNSN9fmrdZJhlSqXVablb8HG5Afj5S114KqIA3BTje', '2018-12-26 10:09:10', '2018-12-26 10:09:10'),
	(2, 'user', 'user1@gmail.com', '$2y$10$ZEMoAUI4aVsFUeLNnx2YJe1JSeH08FndtIQp1YAnrX4cllwGtcRba', '0Xkcb3StsHGK8hsvgBF5xvIhomXRVRVpUdxTeYUk6IzhYO5xouZGgU7d6pEL', '2018-12-26 10:09:24', '2018-12-26 10:09:24'),
	(3, 'user2', 'user2@gmail.com', '$2y$10$jKp3rN27NwS9RIfQedl0Jux6z4kncdL8ze/49vcVVMUuiDWohVVe.', 'EhxkGjMI3WP8oQSnAZMldozD1CWTU0uEKPH1awTzbnUBuFQt3zTF5lifKTX2', '2018-12-27 01:41:39', '2018-12-27 01:41:39');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
