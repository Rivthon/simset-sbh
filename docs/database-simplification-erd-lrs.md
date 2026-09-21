# Rekomendasi Penyederhanaan Database untuk ERD dan LRS

Dokumen ini merangkum skema database aplikasi SIMSET SBH agar lebih mudah dijelaskan di skripsi. Fokusnya adalah model data inti sistem inventaris aset, bukan tabel teknis bawaan Laravel.

## 1. Tabel teknis yang tidak perlu masuk ERD utama

Tabel berikut boleh disebut sebagai tabel pendukung sistem, tetapi tidak wajib digambar di ERD/LRS utama karena bukan bagian langsung dari proses bisnis inventaris:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`

Untuk skripsi, cukup jelaskan bahwa tabel tersebut adalah tabel sistem/framework.

## 2. Skema inti yang sedang ada

Skema aplikasi saat ini terdiri dari beberapa kelompok:

### Master data

- `users`: data pengguna dan role.
- `units`: unit kerja/laboratorium.
- `categories`: kategori aset.
- `locations`: lokasi/ruangan.
- `containers`: tempat penyimpanan aset.

### Data utama

- `assets`: data aset inventaris.

### Transaksi/riwayat aset

- `asset_maintenances`: riwayat pemeliharaan.
- `asset_mutations`: riwayat mutasi aset.
- `asset_disposals`: riwayat penghapusan aset.
- `damage_reports`: laporan kerusakan aset.
- `inventory_checks`: header stock opname/inventarisasi.
- `inventory_check_items`: detail aset yang dicek saat stock opname.

## 3. Versi sederhana yang disarankan untuk ERD/LRS

Supaya ERD dan LRS lebih mudah dibaca, gunakan 10 tabel inti berikut:

1. `users`
2. `units`
3. `categories`
4. `locations`
5. `containers`
6. `assets`
7. `asset_histories`
8. `damage_reports`
9. `inventory_checks`
10. `inventory_check_items`

Catatan: `asset_histories` adalah penyederhanaan dari tiga tabel saat ini: `asset_maintenances`, `asset_mutations`, dan `asset_disposals`.

## 4. Penyederhanaan paling penting

### Gabungkan transaksi aset menjadi `asset_histories`

Saat ini database punya tiga tabel berbeda:

- `asset_maintenances`
- `asset_mutations`
- `asset_disposals`

Untuk ERD skripsi, ketiganya bisa disederhanakan menjadi satu tabel:

`asset_histories`

Kolom yang disarankan:

- `id`
- `asset_id`
- `user_id`
- `history_type`
- `from_unit_id`
- `to_unit_id`
- `from_location_id`
- `to_location_id`
- `history_date`
- `cost`
- `method`
- `description`
- `created_at`
- `updated_at`

Nilai `history_type`:

- `maintenance`
- `mutation`
- `disposal`

Manfaat:

- ERD lebih ringkas.
- LRS lebih mudah dijelaskan.
- Semua riwayat aset berada di satu relasi: `assets` ke `asset_histories`.
- Tidak perlu menggambar tiga tabel transaksi yang bentuknya mirip.

## 5. Relasi ERD versi sederhana

Relasi utama:

- `units` 1..n `users`
- `units` 1..n `locations`
- `units` 1..n `categories`
- `units` 1..n `assets`
- `categories` 1..n `assets`
- `locations` 1..n `containers`
- `locations` 1..n `assets`
- `containers` 1..n `assets`
- `users` 1..n `assets` sebagai pembuat/penginput
- `assets` 1..n `asset_histories`
- `assets` 1..n `damage_reports`
- `users` 1..n `damage_reports` sebagai pelapor
- `units` 1..n `inventory_checks`
- `users` 1..n `inventory_checks` sebagai petugas
- `inventory_checks` 1..n `inventory_check_items`
- `assets` 1..n `inventory_check_items`

## 6. LRS versi sederhana

### users

- `id` PK
- `unit_id` FK -> `units.id`, nullable
- `name`
- `username`
- `email`
- `password`
- `role`
- `position`
- `access_scope`
- `status`
- `created_at`
- `updated_at`

### units

- `id` PK
- `name`
- `code`
- `description`
- `status`
- `created_at`
- `updated_at`

### categories

- `id` PK
- `unit_id` FK -> `units.id`, nullable
- `name`
- `code`
- `description`
- `status`
- `created_at`
- `updated_at`

### locations

- `id` PK
- `unit_id` FK -> `units.id`
- `name`
- `code`
- `description`
- `status`
- `created_at`
- `updated_at`

### containers

- `id` PK
- `location_id` FK -> `locations.id`
- `name`
- `code`
- `qr_code`
- `description`
- `status`
- `created_at`
- `updated_at`

### assets

- `id` PK
- `unit_id` FK -> `units.id`
- `category_id` FK -> `categories.id`
- `location_id` FK -> `locations.id`
- `container_id` FK -> `containers.id`, nullable
- `created_by` FK -> `users.id`, nullable
- `asset_code`
- `legacy_inventory_code`
- `qr_code`
- `barcode_code`
- `name`
- `asset_type`
- `identification_type`
- `quantity`
- `brand`
- `model`
- `serial_number`
- `purchase_date`
- `purchase_price`
- `condition`
- `status`
- `photo`
- `description`
- `created_at`
- `updated_at`

### asset_histories

- `id` PK
- `asset_id` FK -> `assets.id`
- `user_id` FK -> `users.id`, nullable
- `history_type`
- `from_unit_id` FK -> `units.id`, nullable
- `to_unit_id` FK -> `units.id`, nullable
- `from_location_id` FK -> `locations.id`, nullable
- `to_location_id` FK -> `locations.id`, nullable
- `history_date`
- `cost`
- `method`
- `description`
- `created_at`
- `updated_at`

### damage_reports

- `id` PK
- `asset_id` FK -> `assets.id`
- `unit_id` FK -> `units.id`
- `reported_by` FK -> `users.id`
- `handled_by` FK -> `users.id`, nullable
- `report_code`
- `damage_date`
- `damage_description`
- `damage_photo`
- `priority`
- `status`
- `admin_note`
- `handled_at`
- `created_at`
- `updated_at`

### inventory_checks

- `id` PK
- `unit_id` FK -> `units.id`
- `checked_by` FK -> `users.id`
- `check_code`
- `semester`
- `academic_year`
- `check_date`
- `status`
- `notes`
- `created_at`
- `updated_at`

### inventory_check_items

- `id` PK
- `inventory_check_id` FK -> `inventory_checks.id`
- `asset_id` FK -> `assets.id`
- `system_quantity`
- `actual_quantity`
- `physical_status`
- `condition`
- `check_result`
- `checked_at`
- `notes`
- `created_at`
- `updated_at`

## 7. Jika ingin lebih sederhana lagi

Kalau dosen lebih suka ERD yang sangat ringkas, `containers` bisa tidak dijadikan tabel terpisah. Alternatifnya, tempat penyimpanan cukup menjadi kolom di `assets`, misalnya:

- `storage_name`
- `storage_code`

Namun, untuk aplikasi ini `containers` masih layak dipertahankan karena sudah punya QR code sendiri dan bisa berisi banyak aset.

## 8. Rekomendasi akhir

Untuk skripsi, gunakan skema 10 tabel inti:

- `users`
- `units`
- `categories`
- `locations`
- `containers`
- `assets`
- `asset_histories`
- `damage_reports`
- `inventory_checks`
- `inventory_check_items`

Jangan masukkan tabel teknis Laravel ke ERD utama. Jelaskan bahwa `asset_histories` adalah generalisasi dari proses pemeliharaan, mutasi, dan penghapusan aset.
