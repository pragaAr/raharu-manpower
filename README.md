# 🚀 Manpower Management System

Aplikasi manajemen sumber daya manusia (HR) berbasis web yang dibangun menggunakan **Laravel 12** dan **Livewire 3** untuk mengelola data karyawan secara terpusat, terstruktur, dan scalable.

> ⚠️ **Status: Work In Progress (WIP)**  
> Beberapa modul utama masih dalam tahap pengembangan.
> 🔥 Dibangun 100% dengan pendekatan _vibe coding_ — eksploratif, iteratif, dan terus berkembang.🔥

---

## 📌 Gambaran Umum

Dirancang untuk membantu proses pengelolaan siklus hidup karyawan secara sistematis, mulai dari pencatatan data awal, perubahan status, mutasi jabatan, renewal kontrak hingga pengaturan hak akses pengguna.

Fokus utama pengembangan saat ini adalah:

- Struktur data karyawan yang solid
- Pencatatan riwayat kerja yang terorganisir
- Sistem hak akses berbasis role
- Arsitektur kode yang modular dan maintainable

---

## ✨ Fitur yang Sudah Tersedia

### 👥 Manajemen Karyawan

- Pengelolaan data lengkap karyawan
- Kategori kerja (PKWT / PKWTT)
- Pengelolaan status karyawan (Aktif, Nonaktif, dll)
- Tracking masa kontrak
- Relasi data yang terstruktur

### 🔄 Mutasi & Riwayat Kerja

- Pencatatan promosi dan demosi
- Perpindahan unit/divisi
- Perubahan status kerja
- Perpanjangan kontrak
- Riwayat jabatan tersimpan secara kronologis

### 🗂 Master Data

Pengelolaan data referensi:

- Lokasi
- Divisi
- Jabatan
- Unit
- Kategori

### 🔐 Role-Based Access Control (RBAC)

- Hak akses berbasis role
- Pembagian peran (Admin, HR, dll)
- Menggunakan **Spatie Laravel Permission**

### 📤 Export Data

- Export ke format Excel
- Export ke format PDF
- Mendukung kebutuhan laporan dan dokumentasi

---

## 🚧 Dalam Tahap Pengembangan

Modul berikut masih belum diimplementasikan:

- 🕒 Sistem Absensi
- 📝 Pengajuan Cuti & Izin
- 💰 Sistem Payroll
- 📊 Dashboard & Reporting lanjutan

---

## 🛠 Teknologi yang Digunakan

- Laravel 12
- Livewire 3
- Spatie Permission
- Laravel Excel & PDF

---

## 🧱 Pendekatan Arsitektur

- Struktur folder modular
- Pemisahan business logic menggunakan Service Layer
- Optimasi query & clean URL handling
- Komponen Livewire terstruktur
- Sistem permission yang scalable

---

## 📅 Roadmap Pengembangan

- [x] Struktur inti data karyawan
- [x] Sistem mutasi & riwayat kerja
- [x] Role & permission management
- [x] Fitur export data
- [ ] Modul absensi
- [ ] Sistem cuti & izin
- [ ] Sistem payroll
- [ ] Dashboard laporan

---

## 📌 Catatan

Aplikasi ini masih dalam tahap pengembangan aktif.
Struktur dan fitur dapat mengalami perubahan seiring proses pengembangan.
Dibangun dengan tujuan untuk **belajar** Laravel dengan kasus HR.
