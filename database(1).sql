-- ============================================================
--  PowerFit Gym — Database Schema (Proper Relational Version)
--  Berdasarkan jurnal: "Rancang Bangun Website Profil Gym
--  Berbasis PHP Native untuk Manajemen Informasi"
--
--  Relasi yang diimplementasikan:
--    - admins       1:N  gym_profile   (admin mengelola profil)
--    - admins       1:N  galleries     (admin mengelola galeri)
--    - admins       1:N  packages      (admin mengelola paket)
--    - admins       1:N  trainers      (admin mengelola trainer)
--    - admins       1:N  testimonials  (admin mengelola testimoni)
--    - admins       1:N  schedules     (admin mengelola jadwal)
--    - trainers     1:N  schedules     (trainer mengajar kelas)
--    - packages     1:N  schedules     (paket memiliki jadwal kelas)
--    - contact_messages (standalone — tidak dikelola relasional)
-- ============================================================

CREATE DATABASE IF NOT EXISTS gym_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gym_db;

-- ── Nonaktifkan FK sementara saat setup ─────────────────────
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
--  1. ADMINS
--     Akun pengelola sistem (admin internal)
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)      NOT NULL UNIQUE,
    password_hash VARCHAR(255)     NOT NULL               COMMENT 'bcrypt hash',
    name          VARCHAR(100)     NOT NULL,
    created_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Akun admin pengelola website';

