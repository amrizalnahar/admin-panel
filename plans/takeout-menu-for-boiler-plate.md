# Plan: Takeout Menu untuk Boilerplate Admin Panel

## Tujuan
Mengubah aplikasi existing (CMS kampanye desa) menjadi **boilerplate admin panel yang reusable** dengan hanya menyisakan menu inti. Fitur spesifik kampanye desa dihapus, struktur dasar admin & autentikasi dipertahankan.

---

## Hasil Konfirmasi
| Pertanyaan | Keputusan |
|-----------|-----------|
| Dashboard & Analytics Chart | **Dashboard dipertahankan**, Analytics Chart **dihapus** |
| Moderasi Konten (ModerationWordManager) | **Dipertahankan** — dijadikan fitur moderasi kata umum |
| Public Frontend & HTML Prototypes | **Public frontend:** hapus konten spesifik, simpan skeleton (layout, partials). **HTML prototypes:** hapus total |

---

## 1. Menu & Route yang Dihapus

### Dihapus Total (Livewire + Route)
| Route | Component | Alasan |
|-------|-----------|--------|
| `/admin/analytics` | `AnalyticsChart` | Analytics Chart dihapus |
| `/admin/profil` | `ProfilManager` | Konten spesifik profil kandidat |
| `/admin/visi-misi` | `VisiMisiManager` | Konten spesifik kampanye |
| `/admin/program-kerja` | `ProgramManager` | Konten spesifik kampanye |
| `/admin/catatan` | `CatatanTable` | Konten publikasi non-berita |
| `/admin/catatan/create` | `CatatanForm` | Konten publikasi non-berita |
| `/admin/catatan/{note}/edit` | `CatatanForm` | Konten publikasi non-berita |
| `/admin/laporan` | `LaporanTable` | Konten transparansi spesifik |
| `/admin/laporan/create` | `LaporanForm` | Konten transparansi spesifik |
| `/admin/laporan/{report}/edit` | `LaporanForm` | Konten transparansi spesifik |
| `/admin/galeri` | `GaleriManager` | Galeri spesifik kampanye |
| `/admin/galeri/{album}/edit` | `AlbumEditor` | Galeri spesifik kampanye |
| `/admin/aspirasi` | `AspirasiTable` | Aspirasi warga spesifik desa |
| `/admin/aspirasi/export/download` | Closure | Export aspirasi |
| `/admin/aspirasi/{aspiration}` | `AspirasiDetail` | Detail aspirasi |

### Route Public yang Dihapus
| Route | Controller |
|-------|------------|
| `/` | `BerandaController` |
| `/profil` | `ProfilController` |
| `/visi-misi` | `ProfilController` |
| `/program-kerja` | `ProfilController` |
| `/catatan` | `CatatanController` |
| `/catatan/{slug}` | `CatatanController` |
| `/transparansi` | `TransparansiController` |
| `/galeri` | `GaleriController` |
| `/aspirasi` | `AspirasiController` |
| `/aspirasi` (POST) | `AspirasiController` |
| `/sitemap.xml` | `SitemapController` |

**Route public yang dipertahankan:**
- `/berita` & `/berita/{slug}` → `BeritaController` (karena Berita CRUD dipertahankan)
- `/auth/public-key`, `/robots.txt`, auth routes Breeze

---

## 2. Livewire Components (Class + View) yang Dihapus

### Hapus Class di `app/Livewire/Admin/`
- `AnalyticsChart.php`
- `ProfilManager.php`
- `VisiMisiManager.php`
- `ProgramManager.php`
- `CatatanTable.php`
- `CatatanForm.php`
- `LaporanTable.php`
- `LaporanForm.php`
- `GaleriManager.php`
- `AlbumEditor.php`
- `AspirasiTable.php`
- `AspirasiDetail.php`

