# 📋 PRODUCT REQUIREMENTS DOCUMENT (PRD)
# SIMOR BMS — Sistem Informasi Manajemen Operasi Rig
### PT. Besmindo Materi Sewatama

---

> **Versi:** 1.0.0  
> **Tanggal:** September 2026  
> **Status:** Production  
> **Penulis:** Tim Teknologi PT. Besmindo Materi Sewatama  

---

## 📑 DAFTAR ISI

1. [Latar Belakang & Ringkasan Eksekutif](#1-latar-belakang--ringkasan-eksekutif)
2. [Tujuan & Sasaran Produk](#2-tujuan--sasaran-produk)
3. [Pemangku Kepentingan (Stakeholders)](#3-pemangku-kepentingan-stakeholders)
4. [Arsitektur Sistem & Stack Teknologi](#4-arsitektur-sistem--stack-teknologi)
5. [Skema Database (Data Model)](#5-skema-database-data-model)
6. [Modul & Fitur Lengkap](#6-modul--fitur-lengkap)
7. [Alur Kerja Operasional (Workflow)](#7-alur-kerja-operasional-workflow)
8. [Formula & Kalkulasi KPI](#8-formula--kalkulasi-kpi)
9. [Arsitektur Sinkronisasi Data](#9-arsitektur-sinkronisasi-data)
10. [API Endpoint & Routing](#10-api-endpoint--routing)
11. [Fitur Export & Pelaporan](#11-fitur-export--pelaporan)
12. [User Interface & Pengalaman Pengguna](#12-user-interface--pengalaman-pengguna)
13. [Keamanan & Autentikasi](#13-keamanan--autentikasi)
14. [Sumber Data & Proses Impor](#14-sumber-data--proses-impor)
15. [Batasan & Asumsi Sistem](#15-batasan--asumsi-sistem)
16. [Roadmap Pengembangan](#16-roadmap-pengembangan)

---

## 1. Latar Belakang & Ringkasan Eksekutif

### 1.1 Profil Perusahaan

**PT. Besmindo Materi Sewatama (BMS)** adalah perusahaan penyedia jasa pengeboran dan workover yang mengoperasikan armada rig di wilayah operasi minyak dan gas bumi. BMS mengelola **18 unit rig** dengan kode identifikasi `BMS 01` hingga `BMS 18`, masing-masing dengan tarif operasi harian (ODR) berbeda sesuai kapasitas dan kontrak dengan perusahaan minyak.

### 1.2 Permasalahan yang Diselesaikan

Sebelum SIMOR, pencatatan operasi rig BMS dilakukan sepenuhnya menggunakan **Microsoft Excel** dengan file terpisah:
- `Daily Report [BULAN] [TAHUN] SYS.xls` — Data harian per sumur per rig
- `NPT [BULAN] [TAHUN] SYS.xlsx` — Catatan jam downtime per kategori
- `Monthly Report [BULAN] [TAHUN] SYS.xlsx` — Rekapitulasi bulanan KPI
- `REKAP DAILY REPORT THN [TAHUN] SYS.xls` — Rekap tahunan data sumur
- `REKAP NPT TAHUN [TAHUN] SYS.xlsx` — Rekap tahunan NPT seluruh rig

**Masalah utama sistem Excel:**

| Masalah | Dampak |
|---|---|
| Data tersebar di banyak file | Inkonsistensi antar laporan, risiko salah copy-paste |
| Tidak ada validasi input | Angka bisa salah ketik tanpa terdeteksi |
| Tidak ada sinkronisasi otomatis | NPT dan Daily Report harus diisi dua kali secara manual |
| Tidak ada akses multi-user | Hanya bisa diakses satu orang sekaligus |
| Formula kompleks mudah rusak | Kolom tersembunyi atau baris dihapus merusak seluruh laporan |
| Tidak ada history/audit trail | Perubahan tidak bisa dilacak |
| Lambat untuk data besar | File 2.000+ baris jadi lambat dan crash |

### 1.3 Solusi: SIMOR BMS

**SIMOR** (Sistem Informasi Manajemen Operasi Rig) adalah aplikasi web berbasis database yang menggantikan seluruh workflow Excel menjadi sistem terpusat, real-time, dan otomatis, tanpa menghilangkan kemampuan export ke format Excel yang sudah familiar bagi pengguna.

---

## 2. Tujuan & Sasaran Produk

### 2.1 Tujuan Utama

1. **Digitalisasi pencatatan operasi** — Menggantikan file Excel manual dengan sistem database terpusat
2. **Sinkronisasi otomatis** — Setiap input di Log Harian atau NPT langsung terhitung ke semua laporan terkait tanpa input ulang
3. **Akurasi data** — Validasi input, kalkulasi formula otomatis, eliminasi human error
4. **Aksesibilitas** — Dapat diakses dari browser tanpa instalasi software
5. **Pelaporan instan** — Dashboard dan laporan siap pakai kapan saja, tidak perlu tunggu rekap manual

### 2.2 Sasaran Terukur (OKR)

| Objective | Key Result |
|---|---|
| Eliminasi double-entry data | 0 data yang harus diinput di lebih dari 1 tempat |
| Akurasi laporan 100% | Selisih antara export Excel SIMOR dan dokumen asli < 0.01% |
| Kecepatan pelaporan | Laporan Monthly Report tersedia < 5 detik (bukan 2 jam rekap manual) |
| Coverage data | Seluruh data Jan–Des 2026 untuk 18 rig terimport ke database |

---

## 3. Pemangku Kepentingan (Stakeholders)

### 3.1 Pengguna Sistem

| Peran | Tanggung Jawab dalam SIMOR |
|---|---|
| **Operator Lapangan** | Input Log Harian per sumur (MIRU, OPS, jam downtime harian) |
| **Supervisor Rig** | Input NPT Harian, verifikasi data Daily Report per rig |
| **Engineer/Admin Operasi** | Review Monthly Report, export laporan, input master data |
| **Management BMS** | Melihat Dashboard KPI, analisis tren, perbandingan antar rig |

### 3.2 Sistem Eksternal

| Sistem | Interaksi |
|---|---|
| **Excel SYS Files** | Sumber data historis untuk impor awal; target format export |
| **MySQL (XAMPP)** | Database server lokal/intranet |
| **PhpSpreadsheet** | Library generate file Excel dari SIMOR |

---

## 4. Arsitektur Sistem & Stack Teknologi

### 4.1 Stack Teknologi

```
┌─────────────────────────────────────────────────┐
│              PRESENTATION LAYER                  │
│  Tailwind CSS + Alpine.js + Chart.js            │
│  Font Awesome Icons + Google Fonts               │
│  JetBrains Mono (angka) + Plus Jakarta Sans     │
└──────────────────────┬──────────────────────────┘
                       │ HTTP
┌──────────────────────▼──────────────────────────┐
│              APPLICATION LAYER                   │
│         CodeIgniter 4 (PHP 8.x)                 │
│  MVC Pattern: Controllers / Models / Views       │
│  Session Auth + CSRF Protection                 │
└──────────────────────┬──────────────────────────┘
                       │ PDO/MySQLi
┌──────────────────────▼──────────────────────────┐
│                DATA LAYER                        │
│         MySQL 8.x via XAMPP                     │
│         Database: simor_bms                     │
└─────────────────────────────────────────────────┘
```

### 4.2 Desain Tema Visual

| Elemen | Spesifikasi |
|---|---|
| Background utama | `#0F172A` (Slate 900) |
| Header tabel | `#0B1E4A` (Navy BMS) |
| Aksen/highlight | `#EAB308` (Gold/Yellow) |
| Sukses/OPS | `#10B981` (Emerald) |
| Downtime/SBWC | `#F59E0B` (Amber) |
| UNPAID/bahaya | `#EF4444` (Rose) |
| Font angka | JetBrains Mono (tabular nums) |
| Font umum | Plus Jakarta Sans |

### 4.3 Struktur Direktori

```
Project Besmindo Summary/
├── app/
│   ├── Controllers/
│   │   ├── Auth.php
│   │   ├── Dashboard.php
│   │   ├── DailyReport.php        ← Step 1 & 2
│   │   ├── Npt.php                ← Step 3
│   │   ├── MonthlyReport.php
│   │   ├── RekapTahunan.php
│   │   ├── Export.php             ← Excel export (54KB)
│   │   ├── Import.php             ← Impor data Excel
│   │   └── Master/
│   │       ├── Rig.php
│   │       ├── KategoriDowntime.php
│   │       ├── ThirdParty.php
│   │       └── Lokasi.php
│   ├── Models/
│   │   ├── RigModel.php
│   │   ├── KategoriDowntimeModel.php
│   │   ├── ThirdPartyModel.php
│   │   ├── LokasiModel.php
│   │   ├── NptModel.php           ← Core model, 367 baris
│   │   ├── DailyReportModel.php
│   │   ├── DailyReportDtModel.php
│   │   └── MonthlySummaryModel.php
│   ├── Views/
│   │   ├── layout/main.php
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── daily_report/          ← grid.php, form.php, log_harian.php
│   │   ├── npt/
│   │   ├── monthly_report/
│   │   ├── rekap_tahunan/
│   │   └── master/
│   └── Config/
│       └── Routes.php
└── writable/
    └── rekap_npt_2026.json        ← Cache 2113 events kronologis
```

---

## 5. Skema Database (Data Model)

### 5.1 Diagram Relasi Tabel

```
users (1) ─────────────────────── (M) sessions [CI4 built-in]

rigs (1) ─────────────────────── (M) daily_report
rigs (1) ─────────────────────── (M) npt_harian
rigs (1) ─────────────────────── (M) monthly_summary

lokasi (1) ────────────────────── (M) daily_report
lokasi (1) ────────────────────── (M) npt_harian

daily_report (1) ──────────────── (M) daily_report_log
daily_report (1) ──────────────── (M) daily_report_dt

kategori_downtime (1) ─────────── (M) npt_harian
kategori_downtime (1) ─────────── (M) daily_report_dt

third_parties (1) ─────────────── (M) npt_harian
```

### 5.2 Definisi Tabel Lengkap

#### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| username | VARCHAR(50) UNIQUE | Login identifier |
| password | VARCHAR(255) | bcrypt hash |
| nama | VARCHAR(100) | Nama lengkap |
| created_at | DATETIME | |

#### `rigs`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| kode | VARCHAR(10) | Contoh: `BMS 01` |
| nama_rig | VARCHAR(50) | Contoh: `RIG BMS 01` |
| odr | BIGINT | Tarif harian (Rp/hari) |
| aktif | TINYINT(1) | 1=aktif, 0=non-aktif |
| created_at | DATETIME | |

**Data 18 Rig BMS:**

| Kode | ODR (Rp/hari) | Kode | ODR (Rp/hari) |
|---|---|---|---|
| BMS 01 | 38,100,000 | BMS 10 | 26,500,000 |
| BMS 02 | 23,000,000 | BMS 11 | 23,000,000 |
| BMS 03 | 26,500,000 | BMS 12 | 23,000,000 |
| BMS 04 | 26,500,000 | BMS 13 | 26,500,000 |
| BMS 05 | 26,500,000 | BMS 14 | 26,500,000 |
| BMS 06 | 26,500,000 | BMS 15 | 26,500,000 |
| BMS 07 | 26,500,000 | BMS 16 | 26,500,000 |
| BMS 08 | 26,500,000 | BMS 17 | 23,000,000 |
| BMS 09 | 26,500,000 | BMS 18 | 23,000,000 |

#### `kategori_downtime`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| nama | VARCHAR(100) | Nama kategori |
| tipe | ENUM('SBWC','UNPAID') | Klasifikasi |
| urutan | INT | Urutan tampil |
| aktif | TINYINT(1) | |

**14 Kategori Downtime (Sesuai Format Excel SYS):**

| ID | Nama | Tipe |
|---|---|---|
| 1 | Rig Downtime | UNPAID |
| 2 | Tool/Equipment Failure | UNPAID |
| 3 | Rain / Hujan | SBWC |
| 4 | Dry Road (Jalan Kering) | SBWC |
| 5 | Dry Pad (Lokasi Kering) | SBWC |
| 6 | Daylight Operation | SBWC |
| 7 | PHR Operation Permit | SBWC |
| 8 | PHR Well Problem | SBWC |
| 9 | Foam / Busa | SBWC |
| 10 | 3rd Party | SBWC |
| 11 | Transport/Mobilisasi | SBWC |
| 12 | Idul Fitri | SBWC |
| 13 | CE/PE Waiting | SBWC |
| 14 | WO PDC | SBWC |
| 15 | ESP (Electric Submersible Pump) | SBWC |
| 16 | PEMILU | SBWC |

#### `third_parties`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| nama | VARCHAR(100) | Nama perusahaan 3rd party |
| aktif | TINYINT(1) | |

**Vendor 3rd Party:**

`BHI` · `HLS` · `WI/BUKAKA` · `EJP` · `HALCO` · `SCHL` · `MGA` · `SGN` · `PESI` · `PT. CHAST` · `MU`

#### `lokasi`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| nama_lokasi | VARCHAR(100) | Nama sumur / lokasi operasi |
| aktif | TINYINT(1) | |

#### `daily_report`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| rig_id | INT FK | Reference ke rigs |
| lokasi_id | INT FK | Reference ke lokasi (sumur) |
| no_well | INT | Nomor urut sumur dalam periode |
| tanggal_mulai | DATE | Tanggal mulai pekerjaan sumur |
| tanggal_selesai | DATE | Tanggal selesai (nullable) |
| jarak | DECIMAL(15,2) | Jarak mobilisasi (KM) |
| miru_jam | DECIMAL(6,2) | Total jam MIRU (Move In / Rig Up) |
| ops_jam | DECIMAL(6,2) | Total jam operasi pengeboran |
| total_dt | DECIMAL(6,2) | Total jam downtime |
| total_jam | DECIMAL(6,2) | MIRU + OPS + Total DT |
| status_job | VARCHAR(20) | `JOB COMPLETED` / `ON PROGRESS` |
| remark | TEXT | Catatan khusus |
| bulan | INT | Filter bulan |
| tahun | INT | Filter tahun |
| created_at | DATETIME | |

#### `daily_report_log`
Tabel log harian per tanggal per sumur (1 baris = 1 hari operasi):

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| daily_report_id | INT FK | Reference ke daily_report |
| tanggal | DATE | Tanggal log |
| miru_jam | DECIMAL(5,2) | Jam MIRU hari itu |
| ops_jam | DECIMAL(5,2) | Jam OPS hari itu |
| dt_rain | DECIMAL(5,2) | Downtime Hujan |
| dt_dry_road | DECIMAL(5,2) | Downtime Jalan Kering |
| dt_dry_pad | DECIMAL(5,2) | Downtime Pad Kering |
| dt_daylight | DECIMAL(5,2) | Downtime Daylight |
| dt_3rd_party | DECIMAL(5,2) | Downtime 3rd Party total |
| dt_rig | DECIMAL(5,2) | Downtime Rig (UNPAID) |
| total_dt | DECIMAL(5,2) | Total semua downtime hari itu |
| total_hrs | DECIMAL(5,2) | Total jam hari itu |
| remark_npt | TEXT | Uraian kejadian |

#### `npt_harian`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| rig_id | INT FK | |
| lokasi_id | INT nullable FK | |
| tanggal | DATE | |
| kategori_id | INT FK | |
| third_party_id | INT nullable FK | Untuk breakdown per perusahaan |
| jam | DECIMAL(5,2) | Jam downtime |
| remark | TEXT | Uraian |
| created_at | DATETIME | |
| **UNIQUE KEY** | `(rig_id, tanggal, kategori_id, third_party_id)` | Mencegah duplikasi |

#### `daily_report_dt`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| daily_report_id | INT FK | |
| tanggal | DATE | |
| kategori_id | INT FK | |
| third_party_id | INT nullable FK | |
| jam | DECIMAL(5,2) | |

#### `monthly_summary`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT PK AI | |
| rig_id | INT FK | |
| bulan | INT | 1–12 |
| tahun | INT | |
| reliability | DECIMAL(8,6) | 0.000000 – 1.000000 |
| availability | DECIMAL(8,6) | |
| utilization | DECIMAL(8,6) | |
| total_miru | DECIMAL(8,2) | Jam |
| total_ops | DECIMAL(8,2) | Jam |
| avg_miru | DECIMAL(8,2) | Jam rata-rata per sumur |
| avg_cycle_time | DECIMAL(8,2) | Jam rata-rata OPS per sumur |
| total_well_job | INT | Jumlah sumur dikerjakan |
| sbwc_jam | DECIMAL(8,2) | Total jam SBWC |
| unpaid_jam | DECIMAL(8,2) | Total jam UNPAID |
| revenue_target | BIGINT | Target pendapatan (Rp) |
| revenue_actual | BIGINT | Realisasi pendapatan (Rp) |
| total_jam | DECIMAL(8,2) | Hari bulan × 24 |
| remark | TEXT | |
| **UNIQUE KEY** | `(rig_id, bulan, tahun)` | |

---

## 6. Modul & Fitur Lengkap

### 6.1 Modul Autentikasi

**URL:** `/login`, `/logout`

- Login dengan username + password (bcrypt)
- Session-based authentication (CodeIgniter 4 Session)
- Filter auth melindungi semua route kecuali `/login`
- Redirect otomatis ke Dashboard setelah login berhasil
- Default user: `admin` / `admin123`

---

### 6.2 Modul Dashboard

**URL:** `/dashboard`

Dashboard utama menampilkan ringkasan KPI operasional seluruh armada rig untuk bulan & tahun yang dipilih.

#### 6.2.1 KPI Cards (5 Kartu Utama)
- **Total Pekerjaan Sumur** — Jumlah sumur dikerjakan semua rig
- **Revenue Target vs Aktual** — Dalam juta Rupiah + persentase pencapaian
- **Total Downtime** — SBWC + UNPAID dalam jam
- **Rata-rata Utilitas** — Average utilization semua rig dalam %
- **Total Jam Operasi (OPS)** — Akumulasi seluruh rig

#### 6.2.2 Chart & Visualisasi (Chart.js)
- **Doughnut Chart** — Breakdown downtime per kategori (hanya tampil kategori > 0)
- **Bar Chart** — Utilitas (%) per rig + Revenue Target vs Aktual per rig
- **Line Chart** — Tren NPT bulanan 2026 (SBWC, UNPAID, Total) dari JSON cache
- **Horizontal Bar Chart** — Jam MIRU, OPS, SBWC, UNPAID per rig

#### 6.2.3 Tabel Summary Per Rig
Tabel seluruh rig dengan kolom: Kode Rig, Total Well Job, MIRU, OPS, SBWC, UNPAID, Utilitas, Revenue Target, Revenue Aktual.

---

### 6.3 Modul Daily Report (Step 1 & 2)

#### 6.3.1 Step 1 — Matriks Pekerjaan Sumur
**URL:** `/daily-report/{rig_id}/{bulan}/{tahun}`

Halaman utama pencatatan per sumur per rig per periode.

**Fitur:**
- **5 KPI Cards** — Total Sumur (progress bar), Jam OPS (% porsi), Jam MIRU (% porsi), Total Downtime (% total), Tarif ODR Kontrak
- **Toolbar Filter Interaktif:**
  - Selector Rig, Bulan (dengan tombol ‹ ›), Tahun
  - Live Search sumur/lokasi (dengan tombol × clear)
  - Filter Status: Semua / Completed / Progress
  - 3 View Mode: Ringkasan | Kartu Sumur | Matriks SYS
  - Tombol Export Excel
- **3-Step Workflow Guide** — Banner informatif di header menunjukkan alur Step 1→2→3
- **View 1: Ringkasan (Default)** — Tabel compact (no horizontal scroll), kolom: No, Nama Lokasi, Tanggal Mulai, Jarak, MIRU, OPS, SBWC, UNPAID, Total, Status, Aksi
- **Accordion Log Harian** — Klik baris sumur untuk expand/collapse rincian log per hari
- **Tombol "Buka Semua Log" / "Tutup Semua"** di header tabel
- **Quick Look Modal** — Klik icon 👁 untuk popup detail sumur tanpa navigasi halaman
- **View 2: Kartu Sumur** — Grid card visual dengan distribution bar (MIRU/OPS/DT), badge status animasi
- **View 3: Matriks SYS** — Tabel 24 kolom persis format Excel (MIRU, OPS, 11 pos SBWC, 2 pos UNPAID), dengan sticky header

**Aksi:**
- Tambah Sumur Baru (form `/daily-report/tambah/{rig_id}/{bulan}/{tahun}`)
- Edit Sumur (`/daily-report/edit/{id}`)
- Hapus Sumur (dengan konfirmasi)
- Navigasi ke Log Harian
- Navigasi ke Input NPT

#### 6.3.2 Form Tambah / Edit Sumur
**URL:** `/daily-report/tambah/{rig_id}/{bulan}/{tahun}` dan `/daily-report/edit/{id}`

**Field:**
- No Well (auto-increment, bisa diubah)
- Location / Nama Sumur (dropdown dari master `lokasi`)
- Distance/Jarak (KM)
- Tanggal Mulai & Selesai
- MIRU (jam) dengan kalkulasi live
- OPS (jam) dengan kalkulasi live
- Rincian Downtime per pos kategori (grid input semua kategori)
- Total Hours (preview otomatis: MIRU + OPS + Total DT)
- Status Job (JOB COMPLETED / ON PROGRESS)
- Remark/Catatan

**Formula Live (JavaScript):**
```
Total DT = Σ semua input kategori downtime
Total Hours = MIRU + OPS + Total DT
```

#### 6.3.3 Step 2 — Log Harian Operasi
**URL:** `/daily-report/log-harian/{rig_id}/{bulan}/{tahun}`

Input harian yang lebih granular — 1 baris = 1 hari. Setiap simpan otomatis menyinkron ke:
- Tabel `daily_report` (update aggregate)
- Tabel `npt_harian` (sinkronisasi downtime)
- Tabel `monthly_summary` (recalculate KPI)

**Field per hari:**
- Tanggal (date picker)
- Nomor Sumur (dropdown data sumur bulan ini)
- MIRU (jam)
- OPS (jam)
- DT Rain, DT Road/Pad, DT Daylight, DT 3rd Party, DT Rig
- Total DT (auto-sum)
- Total Hours (auto-sum)
- Remark NPT / Uraian

---

### 6.4 Modul NPT Harian (Step 3)

**URL:** `/npt/{rig_id}/{bulan}/{tahun}`

Pencatatan jam downtime harian per kategori dalam format matriks kalender (baris = hari, kolom = kategori downtime).

**Fitur:**
- **Matriks Kalender** — Grid tanggal × kategori downtime (1–31 baris, 14+ kolom)
- **Sinkronisasi dari Log Harian** — Angka yang sudah diinput di Log Harian tampil otomatis sebagai referensi
- **Breakdown 3rd Party** — Khusus kategori 3rd Party, bisa diisi per perusahaan (BHI, HLS, WI, dll.)
- **Remark per hari** — Untuk SBWC dan UNPAID secara terpisah
- **Total per Kolom** — Total jam per kategori downtime di footer
- **Total per Baris** — Total downtime per hari di kolom akhir
- **Auto-sync ke Monthly Summary** — Saat simpan, KPI bulanan langsung dihitung ulang

**Logika Simpan (Upsert):**
```
INSERT INTO npt_harian ... ON DUPLICATE KEY UPDATE jam = VALUES(jam)
```

**Sinkronisasi Balik ke Daily Report Log:**
Setiap perubahan NPT otomatis memperbarui field `dt_*` di tabel `daily_report_log` sesuai tanggal dan rig.

---

### 6.5 Modul Monthly Report

**URL:** `/monthly-report/{bulan}/{tahun}`

Laporan rekapitulasi KPI bulanan seluruh armada rig dalam satu tabel horizontal besar.

**Kolom yang Ditampilkan:**

| Kolom | Formula |
|---|---|
| Reliabilitas | `1 - (unpaid_jam / total_jam)` |
| Availability | `= Reliabilitas` |
| Utilization | `ops_jam / total_jam` |
| Avg MIRU | `total_miru / total_well_job` |
| Avg Cycle Time | `total_ops / total_well_job` |
| Total Well Job | COUNT sumur |
| Total MIRU | Σ jam MIRU |
| Total OPS | Σ jam OPS |
| SBWC per kategori | Breakdown NPT per pos |
| Total SBWC | Σ jam SBWC |
| Total UNPAID | Σ jam UNPAID |
| Revenue Target | `ODR × hari_dalam_bulan` |
| Revenue Aktual | `ODR × (total_jam - sbwc - unpaid) / 24` |

**Fitur:**
- Filter bulan/tahun dengan selector
- Tombol "Hitung Ulang" untuk trigger recalculate
- Total baris di footer (grand total semua rig)
- Rata-rata baris (average semua rig)
- Format angka yang konsisten (desimal 2 digit untuk jam, format Rp. untuk currency)

---

### 6.6 Modul Rekap Tahunan

#### 6.6.1 Rekap KPI Tahunan
**URL:** `/rekap-tahunan/{tahun}`

Tabel 12 bulan × 18 rig menampilkan akumulasi KPI seluruh tahun.

**Fitur:**
- Baris per Rig, kolom per bulan (Jan–Des)
- KPI yang ditampilkan: Total Well, MIRU, OPS, Utilitas, Revenue
- Click rig → detail per rig per bulan

#### 6.6.2 Rekap NPT Tahunan
**URL:** `/rekap-tahunan/npt/{tahun}`

Memiliki 4 mode tampilan (tabs):

| Tab | Konten |
|---|---|
| **Ringkasan (No-Scroll)** | Matriks ringkas semua rig × kategori downtime per bulan dipilih |
| **Log Kronologis** | Timeline event downtime per rig, urut tanggal (dari JSON cache) |
| **Matriks Lengkap SYS** | Format persis Excel REKAP NPT (30+ kolom) |
| **Grand Total** | Akumulasi keseluruhan tahun |

**Data Source:** File JSON `writable/rekap_npt_2026.json` (2,113 chronological events, 17 rig sheets)

---

### 6.7 Modul Master Data

Semua master data mendukung operasi CRUD lengkap:

#### Rig (`/master/rig`)
- Kode, Nama Rig, ODR (Rp/hari), Status Aktif
- 18 rig seeded default

#### Kategori Downtime (`/master/kategori`)
- Nama, Tipe (SBWC/UNPAID), Urutan, Status Aktif
- 16 kategori seeded default

#### Third Party (`/master/third-party`)
- Nama perusahaan, Status Aktif
- 11 vendor seeded default

#### Lokasi/Sumur (`/master/lokasi`)
- Nama lokasi/sumur, Status Aktif
- Data diimpor dari file Excel historis

---

### 6.8 Modul Export Excel

**URL:** `/export/{tipe}/{parameter}`

Semua export menggunakan **PhpSpreadsheet** dan menghasilkan file `.xlsx` dengan format identik dokumen Excel SYS asli BMS.

| Route | File Output |
|---|---|
| `/export/npt/{rig_id}/{bulan}/{tahun}` | NPT Harian per Rig |
| `/export/npt-all/{bulan}/{tahun}` | NPT All Rig (1 file, multi-sheet) |
| `/export/daily-report/{rig_id}/{bulan}/{tahun}` | Daily Report per Rig |
| `/export/monthly-report/{bulan}/{tahun}` | Monthly Report (format SYS) |
| `/export/rekap-tahunan/{tahun}` | Rekap Tahunan KPI |
| `/export/rekap-npt-tahunan/{tahun}` | Rekap NPT Tahunan |

**Standar Format Export:**
- Header perusahaan: `PT. BESMINDO MATERI SEWATAMA`
- Warna header: Navy `#002060`
- Font: Calibri
- Border: Abu-abu jelas `#94A3B8`
- Zebra rows: Putih/Soft `#F8FAFC`
- Angka UNPAID: Merah `#FF0000`
- Baris total: Kuning `#FFFF00`
- Baris rata-rata: Gold `#FFC000`

---

## 7. Alur Kerja Operasional (Workflow)

### 7.1 Workflow Harian (3 Langkah)

```
STEP 1: DATA PEKERJAAN SUMUR
   └─ Buka /daily-report/{rig}/{bulan}/{tahun}
   └─ Tambah sumur baru jika ada lokasi baru (Klik "+ Tambah Sumur")
   └─ Isi No Well, Lokasi, Tanggal Mulai, Jarak, MIRU, OPS awal
   └─ Data sumur tersimpan di tabel daily_report

       ↓

STEP 2: LOG HARIAN OPERASI
   └─ Buka /daily-report/log-harian/{rig}/{bulan}/{tahun}
   └─ Pilih tanggal & sumur yang sedang berjalan
   └─ Isi jam MIRU, OPS, dan setiap pos downtime per hari
   └─ Klik Simpan Log
   └─ Sistem otomatis:
       ├─ Update aggregate di daily_report (sum MIRU, OPS, DT)
       ├─ Sinkron ke npt_harian (INSERT/UPDATE per kategori)
       └─ Recalculate monthly_summary

       ↓

STEP 3: INPUT NPT (OPSIONAL / KOREKSI)
   └─ Buka /npt/{rig}/{bulan}/{tahun}
   └─ Cek data yang sudah ter-sinkron dari Log Harian
   └─ Koreksi atau tambah detail breakdown per kategori/per hari
   └─ Simpan NPT
   └─ Sistem otomatis:
       ├─ Sinkron balik ke daily_report_log (syncNptBackToDaily)
       └─ Recalculate monthly_summary
```

### 7.2 Workflow Pelaporan

```
MONTHLY REPORT
   └─ Buka /monthly-report/{bulan}/{tahun}
   └─ Sistem otomatis kalkulasi semua KPI dari database
   └─ Tampilkan tabel per rig + grand total
   └─ Export ke Excel (format SYS) via /export/monthly-report/{b}/{t}

       ↓

REKAP TAHUNAN KPI
   └─ Buka /rekap-tahunan/{tahun}
   └─ 12 bulan × semua rig dalam 1 tampilan

REKAP NPT TAHUNAN
   └─ Buka /rekap-tahunan/npt/{tahun}
   └─ Pilih mode: Ringkasan / Kronologis / Matriks / Grand Total
```

---

## 8. Formula & Kalkulasi KPI

### 8.1 Formula Utama

```
PERIODE = Hari dalam bulan (cal_days_in_month)
TOTAL_JAM_KALENDER = PERIODE × 24

RELIABILITY = MAX(0, 1 - (UNPAID_JAM / TOTAL_JAM_KALENDER))

AVAILABILITY = RELIABILITY  [sementara sama, rencana dikembangkan]

UTILIZATION = OPS_JAM / TOTAL_JAM_KALENDER

AVG_MIRU = TOTAL_MIRU_JAM / TOTAL_WELL_JOB

AVG_CYCLE_TIME = TOTAL_OPS_JAM / TOTAL_WELL_JOB

REVENUE_TARGET = ODR × PERIODE

REVENUE_ACTUAL = ODR × (TOTAL_JAM_KALENDER - SBWC_JAM - UNPAID_JAM) / 24
              = ODR × PRODUCTIVE_DAYS
```

### 8.2 Klasifikasi Downtime

```
SBWC (Standby Waiting Company)
  = Downtime di luar kendali Kontraktor
  = Rain + Dry Road + Dry Pad + Daylight + 3rd Party +
    Transport + CE/PE + PHR Well + Foam + Idul Fitri +
    WO PDC + ESP + PEMILU

UNPAID
  = Downtime akibat kesalahan/kerusakan Kontraktor
  = Rig Downtime + Tool/Equipment Failure

TOTAL_DT = SBWC + UNPAID
```

### 8.3 Formula Daily Report

```
TOTAL_HOURS_PER_WELL = MIRU_JAM + OPS_JAM + TOTAL_DT

SBWC_PER_WELL = Σ jam semua kategori tipe SBWC
UNPAID_PER_WELL = Σ jam semua kategori tipe UNPAID
```

### 8.4 Validasi Konsistensi

> [!IMPORTANT]
> Formula total jam harus memenuhi: `MIRU + OPS + SBWC + UNPAID = TOTAL_JAM_OPERASI_SUMUR`
> Jika tidak terpenuhi, ada data yang belum terisi di Log Harian.

---

## 9. Arsitektur Sinkronisasi Data

### 9.1 Diagram Sinkronisasi Bidireksional

```
Daily Report Log ←──────────────────────→ NPT Harian
     │                                         │
     │ syncNptBackToDaily()        upsertNpt() │
     │                                         │
     ▼                                         ▼
daily_report (aggregate)        npt_harian (detail per kategori)
     │                                         │
     └──────────────────┬──────────────────────┘
                        │
                        ▼
              monthly_summary (KPI)
                hitungDanSimpan()
                        │
                        ▼
              Dashboard & Reports
```

### 9.2 Metode Sinkronisasi Kunci (NptModel)

| Method | Fungsi |
|---|---|
| `upsertNpt()` | INSERT OR UPDATE data NPT harian dengan UNIQUE constraint |
| `syncNptBackToDaily()` | Setelah simpan NPT, perbarui daily_report_log |
| `getSummaryAllRig()` | Total NPT per kategori per rig untuk Dashboard |

### 9.3 Trigger Recalculate Monthly Summary

Monthly Summary otomatis dihitung ulang ketika:
1. User simpan Log Harian (POST `/daily-report/simpan-log`)
2. User simpan NPT (POST `/npt/simpan`)
3. User akses Monthly Report (GET `/monthly-report/{b}/{t}`)
4. User akses Dashboard (GET `/dashboard`)
5. User akses Rekap Tahunan (GET `/rekap-tahunan/{t}`)

---

## 10. API Endpoint & Routing

### 10.1 Daftar Route Lengkap

```
GET  /login                                    → Auth::index
POST /login                                    → Auth::login
GET  /logout                                   → Auth::logout

[Auth Protected]
GET  /                                         → Dashboard::index
GET  /dashboard                                → Dashboard::index

-- Master Data --
GET  /master/rig                               → Master\Rig::index
GET  /master/rig/tambah                        → Master\Rig::tambah
POST /master/rig/simpan                        → Master\Rig::simpan
GET  /master/rig/edit/{id}                     → Master\Rig::edit
POST /master/rig/update/{id}                   → Master\Rig::update
POST /master/rig/hapus/{id}                    → Master\Rig::hapus
[sama untuk: /master/kategori, /master/third-party, /master/lokasi]

-- NPT Harian --
GET  /npt                                      → Npt::index (redirect)
GET  /npt/{rig_id}/{bulan}/{tahun}             → Npt::grid
POST /npt/simpan                               → Npt::simpan
GET  /npt/data/{rig_id}/{bulan}/{tahun}        → Npt::getData (AJAX)

-- Daily Report --
GET  /daily-report                             → DailyReport::index (redirect)
GET  /daily-report/{rig_id}/{bulan}/{tahun}    → DailyReport::grid
GET  /daily-report/tambah/{rig}/{b}/{t}        → DailyReport::tambah
POST /daily-report/simpan                      → DailyReport::simpan
GET  /daily-report/log-harian/{rig}/{b}/{t}    → DailyReport::logHarian
POST /daily-report/simpan-log                  → DailyReport::simpanLog
GET  /daily-report/edit/{id}                   → DailyReport::edit
POST /daily-report/update/{id}                 → DailyReport::update
POST /daily-report/hapus/{id}                  → DailyReport::hapus

-- Monthly Report --
GET  /monthly-report                           → MonthlyReport::index
GET  /monthly-report/{bulan}/{tahun}           → MonthlyReport::view
POST /monthly-report/hitung                    → MonthlyReport::hitung

-- Rekap Tahunan --
GET  /rekap-tahunan                            → RekapTahunan::index
GET  /rekap-tahunan/{tahun}                    → RekapTahunan::view
GET  /rekap-tahunan/rig/{rig_id}/{tahun}       → RekapTahunan::viewRig
GET  /rekap-tahunan/npt                        → RekapTahunan::npt
GET  /rekap-tahunan/npt/{tahun}               → RekapTahunan::npt

-- Export --
GET  /export/npt/{rig_id}/{bulan}/{tahun}      → Export::npt
GET  /export/npt-all/{bulan}/{tahun}           → Export::nptAll
GET  /export/daily-report/{rig}/{b}/{t}        → Export::dailyReport
GET  /export/monthly-report/{bulan}/{tahun}    → Export::monthlyReport
GET  /export/rekap-tahunan/{tahun}             → Export::rekapTahunan
GET  /export/rekap-npt-tahunan/{tahun}         → Export::rekapNptTahunan
```

---

## 11. Fitur Export & Pelaporan

### 11.1 Export NPT Harian

File output: `NPT_{KODE_RIG}_{BULAN}_{TAHUN}.xlsx`

**Sheet Contents:**
- Header perusahaan (nama, judul, periode)
- Matriks 31 hari × 14+ kategori downtime
- Total per kategori di baris bawah
- Format: angka UNPAID warna merah, header navy

### 11.2 Export Daily Report

File output: `Daily_Report_{KODE_RIG}_{BULAN}_{TAHUN}.xlsx`

**Sheet Contents:**
- Header perusahaan
- Tabel sumur (no, lokasi, tanggal, jarak, MIRU, OPS, DT per kategori, total)
- Footer total akumulasi
- Format identik template Excel SYS

### 11.3 Export Monthly Report

File output: `Monthly_Report_{BULAN}_{TAHUN}.xlsx`

**Sheet Contents:**
- Header utama
- Tabel KPI per rig (Reliability, Availability, Utilization, Well Job, MIRU, OPS, DT breakdown, Revenue)
- Baris Average/Rata-rata (gold background)
- Baris Grand Total (kuning)

### 11.4 Export Rekap Tahunan

File output: `Rekap_Tahunan_{TAHUN}.xlsx`

**Multi-sheet:**
- Sheet KPI per bulan (Jan–Des) per rig
- Sheet NPT Tahunan

---

## 12. User Interface & Pengalaman Pengguna

### 12.1 Prinsip Desain

| Prinsip | Implementasi |
|---|---|
| **Dark Theme Konsisten** | Slate-900 background, semua halaman |
| **No Horizontal Scroll (Default)** | View ringkasan selalu fit di layar |
| **Family-Friendly** | 3-step guide, tooltip, empty state yang informatif |
| **Data-Dense tapi Readable** | JetBrains Mono untuk angka, warna konsisten per tipe data |
| **Interactive** | Accordion log, modal quick look, live filter, filter pill |
| **Progressive Disclosure** | Default ringkasan, detail on-demand |

### 12.2 Navigasi Sidebar

```
SIMOR BMS
├── UTAMA
│   └── Dashboard Utama
├── PENCATATAN OPERASI
│   ├── Data Pekerjaan Sumur  [STEP 1]
│   └── Log Harian Operasi    [STEP 2]
├── PENCATATAN DOWNTIME
│   └── Input Jam Downtime    [STEP 3]
├── LAPORAN BULANAN
│   └── Monthly Report
└── REKAP TAHUNAN
    ├── Rekap KPI Tahunan
    ├── Rekap NPT Tahunan
    └── Per Rig Tahunan
```

### 12.3 Responsivitas

- Sidebar collapsible dengan animasi transisi
- Grid 5 kolom → 2 kolom di mobile untuk KPI cards
- View Matriks SYS tetap bisa di-scroll horizontal secara terkontrol

---

## 13. Keamanan & Autentikasi

| Aspek | Implementasi |
|---|---|
| Autentikasi | Session-based, filter `auth` pada semua route protected |
| Password | bcrypt (`PASSWORD_BCRYPT`) via PHP `password_hash()` |
| CSRF | Token otomatis di semua form POST (`<?= csrf_field() ?>`) |
| XSS | Semua output di-escape dengan `esc()` helper CI4 |
| SQL Injection | Query Builder CI4 (parameterized queries) |
| Input Validasi | Tipe data casting, `(int)`, `(float)` sebelum insert |

---

## 14. Sumber Data & Proses Impor

### 14.1 Data yang Diimpor (2026)

| Sumber File | Tabel Target | Jumlah Record |
|---|---|---|
| `REKAP DAILY REPORT THN 2026 SYS.xls` | `daily_report` | 4,849 |
| `REKAP DAILY REPORT THN 2026 SYS.xls` | `daily_report_log` | 5,460 |
| `REKAP NPT TAHUN 2026 SYS.xlsx` | `npt_harian` | 3,392 |
| `REKAP NPT TAHUN 2026 SYS.xlsx` | `writable/rekap_npt_2026.json` | 2,113 events |

### 14.2 Controller Import

**URL:** `/import/*` (Controller `Import.php`, 38KB)

Mendukung impor dari file Excel menggunakan PhpSpreadsheet dengan validasi dan mapping kolom otomatis.

### 14.3 Script Python Bantu

Tersimpan di `brain/scratch/`:
- `import_real_dr.py` — Impor Daily Report dari Excel ke MySQL
- `import_real_npt.py` — Impor NPT dari Excel ke MySQL
- `sync_all_logs_to_npt.py` — Sinkron seluruh log ke NPT
- `extract_chronological_npt.py` — Ekstrak 2,113 events ke JSON
- `inspect_rekap_dr_rows.py` — Inspeksi struktur Excel

---

## 15. Batasan & Asumsi Sistem

### 15.1 Batasan Saat Ini

| Batasan | Keterangan |
|---|---|
| Single-user concurrent | Tidak ada locking antar user, asumsi 1 user aktif per rig per waktu |
| Local server | Berjalan di XAMPP localhost, belum ada deployment production |
| Tahun aktif | Range tahun 2024–2028 (hardcoded di selector) |
| NPT JSON | Data NPT kronologis dari JSON hanya untuk 2026, bukan real-time database |
| Reliability = Availability | Sementara menggunakan formula yang sama, bisa dikembangkan |

### 15.2 Asumsi Bisnis

- 1 hari = 24 jam (operasi 24/7)
- ODR adalah tarif fixed per hari (tidak ada overtime)
- Kategori downtime tidak berubah dalam satu tahun buku
- SBWC tidak mengurangi pendapatan; hanya UNPAID yang mengurangi

---

## 16. Roadmap Pengembangan

### Phase 1 — MVP ✅ (Selesai September 2026)

- [x] Autentikasi (login/logout)
- [x] Master Data CRUD (Rig, Kategori, 3rd Party, Lokasi)
- [x] Daily Report Step 1 (matriks sumur) + redesign UI
- [x] Daily Report Step 2 (log harian)
- [x] NPT Harian (matriks kalender + 3rd party breakdown)
- [x] Monthly Report (KPI otomatis)
- [x] Rekap Tahunan KPI & NPT (4 mode view)
- [x] Dashboard dengan Chart.js
- [x] Export Excel (semua jenis laporan, format SYS)
- [x] Import data historis 2026 (4,849 sumur, 5,460 logs, 3,392 NPT)
- [x] Sinkronisasi bidireksional Daily Log ↔ NPT Harian

### Phase 2 — Enhancement (Q4 2026)

- [ ] Multi-user management (tambah user, role-based access: Admin, Supervisor, Viewer)
- [ ] Notifikasi email/WhatsApp saat ada anomali downtime tinggi
- [ ] Import template Excel baru setiap bulan secara mandiri
- [ ] Fitur perbandingan antar periode (bulan-ke-bulan, tahun-ke-tahun)
- [ ] Availability formula yang lebih akurat (berbeda dari Reliability)
- [ ] Grafik tren NPT per rig di halaman individual rig

### Phase 3 — Advanced (2027)

- [ ] Deployment ke server intranet BMS (bukan localhost)
- [ ] Mobile-friendly view untuk input lapangan via HP
- [ ] API REST untuk integrasi dengan sistem ERP/Accounting
- [ ] Laporan PDF (generate dari template HTML→PDF)
- [ ] Backup dan restore database otomatis
- [ ] Audit trail (log perubahan data oleh siapa & kapan)
- [ ] Dashboard realtime dengan WebSocket (live update tanpa refresh)

---

## Lampiran A — Daftar Singkatan

| Singkatan | Kepanjangan |
|---|---|
| BMS | Besmindo Materi Sewatama |
| SIMOR | Sistem Informasi Manajemen Operasi Rig |
| ODR | Operating Day Rate (Tarif Operasi Harian) |
| NPT | Non-Productive Time |
| SBWC | Stand-By Waiting Company |
| MIRU | Move In Rig Up (Mobilisasi + Pemasangan Rig) |
| OPS | Operations (Operasi Pengeboran/Workover) |
| DT | Downtime |
| KPI | Key Performance Indicator |
| RAU | Reliability, Availability, Utilization |
| MVC | Model-View-Controller |
| CRUD | Create, Read, Update, Delete |

---

## Lampiran B — Kontak & Environment

| Item | Nilai |
|---|---|
| **Framework** | CodeIgniter 4 (PHP 8.x) |
| **Database** | MySQL 8.x via XAMPP |
| **URL Development** | `http://localhost:8080` |
| **Database Name** | `simor_bms` |
| **Admin Login** | `admin` / `admin123` |
| **MySQL Executable** | `C:\xampp\mysql\bin\mysql.exe` |
| **Project Directory** | `C:\xampp\htdocs\Project Besmindo Summary` |
| **Excel Source Files** | `C:\Users\LENOVO\Downloads\System\` |

---

*Dokumen ini adalah living document dan akan diperbarui seiring perkembangan sistem SIMOR BMS.*

*© 2026 PT. Besmindo Materi Sewatama — Divisi Teknologi Informasi*
