-- ============================================================
-- KostKu - Database Schema
-- Jalankan file ini di MySQL/phpMyAdmin
-- ============================================================

CREATE DATABASE IF NOT EXISTS kost_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kost_db;

-- ─── USERS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    avatar VARCHAR(255),
    nik VARCHAR(16),
    gender ENUM('Laki-laki','Perempuan'),
    address TEXT,
    dob DATE,
    occupation VARCHAR(100),
    status ENUM('active','inactive') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── KOSTS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS kosts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    type ENUM('Putra','Putri','Campur') NOT NULL,
    price_monthly DECIMAL(12,2) NOT NULL,
    price_yearly DECIMAL(12,2),
    total_rooms INT NOT NULL DEFAULT 0,
    available_rooms INT NOT NULL DEFAULT 0,
    facilities JSON,
    shared_facilities JSON,
    owner_name VARCHAR(255) NOT NULL,
    owner_phone VARCHAR(20) NOT NULL,
    thumbnail VARCHAR(255),
    video_tour VARCHAR(255),
    status ENUM('active','inactive','full') DEFAULT 'active',
    is_featured TINYINT(1) DEFAULT 0,
    min_stay INT DEFAULT 1,
    rules JSON,
    entry_time TIME,
    exit_time TIME,
    allow_cooking TINYINT(1) DEFAULT 0,
    allow_pets TINYINT(1) DEFAULT 0,
    allow_guest TINYINT(1) DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── ROOMS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kost_id BIGINT UNSIGNED NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    floor INT DEFAULT 1,
    size DECIMAL(6,2),
    price DECIMAL(12,2) NOT NULL,
    status ENUM('available','occupied','maintenance') DEFAULT 'available',
    description TEXT,
    facilities JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kost_id) REFERENCES kosts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── KOST PHOTOS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS kost_photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kost_id BIGINT UNSIGNED NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    type ENUM('exterior','interior','bathroom','kitchen','room','other') DEFAULT 'other',
    is_primary TINYINT(1) DEFAULT 0,
    `order` INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kost_id) REFERENCES kosts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── BOOKINGS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    kost_id BIGINT UNSIGNED NOT NULL,
    room_id BIGINT UNSIGNED NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE,
    duration_months INT NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    deposit DECIMAL(12,2) DEFAULT 0,
    payment_status ENUM('pending','paid','partial','refunded') DEFAULT 'pending',
    booking_status ENUM('pending','confirmed','active','completed','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_proof VARCHAR(255),
    notes TEXT,
    special_requests TEXT,
    paid_at TIMESTAMP NULL,
    confirmed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kost_id) REFERENCES kosts(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── REVIEWS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    kost_id BIGINT UNSIGNED NOT NULL,
    booking_id BIGINT UNSIGNED NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NOT NULL,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kost_id) REFERENCES kosts(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── NOTIFICATIONS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── SESSIONS (Laravel) ──────────────────────────────────
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── CACHE ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────
-- SAMPLE DATA
-- ─────────────────────────────────────────────────────────

-- Admin user (password: "password")
INSERT INTO users (name, email, phone, password, role, gender, occupation, status) VALUES
('Admin KostKu', 'admin@kostku.id', '08001234567',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Laki-laki', 'Administrator', 'active');

-- Regular users
INSERT INTO users (name, email, phone, password, role, gender, occupation, status) VALUES
('Budi Santoso',  'user@kostku.id',   '08111111111', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Laki-laki',  'Mahasiswa',       'active'),
('Sari Dewi',     'sari@email.com',   '08222222222', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Perempuan',  'Karyawan Swasta', 'active'),
('Ahmad Fauzi',   'ahmad@email.com',  '08333333333', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Laki-laki',  'Freelancer',      'active');

-- Sample Kost
INSERT INTO kosts (name, slug, description, address, city, province, postal_code, type, price_monthly, price_yearly,
                   total_rooms, available_rooms, facilities, shared_facilities, owner_name, owner_phone,
                   status, is_featured, min_stay, allow_cooking, allow_pets, allow_guest,
                   entry_time, exit_time, rules, created_by) VALUES

('Kost Melati Indah', 'kost-melati-indah-abc123',
 'Kost nyaman dan bersih di lokasi strategis dekat kampus. Lingkungan aman dengan keamanan 24 jam.',
 'Jl. Margonda Raya No. 45', 'Depok', 'Jawa Barat', '16424',
 'Putri', 1200000, 12000000, 20, 5,
 '["AC","Kasur","Lemari","Meja Belajar","WiFi","Kamar Mandi Dalam"]',
 '["Dapur Bersama","Parkir Motor","CCTV","Laundry"]',
 'Bu Siti Rahayu', '08123456789', 'active', 1, 1, 1, 0, 1,
 '06:00:00', '22:00:00',
 '["Jam malam pukul 22.00","Dilarang merokok di dalam kamar","Wajib menjaga kebersihan"]',
 1),

('Kost Mahkota Jaya', 'kost-mahkota-jaya-def456',
 'Kost eksklusif dengan fasilitas lengkap di jantung kota Jakarta. AC, WiFi cepat, kamar mandi dalam.',
 'Jl. Sudirman No. 88, Karet Tengsin', 'Jakarta', 'DKI Jakarta', '10250',
 'Campur', 2500000, 25000000, 30, 8,
 '["AC","Kasur","Lemari","TV","WiFi","Kamar Mandi Dalam","Water Heater","Kulkas Mini"]',
 '["Parkir Motor","Parkir Mobil","CCTV","Satpam","Gym","Laundry"]',
 'Pak Hendra Gunawan', '08234567890', 'active', 1, 3, 0, 0, 1,
 '07:00:00', '23:00:00',
 '["Tamu diizinkan hingga pukul 22.00","Dilarang merokok di area kost"]',
 1),

('Kost Green Garden', 'kost-green-garden-ghi789',
 'Kost dengan konsep taman hijau yang asri. Suasana nyaman dan segar di Bandung.',
 'Jl. Buah Batu No. 210, Lengkong', 'Bandung', 'Jawa Barat', '40265',
 'Campur', 1500000, 15000000, 25, 10,
 '["AC","Kasur","Lemari","WiFi","Kamar Mandi Dalam","Balkon"]',
 '["Taman","Parkir Motor","Laundry","Dapur Bersama","CCTV"]',
 'Bu Ratna Dewi', '08456789012', 'active', 1, 3, 1, 1, 1,
 '06:00:00', '23:00:00',
 '["Jaga kebersihan taman","Hewan peliharaan harus divaksin"]',
 1),

('Kost Putra Mandiri', 'kost-putra-mandiri-jkl012',
 'Kost khusus putra dengan harga terjangkau dekat kampus UGM Yogyakarta.',
 'Jl. Kaliurang KM 5, Sleman', 'Yogyakarta', 'DI Yogyakarta', '55281',
 'Putra', 800000, 8400000, 15, 3,
 '["Kipas Angin","Kasur","Lemari","Meja Belajar","WiFi"]',
 '["Dapur Bersama","Parkir Motor","Mushola","Jemuran"]',
 'Pak Bambang Susilo', '08345678901', 'active', 0, 1, 1, 0, 0,
 '06:00:00', '21:00:00',
 '["Jam malam pukul 21.00","Wajib menjaga kebersihan bersama"]',
 1);

-- Sample Rooms for first kost
INSERT INTO rooms (kost_id, room_number, floor, size, price, status, facilities) VALUES
(1, 'A1', 1, 18.0, 1200000, 'available', '["AC","Kasur","Lemari","WiFi"]'),
(1, 'A2', 1, 18.0, 1200000, 'available', '["AC","Kasur","Lemari","WiFi"]'),
(1, 'B1', 2, 20.0, 1300000, 'occupied',  '["AC","Kasur","Lemari","WiFi","Meja Belajar"]'),
(1, 'B2', 2, 20.0, 1300000, 'available', '["AC","Kasur","Lemari","WiFi","Meja Belajar"]'),
(2, 'A1', 1, 25.0, 2500000, 'available', '["AC","Kasur","Lemari","TV","WiFi","Kamar Mandi Dalam"]'),
(2, 'A2', 1, 25.0, 2500000, 'occupied',  '["AC","Kasur","Lemari","TV","WiFi","Kamar Mandi Dalam"]');

-- Sample Booking
INSERT INTO bookings (booking_code, user_id, kost_id, room_id, check_in_date, duration_months,
                      total_price, deposit, payment_status, booking_status, payment_method, paid_at, confirmed_at) VALUES
('BK-SAMPLE01', 2, 1, 1, '2025-01-01', 6, 7200000, 1200000, 'paid', 'active', 'transfer_bca', NOW(), NOW()),
('BK-SAMPLE02', 3, 2, 5, '2025-02-01', 3, 7500000, 2500000, 'paid', 'completed', 'gopay', NOW(), NOW());

-- Sample Reviews
INSERT INTO reviews (user_id, kost_id, booking_id, rating, comment, is_approved) VALUES
(2, 1, 1, 5, 'Kost sangat nyaman dan bersih! Pemilik ramah dan responsif. WiFi kencang, AC dingin. Sangat direkomendasikan!', 1),
(3, 2, 2, 4, 'Lokasi strategis di Jakarta. Fasilitas lengkap walau harga agak mahal. Overall puas dengan pelayanannya.', 1);

SELECT 'Database KostKu berhasil dibuat!' AS status;
