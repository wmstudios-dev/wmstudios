<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Faq;
use App\Models\Package;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Work;
use App\Support\ImageUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

/**
 * Starter copy for the structural parts of the site (services, process, packages, FAQ, settings).
 * Safe to run on every deploy: rows are only created when they do not exist yet, so nothing edited
 * in the admin is overwritten. Everything here is editable (and replaceable) from the admin panel.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->studioContact();

        // Structural content is created ONCE (a settings flag remembers it), so whatever the admin deletes or
        // rewrites afterwards is never brought back by a later deploy.
        $this->once('seeded_services', fn () => $this->services());
        $this->once('seeded_process', fn () => $this->process());
        $this->once('seeded_packages', fn () => $this->packages());
        $this->once('seeded_faqs', fn () => $this->faqs());
        $this->once('seeded_clients', fn () => $this->clients());

        $this->sampleWorks();
        $this->deckContent();
    }

    private function once(string $flag, \Closure $seed): void
    {
        if (Setting::get($flag) !== null) {
            return;
        }

        $seed();
        Setting::put($flag, '1');
    }
    /**
     * Six made-up portfolio pieces with abstract cover art (database/data/sample), so the Works menu, the home page
     * and the Works page can be judged before real work is added. Added ONCE (a settings flag remembers it), and
     * only while there are no works at all. Every title and client is fictional; delete or edit them in Admin > Works.
     */
    private function sampleWorks(): void
    {
        if (Setting::get('sample_works_seeded') !== null || Work::query()->exists()) {
            return;
        }

        $rows = [
            ['photo', 'Kopi Senja Campaign', 'Kampanye Kopi Senja', 'Kopi Senja', 2025, true, 'Product photography for a new seasonal menu.', 'Foto produk untuk menu musiman yang baru.'],
            ['video', 'Aftermovie Pameran Kriya', 'Aftermovie Pameran Kriya', 'Dewan Kriya', 2025, true, 'A two-minute highlight film of a three-day craft expo.', 'Film highlight dua menit dari pameran kriya tiga hari.'],
            ['design', 'Brand Identity Aksara Studio', 'Identitas Brand Aksara Studio', 'Aksara Studio', 2024, true, 'Logo, colour system and social templates for a design studio.', 'Logo, sistem warna, dan template sosial media untuk studio desain.'],
            ['web', 'Rumah Tenun Website', 'Website Rumah Tenun', 'Rumah Tenun', 2025, true, 'A fast storefront and catalogue with an admin panel.', 'Etalase dan katalog yang cepat, lengkap dengan panel admin.'],
            ['social', 'Fitkita Instagram Revamp', 'Revamp Instagram Fitkita', 'Fitkita', 2025, true, 'Feed redesign and a three-month content plan.', 'Desain ulang feed dan rencana konten tiga bulan.'],
            ['photo', 'Wedding Documentation', 'Dokumentasi Pernikahan', 'Private client', 2024, false, 'A full-day documentary-style wedding set.', 'Satu set dokumentasi pernikahan sehari penuh bergaya dokumenter.'],
        ];

        foreach ($rows as $i => [$category, $titleEn, $titleId, $client, $year, $featured, $summaryEn, $summaryId]) {
            $art = database_path('data/sample/work-' . ($i + 1) . '.jpg');
            $cover = is_file($art)
                ? ImageUploader::store(new UploadedFile($art, basename($art), 'image/jpeg', null, true), 'works')
                : null;

            Work::create([
                'slug' => Str::slug($titleEn),
                'category' => $category,
                'title_en' => $titleEn, 'title_id' => $titleId,
                'client' => $client, 'year' => $year, 'is_featured' => $featured,
                'summary_en' => $summaryEn, 'summary_id' => $summaryId,
                'description_en' => $summaryEn . "

Sample project text. Replace it with the real story: the brief, what you did, and the result.",
                'description_id' => $summaryId . "

Teks contoh proyek. Ganti dengan cerita asli: brief, apa yang dikerjakan, dan hasilnya.",
                'cover_photo' => $cover,
                'sort_order' => $i + 1,
            ]);
        }

        Setting::put('sample_works_seeded', '1');
    }

    /**
     * A handful of made-up brands so the "Trusted by" row on the home page can be judged before real
     * clients exist. They are added ONCE (a flag in the settings remembers it), so deleting them in the
     * admin is permanent. All names are fictional; replace them under Admin > Clients.
     */
    /** Brands from the studio's own portfolio deck (Figma "Portfolio" slides). */
    private function clients(): void
    {
        foreach (['Omah Latareombo', 'Petlett', 'Bess Coffee & Roastery', 'Semarang Ban', 'Panda Street Coffee', 'Sampoerna'] as $i => $name) {
            Client::firstOrCreate(['name' => $name], ['sort_order' => $i + 1]);
        }
    }

    /**
     * Clean-up after the switch to the company-profile content (runs ONCE): removes the placeholder content that
     * earlier deploys created, but only the untouched originals, never anything the admin wrote.
     */
    private function deckContent(): void
    {
        if (Setting::get('deck_content_v1') !== null) {
            return;
        }

        // The four old process steps, replaced by the five "How we work" steps.
        ProcessStep::whereIn('title_en', ['Brainstorming', 'Shooting', 'Editing', 'Delivery'])->delete();

        // The three generic price-less packages, replaced by the real pricelist.
        Package::whereNull('group_en')->whereNull('price_label_id')
            ->whereIn('name_en', ['Essential', 'Growth', 'Signature'])->delete();

        // The six made-up demo brands.
        Client::whereIn('name', ['Kopi Senja', 'Dewan Kriya', 'Aksara Studio', 'Rumah Tenun', 'Fitkita', 'Langit Biru'])->delete();

        Setting::put('deck_content_v1', '1');
    }
    private function settings(): void
    {
        $defaults = [
            'site_name' => 'WMSTUDIOS',
            'tagline_id' => 'Studio kreatif untuk sosial media, foto & video, desain, dan web.',
            'tagline_en' => 'A creative studio for social media, photo & video, design and web.',
        ];

        foreach ($defaults as $key => $value) {
            if (Setting::get($key) === null) {
                Setting::put($key, $value);
            }
        }
    }

    /**
     * The studio's real contact details, filled in ONCE (a settings flag remembers it) and only into fields that are
     * still empty, so anything edited in the admin afterwards is never overwritten.
     */
    private function studioContact(): void
    {
        if (Setting::get('studio_contact_seeded') !== null) {
            return;
        }

        $contact = [
            'whatsapp' => '085641034599',
            'email' => 'wmstudios.cf@gmail.com',
            'address_id' => 'Magelang',
            'address_en' => 'Magelang, Indonesia',
            'hours_id' => '24 jam',
            'hours_en' => '24 hours',
        ];

        foreach ($contact as $key => $value) {
            if (blank(Setting::get($key))) {
                Setting::put($key, $value);
            }
        }

        Setting::put('studio_contact_seeded', '1');
    }

    private function services(): void
    {
        $rows = [
            [
                'slug' => 'social-media', 'icon' => 'megaphone', 'order' => 1,
                'title' => ['Social Media Specialist', 'Social Media Specialist'],
                'summary' => [
                    'Strategi, konten, dan pengelolaan akun supaya audiens tumbuh, terlibat, dan akhirnya jadi pelanggan.',
                    'Strategy, content and account management so your audience grows, engages and becomes customers.',
                ],
                'details' => [
                    "Strategi & kalender konten\nCopywriting & caption\nDesain feed, story, dan reels\nPengelolaan akun & komunitas\nLaporan performa bulanan",
                    "Content strategy & calendar\nCopywriting & captions\nFeed, story and reels design\nAccount & community management\nMonthly performance report",
                ],
            ],
            [
                'slug' => 'documentation', 'icon' => 'camera', 'order' => 2,
                'title' => ['Dokumentasi', 'Documentation'],
                'summary' => [
                    'Rekam momen penting acara, kegiatan, dan perjalanan bisnismu dengan rapi dan siap dibagikan.',
                    'Capture the moments that matter, from events and activities to your business journey, ready to share.',
                ],
                'details' => [
                    "Dokumentasi foto & video acara\nLiputan di balik layar\nAftermovie & highlight\nPotongan cepat untuk sosial media\nArsip hasil yang tertata",
                    "Event photo & video coverage\nBehind-the-scenes coverage\nAftermovie & highlights\nQuick cuts for social media\nOrganized delivery archive",
                ],
            ],
            [
                'slug' => 'design', 'icon' => 'palette', 'order' => 3,
                'title' => ['Desain', 'Design'],
                'summary' => [
                    'Identitas visual dan materi desain yang konsisten, dari logo sampai kebutuhan promosi harian.',
                    'A consistent visual identity and design assets, from your logo to everyday promotion.',
                ],
                'details' => [
                    "Branding & identitas visual\nTemplate sosial media\nPoster, banner, dan materi cetak\nPitch deck & presentasi\nPanduan brand",
                    "Branding & visual identity\nSocial media templates\nPosters, banners and print\nPitch decks & presentations\nBrand guidelines",
                ],
            ],
            [
                'slug' => 'photo-video-production', 'icon' => 'video', 'order' => 4,
                'title' => ['Produksi Foto & Video', 'Photo & Video Production'],
                'summary' => [
                    'Produksi foto dan video profesional, dari konsep dan naskah sampai editing akhir.',
                    'Professional photo and video production, from concept and script to the final edit.',
                ],
                'details' => [
                    "Foto produk, potret, dan lifestyle\nVideo komersial & company profile\nNaskah & storyboard\nEditing & color grading\nRetouching foto",
                    "Product, portrait and lifestyle photography\nCommercial & company-profile videos\nScripting & storyboarding\nEditing & color grading\nPhoto retouching",
                ],
            ],
            [
                'slug' => 'web-design-development', 'icon' => 'code', 'order' => 5,
                'title' => ['Web Design & Development', 'Web Design & Development'],
                'summary' => [
                    'Website yang rapi, cepat, dan mudah dikelola, dirancang selaras dengan identitas brand-mu.',
                    'Clean, fast websites that are easy to manage, designed to match your brand identity.',
                ],
                'details' => [
                    "Desain website khusus\nPengembangan responsif (HP, tablet, desktop)\nPanel admin untuk kelola konten sendiri\nSiap SEO & cepat dibuka\nPengaturan domain, hosting, dan perawatan",
                    "Custom website design\nResponsive development (phone, tablet, desktop)\nAdmin panel to manage your own content\nSEO-ready and fast loading\nDomain, hosting setup and maintenance",
                ],
            ],
        ];

        foreach ($rows as $r) {
            Service::firstOrCreate(['slug' => $r['slug']], [
                'icon' => $r['icon'],
                'title_id' => $r['title'][0], 'title_en' => $r['title'][1],
                'summary_id' => $r['summary'][0], 'summary_en' => $r['summary'][1],
                'details_id' => $r['details'][0], 'details_en' => $r['details'][1],
                'sort_order' => $r['order'],
            ]);
        }
    }

    private function process(): void
    {
        $steps = [
            ['users', 'Listen & Understand',
                'Memulai dengan mendengarkan, memahami kebutuhan, tujuan, dan karakter brand secara menyeluruh.',
                'We start by listening, and fully understanding the needs, goals and character of the brand.'],
            ['bulb', 'Explore Ideas',
                'Proses diskusi dan eksplorasi ide dilakukan secara terbuka untuk menemukan pendekatan yang paling relevan.',
                'Discussion and idea exploration happen openly, to find the approach that fits best.'],
            ['layers', 'Create & Execute',
                'Ide yang sudah matang dieksekusi dengan proses yang terarah, rapi, dan fleksibel.',
                'Mature ideas are executed through a focused, tidy and flexible process.'],
            ['chart', 'Review & Improve',
                'Setiap proses dievaluasi untuk memastikan hasil tetap selaras dengan tujuan awal.',
                'Every step is reviewed to make sure the result stays aligned with the original goal.'],
            ['heart', 'Grow Together',
                'Kami percaya kerja sama yang baik adalah tentang tumbuh bersama, bukan sekadar menyelesaikan proyek.',
                'We believe good collaboration is about growing together, not just finishing a project.'],
        ];

        foreach ($steps as $i => [$icon, $title, $descId, $descEn]) {
            ProcessStep::firstOrCreate(['title_en' => $title], [
                'icon' => $icon,
                'title_id' => $title,
                'description_id' => $descId, 'description_en' => $descEn,
                'sort_order' => $i + 1,
            ]);
        }
    }
    /**
     * The pricelist from the company profile deck. Prices are shown as written there ("Mulai dari ..."), grouped
     * by service, and every one of them can be edited in Admin > Packages.
     */
    private function packages(): void
    {
        $notesBrand = [
            "Semua paket dapat disesuaikan dengan kebutuhan brand\nHarga dapat berubah sesuai kompleksitas konten & kebutuhan tambahan",
            "All packages can be adjusted to the needs of the brand\nPrices may change with the complexity of the content and any extra requirements",
        ];
        $notesEvent = [
            "Semua paket dapat disesuaikan dengan kebutuhan acara\nHarga dapat berubah sesuai kompleksitas konten & kebutuhan tambahan",
            "All packages can be adjusted to the needs of the event\nPrices may change with the complexity of the content and any extra requirements",
        ];

        $groups = [
            [
                'group' => ['Social Media & Content Creation', 'Social Media & Content Creation'],
                'note' => [
                    $notesBrand[0] . "\nPaket tidak termasuk budget iklan (jika ada)",
                    $notesBrand[1] . "\nPackages do not include ad budget (if any)",
                ],
                'items' => [
                    ['Starter', 'Rp500K / bulan', 'IDR 500K / month',
                        "4 feed / bulan\n2 carousel / bulan\n1 reels / bulan\n6–8 story / bulan\nPerencanaan konten dasar\nCopywriting\nPenjadwalan konten",
                        "4 feed posts / month\n2 carousels / month\n1 reel / month\n6–8 stories / month\nBasic content planning\nCopywriting\nContent scheduling"],
                    ['Growth', 'Rp1.000K / bulan', 'IDR 1,000K / month',
                        "6 feed post / bulan\n3 carousel / bulan\n2 reels / bulan\n10–12 story / bulan\nPerencanaan konten dasar\nCopywriting\nPenjadwalan konten\nGratis ucapan hari besar",
                        "6 feed posts / month\n3 carousels / month\n2 reels / month\n10–12 stories / month\nBasic content planning\nCopywriting\nContent scheduling\nFree holiday greetings"],
                    ['Advance', 'Rp1.500K / bulan', 'IDR 1,500K / month',
                        "8 feed post / bulan\n4 carousel / bulan\n4 reels / bulan\n15–18 story / bulan\nStrategi & arahan konten\nCopywriting\nPenjadwalan konten\nEvaluasi & perbaikan performa",
                        "8 feed posts / month\n4 carousels / month\n4 reels / month\n15–18 stories / month\nContent strategy & direction\nCopywriting\nContent scheduling\nPerformance evaluation & improvement"],
                ],
            ],
            [
                'group' => ['Desain', 'Design'],
                'note' => $notesBrand,
                'items' => [
                    ['Simple', 'Rp50K / desain', 'IDR 50K / design',
                        "1× revisi\n1 ukuran\nFile JPG, PNG, PDF",
                        "1 revision\n1 size\nJPG, PNG, PDF files"],
                    ['Standard', 'Rp100K / desain', 'IDR 100K / design',
                        "2× revisi\n1 ukuran\nFile JPG, PNG, PDF",
                        "2 revisions\n1 size\nJPG, PNG, PDF files"],
                    ['Complex', 'Rp150K / desain', 'IDR 150K / design',
                        "3× revisi\n1 ukuran\nFile JPG, PNG, PDF, EPS/AI, PSD",
                        "3 revisions\n1 size\nJPG, PNG, PDF, EPS/AI, PSD files"],
                ],
            ],
            [
                'group' => ['Dokumentasi', 'Documentation'],
                'note' => $notesEvent,
                'items' => [
                    ['Short', 'Rp499K / acara', 'IDR 499K / event',
                        "Acara 2–3 jam\nFoto + editing\nVideo + editing",
                        "2–3 hour event\nPhoto + editing\nVideo + editing"],
                    ['Medium', 'Rp799K / acara', 'IDR 799K / event',
                        "Acara 5–6 jam\nFoto + editing\nVideo + editing",
                        "5–6 hour event\nPhoto + editing\nVideo + editing"],
                    ['Long', 'Rp1.199K / acara', 'IDR 1,199K / event',
                        "Acara hingga 10 jam\nFoto + editing\nVideo + editing",
                        "Event of up to 10 hours\nPhoto + editing\nVideo + editing"],
                ],
            ],
            [
                'group' => ['Video Editing', 'Video Editing'],
                'note' => $notesEvent,
                'items' => [
                    ['Short', 'Rp99K / acara', 'IDR 99K / event',
                        "Video hingga 1 menit\nEditing sederhana\nAnimasi judul sederhana",
                        "Video of up to 1 minute\nSimple editing\nSimple title animation"],
                    ['Medium', 'Rp149K / acara', 'IDR 149K / event',
                        "Video hingga 2 menit\nEditing sederhana\nAnimasi judul sederhana\nTransisi\nColor grading",
                        "Video of up to 2 minutes\nSimple editing\nSimple title animation\nTransitions\nColor grading"],
                    ['Long', 'Rp229K / acara', 'IDR 229K / event',
                        "Video hingga 3 menit\nEditing sederhana\nAnimasi judul sederhana\nTransisi\nColor grading",
                        "Video of up to 3 minutes\nSimple editing\nSimple title animation\nTransitions\nColor grading"],
                ],
            ],
        ];

        $order = 0;

        foreach ($groups as $g) {
            foreach ($g['items'] as $item) {
                [$name, $priceId, $priceEn, $featId, $featEn] = $item;
                Package::firstOrCreate(['name_en' => $name, 'group_en' => $g['group'][1]], [
                    'name_id' => $name,
                    'group_id' => $g['group'][0],
                    'price_label_id' => $priceId, 'price_label_en' => $priceEn,
                    'features_id' => $featId, 'features_en' => $featEn,
                    'note_id' => $g['note'][0], 'note_en' => $g['note'][1],
                    'is_featured' => false,
                    'sort_order' => ++$order,
                ]);
            }
        }
    }
    private function faqs(): void
    {
        $rows = [
            [
                ['Berapa lama pengerjaan satu proyek?', 'How long does a project take?'],
                [
                    'Tergantung ruang lingkupnya. Konten sosial media berjalan mingguan atau bulanan, video biasanya 1–3 minggu, dan website 3–6 minggu. Kami berikan perkiraan jadwal yang jelas di awal.',
                    'It depends on the scope. Social media content runs weekly or monthly, videos usually take 1–3 weeks and websites 3–6 weeks. We give you a clear timeline up front.',
                ],
            ],
            [
                ['Apakah ada revisi?', 'Are revisions included?'],
                [
                    'Ya. Setiap proyek sudah termasuk revisi, dan jumlahnya disepakati sebelum pengerjaan dimulai supaya prosesnya jelas untuk kedua pihak.',
                    'Yes. Every project includes revisions, and the number is agreed before work starts so the process is clear for both sides.',
                ],
            ],
            [
                ['Bagaimana sistem pembayarannya?', 'How does payment work?'],
                [
                    'Umumnya ada uang muka di awal dan pelunasan saat hasil akhir diserahkan. Rinciannya kami bahas dan sepakati lebih dulu.',
                    'Usually there is a deposit at the start and the balance when the final result is delivered. We discuss and agree on the details beforehand.',
                ],
            ],
            [
                ['Apakah bisa untuk bisnis kecil dengan budget terbatas?', 'Can small businesses with a limited budget work with you?'],
                [
                    'Bisa. Kami menyesuaikan ruang lingkup dengan budget, dan bisa mulai dari satu layanan dulu lalu berkembang bertahap.',
                    'Yes. We shape the scope around your budget, and you can start with a single service and grow step by step.',
                ],
            ],
            [
                ['Apakah kalian juga membuat website?', 'Do you also build websites?'],
                [
                    'Ya. Kami merancang dan mengembangkan website, lengkap dengan panel admin supaya kamu bisa mengelola konten sendiri.',
                    'Yes. We design and develop websites, including an admin panel so you can manage the content yourself.',
                ],
            ],
        ];

        foreach ($rows as $i => [$q, $a]) {
            Faq::firstOrCreate(['question_en' => $q[1]], [
                'question_id' => $q[0],
                'answer_id' => $a[0], 'answer_en' => $a[1],
                'sort_order' => $i + 1,
            ]);
        }
    }
}
