-- M/S Afrin Traders ERP - Seller Control module
-- Use only if you cannot run Laravel migrations/seeders.
-- Always back up the database before applying.

ALTER TABLE `users`
    ADD COLUMN `seller_code` VARCHAR(30) NULL AFTER `username`,
    ADD UNIQUE KEY `users_seller_code_unique` (`seller_code`);

INSERT INTO `roles` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT 'Seller', 'web', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `roles` WHERE `name` = 'Seller' AND `guard_name` = 'web'
);

INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT 'seller_accounts.view', 'web', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permissions` WHERE `name` = 'seller_accounts.view' AND `guard_name` = 'web'
);

-- Assign Seller operational permissions. Existing permission rows are reused.
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r ON r.name = 'Seller' AND r.guard_name = 'web'
WHERE p.name IN (
    'sales.view',
    'sales.create',
    'sales.post',
    'customer_payments.view',
    'customer_payments.create'
);

-- New administrative seller-account permission belongs only to Admin/Super Admin.
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id
FROM `permissions` p
JOIN `roles` r ON r.name IN ('Super Admin', 'Admin') AND r.guard_name = 'web'
WHERE p.name = 'seller_accounts.view';

-- Mark the Laravel migration as applied so a later `php artisan migrate`
-- does not try to add seller_code a second time.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_28_001000_add_seller_code_to_users_table', next_batch.batch_no
FROM (
    SELECT COALESCE(MAX(`batch`), 0) + 1 AS batch_no FROM `migrations`
) AS next_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_09_28_001000_add_seller_code_to_users_table'
);

