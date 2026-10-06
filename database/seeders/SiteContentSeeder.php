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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

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

        $this->deckContent();
        $this->portfolio();
        $this->once('portfolio_logos_v1', fn () => $this->portfolioLogos());
        $this->once('portfolio_photos_v2', fn () => $this->portfolioPhotos());
        $this->once('portfolio_covers_v3', fn () => $this->portfolioCovers());
    }

    private function once(string $flag, \Closure $seed): void
    {
        if (Setting::get($flag) !== null) {
            return;
        }

        $seed();
        Setting::put($flag, '1');
    }
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
    /**
     * The studio's real client work (Figma portfolio folders): one work per client with a cover and a gallery, and the
     * client's logo for the "Trusted by" row. The pictures are already shrunk to WebP (+ thumbnails) under
     * database/data/portfolio, so no image library is needed on the server. Runs ONCE; after that everything is
     * editable in the admin and never touched again.
     */
    private function portfolio(): void
    {
        if (Setting::get('portfolio_v1') !== null) {
            return;
        }

        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');
        $site = Setting::get('site_name', 'WMSTUDIOS');

        // The made-up demo works from the first previews.
        foreach (Work::whereIn('slug', [
            'kopi-senja-campaign', 'aftermovie-pameran-kriya', 'brand-identity-aksara-studio',
            'rumah-tenun-website', 'fitkita-instagram-revamp', 'wedding-documentation',
        ])->get() as $demo) {
            ImageUploader::delete($demo->cover_photo);
            $demo->delete();
        }

        // Cases that were only named in the company-profile deck and have no material yet stay in the admin, hidden.
        Client::whereIn('name', ['Petlett', 'Bess Coffee & Roastery', 'Panda Street Coffee', 'Sampoerna'])->update(['is_active' => false]);

        $rows = [
            ['omah-latareombo', 'Omah Latareombo', true,
                'Content strategy, visual production and social media management for a Javanese-modern coffee & restaurant.',
                'Strategi konten, produksi visual, dan pengelolaan media sosial untuk coffee & restaurant berkarakter Jawa-modern.',
                "Omah Latareombo is a coffee & restaurant with a calm, warm Javanese-modern character. This project focuses on building a consistent, easily recognised social media look, with a tone of voice that is calm, elegant and still warm.\n\n{$site} handled content strategy, visual production and social media management. The output covers feeds, reels, stories and various design needs that support the brand's communication and promotion.",
                "Omah Latareombo adalah coffee & restaurant dengan karakter Jawa-modern yang tenang dan hangat. Proyek ini berfokus pada membangun tampilan media sosial yang konsisten dan mudah dikenali, dengan tone of voice yang kalem, elegan, dan tetap terasa hangat.\n\n{$site} berperan dalam penyusunan strategi konten, produksi visual, serta pengelolaan akun media sosial. Output meliputi feed, reels, story, dan berbagai kebutuhan desain untuk mendukung komunikasi dan promosi brand."],
            ['cupfine', 'Cupfine', true,
                'Feed and story content for a coffee shop: atmosphere and menu photos, plus opening-hours stories.',
                'Konten feed dan story untuk coffee shop: foto suasana dan menu, serta story informasi jam buka.',
                "Social media content for Cupfine coffee shop. Feed posts show the atmosphere, the bar and the drinks, and designed stories share opening hours and daily updates.",
                "Konten media sosial untuk coffee shop Cupfine. Feed menampilkan suasana, bar, dan minuman, sementara story yang didesain menyampaikan jam buka dan kabar harian."],
            ['kandang-kopi', 'Kandang Kopi', true,
                'Feed and story content for a nature-inspired coffee place: atmosphere, food and drinks, and holiday greetings.',
                'Konten feed dan story untuk kedai kopi bernuansa alam: suasana, makanan dan minuman, serta ucapan hari besar.',
                "Social media content for Kandang Kopi. Feed and story designs show the green, wooden atmosphere of the place, the food and drink menu, and greetings for national and religious days.",
                "Konten media sosial untuk Kandang Kopi. Desain feed dan story menampilkan suasana tempat yang hijau dan penuh kayu, menu makanan dan minuman, serta ucapan hari nasional dan hari besar."],
            ['maza-coffee-and-resto', 'Maza Coffee & Resto', true,
                'Feed and story content for a coffee & resto: the place, the menu, and open and closed announcements.',
                'Konten feed dan story untuk coffee & resto: suasana tempat, menu, serta pengumuman buka dan tutup.',
                "Social media content for Maza Coffee & Resto. Feed posts present the building, the interior and the dishes, and stories announce when the place is open or closed.",
                "Konten media sosial untuk Maza Coffee & Resto. Feed menampilkan bangunan, interior, dan sajian, sementara story mengumumkan kapan tempat buka atau tutup."],
            ['reamor', 'Reamor', true,
                'Carousel content for a perfume brand: brand messages, products and a soft visual style.',
                'Konten carousel untuk brand parfum: pesan brand, produk, dan gaya visual yang lembut.',
                "Carousel content for the perfume brand Reamor. Each carousel carries a brand message about love, closeness and everyday moments, paired with the products in a soft, warm visual style. Client review carousels are part of the set.",
                "Konten carousel untuk brand parfum Reamor. Tiap carousel membawa pesan brand tentang cinta, kedekatan, dan momen sehari-hari, dipadukan dengan produk dalam gaya visual yang lembut dan hangat. Carousel ulasan klien juga termasuk dalam set ini."],
            ['semarang-ban', 'Semarang Ban', true,
                'Social media branding and content for a tyre and wheel alignment shop: feed, educational carousels and promo stories.',
                'Branding media sosial dan konten untuk toko ban dan spooring: feed, carousel edukasi, dan story promo.',
                "Social media branding and content for Semarang Ban. The work covers the Instagram look (logo use, typography, highlights and bio), educational carousels about wheel alignment, product and promo posts, and stories.",
                "Branding media sosial dan konten untuk Semarang Ban. Pekerjaannya mencakup tampilan Instagram (penggunaan logo, tipografi, highlight, dan bio), carousel edukasi tentang spooring, postingan produk dan promo, serta story."],
            ['the-overlander', 'The Overlander', false,
                'Visual identity and social media content for a travel brand: colour palette, typography, destination carousels and reel covers.',
                'Identitas visual dan konten media sosial untuk brand travel: palet warna, tipografi, carousel destinasi, dan cover reels.',
                "Visual identity and social media content for The Overlander Indonesia. The set includes the colour palette and typography, destination carousels, travel package posts and reel covers.",
                "Identitas visual dan konten media sosial untuk The Overlander Indonesia. Set ini mencakup palet warna dan tipografi, carousel destinasi, postingan paket perjalanan, dan cover reels."],
        ];

        foreach ($rows as $i => [$slug, $name, $featured, $summaryEn, $summaryId, $descEn, $descId]) {
            // client + white-ready logo
            $logo = null;
            if (is_file("{$base}/logos/{$slug}.png")) {
                $logo = "uploads/clients/{$slug}.png";
                $disk->put($logo, file_get_contents("{$base}/logos/{$slug}.png"));
            }

            $client = Client::firstOrNew(['name' => $name]);
            $client->logo = $logo ?? $client->logo;
            $client->sort_order = $i + 1;
            $client->is_active = true;
            $client->save();

            // pictures: 01 is the cover, the rest is the gallery
            $paths = [];
            foreach (glob("{$base}/{$slug}/[0-9][0-9].webp") ?: [] as $file) {
                $n = basename($file, '.webp');
                $paths[] = "uploads/works/{$slug}/{$n}.webp";
                $disk->put("uploads/works/{$slug}/{$n}.webp", file_get_contents($file));
                if (is_file("{$base}/{$slug}/{$n}_thumb.webp")) {
                    $disk->put("uploads/works/{$slug}/{$n}_thumb.webp", file_get_contents("{$base}/{$slug}/{$n}_thumb.webp"));
                }
            }

            if (Work::where('slug', $slug)->exists()) {
                continue;
            }

            $work = Work::create([
                'slug' => $slug,
                'category' => 'social',
                'title_en' => $name, 'title_id' => $name,
                'client' => $name,
                'is_featured' => $featured,
                'summary_en' => $summaryEn, 'summary_id' => $summaryId,
                'description_en' => $descEn, 'description_id' => $descId,
                'cover_photo' => $paths[0] ?? null,
                'sort_order' => $i + 1,
            ]);

            foreach (array_slice($paths, 1) as $order => $path) {
                $work->photos()->create([
                    'path' => $path,
                    'sort_order' => $order + 1,
                    'kind' => self::portfolioManifest()[$slug][basename($path, '.webp')][0] ?? 'other',
                    'caption' => self::portfolioManifest()[$slug][basename($path, '.webp')][1] ?? null,
                ]);
            }
        }

        Setting::put('portfolio_v1', '1');
    }
    /** Every shipped portfolio picture: slug => file number => [kind (feed / story / carousel / reel / identity), carousel set label, order]. */
    private static function portfolioManifest(): array
    {
        $file = database_path('data/portfolio/manifest.json');

        return is_file($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    }

    /**
     * Brings the portfolio pictures seeded earlier up to date (runs once): sets what each one is (feed, story,
     * carousel set, ...) and its place in the gallery, and adds the pictures that were shipped later so that every
     * usable picture of the client folders is on the site. Nothing is removed.
     */
    private function portfolioPhotos(): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');

        foreach (self::portfolioManifest() as $slug => $pictures) {
            $work = Work::where('slug', $slug)->first();

            if (! $work) {
                continue;
            }

            $prefix = "uploads/works/{$slug}/";
            $byPath = $work->photos->keyBy('path');

            foreach ($pictures as $number => [$kind, $caption, $order]) {
                $path = "{$prefix}{$number}.webp";

                if ($path === $work->cover_photo) {
                    continue;
                }

                if ($photo = $byPath->get($path)) {
                    $photo->update(['kind' => $kind, 'caption' => $photo->caption ?? $caption, 'sort_order' => $order + 1]);
                    continue;
                }

                if (! is_file("{$base}/{$slug}/{$number}.webp")) {
                    continue;
                }

                $disk->put($path, file_get_contents("{$base}/{$slug}/{$number}.webp"));

                if (is_file("{$base}/{$slug}/{$number}_thumb.webp")) {
                    $disk->put("{$prefix}{$number}_thumb.webp", file_get_contents("{$base}/{$slug}/{$number}_thumb.webp"));
                }

                $work->photos()->create(['path' => $path, 'kind' => $kind, 'caption' => $caption, 'sort_order' => $order + 1]);
            }
        }
    }
    /**
     * Gives works the hand-picked landscape main photo (covers.json) in place of the picture they were seeded with
     * (runs once). Only a shipped cover (a numbered picture or an earlier cover file) is replaced, never one chosen in
     * the admin, and a replaced numbered picture stays on the work page as a gallery picture.
     */
    private function portfolioCovers(): void
    {
        $file = database_path('data/portfolio/covers.json');

        if (! is_file($file)) {
            return;
        }

        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');
        $manifest = self::portfolioManifest();

        foreach (json_decode(file_get_contents($file), true) ?: [] as $slug => $spec) {
            $name = $spec['file'] ?? 'cover';
            $work = Work::where('slug', $slug)->first();
            $new = "uploads/works/{$slug}/{$name}.webp";
            $old = (string) $work?->cover_photo;

            if ($work && $work->cover_photo === $new && ! $work->cover_focus && ! empty($spec['focus'])) {
                $work->update(['cover_focus' => $spec['focus']]);
            }

            if (! $work || $old === $new || ! preg_match('#/(?:\d\d|cover[a-z0-9-]*)\.webp$#', $old) || ! is_file("{$base}/{$slug}/{$name}.webp")) {
                continue;
            }

            foreach ([$name . '.webp', $name . '_thumb.webp'] as $picture) {
                if (is_file("{$base}/{$slug}/{$picture}")) {
                    $disk->put("uploads/works/{$slug}/{$picture}", file_get_contents("{$base}/{$slug}/{$picture}"));
                }
            }

            $work->update(['cover_photo' => $new, 'cover_focus' => $work->cover_focus ?: ($spec['focus'] ?? null)]);

            $number = basename($old, '.webp');

            if (isset($manifest[$slug][$number]) && ! $work->photos()->where('path', $old)->exists()) {
                [$kind, $caption, $order] = $manifest[$slug][$number];
                $work->photos()->create(['path' => $old, 'kind' => $kind, 'caption' => $caption, 'sort_order' => $order + 1]);
            }
        }
    }

    /**
     * Gives a client its white-ready logo from database/data/portfolio/logos, but only where the client has none yet
     * (the portfolio seed above ran before every logo existed). Runs once; a logo set in the admin is never replaced.
     */
    private function portfolioLogos(): void
    {
        $names = [
            'omah-latareombo' => 'Omah Latareombo', 'cupfine' => 'Cupfine', 'kandang-kopi' => 'Kandang Kopi',
            'maza-coffee-and-resto' => 'Maza Coffee & Resto', 'reamor' => 'Reamor', 'semarang-ban' => 'Semarang Ban',
            'the-overlander' => 'The Overlander',
        ];

        foreach ($names as $slug => $name) {
            $file = database_path("data/portfolio/logos/{$slug}.png");
            $client = Client::where('name', $name)->first();

            if (! is_file($file) || ! $client || filled($client->logo)) {
                continue;
            }

            $path = "uploads/clients/{$slug}.png";
            Storage::disk('public')->put($path, file_get_contents($file));
            $client->update(['logo' => $path]);
        }
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
