# Alur Pembuatan Database YBB CMS

## Alur Setup Database

### Fase 1: Database Pusat (Landlord) - Buat Terlebih Dahulu
```
1. tenants          ← Independen (buat pertama)
2. admins           ← Independen
3. domains          ← Tergantung pada: tenants
```

### Fase 2: Sistem Permission - Buat Sebelum Users
```
4. permissions      ← Independen
5. roles            ← Independen
6. role_has_permissions ← Tergantung pada: permissions, roles
```

### Fase 3: Database Tenant - Manajemen User
```
7. users            ← Independen di database tenant
8. model_has_roles  ← Tergantung pada: users, roles
9. model_has_permissions ← Tergantung pada: users, permissions
```

### Fase 4: Dasar Taksonomi & Konten
```
10. terms           ← Independen (self-referencing via parent_id)
11. posts           ← Tergantung pada: tenants, users (created_by)
12. term_post       ← Tergantung pada: terms, posts, tenants
```

### Fase 5: Tipe Konten Spesial
```
13. pt_program      ← Tergantung pada: posts, tenants
14. pt_job          ← Tergantung pada: posts, tenants
15. media           ← Tergantung pada: tenants (post_id dan uploaded_by opsional)
```

### Fase 6: Marketing & Monetisasi
```
16. ads             ← Tergantung pada: users, tenants
17. ad_impressions  ← Tergantung pada: ads, users (opsional), tenants
18. ad_clicks       ← Tergantung pada: ads, ad_impressions, users (opsional), tenants
```

### Fase 7: SEO & Manajemen Traffic
```
19. seo_landings    ← Tergantung pada: users, tenants
20. redirects       ← Tergantung pada: users (opsional), tenants
```

### Fase 8: Email Marketing
```
21. newsletter_subscriptions ← Tergantung pada: tenants
22. email_campaigns ← Tergantung pada: users, tenants
```

### Fase 9: Analytics
```
23. analytics_events ← Tergantung pada: users (opsional), tenants
24. failed_jobs      ← Independen (tabel sistem)
```

---

## Alur Pembuatan Konten Umum

### Membuat Beasiswa/Peluang:

1. **Buat tenant** (jika situs baru)
   - Isi konfigurasi: nama, domain, branding, SEO settings
   - Status: 'active'

2. **Buat user** (penulis/editor)
   - Role: 'admin', 'editor', atau 'author'
   - Assign permissions yang sesuai

3. **Buat terms** (opsional):
   - **Kategori**: "Beasiswa S2", "Beasiswa S3"
   - **Tag**: "STEM", "fully-funded", "international"
   - **Lokasi**: "Jerman", "Eropa"
   - **Skill**: "Pengalaman Riset", "Publikasi Jurnal"
   - **Industri**: "Teknologi", "Kesehatan"
   
4. **Buat post** (kind='program'):
   - Judul, slug, excerpt, content
   - Meta SEO: meta_title, meta_description, og_image
   - Status: 'draft'
   - created_by: user_id
   
5. **Buat record pt_program**:
   - Link ke post_id yang baru dibuat
   - Isi detail spesifik:
     - program_type: 'scholarship', 'opportunity', atau 'internship'
     - organizer_name: nama penyelenggara
     - location_text, country_code
     - deadline_at atau is_rolling
     - funding_type: 'fully_funded', 'partially_funded', 'unfunded'
     - stipend_amount, fee_amount
     - eligibility_text
     - apply_url
   
6. **Link terms** via `term_post`:
   - Hubungkan semua kategori yang relevan
   - Tambahkan tag-tag terkait
   - Set lokasi geografis
   - Associate skill yang dibutuhkan
   - Link industri terkait
   
7. **Upload media** (opsional):
   - Cover image untuk post
   - Lampiran dokumen (PDF, dll)
   - Gambar pendukung dalam content
   
8. **Update status post** → 'published'
   - Set published_at timestamp
   - Post akan muncul di frontend

### Membuat Lowongan Kerja:

1. **Buat tenant & user** (jika belum ada)

2. **Buat terms yang relevan**:
   - Kategori: "Lowongan Kerja", "Internship"
   - Tag: "remote-friendly", "startup", "multinational"
   - Lokasi: "Jakarta", "Indonesia", "Southeast Asia"
   - Skill: "Python", "JavaScript", "UI/UX Design"
   - Industri: "Teknologi", "Fintech", "E-commerce"

