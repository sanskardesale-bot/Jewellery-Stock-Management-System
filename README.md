# JewelTrack — Jewellery Inventory & Stock Management System

JewelTrack is a specialized, production-ready web application built with **PHP (Procedural & OOP helpers)**, **MySQL / MariaDB**, and modern **Bootstrap 5 + Vanilla CSS/JS**. It is crafted specifically for jewellery retailers, bullion merchants, and goldsmiths to manage daily stock movements, purchases, customer billing/invoicing, supplier ledgers, and inventory valuation.

---

## 💎 Features at a Glance

- **Modern Luxury Dashboard**: Live bullion rates ticker (24K Gold, 22K Gold, Silver), financial KPIs, stock valuation, inventory health indicators, and recent invoices.
- **Jewellery Inventory Management**: SKU-based tracking, metal types (Gold, Silver, Platinum), purity (22K, 18K, 925), gross weight, net weight, purchase costs, and selling margins.
- **Stock Movements & Audit Trail**: Real-time logging of stock adjustments, opening stock, purchase receipts, and sales dispatches with before/after quantity balances.
- **Purchases & Supplier Management**: Purchase orders with line items, tax, discounts, supplier contact directories, and outstanding ledger tracking.
- **Sales & Point of Sale (POS)**: Fast checkout form with live customer selection, product auto-fill, tax/GST calculation, discount management, and printable thermal/A4 tax invoices.
- **Customer CRM**: Customer purchase histories, contact information, notes, and lifetime order values.
- **Reports & Analytics**: Date-range filtered sales reports, top-selling jewellery items, category revenue distribution, payment method breakdowns, and low-stock alerts.
- **Administration & Security**:
  - Secure role-based access control (`admin` vs `staff`).
  - `password_hash()` (bcrypt) authentication.
  - CSRF token validation on all mutating POST requests.
  - PDO Prepared Statements protecting against SQL Injection.
  - Output sanitization (`htmlspecialchars`) guarding against XSS.
  - Session timeout and brute-force login lockout handling.

---

## 🛠️ System Requirements

- **PHP**: 8.0 or higher (with `pdo_mysql`, `mbstring`, `fileinfo` enabled)
- **Database**: MySQL 8.0+ or MariaDB 10.4+
- **Web Server**: Apache (XAMPP / WampServer / LAMP) or Nginx
- **Browser**: Any modern browser (Chrome, Edge, Firefox, Safari)

---

## 🚀 Quick Setup & Installation Guide

### Step 1: Place Files in Web Root
If you are using **XAMPP**:
1. Copy the `jeweltrack` folder into your XAMPP web directory:
   ```
   C:\xampp\htdocs\jeweltrack
   ```

### Step 2: Create Database & Import SQL
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin/`) or MySQL CLI.
2. Create a new database named `jeweltrack_db` with `utf8mb4_unicode_ci` collation.
3. Import the SQL file located at:
   ```
   jeweltrack/database/jeweltrack_db.sql
   ```
   *(This initializes all 9 tables, indexes, constraints, and complete sample jewellery seed data).*

### Step 3: Configure Database Connection (If Required)
Open `jeweltrack/config/database.php` and verify your MySQL credentials:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'jeweltrack_db');
define('DB_USER', 'root');
define('DB_PASS', '');       // Default is blank in XAMPP
```

### Step 4: Run the Application
Open your browser and navigate to:
```
http://localhost/jeweltrack/
```

---

## 🔐 Default Login Credentials

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Administrator** | `admin@jeweltrack.com` | `Admin@123` | Full access (Users, Settings, Deletion, Reports) |
| **Sales Staff** | `priya@jeweltrack.com` | `Staff@123` | Sales, Purchases, Inventory, Customers |
| **Sales Staff** | `raj@jeweltrack.com` | `Staff@123` | Sales, Purchases, Inventory, Customers |

---

## 📁 Project Architecture & Directory Structure

```
jeweltrack/
├── assets/
│   ├── css/
│   │   └── style.css            # Custom luxury dark-accented gold design system & print styles
│   ├── js/
│   │   └── app.js               # Dynamic computations, POS logic, confirm modals, tickers
│   └── uploads/
│       └── products/            # Product image attachments
├── config/
│   ├── app.php                  # Global application constants, session, paths
│   └── database.php             # PDO database connection with error handling
├── database/
│   └── jeweltrack_db.sql        # Database schema, foreign keys, & sample inventory data
├── includes/
│   ├── ajax/
│   │   └── product_search.php   # Real-time search endpoint for products
│   ├── auth.php                 # Session checks, role enforcement, CSRF protection
│   ├── footer.php               # Shared footer scripts & layout close
│   ├── functions.php            # Helper utilities (formatMoney, getSetting, logActivity, etc.)
│   ├── header.php               # Responsive sidebar, navigation, flash messaging, user bar
│   └── validation.php           # Server-side validation class (rules, error bags)
├── modules/
│   ├── categories/
│   │   └── index.php            # Category management (add, edit, list)
│   ├── customers/
│   │   ├── index.php            # Customer listing with search & pagination
│   │   ├── create.php           # Add new customer
│   │   ├── edit.php             # Edit customer information
│   │   └── view.php             # Customer profile & transaction history
│   ├── products/
│   │   ├── index.php            # Inventory catalog, filters, low-stock badges
│   │   ├── create.php           # Add new jewellery item with purity & weights
│   │   ├── edit.php             # Edit product attributes and prices
│   │   ├── view.php             # Detailed product card with stock movement history
│   │   └── delete.php           # Safe soft-delete / remove product
│   ├── purchases/
│   │   ├── index.php            # Purchase order history
│   │   ├── create.php           # New purchase entry with dynamic line items
│   │   └── view.php             # Purchase receipt detail
│   ├── reports/
│   │   └── index.php            # Date-filtered sales, top products, valuation, printable reports
│   ├── sales/
│   │   ├── index.php            # Sales invoices log
│   │   ├── create.php           # POS billing terminal with instant total calculation
│   │   ├── view.php             # Sales order overview
│   │   └── invoice.php          # Clean, printable Tax Invoice (A4 & thermal ready)
│   ├── settings/
│   │   └── index.php            # Store profile, GST rates, bullion rates, invoice footer
│   ├── stock/
│   │   ├── index.php            # Stock movement log (audit trail)
│   │   └── adjust.php           # Manual stock adjustment (breakage, audit, correction)
│   ├── suppliers/
│   │   ├── index.php            # Supplier directory
│   │   ├── create.php           # Add supplier
│   │   ├── edit.php             # Edit supplier details
│   │   └── view.php             # Supplier details & purchase log
│   └── users/
│       └── index.php            # Staff management & status toggle (Admin only)
├── dashboard.php                # Analytical dashboard with metrics & charts
├── index.php                    # Route entry point
├── login.php                    # Secure authentication page
└── logout.php                   # Secure session termination
```

---

## 🛡️ Academic & Technical Highlights

1. **Prepared Statements Everywhere**: Zero raw SQL concatenation. All parameters bound safely through PDO.
2. **Double-entry style Stock Audit**: No product quantity is modified without creating a corresponding immutable record in `stock_movements`.
3. **Transaction Safety**: All multi-table operations (such as sales billing which affects `sales`, `sale_items`, `products`, and `stock_movements`) are wrapped within MySQL InnoDB ACID transactions (`beginTransaction()`, `commit()`, `rollBack()`).
4. **Clean Code & Modularity**: Separation of database configuration, helper functions, page headers, footers, and business logic modules.
