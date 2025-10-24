-- Database initialization for ShipTrack (Sistem Informasi Manajemen Pengiriman Barang)
CREATE DATABASE IF NOT EXISTS shiptrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shiptrack;

CREATE TABLE IF NOT EXISTS shipments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tracking_number VARCHAR(100) NOT NULL UNIQUE,
  sender_name VARCHAR(200) NOT NULL,
  sender_phone VARCHAR(50),
  receiver_name VARCHAR(200) NOT NULL,
  receiver_phone VARCHAR(50),
  origin VARCHAR(200),
  destination VARCHAR(200),
  weight DECIMAL(8,2) DEFAULT 0.00,
  status ENUM('Draft','Picked Up','In Transit','Delivered','Cancelled') DEFAULT 'Draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
