-- M/S Afrin Traders ERP — Phase 3 database patch
-- Target: MySQL 5.7+ / MariaDB
-- Apply ONLY when upgrading an existing Phase-2 database and Laravel migrations cannot be run.
-- Back up the database before importing this patch.

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

ALTER TABLE `products`
  ADD COLUMN `parent_product_id` BIGINT UNSIGNED NULL AFTER `id`,
  ADD COLUMN `variant_name` VARCHAR(180) NULL AFTER `name`,
  ADD COLUMN `variant_attributes` JSON NULL AFTER `variant_name`,
  ADD COLUMN `track_batch` TINYINT(1) NOT NULL DEFAULT 0 AFTER `track_stock`,
  ADD COLUMN `track_expiry` TINYINT(1) NOT NULL DEFAULT 0 AFTER `track_batch`,
  ADD COLUMN `costing_method` VARCHAR(20) NOT NULL DEFAULT 'average' AFTER `track_expiry`,
  ADD INDEX `products_parent_product_id_is_active_index` (`parent_product_id`,`is_active`),
  ADD CONSTRAINT `products_parent_product_id_foreign` FOREIGN KEY (`parent_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

ALTER TABLE `warehouse_stocks`
  ADD COLUMN `average_cost` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `reserved_quantity`;

-- Drop any unique two-column purchase/product index regardless of its historical name.
SET @purchase_unique_idx := (
  SELECT x.INDEX_NAME
  FROM (
    SELECT INDEX_NAME, NON_UNIQUE,
           GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX SEPARATOR ',') AS cols
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'purchase_items'
      AND INDEX_NAME <> 'PRIMARY'
    GROUP BY INDEX_NAME, NON_UNIQUE
  ) x
  WHERE x.NON_UNIQUE = 0 AND x.cols = 'purchase_id,product_id'
  LIMIT 1
);
SET @drop_purchase_unique_sql := IF(
  @purchase_unique_idx IS NULL,
  'SELECT 1',
  CONCAT('ALTER TABLE `purchase_items` DROP INDEX `', REPLACE(@purchase_unique_idx,'`','``'), '`')
);
PREPARE stmt FROM @drop_purchase_unique_sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @purchase_nonunique_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='purchase_items'
    AND INDEX_NAME='purchase_items_purchase_product_index'
);
SET @add_purchase_idx_sql := IF(
  @purchase_nonunique_exists > 0,
  'SELECT 1',
  'ALTER TABLE `purchase_items` ADD INDEX `purchase_items_purchase_product_index` (`purchase_id`,`product_id`)'
);
PREPARE stmt FROM @add_purchase_idx_sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

ALTER TABLE `purchase_items`
  ADD COLUMN `free_quantity` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `quantity`,
  ADD COLUMN `batch_no` VARCHAR(100) NULL AFTER `free_quantity`,
  ADD COLUMN `manufactured_at` DATE NULL AFTER `batch_no`,
  ADD COLUMN `expiry_date` DATE NULL AFTER `manufactured_at`;

ALTER TABLE `sale_items`
  ADD COLUMN `free_quantity` DECIMAL(18,4) NOT NULL DEFAULT 0 AFTER `quantity`;

