# Use Case Diagram — Warriors Gym

Dokumen ini menggambarkan interaksi antara aktor dan fungsionalitas sistem Warriors Gym.

**Aktor:**
- **Pengunjung** — pengguna publik yang mengakses website tanpa login
- **Admin** — pengelola sistem yang mengakses panel admin setelah login

---

## Diagram Utama (Overview)

```mermaid
graph LR
    Pengunjung(["👤 Pengunjung"])
    Admin(["👤 Admin"])

    subgraph SYS["&#9;&#9;&#9; Warriors Gym System &#9;&#9;&#9;"]
        direction TB

        subgraph PUB["Akses Publik"]
            UC1("Melihat Profil Gym")
            UC2("Melihat Paket Member")
            UC3("Melihat Galeri Foto")
            UC4("Mengirim Pesan Kontak")
        end

        subgraph ADM["Manajemen Admin"]
            UC5("Login")
            UC6("Logout")
            UC7("Mengelola Profil Gym")
            UC8("Mengelola Paket Member")
            UC9("Mengelola Galeri Foto")
            UC10("Mengelola Data Member")
            UC11("Mengelola Pesan Masuk")
            UC12("Mengelola Akun Admin")
        end
    end

    Pengunjung --- UC1
    Pengunjung --- UC2
    Pengunjung --- UC3
    Pengunjung --- UC4

    Admin --- UC5
    Admin --- UC6
    Admin --- UC7
    Admin --- UC8
    Admin --- UC9
    Admin --- UC10
    Admin --- UC11
    Admin --- UC12
```

---

## Detail Use Case — Pengunjung

```mermaid
graph LR
    P(["👤 Pengunjung"])

    subgraph SYS["Warriors Gym System"]
        UC1("Melihat Profil Gym")
        UC2("Melihat Paket Member")
        UC3("Melihat Galeri Foto")
        UC4("Mengirim Pesan Kontak")

        UC1a("Baca Sejarah Gym")
        UC1b("Baca Visi & Misi")
        UC1c("Lihat Info Kontak")
        UC3a("Filter per Kategori")
        UC4a("Validasi Form")
        UC4b("Simpan ke Database")

        UC1 -.->|include| UC1a
        UC1 -.->|include| UC1b
        UC1 -.->|include| UC1c
        UC3 -.->|extend| UC3a
        UC4 -.->|include| UC4a
        UC4 -.->|include| UC4b
    end

    P --- UC1
    P --- UC2
    P --- UC3
    P --- UC4
```

---

## Detail Use Case — Admin (Autentikasi)

```mermaid
graph LR
    A(["👤 Admin"])

    subgraph SYS["Warriors Gym System"]
        UC5("Login")
        UC6("Logout")
        UC5a("Verifikasi Kredensial")
        UC5b("Buat PHP Session")
        UC6a("Hapus PHP Session")

        UC5 -.->|include| UC5a
        UC5 -.->|include| UC5b
        UC6 -.->|include| UC6a
    end

    A --- UC5
    A --- UC6
```

---

## Detail Use Case — Admin (Kelola Konten)

```mermaid
graph LR
    A(["👤 Admin"])

    subgraph SYS["Warriors Gym System"]
        UC7("Mengelola Profil Gym")
        UC8("Mengelola Paket Member")
        UC9("Mengelola Galeri Foto")

        UC7a("Edit Info Gym")
        UC7b("Update Kontak & Maps")

        UC8a("Tambah Paket")
        UC8b("Edit Paket")
        UC8c("Hapus Paket")
        UC8d("Atur Urutan Tampil")

        UC9a("Tambah Foto")
        UC9b("Edit Foto")
        UC9c("Hapus Foto")
        UC9d("Atur Kategori")

        UC7 -.->|include| UC7a
        UC7 -.->|include| UC7b

        UC8 -.->|include| UC8a
        UC8 -.->|include| UC8b
        UC8 -.->|include| UC8c
        UC8 -.->|extend| UC8d

        UC9 -.->|include| UC9a
        UC9 -.->|include| UC9b
        UC9 -.->|include| UC9c
        UC9 -.->|extend| UC9d
    end

    A --- UC7
    A --- UC8
    A --- UC9
```

---

## Detail Use Case — Admin (Member & Pesan)

