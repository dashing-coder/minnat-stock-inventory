-- Laravel ERP System - Steps 1-14 (Full ERP)
-- MySQL 8+ / MariaDB compatible starter dump
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
CREATE DATABASE IF NOT EXISTS `erp_system` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `erp_system`;

DROP TABLE IF EXISTS `system_notifications`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `payroll_payments`;
DROP TABLE IF EXISTS `payroll_items`;
DROP TABLE IF EXISTS `payroll_runs`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `expenses`;
DROP TABLE IF EXISTS `expense_categories`;
DROP TABLE IF EXISTS `supplier_payments`;
DROP TABLE IF EXISTS `customer_payments`;
DROP TABLE IF EXISTS `quotation_items`;
DROP TABLE IF EXISTS `quotations`;
DROP TABLE IF EXISTS `sale_return_items`;
DROP TABLE IF EXISTS `sale_returns`;
DROP TABLE IF EXISTS `sale_items`;
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `purchase_return_items`;
DROP TABLE IF EXISTS `purchase_returns`;
DROP TABLE IF EXISTS `purchase_items`;
DROP TABLE IF EXISTS `purchases`;
DROP TABLE IF EXISTS `journal_lines`;
DROP TABLE IF EXISTS `journal_entries`;
DROP TABLE IF EXISTS `accounts`;

