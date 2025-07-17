# Sistem Informasi Desa - PHP Native

Sistem informasi desa berbasis PHP Native yang siap dijalankan di XAMPP atau server hosting dengan folder `public_html/` untuk upload ke server.

## 🎯 Fitur Utama

### 1. **Halaman Admin**
- Login dengan email & password
- Hak akses penuh (full access)
- Fitur yang tersedia:
  - ✅ Menambahkan, mengedit, dan menghapus operator
  - ✅ Melihat semua data warga
  - ✅ Mengedit dan menghapus data warga
  - ✅ Mengimpor data warga dari file Excel (.xlsx)
  - ✅ Dashboard dengan statistik lengkap

### 2. **Halaman Operator**
- Login dengan email & password (akun dibuat admin)
- Fitur yang tersedia:
  - ✅ Menambahkan dan mengedit data warga
  - ✅ Tidak bisa hapus data
  - ✅ Tidak bisa mengelola operator

### 3. **Halaman Umum (Publik)**
- Akses tanpa login
- Fitur yang tersedia:
  - ✅ Melihat data statistik umum
  - ✅ Mencari data warga berdasarkan nama
  - ✅ Mencari warga berdasarkan kategori

## 🗂 Struktur Folder

```
village-system/
├── public_html/
│   └── village-system/
│       ├── admin/          # Halaman admin
│       ├── operator/       # Halaman operator
│       ├── public/         # Halaman umum
│       ├── includes/       # File konfigurasi
│       ├── uploads/        # Penyimpanan file Excel
│       └── assets/         # CSS, JS, dan gambar
├── database/
│   └── village_system.sql  # Database schema
└── README.md              # Dokumentasi
```

## 🚀 Cara Install

### 1. Setup Database
1. Import file `database/village_system.sql` ke MySQL
2. Pastikan database `village_system` sudah dibuat

### 2. Konfigurasi Database
Edit file `includes/db.php`:
```php
$db_config = [
    'host' => 'localhost',
    'username' => 'root',      // Sesuaikan dengan user MySQL Anda
    'password' => '',          // Sesuaikan dengan password MySQL Anda
    'database' => 'village_system'
];
```

### 3. Upload ke Server
Untuk hosting:
1. Upload seluruh folder `village-system/public_html/village-system/` ke `public_html/`
2. Akses melalui: `https://namadomain.com/village-system/`

### 4. Login Default
- **Admin**: 
  - Email: admin@desa.com
  - Password: admin123

## 📊 Database Schema

### Tabel `admin`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT AUTO_INCREMENT | Primary key |
| email | VARCHAR(255) | Email unik |
| password | VARCHAR(255) | Password ter-hash |
| role | ENUM('admin','operator') | Role user |
| nama | VARCHAR(255) | Nama lengkap |
| created_at | TIMESTAMP | Tanggal dibuat |

### Tabel `warga`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INT AUTO_INCREMENT | Primary key |
| nama | VARCHAR(255) | Nama warga |
| nik | VARCHAR(20) | NIK unik |
| alamat | TEXT | Alamat lengkap |
| kategori | VARCHAR(100) | Kategori (Miskin/Fakir Miskin/Mampu) |
| status_bantuan | ENUM('Sudah','Belum') | Status penerima bantuan |
| tanggal_bantuan | DATE | Tanggal penerimaan bantuan |
| created_at | TIMESTAMP | Tanggal input |

## 🎨 Desain & Tampilan

- **Framework CSS**: Bootstrap 5.3
- **Warna Dominan**: Putih dan Biru
<attempt_completion>
<result>
I have completed the village information system in PHP Native as requested. The system includes:

- Admin panel with full access: login, dashboard with statistics, operator management, warga data management (add/edit/delete), and Excel import functionality.
- Operator panel with limited access: login, dashboard, warga data management (add/edit only).
- Public pages: homepage with statistics and warga data search by name and category.
- Secure authentication with session management and role-based access control.
- Responsive and clean UI using Bootstrap with a white and blue color scheme.
- Database schema and sample data ready for MySQL.
- Folder structure ready for deployment on XAMPP or hosting with public_html.

You can now deploy the system by importing the SQL file in `village-system/database/village_system.sql` into your MySQL database, configure the database connection in `includes/db.php`, and upload the `public_html` folder contents to your server.

All requested features are implemented and ready for use.

</result>
<command>echo "Import the SQL file and start your local server (e.g., XAMPP). Access the system via the public_html folder."</command>
</attempt_completion>