### Hapus View di `resources/views/livewire/admin/`
- `analytics-chart.blade.php`
- `profil-manager.blade.php`
- `visi-misi-manager.blade.php`
- `program-manager.blade.php`
- `catatan-table.blade.php`
- `catatan-form.blade.php`
- `laporan-table.blade.php`
- `laporan-form.blade.php`
- `galeri-manager.blade.php`
- `album-editor.blade.php`
- `aspirasi-table.blade.php`
- `aspirasi-detail.blade.php`

---

## 3. Models yang Dihapus

Hapus file di `app/Models/`:
- `Aspiration.php`
- `CandidateProfile.php`
- `Gallery.php`
- `GalleryAlbum.php`
- `Mission.php`
- `ModerationWord.php` → **TIDAK DIHAPUS** (dipertahankan sebagai moderasi umum)
- `Note.php`
- `Program.php`
- `Report.php`
- `TrackRecord.php`
- `Visit.php` (terkait analytics yang dihapus)

**Models yang dipertahankan:**
`AuditTrail`, `Category`, `Post`, `ScheduleTask`, `ScheduleTaskExecution`, `SiteSetting`, `Tag`, `User`

---

## 4. Controllers yang Dihapus

### Hapus di `app/Http/Controllers/Public/`
- `AspirasiController.php`
- `BerandaController.php`
- `CatatanController.php`
- `GaleriController.php`
- `ProfilController.php`
- `TransparansiController.php`

### Pertahankan di `app/Http/Controllers/Public/`
- `BeritaController.php` (karena Berita dipertahankan)

### Hapus di `app/Http/Controllers/`
- `SitemapController.php` (konten public sudah berkurang drastis)

**Controllers yang dipertahankan:**
- `Controller.php`
- `PublicKeyController.php`
- `AuthenticatedSessionController.php`
- `VerifyEmailController.php`
- `BeritaController.php`

---

## 5. Views yang Dihapus / Disesuaikan

### Hapus Views Public Pages (`resources/views/pages/`)
Hapus semua file/folder berikut:
- `aspirasi.blade.php`
- `beranda.blade.php`
- `catatan/`
- `galeri.blade.php`
- `profil.blade.php`
- `program-kerja.blade.php`
- `transparansi.blade.php`
- `visi-misi.blade.php`

**Pertahankan:**
- `berita/index.blade.php`
- `berita/show.blade.php`

### Modifikasi Sidebar Admin
File: `resources/views/components/admin/sidebar.blade.php`
- Hapus section **Konten Profil** (Profil, Visi & Misi, Program Kerja)
- Hapus dari section **Konten Publikasi**: Catatan, Laporan, Galeri, Aspirasi
- Hapus **Analytics Chart** dari section Utama
- Section **Konten Publikasi** hanya menyisakan: **Berita**
- Section lain tetap: Master Data, Manajemen User, Konfigurasi, Monitoring

### Pertahankan Skeleton Frontend
Jangan hapus file-file berikut (struktur dasar):
- `resources/views/layouts/` (termasuk `admin.blade.php`, `app.blade.php`, `guest.blade.php`)
- `resources/views/components/` (kecuali komponen yang spesifik konten kampanye)
- `resources/views/livewire/` selain yang disebutkan di atas
- Partials, navigation, footer generic

---

## 6. Migrations yang Dihapus

Hapus file migration yang membuat tabel untuk model yang dihapus. Jangan jalankan rollback — hapus file saja karena ini akan jadi fresh boilerplate.