3. **Buat post** (kind='job'):
   - Judul pekerjaan
   - Deskripsi lengkap di content
   - Status: 'draft'

4. **Buat record pt_job**:
   - Link ke post_id
   - Detail pekerjaan:
     - company_name
     - employment_type: 'full_time', 'part_time', 'contract', 'internship'
     - workplace_type: 'onsite', 'hybrid', 'remote'
     - location_city, country_code
     - min_salary, max_salary, salary_currency, salary_period
     - experience_level: 'junior', 'mid', 'senior', 'lead'
     - responsibilities (longtext)
     - requirements (longtext)
     - benefits (JSON array)
     - deadline_at
     - apply_url

5. **Link terms**:
   - Kategori job
   - Skill yang dibutuhkan
   - Industri
   - Lokasi

6. **Upload logo perusahaan** ke media

7. **Publish** (update status → 'published')

### Membuat Halaman Landing SEO:

1. **Identifikasi target**:
   - Keyword: "Lowongan Kerja Python Remote di Eropa"
   - Target audience: Developer Python yang cari remote job

2. **Buat seo_landings**:
   ```json
   {
     "title": "Lowongan Kerja Python Remote di Eropa 2025",
     "slug": "lowongan-python-remote-eropa",
     "meta_description": "Temukan lowongan kerja Python remote terbaik...",
     "content_type": "jobs",
     "target_keyword": "lowongan python remote",
     "target_filters": {
       "skills": ["Python"],
       "location": ["Eropa"],
       "workplace_type": "remote"
     },
     "schema_markup": { /* Structured data JSON-LD */ }
   }
   ```

3. Halaman landing akan otomatis query posts/jobs yang match dengan filter

4. Monitor performance via `views` dan `conversion_rate`

### Membuat Kampanye Newsletter:

1. **Kumpulkan subscribers** via `newsletter_subscriptions`:
   - User subscribe dengan email
   - Set status: 'pending'
   - Kirim verification email
   - Update status → 'active' setelah verify

2. **Buat email_campaigns**:
   - name, subject, preview_text
   - content (HTML email)
   - type: 'newsletter', 'digest', 'announcement', 'promotional'
   - recipient_criteria (JSON filter subscribers)
   - scheduled_at untuk pengiriman terjadwal

3. **Kirim campaign**:
   - Status: 'draft' → 'scheduled' → 'sending' → 'sent'
   - Track metrics: emails_sent, emails_delivered, emails_opened, emails_clicked

### Setup Iklan:

1. **Buat ad** di tabel `ads`:
   - title, description
   - placement: 'header', 'sidebar', 'content', 'footer'
   - content (JSON dengan URL gambar, link, dll)
   - targeting (JSON: lokasi, device, waktu)
   - start_date, end_date
   - max_impressions, max_clicks

2. **Track performance**:
   - Setiap ad view → insert ke `ad_impressions`
   - Setiap click → insert ke `ad_clicks`
   - Update counters: current_impressions, current_clicks, click_rate

---

## Dependensi Penting yang Perlu Diingat:

### Aturan Urutan Pembuatan:

✅ **HARUS DIBUAT LEBIH DULU:**
- `tenants` sebelum semua tabel tenant
- `users` sebelum membuat konten (posts)
- `posts` sebelum `pt_program` atau `pt_job`
- `terms` sebelum `term_post`
- `ads` sebelum `ad_impressions` dan `ad_clicks`

✅ **BISA DIBUAT KAPAN SAJA:**
- `media` (post_id opsional, bisa independent)
- `redirects` (created_by opsional)
- `analytics_events` (user_id opsional untuk tracking)
- `newsletter_subscriptions` (independent dari posts)

### Pola Umum:

1. **Posts adalah pusat** - Program dan Job memperluas posts dengan relasi 1:1
2. **Terms sebelum linking** - Buat taksonomi dulu sebelum dihubungkan ke posts
3. **Users sebelum konten** - Semua konten butuh referensi `created_by`
4. **Tenant selalu pertama** - Semua data terisolasi per tenant
5. **Media bersifat opsional** - Tidak wajib, bisa ditambahkan kemudian

### Field Opsional vs Required:

