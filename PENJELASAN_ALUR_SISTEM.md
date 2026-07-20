# PENJELASAN MENDETAIL ALUR KERJA SISTEM
## Sistem Manajemen Perubahan Harga Komoditas SULUT
**Badan Pusat Statistik (BPS) Provinsi Sulawesi Utara**

---

## 📋 DAFTAR ISI
1. [Pendahuluan & Konsep Utama](#1-pendahuluan--konsep-utama)
2. [Tahap 1: Input (Pemasukan Data)](#2-tahap-1-input-pemasukan-data)
3. [Tahap 2: Fungsi Input](#3-tahap-2-fungsi-input)
4. [Tahap 3: Proses Pengolahan Data (Backend Data Processing)](#4-tahap-3-proses-pengolahan-data-backend-data-processing)
5. [Tahap 4: Output (Keluaran Sistem)](#5-tahap-4-output-keluaran-sistem)
6. [Ringkasan Matriks Alasan & Status Laporan](#6-ringkasan-matriks-alasan--status-laporan)

---

## 1. PENDAHULUAN & KONSEP UTAMA

Sistem ini dibangun untuk mendokumentasikan, memantau, dan menganalisis dinamika perubahan harga komoditas di **15 Kabupaten/Kota se-Sulawesi Utara**. 

Sistem ini menghubungkan dua entitas pengguna utama:
1. **User BPS Provinsi**: Bertindak sebagai pengelola sistem (*administrator*), pengunggah data harga agregat dari survei/excel, peninjau (*reviewer*) alasan perubahan harga daerah, dan pengambil keputusan analisis makro.
2. **User BPS Kabupaten/Kota**: Bertindak sebagai pelapor daerah yang memverifikasi komoditas bergejolak di wilayahnya, memberikan narasi penjelasan kualitatif (alasan), faktor pendorong lokal, serta rekomendasi penanganan.

---

## 2. TAHAP 1: INPUT (PEMASUKAN DATA)

Sistem menerima 4 jenis input utama yang terbagi berdasarkan fungsinya:

### A. Input Data Harga via Excel (Bulk Upload)
- **Pelaksana**: User BPS Provinsi.
- **Bentuk Input**: File spreadsheet berformat `.xlsx` atau `.xls`.
- **Elemen Data**:
  - `Kode Komoditas` (misal: `001` untuk Beras, `007` untuk Cabai Rawit).
  - `Harga Level` (Nilai nominal harga atau nilai indeks).
  - `Inflasi MtM (%)` (Month-to-Month / bulanan).
  - `Inflasi YtD (%)` (Year-to-Date / kumulatif tahun berjalan).
  - `Inflasi YoY (%)` (Year-on-Year / tahunan).
  - `Andil MtM`, `Andil YtD`, `Andil YoY` (Sumbangan poin inflasi komoditas).
- **Parameter Pendukung**: Pemilihan `Periode` (Bulan & Tahun), `Wilayah` (Kabupaten/Kota), dan `Tipe Indeks` (IHK, IHPB, IPP, IPH).

### B. Input Data Harga Manual
- **Pelaksana**: User BPS Provinsi.
- **Bentuk Input**: Form web HTML interaktif.
- **Elemen Data**: Penginputan per satu komoditas secara langsung untuk merevisi atau menambahkan data harga yang belum tercover file Excel.

### C. Input Alasan Perubahan Harga (Laporan Daerah)
- **Pelaksana**: User BPS Kabupaten/Kota.
- **Bentuk Input**: Form laporan naratif pada komoditas yang mengalami gejolak harga ($|\text{Inflasi MtM}| \ge 1.0\%$).
- **Elemen Data**:
  - `Alasan Perubahan`: Deskripsi naratif penyebab perubahan harga (misal: "Pasokan cabai dari Gorontalo terhambat akibat cuaca buruk").
  - `Faktor Pendorong`: Pilihan multi-select (misal: *Gagal Panen*, *Kenaikan BBM*, *Permintaan Hari Raya*, *Kendala Transportasi*).
  - `Rekomendasi`: Saran langkah stabilisasi harga untuk pemerintah daerah (TPID).

### D. Input Master & Pengaturan Sistem
- **Pelaksana**: User BPS Provinsi.
- **Elemen Data**:
  - `Manajemen Periode`: Membuka/menutup periode laporan baru.
  - `Manajemen Komoditas`: Mengaktifkan/menonaktifkan komoditas yang dipantau.
  - `Manajemen User`: Mengelola akun dan reset password BPS Kabupaten/Kota.

---

## 3. TAHAP 2: FUNGSI INPUT

| Jenis Input | Fungsi Utama dalam Sistem | dampak Jika Tidak Ada Input |
| :--- | :--- | :--- |
| **Excel Data Harga** | Memberikan angka dasar kuantitatif harga, laju inflasi, dan nilai andil per komoditas per daerah. | Grafik, peta visualisasi, dan tabel relatif tidak akan menampilkan angka. |
| **Input Alasan Daerah** | Memberikan konteks kualitatif (alasan di balik angka) mengapa suatu komoditas harganya naik/turun tajam. | Provinsi hanya melihat angka tanpa mengetahui penyebab riil di lapangan. |
| **Faktor Pendorong** | Mengelompokkan pola penyebab inflasi untuk analisis kecenderungan secara otomatis (analisis tematik). | Analisis faktor pendorong harus dibaca manual satu per satu dari narasi. |
| **Manajemen Periode** | Mengunci data pada periode lalu agar tidak terjadi perubahan yang tidak sah (*data locking*). | Data historis berisiko tertimpa atau diubah tanpa sengaja. |

---

## 4. TAHAP 3: PROSES PENGOLAHAN DATA (BACKEND DATA PROCESSING)

Setelah data diinput ke dalam sistem, Laravel Backend mengeksekusi serangkaian algoritma pengolahan data:

```
[Input Excel / Form]
        │
        ▼
[1. Parsing & Normalisasi] ──► (Zero-padding kode komoditas & pembersihan desimal)
        │
        ▼
[2. Validasi Relasi] ───────► (Cek keberadaan Kode Komoditas & Wilayah)
        │
        ▼
[3. Penyimpanan & Indexing] ──► (UpdateOrCreate pada tabel data_hargas)
        │
        ▼
[4. Deteksi Anomali] ────────► (Cek apakah |Inflasi MtM| >= 1.0%)
        │
        ├── YES ──► [Flag: Wajib Diisi Alasan] ──► Notification & Task Notification
        └── NO  ──► [Flag: Stabil]
        │
        ▼
[5. Kalkulasi Agregat KPI] ──► (Avg Inflasi Provinsi, Progress Penyelesaian, Top 10 Andil)
```

### rincian Pengolahan Backend:
1. **Parsing & Sanitasi Angka (Excel Parser)**:
   - Membaca sel Excel tanpa format bawaan (`unformatted values`) untuk menghindari kesalahan pembacaan tanda koma/titik.
   - Menghapus karakter non-numerik (seperti "Rp" atau spasi).
   - Memastikan kode komoditas ter-format 3 digit (`str_pad($kode, 3, '0', STR_PAD_LEFT)`).

2. **Kalkulasi Ambang Batas Gejolak (Anomaly Thresholding)**:
   - Sistem memeriksa setiap angka inflasi dengan formula:
     $$\text{Status Signifikan} = \begin{cases} \mathbf{True}, & \text{jika } |\text{Inflasi MtM}| \ge 1.0\% \\ \mathbf{False}, & \text{jika } |\text{Inflasi MtM}| < 1.0\% \end{cases}$$
   - Jika `True`, sistem menetapkan komoditas tersebut wajib diberikan penjelasan alasan oleh Kabupaten/Kota terkait.

3. **Kalkulasi KPI Dashboard**:
   - **Progress Penyelesaian Wilayah (%)**:
     $$\text{Progress} = \frac{\text{Jumlah Alasan yang Diinput}}{\text{Total Komoditas Signifikan}} \times 100\%$$
   - **Rata-rata Inflasi Provinsi**:
     $$\text{Rata-rata} = \frac{\sum \text{Inflasi MtM}}{N}$$
   - **Pemeringkatan Komoditas Pendorong (Top 10)**:
     Data diurutkan berdasarkan `andil_mtm` tertinggi secara menurun (`orderByDesc`).

4. **Siklus Hidup Status Alasan (State Management Workflow)**:
   - `draft`: Alasan disimpan sementara oleh Kabupaten/Kota.
   - `submitted`: Alasan dikirim ke Provinsi untuk ditinjau.
   - `disetujui`: Provinsi menyetujui laporan alasan.
   - `revisi`: Provinsi mengembalikan laporan dengan catatan revisi (memicu notifikasi ke Kabupaten/Kota).

---

## 5. TAHAP 4: OUTPUT (KELUARAN SISTEM)

Sistem menghasilkan 4 bentuk utama keluaran (*output*) yang disajikan secara visual dan dapat diekspor:

### A. Dashboard Utama (Provinsi & Wilayah)
- **KPI Cards**: Menampilkan metrik utama (Total Komoditas Dipantau, Progress Penyelesaian %, Rata-rata Inflasi Provinsi, Pending Review).
- **Peta Interaktif Sulawesi Utara**: Peta spasial 15 Kabupaten/Kota dengan indikator warna status penyelesaian laporan daerah.
- **Top 10 Komoditas Pendorong Inflasi**: Tabel urutan komoditas dengan kontribusi andil terbesar.

### B. Visualisasi Data Interaktif
1. **Tabel Relatif (Matrix Cross-Tabulation)**:
   - Matriks persilangan `Komoditas × 15 Wilayah`.
   - Menampilkan perbandingan harga/inflasi antar kabupaten/kota dalam satu tabel besar.
2. **Output MtM (Heatmap Matrix)**:
   - Tabel bermata-warna (*heat-map*) untuk mengidentifikasi tingkat kenaikan/penurunan harga secara cepat:
     - 🔴 **Merah Tua**: Kenaikan Tinggi ($\ge 2.0\%$)
     - 🟠 **Merah Sedang**: Kenaikan Sedang ($1.0\% \text{ s/d } 1.99\%$)
     - 🟡 **Merah Muda**: Kenaikan Rendah ($0.1\% \text{ s/d } 0.99\%$)
     - ⚪ **Abu-abu**: Stabil ($0.0\%$)
     - 🟢 **Hijau Muda / Tua**: Penurunan / Deflasi ($-0.1\% \text{ s/d } \le -2.0\%$)
3. **Grafik Tren Komoditas (Line Chart Interaktif)**:
   - Visualisasi grafik pergerakan historis komoditas tertentu selama 12 bulan terakhir menggunakan library ECharts.

### C. Ekspor Dokumen & Laporan
- **Ekspor Excel (.xlsx)**: Mengunduh data tabel riwayat harga atau histori wilayah.
- **Ekspor PDF Rekapitulasi**: Mengunduh dokumen ringkasan resmi laporan bulanan komoditas dan alasannya untuk bahan rapat TPID / pimpinan.

---

## 6. RINGKASAN MATRIKS ALASAN & STATUS LAPORAN

```
[BPS KABUPATEN/KOTA]                            [BPS PROVINSI]
         │                                             │
         ├─► Input Alasan & Faktor ──► (Save Draft)    │
         │                                             │
         ├─► Submit Laporan ──────────────────────────►│ (Status: submitted)
         │                                             │
         │                                             ├─► Peninjauan Laporan
         │                                             │         │
         │◄── (Terima Revisi & Catatan) ◄──────────────┼─────────┴─► [Minta Revisi]
         │          │                                  │
         │          └─► Perbaiki Alasan ──► Resubmit ──►│
         │                                             │
         │◄── (Notifikasi Disetujui) ◄─────────────────┴───────────► [Setujui] (Status: disetujui)
```

---

*Dokumen ini dibuat sebagai panduan pemahaman alur kerja Sistem Manajemen Perubahan Harga Komoditas SULUT.*
