-- file: sql/create_database.sql
CREATE DATABASE IF NOT EXISTS cashapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE cashapp_db;

-- users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- produk
CREATE TABLE IF NOT EXISTS produk (
  id_produk INT AUTO_INCREMENT PRIMARY KEY,
  kode_produk VARCHAR(50) NOT NULL UNIQUE,
  nama_produk VARCHAR(100) NOT NULL,
  kategori ENUM('Makanan','Minuman','Cemilan') NOT NULL,
  harga_beli DECIMAL(10,2) NOT NULL,
  harga_jual DECIMAL(10,2) NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  satuan ENUM('pcs','paket') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- jual
CREATE TABLE IF NOT EXISTS jual (
  id_jual INT AUTO_INCREMENT PRIMARY KEY,
  no_faktur VARCHAR(50) NOT NULL UNIQUE,
  tanggal_beli DATETIME DEFAULT CURRENT_TIMESTAMP,
  total_belanja DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  total_bayar DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  kembalian DECIMAL(12,2) NOT NULL DEFAULT 0.00
);

-- rinci_jual
CREATE TABLE IF NOT EXISTS rinci_jual (
  id_rinci_jual INT AUTO_INCREMENT PRIMARY KEY,
  no_faktur VARCHAR(50) NOT NULL,
  kode_produk VARCHAR(50) NOT NULL,
  nama_produk VARCHAR(100) NOT NULL,
  harga_modal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  qty INT NOT NULL DEFAULT 1,
  total_harga DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  untung DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  INDEX (no_faktur),
  INDEX (kode_produk)
);