DROP TABLE IF EXISTS `stock_transfer_items`;
DROP TABLE IF EXISTS `stock_transfers`;
DROP TABLE IF EXISTS `stock_adjustment_items`;
DROP TABLE IF EXISTS `stock_adjustments`;
DROP TABLE IF EXISTS `stock_movements`;
DROP TABLE IF EXISTS `warehouse_stocks`;
DROP TABLE IF EXISTS `warehouses`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `units`;
DROP TABLE IF EXISTS `supplier_ledger_entries`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `supplier_groups`;
DROP TABLE IF EXISTS `customer_ledger_entries`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `customer_groups`;
DROP TABLE IF EXISTS `role_has_permissions`;
DROP TABLE IF EXISTS `model_has_roles`;
DROP TABLE IF EXISTS `model_has_permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `mhp_permission_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `mhr_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  CONSTRAINT `rhp_permission_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rhp_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customer_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL, `code` varchar(50) DEFAULT NULL, `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`name`), UNIQUE KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(50) DEFAULT NULL, `customer_group_id` bigint unsigned DEFAULT NULL,
  `name` varchar(200) NOT NULL, `company_name` varchar(200) DEFAULT NULL, `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL, `alternate_phone` varchar(30) DEFAULT NULL, `email` varchar(200) DEFAULT NULL,
  `address` text, `city` varchar(100) DEFAULT NULL, `district` varchar(100) DEFAULT NULL, `country` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT 0, `opening_balance_type` varchar(20) NOT NULL DEFAULT 'receivable',
  `credit_limit` decimal(18,2) DEFAULT NULL, `payment_terms_days` int unsigned NOT NULL DEFAULT 0, `tax_number` varchar(100) DEFAULT NULL,
  `notes` text, `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`code`), KEY (`customer_group_id`), KEY (`name`), KEY (`phone`),
  CONSTRAINT `customers_group_fk` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `customer_ledger_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `customer_id` bigint unsigned NOT NULL, `transaction_date` date NOT NULL,
  `entry_type` varchar(50) NOT NULL, `reference_no` varchar(100) DEFAULT NULL, `source_type` varchar(100) DEFAULT NULL,
  `source_id` bigint unsigned DEFAULT NULL, `debit` decimal(18,2) NOT NULL DEFAULT 0, `credit` decimal(18,2) NOT NULL DEFAULT 0,
  `description` text, `created_by` bigint unsigned DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), KEY (`customer_id`,`transaction_date`), KEY (`source_type`,`source_id`),
  CONSTRAINT `cle_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cle_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `supplier_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL, `code` varchar(50) DEFAULT NULL, `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`name`), UNIQUE KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(50) DEFAULT NULL, `supplier_group_id` bigint unsigned DEFAULT NULL,
  `name` varchar(200) NOT NULL, `company_name` varchar(200) DEFAULT NULL, `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL, `alternate_phone` varchar(30) DEFAULT NULL, `email` varchar(200) DEFAULT NULL,
  `address` text, `city` varchar(100) DEFAULT NULL, `district` varchar(100) DEFAULT NULL, `country` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT 0, `opening_balance_type` varchar(20) NOT NULL DEFAULT 'payable',
  `payment_terms_days` int unsigned NOT NULL DEFAULT 0, `tax_number` varchar(100) DEFAULT NULL, `notes` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`code`), KEY (`supplier_group_id`), KEY (`name`), KEY (`phone`),
  CONSTRAINT `suppliers_group_fk` FOREIGN KEY (`supplier_group_id`) REFERENCES `supplier_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `supplier_ledger_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `supplier_id` bigint unsigned NOT NULL, `transaction_date` date NOT NULL,
  `entry_type` varchar(50) NOT NULL, `reference_no` varchar(100) DEFAULT NULL, `source_type` varchar(100) DEFAULT NULL,
  `source_id` bigint unsigned DEFAULT NULL, `debit` decimal(18,2) NOT NULL DEFAULT 0, `credit` decimal(18,2) NOT NULL DEFAULT 0,
  `description` text, `created_by` bigint unsigned DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), KEY (`supplier_id`,`transaction_date`), KEY (`source_type`,`source_id`),
  CONSTRAINT `sle_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sle_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `short_name` varchar(30) DEFAULT NULL,
  `allow_decimal` tinyint(1) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `parent_id` bigint unsigned DEFAULT NULL, `name` varchar(150) NOT NULL,
  `slug` varchar(180) NOT NULL, `code` varchar(50) DEFAULT NULL, `description` text, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`slug`), UNIQUE KEY (`code`),
  CONSTRAINT `categories_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `name` varchar(150) NOT NULL, `code` varchar(50) DEFAULT NULL,
  `description` text, `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`name`), UNIQUE KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(50) DEFAULT NULL, `sku` varchar(100) NOT NULL,
  `barcode` varchar(150) DEFAULT NULL, `name` varchar(200) NOT NULL, `category_id` bigint unsigned DEFAULT NULL,
  `brand_id` bigint unsigned DEFAULT NULL, `unit_id` bigint unsigned NOT NULL,
  `purchase_price` decimal(18,2) NOT NULL DEFAULT 0, `selling_price` decimal(18,2) NOT NULL DEFAULT 0,
  `wholesale_price` decimal(18,2) DEFAULT NULL, `minimum_selling_price` decimal(18,2) DEFAULT NULL,
  `tax_rate` decimal(8,4) NOT NULL DEFAULT 0, `reorder_level` decimal(18,4) NOT NULL DEFAULT 0,
  `minimum_stock_level` decimal(18,4) NOT NULL DEFAULT 0, `maximum_stock_level` decimal(18,4) DEFAULT NULL,
  `track_stock` tinyint(1) NOT NULL DEFAULT 1, `allow_negative_stock` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL, `description` text, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`code`), UNIQUE KEY (`sku`), UNIQUE KEY (`barcode`),
  CONSTRAINT `products_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_brand_fk` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_unit_fk` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(50) NOT NULL, `name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL, `email` varchar(150) DEFAULT NULL, `address` text,
  `is_default` tinyint(1) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `warehouse_stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `warehouse_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL DEFAULT 0, `reserved_quantity` decimal(18,4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  UNIQUE KEY `warehouse_product_unique` (`warehouse_id`,`product_id`),
  CONSTRAINT `ws_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ws_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `warehouse_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `transaction_date` date NOT NULL, `movement_type` varchar(50) NOT NULL, `reference_no` varchar(100) DEFAULT NULL,
  `source_type` varchar(50) DEFAULT NULL, `source_id` bigint unsigned DEFAULT NULL, `quantity_in` decimal(18,4) NOT NULL DEFAULT 0,
  `quantity_out` decimal(18,4) NOT NULL DEFAULT 0, `balance_after` decimal(18,4) NOT NULL, `unit_cost` decimal(18,4) DEFAULT NULL,
  `description` text, `created_by` bigint unsigned DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), KEY (`warehouse_id`,`product_id`,`transaction_date`), KEY (`source_type`,`source_id`), KEY (`reference_no`),
  CONSTRAINT `sm_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sm_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sm_user_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `adjustment_no` varchar(50) NOT NULL, `warehouse_id` bigint unsigned NOT NULL,
  `adjustment_date` date NOT NULL, `adjustment_type` varchar(30) NOT NULL, `status` varchar(30) NOT NULL DEFAULT 'draft',
  `reason` text, `notes` text, `created_by` bigint unsigned DEFAULT NULL, `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`adjustment_no`),
  CONSTRAINT `sa_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sa_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sa_posted_by_fk` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `stock_adjustment_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `stock_adjustment_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_cost` decimal(18,4) DEFAULT NULL, `remarks` text,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  UNIQUE KEY (`stock_adjustment_id`,`product_id`),
  CONSTRAINT `sai_header_fk` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sai_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `transfer_no` varchar(50) NOT NULL,
  `from_warehouse_id` bigint unsigned NOT NULL, `to_warehouse_id` bigint unsigned NOT NULL, `transfer_date` date NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft', `notes` text, `created_by` bigint unsigned DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL, `posted_at` timestamp NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`transfer_no`),
  CONSTRAINT `st_from_fk` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `st_to_fk` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `st_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `st_posted_by_fk` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `stock_transfer_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_cost` decimal(18,4) DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`stock_transfer_id`,`product_id`),
  CONSTRAINT `sti_header_fk` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sti_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data
INSERT INTO `users` (`id`,`name`,`username`,`email`,`password`,`status`,`created_at`,`updated_at`) VALUES (1,'Super Admin','admin','admin@example.com','$2y$12$u/Tz4r.g6m7oshOugCdVKexujG0JIdm5Mksus8lSN8AVMeubKISj.','active','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (1,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (2,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (3,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (4,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (5,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (6,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (7,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (8,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (9,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (10,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (11,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (12,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (16,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (18,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (19,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (20,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (21,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (22,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (26,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (28,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (29,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (30,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (31,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (32,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (33,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (34,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (35,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (36,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (37,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (38,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (39,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (40,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (42,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (43,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (44,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (45,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (46,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (48,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (49,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (50,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (53,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (54,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (55,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (56,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (60,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (61,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (62,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (63,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (67,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (68,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (69,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (70,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (72,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (73,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (74,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (75,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (76,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (77,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (78,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (79,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (80,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (81,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (82,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (83,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (84,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (85,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (86,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (87,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (91,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (92,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (93,1);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (1,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (2,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (3,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (4,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (5,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (6,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (7,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (8,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (9,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (10,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (11,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (12,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (16,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (18,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (19,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (20,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (21,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (22,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (26,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (28,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (29,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (30,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (31,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (32,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (33,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (34,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (35,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (36,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (37,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (38,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (39,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (40,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (42,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (43,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (44,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (45,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (46,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (48,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (49,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (50,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (53,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (54,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (55,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (56,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (60,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (61,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (62,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (63,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (67,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (68,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (69,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (70,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (72,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (73,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (74,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (75,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (76,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (77,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (78,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (79,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (80,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (81,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (82,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (83,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (84,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (85,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (86,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (87,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (91,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (92,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (93,2);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (61,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (68,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (75,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,3);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (72,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (73,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (82,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (83,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (84,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,4);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (69,5);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (62,6);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (53,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (54,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (55,7);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,8);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,8);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,8);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,8);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,8);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,8);
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,8);
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (1,'Piece','PCS',0,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (2,'Kilogram','KG',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (3,'Gram','GM',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (4,'Liter','LTR',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (5,'Meter','MTR',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (6,'Box','BOX',0,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (7,'Carton','CTN',0,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (8,'Pack','PACK',0,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `units` (`id`,`name`,`short_name`,`allow_decimal`,`is_active`,`created_at`,`updated_at`) VALUES (9,'Set','SET',0,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `warehouses` (`id`,`code`,`name`,`is_default`,`is_active`,`created_at`,`updated_at`) VALUES (1,'WH-001','Main Warehouse',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (4,'2026_08_28_000100_create_permission_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (5,'2026_08_28_000200_create_parties_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (6,'2026_08_28_000300_create_product_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (7,'2026_08_28_000400_create_inventory_tables',1);




-- Step 11: Accounting
CREATE TABLE `accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(30) NOT NULL,
  `subtype` varchar(50) DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `accounts_code_unique` (`code`), KEY `accounts_type_active_idx` (`type`,`is_active`),
  CONSTRAINT `accounts_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_no` varchar(50) NOT NULL,
  `journal_date` date NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `description` text,
  `status` varchar(20) NOT NULL DEFAULT 'posted',
  `created_by` bigint unsigned DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `journal_no_unique` (`journal_no`),
  UNIQUE KEY `journal_reference_unique` (`reference_type`,`reference_id`),
  KEY `journal_date_status_idx` (`journal_date`,`status`),
  CONSTRAINT `je_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `je_posted_by_fk` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `debit` decimal(18,2) NOT NULL DEFAULT 0,
  `credit` decimal(18,2) NOT NULL DEFAULT 0,
  `party_type` varchar(30) DEFAULT NULL,
  `party_id` bigint unsigned DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), KEY `journal_lines_account_idx` (`account_id`,`journal_entry_id`),
  CONSTRAINT `jl_entry_fk` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jl_account_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 6/7: Purchase, Sales, Returns, Quotation
CREATE TABLE `purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `purchase_no` varchar(50) NOT NULL,
  `supplier_id` bigint unsigned NOT NULL, `warehouse_id` bigint unsigned NOT NULL,
  `purchase_date` date NOT NULL, `due_date` date DEFAULT NULL, `supplier_invoice_no` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft', `subtotal` decimal(18,2) NOT NULL DEFAULT 0,
  `discount` decimal(18,2) NOT NULL DEFAULT 0, `tax` decimal(18,2) NOT NULL DEFAULT 0,
  `shipping` decimal(18,2) NOT NULL DEFAULT 0, `other_cost` decimal(18,2) NOT NULL DEFAULT 0,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0, `paid_amount` decimal(18,2) NOT NULL DEFAULT 0,
  `due_amount` decimal(18,2) NOT NULL DEFAULT 0, `notes` text, `created_by` bigint unsigned DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL, `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL, `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `purchases_no_unique` (`purchase_no`), KEY (`supplier_id`), KEY (`warehouse_id`),
  CONSTRAINT `purchases_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchases_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `purchase_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `purchase_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_cost` decimal(18,4) NOT NULL, `discount` decimal(18,2) NOT NULL DEFAULT 0,
  `tax` decimal(18,2) NOT NULL DEFAULT 0, `line_total` decimal(18,2) NOT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`purchase_id`,`product_id`),
  CONSTRAINT `pi_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pi_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `return_no` varchar(50) NOT NULL, `purchase_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned NOT NULL, `warehouse_id` bigint unsigned NOT NULL, `return_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft', `subtotal` decimal(18,2) NOT NULL DEFAULT 0, `tax` decimal(18,2) NOT NULL DEFAULT 0,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0, `reason` text, `created_by` bigint unsigned DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL, `posted_at` timestamp NULL DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`return_no`),
  CONSTRAINT `pr_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pr_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pr_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `purchase_return_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_cost` decimal(18,4) NOT NULL, `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  CONSTRAINT `pri_return_fk` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pri_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `invoice_no` varchar(50) NOT NULL, `customer_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL, `sale_date` date NOT NULL, `due_date` date DEFAULT NULL, `status` varchar(20) NOT NULL DEFAULT 'draft',
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0, `discount` decimal(18,2) NOT NULL DEFAULT 0, `tax` decimal(18,2) NOT NULL DEFAULT 0,
  `shipping` decimal(18,2) NOT NULL DEFAULT 0, `grand_total` decimal(18,2) NOT NULL DEFAULT 0,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0, `due_amount` decimal(18,2) NOT NULL DEFAULT 0, `notes` text,
  `created_by` bigint unsigned DEFAULT NULL, `posted_by` bigint unsigned DEFAULT NULL, `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`invoice_no`),
  CONSTRAINT `sales_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `sale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `sale_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_price` decimal(18,4) NOT NULL, `cost_price` decimal(18,4) NOT NULL DEFAULT 0,
  `discount` decimal(18,2) NOT NULL DEFAULT 0, `tax` decimal(18,2) NOT NULL DEFAULT 0, `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`sale_id`,`product_id`),
  CONSTRAINT `si_sale_fk` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `si_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `sale_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `return_no` varchar(50) NOT NULL, `sale_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL, `warehouse_id` bigint unsigned NOT NULL, `return_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft', `subtotal` decimal(18,2) NOT NULL DEFAULT 0, `tax` decimal(18,2) NOT NULL DEFAULT 0,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0, `reason` text, `created_by` bigint unsigned DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL, `posted_at` timestamp NULL DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`return_no`),
  CONSTRAINT `sr_sale_fk` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sr_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sr_warehouse_fk` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `sale_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `sale_return_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_price` decimal(18,4) NOT NULL, `cost_price` decimal(18,4) NOT NULL DEFAULT 0,
  `line_total` decimal(18,2) NOT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  CONSTRAINT `sri_return_fk` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sri_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `quotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `quotation_no` varchar(50) NOT NULL, `customer_id` bigint unsigned DEFAULT NULL,
  `quotation_date` date NOT NULL, `valid_until` date DEFAULT NULL, `status` varchar(20) NOT NULL DEFAULT 'draft',
  `subtotal` decimal(18,2) NOT NULL DEFAULT 0, `discount` decimal(18,2) NOT NULL DEFAULT 0, `tax` decimal(18,2) NOT NULL DEFAULT 0,
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0, `notes` text, `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`quotation_no`),
  CONSTRAINT `quotation_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `quotation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `quotation_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_price` decimal(18,4) NOT NULL, `discount` decimal(18,2) NOT NULL DEFAULT 0,
  `tax` decimal(18,2) NOT NULL DEFAULT 0, `line_total` decimal(18,2) NOT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), CONSTRAINT `qi_quotation_fk` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qi_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 8/9: Payments & Expenses
