-- M/S Afrin Traders — guarded reconciliation for migration history only.
-- Target: the user's 2026-10-27 MySQL/MariaDB schema snapshot.
-- IMPORTANT: Review and back up the real DB before running this script.
-- It DOES NOT create/drop/alter tables or business data.
-- It only registers two missing Laravel migration records after checking key schema elements.
-- All schema objects are expected to exist from manual SQL patches.
-- NOTE: These are expanded structural checks, not a full schema/data equivalence proof.
-- REQUIRES OPERATOR APPROVAL: change 0 to 1 below only after a verified staging QA.
SET @operator_approved := 0;

SET @tables_present := (
    SELECT COUNT(DISTINCT TABLE_NAME)
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('business_days', 'market_hierarchies', 'accounting_periods', 'bank_reconciliations', 'bank_reconciliation_items', 'tax_rates', 'approval_requests', 'purchase_orders', 'sales_orders', 'purchase_order_items', 'sales_order_items', 'inventory_reservations', 'product_serials', 'currencies', 'exchange_rates', 'document_attachments', 'cost_centers', 'projects', 'budgets', 'fixed_assets', 'fixed_asset_depreciations')
);
SET @columns_present := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND (
      (TABLE_NAME='customers' AND COLUMN_NAME='market_hierarchy_id') OR
      (TABLE_NAME='products' AND COLUMN_NAME='track_serial') OR
      (TABLE_NAME='sales' AND COLUMN_NAME='currency_code') OR
      (TABLE_NAME='sales' AND COLUMN_NAME='exchange_rate') OR
      (TABLE_NAME='purchases' AND COLUMN_NAME='currency_code') OR
      (TABLE_NAME='purchases' AND COLUMN_NAME='exchange_rate') OR
      (TABLE_NAME='expenses' AND COLUMN_NAME='cost_center_id') OR
      (TABLE_NAME='expenses' AND COLUMN_NAME='project_id') OR
      (TABLE_NAME='journal_lines' AND COLUMN_NAME='cost_center_id') OR
      (TABLE_NAME='journal_lines' AND COLUMN_NAME='project_id')
    )
);
-- Permissions seeded by BOTH migrations must already be present.
SET @permissions_present := (
 SELECT COUNT(DISTINCT name) FROM permissions WHERE guard_name = 'web' AND name IN (
 'client_setup.view','reorder_setup.view','reorder_setup.edit',
 'business_days.view','business_days.manage','user_modules.view','user_modules.edit',
 'market_hierarchy.view','market_hierarchy.create','market_hierarchy.edit','market_hierarchy.delete',
 'barcode_print.view',
 'production_controls.view','production_controls.manage','approvals.view','approvals.manage',
 'orders.view','orders.manage','bank_reconciliation.view','bank_reconciliation.manage',
 'taxes.view','taxes.manage','planning.view','planning.manage','assets.view','assets.manage'
 )
);
-- Check three business-critical unique indexes from the two schema migrations.
SET @indexes_present := (
 SELECT COUNT(*) FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = DATABASE() AND NON_UNIQUE = 0 AND SEQ_IN_INDEX = 1 AND (
 (TABLE_NAME='business_days' AND INDEX_NAME='business_days_business_date_unique') OR
 (TABLE_NAME='market_hierarchies' AND INDEX_NAME='market_hierarchies_code_unique') OR
 (TABLE_NAME='bank_reconciliation_items' AND INDEX_NAME='bank_reconciliation_items_unique')
 )
);
SET @schema_verified := (@tables_present = 21 AND @columns_present = 10 AND
                         @permissions_present = 26 AND @indexes_present = 3);


SELECT DATABASE() AS database_name,
       @tables_present AS tables_present, 21 AS expected_tables,
       @columns_present AS columns_present, 10 AS expected_columns,
       @permissions_present AS permissions_present, 26 AS expected_permissions,
       @indexes_present AS unique_indexes_present, 3 AS expected_unique_indexes,
       @operator_approved AS operator_approved,
       IF(@schema_verified, 'STRUCTURAL CHECK PASS — OPERATOR APPROVAL ALSO REQUIRED',
          'FAIL: NO MIGRATION HISTORY CHANGES WILL BE MADE') AS preflight;

-- If any check fails, or if @operator_approved stays 0, these INSERTs make NO changes.
-- DO NOT use this script on a different schema or on production without independent DBA review.
-- Do not rerun original Laravel schema-creation migrations against existing tables.
START TRANSACTION;
SET @migration_batch := (SELECT COALESCE(MAX(batch),0) + 1 FROM migrations);
INSERT INTO migrations (migration, batch)
SELECT '2026_10_22_130000_complete_settings_requirements', @migration_batch
WHERE @schema_verified = 1 AND @operator_approved = 1
  AND NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_10_22_130000_complete_settings_requirements');
INSERT INTO migrations (migration, batch)
SELECT '2026_10_23_000000_complete_production_erp_controls', @migration_batch
WHERE @schema_verified = 1 AND @operator_approved = 1
  AND NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_10_23_000000_complete_production_erp_controls');
COMMIT;

SELECT migration, batch FROM migrations
WHERE migration IN (
 '2026_10_22_130000_complete_settings_requirements',
 '2026_10_23_000000_complete_production_erp_controls'
) ORDER BY migration;
