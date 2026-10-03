<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Testimonial;
use App\Models\Thought;
use App\Models\Work;
use Illuminate\Database\Seeder;

/**
 * Sample portfolio, clients and articles so the site can be tried before real content exists.
 * Local development only (see DatabaseSeeder); run it by hand with
 *   php artisan db:seed --class=DemoContentSeeder
 * if you ever want sample content elsewhere. Every name below is made up.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            ['photo', 'Kopi Senja Campaign', 'Kampanye Kopi Senja', 'Kopi Senja', 2025, true, 'Product photography for a new seasonal menu.', 'Foto produk untuk menu musiman yang baru.'],
            ['video', 'Aftermovie Pameran Kriya', 'Aftermovie Pameran Kriya', 'Dewan Kriya', 2025, true, 'A two-minute highlight film of a three-day craft expo.', 'Film highlight dua menit dari pameran kriya tiga hari.'],
            ['design', 'Brand Identity Aksara Studio', 'Identitas Brand Aksara Studio', 'Aksara Studio', 2024, true, 'Logo, color system and social templates for a design studio.', 'Logo, sistem warna, dan template sosial media untuk studio desain.'],
            ['web', 'Rumah Tenun Website', 'Website Rumah Tenun', 'Rumah Tenun', 2025, true, 'A fast storefront and catalogue with an admin panel.', 'Etalase dan katalog yang cepat, lengkap dengan panel admin.'],
            ['social', 'Fitkita Instagram Revamp', 'Revamp Instagram Fitkita', 'Fitkita', 2025, true, 'Feed redesign and a three-month content plan.', 'Desain ulang feed dan rencana konten tiga bulan.'],
            ['photo', 'Wedding Documentation', 'Dokumentasi Pernikahan', 'Private client', 2024, false, 'A full-day documentary-style wedding set.', 'Satu set dokumentasi pernikahan sehari penuh bergaya dokumenter.'],
        ];

        foreach ($works as $i => $w) {
            Work::firstOrCreate(['slug' => \Illuminate\Support\Str::slug($w[1])], [
                'category' => $w[0],
                'title_en' => $w[1], 'title_id' => $w[2],
                'client' => $w[3], 'year' => $w[4], 'is_featured' => $w[5],
                'summary_en' => $w[6], 'summary_id' => $w[7],
                'description_en' => $w[6] . "\n\nSample project text. Replace it with the real story: the brief, what you did, and the result.",
                'description_id' => $w[7] . "\n\nTeks contoh proyek. Ganti dengan cerita asli: brief, apa yang dikerjakan, dan hasilnya.",
                'video_url' => $w[8] ?? null,
                'sort_order' => $i + 1,
            ]);
        }

        foreach (['Kopi Senja', 'Dewan Kriya', 'Aksara Studio', 'Rumah Tenun', 'Fitkita', 'Langit Biru'] as $i => $name) {
            Client::firstOrCreate(['name' => $name], ['sort_order' => $i + 1]);
        }

        Testimonial::firstOrCreate(['name' => 'Rani Putri'], [
            'role' => 'Owner, Kopi Senja',
            'quote_id' => 'Prosesnya rapi dan komunikatif. Hasil fotonya langsung kami pakai untuk semua kanal.',
            'quote_en' => 'The process was tidy and communicative. We used the photos across every channel right away.',
            'sort_order' => 1,
        ]);

        Testimonial::firstOrCreate(['name' => 'Bima Santoso'], [
            'role' => 'Marketing, Rumah Tenun',
            'quote_id' => 'Website baru kami cepat dan gampang dikelola sendiri. Sangat membantu.',
            'quote_en' => 'Our new website is fast and easy to manage ourselves. A real help.',
            'sort_order' => 2,
        ]);

        Thought::firstOrCreate(['slug' => 'why-consistency-beats-virality'], [
            'title_en' => 'Why consistency beats virality',
            'title_id' => 'Kenapa konsistensi mengalahkan viral',
            'excerpt_en' => 'A viral post is a lucky day. A consistent brand is a habit your audience learns to trust.',
            'excerpt_id' => 'Postingan viral hanyalah hari keberuntungan. Brand yang konsisten adalah kebiasaan yang dipercaya audiens.',
            'body_en' => "A viral post is a lucky day. A consistent brand is something people learn to trust.\n\n## Show up the same way\n\nSame tone, same look, same rhythm. People should recognise you before they read your name.\n\n## Measure the boring things\n\nSaves, replies and returning viewers say more about your brand than a single spike.\n\n*This is sample text. Replace it from the admin panel.*",
            'body_id' => "Postingan viral hanyalah hari keberuntungan. Brand yang konsisten adalah sesuatu yang perlahan dipercaya orang.\n\n## Hadir dengan cara yang sama\n\nNada, tampilan, dan ritme yang sama. Orang seharusnya mengenalimu sebelum membaca namamu.\n\n## Ukur hal-hal yang membosankan\n\nSimpanan, balasan, dan penonton yang kembali bercerita lebih banyak daripada satu lonjakan.\n\n*Ini teks contoh. Ganti dari panel admin.*",
            'is_published' => true,
            'published_at' => now()->subDays(5),
        ]);
    }
}
