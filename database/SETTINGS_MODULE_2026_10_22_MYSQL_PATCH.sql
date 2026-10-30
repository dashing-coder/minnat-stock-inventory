-- M/S Afrin Traders ERP - Settings Module completion patch
-- MySQL 5.7+
-- Adds Day Open/Close, Market Hierarchy, Client-to-Market assignment and fine-grained Settings permissions.

START TRANSACTION;

CREATE TABLE `business_days` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_date` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'open',
  `opening_notes` TEXT NULL,
  `closing_notes` TEXT NULL,
  `opened_by` BIGINT UNSIGNED NULL,
  `opened_at` TIMESTAMP NULL DEFAULT NULL,
  `closed_by` BIGINT UNSIGNED NULL,
  `closed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_days_business_date_unique` (`business_date`),
  KEY `business_days_status_business_date_index` (`status`,`business_date`),
  KEY `business_days_opened_by_foreign` (`opened_by`),
  KEY `business_days_closed_by_foreign` (`closed_by`),
  CONSTRAINT `business_days_opened_by_foreign` FOREIGN KEY (`opened_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `business_days_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `market_hierarchies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(180) NOT NULL,
  `code` VARCHAR(60) NULL,
  `level_type` VARCHAR(30) NOT NULL DEFAULT 'market',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `market_hierarchies_code_unique` (`code`),
  KEY `market_hierarchies_parent_id_sort_order_index` (`parent_id`,`sort_order`),
  KEY `market_hierarchies_level_type_is_active_index` (`level_type`,`is_active`),
  CONSTRAINT `market_hierarchies_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `market_hierarchies` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `customers`
  ADD COLUMN `market_hierarchy_id` BIGINT UNSIGNED NULL AFTER `customer_group_id`,
  ADD KEY `customers_market_hierarchy_id_foreign` (`market_hierarchy_id`),
  ADD CONSTRAINT `customers_market_hierarchy_id_foreign` FOREIGN KEY (`market_hierarchy_id`) REFERENCES `market_hierarchies` (`id`) ON DELETE SET NULL;

-- Keep production usable immediately after deployment. Admin may close/open from Settings -> Day Open Close.
INSERT INTO `business_days` (`business_date`,`status`,`opened_at`,`created_at`,`updated_at`)
VALUES (CURDATE(),'open',NOW(),NOW(),NOW());

INSERT IGNORE INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES
('client_setup.view','web',NOW(),NOW()),
('reorder_setup.view','web',NOW(),NOW()),
('reorder_setup.edit','web',NOW(),NOW()),
('business_days.view','web',NOW(),NOW()),
('business_days.manage','web',NOW(),NOW()),
('user_modules.view','web',NOW(),NOW()),
('user_modules.edit','web',NOW(),NOW()),
('market_hierarchy.view','web',NOW(),NOW()),
('market_hierarchy.create','web',NOW(),NOW()),
('market_hierarchy.edit','web',NOW(),NOW()),
('market_hierarchy.delete','web',NOW(),NOW()),
('barcode_print.view','web',NOW(),NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`)
SELECT p.id, r.id
FROM `permissions` p
CROSS JOIN `roles` r
WHERE p.name IN (
  'client_setup.view','reorder_setup.view','reorder_setup.edit',
  'business_days.view','business_days.manage','user_modules.view','user_modules.edit',
  'market_hierarchy.view','market_hierarchy.create','market_hierarchy.edit','market_hierarchy.delete',
  'barcode_print.view'
) AND r.name IN ('Super Admin','Admin') AND p.guard_name='web' AND r.guard_name='web';

COMMIT;
