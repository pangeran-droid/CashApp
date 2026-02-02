<div align="center">

# CashApp - Simple POS System

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-777bb4?style=for-the-badge&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479a1?style=for-the-badge&logo=mysql)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

**CashApp** is a web-based cashier application developed using **PHP Native** and **MySQL**. 
It provides an efficient workflow for small businesses to manage inventory and sales.

</div>

---

## Key Features

**User Authentication** – Secure login and user role management.
**Product CRUD** – Create, Read, Update, and Delete products easily.
**Unit & Category Management** – Organized product classification.
**Unified POS Interface** – Handle transactions and shopping carts on a single page.
**Live Product Search** – Fast searching with Auto-Suggest functionality.
**Auto Stock Sync** – Inventory levels update automatically after every transaction.
**Financial Reporting** – Detailed sales reports with **PDF & Excel Export** support.
**Automated Change Calculator** – Minimize human error during payments.
**Profit Tracking** – Profits are recorded and visible in transaction details.
**Analytics Dashboard** – Visualize sales data with interactive charts.

---

## Tech Stack

| Technology | Purpose |
|----------|----------------|
| **PHP Native** | Backend Logic |
| **MySQL** | Database Management |
| **Bootstrap 5** | Responsive UI Styling |
| **SweetAlert2** | Interactive Notifications |
| **DOMPDF** | PDF Report Generation |
| **PhpSpreadsheet** | Excel Export Engine |

---

## Database Schema

Database Name: **`cashapp_db`**

To set up the structure, import the SQL file located at: `sql/create_database.sql`

| Table | Description |
|------|--------|
| `users` | Handles authentication and user data |
| `produk` | Inventory/Product information |
| `jual` | Transaction headers (date, total, etc.) |
| `rinci_jual` | Transaction details (items sold) |
| `laporan` | Compiled sales data for reporting |

---

## Installation Guide

**Clone the repository**
```bash
git clone https://github.com/pangeran-droid/CashApp.git
cd CashApp
```

**Install Composer dependencies** (Required for Exports)
```bash
composer install
```

**Import the Database**
- Open **phpMyAdmin**.
- Create a new database named `cashapp_db`.
- Import the `sql/create_database.sql` file.

**Configure Database Connection** Edit `koneksi.php` to match your local environment:
```php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "cashapp_db";
```

**Launch the App** Open your browser and navigate to:
```bash
http://localhost/CashApp
```

---

## Export Tools

| File | Function |
|------|--------|
| export_laporan_pdf.php | Generates a professional PDF report |
| export_laporan_excel.php | Downloads sales data in .xlsx format |

---

## Lisensi
**MIT**  

---