```mermaid
graph LR
    A(["👤 Admin"])

    subgraph SYS["Warriors Gym System"]
        UC10("Mengelola Data Member")
        UC11("Mengelola Pesan Masuk")
        UC12("Mengelola Akun Admin")

        UC10a("Tambah Member")
        UC10b("Edit Member")
        UC10c("Hapus Member")
        UC10d("Cari Member")
        UC10e("Filter Status Member")
        UC10f("Kalkulasi Status Otomatis")

        UC11a("Baca Pesan")
        UC11b("Tandai Sudah Dibaca")
        UC11c("Hapus Pesan")
        UC11d("Filter Pesan Belum Dibaca")

        UC12a("Ganti Password")
        UC12b("Update Nama Tampil")

        UC10 -.->|include| UC10a
        UC10 -.->|include| UC10b
        UC10 -.->|include| UC10c
        UC10 -.->|extend| UC10d
        UC10 -.->|extend| UC10e
        UC10 -.->|include| UC10f

        UC11 -.->|include| UC11a
        UC11 -.->|extend| UC11b
        UC11 -.->|extend| UC11c
        UC11 -.->|extend| UC11d

        UC12 -.->|include| UC12a
        UC12 -.->|extend| UC12b
    end

    A --- UC10
    A --- UC11
    A --- UC12
```

---

## Tabel Use Case Lengkap

### Pengunjung

| ID | Use Case | Deskripsi | Relasi |
|---|---|---|---|
| UC-01 | Melihat Profil Gym | Membaca info gym: sejarah, visi, misi, alamat, kontak | — |
| UC-02 | Melihat Paket Member | Melihat daftar paket keanggotaan dan harga | — |
| UC-03 | Melihat Galeri Foto | Browse foto fasilitas, filter per kategori | extend UC-03a |
| UC-03a | Filter Galeri per Kategori | Filter foto berdasarkan kategori (Ruang Utama, Kardio, dll) | extend dari UC-03 |
| UC-04 | Mengirim Pesan Kontak | Mengisi dan submit form kontak | include UC-04a, UC-04b |
| UC-04a | Validasi Form | Cek kelengkapan field sebelum submit | include dari UC-04 |
| UC-04b | Simpan Pesan ke DB | INSERT ke tabel `contact_messages` | include dari UC-04 |

### Admin

| ID | Use Case | Deskripsi | Relasi |
|---|---|---|---|
| UC-05 | Login | Verifikasi username & password, buat session | include UC-05a, UC-05b |
| UC-05a | Verifikasi Kredensial | `password_verify()` terhadap hash di tabel `admins` | include dari UC-05 |
| UC-05b | Buat PHP Session | Set `$_SESSION['admin_id']` dan `admin_name` | include dari UC-05 |
| UC-06 | Logout | Hapus session dan redirect ke login | include UC-06a |
| UC-07 | Mengelola Profil Gym | CRUD data `gym_profile` | include UC-07a, UC-07b |
| UC-08 | Mengelola Paket Member | CRUD data `packages` | include UC-08a/b/c |
| UC-08d | Atur Urutan Tampil | Menentukan `display_order` paket di halaman publik | extend dari UC-08 |
| UC-09 | Mengelola Galeri Foto | CRUD data `galleries` | include UC-09a/b/c |
| UC-09d | Atur Kategori Foto | Menentukan kategori foto untuk filter galeri | extend dari UC-09 |
| UC-10 | Mengelola Data Member | CRUD data `members` | include UC-10a/b/c/f |
| UC-10d | Cari Member | Cari berdasarkan nama, email, atau no. HP | extend dari UC-10 |
| UC-10e | Filter Status Member | Filter berdasarkan status: aktif/kadaluarsa/ditangguhkan | extend dari UC-10 |
| UC-10f | Kalkulasi Status Otomatis | Status dikalkulasi dari `end_date` vs tanggal hari ini | include dari UC-10 |
| UC-11 | Mengelola Pesan Masuk | Baca, tandai, dan hapus `contact_messages` | include UC-11a |
| UC-11b | Tandai Sudah Dibaca | UPDATE `is_read = 1` satu atau semua pesan | extend dari UC-11 |
| UC-11d | Filter Pesan Belum Dibaca | Tampil hanya pesan dengan `is_read = 0` | extend dari UC-11 |
| UC-12 | Mengelola Akun Admin | Ganti password dan nama tampil | include UC-12a |
| UC-12b | Update Nama Tampil | Perbarui `name` di tabel `admins` | extend dari UC-12 |

---

## Keterangan Relasi

| Simbol | Keterangan |
|---|---|
| `---` | Association — aktor berinteraksi langsung dengan use case |
| `-.->` include | Use case yang selalu dieksekusi saat use case utama dijalankan |
| `-.->` extend | Use case opsional yang memperluas perilaku use case utama |