CREATE TABLE `inventory_batches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_no` VARCHAR(100) NOT NULL,
  `manufactured_at` DATE NULL,
  `expires_at` DATE NULL,
  `quantity` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `unit_cost` DECIMAL(18,4) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inv_batch_unique` (`warehouse_id`,`product_id`,`batch_no`),
  KEY `inventory_batches_expires_at_status_index` (`expires_at`,`status`),
  KEY `inventory_batches_product_id_foreign` (`product_id`),
  CONSTRAINT `inventory_batches_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_cost_layers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `inventory_batch_id` BIGINT UNSIGNED NULL,
  `source_type` VARCHAR(50) NULL,
  `source_id` BIGINT UNSIGNED NULL,
  `transaction_date` DATE NOT NULL,
  `original_quantity` DECIMAL(18,4) NOT NULL,
  `quantity_remaining` DECIMAL(18,4) NOT NULL,
  `unit_cost` DECIMAL(18,4) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cost_layer_lookup` (`warehouse_id`,`product_id`,`transaction_date`),
  KEY `inventory_cost_layers_source_type_source_id_index` (`source_type`,`source_id`),
  KEY `inventory_cost_layers_product_id_foreign` (`product_id`),
  KEY `inventory_cost_layers_inventory_batch_id_foreign` (`inventory_batch_id`),
  CONSTRAINT `inventory_cost_layers_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_cost_layers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `inventory_cost_layers_inventory_batch_id_foreign` FOREIGN KEY (`inventory_batch_id`) REFERENCES `inventory_batches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initialize all legacy on-hand stock into the new valuation engine.
UPDATE `warehouse_stocks` ws
JOIN `products` p ON p.id=ws.product_id
SET ws.average_cost=p.purchase_price
WHERE ws.quantity > 0;

INSERT INTO `inventory_cost_layers`
(`warehouse_id`,`product_id`,`inventory_batch_id`,`source_type`,`source_id`,`transaction_date`,`original_quantity`,`quantity_remaining`,`unit_cost`,`created_at`,`updated_at`)
SELECT ws.warehouse_id,ws.product_id,NULL,'migration_opening',ws.id,CURDATE(),ws.quantity,ws.quantity,p.purchase_price,NOW(),NOW()
FROM `warehouse_stocks` ws
JOIN `products` p ON p.id=ws.product_id
WHERE ws.quantity > 0;

CREATE TABLE `loans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_no` VARCHAR(50) NOT NULL,
  `direction` VARCHAR(20) NOT NULL,
  `counterparty` VARCHAR(200) NOT NULL,
  `phone` VARCHAR(50) NULL,
  `principal` DECIMAL(18,2) NOT NULL,
  `method` VARCHAR(30) NOT NULL DEFAULT 'cash',
  `interest_rate` DECIMAL(8,4) NOT NULL DEFAULT 0,
  `installment_count` INT UNSIGNED NOT NULL DEFAULT 1,
  `start_date` DATE NOT NULL,
  `due_date` DATE NULL,
  `outstanding_principal` DECIMAL(18,2) NOT NULL,
  `interest_paid` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `notes` TEXT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `loans_loan_no_unique` (`loan_no`),
  KEY `loans_direction_status_index` (`direction`,`status`), KEY `loans_due_date_index` (`due_date`), KEY `loans_created_by_foreign` (`created_by`),
  CONSTRAINT `loans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `loan_installments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_id` BIGINT UNSIGNED NOT NULL,
  `installment_no` INT UNSIGNED NOT NULL,
  `due_date` DATE NOT NULL,
  `principal_due` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `interest_due` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `principal_paid` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `interest_paid` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `loan_installments_loan_id_installment_no_unique` (`loan_id`,`installment_no`),
  KEY `loan_installments_due_date_status_index` (`due_date`,`status`),
  CONSTRAINT `loan_installments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `loan_payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `loan_id` BIGINT UNSIGNED NOT NULL,
  `payment_no` VARCHAR(50) NOT NULL,
  `payment_date` DATE NOT NULL,
  `principal_amount` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `interest_amount` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `method` VARCHAR(30) NOT NULL DEFAULT 'cash',
  `reference` VARCHAR(150) NULL,
  `notes` TEXT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `loan_payments_payment_no_unique` (`payment_no`),
  KEY `loan_payments_loan_id_payment_date_index` (`loan_id`,`payment_date`), KEY `loan_payments_created_by_foreign` (`created_by`),
  CONSTRAINT `loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts` (`code`,`name`,`type`,`subtype`,`parent_id`,`is_system`,`is_active`,`created_at`,`updated_at`)
VALUES
('1300','Loan Receivable','asset','loan_receivable',NULL,1,1,NOW(),NOW()),
('2200','Loan Payable','liability','loan_payable',NULL,1,1,NOW(),NOW()),
('4110','Interest Income','income','interest',NULL,1,1,NOW(),NOW()),
('6050','Interest Expense','expense','interest',NULL,1,1,NOW(),NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`),`type`=VALUES(`type`),`subtype`=VALUES(`subtype`),`is_system`=1,`is_active`=1,`updated_at`=NOW();

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
