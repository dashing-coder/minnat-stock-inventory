-- M/S Afrin Traders ERP - Phase 1 feature patch
-- Date: 2026-10-22
-- Adds persisted sales item price type for Retail / Wholesale / Special pricing.

SET @schema_name = DATABASE();
SET @column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @schema_name
      AND TABLE_NAME = 'sale_items'
      AND COLUMN_NAME = 'price_type'
);

SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE `sale_items` ADD COLUMN `price_type` VARCHAR(20) NOT NULL DEFAULT ''retail'' AFTER `cost_price`',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `sale_items`
SET `price_type` = 'retail'
WHERE `price_type` IS NULL OR `price_type` = '';
