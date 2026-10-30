-- M/S Afrin Traders - Production ERP Controls
-- Generated 2026-10-23
-- Run ONCE on the schema if Laravel migrations cannot be used.
-- Recommended deployment remains: php artisan migrate --force

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `accounting_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL, `end_date` date NOT NULL, `status` varchar(20) NOT NULL DEFAULT 'open',
  `closed_by` bigint unsigned NULL, `closed_at` timestamp NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `accounting_periods_dates_unique` (`start_date`,`end_date`),
  KEY `accounting_periods_status_dates_index` (`status`,`start_date`,`end_date`),
  CONSTRAINT `accounting_periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bank_reconciliations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `account_id` bigint unsigned NOT NULL, `statement_date` date NOT NULL,
  `statement_balance` decimal(18,2) NOT NULL, `system_balance` decimal(18,2) NOT NULL,
  `cleared_balance` decimal(18,2) NOT NULL DEFAULT 0, `difference` decimal(18,2) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'draft', `notes` text NULL,
  `created_by` bigint unsigned NULL, `closed_by` bigint unsigned NULL, `closed_at` timestamp NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  KEY `bank_reconciliations_account_date_index` (`account_id`,`statement_date`),
  CONSTRAINT `bank_reconciliations_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  CONSTRAINT `bank_reconciliations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_reconciliations_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bank_reconciliation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `bank_reconciliation_id` bigint unsigned NOT NULL,
  `journal_line_id` bigint unsigned NOT NULL, `amount` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  UNIQUE KEY `bank_reconciliation_items_unique` (`bank_reconciliation_id`,`journal_line_id`),
  CONSTRAINT `bank_reconciliation_items_reconciliation_foreign` FOREIGN KEY (`bank_reconciliation_id`) REFERENCES `bank_reconciliations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_reconciliation_items_journal_line_foreign` FOREIGN KEY (`journal_line_id`) REFERENCES `journal_lines` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tax_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(40) NOT NULL, `name` varchar(120) NOT NULL,
  `rate` decimal(8,4) NOT NULL DEFAULT 0, `type` varchar(20) NOT NULL DEFAULT 'vat',
  `is_default` tinyint(1) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY `tax_rates_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `approvable_type` varchar(80) NOT NULL, `approvable_id` bigint unsigned NOT NULL,
  `reference_no` varchar(120) NULL, `status` varchar(20) NOT NULL DEFAULT 'pending', `reason` text NULL,
  `requested_by` bigint unsigned NULL, `decided_by` bigint unsigned NULL, `decided_at` timestamp NULL, `decision_notes` text NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  UNIQUE KEY `approval_requests_approvable_unique` (`approvable_type`,`approvable_id`), KEY `approval_requests_status_created_index` (`status`,`created_at`),
  CONSTRAINT `approval_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_requests_decided_by_foreign` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `order_no` varchar(50) NOT NULL, `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL, `order_date` date NOT NULL, `expected_date` date NULL, `status` varchar(20) NOT NULL DEFAULT 'open',
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0, `currency_code` varchar(10) NOT NULL DEFAULT 'BDT', `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1,
  `notes` text NULL, `converted_id` bigint unsigned NULL, `created_by` bigint unsigned NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `purchase_orders_order_no_unique` (`order_no`),
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `order_no` varchar(50) NOT NULL, `customer_id` bigint unsigned NULL,
  `warehouse_id` bigint unsigned NOT NULL, `order_date` date NOT NULL, `expected_date` date NULL, `status` varchar(20) NOT NULL DEFAULT 'open',
  `grand_total` decimal(18,2) NOT NULL DEFAULT 0, `currency_code` varchar(10) NOT NULL DEFAULT 'BDT', `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1,
  `notes` text NULL, `converted_id` bigint unsigned NULL, `created_by` bigint unsigned NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `sales_orders_order_no_unique` (`order_no`),
  CONSTRAINT `sales_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `sales_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `purchase_order_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_price` decimal(18,4) NOT NULL, `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  CONSTRAINT `purchase_order_items_order_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `sales_order_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `unit_price` decimal(18,4) NOT NULL, `line_total` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`),
  CONSTRAINT `sales_order_items_order_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_order_items_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `warehouse_id` bigint unsigned NOT NULL, `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL, `reference_type` varchar(50) NULL, `reference_id` bigint unsigned NULL, `reference_no` varchar(100) NULL,
  `reserved_until` date NULL, `status` varchar(20) NOT NULL DEFAULT 'active', `notes` text NULL, `created_by` bigint unsigned NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), KEY `inventory_reservations_stock_status_index` (`warehouse_id`,`product_id`,`status`),
  CONSTRAINT `inventory_reservations_warehouse_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`),
  CONSTRAINT `inventory_reservations_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `inventory_reservations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_serials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `product_id` bigint unsigned NOT NULL, `warehouse_id` bigint unsigned NULL,
  `serial_no` varchar(160) NOT NULL, `imei` varchar(160) NULL, `status` varchar(20) NOT NULL DEFAULT 'in_stock',
  `source_type` varchar(50) NULL, `source_id` bigint unsigned NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `product_serials_serial_no_unique` (`serial_no`), UNIQUE KEY `product_serials_imei_unique` (`imei`),
  KEY `product_serials_product_status_index` (`product_id`,`status`),
  CONSTRAINT `product_serials_product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `product_serials_warehouse_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(10) NOT NULL, `name` varchar(100) NOT NULL, `symbol` varchar(12) NULL,
  `is_base` tinyint(1) NOT NULL DEFAULT 0, `is_active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL, `updated_at` timestamp NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `currencies_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `exchange_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `currency_code` varchar(10) NOT NULL, `rate_date` date NOT NULL, `rate` decimal(18,6) NOT NULL,
  `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), UNIQUE KEY `exchange_rates_currency_date_unique` (`currency_code`,`rate_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `document_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `attachable_type` varchar(60) NOT NULL, `attachable_id` bigint unsigned NOT NULL,
  `original_name` varchar(255) NOT NULL, `stored_path` varchar(255) NOT NULL, `mime_type` varchar(120) NULL, `size` bigint unsigned NOT NULL DEFAULT 0,
  `uploaded_by` bigint unsigned NULL, `created_at` timestamp NULL, `updated_at` timestamp NULL, PRIMARY KEY (`id`), KEY `document_attachments_attachable_index` (`attachable_type`,`attachable_id`),
  CONSTRAINT `document_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cost_centers` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`code` varchar(40) NOT NULL,`name` varchar(120) NOT NULL,`is_active` tinyint(1) NOT NULL DEFAULT 1,`created_at` timestamp NULL,`updated_at` timestamp NULL,PRIMARY KEY (`id`),UNIQUE KEY `cost_centers_code_unique` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `projects` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`code` varchar(40) NOT NULL,`name` varchar(160) NOT NULL,`cost_center_id` bigint unsigned NULL,`start_date` date NULL,`end_date` date NULL,`status` varchar(20) NOT NULL DEFAULT 'active',`created_at` timestamp NULL,`updated_at` timestamp NULL,PRIMARY KEY (`id`),UNIQUE KEY `projects_code_unique` (`code`),CONSTRAINT `projects_cost_center_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `budgets` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`cost_center_id` bigint unsigned NULL,`project_id` bigint unsigned NULL,`account_id` bigint unsigned NOT NULL,`period_start` date NOT NULL,`period_end` date NOT NULL,`amount` decimal(18,2) NOT NULL,`created_at` timestamp NULL,`updated_at` timestamp NULL,PRIMARY KEY (`id`),KEY `budgets_period_index` (`period_start`,`period_end`),CONSTRAINT `budgets_cost_center_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,CONSTRAINT `budgets_project_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,CONSTRAINT `budgets_account_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fixed_assets` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`asset_no` varchar(50) NOT NULL,`name` varchar(180) NOT NULL,`purchase_date` date NULL,`cost` decimal(18,2) NOT NULL,`salvage_value` decimal(18,2) NOT NULL DEFAULT 0,`useful_life_months` int unsigned NOT NULL DEFAULT 60,`depreciation_method` varchar(30) NOT NULL DEFAULT 'straight_line',`accumulated_depreciation` decimal(18,2) NOT NULL DEFAULT 0,`status` varchar(20) NOT NULL DEFAULT 'active',`notes` text NULL,`created_at` timestamp NULL,`updated_at` timestamp NULL,PRIMARY KEY (`id`),UNIQUE KEY `fixed_assets_asset_no_unique` (`asset_no`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `fixed_asset_depreciations` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`fixed_asset_id` bigint unsigned NOT NULL,`depreciation_date` date NOT NULL,`amount` decimal(18,2) NOT NULL,`created_by` bigint unsigned NULL,`created_at` timestamp NULL,`updated_at` timestamp NULL,PRIMARY KEY (`id`),UNIQUE KEY `fixed_asset_depreciations_unique` (`fixed_asset_id`,`depreciation_date`),CONSTRAINT `fixed_asset_depreciations_asset_foreign` FOREIGN KEY (`fixed_asset_id`) REFERENCES `fixed_assets` (`id`) ON DELETE CASCADE,CONSTRAINT `fixed_asset_depreciations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `products` ADD `track_serial` tinyint(1) NOT NULL DEFAULT 0 AFTER `track_stock`;
ALTER TABLE `sales` ADD `currency_code` varchar(10) NOT NULL DEFAULT 'BDT' AFTER `status`, ADD `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1 AFTER `currency_code`;
ALTER TABLE `purchases` ADD `currency_code` varchar(10) NOT NULL DEFAULT 'BDT' AFTER `status`, ADD `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1 AFTER `currency_code`;
ALTER TABLE `expenses` ADD `cost_center_id` bigint unsigned NULL, ADD `project_id` bigint unsigned NULL,
  ADD CONSTRAINT `expenses_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;
ALTER TABLE `journal_lines` ADD `cost_center_id` bigint unsigned NULL, ADD `project_id` bigint unsigned NULL,
  ADD CONSTRAINT `journal_lines_cost_center_id_foreign` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `journal_lines_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

INSERT IGNORE INTO `currencies` (`code`,`name`,`symbol`,`is_base`,`is_active`,`created_at`,`updated_at`) VALUES ('BDT','Bangladeshi Taka','৳',1,1,NOW(),NOW());
INSERT IGNORE INTO `tax_rates` (`code`,`name`,`rate`,`type`,`is_default`,`is_active`,`created_at`,`updated_at`) VALUES
('TAX-0','Zero Rated',0,'vat',0,1,NOW(),NOW()),('VAT-5','VAT 5%',5,'vat',0,1,NOW(),NOW()),('VAT-7.5','VAT 7.5%',7.5,'vat',0,1,NOW(),NOW()),('VAT-10','VAT 10%',10,'vat',0,1,NOW(),NOW()),('VAT-15','VAT 15%',15,'vat',0,1,NOW(),NOW());
INSERT IGNORE INTO `accounts` (`code`,`name`,`type`,`subtype`,`is_system`,`is_active`,`created_at`,`updated_at`) VALUES
('1300','Fixed Assets','asset','fixed_asset',1,1,NOW(),NOW()),('1310','Accumulated Depreciation','asset','contra_asset',1,1,NOW(),NOW()),('6050','Depreciation Expense','expense','depreciation',1,1,NOW(),NOW());

INSERT INTO `settings` (`key`,`value`,`group`,`type`,`is_public`,`created_at`,`updated_at`) VALUES
('maker_checker_enabled','0','controls','boolean',0,NOW(),NOW()),('credit_limit_enforced','1','controls','boolean',0,NOW(),NOW()),('auto_payment_terms','1','controls','boolean',0,NOW(),NOW()),('auto_product_tax','1','controls','boolean',0,NOW(),NOW())
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`),`group`=VALUES(`group`),`type`=VALUES(`type`),`is_public`=VALUES(`is_public`),`updated_at`=NOW();

INSERT IGNORE INTO `permissions` (`name`,`guard_name`,`created_at`,`updated_at`) VALUES
('production_controls.view','web',NOW(),NOW()),('production_controls.manage','web',NOW(),NOW()),('approvals.view','web',NOW(),NOW()),('approvals.manage','web',NOW(),NOW()),('orders.view','web',NOW(),NOW()),('orders.manage','web',NOW(),NOW()),('bank_reconciliation.view','web',NOW(),NOW()),('bank_reconciliation.manage','web',NOW(),NOW()),('taxes.view','web',NOW(),NOW()),('taxes.manage','web',NOW(),NOW()),('planning.view','web',NOW(),NOW()),('planning.manage','web',NOW(),NOW()),('assets.view','web',NOW(),NOW()),('assets.manage','web',NOW(),NOW());

INSERT IGNORE INTO `role_has_permissions` (`permission_id`,`role_id`)
SELECT p.id,r.id FROM permissions p JOIN roles r ON r.name IN ('Super Admin','Admin')
WHERE p.name IN ('production_controls.view','production_controls.manage','approvals.view','approvals.manage','orders.view','orders.manage','bank_reconciliation.view','bank_reconciliation.manage','taxes.view','taxes.manage','planning.view','planning.manage','assets.view','assets.manage');

SET FOREIGN_KEY_CHECKS=1;
