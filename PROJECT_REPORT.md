# ACADEMIC PROJECT REPORT

## PROJECT TITLE:
### **JEWELTRACK — JEWELLERY INVENTORY & STOCK MANAGEMENT SYSTEM**

---

### **TABLE OF CONTENTS**
1. [Abstract](#1-abstract)
2. [Introduction & Problem Statement](#2-introduction--problem-statement)
3. [System Analysis & Requirements](#3-system-analysis--requirements)
   - 3.1 Functional Requirements
   - 3.2 Non-Functional Requirements
   - 3.3 Hardware & Software Specifications
4. [System Architecture & Design](#4-system-architecture--design)
   - 4.1 System Flow Architecture
   - 4.2 Database Design & Entity Relationship
   - 4.3 Data Dictionary
5. [Module Implementation & Features](#5-module-implementation--features)
   - 5.1 Authentication & Role-Based Access Control
   - 5.2 Interactive Dashboard & Live Bullion Reference
   - 5.3 Jewellery Product & Category Management
   - 5.4 Stock Ledger & Inventory Audit Trail
   - 5.5 Purchasing & Supplier Management
   - 5.6 Point of Sale (POS) & Tax Invoicing
   - 5.7 Customer Management (CRM)
   - 5.8 Business Reports & Analytics
   - 5.9 Administration & System Settings
6. [Security Implementation](#6-security-implementation)
7. [Testing & Validation](#7-testing--validation)
8. [Conclusion & Future Scope](#8-conclusion--future-scope)

---

### **1. ABSTRACT**

**JewelTrack** is a web-based Jewellery Stock & Inventory Management System engineered to automate retail and wholesale jewellery operations. Unlike generic retail inventory systems, jewellery shops require precision tracking of metal types (Gold, Silver, Platinum), purity (24K, 22K, 18K, 925), gross weight, net weight, wastage/making charges, and fluctuating bullion prices.

JewelTrack delivers an integrated platform that handles product cataloging, stock adjustment auditing, vendor purchases, customer billing with GST tax computation, printable invoices, customer lifetime metrics, and analytical business reports. Developed using **PHP 8**, **MySQL (InnoDB Engine)**, **Bootstrap 5**, and responsive web design, the system provides high performance, transaction integrity, and an intuitive user interface tailored for jewellery businesses.

---

### **2. INTRODUCTION & PROBLEM STATEMENT**

#### 2.1 Background
Jewellery represents high-value, weight-sensitive inventory. Traditional jewellery shops in India and worldwide often rely on physical ledgers or fragmented desktop software. This creates severe operational bottlenecks:
- Inaccurate stock reconciliation and risk of unaccounted shrinkage.
- Lack of audit trails when quantities are altered.
- Tedious manual billing calculations for gross weight, stone weight, making charges, and tax.
- Inability to track supplier purchase histories and customer buying habits.

#### 2.2 Proposed Solution
JewelTrack solves these challenges by implementing an automated, transactional web application:
- Every stock change is backed by an immutable ledger entry.
- Fast Point of Sale (POS) billing with instant auto-calculation of totals, discounts, and GST.
- Print-ready tax invoices formatted for both standard A4 printers and thermal receipt printers.
- Real-time dashboard indicators showcasing live inventory valuation and low-stock alerts.

---

### **3. SYSTEM ANALYSIS & REQUIREMENTS**

#### 3.1 Functional Requirements
1. **User Authentication**: Secure login/logout with session management, role enforcement (`admin` vs `staff`), and account lockout protection.
2. **Product Management**: Ability to add, edit, view, and soft-delete jewellery items with attributes like SKU, category, metal type, purity, gross weight, net weight, cost, and selling price.
3. **Stock Control**: Automated stock decrement on sales, increment on purchases, and manual adjustments with mandatory audit comments.
4. **Billing & Invoicing**: Multi-item checkout form, automatic line-item computation, tax/GST addition, payment method tagging, and invoice generation.
5. **Procurement**: Supplier directory and purchase order recording with cost tracking.
6. **Reporting**: Date-range filtering for revenue, estimated gross margins, top-selling items, payment breakdowns, and low-stock alerts.

#### 3.2 Non-Functional Requirements
- **Security**: Robust defense against SQL Injection (PDO), Cross-Site Scripting (XSS), and Cross-Site Request Forgery (CSRF).
- **Usability**: Responsive, dark/gold luxury theme tailored for jewellery aesthetics with clear visual cues and feedback.
- **Reliability & Consistency**: Database ACID transaction handling ensuring zero partial records on checkout failures.
- **Maintainability**: Modular PHP structure separating configuration, layout templates, utility functions, and business controllers.

#### 3.3 Hardware & Software Specifications
- **Operating System**: Windows 10/11, Linux (Ubuntu/Debian), macOS.
- **Web Server**: Apache 2.4+ (XAMPP / WampServer / LAMP) or Nginx.
- **PHP Version**: PHP 8.0 or higher.
- **Database**: MySQL 8.0+ or MariaDB 10.4+.
- **Client**: Any modern web browser (Google Chrome, Microsoft Edge, Mozilla Firefox, Safari).

---

### **4. SYSTEM ARCHITECTURE & DESIGN**

#### 4.1 System Flow Architecture
```
[ Client Browser ]
        │  (HTTP / HTTPS Requests)
        ▼
[ Apache Web Server / PHP Runtime ]
        │
   ┌────┴─────────────────────────────┐
   │  - auth.php (Session & Roles)    │
   │  - validation.php (Input Checks) │
   │  - functions.php (Utilities)     │
   │  - modules (CRUD & Business)     │
   └────┬─────────────────────────────┘
        │  (PDO Prepared Statements)
        ▼
[ MySQL Database (InnoDB Engine) ]
   ├── users
   ├── categories
   ├── products
   ├── suppliers
   ├── customers
   ├── purchases & purchase_items
   ├── sales & sale_items
   ├── stock_movements (Audit Ledger)
   ├── settings
   └── activity_logs
```

#### 4.2 Database Relational Schema
The database employs 10 relational tables utilizing foreign keys with `ON DELETE CASCADE` or `ON DELETE SET NULL` constraints to maintain strict referential integrity.

1. **`users`**: User authentication credentials, names, roles, and status.
2. **`categories`**: Jewellery classification (Rings, Necklaces, Bangles, Earrings, etc.).
3. **`products`**: Product catalogue with weights, prices, stock levels, and SKUs.
4. **`suppliers`**: Vendor contact details and business information.
5. **`customers`**: Customer database for POS association and billing history.
6. **`purchases`**: Inward purchase headers (supplier, invoice number, totals, tax).
7. **`purchase_items`**: Line items for each purchase order.
8. **`sales`**: Outward sales transactions (customer, invoice number, payment method, grand total).
9. **`sale_items`**: Line items for each invoice (product, quantity, unit price, total).
10. **`stock_movements`**: Immutable double-entry style stock ledger recording `qty_before`, `quantity`, and `qty_after`.
11. **`settings`**: Store profile, tax rates, currency, and bullion rates.
12. **`activity_logs`**: System event tracking for auditing administrative actions.

---

### **5. MODULE IMPLEMENTATION & FEATURES**

#### 5.1 Authentication & Security (`login.php`, `includes/auth.php`)
- Passwords stored using industry-standard `password_hash()` with `PASSWORD_DEFAULT` (bcrypt).
- Session fixation protection with `session_regenerate_id()`.
- Brute-force mitigation tracking failed attempts with automatic 15-minute temporary lockout.

#### 5.2 Dashboard (`dashboard.php`)
- Displays real-time KPIs: Today's Sales, Month-to-date Revenue, Total SKUs, and Items at/below Reorder Level.
- Gold & Silver live bullion price ticker banner.
- Recent sales and purchases quick tables with direct links to view or print invoices.

#### 5.3 Products & Categories (`modules/products/`, `modules/categories/`)
- Unified SKU code generation preventing duplicate inventory tags.
- Detailed weight tracking: Gross weight and Net weight in grams (up to 3 decimal places: `0.000g`).
- Low-stock threshold indicator (`reorder_level`) highlighting items requiring replenishment.

#### 5.4 Stock Ledger & Movements (`modules/stock/`)
- Audit log tracking movement types: `opening_stock`, `purchase_received`, `sale_dispatched`, and `manual_correction`.
- Manual adjustment form requiring reason documentation for damaged, lost, or audited items.

#### 5.5 Purchasing & Suppliers (`modules/purchases/`, `modules/suppliers/`)
- Add purchase orders with dynamic product lines, unit cost inputs, and supplier association.
- Automatic inventory increment upon purchase confirmation inside database transactions.

#### 5.6 Point of Sale & Invoicing (`modules/sales/`)
- Fast POS billing screen supporting both registered customers and walk-in clients.
- Dynamic line item addition, real-time recalculation of subtotals, discounts, and GST.
- Clean printable invoice (`invoice.php`) featuring store logo, customer details, itemized jewellery breakdown, GST summary, and store terms.

#### 5.7 Reports & Analytics (`modules/reports/`)
- Date-range filterable revenue and estimated gross profit analysis.
- Top-selling jewellery pieces ranked by quantity and sales volume.
- Category breakdown analyzing which segments (e.g., Rings vs Necklaces) drive the highest turnover.
- One-click print support with dedicated `@media print` CSS rules hiding navigation elements.

#### 5.8 System Settings & Administration (`modules/settings/`, `modules/users/`)
- Store settings: Store name, contact number, store address, GSTIN, currency symbol, and invoice footer disclaimer.
- Staff user management: Add new staff accounts, set roles (`admin` or `staff`), and toggle active status.

---

### **6. SECURITY IMPLEMENTATION**

| Security Threat | Mitigation Strategy Applied in JewelTrack |
|---|---|
| **SQL Injection (SQLi)** | 100% of database queries use PDO Prepared Statements with bound parameters. |
| **Cross-Site Scripting (XSS)** | All user outputs passed through `e()` helper function utilizing `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. |
| **Cross-Site Request Forgery (CSRF)** | Synchronizer token pattern implemented across all POST forms via `csrfField()` and `verifyCsrf()`. |
| **Session Hijacking** | Secure session initialization with `httponly` and `samesite=Lax` cookies, with session regeneration upon authentication. |
| **Unauthorized Access** | Route-level guard functions (`requireLogin()`, `requireAdmin()`) halting execution on unauthorized attempts. |

---

### **7. TESTING & VALIDATION**

- **Unit & Logic Validation**: Form validation class (`Validator`) thoroughly tests required fields, valid email structures, positive numeric values, and weight formats.
- **Transaction Rollback Testing**: Simulated failures during sales checkout verified that inventory quantities and sale records are cleanly rolled back without orphaned data.
- **Cross-Browser Verification**: Verified responsive layout and styling across Google Chrome, Mozilla Firefox, and Microsoft Edge.

---

### **8. CONCLUSION & FUTURE SCOPE**

#### 8.1 Conclusion
The **JewelTrack — Jewellery Inventory & Stock Management System** successfully fulfills all objectives set forth for modern jewellery management. By combining strict inventory auditing, intuitive POS billing, comprehensive reporting, and enterprise-grade PHP/MySQL security standards, it provides a dependable, scalable solution for jewellery retailers.

#### 8.2 Future Scope
- Integration with barcode and RFID tag scanners for instant item scanning at the sales counter.
- Automated SMS / WhatsApp notification integration for sending digital invoice links to customers.
- Integration with live bullion market APIs (e.g., Metals-API) for real-time automatic rate updates.
- Multi-branch inventory transfers for retail chains.

---
**Academic Project Submission — JewelTrack System**