-- ============================================================
--  2. GYM_PROFILE
--     Profil utama gym — satu baris aktif (singleton)
--     Dikelola oleh admin (created_by / updated_by → admins.id)
-- ============================================================
CREATE TABLE IF NOT EXISTS gym_profile (
    id          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    gym_name    VARCHAR(150)     NOT NULL,
    tagline     VARCHAR(255),
    history     TEXT                                       COMMENT 'Sejarah singkat gym',
    vision      TEXT,
    mission     TEXT,
    address     VARCHAR(255),
    phone       VARCHAR(20),
    email       VARCHAR(100),
    whatsapp    VARCHAR(20),
    maps_embed  TEXT                                       COMMENT 'Embed iframe Google Maps',
    logo        VARCHAR(255)                               COMMENT 'Path file logo',
    created_by  INT UNSIGNED     NOT NULL,
    updated_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                 ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_gymprofile_admin
        FOREIGN KEY (created_by) REFERENCES admins (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Profil utama gym (singleton)';

-- ============================================================
--  3. TRAINERS
--     Data trainer / instruktur
--     Dikelola oleh admin
-- ============================================================
CREATE TABLE IF NOT EXISTS trainers (
    id          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)     NOT NULL,
    photo       VARCHAR(255)                               COMMENT 'Path file foto trainer',
    specialty   VARCHAR(150)                               COMMENT 'Spesialisasi keahlian',
    bio         TEXT,
    instagram   VARCHAR(100),
    is_active   TINYINT(1)       NOT NULL DEFAULT 1,
    created_by  INT UNSIGNED     NOT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                 ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_trainers_admin
        FOREIGN KEY (created_by) REFERENCES admins (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Data trainer dan instruktur';

-- ============================================================
--  4. PACKAGES (Paket Keanggotaan)
--     Daftar paket membership dengan harga dan benefit
--     Dikelola oleh admin
-- ============================================================
CREATE TABLE IF NOT EXISTS packages (
    id            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100)     NOT NULL               COMMENT 'Nama paket (mis. Monthly, Quarterly)',
    duration      VARCHAR(50)      NOT NULL               COMMENT 'Durasi paket (mis. 1 Bulan, 3 Bulan)',
    price         DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
    description   TEXT                                    COMMENT 'Deskripsi singkat paket',
    benefits      TEXT                                    COMMENT 'Daftar keuntungan (JSON array atau teks)',
    display_order SMALLINT         NOT NULL DEFAULT 0     COMMENT 'Urutan tampil di halaman publik',
    is_active     TINYINT(1)       NOT NULL DEFAULT 1,
    created_by    INT UNSIGNED     NOT NULL,
    created_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_packages_admin
        FOREIGN KEY (created_by) REFERENCES admins (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Paket keanggotaan gym';

-- ============================================================
--  5. SCHEDULES (Jadwal Kelas)
--     Jadwal kelas mingguan
--     Berelasi ke: admins, trainers, packages
-- ============================================================
CREATE TABLE IF NOT EXISTS schedules (
    id          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    class_name  VARCHAR(150)     NOT NULL               COMMENT 'Nama kelas (mis. Yoga, HIIT)',
    trainer_id  INT UNSIGNED     NOT NULL               COMMENT 'Trainer pengajar → trainers.id',
    package_id  INT UNSIGNED                            COMMENT 'Paket terkait (opsional) → packages.id',
    day         ENUM('Senin','Selasa','Rabu','Kamis',
                     'Jumat','Sabtu','Minggu') NOT NULL,
    time_start  TIME             NOT NULL,
    time_end    TIME             NOT NULL,
    room        VARCHAR(100)                            COMMENT 'Ruangan / area kelas',
    is_active   TINYINT(1)       NOT NULL DEFAULT 1,
    created_by  INT UNSIGNED     NOT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                 ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_schedules_trainer
        FOREIGN KEY (trainer_id) REFERENCES trainers (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_schedules_package
        FOREIGN KEY (package_id) REFERENCES packages (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_schedules_admin
        FOREIGN KEY (created_by) REFERENCES admins (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Jadwal kelas mingguan';

-- ============================================================
--  6. GALLERIES (Galeri Fasilitas)
--     Foto/gambar fasilitas gym
--     Dikelola oleh admin
-- ============================================================
CREATE TABLE IF NOT EXISTS galleries (
    id            INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(150),
    image         VARCHAR(255)     NOT NULL               COMMENT 'Path file gambar',
    caption       VARCHAR(255),
    category      VARCHAR(100)                            COMMENT 'Kategori (mis. Ruang Utama, Locker)',
    display_order SMALLINT         NOT NULL DEFAULT 0,
    is_active     TINYINT(1)       NOT NULL DEFAULT 1,
    created_by    INT UNSIGNED     NOT NULL,
    created_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_galleries_admin
        FOREIGN KEY (created_by) REFERENCES admins (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Galeri foto fasilitas gym';

-- ============================================================
--  7. TESTIMONIALS (Testimoni Member)
--     Ulasan / testimoni dari member gym
--     Dikelola oleh admin
-- ============================================================
CREATE TABLE IF NOT EXISTS testimonials (
    id          INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    member_name VARCHAR(100)     NOT NULL,
    photo       VARCHAR(255)                            COMMENT 'Path foto member (opsional)',
    quote       TEXT             NOT NULL               COMMENT 'Isi testimoni',
    rating      TINYINT(1)       NOT NULL DEFAULT 5
                                 CHECK (rating BETWEEN 1 AND 5),
    is_active   TINYINT(1)       NOT NULL DEFAULT 1,
    created_by  INT UNSIGNED     NOT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
                                 ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_testimonials_admin
        FOREIGN KEY (created_by) REFERENCES admins (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Testimoni dari member gym';

-- ============================================================
--  8. CONTACT_MESSAGES (Pesan Kontak)
--     Pesan masuk dari pengunjung publik
--     Standalone — tidak perlu FK ke admin
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)     NOT NULL,
    email      VARCHAR(100)     NOT NULL,
    phone      VARCHAR(20),
    message    TEXT             NOT NULL,
    is_read    TINYINT(1)       NOT NULL DEFAULT 0,
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='Pesan masuk dari pengunjung';

-- ── Aktifkan kembali FK ──────────────────────────────────────
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  SEED DATA
-- ============================================================

-- Admin default (password: admin123 — bcrypt hash)
INSERT IGNORE INTO admins (username, password_hash, name) VALUES
('admin',
 '$2y$10$TKh8H1.PfuniQ6wYZhB1me2Fzii3Y5L6u0YKnvtYD9RI0W/9K4lYu',
 'Administrator');

-- Profil gym
INSERT IGNORE INTO gym_profile
    (gym_name, tagline, history, vision, mission,
     address, phone, email, whatsapp, created_by)
VALUES (
    'PowerFit Gym',
    'Transform Your Body, Transform Your Life',
    'PowerFit Gym berdiri sejak 2018 dengan misi memberikan fasilitas kebugaran terbaik.',
    'Menjadi pusat kebugaran pilihan utama di kota.',
    'Memberikan layanan kebugaran profesional yang terjangkau dan berkualitas.',
    'Jl. Sudirman No. 123, Jakarta Selatan',
    '+62 812-3456-7890',
    'info@powerfitgym.com',
    '+62 812-3456-7890',
    1
);

-- Trainers
INSERT IGNORE INTO trainers (id, name, specialty, bio, instagram, is_active, created_by) VALUES
(1, 'Budi Santoso',  'Weight Training & Strength',
    'Berpengalaman 8 tahun di bidang strength training dengan sertifikasi ISSA.',
    'budi_fit',  1, 1),
(2, 'Sari Dewi',     'Yoga & Pilates',
    'Instruktur yoga bersertifikat dari Yoga Alliance, spesialis mindfulness dan fleksibilitas.',
    'sari_yoga', 1, 1),
(3, 'Rina Kusuma',   'HIIT & Cardio',
    'Atlet lari nasional yang kini berfokus melatih program HIIT dan kardio intensif.',
    'rina_hiit', 1, 1),
(4, 'Doni Pratama',  'Nutrition & Body Transformation',
    'Spesialis transformasi tubuh dan nutrisi olahraga dengan ratusan klien sukses.',
    'doni_coach', 1, 1);

-- Paket keanggotaan
INSERT IGNORE INTO packages
    (id, name, duration, price, description, benefits, display_order, is_active, created_by)
VALUES
(1, 'Paket Harian',    '1 Hari',   50000.00,
    'Akses gym untuk 1 hari penuh.',
    '["Akses area utama","Loker gratis"]',
    1, 1, 1),
(2, 'Paket Bulanan',   '1 Bulan',  300000.00,
    'Akses penuh selama 1 bulan termasuk kelas grup.',
    '["Akses area utama","Kelas grup","Loker gratis","Konsultasi trainer 1x"]',
    2, 1, 1),
(3, 'Paket Kuartalan', '3 Bulan',  750000.00,
    'Hemat 17% dibanding paket bulanan.',
    '["Akses area utama","Kelas grup","Loker gratis","Konsultasi trainer 3x","Analisis komposisi tubuh"]',
    3, 1, 1),
(4, 'Paket Tahunan',   '12 Bulan', 2500000.00,
    'Paket terbaik untuk komitmen jangka panjang.',
    '["Akses area utama","Semua kelas grup","Loker gratis","Konsultasi trainer unlimited","Analisis komposisi tubuh","Kaos gym gratis"]',
    4, 1, 1);

-- Jadwal kelas (trainer_id & package_id berelasi ke tabel di atas)
INSERT IGNORE INTO schedules
    (class_name, trainer_id, package_id, day, time_start, time_end, room, is_active, created_by)
VALUES
('Yoga & Mindfulness', 2, 2, 'Senin',  '07:00', '08:00', 'Studio A', 1, 1),
('Yoga & Mindfulness', 2, 2, 'Rabu',   '07:00', '08:00', 'Studio A', 1, 1),
('Yoga & Mindfulness', 2, 2, 'Jumat',  '07:00', '08:00', 'Studio A', 1, 1),
('Weight Training',    1, 2, 'Senin',  '09:00', '10:30', 'Ruang Beban', 1, 1),
('Weight Training',    1, 2, 'Rabu',   '17:00', '18:30', 'Ruang Beban', 1, 1),
('Weight Training',    1, 2, 'Jumat',  '09:00', '10:30', 'Ruang Beban', 1, 1),
('HIIT Cardio',        3, 2, 'Selasa', '06:00', '06:45', 'Studio B',   1, 1),
('HIIT Cardio',        3, 2, 'Kamis',  '06:00', '06:45', 'Studio B',   1, 1),
('HIIT Cardio',        3, 2, 'Sabtu',  '06:00', '06:45', 'Studio B',   1, 1),
('Body Transformation',4, 3, 'Selasa', '17:00', '18:30', 'Ruang Beban', 1, 1),
('Body Transformation',4, 3, 'Kamis',  '17:00', '18:30', 'Ruang Beban', 1, 1);

-- Testimoni
INSERT IGNORE INTO testimonials (member_name, quote, rating, is_active, created_by) VALUES
('Ahmad Fauzi',
 'Bergabung sudah 6 bulan, badan jauh lebih fit dan sehat! Trainer-nya sangat profesional dan supportif. Highly recommended!',
 5, 1, 1),
('Dewi Rahayu',
 'Fasilitas lengkap dan bersih, jadwal kelas fleksibel. Saya sudah turun 12 kg dalam 4 bulan. Terima kasih PowerFit!',
 5, 1, 1),
('Rizky Pratama',
 'Tempat gym terbaik yang pernah saya coba. Suasananya sangat nyaman dan trainernya berpengalaman.',
 4, 1, 1);

-- ============================================================
--  RINGKASAN RELASI
-- ============================================================
--
--  admins ──< gym_profile      (1 admin mengelola 1 profil gym)
--  admins ──< trainers         (1 admin mengelola banyak trainer)
--  admins ──< packages         (1 admin mengelola banyak paket)
--  admins ──< schedules        (1 admin mengelola banyak jadwal)
--  admins ──< galleries        (1 admin mengelola banyak galeri)
--  admins ──< testimonials     (1 admin mengelola banyak testimoni)
--  trainers ──< schedules      (1 trainer mengajar banyak kelas)
--  packages ──< schedules      (1 paket mencakup banyak kelas)
--
-- ============================================================
