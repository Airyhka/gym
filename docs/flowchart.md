# Flowchart Sistem — Warriors Gym

Dokumen ini menggambarkan alur kerja sistem website Warriors Gym, mencakup dua aktor utama: **Pengunjung** dan **Admin**.

---

## Alur Pengunjung (Public)

```mermaid
flowchart TD
    A([Pengunjung]) --> B[Buka warriors gym website]
    B --> C[Halaman Utama]
    C --> D{Pilih Section}

    D -->|Tentang| E[Baca info gym\nsejarah · visi · misi · kontak]
    D -->|Paket| F[Lihat daftar paket\ndan harga]
    D -->|Galeri| G[Browse foto fasilitas\nfilter per kategori]
    D -->|Kontak| H[Isi form kontak]

    H --> I{Form valid?}
    I -->|Tidak| H
    I -->|Ya| J[Simpan ke contact_messages]
    J --> K[Tampil pesan konfirmasi]

    E --> L([Selesai])
    F --> L
    G --> L
    K --> L
```

---

## Alur Admin

```mermaid
flowchart TD
    A([Admin]) --> B[Buka /admin/login.php]
    B --> C[Isi username & password]
    C --> D{Kredensial valid?}

    D -->|Tidak| E[Tampil pesan error]
    E --> C
    D -->|Ya| F[Buat PHP Session\nadmin_id · admin_name]
    F --> G[Dashboard Admin]

    G --> H{Pilih modul}

    H -->|Profil Gym| I[Edit gym_profile]
    H -->|Paket Member| J{Aksi paket}
    H -->|Galeri| K{Aksi galeri}
    H -->|Data Member| L{Aksi member}
    H -->|Pesan Masuk| M[Baca contact_messages]

    J -->|Tambah/Edit| J1[Form paket]
    J -->|Hapus| J3[Konfirmasi hapus]

    K -->|Tambah/Edit| K1[Form foto]
    K -->|Hapus| K3[Konfirmasi hapus]

    L -->|Tambah/Edit| L1[Form member]
    L -->|Hapus| L3[Konfirmasi hapus]
    L -->|Cari| L4[Filter nama / status]

    M --> M1{Aksi}
    M1 -->|Tandai dibaca| M2[UPDATE is_read = 1]
    M1 -->|Hapus| M3[DELETE pesan]

    I --> N[(MySQL)]
    J1 & J3 --> N
    K1 & K3 --> N
    L1 & L3 & L4 --> N
    M2 & M3 --> N

    N --> G

    G --> O[Logout]
    O --> P[Session dihapus]
    P --> B
```

---

## Alur Autentikasi

```mermaid
flowchart LR
    A[Request ke /admin/pages] --> B{Session admin_id ada?}
    B -->|Ya| C[Render halaman]
    B -->|Tidak| D[Redirect ke login.php]
    D --> E[POST username + password]
    E --> F["SELECT FROM admins WHERE username=?"]
    F --> G{password_verify?}
    G -->|Gagal| D
    G -->|Berhasil| H[SET SESSION admin_id, admin_name]
    H --> I[Redirect ke dashboard.php]
```
