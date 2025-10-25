<h1 align="center">
  💰 CashApp - Aplikasi Kasir Sederhana
</h1>

<p align="center">
  Sistem Kasir berbasis Web menggunakan PHP Native + MySQL
</p>

---

## 📌 Fitur Utama

✅ Login & Manajemen Pengguna  
✅ CRUD Produk  
✅ Input Satuan & Kategori sudah tersedia  
✅ Kasir & Cart dalam satu halaman  
✅ Pencarian Produk (Auto Suggest)  
✅ Transaksi otomatis update stok  
✅ Laporan Penjualan + Export ke PDF & Excel  
✅ Hitung kembalian otomatis  
✅ Keuntungan tercatat pada detail transaksi  
✅ Dashboard & Grafik Laporan  

---

## 🛠️ Teknologi

| Teknologi | Digunakan Untuk |
|----------|----------------|
| PHP Native | Logic Backend |
| MySQL | Database |
| Bootstrap 5 | UI Styling |
| SweetAlert2 | Notifikasi |
| DOMPDF | Export PDF |
| PhpSpreadsheet | Export Excel |

---

## 🧩 Database

Database bernama: **`cashapp_db`**

📌 Import file SQL untuk membuat struktur database:

(`sql/create_database.sql`)

Tabel:

| Tabel | Fungsi |
|------|--------|
| users | Login |
| produk | Data barang |
| jual | Header transaksi |
| rinci_jual | Detail transaksi |
| laporan | Laporan Penjualan |

---

## 🚀 Cara Install & Jalankan

1️⃣ Clone / Download repository  
```bash
https://github.com/pangeran-droid/CashApp.git
cd CashApp
```

2️⃣ Pasang Composer dependency (untuk export):
```bash
composer install
```

3️⃣ Import database:
```bash
- Buka phpMyAdmin → Import `create_database.sql`
```

4️⃣ Sesuaikan **koneksi.php**
```bash
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "cashapp_db";
```

5️⃣ Jalankan melalui browser:
```bash
http://localhost/CashApp
```

---

## 📦 Export Tools

| File | Fungsi |
|------|--------|
| export_laporan_pdf.php | Export laporan ke PDF |
| export_laporan_excel.php | Download Excel |

---

## 📝 Lisensi

Proyek ini menggunakan lisensi **MIT**  
Bebas digunakan untuk apa saja ✅

---