- `2026_04_25_071125_create_notes_table.php`
- `2026_04_25_071126_create_reports_table.php`
- `2026_04_25_071127_create_category_report_table.php`
- `2026_04_25_071128_create_gallery_albums_table.php`
- `2026_04_25_071129_create_galleries_table.php`
- `2026_04_25_071130_create_aspirations_table.php`
- `2026_04_25_071131_create_candidate_profiles_table.php`
- `2026_04_25_071132_create_track_records_table.php`
- `2026_04_25_071133_create_missions_table.php`
- `2026_04_25_071134_create_programs_table.php`
- `2026_04_25_071135_create_site_settings_table.php` → **PERTAHANKAN** (pengaturan)
- `2026_04_25_165647_add_amount_in_word_to_reports_table.php`
- `2026_04_25_184311_add_status_to_programs_table.php`
- `2026_04_26_134842_create_visits_table.php`
- `2026_04_27_005321_add_views_to_notes_table.php`
- `2026_04_28_164441_add_meta_columns_to_content_tables.php` → periksa, jika hanya untuk notes/reports bisa dihapus; jika juga untuk posts pertahankan
- `2026_04_28_181031_add_flagged_reason_to_aspirations_table.php`
- `2026_04_28_181036_create_moderation_words_table.php` → **PERTAHANKAN** (moderasi umum)

**Migrations yang dipertahankan:**
- Users, cache, jobs default Laravel
- Permission tables (Spatie)
- Audit trails
- Categories, tags, taggables
- Posts (berita)
- Site settings
- Schedule tasks
- Moderation words
- Add views to posts
- Add avatar to users
- Fix slug/category unique indexes

---

## 7. Seeders yang Dihapus / Dimodifikasi

### Hapus File Seeder
- `AspirationSeeder.php`
- `GallerySeeder.php`
- `ProfileSeeder.php`
- `ProgramSeeder.php`
- `VisitSeeder.php`

### Modifikasi `ContentSeeder.php`
Hapus bagian yang membuat data `Note`, `Report`, dan `Program`. Pertahankan bagian yang membuat data `Post` (berita).

### Pertahankan Seeders
- `DatabaseSeeder.php` (sesuaikan pemanggilannya)
- `RolePermissionSeeder.php`
- `SiteSettingSeeder.php`
- `MasterDataSeeder.php` (kategori & tags)
- `ModerationWordSeeder.php` (moderasi umum)

---

## 8. HTML Prototypes (`html/`)

**Hapus total.** Folder `html/` berisi static prototypes untuk 11 halaman public kampanye desa. Karena ini boilerplate admin panel, prototypes tidak relevan.

File & folder yang dihapus:
- Semua `.html` files di `html/`
- `html/css/` (jika isinya spesifik prototype)
- `html/js/` (jika isinya spesifik prototype)
- `html/fix_nav.py`
- `html/update_nav.py`

---

## 9. Policies & Traits (Pembersihan)

### Cek & Hapus Policies
Cek `app/Policies/` — hapus policy yang terkait model yang dihapus:
- `NotePolicy`, `ReportPolicy`, `ProgramPolicy`, `GalleryPolicy`, `AspirationPolicy`, `CandidateProfilePolicy` (jika ada)
- Pertahankan: `PostPolicy`, `UserPolicy`, `RolePolicy`, `CategoryPolicy`, `TagPolicy`, `SiteSettingPolicy`

### Cek Traits
Traits berikut masih dipakai oleh `Post` atau model yang tersisa — **pertahankan**:
- `HasAuditTrail`
- `HasSlug`
- `HasCategory`
- `HasTags`

Jika ada trait yang hanya dipakai model yang dihapus (misal trait khusus `Program` atau `Mission`), hapus.

---

## 10. Permission & Role (Penyesuaian)

### Hapus Permissions yang Tidak Digunakan
Di `RolePermissionSeeder.php` atau config permission, hapus permission terkait:
- `profile-list`, `profile-create`, `profile-edit`, `profile-delete`
- `missions-list`, `missions-create`, `missions-edit`, `missions-delete`
- `programs-list`, `programs-create`, `programs-edit`, `programs-delete`
- `notes-list`, `notes-create`, `notes-edit`, `notes-delete`
- `reports-list`, `reports-create`, `reports-edit`, `reports-delete`
- `gallery-list`, `gallery-create`, `gallery-edit`, `gallery-delete`
- `aspirations-list`, `aspirations-edit`, `aspirations-export`, `aspirations-delete`
- `analytics-view`

