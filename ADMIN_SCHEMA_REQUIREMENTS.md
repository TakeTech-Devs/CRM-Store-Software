# Admin Panel — Full Schema Requirements

Compiled from store panel migrations. Each table shows its **final store schema** and what the **admin DB needs**.

---

## Legend
- ✅ Admin already has this (assumed from sync working)
- ⚠️ Admin needs ALTER (column missing or wrong type)
- ❌ Admin needs CREATE TABLE
- 🔁 Store-only table (no admin equivalent needed)

---

## 1. `store` table ✅ (admin is source of truth)

Admin already owns this. Store reads from it during Sync In.

| Column | Type |
|---|---|
| id | bigint PK auto |
| name | varchar nullable |
| store_address | varchar nullable |
| store_mail | varchar nullable |
| store_start_date | varchar nullable |
| store_meta_id | varchar nullable |
| store_pass_key | varchar nullable |
| store_status | varchar nullable |
| store_verify_status | boolean nullable |
| dl_number | varchar nullable |
| helpline_number | varchar nullable |
| created_at / updated_at | timestamp |

---

## 2. `customer` table ⚠️

Admin has the base table. These columns are **missing**:

```sql
ALTER TABLE `customer` ADD COLUMN `cus_id` VARCHAR(255) NULL UNIQUE AFTER `id`;
ALTER TABLE `customer` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `cus_id`;
ALTER TABLE `customer` ADD UNIQUE KEY `customer_store_phone_unique` (`store_id`, `phone`);

-- Backfill existing admin-created customers
SET @counter = 0;
UPDATE `customer`
SET `cus_id` = CONCAT('CUS-0-', LPAD(@counter := @counter + 1, 5, '0'))
WHERE `cus_id` IS NULL
ORDER BY `id`;
```

**Final schema:**

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| cus_id | varchar UNIQUE | ⚠️ ADD — format: CUS-{storeId}-{00001} |
| store_id | bigint nullable | ⚠️ ADD — 0 = admin created |
| name | varchar | |
| mail | varchar | |
| phone | varchar | |
| status | varchar | |
| created_at / updated_at | timestamp | |
| UNIQUE (store_id, phone) | | ⚠️ ADD |

---

## 3. `staff` table ⚠️

```sql
ALTER TABLE `staff` ADD COLUMN `stf_id` VARCHAR(255) NULL UNIQUE AFTER `id`;
ALTER TABLE `staff` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `stf_id`;
ALTER TABLE `staff` ADD UNIQUE KEY `staff_store_phone_unique` (`store_id`, `phone`);

-- Backfill existing admin-created staff
SET @counter = 0;
UPDATE `staff`
SET `stf_id` = CONCAT('STF-0-', LPAD(@counter := @counter + 1, 5, '0'))
WHERE `stf_id` IS NULL
ORDER BY `id`;
```

**Final schema:**

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| stf_id | varchar UNIQUE | ⚠️ ADD — company-provided ID |
| store_id | bigint nullable | ⚠️ ADD |
| name | varchar | |
| mail | varchar | |
| phone | varchar | |
| status | varchar | |
| created_at / updated_at | timestamp | |
| UNIQUE (store_id, phone) | | ⚠️ ADD |

---

## 4. `doctor` table ✅

No changes needed. Admin is source of truth, store reads during Sync In.

| Column | Type |
|---|---|
| id | bigint PK auto |
| name | varchar |
| mail | varchar |
| phone | varchar |
| degree | varchar |
| status | varchar |
| created_at / updated_at | timestamp |

---

## 5. `brand` table ✅

Admin is source of truth.

| Column | Type |
|---|---|
| id | bigint PK auto |
| brand_name | varchar |
| status | boolean |
| created_at / updated_at | timestamp |

---

## 6. `category` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| category_name | varchar |
| status | boolean |
| created_at / updated_at | timestamp |

---

## 7. `sub_category` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| category_id | bigint FK → category |
| sub_category_name | varchar |
| status | boolean |
| created_at / updated_at | timestamp |

---

## 8. `pack` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| pack_name | varchar |
| status | boolean |
| created_at / updated_at | timestamp |

---

## 9. `price` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| price_name | varchar |
| created_at / updated_at | timestamp |

---

## 10. `product` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| product_name | varchar |
| brand_id | bigint FK → brand |
| category_id | bigint FK → category |
| sub_category_id | bigint FK → sub_category |
| hsn_code | varchar |
| gst | varchar |
| status | boolean |
| created_at / updated_at | timestamp |

---

## 11. `supplier` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| supplier_name | varchar |
| status | boolean |
| created_at / updated_at | timestamp |

---

