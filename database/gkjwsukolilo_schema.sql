-- ==========================================================
-- FILE SCHEMA & SEEDER DATABASE: db_gkjwsukolilo
-- Dibuat untuk Sistem Portal & E-Voting Majelis GKJW Sukolilo
-- ==========================================================

-- Pastikan menggunakan database yang tepat (Opsional jika di phpMyAdmin)
-- CREATE DATABASE IF NOT EXISTS db_gkjwsukolilo;
-- USE db_gkjwsukolilo;

-- ==========================================================
-- 1. HAPUS TABEL LAMA (Jika ada) AGAR TIDAK BENTROK
-- ==========================================================
DROP TABLE IF EXISTS pemilih;
DROP TABLE IF EXISTS kandidat_majelis;
DROP TABLE IF EXISTS warta_jemaat;
DROP TABLE IF EXISTS konten_web;
DROP TABLE IF EXISTS admin;

-- ==========================================================
-- 2. PEMBUATAN TABEL (SCHEMA)
-- ==========================================================

-- Tabel Admin
CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Tabel Konten Dinamis (YouTube, Renungan, Sejarah, Info, Status Fase)
CREATE TABLE konten_web (
    id_konten INT AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR(50) NOT NULL,
    isi_konten TEXT NOT NULL,
    diupdate_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Warta Jemaat (Untuk PDF)
CREATE TABLE warta_jemaat (
    id_warta INT AUTO_INCREMENT PRIMARY KEY,
    nama_file VARCHAR(255) NOT NULL,
    tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Kandidat Majelis
CREATE TABLE kandidat_majelis (
    id_kandidat INT AUTO_INCREMENT PRIMARY KEY,
    nama_kandidat VARCHAR(150) NOT NULL,
    foto VARCHAR(255) DEFAULT 'kandidat_kosong.png',
    wilayah ENUM('Wilayah 1', 'Wilayah 2') NOT NULL,
    suara_fase1 INT DEFAULT 0,
    suara_fase2 INT DEFAULT 0,
    lolos_fase2 BOOLEAN DEFAULT FALSE
);

-- Tabel Pemilih (Jemaat yang memiliki hak suara)
CREATE TABLE pemilih (
    id_pemilih INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemilih VARCHAR(150) NOT NULL,
    password_pemilih VARCHAR(255) NOT NULL,
    wilayah ENUM('Wilayah 1', 'Wilayah 2') NOT NULL,
    status_fase1 BOOLEAN DEFAULT FALSE, -- 0 = Belum milih, 1 = Sudah milih Fase 1
    status_fase2 BOOLEAN DEFAULT FALSE  -- 0 = Belum milih, 1 = Sudah milih Fase 2
);

-- ==========================================================
-- 3. INPUT DATA AWAL (SEEDER)
-- ==========================================================

-- Akun Admin Default
INSERT INTO admin (username, password) VALUES 
('admin_gkjw', 'admin123');

-- Data Konten Default (Termasuk saklar "fase_pemilihan")
INSERT INTO konten_web (kategori, isi_konten) VALUES 
('youtube', 'https://www.youtube.com/embed/dQw4w9WgXcQ'),
('renungan_pagi', '<p>Teks renungan pagi default. Silakan edit di panel admin.</p>'),
('renungan_malam', '<p>Teks renungan malam default. Silakan edit di panel admin.</p>'),
('sejarah', '<p>Sejarah GKJW Sukolilo dimulai pada tahun...</p>'),
('info_gereja', '<p>No Telp: 08123456789 <br> Email: sekretariat@gkjwsukolilo.org</p>'),
('fase_pemilihan', 'tutup'); -- Nilai bisa: 'tutup', 'fase1', 'fase2'

-- Data Dummy Kandidat Majelis (Untuk Tes Fase 1)
INSERT INTO kandidat_majelis (nama_kandidat, wilayah) VALUES 
('Bpk. Abraham', 'Wilayah 1'),
('Ibu Sarah', 'Wilayah 1'),
('Sdr. Ishak', 'Wilayah 1'),
('Bpk. Yakub', 'Wilayah 2'),
('Ibu Ribka', 'Wilayah 2'),
('Sdri. Rahel', 'Wilayah 2');

-- Data Dummy Pemilih (Nama untuk Dropdown & Password)
INSERT INTO pemilih (nama_pemilih, password_pemilih, wilayah) VALUES 
('Abe', 'pass123', 'Wilayah 1'),
('Budi Santoso', 'jemaat1', 'Wilayah 1'),
('Siti Aminah', 'jemaat2', 'Wilayah 2'),
('Lukas', 'jemaat3', 'Wilayah 2');