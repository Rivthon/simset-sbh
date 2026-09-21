# Dokumentasi Black Box Testing SIMSET SBH

Dokumentasi ini disusun berdasarkan fitur yang benar-benar terimplementasi pada route, controller, model, view, dan feature test project Laravel SIMSET SBH.

| No | Skenario Pengujian | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|---:|---|---|---|---|
| 1 | Pengguna membuka halaman utama `/`. | Sistem menampilkan halaman welcome aplikasi. | Sesuai implementasi. | Berhasil. |
| 2 | Pengguna membuka halaman login. | Sistem menampilkan form login. | Sesuai implementasi. | Berhasil. |
| 3 | Pengguna login dengan username dan password aktif yang valid. | Sistem mengautentikasi pengguna dan mengarahkan ke dashboard. | Sesuai implementasi. | Berhasil. |
| 4 | Pengguna login dengan email dan password aktif yang valid. | Sistem mengautentikasi pengguna berdasarkan email dan mengarahkan ke dashboard. | Sesuai implementasi. | Berhasil. |
| 5 | Pengguna login dengan password salah. | Sistem menolak login dan menampilkan error pada field login. | Sesuai implementasi. | Berhasil. |
| 6 | Pengguna tidak login mengakses halaman dashboard. | Sistem mengarahkan pengguna ke halaman login. | Sesuai implementasi. | Berhasil. |
| 7 | Pengguna login melakukan logout. | Sesi pengguna dihapus dan sistem mengarahkan ke halaman login. | Sesuai implementasi. | Berhasil. |
| 8 | Pengguna membuka halaman lupa password. | Sistem menampilkan form permintaan reset password. | Sesuai implementasi. | Berhasil. |
| 9 | Pengguna mengirim email reset password yang terdaftar. | Sistem memproses pengiriman link reset password dan menampilkan pesan sukses bila berhasil dikirim. | Sesuai implementasi. | Berhasil. |
| 10 | Pengguna mengirim email reset password yang tidak terdaftar. | Sistem menolak input karena email harus ada pada tabel users. | Sesuai implementasi. | Berhasil. |
| 11 | Pengguna membuka link reset password dengan token. | Sistem menampilkan form reset password berisi token dan email dari query. | Sesuai implementasi. | Berhasil. |
| 12 | Pengguna mereset password dengan token, email, password, dan konfirmasi valid. | Sistem memperbarui password dan mengarahkan ke halaman login dengan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 13 | Admin membuka dashboard. | Sistem menampilkan dashboard admin dengan statistik aset, unit, user, penyimpanan, stock opname, dan penggantian alat. | Sesuai implementasi. | Berhasil. |
| 14 | Pengelola membuka dashboard. | Sistem menampilkan dashboard pengelola dengan data yang dibatasi pada unit miliknya. | Sesuai implementasi. | Berhasil. |
| 15 | Pimpinan membuka dashboard. | Sistem menampilkan dashboard pimpinan dengan ringkasan monitoring. | Sesuai implementasi. | Berhasil. |
| 16 | Pengguna tanpa role yang sesuai mengakses menu terbatas. | Sistem menolak akses dengan status 403. | Sesuai implementasi. | Berhasil. |
| 17 | Tamu mengakses halaman admin seperti data unit. | Sistem mengarahkan ke halaman login. | Sesuai implementasi. | Berhasil. |
| 18 | Admin membuka daftar unit. | Sistem menampilkan daftar unit terurut terbaru dengan pagination. | Sesuai implementasi. | Berhasil. |
| 19 | Admin menambah unit dengan nama dan status valid. | Sistem menyimpan unit, membuat kode unit otomatis, dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 20 | Admin menambah unit dengan nama yang menghasilkan kode sama. | Sistem tetap membuat kode unit unik dengan penomoran tambahan. | Sesuai implementasi. | Berhasil. |
| 21 | Admin menambah unit tanpa field wajib. | Sistem menampilkan error validasi untuk nama dan status. | Sesuai implementasi. | Berhasil. |
| 22 | Admin memperbarui data unit. | Sistem menyimpan perubahan dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 23 | Admin menghapus unit. | Sistem menghapus data unit dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 24 | Admin membuka daftar user. | Sistem menampilkan daftar user beserta unit dan urutan role. | Sesuai implementasi. | Berhasil. |
| 25 | Admin menambah user dengan data valid. | Sistem menyimpan user baru dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 26 | Admin menambah user dengan username atau email yang sudah dipakai. | Sistem menolak input karena username/email harus unik. | Sesuai implementasi. | Berhasil. |
| 27 | Admin memperbarui user tanpa mengisi password. | Sistem memperbarui data lain dan mempertahankan password lama. | Sesuai implementasi. | Berhasil. |
| 28 | Admin menghapus akun yang sedang login. | Sistem menolak penghapusan dan menampilkan pesan error. | Sesuai implementasi. | Berhasil. |
| 29 | Admin atau pengelola membuka daftar kategori. | Sistem menampilkan kategori yang dapat diakses; pengelola hanya melihat kategori global atau unitnya. | Sesuai implementasi. | Berhasil. |
| 30 | Admin atau pengelola menambah kategori valid. | Sistem menyimpan kategori dan menampilkan pesan sukses; pengelola otomatis memakai unitnya. | Sesuai implementasi. | Berhasil. |
| 31 | Pimpinan mencoba membuka form tambah kategori. | Sistem menolak akses karena role read-only. | Sesuai implementasi. | Berhasil. |
| 32 | Pengelola mengedit kategori milik unit lain. | Sistem menolak akses dengan status 403. | Sesuai implementasi. | Berhasil. |
| 33 | Admin atau pengelola membuka daftar lokasi. | Sistem menampilkan lokasi yang dapat diakses; pengelola hanya melihat lokasi unitnya. | Sesuai implementasi. | Berhasil. |
| 34 | Admin atau pengelola menambah lokasi valid. | Sistem menyimpan lokasi dan menampilkan pesan sukses; pengelola otomatis memakai unitnya. | Sesuai implementasi. | Berhasil. |
| 35 | Pengelola mengedit lokasi unit lain. | Sistem menolak akses dengan status 403. | Sesuai implementasi. | Berhasil. |
| 36 | Admin, pengelola, atau pimpinan membuka daftar penyimpanan. | Sistem menampilkan daftar penyimpanan sesuai hak akses unit. | Sesuai implementasi. | Berhasil. |
| 37 | Admin atau pengelola menambah penyimpanan valid. | Sistem menyimpan penyimpanan, membuat kode penyimpanan dan QR otomatis, lalu menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 38 | Admin atau pengelola menambah penyimpanan dengan lokasi yang tidak sesuai unit. | Sistem menolak input dengan error bahwa lokasi harus berada pada unit yang dipilih. | Sesuai implementasi. | Berhasil. |
| 39 | Admin, pengelola, atau pimpinan membuka detail penyimpanan. | Sistem menampilkan detail penyimpanan, aset di dalamnya, aset yang bisa dimasukkan, dan data QR bila tersedia. | Sesuai implementasi. | Berhasil. |
| 40 | Admin atau pengelola memasukkan aset ke penyimpanan dengan unit yang sama. | Sistem memperbarui penyimpanan aset dan lokasi aset mengikuti lokasi penyimpanan. | Sesuai implementasi. | Berhasil. |
| 41 | Admin atau pengelola memasukkan aset dari unit berbeda ke penyimpanan. | Sistem menolak karena aset harus berada pada unit yang sama dengan penyimpanan. | Sesuai implementasi. | Berhasil. |
| 42 | Admin atau pengelola menghapus penyimpanan yang masih memiliki aset. | Sistem menolak penghapusan dan menampilkan pesan error. | Sesuai implementasi. | Berhasil. |
| 43 | Admin atau pengelola membuat QR penyimpanan. | Sistem membuat QR bila belum ada dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 44 | Admin, pengelola, atau pimpinan membuka daftar aset. | Sistem menampilkan daftar aset sesuai hak akses unit dengan pagination. | Sesuai implementasi. | Berhasil. |
| 45 | Pengguna memfilter aset berdasarkan keyword. | Sistem menampilkan aset yang cocok dengan nama, kode aset sistem, kode aset lama, atau QR. | Sesuai implementasi. | Berhasil. |
| 46 | Pengguna memfilter aset berdasarkan unit, kategori, lokasi, penyimpanan, kondisi, atau tipe identifikasi. | Sistem menampilkan aset sesuai filter dan hak akses pengguna. | Sesuai implementasi. | Berhasil. |
| 47 | Admin atau pengelola menambah aset valid. | Sistem menyimpan aset, membuat kode aset sistem, membuat QR, dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 48 | Admin atau pengelola menambah aset tanpa field wajib. | Sistem menampilkan error validasi untuk unit, kategori, lokasi, nama, jumlah, satuan, tipe identifikasi, dan kondisi sesuai input yang kosong. | Sesuai implementasi. | Berhasil. |
| 49 | Admin atau pengelola menambah aset dengan satuan "lainnya" tanpa satuan manual. | Sistem menolak input dan meminta satuan manual. | Sesuai implementasi. | Berhasil. |
| 50 | Admin atau pengelola menambah aset yang terindikasi BHP/bahan habis pakai. | Sistem menolak input dengan pesan bahwa BHP tidak boleh masuk data aset utama. | Sesuai implementasi. | Berhasil. |
| 51 | Pengelola menambah aset untuk unit lain. | Sistem memaksa unit aset menjadi unit pengelola atau menolak akses bila referensi tidak sesuai. | Sesuai implementasi. | Berhasil. |
| 52 | Admin atau pengelola menambah aset dengan penyimpanan valid. | Sistem mengisi lokasi dan unit aset mengikuti penyimpanan. | Sesuai implementasi. | Berhasil. |
| 53 | Pengelola membuka detail aset dari unit lain. | Sistem menolak akses dengan status 403. | Sesuai implementasi. | Berhasil. |
| 54 | Admin atau pengelola memperbarui aset valid. | Sistem menyimpan perubahan dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 55 | Admin menghapus aset. | Sistem menghapus aset dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 56 | Pengelola menghapus aset yang sudah memiliki riwayat stock opname. | Sistem menolak penghapusan karena aset memiliki riwayat transaksi. | Sesuai implementasi. | Berhasil. |
| 57 | Admin, pengelola, atau pimpinan membuka halaman QR aset. | Sistem menampilkan QR aset bila QR tersedia dan user berhak mengakses unit aset. | Sesuai implementasi. | Berhasil. |
| 58 | Admin atau pengelola membuat QR aset yang belum memiliki QR. | Sistem membuat QR aset dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 59 | Admin atau pengelola membuka halaman import aset. | Sistem menampilkan halaman import beserta referensi unit, kategori, lokasi, dan penyimpanan sesuai hak akses. | Sesuai implementasi. | Berhasil. |
| 60 | Admin atau pengelola mengunduh template import aset. | Sistem mengunduh file Excel template `template-import-aset.xlsx`. | Sesuai implementasi. | Berhasil. |
| 61 | Admin mengimport aset dari file Excel valid dan memilih unit. | Sistem menyimpan baris valid sebagai aset baru dan menampilkan jumlah aset berhasil/import ditolak. | Sesuai implementasi. | Berhasil. |
| 62 | Pengelola mengimport aset dari file Excel valid. | Sistem menggunakan unit pengelola sebagai unit import dan menyimpan baris valid. | Sesuai implementasi. | Berhasil. |
| 63 | Pengguna mengimport file aset kosong atau tanpa header. | Sistem menolak import dan menampilkan pesan file kosong/header tidak ditemukan. | Sesuai implementasi. | Berhasil. |
| 64 | Pengguna mengimport aset dengan kategori/lokasi/penyimpanan yang tidak ada. | Sistem menolak baris terkait dan menampilkan daftar error import. | Sesuai implementasi. | Berhasil. |
| 65 | Pengguna mengimport aset dengan kode aset lama duplikat dalam unit yang sama. | Sistem menolak baris terkait dengan pesan kode aset lama sudah ada. | Sesuai implementasi. | Berhasil. |
| 66 | Pengguna mengexport data aset ke Excel. | Sistem mengunduh file `.xlsx` dengan header data aset dan nama file sesuai filter unit bila ada. | Sesuai implementasi. | Berhasil. |
| 67 | Pengguna membuka halaman scan QR manual setelah login. | Sistem menampilkan form scan/input kode QR. | Sesuai implementasi. | Berhasil. |
| 68 | Pengguna memasukkan kode QR aset yang valid. | Sistem mengarahkan ke detail aset. | Sesuai implementasi. | Berhasil. |
| 69 | Pengguna memasukkan kode QR penyimpanan yang valid. | Sistem mengarahkan ke detail penyimpanan. | Sesuai implementasi. | Berhasil. |
| 70 | Pengguna memasukkan kode QR yang tidak ditemukan. | Sistem kembali ke form scan dengan pesan kode tidak ditemukan. | Sesuai implementasi. | Berhasil. |
| 71 | Pengguna scan URL QR aset publik `/qr/assets/{qrCode}` yang valid. | Sistem menampilkan halaman hasil scan berisi detail aset. | Sesuai implementasi. | Berhasil. |
| 72 | Pengguna scan URL QR penyimpanan publik `/qr/storages/{qrCode}` yang valid. | Sistem menampilkan halaman hasil scan berisi detail penyimpanan dan aset di dalamnya. | Sesuai implementasi. | Berhasil. |
| 73 | Pengguna scan QR publik yang tidak ditemukan. | Sistem menampilkan halaman scan-not-found berisi kode yang dicari. | Sesuai implementasi. | Berhasil. |
| 74 | Pengelola membuka hasil scan aset dari unit lain. | Sistem menolak akses dengan status 403 atau pesan tidak memiliki akses. | Sesuai implementasi. | Berhasil. |
| 75 | Admin, pengelola, atau pimpinan membuka daftar stock opname. | Sistem menampilkan sesi stock opname sesuai hak akses unit. | Sesuai implementasi. | Berhasil. |
| 76 | Admin atau pengelola membuat sesi stock opname valid. | Sistem membuat sesi dengan status `ongoing`, membuat item pemeriksaan dari aset unit tersebut, dan menampilkan pesan sukses. | Sesuai implementasi. | Berhasil. |
| 77 | Pengelola membuat sesi stock opname untuk unit lain. | Sistem menolak akses dengan status 403. | Sesuai implementasi. | Berhasil. |
| 78 | Pengguna membuat sesi stock opname duplikat untuk unit, semester, dan tahun akademik yang sama. | Sistem menolak pembuatan sesi dan menampilkan pesan bahwa sesi sudah ada. | Sesuai implementasi. | Berhasil. |
| 79 | Pengguna membuka detail sesi stock opname berjalan. | Sistem menampilkan daftar item aset, progress total, sudah diperiksa, belum diperiksa, sesuai, dan tidak sesuai. | Sesuai implementasi. | Berhasil. |
| 80 | Admin atau pengelola menyimpan hasil pemeriksaan dengan total kondisi sama dengan jumlah aktual. | Sistem menyimpan jumlah aktual, rincian kondisi, dan hasil pemeriksaan. | Sesuai implementasi. | Berhasil. |
| 81 | Admin atau pengelola menyimpan hasil pemeriksaan dengan total kondisi tidak sama dengan jumlah aktual. | Sistem menolak dan menampilkan pesan bahwa total jumlah kondisi harus sama dengan jumlah aktual. | Sesuai implementasi. | Berhasil. |
| 82 | Admin atau pengelola menyelesaikan sesi ketika masih ada aset belum diperiksa. | Sistem menolak penyelesaian dan menampilkan jumlah aset yang belum diperiksa. | Sesuai implementasi. | Berhasil. |
| 83 | Admin atau pengelola menyelesaikan sesi ketika semua aset sudah diperiksa. | Sistem mengubah status sesi menjadi `completed` dan memperbarui kondisi aset berdasarkan hasil pemeriksaan. | Sesuai implementasi. | Berhasil. |
| 84 | Admin atau pengelola mencoba mengubah sesi stock opname yang sudah selesai. | Sistem menolak perubahan karena sesi selesai tidak bisa diubah/di-scan/import. | Sesuai implementasi. | Berhasil. |
| 85 | Admin atau pengelola membuka form scan stock opname. | Sistem menampilkan form scan dengan progress pemeriksaan dan konteks lokasi bila ada. | Sesuai implementasi. | Berhasil. |
| 86 | Admin atau pengelola scan kode aset pada sesi stock opname yang sesuai unit. | Sistem menampilkan form pemeriksaan untuk aset tersebut dan memastikan item tidak dibuat duplikat. | Sesuai implementasi. | Berhasil. |
| 87 | Admin atau pengelola scan kode aset dari unit berbeda pada sesi stock opname. | Sistem menolak dan menampilkan pesan bahwa aset tidak termasuk unit pemeriksaan. | Sesuai implementasi. | Berhasil. |
| 88 | Admin atau pengelola scan penyimpanan pada sesi stock opname yang sesuai unit. | Sistem menampilkan daftar item aset dalam penyimpanan untuk diperiksa. | Sesuai implementasi. | Berhasil. |
| 89 | Admin atau pengelola scan kode yang tidak ditemukan pada sesi stock opname. | Sistem kembali ke form scan dengan pesan kode QR atau kode aset tidak ditemukan. | Sesuai implementasi. | Berhasil. |
| 90 | Admin atau pengelola membuka form import kondisi stock opname. | Sistem menampilkan halaman import kondisi untuk sesi yang belum selesai. | Sesuai implementasi. | Berhasil. |
| 91 | Admin atau pengelola mengunduh template import kondisi stock opname. | Sistem mengunduh file Excel `template-import-kondisi-stock-opname.xlsx`. | Sesuai implementasi. | Berhasil. |
| 92 | Admin atau pengelola mengimport kondisi stock opname dari file valid. | Sistem memperbarui item pemeriksaan dan menampilkan jumlah hasil pemeriksaan yang berhasil diimport. | Sesuai implementasi. | Berhasil. |
| 93 | Admin atau pengelola mengimport kondisi dengan kode aset tidak ditemukan atau bukan milik unit/lokasi pemeriksaan. | Sistem menolak import dan menampilkan daftar error. | Sesuai implementasi. | Berhasil. |
| 94 | Pengguna publik membuka form penggantian alat rusak untuk aset. | Sistem menampilkan form pengajuan penggantian alat dengan informasi aset. | Sesuai implementasi. | Berhasil. |
| 95 | Pengguna publik mengirim pengajuan penggantian alat valid. | Sistem menyimpan pengajuan, membuat kode penggantian, status awal `menunggu_verifikasi`, dan mengarahkan ke halaman berhasil. | Sesuai implementasi. | Berhasil. |
| 96 | Pengguna publik mengirim pengajuan dengan jumlah penggantian melebihi jumlah aset. | Sistem menolak input karena jumlah maksimal mengikuti quantity aset. | Sesuai implementasi. | Berhasil. |
| 97 | Pengguna publik mengunggah foto kerusakan valid pada pengajuan. | Sistem menyimpan file foto ke storage publik dan menyimpan path pada pengajuan. | Sesuai implementasi. | Berhasil. |
| 98 | Pengguna publik membuka halaman cek status penggantian. | Sistem menampilkan form input kode penggantian dan NIM. | Sesuai implementasi. | Berhasil. |
| 99 | Pengguna publik mengecek status dengan kode dan NIM yang cocok. | Sistem menampilkan data pengajuan beserta status dan pesan status. | Sesuai implementasi. | Berhasil. |
| 100 | Pengguna publik mengecek status dengan kode atau NIM yang tidak cocok. | Sistem menampilkan halaman hasil pencarian tanpa data pengajuan. | Sesuai implementasi. | Berhasil. |
| 101 | Admin, pengelola, atau pimpinan membuka daftar penggantian alat. | Sistem menampilkan daftar pengajuan sesuai hak akses unit dengan filter status, tanggal kejadian, mahasiswa, praktikum, dan aset. | Sesuai implementasi. | Berhasil. |
| 102 | Admin, pengelola, atau pimpinan membuka detail penggantian alat yang dapat diakses. | Sistem menampilkan detail pengajuan, aset, unit, verifier, dan pilihan status. | Sesuai implementasi. | Berhasil. |
| 103 | Pengelola membuka pengajuan penggantian alat dari unit lain. | Sistem menolak akses dengan status 403. | Sesuai implementasi. | Berhasil. |
| 104 | Admin atau pengelola mengubah status penggantian menjadi `menunggu_penggantian`. | Sistem menyimpan status, verifier, waktu verifikasi, dan catatan laboran bila ada. | Sesuai implementasi. | Berhasil. |
| 105 | Admin atau pengelola mengubah status penggantian menjadi `sudah_diganti`. | Sistem menyimpan status, verifier, waktu verifikasi, dan waktu penerimaan. | Sesuai implementasi. | Berhasil. |
| 106 | Admin atau pengelola mengubah status penggantian menjadi `ditolak` tanpa alasan penolakan. | Sistem menolak input karena alasan penolakan wajib diisi. | Sesuai implementasi. | Berhasil. |
| 107 | Pimpinan mencoba mengubah status penggantian alat. | Sistem menolak akses karena pimpinan bersifat monitoring/read-only. | Sesuai implementasi. | Berhasil. |
| 108 | Admin, pengelola, atau pimpinan membuka halaman laporan. | Sistem menampilkan laporan aset dengan total, rekap kondisi, rekap unit, kategori, lokasi, penyimpanan, dan daftar aset. | Sesuai implementasi. | Berhasil. |
| 109 | Pengguna membuka laporan tanpa memilih `report_scope=all`. | Sistem menampilkan laporan default untuk aset yang membutuhkan perhatian, yaitu kondisi sedang, rusak, atau hilang. | Sesuai implementasi. | Berhasil. |
| 110 | Pengguna membuka laporan dengan `report_scope=all`. | Sistem menampilkan laporan seluruh aset sesuai filter. | Sesuai implementasi. | Berhasil. |
| 111 | Pengguna memfilter laporan berdasarkan keyword, unit, kategori, lokasi, penyimpanan, atau kondisi aset. | Sistem menghitung dan menampilkan laporan sesuai filter dan hak akses pengguna. | Sesuai implementasi. | Berhasil. |
| 112 | Pimpinan membuka laporan dan mencoba membuka form tambah aset. | Sistem mengizinkan laporan tetapi menolak akses form tambah aset. | Sesuai implementasi. | Berhasil. |
