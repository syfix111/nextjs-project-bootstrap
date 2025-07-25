-- Database: village_system
-- Created for Village Information System

CREATE DATABASE IF NOT EXISTS village_system;
USE village_system;

-- Table structure for table admin
CREATE TABLE admin (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  role enum('admin','operator') NOT NULL DEFAULT 'operator',
  nama varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table warga
CREATE TABLE warga (
  id int(11) NOT NULL AUTO_INCREMENT,
  nama varchar(255) NOT NULL,
  nik varchar(20) NOT NULL,
  alamat text,
  kategori varchar(100) DEFAULT NULL,
  status_bantuan enum('Sudah','Belum') NOT NULL DEFAULT 'Belum',
  tanggal_bantuan date DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY nik (nik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin account
-- Password: admin123 (hashed)
INSERT INTO admin (email, password, role, nama) VALUES
('admin@desa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator');

-- Insert sample data for warga
INSERT INTO warga (nama, nik, alamat, kategori, status_bantuan, tanggal_bantuan) VALUES
('Ahmad Suryanto', '3201012345678901', 'Jl. Merdeka No. 12, RT 01/RW 02', 'Miskin', 'Sudah', '2024-01-15'),
('Siti Nurhaliza', '3201012345678902', 'Jl. Kemerdekaan No. 25, RT 02/RW 03', 'Fakir Miskin', 'Belum', NULL),
('Budi Santoso', '3201012345678903', 'Jl. Pahlawan No. 8, RT 03/RW 01', 'Miskin', 'Sudah', '2024-02-10'),
('Dewi Sartika', '3201012345678904', 'Jl. Diponegoro No. 15, RT 01/RW 04', 'Mampu', 'Belum', NULL),
('Rudi Hermawan', '3201012345678905', 'Jl. Sudirman No. 30, RT 04/RW 02', 'Fakir Miskin', 'Sudah', '2024-01-20');
