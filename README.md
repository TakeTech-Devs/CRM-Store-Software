# RightAid Homeopathy — ERP System (Store Panel)

A full-stack pharmacy ERP built for homeopathy medicine retail chains. This repository is the **Store Panel** — a Laravel application installed locally at each store, connected to a central Admin Panel through a Sync In / Sync Out architecture.

---

## Architecture Overview

```
┌──────────────────────────────────────────────────────┐
│                   ADMIN PANEL                         │
│          (Central Server — rightaid_admin DB)         │
│   Master data: Products, Brands, Categories, Prices  │
│   Store management, Purchase stock, Analytics        │
└─────────────────────┬────────────────────────────────┘
                      │  Sync In / Sync Out (MySQL over network)
         ┌────────────┴────────────┐
         │                         │
┌────────▼─────────┐   ┌───────────▼──────────┐
│  Store Panel #1  │   │   Store Panel #2      │
│ (Local Laravel)  │   │  (Local Laravel)      │
│ rightaid_store   │   │  rightaid_store       │
└──────────────────┘   └───────────────────────┘
```

### Key Design Principles
- **Store-specific product assignment** — Admin assigns products per store. Each store only sees its allocated stock.
- **Offline-capable** — Full local MySQL database. Billing works without internet; sync when connectivity is available.
- **Delta Sync** — Sync In only pulls records changed since the last sync, not the full dataset.
- **Globally unique IDs** — Customers (`CUS-{storeId}-{seq}`) and staff have chain-wide unique identifiers to prevent conflicts across stores.

---

## Store Panel Features

### Authentication
- Store verification by meta ID and pass key (configured by admin)
- Staff login with session-based auth

### Dashboard
- Today's and yesterday's sales summary
- Zero-stock medicine alerts
- Monthly earnings chart
- Quick navigation to all modules

### Customer Billing
- Search existing customers by phone (searchable select2 dropdown)
- Add new customers inline — phone, name, email, status
- Globally unique customer ID (`CUS-{storeId}-{seq}`)
- Multi-product billing per invoice (regular + inhouse products)
- GST calculation with CGST + SGST split
- Discount per product line, payment type selection
- Auto-generated invoice number
- Doctor name association per bill
- Print invoice: A4 and TVS RP-45 thermal printer formats
- Edit existing bills, date-range filtered billing list

### Staff Billing
- Same billing flow as customer billing
- Staff identified by company-provided Staff ID (`stf_id`)

### Inhouse Products
- Store-manufactured or store-sourced products outside the main catalogue
- Appear as a separate group in the billing dropdown
- Billed and synced to admin like regular products

### Stock Transfer
- Transfer stock between stores in the chain
- Create transfer: select destination store, add product lines with quantities
- Regular and inhouse products shown separately in the product dropdown
- Auto-generated transfer number
- **Sent tab**: shows Pending / Sent status per outgoing transfer
- **Received tab**: shows transfers received from other stores
- Transfer detail modal with full item breakdown
- Status syncs automatically — sending store updates when receiving store syncs

### Reports

| Report | Description |
|---|---|
| Doctor-wise Report | Bills grouped by prescribing doctor |
| GST Report | GST-compliant tax breakdown (CGST/SGST) |
| Cumulative Sales Report | Aggregated sales over a date range |
| Expiry Report | Products approaching or past expiry date |
| Stock Report | Current stock levels per product |
| Analytics Dashboard | Revenue and billing trend charts |

### Sync Engine

**Sync In** (Admin → Store):
- Pulls master data: products, brands, categories, packs, prices, doctors, suppliers
- Only pulls products assigned to this store
- Upserts customers and staff by `(store_id, phone)` composite key
- Applies incoming stock transfers and deducts stock accordingly
- Updates sent transfer statuses (marks as received if admin confirms)
- Incremental delta sync — only changed records since last sync

**Sync Out** (Store → Admin):
- Pushes customer and staff records (upsert by unique ID)
- Pushes customer billing + product billing rows
- Pushes staff billing + product billing rows
- Runs people sync before billing push to ensure FK integrity on admin

### Sync History
Full log of every Sync In and Sync Out with timestamp, type, and status.

### Backup
- On-demand SQL backup of the local database
- Backup file list with delete management

### My Store
View store details: name, address, GSTIN, DL number, helpline number.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 (PHP) |
| Frontend | Blade templates, Bootstrap 4, jQuery |
| UI Components | Select2, SweetAlert2 |
| Database | MySQL — dual connection (local + remote admin) |
| Print | Browser A4 + TVS RP-45 thermal |
| Auth | Laravel session-based |
| Packaging (planned) | Windows .exe installer via Inno Setup |

---

## Database Tables (Store Panel)

| Table | Purpose |
|---|---|
| `store` | Local store info and credentials |
| `customer` | Customer records with global `cus_id` |
| `staff` | Staff records with company `stf_id` |
| `doctor` | Doctors (synced from admin) |
| `brand`, `category`, `sub_category` | Master data (synced) |
| `pack`, `price`, `product` | Product master (synced) |
| `purchase_request` | Assigned stock with available qty tracking |
| `inhouse_product` | Store's own products |
| `customer_billing` | Customer invoice headers |
| `customer_product_billing` | Customer invoice line items |
| `staff_billing` | Staff invoice headers |
| `staff_product_billing` | Staff invoice line items |
| `stock_transfer` | Inter-store transfer headers |
| `stock_transfer_items` | Transfer line items |
| `sync_history` | Sync log |
| `backup` | Backup file records |

---

## Unique ID Formats

| Entity | Format | Example |
|---|---|---|
| Customer | `CUS-{storeId}-{seq}` | CUS-1-00042 |
| Staff | Company-provided | EMP-2024-001 |
| Invoice | `INVC{storeId}{seq}` | INVC001200003 |
| Transfer | `TRF-{storeId}-{seq}` | TRF-1-00001 |

---

## Project Structure

```
app/Http/Controllers/Api/
├── CustomerBilling.php          Customer billing CRUD + customer management
├── StaffBilling.php             Staff billing CRUD + staff management
├── DataController.php           Master data fetch endpoints
├── DataFetchController.php      Sync In / Sync Out engine
├── StockTransferController.php  Stock transfer logic
├── ReportController.php         Reports and analytics
└── LoginController.php          Auth

resources/views/store/
├── dashboard/                   Home dashboard
├── billing/customer/            Customer billing pages
├── billing/staff/               Staff billing pages
├── stockTransfer/               Transfer list and create
├── reports/                     All report pages
├── analytics/                   Analytics dashboard
├── storeSync/                   Sync history and store details
└── backup/                      Backup management
```

---

## Roadmap

- [ ] Admin panel schema updates and feature parity (tracked in `ADMIN_SCHEMA_REQUIREMENTS.md`)
- [ ] Admin billing views for inhouse products
- [ ] Chain-wide analytics on admin panel
- [ ] Windows `.exe` installer for one-click store setup (Inno Setup)