## 12. `purchase_stock` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| sku_id | varchar |
| supplier_id | bigint FK → supplier |
| purchase_bill_number | varchar |
| total | varchar |
| created_at / updated_at | timestamp |

---

## 13. `purchase_stock_entry` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| purchase_stock_id | bigint FK |
| brand_id | bigint FK |
| category_id | bigint FK |
| sub_category_id | bigint FK |
| product_id | bigint FK |
| pack_id | bigint FK |
| price_id | bigint FK |
| qty | varchar |
| created_at / updated_at | timestamp |

---

## 14. `store_assign` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| store_id | bigint FK → store |
| assign_bill_number | varchar |
| total | varchar |
| created_at / updated_at | timestamp |

---

## 15. `purchase_request` table ✅ + ⚠️

Store reads this during Sync In to get assigned stock. Admin needs `transfer_id` column:

```sql
ALTER TABLE `purchase_request` ADD COLUMN `transfer_id` BIGINT UNSIGNED NULL;
```

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| store_assign_id | bigint FK | |
| brand_id | bigint FK | |
| product_id | bigint FK | |
| pack_id | bigint FK | |
| price_id | bigint FK | |
| qty | varchar | |
| qty_left | varchar | |
| transfer_id | bigint nullable | ⚠️ ADD |
| created_at / updated_at | timestamp | |

---

## 16. `inhouse_product` table ❌

**Needs to be created on admin:**

```sql
CREATE TABLE IF NOT EXISTS `inhouse_product` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_name` VARCHAR(255) NOT NULL,
    `price` INT NOT NULL,
    `category_id` BIGINT UNSIGNED NULL,
    `sub_category_id` BIGINT UNSIGNED NULL,
    `pack_id` BIGINT UNSIGNED NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);
