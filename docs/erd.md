# ERD — Entity Relationship Diagram
# Warriors Gym Database

Diagram ini menggambarkan struktur tabel dan relasi antar entitas pada database `gym_db`.

---

## Diagram ERD

```mermaid
erDiagram
    ADMINS {
        int id PK
        varchar username UK
        varchar password_hash
        varchar name
        timestamp created_at
        timestamp updated_at
    }

    GYM_PROFILE {
        int id PK
        varchar gym_name
        varchar tagline
        text history
        text vision
        text mission
        varchar address
        varchar phone
        varchar email
        varchar whatsapp
        text maps_embed
        varchar logo
        int created_by FK
        timestamp updated_at
    }

    PACKAGES {
        int id PK
        varchar name
        varchar duration
        decimal price
        text description
        text benefits
        smallint display_order
        tinyint is_active
        int created_by FK
        timestamp created_at
        timestamp updated_at
    }

    GALLERIES {
        int id PK
        varchar title
        varchar image
        varchar caption
        varchar category
        smallint display_order
        tinyint is_active
        int created_by FK
        timestamp created_at
        timestamp updated_at
    }

    MEMBERS {
        int id PK
        varchar name
        varchar email
        varchar phone
        int package_id FK
        varchar package_name
        date start_date
        date end_date
        enum status
        text notes
        timestamp created_at
        timestamp updated_at
    }

    CONTACT_MESSAGES {
        int id PK
        varchar name
        varchar email
        varchar phone
        text message
        tinyint is_read
        timestamp created_at
    }

    ADMINS ||--o{ GYM_PROFILE  : "mengelola (created_by)"
    ADMINS ||--o{ PACKAGES     : "mengelola (created_by)"
    ADMINS ||--o{ GALLERIES    : "mengelola (created_by)"
    PACKAGES ||--o{ MEMBERS    : "dipilih oleh (package_id)"
```

---

## Keterangan Entitas

| Tabel | Keterangan |
|---|---|
| `admins` | Akun pengelola sistem. Password disimpan dalam bcrypt hash. |
| `gym_profile` | Data profil gym — singleton (satu baris aktif). Dikelola admin. |
| `packages` | Daftar paket keanggotaan. Kolom `benefits` menyimpan JSON array. |
| `galleries` | Foto fasilitas gym. Path gambar relatif terhadap root project. |
| `members` | Data member gym. `status` bisa: `active`, `expired`, `suspended`. |
| `contact_messages` | Pesan masuk dari pengunjung publik. Tidak memerlukan akun. |

---

## Keterangan Relasi

| Relasi | Jenis | Keterangan |
|---|---|---|
| `admins` → `gym_profile` | 1 : N | Satu admin bisa mengelola banyak versi profil |
| `admins` → `packages` | 1 : N | Satu admin bisa membuat banyak paket |
| `admins` → `galleries` | 1 : N | Satu admin bisa menambah banyak foto |
| `packages` → `members` | 1 : N | Satu paket bisa dipilih banyak member |
| `contact_messages` | Standalone | Tidak berelasi ke entitas lain |

---

## Catatan

- `benefits` di tabel `packages` disimpan sebagai JSON string, contoh: `["Akses area utama","Kelas grup","Loker gratis"]`
- `status` di tabel `members` adalah ENUM: nilai di luar `active`/`expired`/`suspended` tidak diterima
- `package_name` di `members` menyimpan nama paket secara redundan — dimaksudkan agar nama paket tetap terbaca meski paket dihapus dari tabel `packages`