CREATE TABLE `customer_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `payment_no` varchar(50) NOT NULL, `customer_id` bigint unsigned NOT NULL,
  `sale_id` bigint unsigned DEFAULT NULL, `account_id` bigint unsigned NOT NULL, `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL, `method` varchar(30) NOT NULL DEFAULT 'cash', `reference` varchar(100) DEFAULT NULL,
  `notes` text, `status` varchar(20) NOT NULL DEFAULT 'posted', `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`payment_no`),
  CONSTRAINT `cp_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `cp_sale_fk` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cp_account_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `supplier_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `payment_no` varchar(50) NOT NULL, `supplier_id` bigint unsigned NOT NULL,
  `purchase_id` bigint unsigned DEFAULT NULL, `account_id` bigint unsigned NOT NULL, `payment_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL, `method` varchar(30) NOT NULL DEFAULT 'cash', `reference` varchar(100) DEFAULT NULL,
  `notes` text, `status` varchar(20) NOT NULL DEFAULT 'posted', `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`payment_no`),
  CONSTRAINT `sp_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sp_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sp_account_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `name` varchar(150) NOT NULL, `expense_account_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`name`), CONSTRAINT `ec_account_fk` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `expense_no` varchar(50) NOT NULL, `expense_date` date NOT NULL,
  `expense_category_id` bigint unsigned NOT NULL, `expense_account_id` bigint unsigned NOT NULL, `payment_account_id` bigint unsigned NOT NULL,
  `amount` decimal(18,2) NOT NULL, `status` varchar(20) NOT NULL DEFAULT 'draft', `reference` varchar(100) DEFAULT NULL,
  `description` text, `attachment` varchar(255) DEFAULT NULL, `created_by` bigint unsigned DEFAULT NULL, `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`expense_no`),
  CONSTRAINT `e_category_fk` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `e_expense_account_fk` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `e_payment_account_fk` FOREIGN KEY (`payment_account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 10: Payroll
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `employee_no` varchar(50) NOT NULL, `name` varchar(180) NOT NULL,
  `department` varchar(120) DEFAULT NULL, `designation` varchar(120) DEFAULT NULL, `joining_date` date DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL, `email` varchar(150) DEFAULT NULL, `address` text,
  `basic_salary` decimal(18,2) NOT NULL DEFAULT 0, `allowance` decimal(18,2) NOT NULL DEFAULT 0,
  `standard_deduction` decimal(18,2) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`employee_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `payroll_runs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `payroll_no` varchar(50) NOT NULL, `period` varchar(7) NOT NULL, `pay_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft', `gross_total` decimal(18,2) NOT NULL DEFAULT 0,
  `deduction_total` decimal(18,2) NOT NULL DEFAULT 0, `net_total` decimal(18,2) NOT NULL DEFAULT 0, `notes` text,
  `created_by` bigint unsigned DEFAULT NULL, `posted_by` bigint unsigned DEFAULT NULL, `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`payroll_no`), UNIQUE KEY (`period`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `payroll_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `payroll_run_id` bigint unsigned NOT NULL, `employee_id` bigint unsigned NOT NULL,
  `basic_salary` decimal(18,2) NOT NULL DEFAULT 0, `allowance` decimal(18,2) NOT NULL DEFAULT 0, `bonus` decimal(18,2) NOT NULL DEFAULT 0,
  `overtime` decimal(18,2) NOT NULL DEFAULT 0, `deduction` decimal(18,2) NOT NULL DEFAULT 0, `gross_salary` decimal(18,2) NOT NULL DEFAULT 0,
  `net_salary` decimal(18,2) NOT NULL DEFAULT 0, `paid_amount` decimal(18,2) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`payroll_run_id`,`employee_id`),
  CONSTRAINT `payi_run_fk` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payi_employee_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `payroll_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `payment_no` varchar(50) NOT NULL, `payroll_item_id` bigint unsigned NOT NULL,
  `payment_account_id` bigint unsigned NOT NULL, `payment_date` date NOT NULL, `amount` decimal(18,2) NOT NULL,
  `method` varchar(30) NOT NULL DEFAULT 'cash', `reference` varchar(100) DEFAULT NULL, `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY (`payment_no`),
  CONSTRAINT `payp_item_fk` FOREIGN KEY (`payroll_item_id`) REFERENCES `payroll_items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payp_account_fk` FOREIGN KEY (`payment_account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 14: Settings, Audit, Notifications
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `key` varchar(150) NOT NULL, `value` text, `group` varchar(80) NOT NULL DEFAULT 'general',
  `type` varchar(30) NOT NULL DEFAULT 'string', `is_public` tinyint(1) NOT NULL DEFAULT 0, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `user_id` bigint unsigned DEFAULT NULL, `action` varchar(30) NOT NULL,
  `route_name` varchar(150) DEFAULT NULL, `method` varchar(10) NOT NULL, `url` text NOT NULL, `payload` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL, `user_agent` text, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), KEY (`user_id`,`created_at`), CONSTRAINT `al_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `system_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `user_id` bigint unsigned DEFAULT NULL, `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(180) NOT NULL, `message` text NOT NULL, `link` varchar(255) DEFAULT NULL, `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), KEY (`user_id`,`read_at`),
  CONSTRAINT `sn_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Seed data for Step 6-14
INSERT IGNORE INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (9,'Payroll Officer','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (1,'Super Admin','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (2,'Admin','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (3,'Manager','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (4,'Accountant','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (5,'Sales Executive','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (6,'Purchase Officer','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (7,'Store Keeper','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (8,'Viewer','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (9,'Payroll Officer','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (1,'users.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (2,'users.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (3,'users.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (4,'users.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (5,'roles.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (6,'roles.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (7,'roles.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (8,'roles.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (9,'customer_groups.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (10,'customer_groups.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (11,'customer_groups.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (12,'customer_groups.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (13,'customers.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (14,'customers.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (15,'customers.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (16,'customers.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (17,'customers.ledger','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (18,'customers.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (19,'supplier_groups.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (20,'supplier_groups.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (21,'supplier_groups.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (22,'supplier_groups.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (23,'suppliers.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (24,'suppliers.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (25,'suppliers.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (26,'suppliers.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (27,'suppliers.ledger','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (28,'suppliers.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (29,'units.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (30,'units.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (31,'units.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (32,'units.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (33,'categories.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (34,'categories.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (35,'categories.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (36,'categories.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (37,'brands.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (38,'brands.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (39,'brands.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (40,'brands.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (41,'products.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (42,'products.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (43,'products.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (44,'products.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (45,'products.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (46,'products.import','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (47,'warehouses.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (48,'warehouses.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (49,'warehouses.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (50,'warehouses.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (51,'inventory.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (52,'inventory.ledger','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (53,'inventory.opening','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (54,'inventory.adjust','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (55,'inventory.transfer','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (56,'inventory.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (57,'purchases.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (58,'purchases.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (59,'purchases.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (60,'purchases.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (61,'purchases.approve','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (62,'purchases.print','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (63,'purchases.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (64,'sales.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (65,'sales.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (66,'sales.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (67,'sales.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (68,'sales.approve','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (69,'sales.print','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (70,'sales.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (71,'expenses.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (72,'expenses.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (73,'expenses.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (74,'expenses.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (75,'expenses.approve','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (76,'expenses.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (77,'payroll.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (78,'payroll.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (79,'payroll.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (80,'payroll.approve','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (81,'payroll.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (82,'accounts.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (83,'accounts.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (84,'accounts.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (85,'accounts.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (86,'accounts.approve','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (87,'accounts.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (88,'reports.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (89,'reports.print','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (90,'reports.export','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (91,'settings.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (92,'settings.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (93,'audit.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (154,'purchases.post','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (157,'purchase_returns.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (158,'purchase_returns.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (159,'purchase_returns.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (160,'purchase_returns.post','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (165,'sales.post','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (168,'sale_returns.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (169,'sale_returns.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (170,'sale_returns.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (171,'sale_returns.post','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (172,'quotations.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (173,'quotations.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (174,'quotations.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (175,'quotations.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (176,'quotations.print','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (177,'customer_payments.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (178,'customer_payments.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (179,'supplier_payments.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (180,'supplier_payments.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (181,'expense_categories.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (182,'expense_categories.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (183,'expense_categories.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (184,'expense_categories.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (189,'expenses.post','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (191,'employees.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (192,'employees.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (193,'employees.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (194,'employees.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (198,'payroll.delete','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (199,'payroll.post','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (200,'payroll.pay','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (206,'journals.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (207,'journals.create','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (214,'notifications.view','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (215,'purchase_returns.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (216,'sale_returns.edit','web','2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `model_has_roles` (`role_id`,`model_type`,`model_id`) VALUES (1,'App\\Models\\User',1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (1,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (1,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (2,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (2,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (3,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (3,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (4,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (4,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (5,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (5,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (6,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (6,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (7,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (7,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (8,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (8,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (9,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (9,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (10,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (10,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (11,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (11,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (12,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (12,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (13,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (14,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (15,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (16,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (16,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (17,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (18,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (18,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (19,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (19,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (20,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (20,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (21,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (21,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (22,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (22,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (23,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (24,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (25,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (26,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (26,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (27,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (28,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (28,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (29,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (29,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (30,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (30,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (31,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (31,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (32,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (32,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (33,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (33,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (34,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (34,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (35,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (35,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (36,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (36,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (37,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (37,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (38,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (38,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (39,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (39,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (40,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (40,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (41,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (42,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (42,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (43,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (43,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (44,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (44,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (45,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (45,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (46,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (46,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (47,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (48,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (48,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (49,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (49,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (50,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (50,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (51,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (52,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (53,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (53,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (53,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (54,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (54,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (54,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (55,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (55,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (55,7);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (56,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (56,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (57,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (58,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (59,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (60,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (60,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (61,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (61,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (61,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (62,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (62,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (62,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (63,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (63,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (64,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (65,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (66,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (67,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (67,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (68,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (68,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (68,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (69,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (69,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (69,5);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (70,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (70,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (71,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (72,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (72,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (72,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (73,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (73,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (73,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (74,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (74,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (75,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (75,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (75,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (76,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (76,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (77,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (77,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (77,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (78,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (78,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (78,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (79,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (79,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (79,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (80,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (80,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (81,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (81,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (81,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (82,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (82,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (82,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (83,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (83,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (83,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (84,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (84,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (84,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (85,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (85,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (86,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (86,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (87,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (87,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (88,8);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (89,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (90,4);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (91,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (91,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (92,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (92,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (93,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (93,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (154,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (154,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (157,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (157,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (158,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (158,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (159,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (159,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (160,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (160,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (165,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (165,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (168,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (168,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (169,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (169,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (170,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (170,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (171,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (171,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (172,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (172,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (173,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (173,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (174,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (174,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (175,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (175,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (176,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (176,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (177,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (177,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (178,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (178,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (179,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (179,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (180,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (180,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (181,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (181,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (182,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (182,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (183,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (183,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (184,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (184,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (189,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (189,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (191,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (191,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (191,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (192,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (192,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (192,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (193,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (193,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (193,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (194,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (194,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (194,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (198,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (198,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (198,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (199,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (199,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (199,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (200,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (200,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (200,9);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (206,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (206,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (207,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (207,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (214,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (214,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (215,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (215,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (215,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (215,6);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (216,1);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (216,2);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (216,3);
INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (216,5);
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (1,'1000','Cash on Hand','asset','cash',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (2,'1010','Bank Account','asset','bank',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (3,'1100','Accounts Receivable','asset','receivable',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (4,'1200','Inventory','asset','inventory',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (5,'2000','Accounts Payable','liability','payable',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (6,'2100','Payroll Payable','liability','payroll',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (7,'3000','Owner Equity','equity','capital',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (8,'3100','Opening Balance Equity','equity','opening',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (9,'4000','Sales Revenue','income','sales',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (10,'4010','Sales Returns & Allowances','income','sales_return',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (11,'4100','Other Income','income','other',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (12,'4200','Inventory Adjustment Gain','income','inventory_gain',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (13,'5000','Cost of Goods Sold','expense','cogs',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (14,'6000','Operating Expense','expense','operating',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (15,'6010','Salary Expense','expense','salary',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (16,'6020','Rent Expense','expense','rent',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (17,'6030','Utilities Expense','expense','utility',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (18,'6040','Inventory Adjustment Loss','expense','inventory_loss',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `accounts` (`id`,`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES (19,'6990','Other Expense','expense','other',1,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (1,'company_name','Laravel ERP','general','string',1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (2,'company_phone','','general','string',1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (3,'company_email','','general','string',1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (4,'company_address','','general','string',1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (5,'currency','USD','general','string',1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (6,'date_format','Y-m-d','general','string',1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (7,'timezone','UTC','general','string',0,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (8,'invoice_prefix','SAL','document','string',0,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (9,'purchase_prefix','PUR','document','string',0,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (10,'allow_negative_stock','0','inventory','boolean',0,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `settings` (`id`,`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES (11,'low_stock_notifications','1','inventory','boolean',0,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `expense_categories` (`id`,`name`,`expense_account_id`,`is_active`,`created_at`,`updated_at`) VALUES (1,'Salary',15,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `expense_categories` (`id`,`name`,`expense_account_id`,`is_active`,`created_at`,`updated_at`) VALUES (2,'Rent',16,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `expense_categories` (`id`,`name`,`expense_account_id`,`is_active`,`created_at`,`updated_at`) VALUES (3,'Utilities',17,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `expense_categories` (`id`,`name`,`expense_account_id`,`is_active`,`created_at`,`updated_at`) VALUES (4,'Inventory Loss',18,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `expense_categories` (`id`,`name`,`expense_account_id`,`is_active`,`created_at`,`updated_at`) VALUES (5,'Other',19,1,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `system_notifications` (`id`,`user_id`,`type`,`title`,`message`,`link`,`read_at`,`created_at`,`updated_at`) VALUES (1,1,'success','ERP ready','Step 1-14 ERP database is installed and ready.','/dashboard',NULL,'2026-08-28 00:00:00','2026-08-28 00:00:00');
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (8,'2026_08_28_000500_create_accounting_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (9,'2026_08_28_000600_create_commercial_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (10,'2026_08_28_000700_create_payments_expenses_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (11,'2026_08_28_000800_create_payroll_tables',1);
INSERT INTO `migrations` (`id`,`migration`,`batch`) VALUES (12,'2026_08_28_000900_create_system_tables',1);
SET FOREIGN_KEY_CHECKS=1;