**Field yang NULLABLE (opsional):**
- `posts.created_by` - NULL jika user dihapus (ON DELETE SET NULL)
- `posts.updated_by` - NULL jika user dihapus
- `media.post_id` - NULL untuk media yang tidak terkait post
- `media.uploaded_by` - NULL jika user dihapus
- `redirects.created_by` - NULL jika user dihapus
- `ad_impressions.user_id` - NULL untuk anonymous users
- `ad_clicks.user_id` - NULL untuk anonymous users
- `ad_clicks.impression_id` - NULL jika impression data tidak ada
- `analytics_events.user_id` - NULL untuk anonymous tracking

**Field yang REQUIRED (NOT NULL):**
- `pt_program.post_id` - Harus terkait dengan post
- `pt_job.post_id` - Harus terkait dengan post
- `ads.created_by` - Harus ada pembuat iklan
- `seo_landings.created_by` - Harus ada pembuat
- `email_campaigns.created_by` - Harus ada pembuat

### Tips Performa:

- Gunakan **batch insert** untuk terms jika banyak
- **Index** sudah dioptimasi untuk query umum
- Gunakan **FULLTEXT search** pada posts untuk pencarian konten
- **Cache** term counts untuk performa listing
- **Partition** analytics tables berdasarkan tanggal untuk data besar

---

## Contoh Flow Lengkap: Publikasi Beasiswa

```sql
-- 1. Insert tenant (sudah ada)
INSERT INTO tenants (id, name, domain, status) 
VALUES (UUID(), 'Beasiswa Indonesia', 'beasiswa.id', 'active');

-- 2. Insert user author
INSERT INTO users (id, name, email, password, role) 
VALUES (UUID(), 'Editor Beasiswa', 'editor@beasiswa.id', '$hash', 'editor');

-- 3. Insert terms
INSERT INTO terms (id, tenant_id, name, slug, type) VALUES
(UUID(), @tenant_id, 'Beasiswa S2', 'beasiswa-s2', 'category'),
(UUID(), @tenant_id, 'STEM', 'stem', 'tag'),
(UUID(), @tenant_id, 'Jerman', 'jerman', 'location'),
(UUID(), @tenant_id, 'Riset', 'riset', 'skill');

-- 4. Insert post
INSERT INTO posts (id, tenant_id, kind, title, slug, content, status, created_by) 
VALUES (
  UUID(), 
  @tenant_id, 
  'program', 
  'DAAD Scholarship 2025',
  'daad-scholarship-2025',
  'Full content...',
  'published',
  @user_id
);

-- 5. Insert pt_program
INSERT INTO pt_program (id, tenant_id, post_id, program_type, organizer_name, ...) 
VALUES (
  UUID(),
  @tenant_id,
  @post_id,
  'scholarship',
  'DAAD Germany',
  ...
);

-- 6. Link terms
INSERT INTO term_post (id, tenant_id, term_id, post_id) VALUES
(UUID(), @tenant_id, @category_id, @post_id),
(UUID(), @tenant_id, @tag_id, @post_id),
(UUID(), @tenant_id, @location_id, @post_id),
(UUID(), @tenant_id, @skill_id, @post_id);

-- 7. Upload media
INSERT INTO media (id, tenant_id, post_id, name, file_name, mime_type, size, uploaded_by, ...) 
VALUES (UUID(), @tenant_id, @post_id, 'Cover Image', 'daad-cover.jpg', 'image/jpeg', 102400, @user_id, ...);
```

---

## Troubleshooting Umum:

**Error: Foreign key constraint fails**
- Pastikan tenant_id, user_id, post_id sudah ada sebelum insert
- Check urutan pembuatan sesuai flow di atas

**Error: Duplicate entry for key 'unique'**
- Slug sudah dipakai untuk tenant yang sama
- Email user/admin sudah terdaftar
- Domain sudah digunakan tenant lain

**Performance lambat pada query posts**
- Gunakan index yang sudah ada
- Filter berdasarkan tenant_id + kind + status
- Gunakan FULLTEXT search untuk pencarian text

**Media tidak muncul**
- Pastikan post_id valid (jika dilink ke post)
- Check disk path sudah configured
- Verify uploaded_by user masih aktif (jika ada)
- Note: post_id dan uploaded_by bersifat opsional