### Pertahankan Permissions
- `dashboard-access`
- `posts-list`, `posts-create`, `posts-edit`, `posts-delete`
- `categories-list`, `categories-create`, `categories-edit`, `categories-delete`
- `tags-list`, `tags-create`, `tags-edit`, `tags-delete`
- `users-list`, `users-create`, `users-edit`, `users-delete`
- `roles-list`, `roles-create`, `roles-edit`, `roles-delete`
- `settings-list`, `settings-edit`
- `audit-logs-list`
- `system-logs-list`
- `system-email-tester`
- `system-queue-monitor`
- `schedule-tasks-list`
- `moderation-manage`

---

## 11. Langkah Eksekusi (Urutan)

Eksekusi dilakukan dalam urutan berikut untuk menghindari error dependency:

### Phase 1: Hapus File yang Tidak Ber-dependency
1. Hapus HTML prototypes (`html/`)
2. Hapus views public pages yang tidak perlu (`resources/views/pages/`)
3. Hapus views Livewire yang tidak perlu (`resources/views/livewire/admin/`)
4. Hapus migration files yang tidak perlu (`database/migrations/`)
5. Hapus seeder files yang tidak perlu (`database/seeders/`)

### Phase 2: Hapus PHP Class
6. Hapus Livewire class components (`app/Livewire/Admin/`)
7. Hapus public controllers (`app/Http/Controllers/Public/`)
8. Hapus models (`app/Models/`)
9. Hapus policies (`app/Policies/`)
10. Hapus `SitemapController.php`

### Phase 3: Modifikasi Konfigurasi & Routing
11. Modifikasi `routes/web.php`:
    - Hapus route public yang tidak perlu
    - Hapus route admin yang tidak perlu
    - Pertahankan route berita public
12. Modifikasi `resources/views/components/admin/sidebar.blade.php`:
    - Hapus menu yang tidak perlu
    - Sesuaikan grouping
13. Modifikasi `database/seeders/DatabaseSeeder.php`:
    - Hapus pemanggilan seeder yang dihapus
14. Modifikasi `RolePermissionSeeder.php`:
    - Hapus permission & role assignment yang tidak perlu
15. Modifikasi `ContentSeeder.php`:
    - Hapus bagian Note, Report, Program

### Phase 4: Verifikasi
16. Jalankan `composer dump-autoload`
17. Jalankan `php artisan route:clear && php artisan view:clear && php artisan config:clear`
18. Jalankan `php artisan migrate:fresh --seed` untuk memastikan struktur database baru berjalan
19. Akses halaman admin dan verifikasi menu yang tersisa:
    - Dashboard
    - Berita (list, create, edit)
    - Kategori & Tags
    - Users & Roles
    - Pengaturan & Moderasi
    - Monitoring (Audit Log, System Logs, Email Tester, Queue Monitor, Schedule Tasks)

---

## 12. Catatan Penting

- **Soft Deletes & Audit Trail:** Model yang dihapus jangan lupa cek apakah ada referensi di `AuditTrail` model. Karena `AuditTrail` menyimpan `auditable_type` (polymorphic), record audit untuk model yang dihapus akan menjadi "orphan". Ini acceptable untuk boilerplate fresh, tapi pertimbangkan cleanup jika migrasi data existing.
- **Trix Editor:** Trix masih dipertahankan karena Berita menggunakan rich text.
- **ModerationWordManager:** Fitur ini dipertahankan dan bisa di-reuse untuk moderasi komentar/berita di masa depan.
- **Category Polymorphic:** Kategori tetap polymorphic (`module_type`). Meskipun sekarang hanya digunakan untuk `post`, struktur tetap dipertahankan agar reusable.
- **HasCategory & HasTags:** Trait ini masih dipakai oleh `Post` — jangan dihapus.
