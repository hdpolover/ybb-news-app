<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tenant;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $beasiswa = Tenant::where('domain', 'beasiswa.id')->first();
        $techjobs = Tenant::where('domain', 'techjobs.com')->first();
        $ybb = Tenant::where('domain', 'ybb.id')->first();

        // Get users
        $budi = User::where('email', 'budi@beasiswa.id')->first();
        $sarah = User::where('email', 'sarah@techjobs.com')->first();
        $ahmad = User::where('email', 'ahmad@ybb.id')->first();

        // Create categories for each tenant
        $beasiswaCategory = Term::create([
            'tenant_id' => $beasiswa->id,
            'type' => 'category',
            'name' => 'Beasiswa S1',
            'slug' => 'beasiswa-s1',
            'color' => '#3B82F6',
        ]);

        $techCategory = Term::create([
            'tenant_id' => $techjobs->id,
            'type' => 'category',
            'name' => 'Software Engineering',
            'slug' => 'software-engineering',
            'color' => '#10B981',
        ]);

        $ybbCategory = Term::create([
            'tenant_id' => $ybb->id,
            'type' => 'category',
            'name' => 'Youth Programs',
            'slug' => 'youth-programs',
            'color' => '#F59E0B',
        ]);

        // Posts for Beasiswa Indonesia
        $beasiswaPosts = [
            [
                'title' => 'Beasiswa LPDP 2025: Panduan Lengkap Pendaftaran',
                'excerpt' => 'Pelajari syarat, tahapan, dan tips lolos seleksi beasiswa LPDP untuk kuliah di dalam dan luar negeri.',
                'content' => '<p>Beasiswa LPDP (Lembaga Pengelola Dana Pendidikan) adalah program beasiswa yang diselenggarakan oleh pemerintah Indonesia untuk mendanai pendidikan lanjutan bagi warga negara Indonesia.</p><p>Program ini mencakup berbagai jenjang pendidikan mulai dari S2, S3, hingga program profesional di dalam maupun luar negeri.</p>',
                'kind' => 'news',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Beasiswa Unggulan Kemendikbud: Syarat dan Cara Daftar',
                'excerpt' => 'Informasi terbaru tentang Beasiswa Unggulan Kemendikbud untuk mahasiswa berprestasi.',
                'content' => '<p>Beasiswa Unggulan Kemendikbud adalah program beasiswa yang diberikan kepada mahasiswa berprestasi untuk melanjutkan pendidikan S1, S2, dan S3.</p>',
                'kind' => 'guide',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Tips Menulis Motivation Letter untuk Beasiswa',
                'excerpt' => 'Panduan praktis menulis motivation letter yang menarik perhatian pemberi beasiswa.',
                'content' => '<p>Motivation letter adalah salah satu dokumen penting dalam aplikasi beasiswa. Berikut adalah tips untuk menulis motivation letter yang efektif.</p>',
                'kind' => 'guide',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($beasiswaPosts as $postData) {
            $post = Post::create([
                'tenant_id' => $beasiswa->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'kind' => $postData['kind'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'created_by' => $budi->id,
                'updated_by' => $budi->id,
                'meta_title' => $postData['title'],
                'meta_description' => $postData['excerpt'],
            ]);

            $post->terms()->attach($beasiswaCategory->id);
        }

        // Posts for Tech Jobs Portal
        $techPosts = [
            [
                'title' => 'Senior Backend Engineer - Remote',
                'excerpt' => 'Join our team as a Senior Backend Engineer. Work remotely with competitive salary.',
                'content' => '<p>We are looking for an experienced Backend Engineer to join our growing team.</p>',
                'kind' => 'job',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'job_data' => [
                    'company_name' => 'TechStart Indonesia',
                    'employment_type' => 'Full-time',
                    'location_city' => 'Jakarta (Remote)',
                    'min_salary' => 15000000,
                    'max_salary' => 25000000,
                    'apply_url' => 'https://example.com/apply',
                ],
            ],
            [
                'title' => 'Frontend Developer - React & Next.js',
                'excerpt' => 'Looking for passionate Frontend Developer with React and Next.js experience.',
                'content' => '<p>Join our product team and help build amazing user experiences.</p>',
                'kind' => 'job',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'job_data' => [
                    'company_name' => 'Startup Hub',
                    'employment_type' => 'Full-time',
                    'location_city' => 'Bandung',
                    'min_salary' => 12000000,
                    'max_salary' => 20000000,
                    'apply_url' => 'https://example.com/apply',
                ],
            ],
        ];

        foreach ($techPosts as $postData) {
            $post = Post::create([
                'tenant_id' => $techjobs->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'kind' => $postData['kind'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'created_by' => $sarah->id,
                'updated_by' => $sarah->id,
                'meta_title' => $postData['title'],
                'meta_description' => $postData['excerpt'],
            ]);

            $post->terms()->attach($techCategory->id);
        }

        // Posts for YBB
        $ybbPosts = [
            [
                'title' => 'Youth Leadership Summit 2025',
                'excerpt' => 'Join the biggest youth leadership conference in Indonesia. Register now!',
                'content' => '<p>Youth Breaking Barriers presents the Youth Leadership Summit 2025, a platform for young leaders to connect, learn, and inspire.</p>',
                'kind' => 'program',
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'program_data' => [
                    'organizer_name' => 'Youth Breaking Barriers',
                    'location_text' => 'Jakarta Convention Center',
                    'funding_type' => 'Fully Funded',
                    'apply_url' => 'https://ybb.id/summit2025',
                ],
            ],
            [
                'title' => 'Volunteer Program: Education for All',
                'excerpt' => 'Be part of our volunteer program to bring quality education to remote areas.',
                'content' => '<p>Help us make a difference by teaching underprivileged children in remote villages across Indonesia.</p>',
                'kind' => 'program',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'program_data' => [
                    'organizer_name' => 'Youth Breaking Barriers',
                    'location_text' => 'Various Locations',
                    'funding_type' => 'Partially Funded',
                    'apply_url' => 'https://ybb.id/volunteer',
                ],
            ],
        ];

        foreach ($ybbPosts as $postData) {
            $post = Post::create([
                'tenant_id' => $ybb->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'kind' => $postData['kind'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'created_by' => $ahmad->id,
                'updated_by' => $ahmad->id,
                'meta_title' => $postData['title'],
                'meta_description' => $postData['excerpt'],
            ]);

            $post->terms()->attach($ybbCategory->id);
        }

        $this->command->info('✓ Created ' . count($beasiswaPosts) . ' posts for Beasiswa Indonesia');
        $this->command->info('✓ Created ' . count($techPosts) . ' posts for Tech Jobs Portal');
        $this->command->info('✓ Created ' . count($ybbPosts) . ' posts for YBB');
    }
}
