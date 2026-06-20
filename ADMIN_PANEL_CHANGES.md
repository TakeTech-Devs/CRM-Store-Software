# Admin Panel — Pending Schema & Feature Changes

These changes are required on the admin panel (`rightaid_admin` DB and admin Laravel project)
after the store panel is complete.

---

## 1. Database Schema Changes (run on `rightaid_admin`)

### `customer` table
```sql
ALTER TABLE `customer` ADD COLUMN `cus_id` VARCHAR(255) NULL UNIQUE AFTER `id`;
ALTER TABLE `customer` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `cus_id`;
ALTER TABLE `customer` ADD UNIQUE KEY `customer_store_phone_unique` (`store_id`, `phone`);
```
**Why:** Globally unique customer ID (`CUS-{storeId}-{00001}`) so store and admin IDs match everywhere. `store_id` enables per-store customer analytics.

---

### `customer_product_billing` table
```sql
ALTER TABLE `customer_product_billing` MODIFY COLUMN `productId` BIGINT UNSIGNED NULL;
ALTER TABLE `customer_product_billing` ADD COLUMN `inhouse_product_id` BIGINT UNSIGNED NULL;
ALTER TABLE `customer_product_billing` ADD COLUMN `is_inhouse` VARCHAR(10) NULL;
```
**Why:** Inhouse products have no `productId` (it's null). Column must be nullable to support inhouse billing sync.

---

### `staff_product_billing` table
```sql
ALTER TABLE `staff_product_billing` MODIFY COLUMN `productId` BIGINT UNSIGNED NULL;
ALTER TABLE `staff_product_billing` ADD COLUMN `inhouse_product_id` BIGINT UNSIGNED NULL;
ALTER TABLE `staff_product_billing` ADD COLUMN `is_inhouse` VARCHAR(10) NULL;
```
**Why:** Same inhouse product support as customer billing.

---

### `staff` table
```sql
ALTER TABLE `staff` ADD COLUMN `stf_id` VARCHAR(255) NULL UNIQUE AFTER `id`;
ALTER TABLE `staff` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `stf_id`;
ALTER TABLE `staff` ADD UNIQUE KEY `staff_store_phone_unique` (`store_id`, `phone`);

-- Backfill existing rows
SET @counter = 0;
UPDATE `staff` SET `stf_id` = CONCAT('STF-0-', LPAD(@counter := @counter + 1, 5, '0'))
WHERE `stf_id` IS NULL ORDER BY `id`;
```
**Why:** Globally unique staff ID (`STF-{storeId}-{00001}`) matching store panel. Same pattern as `cus_id`.

---

### `inhouse_product` table (create if not exists)
```sql
CREATE TABLE IF NOT EXISTS `inhouse_product` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_name` VARCHAR(255) NOT NULL,
    `price` VARCHAR(255) NOT NULL,
    `category_id` BIGINT UNSIGNED NULL,
    `sub_category_id` BIGINT UNSIGNED NULL,
    `pack_id` BIGINT UNSIGNED NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);
```
**Why:** Store syncs inhouse product billing to admin. Admin needs this table to reference inhouse products.

---

### `stock_transfer` table
```sql
-- Remote stock_transfer is flat (one product per row):
-- id, stock_from, stock_to, product (product_id), qty, unit_value, discount, price
-- Store local schema is different (header + items). No action needed on admin side
-- unless admin panel needs to display transfer history.
```
**Why:** Schemas differ. Store sync handles the mapping. Document only for awareness.

---

## 2. Admin Panel Code Changes (Laravel)

### Customer — generate `cus_id` on create
When admin creates a customer directly, generate `cus_id` using store_id=0 (admin-created):
```
CUS-0-00001  →  admin-created customers
CUS-1-00001  →  store 1 created customers
```

### Billing — display `inhouse_product_id` rows
Admin billing views need to handle rows where `productId` is null and `inhouse_product_id` is set — show the inhouse product name from the `inhouse_product` table.

### Sync — backfill `cus_id` for existing customers
Run this once after adding the `cus_id` column:
```sql
SET @counter = 0;
UPDATE `customer` SET `cus_id` = CONCAT('CUS-0-', LPAD(@counter := @counter + 1, 5, '0'))
WHERE `cus_id` IS NULL
ORDER BY `id`;
```

---

## 3. Feature Parity (after store panel complete)

| Feature | Store Panel | Admin Panel |
|---|---|---|
| Inhouse product billing | Done | Needs view update |
| Analytics page | Done | Not started |
| `cus_id` display in billing | Pending | Pending |
| Stock transfer display | Done | Review needed |

---

## Status
- [ ] Schema changes applied on admin DB
- [ ] Admin `cus_id` generation on customer create
- [ ] Admin billing views handle inhouse products
- [ ] End-to-end Sync In/Out tested with both panels updated
