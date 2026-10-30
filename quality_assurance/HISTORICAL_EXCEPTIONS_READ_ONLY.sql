-- M/S Afrin Traders — READ-ONLY MySQL reconciliation worksheet.
-- Run on a STAGING database; inspect data before posting any corrective entries.
-- This script deliberately contains NO INSERT, UPDATE, DELETE, ALTER, DROP, or TRUNCATE.
-- Compare results to the October 27 SQL export findings; live results may differ.

SELECT DATABASE() AS inspected_database;

-- 1) Migration status in the imported export. The current project has 24 PHP migrations.
SELECT migration, batch FROM migrations ORDER BY id DESC;
SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pos_checkout_requests';

-- 2) Check posted returns linked to invoices (including return IDs 1 and 3).
SELECT r.id AS return_id, r.sale_id, r.status AS return_status,
       r.customer_id AS return_customer_id, s.customer_id AS original_customer_id,
       r.warehouse_id AS return_warehouse_id, s.warehouse_id AS original_warehouse_id,
       CASE WHEN s.id IS NULL THEN 'MISSING_ORIGINAL'
            WHEN s.customer_id <> r.customer_id OR s.warehouse_id <> r.warehouse_id THEN 'PARTY_OR_WAREHOUSE_MISMATCH'
            ELSE 'PARTY_AND_WAREHOUSE_MATCH' END AS result
FROM sale_returns r LEFT JOIN sales s ON s.id = r.sale_id
WHERE r.status='posted';

SELECT r.id AS return_id, r.sale_id, ri.product_id AS returned_product_id,
       ri.quantity AS returned_qty
FROM sale_returns r JOIN sale_return_items ri ON ri.sale_return_id=r.id
WHERE r.status='posted' AND NOT EXISTS (
 SELECT 1 FROM sale_items si WHERE si.sale_id=r.sale_id AND si.product_id=ri.product_id
);

SELECT r.id AS return_id, r.purchase_id, ri.product_id, ri.quantity
FROM purchase_returns r JOIN purchase_return_items ri ON ri.purchase_return_id=r.id
WHERE r.status='posted' AND NOT EXISTS (
 SELECT 1 FROM purchase_items pi WHERE pi.purchase_id=r.purchase_id AND pi.product_id=ri.product_id
);

-- 3) Warehouse transfers: posted transfer lines vs actual movement quantities.
SELECT t.id AS transfer_id, t.transfer_no, t.status, ti.product_id, ti.quantity AS expected_qty,
       COALESCE((SELECT SUM(m.quantity_out) FROM stock_movements m
         WHERE m.source_type='stock_transfer' AND m.source_id=t.id
           AND m.warehouse_id=t.from_warehouse_id AND m.product_id=ti.product_id),0) AS outbound_qty,
       COALESCE((SELECT SUM(m.quantity_in) FROM stock_movements m
         WHERE m.source_type='stock_transfer' AND m.source_id=t.id
           AND m.warehouse_id=t.to_warehouse_id AND m.product_id=ti.product_id),0) AS inbound_qty
FROM stock_transfers t JOIN stock_transfer_items ti ON ti.stock_transfer_id=t.id
WHERE t.status='posted' ORDER BY t.id, ti.product_id;

-- 4) GL inventory account (1200) and independently calculated inventory cost valuation.
SELECT a.code, ROUND(COALESCE(SUM(CASE WHEN j.status='posted' THEN l.debit-l.credit ELSE 0 END),0),2) AS posted_inventory_gl
FROM accounts a LEFT JOIN journal_lines l ON l.account_id=a.id
LEFT JOIN journal_entries j ON j.id=l.journal_entry_id AND j.status='posted'
WHERE a.code='1200' GROUP BY a.code;

SELECT ROUND(SUM(CASE WHEN p.costing_method='fifo'
     THEN COALESCE(c.value,0)+GREATEST(0,w.quantity-COALESCE(c.layer_qty,0)) *
          (CASE WHEN w.average_cost>0 THEN w.average_cost ELSE p.purchase_price END)
     ELSE w.quantity*(CASE WHEN w.average_cost>0 THEN w.average_cost ELSE p.purchase_price END)
END),2) AS stock_value
FROM warehouse_stocks w JOIN products p ON p.id=w.product_id
LEFT JOIN (SELECT warehouse_id, product_id,
                 SUM(quantity_remaining) AS layer_qty,
                 SUM(quantity_remaining*unit_cost) AS value
           FROM inventory_cost_layers GROUP BY warehouse_id,product_id) c
ON c.warehouse_id=w.warehouse_id AND c.product_id=w.product_id;

-- 5) Bank account (1010) must be compared to real bank statements, not reversed automatically.
SELECT a.code, ROUND(COALESCE(SUM(CASE WHEN j.status='posted' THEN l.debit-l.credit ELSE 0 END),0),2) AS posted_bank_gl
FROM accounts a LEFT JOIN journal_lines l ON l.account_id=a.id
LEFT JOIN journal_entries j ON j.id=l.journal_entry_id
WHERE a.code='1010' GROUP BY a.code;
SELECT COUNT(*) AS completed_bank_reconciliations FROM bank_reconciliations WHERE status='completed';

-- 6) Review stale permissions without granting/revoking privileges.
SELECT r.name AS role_name,p.name AS permission_name
FROM role_has_permissions rp JOIN roles r ON r.id=rp.role_id
JOIN permissions p ON p.id=rp.permission_id
WHERE r.name IN ('Seller','Sales Executive')
ORDER BY r.name,p.name;
