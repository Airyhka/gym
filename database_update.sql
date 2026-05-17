-- Warriors Gym — Database Update
-- Jalankan file ini setelah mengimport database(1).sql
-- Menambah tabel members dan memperbarui data seed ke Warriors Gym

USE gym_db;

-- Tabel member gym
CREATE TABLE IF NOT EXISTS members (
    id           INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100)     NOT NULL,
    email        VARCHAR(100),
    phone        VARCHAR(20),
    package_id   INT UNSIGNED                              COMMENT 'FK ke packages.id',
    package_name VARCHAR(100),
    start_date   DATE,
    end_date     DATE,
    status       ENUM('active','expired','suspended')      NOT NULL DEFAULT 'active',
    notes        TEXT,
    created_at   TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_members_package
        FOREIGN KEY (package_id) REFERENCES packages (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Data member gym';

-- Update profil ke Warriors Gym
UPDATE gym_profile SET
    gym_name = 'Warriors Gym',
    tagline  = 'Coba dateng sini, pasti jadi gerak!',
    history  = 'Warriors Gym berdiri sejak 2018. Berawal dari garasi kecil, kini jadi tempat latihan favorit ratusan member di kota.',
    vision   = 'Gym yang terasa kayak rumah sendiri — nyaman, niat, dan tanpa basa-basi.',
    mission  = 'Fasilitasi semua orang untuk bergerak aktif, tanpa intimidasi dan tanpa embel-embel mahal.'
WHERE id = 1;

-- (Opsional) Sample member data
INSERT IGNORE INTO members (name, email, phone, package_name, start_date, end_date, status) VALUES
('Budi Setiawan',  'budi@example.com',  '08111111111', 'Paket Bulanan',   '2024-01-01', '2024-02-01', 'expired'),
('Siti Aminah',    'siti@example.com',  '08222222222', 'Paket Kuartalan', '2024-03-01', '2024-06-01', 'active'),
('Raka Pratama',   'raka@example.com',  '08333333333', 'Paket Tahunan',   '2024-01-15', '2025-01-15', 'active');
