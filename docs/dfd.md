# DFD — Data Flow Diagram
# Warriors Gym Website

Dokumen ini menggambarkan aliran data pada sistem Warriors Gym dari level 0 (konteks) hingga level 1 (proses utama).

**Notasi yang digunakan:**
- `[Entitas Eksternal]` — sumber atau tujuan data di luar sistem
- `(Proses)` — proses yang mengolah data
- `=Penyimpanan Data=` — tempat data disimpan

---

## Level 0 — Context Diagram

Gambaran tingkat tertinggi: sistem sebagai satu proses tunggal yang berinteraksi dengan dua entitas eksternal.

```mermaid
flowchart LR
    Pengunjung["[Pengunjung\nWebsite]"]
    Admin["[Admin]"]
    Sistem(("0\nWarriors Gym\nSystem"))

    Pengunjung -->|"Info gym yang diminta\nData form kontak"| Sistem
    Sistem -->|"Profil, paket, galeri\nKonfirmasi pesan terkirim"| Pengunjung

    Admin -->|"Kredensial login\nData CRUD"| Sistem
    Sistem -->|"Halaman admin\nKonfirmasi operasi\nLaporan data"| Admin
```

---

## Level 1 — Diagram Proses Utama

Sistem dipecah menjadi 6 proses utama beserta penyimpanan data yang terlibat.

```mermaid
flowchart TD
    %% External Entities
    Pengunjung["[Pengunjung]"]
    Admin["[Admin]"]

    %% Data Stores
    DS_Admins[("D1\nadmins")]
    DS_Profil[("D2\ngym_profile")]
    DS_Paket[("D3\npackages")]
    DS_Galeri[("D4\ngalleries")]
    DS_Member[("D5\nmembers")]
    DS_Pesan[("D6\ncontact_messages")]

    %% Processes
    P1(("1.0\nAutentikasi\nAdmin"))
    P2(("2.0\nManajemen\nProfil Gym"))
    P3(("3.0\nManajemen\nPaket Member"))
    P4(("4.0\nManajemen\nGaleri"))
    P5(("5.0\nManajemen\nData Member"))
    P6(("6.0\nManajemen\nPesan Kontak"))
    P7(("7.0\nTampil\nWebsite Publik"))

    %% Aliran Pengunjung
    Pengunjung -->|"Permintaan info"| P7
    P7 -->|"Baca profil"| DS_Profil
    P7 -->|"Baca paket aktif"| DS_Paket
    P7 -->|"Baca galeri aktif"| DS_Galeri
    P7 -->|"Info gym, paket, galeri"| Pengunjung
    Pengunjung -->|"Data kontak & pesan"| P6
    P6 -->|"Simpan pesan"| DS_Pesan
    P6 -->|"Konfirmasi terkirim"| Pengunjung

    %% Aliran Admin — Autentikasi
    Admin -->|"Username & password"| P1
    P1 <-->|"Verifikasi hash"| DS_Admins
    P1 -->|"Session token"| Admin

    %% Aliran Admin — Profil
    Admin -->|"Data profil gym"| P2
    P2 <-->|"CRUD"| DS_Profil

    %% Aliran Admin — Paket
    Admin -->|"Data paket"| P3
    P3 <-->|"CRUD"| DS_Paket

    %% Aliran Admin — Galeri
    Admin -->|"Data foto"| P4
    P4 <-->|"CRUD"| DS_Galeri

    %% Aliran Admin — Member
    Admin -->|"Data member"| P5
    P5 <-->|"CRUD + filter"| DS_Member
    P5 -->|"Sinkronisasi nama paket"| DS_Paket

    %% Aliran Admin — Pesan
    Admin -->|"Baca / hapus pesan"| P6
    P6 <-->|"Read / delete"| DS_Pesan
    P6 -->|"Daftar pesan masuk"| Admin
```

---

## Detail Proses Level 1

### 1.0 — Autentikasi Admin

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input | Admin | Username + password |
| Proses | Verifikasi `password_verify()` terhadap `admins.password_hash` |
| Output | Admin | PHP Session aktif |
| Store | `admins` | Baca data hash |

### 2.0 — Manajemen Profil Gym

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input | Admin | Form data profil |
| Proses | INSERT atau UPDATE `gym_profile` |
| Output | Admin | Redirect + flash message |
| Store | `gym_profile` | Tulis |

### 3.0 — Manajemen Paket Member

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input | Admin | Form paket (nama, harga, benefits) |
| Proses | CRUD pada tabel `packages` |
| Output | Admin | Daftar paket terbaru |
| Store | `packages` | Baca / Tulis |

### 4.0 — Manajemen Galeri

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input | Admin | Form foto (path, judul, kategori) |
| Proses | CRUD pada tabel `galleries` |
| Output | Admin | Grid foto terbaru |
| Store | `galleries` | Baca / Tulis |

### 5.0 — Manajemen Data Member

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input | Admin | Form member + filter pencarian |
| Proses | CRUD + kalkulasi status (aktif/kadaluarsa/ditangguhkan) |
| Output | Admin | Tabel member terfilter |
| Store | `members`, `packages` | Baca / Tulis |

### 6.0 — Manajemen Pesan Kontak

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input Pengunjung | Pengunjung | Nama, email, no. HP, pesan |
| Input Admin | Admin | Aksi tandai dibaca / hapus |
| Proses | INSERT (dari pengunjung) / UPDATE-DELETE (dari admin) |
| Output Pengunjung | Pengunjung | Konfirmasi pesan diterima |
| Output Admin | Admin | Daftar pesan masuk dengan status |
| Store | `contact_messages` | Baca / Tulis |

### 7.0 — Tampil Website Publik

| Aliran | Dari / Ke | Keterangan |
|---|---|---|
| Input | Pengunjung | Navigasi halaman |
| Proses | Baca data dari localStorage (data di-render via JS dari `defaultData`) |
| Output | Pengunjung | Halaman profil, paket, galeri |
| Store | `gym_profile`, `packages`, `galleries` | Baca saja |

> **Catatan:** Proses 7.0 saat ini membaca dari `localStorage` pada sisi browser (frontend statis). Untuk sinkronisasi penuh dengan MySQL, frontend perlu dikonversi ke PHP atau membaca dari REST API.