```

---

## 17. `customer_billing` table ⚠️

```sql
ALTER TABLE `customer_billing` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `id`;
-- Add GST columns if missing:
ALTER TABLE `customer_billing` ADD COLUMN `gst` VARCHAR(255) NULL;
ALTER TABLE `customer_billing` ADD COLUMN `cgst` VARCHAR(255) NULL;
ALTER TABLE `customer_billing` ADD COLUMN `sgst` VARCHAR(255) NULL;
```

**Final schema:**

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| store_id | bigint nullable | ⚠️ ADD |
| customer_phone | varchar | |
| customer_name | varchar | |
| doctor_name | varchar | |
| invoiceNo | varchar | |
| paymentType | varchar | |
| billing_date | varchar | |
| total_amt | varchar | |
| billingType | varchar | |
| gst | varchar | ⚠️ ADD if missing |
| cgst | varchar | ⚠️ ADD if missing |
| sgst | varchar | ⚠️ ADD if missing |
| created_at / updated_at | timestamp | |

---

## 18. `customer_product_billing` table ⚠️

```sql
ALTER TABLE `customer_product_billing` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `id`;
ALTER TABLE `customer_product_billing` MODIFY COLUMN `productId` BIGINT UNSIGNED NULL;
ALTER TABLE `customer_product_billing` ADD COLUMN `inhouse_product_id` BIGINT UNSIGNED NULL;
-- Add GST columns if missing:
ALTER TABLE `customer_product_billing` ADD COLUMN `gstRate` VARCHAR(255) NULL;
ALTER TABLE `customer_product_billing` ADD COLUMN `gstAmount` VARCHAR(255) NULL;
```

**Final schema:**

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| store_id | bigint nullable | ⚠️ ADD |
| cb_id | bigint FK → customer_billing | |
| category | varchar | |
| discount | varchar | |
| pack | varchar | |
| productId | bigint nullable | ⚠️ MODIFY to nullable |
| inhouse_product_id | bigint nullable | ⚠️ ADD |
| qty | varchar | |
| subCategory | varchar | |
| totalAmount | varchar | |
| unitValue | varchar | |
| gstRate | varchar | ⚠️ ADD if missing |
| gstAmount | varchar | ⚠️ ADD if missing |
| created_at / updated_at | timestamp | |

---

## 19. `staff_billing` table ⚠️

```sql
ALTER TABLE `staff_billing` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `id`;
ALTER TABLE `staff_billing` ADD COLUMN `billingType` VARCHAR(255) NULL;
ALTER TABLE `staff_billing` ADD COLUMN `gst` VARCHAR(255) NULL;
ALTER TABLE `staff_billing` ADD COLUMN `cgst` VARCHAR(255) NULL;
ALTER TABLE `staff_billing` ADD COLUMN `sgst` VARCHAR(255) NULL;
```

**Final schema:**

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| store_id | bigint nullable | ⚠️ ADD |
| staff_phone | varchar | |
| staff_name | varchar | |
| doctor_name | varchar | |
| invoiceNo | varchar | |
| paymentType | varchar | |
| billing_date | varchar | |
| total_amt | varchar | |
| billingType | varchar | ⚠️ ADD if missing |
| gst | varchar | ⚠️ ADD if missing |
| cgst | varchar | ⚠️ ADD if missing |
| sgst | varchar | ⚠️ ADD if missing |
| created_at / updated_at | timestamp | |

---

## 20. `staff_product_billing` table ⚠️

```sql
ALTER TABLE `staff_product_billing` ADD COLUMN `store_id` BIGINT UNSIGNED NULL AFTER `id`;
ALTER TABLE `staff_product_billing` MODIFY COLUMN `productId` BIGINT UNSIGNED NULL;
ALTER TABLE `staff_product_billing` ADD COLUMN `inhouse_product_id` BIGINT UNSIGNED NULL;
ALTER TABLE `staff_product_billing` ADD COLUMN `gstRate` VARCHAR(255) NULL;
ALTER TABLE `staff_product_billing` ADD COLUMN `gstAmount` VARCHAR(255) NULL;
```

**Final schema:**

| Column | Type | Notes |
|---|---|---|
| id | bigint PK auto | |
| store_id | bigint nullable | ⚠️ ADD |
| cb_id | bigint FK → staff_billing | |
| category | varchar | |
| discount | varchar | |
| pack | varchar | |
| productId | bigint nullable | ⚠️ MODIFY to nullable |
| inhouse_product_id | bigint nullable | ⚠️ ADD |
| qty | varchar | |
| subCategory | varchar | |
| totalAmount | varchar | |
| unitValue | varchar | |
| gstRate | varchar | ⚠️ ADD if missing |
| gstAmount | varchar | ⚠️ ADD if missing |
| created_at / updated_at | timestamp | |

---

## 21. `stock_transfer` table ❌ (recreate / redesign)

Admin's old schema (`requested_id`, `requester_id`, `stock_id`) is completely different from the store's new schema. Admin needs the same structure:

```sql
-- Drop old table if exists (backup data first!)
-- CREATE new schema:
CREATE TABLE IF NOT EXISTS `stock_transfer` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `transfer_no` VARCHAR(255) NOT NULL UNIQUE,
    `from_store_id` BIGINT UNSIGNED NOT NULL,
    `to_store_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending','received') NOT NULL DEFAULT 'pending',
    `received_at` TIMESTAMP NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS `stock_transfer_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `transfer_id` BIGINT UNSIGNED NOT NULL,
    `purchase_request_id` BIGINT UNSIGNED NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `pack_id` BIGINT UNSIGNED NOT NULL,
    `pack_name` VARCHAR(255) NOT NULL,
    `price_id` BIGINT UNSIGNED NOT NULL,
    `brand_id` BIGINT UNSIGNED NULL,
    `unit_value` VARCHAR(255) NOT NULL,
    `qty` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`transfer_id`) REFERENCES `stock_transfer`(`id`) ON DELETE CASCADE
);
```

---

## 22. `tax_tables` table ✅

| Column | Type |
|---|---|
| id | bigint PK auto |
| hsn | varchar |
| gst | varchar |
| created_at / updated_at | timestamp |

---

## 23. Store-only tables 🔁 (not needed on admin)

These only exist locally on each store and are never synced to admin:

| Table | Purpose |
|---|---|
| `sync_history` | Local sync log |
| `backup` | Local backup records |
| `sessions` | Laravel sessions |
| `cache` | Laravel cache |
| `personal_access_tokens` | API tokens |

---

## Summary — What admin DB needs

| # | Table | Action | Priority |
|---|---|---|---|
| 1 | `customer` | ADD `cus_id`, `store_id`, unique key | 🔴 High |
| 2 | `staff` | ADD `stf_id`, `store_id`, unique key | 🔴 High |
| 3 | `customer_billing` | ADD `store_id`, GST columns | 🔴 High |
| 4 | `customer_product_billing` | ADD `store_id`, `inhouse_product_id`, nullable `productId`, GST cols | 🔴 High |
| 5 | `staff_billing` | ADD `store_id`, `billingType`, GST columns | 🔴 High |
| 6 | `staff_product_billing` | ADD `store_id`, `inhouse_product_id`, nullable `productId`, GST cols | 🔴 High |
| 7 | `inhouse_product` | CREATE TABLE | 🔴 High |
| 8 | `stock_transfer` | Redesign to new schema | 🟠 Medium |
| 9 | `stock_transfer_items` | CREATE TABLE | 🟠 Medium |
| 10 | `purchase_request` | ADD `transfer_id` | 🟠 Medium |
| 11 | All master tables | Verify they exist (brand, category, etc.) | 🟡 Low |
