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
        $this->once('portfolio_batch2_v1', fn () => $this->portfolioBatch2());
        $this->once('portfolio_batch3_v1', fn () => $this->portfolioBatch3());
        $this->once('portfolio_batch4_v1', fn () => $this->portfolioBatch4());
        $this->once('semarang_feed_v1', fn () => $this->portfolioTopUp(['semarang-ban']));
        $this->once('design_only_v1', fn () => $this->designOnlyClients());
        $this->once('instagram_links_v1', fn () => $this->instagramLinks());
        $this->once('remove_photo_doc_sample_v1', fn () => $this->removePhotoDocumentationSample());
        $this->once('doc_videos_v1', fn () => $this->documentationVideos());
        $this->once('doc_video_clients_v1', fn () => $this->documentationVideoClients());
        $this->once('overlander_split_v1', fn () => $this->splitOverlander());
        $this->once('overlander_web_screens_v1', fn () => $this->overlanderWebScreens());
        $this->once('overlander_web_info_v1', fn () => $this->overlanderWebInfo());
        $this->once('overlander_web_hq_v1', fn () => $this->overlanderWebHighRes());
        $this->once('featured_mix_v1', fn () => $this->featuredMix());
        $this->once('richer_texts_v1', fn () => $this->richerTexts());
        $this->once('relaxed_texts_v2', fn () => $this->relaxedTexts());
        $this->once('sample_space_thoughts_v1', fn () => $this->sampleSpaceAndThoughts());
        $this->once('home_images_v1', fn () => $this->homeImages());
        $this->once('portfolio_overlander_web_v1', fn () => $this->portfolioOverlanderWeb());
        $this->once('portfolio_design_clients_v1', fn () => $this->portfolioDesignClients());
        $this->once('sample_kinds_v1', fn () => $this->sampleKinds());
        $this->once('indonesian_titles_v1', fn () => $this->indonesianTitles());
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
     * Two clearly labelled sample works (runs once) that show how the "Website" and "Dokumentasi" gallery kinds
     * look, until real material exists. The pictures are generated placeholders under database/data/portfolio. Delete
     * the works in Admin > Projects whenever you like; they are not created again.
     */
    private function sampleKinds(): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');

        $works = [
            [
                'slug' => 'contoh-website', 'category' => 'web', 'kind' => 'web', 'order' => 90,
                'title_id' => 'Contoh Proyek Website', 'title_en' => 'Sample Website Project',
                'summary_id' => 'Contoh untuk melihat bagaimana proyek website tampil. Ganti atau hapus di Admin > Projects.',
                'summary_en' => 'A sample to see how a website project looks. Replace or delete it in Admin > Projects.',
                'description_id' => "Ini data contoh (dummy). Tangkapan layar di bawah hanya ilustrasi supaya bagian Website terlihat bentuknya.\n\nGanti dengan proyek website sungguhan: link situs live, screenshot desktop dan HP, fitur utama, dan peran.",
                'description_en' => "This is sample (dummy) data. The screenshots below are only illustrations so the Website section can be judged.\n\nReplace it with a real website project: the live link, desktop and phone screenshots, key features and your role.",
            ],
            [
                'slug' => 'contoh-dokumentasi', 'category' => 'photo', 'kind' => 'documentation', 'order' => 91,
                'title_id' => 'Contoh Dokumentasi Acara', 'title_en' => 'Sample Event Documentation',
                'summary_id' => 'Contoh untuk melihat bagaimana dokumentasi acara tampil. Ganti atau hapus di Admin > Projects.',
                'summary_en' => 'A sample to see how event documentation looks. Replace or delete it in Admin > Projects.',
                'description_id' => "Ini data contoh (dummy). Foto di bawah hanya ilustrasi dengan berbagai proporsi supaya bagian Dokumentasi terlihat bentuknya.\n\nGanti dengan dokumentasi sungguhan: foto terbaik dari acara, video highlight, nama dan tanggal acara, serta paket yang dipakai.",
                'description_en' => "This is sample (dummy) data. The photos below are only illustrations in mixed proportions so the Documentation section can be judged.\n\nReplace it with real documentation: the best event photos, a highlight video, the event name and date, and the package used.",
            ],
        ];

        foreach ($works as $w) {
            if (Work::where('slug', $w['slug'])->exists()) {
                continue;
            }

            $paths = [];

            foreach (glob("{$base}/{$w['slug']}/[0-9][0-9].webp") ?: [] as $file) {
                $n = basename($file, '.webp');
                $paths[] = "uploads/works/{$w['slug']}/{$n}.webp";
                $disk->put("uploads/works/{$w['slug']}/{$n}.webp", file_get_contents($file));

                if (is_file("{$base}/{$w['slug']}/{$n}_thumb.webp")) {
                    $disk->put("uploads/works/{$w['slug']}/{$n}_thumb.webp", file_get_contents("{$base}/{$w['slug']}/{$n}_thumb.webp"));
                }
            }

            $work = Work::create([
                'slug' => $w['slug'], 'category' => $w['category'],
                'title_id' => $w['title_id'], 'title_en' => $w['title_en'],
                'client' => 'Klien contoh', 'is_featured' => false,
                'summary_id' => $w['summary_id'], 'summary_en' => $w['summary_en'],
                'description_id' => $w['description_id'], 'description_en' => $w['description_en'],
                'cover_photo' => $paths[0] ?? null,
                'sort_order' => $w['order'],
            ]);

            foreach (array_slice($paths, 1) as $i => $path) {
                $work->photos()->create(['path' => $path, 'kind' => $w['kind'], 'caption' => 'Contoh', 'sort_order' => $i + 1]);
            }
        }
    }

    /**
     * The Indonesian column of some seeded rows still held the English title (runs once). Rewrites those to Indonesian,
     * but only where the Indonesian text is still exactly the seeded English one, so nothing edited in the admin changes.
     */
    private function indonesianTitles(): void
    {
        $steps = [
            'Listen & Understand' => 'Dengarkan & Pahami', 'Explore Ideas' => 'Gali Ide', 'Create & Execute' => 'Buat & Eksekusi',
            'Review & Improve' => 'Evaluasi & Perbaiki', 'Grow Together' => 'Tumbuh Bersama',
        ];

        foreach ($steps as $en => $id) {
            ProcessStep::where('title_en', $en)->where('title_id', $en)->update(['title_id' => $id]);
        }

        foreach (['Social Media Specialist' => 'Spesialis Sosial Media', 'Web Design & Development' => 'Desain & Pengembangan Web'] as $en => $id) {
            Service::where('title_en', $en)->where('title_id', $en)->update(['title_id' => $id]);
        }

        $names = [
            'Starter' => 'Pemula', 'Growth' => 'Tumbuh', 'Advance' => 'Lanjutan', 'Simple' => 'Sederhana', 'Standard' => 'Standar',
            'Complex' => 'Kompleks', 'Short' => 'Singkat', 'Medium' => 'Menengah', 'Long' => 'Panjang',
        ];

        foreach ($names as $en => $id) {
            Package::where('name_en', $en)->where('name_id', $en)->update(['name_id' => $id]);
        }

        foreach (['Social Media & Content Creation' => 'Sosial Media & Pembuatan Konten', 'Video Editing' => 'Editing Video'] as $en => $id) {
            Package::where('group_en', $en)->where('group_id', $en)->update(['group_id' => $id]);
        }

        // the two sample works pointed to "Admin > Works"
        foreach (Work::whereIn('slug', ['contoh-website', 'contoh-dokumentasi'])->get() as $work) {
            foreach (['summary_id', 'summary_en', 'description_id', 'description_en'] as $column) {
                $work->{$column} = str_replace(['Admin > Works', 'bagaimana karya website tampil'], ['Admin > Projects', 'bagaimana proyek website tampil'], (string) $work->{$column});
            }

            $work->save();
        }
    }
    /**
     * Cupfine and Maza only ordered a few designs (feed and story), they are not social media management clients
     * (runs once). Moves them to the design category and rewrites their texts, but only while those are still the
     * ones seeded earlier.
     */
    private function portfolioDesignClients(): void
    {
        $rows = [
            'cupfine' => [
                ['Feed and story content for a coffee shop: atmosphere and menu photos, plus opening-hours stories.',
                    'A few feed and story designs for a coffee shop, including opening-hours stories.'],
                ['Konten feed dan story untuk coffee shop: foto suasana dan menu, serta story informasi jam buka.',
                    'Beberapa desain feed dan story untuk coffee shop, termasuk story informasi jam buka.'],
                ['Social media content for Cupfine coffee shop. Feed posts show the atmosphere, the bar and the drinks, and designed stories share opening hours and daily updates.',
                    'A set of feed and story designs made on request for Cupfine coffee shop, including stories that share opening hours.'],
                ['Konten media sosial untuk coffee shop Cupfine. Feed menampilkan suasana, bar, dan minuman, sementara story yang didesain menyampaikan jam buka dan kabar harian.',
                    'Sekumpulan desain feed dan story yang dibuat atas pesanan untuk coffee shop Cupfine, termasuk story yang menyampaikan jam buka.'],
            ],
            'maza-coffee-and-resto' => [
                ['Feed and story content for a coffee & resto: the place, the menu, and open and closed announcements.',
                    'A few feed and story designs for a coffee & resto, including open and closed announcements.'],
                ['Konten feed dan story untuk coffee & resto: suasana tempat, menu, serta pengumuman buka dan tutup.',
                    'Beberapa desain feed dan story untuk coffee & resto, termasuk pengumuman buka dan tutup.'],
                ['Social media content for Maza Coffee & Resto. Feed posts present the building, the interior and the dishes, and stories announce when the place is open or closed.',
                    'A set of feed and story designs made on request for Maza Coffee & Resto, including stories that announce when the place is open or closed.'],
                ['Konten media sosial untuk Maza Coffee & Resto. Feed menampilkan bangunan, interior, dan sajian, sementara story mengumumkan kapan tempat buka atau tutup.',
                    'Sekumpulan desain feed dan story yang dibuat atas pesanan untuk Maza Coffee & Resto, termasuk story yang mengumumkan kapan tempat buka atau tutup.'],
            ],
        ];

        foreach ($rows as $slug => [$summaryEn, $summaryId, $descEn, $descId]) {
            $work = Work::where('slug', $slug)->first();

            if (! $work) {
                continue;
            }

            if ($work->category === 'social') {
                $work->category = 'design';
            }

            foreach ([['summary_en', $summaryEn], ['summary_id', $summaryId], ['description_en', $descEn], ['description_id', $descId]] as [$column, [$old, $new]]) {
                if ($work->{$column} === $old) {
                    $work->{$column} = $new;
                }
            }

            $work->save();
        }
    }

    /**
     * Four more social media clients (runs once): Among Roso, Haji Widayat, SRC Toko Alif and Sektor Digital. Same
     * recipe as portfolio(): pictures shipped under database/data/portfolio, the first one is the cover, the rest the
     * gallery (type, carousel set and order come from manifest.json). Edit or delete them in the admin afterwards.
     */
    private function portfolioBatch2(): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');
        $manifest = self::portfolioManifest();
        $site = Setting::get('site_name', 'WMSTUDIOS');

        $rows = [
            ['among-roso', 'Among Roso',
                'Social media content for a restaurant and coffee place: signature dishes, promos, opening hours and holiday greetings.',
                'Konten sosial media untuk restoran dan kedai kopi: menu andalan, promo, jam buka, dan ucapan hari besar.',
                "Social media content for Among Roso Coffee - Eatery in Magelang. Feed posts present the signature dishes and promos, carousels share facts about the food, and stories announce special menus, opening hours and holiday greetings. The project also covers the visual identity of the brand.",
                "Konten media sosial untuk Among Roso Coffee - Eatery di Magelang. Feed menampilkan menu andalan dan promo, carousel berbagi fakta menarik tentang hidangan, dan story menyampaikan menu spesial, jam buka, serta ucapan hari besar. Proyek ini juga mencakup identitas visual brand."],
            ['haji-widayat', 'Haji Widayat',
                'Visual identity and social media content for an artist museum, built around the Memorabilia Haji Widayat exhibition.',
                'Identitas visual dan konten sosial media untuk museum seniman, dengan fokus pada pameran Memorabilia Haji Widayat.',
                "Social media content for the Haji Widayat museum, centred on the Memorabilia Haji Widayat exhibition. Carousels introduce the museum, its collection and the artist's story, along with life values worth taking home. The project also includes the brand identity of the museum and a visual identity for the exhibition.",
                "Konten media sosial untuk Museum Haji Widayat, dengan fokus pada pameran Memorabilia Haji Widayat. Carousel memperkenalkan museum, koleksinya, dan kisah sang seniman, beserta nilai hidup yang bisa dipetik. Proyek ini juga mencakup identitas visual brand museum dan identitas visual untuk pameran."],
            ['src-toko-alif', 'SRC Toko Alif',
                'Social media content for a wholesale and retail shop: shopping tips, promos, digital products and a Ramadan catalogue.',
                'Konten sosial media untuk toko grosir dan eceran: tips belanja, promo, produk digital, dan katalog Ramadhan.',
                "Social media content for SRC Toko Alif, a wholesale and retail shop in Magelang. Carousels share money-saving shopping tips and simple recipes, feed posts show promos and digital services such as phone credit and electricity tokens, stories announce opening hours and offers, and a special Ramadan edition presents the seasonal catalogue. The visual identity is part of the project.",
                "Konten media sosial untuk SRC Toko Alif, toko grosir dan eceran di Magelang. Carousel berisi tips belanja hemat dan resep sederhana, feed menampilkan promo dan layanan produk digital seperti pulsa dan token listrik, story menyampaikan jam buka dan penawaran, serta edisi khusus Ramadhan yang memuat katalog musiman. Identitas visual brand ikut disusun dalam proyek ini."],
            ['sektor-digital', 'Sektor Digital',
                'Educational carousel content about digital marketing: strategy, market research and optimisation.',
                'Konten carousel edukasi seputar digital marketing: strategi, riset pasar, dan optimasi.',
                "Social media content for Sektor Digital: seven educational carousel series about digital marketing. Topics include making digital marketing more effective, why market research matters, optimising channels, and how roles compare in the digital workplace. The design follows the blue and yellow identity of the brand.",
                "Konten media sosial untuk Sektor Digital berupa tujuh seri carousel edukasi tentang digital marketing. Topiknya antara lain cara membuat pemasaran digital lebih efektif, pentingnya riset pasar, optimasi channel, dan perbandingan peran di dunia kerja digital. Desainnya mengikuti identitas biru dan kuning brand."],
        ];

        foreach ($rows as $i => [$slug, $name, $summaryEn, $summaryId, $descEn, $descId]) {
            $logo = null;

            if (is_file("{$base}/logos/{$slug}.png")) {
                $logo = "uploads/clients/{$slug}.png";
                $disk->put($logo, file_get_contents("{$base}/logos/{$slug}.png"));
            }

            $client = Client::firstOrNew(['name' => $name]);
            $client->logo = $logo ?? $client->logo;
            $client->sort_order = $client->sort_order ?: 8 + $i;
            $client->is_active = true;
            $client->save();

            if (Work::where('slug', $slug)->exists()) {
                continue;
            }

            $paths = [];

            foreach (glob("{$base}/{$slug}/[0-9][0-9].webp") ?: [] as $file) {
                $n = basename($file, '.webp');
                $paths[$n] = "uploads/works/{$slug}/{$n}.webp";
                $disk->put($paths[$n], file_get_contents($file));

                if (is_file("{$base}/{$slug}/{$n}_thumb.webp")) {
                    $disk->put("uploads/works/{$slug}/{$n}_thumb.webp", file_get_contents("{$base}/{$slug}/{$n}_thumb.webp"));
                }
            }

            $work = Work::create([
                'slug' => $slug, 'category' => 'social',
                'title_en' => $name, 'title_id' => $name, 'client' => $name,
                'is_featured' => false,
                'summary_en' => $summaryEn, 'summary_id' => $summaryId,
                'description_en' => $descEn, 'description_id' => $descId,
                'cover_photo' => $paths['01'] ?? null,
                'sort_order' => 8 + $i,
            ]);

            foreach ($paths as $n => $path) {
                if ($n === '01') {
                    continue;
                }

                [$kind, $caption, $order] = $manifest[$slug][$n] ?? ['other', null, (int) $n];
                $work->photos()->create(['path' => $path, 'kind' => $kind, 'caption' => $caption, 'sort_order' => $order + 1]);
            }
        }
    }
    /**
     * Four more social media management clients (runs once): Bess Coffee, Lucky Adventure, Warkop 13 and Waroeng
     * Koetjingan. Each has a hand-picked cover (cover.webp) and a gallery described in manifest.json. Bess already
     * existed as a hidden client from the company profile deck; it is switched on and given its logo.
     */
    private function portfolioBatch3(): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');
        $manifest = self::portfolioManifest();

        $rows = [
            ['bess-coffee', 'Bess Coffee & Roastery', 'Bess Coffee', 12,
                'Social media management for a coffee and roastery: moody photography of the place, open and close announcements, and menu stories.',
                'Pengelolaan sosial media untuk coffee & roastery: foto suasana tempat yang hangat, pengumuman buka dan tutup, serta story menu.',
                "Social media management for Bess Coffee & Roastery. The feed shows the place, the bar and the people who gather there in warm, low-light photography. Stories announce the opening day, closing days and daily hours, and present the menu in three colour versions.",
                "Pengelolaan media sosial untuk Bess Coffee & Roastery. Feed menampilkan tempat, bar, dan orang-orang yang berkumpul di sana lewat fotografi bernuansa hangat. Story mengumumkan hari pembukaan, hari tutup, dan jam harian, serta menampilkan menu dalam tiga versi warna."],
            ['lucky-adventure', 'Lucky Adventure', 'Lucky Adventure', 13,
                'Social media management for an outdoor gear rental: relatable carousels, mountain photography and rental announcements.',
                'Pengelolaan sosial media untuk penyewaan alat outdoor: carousel yang dekat dengan audiens, foto gunung, dan pengumuman penyewaan.',
                "Social media management for Lucky Adventure, an outdoor equipment rental. Four carousel series explain why renting beats buying, with a light and relatable tone. The feed pairs mountain photography with short reflections, stories announce rental hours and offers, and the visual identity ties it together.",
                "Pengelolaan media sosial untuk Lucky Adventure, penyewaan peralatan outdoor. Empat seri carousel menjelaskan kenapa menyewa lebih masuk akal daripada membeli, dengan gaya yang santai dan dekat dengan audiens. Feed memadukan foto pegunungan dengan renungan singkat, story menyampaikan jam sewa dan penawaran, dan identitas visual menyatukan semuanya."],
            ['warkop-13', 'Warkop 13', 'Warkop 13', 14,
                'Social media management for a neighbourhood coffee shop: photography-led feed, daily stories and an Instagram profile look.',
                'Pengelolaan sosial media untuk warung kopi: feed berbasis fotografi, story harian, dan tampilan profil Instagram.',
                "Social media management for Warkop 13. The feed is built around honest photography of the drinks, the corners of the place and the people who sit there. Stories announce opening hours and when the coffee is ready, and the Instagram profile look and visual identity keep everything consistent.",
                "Pengelolaan media sosial untuk Warkop 13. Feed dibangun dari fotografi yang jujur tentang minuman, sudut-sudut tempat, dan orang-orang yang duduk di sana. Story menyampaikan jam buka dan kabar kopi siap, sementara tampilan profil Instagram dan identitas visual menjaga semuanya tetap konsisten."],
            ['waroeng-koetjingan', 'Waroeng Koetjingan', 'Waroeng Koetjingan', 15,
                'Social media management for a food and beverage spot by the rice fields: menu carousels, food and place photography, seasonal stories.',
                'Pengelolaan sosial media untuk tempat makan dan minum di tepi sawah: carousel menu, foto makanan dan suasana, serta story musiman.',
                "Social media management for Waroeng Koetjingan. A menu carousel lists the food and drinks with prices, feed posts show the dishes and the open-air setting, and stories cover Ramadan hours, weekend greetings and holiday messages such as Nyepi. The visual identity sets the teal look of the brand.",
                "Pengelolaan media sosial untuk Waroeng Koetjingan. Carousel menu mendaftar makanan dan minuman beserta harganya, feed menampilkan hidangan dan suasana terbuka, dan story mencakup jam Ramadhan, ucapan akhir pekan, serta ucapan hari besar seperti Nyepi. Identitas visual menentukan tampilan teal brand."],
        ];

        foreach ($rows as [$slug, $clientName, $title, $order, $summaryEn, $summaryId, $descEn, $descId]) {
            $logo = null;

            if (is_file("{$base}/logos/{$slug}.png")) {
                $logo = "uploads/clients/{$slug}.png";
                $disk->put($logo, file_get_contents("{$base}/logos/{$slug}.png"));
            }

            $client = Client::firstOrNew(['name' => $clientName]);
            $client->logo = $logo ?? $client->logo;
            $client->sort_order = $client->sort_order ?: $order;
            $client->is_active = true;
            $client->save();

            if (Work::where('slug', $slug)->exists()) {
                continue;
            }

            $files = [];

            foreach (array_merge(['cover'], array_map(fn ($n) => sprintf('%02d', $n), range(1, 99))) as $n) {
                if (is_file("{$base}/{$slug}/{$n}.webp")) {
                    $files[$n] = "uploads/works/{$slug}/{$n}.webp";
                    $disk->put($files[$n], file_get_contents("{$base}/{$slug}/{$n}.webp"));

                    if (is_file("{$base}/{$slug}/{$n}_thumb.webp")) {
                        $disk->put("uploads/works/{$slug}/{$n}_thumb.webp", file_get_contents("{$base}/{$slug}/{$n}_thumb.webp"));
                    }
                }
            }

            $work = Work::create([
                'slug' => $slug, 'category' => 'social',
                'title_en' => $title, 'title_id' => $title, 'client' => $clientName,
                'is_featured' => false,
                'summary_en' => $summaryEn, 'summary_id' => $summaryId,
                'description_en' => $descEn, 'description_id' => $descId,
                'cover_photo' => $files['cover'] ?? null,
                'sort_order' => $order,
            ]);

            foreach ($files as $n => $path) {
                if ($n === 'cover') {
                    continue;
                }

                [$kind, $caption, $position] = $manifest[$slug][$n] ?? ['other', null, (int) $n];
                $work->photos()->create(['path' => $path, 'kind' => $kind, 'caption' => $caption, 'sort_order' => $position + 1]);
            }
        }
    }
    /**
     * Three more social media management clients (runs once): Karya Satria, Kopi Panda and Merah Putih. Same recipe as
     * the earlier batches: a hand-picked cover (cover.webp) plus a gallery described in manifest.json.
     */
    private function portfolioBatch4(): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');
        $manifest = self::portfolioManifest();

        $rows = [
            ['karya-satria', 'Karya Satria', 16,
                'Social media management for an outdoor advertising company: bold feed posts about billboards, wall branding and brand activation.',
                'Pengelolaan sosial media untuk perusahaan periklanan luar ruang: feed yang tegas tentang billboard, wall branding, dan brand activation.',
                "Social media management for Karya Satria, an outdoor advertising company. Nine feed posts in the brand's red explain what outdoor media can do: billboards in busy traffic, wall branding, brand activation and the safety standards behind installation.",
                "Pengelolaan media sosial untuk Karya Satria, perusahaan periklanan luar ruang. Sembilan postingan feed berwarna merah khas brand menjelaskan apa yang bisa dilakukan media luar ruang: billboard di jalan padat, wall branding, brand activation, dan standar keselamatan di balik pemasangannya."],
            ['kopi-panda', 'Kopi Panda', 17,
                'Social media management and digital positioning for a coffee brand: stories for hours and holidays, and a positioning deck.',
                'Pengelolaan sosial media dan digital positioning untuk brand kopi: story jadwal dan hari besar, serta dokumen positioning.',
                "Social media management for Kopi Panda. The project starts from a digital positioning document that sets the brand essence, purpose, target audience, personality and key differentiation. Seven stories then put it to work: weekly schedule, opening hours, days off, holiday greetings for Idul Fitri and Nyepi, and short notices.",
                "Pengelolaan media sosial untuk Kopi Panda. Proyek ini berawal dari dokumen digital positioning yang menetapkan esensi brand, tujuan, target audiens, kepribadian, dan pembeda utama. Tujuh story lalu menerapkannya: jadwal mingguan, jam buka, hari libur, ucapan Idul Fitri dan Nyepi, serta pengumuman singkat."],
            ['merah-putih', 'Merah Putih', 18,
                'Social media management and digital positioning for a coffee and eatery: community event coverage, daily open stories and a full brand strategy.',
                'Pengelolaan sosial media dan digital positioning untuk coffee & eatery: liputan acara komunitas, story buka harian, dan strategi brand lengkap.',
                "Social media management for Merah Putih Coffee & Eatery. The work starts with a digital positioning deck covering brand essence, purpose, target audience, personality, content pillars and differentiation. Two long carousel series cover a literacy and community event, the feed shows the place and the drinks, and daily stories announce opening hours and holiday messages.",
                "Pengelolaan media sosial untuk Merah Putih Coffee & Eatery. Pekerjaan diawali dengan dokumen digital positioning yang mencakup esensi brand, tujuan, target audiens, kepribadian, content pillar, dan pembeda. Dua seri carousel panjang meliput acara literasi dan komunitas, feed menampilkan tempat dan minuman, dan story harian menyampaikan jam buka serta ucapan hari besar."],
        ];

        foreach ($rows as [$slug, $name, $order, $summaryEn, $summaryId, $descEn, $descId]) {
            $logo = null;

            if (is_file("{$base}/logos/{$slug}.png")) {
                $logo = "uploads/clients/{$slug}.png";
                $disk->put($logo, file_get_contents("{$base}/logos/{$slug}.png"));
            }

            $client = Client::firstOrNew(['name' => $name]);
            $client->logo = $logo ?? $client->logo;
            $client->sort_order = $client->sort_order ?: $order;
            $client->is_active = true;
            $client->save();

            if (Work::where('slug', $slug)->exists()) {
                continue;
            }

            $files = [];

            foreach (array_merge(['cover'], array_map(fn ($n) => sprintf('%02d', $n), range(1, 99))) as $n) {
                if (is_file("{$base}/{$slug}/{$n}.webp")) {
                    $files[$n] = "uploads/works/{$slug}/{$n}.webp";
                    $disk->put($files[$n], file_get_contents("{$base}/{$slug}/{$n}.webp"));

                    if (is_file("{$base}/{$slug}/{$n}_thumb.webp")) {
                        $disk->put("uploads/works/{$slug}/{$n}_thumb.webp", file_get_contents("{$base}/{$slug}/{$n}_thumb.webp"));
                    }
                }
            }

            $work = Work::create([
                'slug' => $slug, 'category' => 'social',
                'title_en' => $name, 'title_id' => $name, 'client' => $name,
                'is_featured' => false,
                'summary_en' => $summaryEn, 'summary_id' => $summaryId,
                'description_en' => $descEn, 'description_id' => $descId,
                'cover_photo' => $files['cover'] ?? null,
                'sort_order' => $order,
            ]);

            foreach ($files as $n => $path) {
                if ($n === 'cover') {
                    continue;
                }

                [$kind, $caption, $position] = $manifest[$slug][$n] ?? ['other', null, (int) $n];
                $work->photos()->create(['path' => $path, 'kind' => $kind, 'caption' => $caption, 'sort_order' => $position + 1]);
            }
        }
    }

    /**
     * Adds pictures that were shipped after a work was created (runs once per call site): every numbered picture of the
     * given works that the work does not have yet. Pictures the admin deleted earlier are only re-added by this single
     * run, so list works here only when new material was added to them.
     */
    private function portfolioTopUp(array $slugs): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');
        $manifest = self::portfolioManifest();

        foreach ($slugs as $slug) {
            $work = Work::where('slug', $slug)->first();

            if (! $work) {
                continue;
            }

            $prefix = "uploads/works/{$slug}/";
            $known = $work->photos->pluck('path')->push($work->cover_photo)->all();

            foreach ($manifest[$slug] ?? [] as $number => [$kind, $caption, $position]) {
                $path = "{$prefix}{$number}.webp";

                if (in_array($path, $known, true) || ! is_file("{$base}/{$slug}/{$number}.webp")) {
                    continue;
                }

                // Only genuinely new numbers: a number the work once had and the admin removed stays removed
                if ((int) $number <= (int) $work->photos->map(fn ($p) => (int) basename($p->path, '.webp'))->max()) {
                    continue;
                }

                $disk->put($path, file_get_contents("{$base}/{$slug}/{$number}.webp"));

                if (is_file("{$base}/{$slug}/{$number}_thumb.webp")) {
                    $disk->put("{$prefix}{$number}_thumb.webp", file_get_contents("{$base}/{$slug}/{$number}_thumb.webp"));
                }

                $work->photos()->create(['path' => $path, 'kind' => $kind, 'caption' => $caption, 'sort_order' => $position + 1]);
            }
        }
    }
    /**
     * Reamor, Haji Widayat, Sektor Digital and Karya Satria were design jobs, not social media management (runs once).
     * Moves them to the design category and rewrites their texts, but only while the texts are still the seeded ones.
     */
    private function designOnlyClients(): void
    {
        $rows = [
            'reamor' => [
                ['Carousel content for a perfume brand: brand messages, products and a soft visual style.',
                    'Carousel designs for a perfume brand: brand messages, products and a soft visual style.'],
                ['Konten carousel untuk brand parfum: pesan brand, produk, dan gaya visual yang lembut.',
                    'Desain carousel untuk brand parfum: pesan brand, produk, dan gaya visual yang lembut.'],
                ['Carousel content for the perfume brand Reamor. Each carousel carries a brand message about love, closeness and everyday moments, paired with the products in a soft, warm visual style. Client review carousels are part of the set.',
                    'Carousel designs for the perfume brand Reamor. Each carousel carries a brand message about love, closeness and everyday moments, paired with the products in a soft, warm visual style. Client review carousels are part of the set.'],
                ['Konten carousel untuk brand parfum Reamor. Tiap carousel membawa pesan brand tentang cinta, kedekatan, dan momen sehari-hari, dipadukan dengan produk dalam gaya visual yang lembut dan hangat. Carousel ulasan klien juga termasuk dalam set ini.',
                    'Desain carousel untuk brand parfum Reamor. Tiap carousel membawa pesan brand tentang cinta, kedekatan, dan momen sehari-hari, dipadukan dengan produk dalam gaya visual yang lembut dan hangat. Carousel ulasan klien juga termasuk dalam set ini.'],
            ],
            'haji-widayat' => [
                ['Visual identity and social media content for an artist museum, built around the Memorabilia Haji Widayat exhibition.',
                    'Brand identity and carousel designs for an artist museum, built around the Memorabilia Haji Widayat exhibition.'],
                ['Identitas visual dan konten sosial media untuk museum seniman, dengan fokus pada pameran Memorabilia Haji Widayat.',
                    'Identitas brand dan desain carousel untuk museum seniman, dengan fokus pada pameran Memorabilia Haji Widayat.'],
                ["Social media content for the Haji Widayat museum, centred on the Memorabilia Haji Widayat exhibition. Carousels introduce the museum, its collection and the artist's story, along with life values worth taking home. The project also includes the brand identity of the museum and a visual identity for the exhibition.",
                    "Designs for the Haji Widayat museum, centred on the Memorabilia Haji Widayat exhibition. Carousel designs introduce the museum, its collection and the artist's story, along with life values worth taking home. The project also includes the brand identity of the museum and a visual identity for the exhibition."],
                ['Konten media sosial untuk Museum Haji Widayat, dengan fokus pada pameran Memorabilia Haji Widayat. Carousel memperkenalkan museum, koleksinya, dan kisah sang seniman, beserta nilai hidup yang bisa dipetik. Proyek ini juga mencakup identitas visual brand museum dan identitas visual untuk pameran.',
                    'Desain untuk Museum Haji Widayat, dengan fokus pada pameran Memorabilia Haji Widayat. Desain carousel memperkenalkan museum, koleksinya, dan kisah sang seniman, beserta nilai hidup yang bisa dipetik. Proyek ini juga mencakup identitas brand museum dan identitas visual untuk pameran.'],
            ],
            'sektor-digital' => [
                ['Educational carousel content about digital marketing: strategy, market research and optimisation.',
                    'Educational carousel designs about digital marketing: strategy, market research and optimisation.'],
                ['Konten carousel edukasi seputar digital marketing: strategi, riset pasar, dan optimasi.',
                    'Desain carousel edukasi seputar digital marketing: strategi, riset pasar, dan optimasi.'],
                ['Social media content for Sektor Digital: seven educational carousel series about digital marketing. Topics include making digital marketing more effective, why market research matters, optimising channels, and how roles compare in the digital workplace. The design follows the blue and yellow identity of the brand.',
                    'Designs for Sektor Digital: seven educational carousel series about digital marketing. Topics include making digital marketing more effective, why market research matters, optimising channels, and how roles compare in the digital workplace. The design follows the blue and yellow identity of the brand.'],
                ['Konten media sosial untuk Sektor Digital berupa tujuh seri carousel edukasi tentang digital marketing. Topiknya antara lain cara membuat pemasaran digital lebih efektif, pentingnya riset pasar, optimasi channel, dan perbandingan peran di dunia kerja digital. Desainnya mengikuti identitas biru dan kuning brand.',
                    'Desain untuk Sektor Digital berupa tujuh seri carousel edukasi tentang digital marketing. Topiknya antara lain cara membuat pemasaran digital lebih efektif, pentingnya riset pasar, optimasi channel, dan perbandingan peran di dunia kerja digital. Desainnya mengikuti identitas biru dan kuning brand.'],
            ],
            'karya-satria' => [
                ['Social media management for an outdoor advertising company: bold feed posts about billboards, wall branding and brand activation.',
                    'Feed post designs for an outdoor advertising company: bold layouts about billboards, wall branding and brand activation.'],
                ['Pengelolaan sosial media untuk perusahaan periklanan luar ruang: feed yang tegas tentang billboard, wall branding, dan brand activation.',
                    'Desain postingan feed untuk perusahaan periklanan luar ruang: tata letak tegas tentang billboard, wall branding, dan brand activation.'],
                ["Social media management for Karya Satria, an outdoor advertising company. Nine feed posts in the brand's red explain what outdoor media can do: billboards in busy traffic, wall branding, brand activation and the safety standards behind installation.",
                    "Feed post designs for Karya Satria, an outdoor advertising company. Nine posts in the brand's red explain what outdoor media can do: billboards in busy traffic, wall branding, brand activation and the safety standards behind installation."],
                ['Pengelolaan media sosial untuk Karya Satria, perusahaan periklanan luar ruang. Sembilan postingan feed berwarna merah khas brand menjelaskan apa yang bisa dilakukan media luar ruang: billboard di jalan padat, wall branding, brand activation, dan standar keselamatan di balik pemasangannya.',
                    'Desain postingan feed untuk Karya Satria, perusahaan periklanan luar ruang. Sembilan postingan berwarna merah khas brand menjelaskan apa yang bisa dilakukan media luar ruang: billboard di jalan padat, wall branding, brand activation, dan standar keselamatan di balik pemasangannya.'],
            ],
        ];

        foreach ($rows as $slug => [$summaryEn, $summaryId, $descEn, $descId]) {
            $work = Work::where('slug', $slug)->first();

            if (! $work) {
                continue;
            }

            if ($work->category === 'social') {
                $work->category = 'design';
            }

            foreach ([['summary_en', $summaryEn], ['summary_id', $summaryId], ['description_en', $descEn], ['description_id', $descId]] as [$column, [$old, $new]]) {
                if ($work->{$column} === $old) {
                    $work->{$column} = $new;
                }
            }

            $work->save();
        }
    }
    /** Instagram accounts of the social media clients (runs once; only fills projects that have no link yet). */
    private function instagramLinks(): void
    {
        $links = [
            'kopi-panda' => 'panda.streetcoffee',
            'omah-latareombo' => 'omah_latareombo',
            'waroeng-koetjingan' => 'waroeng_koetjingan',
            'warkop-13' => 'warungkopi.13',
            'lucky-adventure' => 'lucky.adventure88',
            'bess-coffee' => 'besscoffee_',
            'semarang-ban' => 'semarangban_id',
            'kandang-kopi' => 'kandangkopi_mgl',
            'the-overlander' => 'theoverlander.id',
            'among-roso' => 'amongroso_magelang',
            'src-toko-alif' => 'tokoalif.magelang',
            'merah-putih' => 'merahputih_coffeeandeatery',
        ];

        foreach ($links as $slug => $account) {
            Work::where('slug', $slug)->whereNull('instagram_url')->update(['instagram_url' => "https://www.instagram.com/{$account}/"]);
        }
    }
    /**
     * Documentation became a simple video project (video link plus a short text), so the made-up photo gallery sample
     * is removed (runs once). Its pictures go with it.
     */
    private function removePhotoDocumentationSample(): void
    {
        $work = Work::where('slug', 'contoh-dokumentasi')->first();

        if (! $work) {
            return;
        }

        foreach ($work->photos as $photo) {
            ImageUploader::delete($photo->path);
        }

        ImageUploader::delete($work->cover_photo);
        $work->delete();
    }
    /**
     * The first three video documentation projects (runs once). Each is just a title, a Google Drive video and a short
     * text; the cover is a frame Drive generated for the video (database/data/portfolio/<slug>/cover.webp). The texts
     * only restate the project title: edit them in Admin > Projects with the real details.
     */
    private function documentationVideos(): void
    {
        $base = database_path('data/portfolio');
        $disk = Storage::disk('public');

        $rows = [
            ['karya-satria-tubing-trip', 'Tubing Trip', 'Karya Satria', 'https://drive.google.com/file/d/1F_KL64sCmIgFWn2pQwGwDsQbhhbIM7o8/view?usp=sharing', true, 19,
                'Video documentation of the Karya Satria tubing trip.', 'Dokumentasi video kegiatan tubing trip Karya Satria.'],
            ['vw-trip-borobudur', 'VW Trip Borobudur', null, 'https://drive.google.com/file/d/1TdXckEFy3qoPVoYA5Lj-zz1ELIxIlWHS/view?usp=sharing', true, 20,
                'Video documentation of a VW trip around Borobudur.', 'Dokumentasi video perjalanan VW Trip di kawasan Borobudur.'],
            ['rewarding-sios-balen-magelang', 'Rewarding SIOS Balen Magelang', null, 'https://drive.google.com/file/d/19wXKEwDvxKoca0X0Jj1UFwTpz5g5HYwj/view?usp=sharing', false, 21,
                'Video documentation of Rewarding SIOS Balen in Magelang.', 'Dokumentasi video Rewarding SIOS Balen di Magelang.'],
        ];

        foreach ($rows as [$slug, $title, $client, $video, $vertical, $order, $descEn, $descId]) {
            if (Work::where('slug', $slug)->exists()) {
                continue;
            }

            $cover = null;

            if (is_file("{$base}/{$slug}/cover.webp")) {
                $cover = "uploads/works/{$slug}/cover.webp";
                $disk->put($cover, file_get_contents("{$base}/{$slug}/cover.webp"));

                if (is_file("{$base}/{$slug}/cover_thumb.webp")) {
                    $disk->put("uploads/works/{$slug}/cover_thumb.webp", file_get_contents("{$base}/{$slug}/cover_thumb.webp"));
                }
            }

            Work::create([
                'slug' => $slug, 'category' => 'documentation',
                'title_en' => $title, 'title_id' => $title, 'client' => $client,
                'is_featured' => false,
                'summary_en' => $descEn, 'summary_id' => $descId,
                'description_en' => $descEn, 'description_id' => $descId,
                'video_url' => $video, 'video_vertical' => $vertical,
                'cover_photo' => $cover, 'sort_order' => $order,
            ]);
        }
    }
    /**
     * Names the clients of two video documentation projects (runs once). Fills the client only while it is empty and
     * extends the text only while it is still the seeded one.
     */
    private function documentationVideoClients(): void
    {
        $rows = [
            'vw-trip-borobudur' => ['Kelompok Lansia Bugar',
                ['Video documentation of a VW trip around Borobudur.', 'Video documentation of a VW trip around Borobudur for the Lansia Bugar group.'],
                ['Dokumentasi video perjalanan VW Trip di kawasan Borobudur.', 'Dokumentasi video perjalanan VW Trip di kawasan Borobudur bersama Kelompok Lansia Bugar.']],
            'rewarding-sios-balen-magelang' => ['SRC',
                ['Video documentation of Rewarding SIOS Balen in Magelang.', 'Video documentation of Rewarding SIOS Balen in Magelang, for SRC.'],
                ['Dokumentasi video Rewarding SIOS Balen di Magelang.', 'Dokumentasi video Rewarding SIOS Balen di Magelang untuk SRC.']],
        ];

        foreach ($rows as $slug => [$client, [$oldEn, $newEn], [$oldId, $newId]]) {
            $work = Work::where('slug', $slug)->first();

            if (! $work) {
                continue;
            }

            $work->client = $work->client ?: $client;

            foreach (['summary_en' => [$oldEn, $newEn], 'description_en' => [$oldEn, $newEn], 'summary_id' => [$oldId, $newId], 'description_id' => [$oldId, $newId]] as $column => [$old, $new]) {
                if ($work->{$column} === $old) {
                    $work->{$column} = $new;
                }
            }

            $work->save();
        }
    }
    /**
     * The Overlander is two projects: the social media work and the website (runs once). The social project loses the
     * "web" category and the site link and goes back to its social-only text; a separate Web project, with the site
     * link and a placeholder cover, is created for the website. The text is only rewritten while it is still the text
     * seeded earlier. Real screenshots of the site go into the Web project's gallery (type "Website screenshot").
     */
    private function splitOverlander(): void
    {
        $site = Setting::get('site_name', 'WMSTUDIOS');
        $url = 'https://overlander-production-b73c.up.railway.app/';

        $social = Work::where('slug', 'the-overlander')->first();

        if ($social) {
            if ($social->extra_categories === 'web') {
                $social->extra_categories = null;
            }

            if ($social->project_url === $url) {
                $social->project_url = null;
            }

            $oldSummary = ['en' => 'Visual identity, social media content and a website for a travel brand: colour palette, typography, destination carousels, reel covers and a trip-catalogue site.',
                'id' => 'Identitas visual, konten media sosial, dan website untuk brand travel: palet warna, tipografi, carousel destinasi, cover reels, dan situs katalog perjalanan.'];
            $newSummary = ['en' => 'Visual identity and social media content for a travel brand: colour palette, typography, destination carousels and reel covers.',
                'id' => 'Identitas visual dan konten media sosial untuk brand travel: palet warna, tipografi, carousel destinasi, dan cover reels.'];

            foreach (['en', 'id'] as $lang) {
                if ($social->{"summary_{$lang}"} === $oldSummary[$lang]) {
                    $social->{"summary_{$lang}"} = $newSummary[$lang];
                }

                // drop the paragraph that told about the website
                $paragraphs = preg_split("/\n\n/", (string) $social->{"description_{$lang}"});
                $kept = array_filter($paragraphs, fn ($p) => ! (str_contains($p, 'also built the website') || str_contains($p, 'juga membangun websitenya')));
                $social->{"description_{$lang}"} = implode("\n\n", $kept);
            }

            $social->save();
        }

        if (Work::where('slug', 'the-overlander-web')->exists()) {
            return;
        }

        $base = database_path('data/portfolio/the-overlander-web');
        $disk = Storage::disk('public');
        $cover = null;

        if (is_file("{$base}/cover.webp")) {
            $cover = 'uploads/works/the-overlander-web/cover.webp';
            $disk->put($cover, file_get_contents("{$base}/cover.webp"));

            if (is_file("{$base}/cover_thumb.webp")) {
                $disk->put('uploads/works/the-overlander-web/cover_thumb.webp', file_get_contents("{$base}/cover_thumb.webp"));
            }
        }

        Work::create([
            'slug' => 'the-overlander-web', 'category' => 'web',
            'title_en' => 'The Overlander Website', 'title_id' => 'Website The Overlander',
            'client' => 'The Overlander', 'is_featured' => false,
            'project_url' => 'https://overlander-production-b73c.up.railway.app/',
            'summary_en' => 'A bilingual travel website: destinations, trip packages, visitor reviews and booking enquiries sent to WhatsApp.',
            'summary_id' => 'Website travel dua bahasa: destinasi, paket perjalanan, ulasan pengunjung, dan pemesanan lewat WhatsApp.',
            'description_en' => "{$site} designed and built the website for The Overlander Indonesia: a bilingual (Indonesian and English) travel site with destinations, trip packages, visitor reviews and ratings, and booking enquiries sent to WhatsApp. It has an admin panel to manage the content, and visitors can sign in with Google or email.",
            'description_id' => "{$site} merancang dan membangun website The Overlander Indonesia: situs travel dua bahasa (Indonesia dan Inggris) dengan destinasi, paket perjalanan, ulasan dan rating pengunjung, serta pemesanan lewat WhatsApp. Ada panel admin untuk mengelola isinya, dan pengunjung bisa masuk lewat Google atau email.",
            'cover_photo' => $cover, 'sort_order' => 22,
        ]);
    }
    /**
     * Real screenshots for the Overlander website project (runs once): five desktop pages and the same five on a phone,
     * and a new cover (the old one was a placeholder crop). The cover is only replaced while it is still the placeholder.
     */
    private function overlanderWebScreens(): void
    {
        $work = Work::where('slug', 'the-overlander-web')->first();

        if (! $work) {
            return;
        }

        $slug = 'the-overlander-web';
        $base = database_path("data/portfolio/{$slug}");
        $disk = Storage::disk('public');
        $manifest = self::portfolioManifest();

        if (is_file("{$base}/cover-2.webp") && (! $work->cover_photo || str_ends_with($work->cover_photo, '/cover.webp'))) {
            $disk->put("uploads/works/{$slug}/cover-2.webp", file_get_contents("{$base}/cover-2.webp"));
            $disk->put("uploads/works/{$slug}/cover-2_thumb.webp", file_get_contents("{$base}/cover-2_thumb.webp"));
            $work->cover_photo = "uploads/works/{$slug}/cover-2.webp";
            $work->save();
        }

        foreach ($manifest[$slug] ?? [] as $n => [$kind, $caption, $position]) {
            if (! is_file("{$base}/{$n}.webp")) {
                continue;
            }

            $path = "uploads/works/{$slug}/{$n}.webp";
            $disk->put($path, file_get_contents("{$base}/{$n}.webp"));
            $disk->put("uploads/works/{$slug}/{$n}_thumb.webp", file_get_contents("{$base}/{$n}_thumb.webp"));
            $work->photos()->firstOrCreate(['path' => $path], ['kind' => $kind, 'caption' => $caption, 'sort_order' => $position + 1]);
        }
    }

    /** Tools and main features of the Overlander website project (runs once; only fills what is still empty). */
    private function overlanderWebInfo(): void
    {
        $work = Work::where('slug', 'the-overlander-web')->first();

        if (! $work) {
            return;
        }

        $work->tech = $work->tech ?: 'Laravel, MySQL, Tailwind CSS, Railway, Google Sign-In';
        $work->year = $work->year ?: 2026;
        $work->features_en = $work->features_en ?: "Bilingual site (Indonesian and English)\nDestinations and trip packages with itineraries\nVisitor reviews and ratings\nBooking enquiries sent to WhatsApp\nSign in with Google or email, with email verification\nAdmin panel to manage all content";
        $work->features_id = $work->features_id ?: "Situs dua bahasa (Indonesia dan Inggris)\nDestinasi dan paket perjalanan lengkap dengan itinerary\nUlasan dan rating pengunjung\nPemesanan lewat WhatsApp\nMasuk dengan Google atau email, dengan verifikasi email\nPanel admin untuk mengelola semua konten";
        $work->save();
    }

    /**
     * Sharper desktop screenshots for the Overlander website project (runs once). Each of the five desktop pictures is
     * swapped for its high-resolution version in place, so caption, order and anything edited in the admin stay; a
     * picture the admin deleted is not brought back. The cover is only swapped while it is still the earlier one.
     */
    private function overlanderWebHighRes(): void
    {
        $work = Work::where('slug', 'the-overlander-web')->first();

        if (! $work) {
            return;
        }

        $slug = 'the-overlander-web';
        $base = database_path("data/portfolio/{$slug}");
        $disk = Storage::disk('public');
        $put = function (string $name) use ($base, $disk, $slug) {
            $disk->put("uploads/works/{$slug}/{$name}.webp", file_get_contents("{$base}/{$name}.webp"));
            $disk->put("uploads/works/{$slug}/{$name}_thumb.webp", file_get_contents("{$base}/{$name}_thumb.webp"));

            return "uploads/works/{$slug}/{$name}.webp";
        };

        if (is_file("{$base}/cover-3.webp") && str_ends_with((string) $work->cover_photo, '/cover-2.webp')) {
            $work->cover_photo = $put('cover-3');
            $work->save();
        }

        foreach (['01', '02', '03', '04', '05'] as $n) {
            if (! is_file("{$base}/{$n}-hq.webp")) {
                continue;
            }

            $photo = $work->photos()->where('path', "uploads/works/{$slug}/{$n}.webp")->first();

            if ($photo) {
                $photo->update(['path' => $put("{$n}-hq")]);
            }
        }
    }

    /**
     * Home page "Featured" projects: a mix of types instead of social media and design only (runs once). Only applies
     * while the featured set is still the one seeded first, so a choice made in the admin is never overwritten.
     */
    private function featuredMix(): void
    {
        $original = ['omah-latareombo', 'cupfine', 'kandang-kopi', 'maza-coffee-and-resto', 'reamor', 'semarang-ban'];
        $current = Work::where('is_featured', true)->pluck('slug')->all();

        if (array_diff($current, $original) || array_diff($original, $current)) {
            return;
        }

        $mix = ['omah-latareombo', 'lucky-adventure', 'cupfine', 'kandang-kopi', 'the-overlander-web', 'karya-satria-tubing-trip'];

        Work::query()->update(['is_featured' => false]);
        Work::whereIn('slug', $mix)->update(['is_featured' => true]);
    }

    /**
     * Fills the empty pictures of the home page with real work from the portfolio (runs once): a picture for each
     * service card and the three hero bubbles. They point at the projects' own cover files, so nothing is copied; only
     * empty slots are filled, and each can be replaced in the admin (Services, Site settings).
     */
    private function homeImages(): void
    {
        $cover = fn (string $slug) => Work::where('slug', $slug)->value('cover_photo');

        $services = [
            'social-media' => 'lucky-adventure',
            'documentation' => 'rewarding-sios-balen-magelang',
            'design' => 'cupfine',
            'photo-video-production' => 'kandang-kopi',
            'web-design-development' => 'the-overlander-web',
        ];

        foreach ($services as $serviceSlug => $workSlug) {
            $service = Service::where('slug', $serviceSlug)->first();

            if ($service && ! $service->cover_photo && ($path = $cover($workSlug))) {
                $service->cover_photo = $path;
                $service->save();
            }
        }

        foreach (['hero_photo_1' => 'omah-latareombo', 'hero_photo_2' => 'maza-coffee-and-resto', 'hero_photo_3' => 'semarang-ban'] as $key => $workSlug) {
            if (! Setting::get($key) && ($path = $cover($workSlug))) {
                Setting::put($key, $path);
            }
        }
    }

    /**
     * Fuller texts for projects, services and process steps (runs once). database/data/descriptions.php holds the new
     * texts and descriptions_old.json the ones seeded earlier: a field is only replaced while it still equals the old
     * text, so anything edited in the admin is left alone.
     */
    private function richerTexts(): void
    {
        $oldFile = database_path('data/descriptions_old.json');
        $old = is_file($oldFile) ? (json_decode(file_get_contents($oldFile), true) ?: []) : [];

        $this->applyTexts(require database_path('data/descriptions.php'), $old);
    }

    /**
     * The same texts in a more relaxed voice (runs once). Only fields that still equal the previous version
     * (data/descriptions.php) are replaced.
     */
    private function relaxedTexts(): void
    {
        $this->applyTexts(require database_path('data/descriptions_v2.php'), require database_path('data/descriptions.php'));
    }

    /** Replaces a text only while it still equals its expected old value, so edits made in the admin are kept. */
    private function applyTexts(array $new, array $old): void
    {
        $same = fn ($a, $b) => trim(str_replace("\r\n", "\n", (string) $a)) === trim(str_replace("\r\n", "\n", (string) $b));

        foreach ($new['projects'] as $slug => $text) {
            if ($work = Work::where('slug', $slug)->first()) {
                foreach (['id', 'en'] as $lang) {
                    if (isset($old['projects'][$slug][$lang]) && $same($work->{"description_{$lang}"}, $old['projects'][$slug][$lang])) {
                        $work->{"description_{$lang}"} = $text[$lang];
                    }
                }
                $work->save();
            }
        }

        foreach ($new['services'] as $slug => $fields) {
            if ($service = Service::where('slug', $slug)->first()) {
                foreach ($fields as $field => $value) {
                    if (isset($old['services'][$slug][$field]) && $same($service->{$field}, $old['services'][$slug][$field])) {
                        $service->{$field} = $value;
                    }
                }
                $service->save();
            }
        }

        foreach ($new['steps'] as $order => $text) {
            if ($step = ProcessStep::where('sort_order', $order)->first()) {
                foreach (['id', 'en'] as $lang) {
                    if (isset($old['steps'][$order][$lang]) && $same($step->{"description_{$lang}"}, $old['steps'][$order][$lang])) {
                        $step->{"description_{$lang}"} = $text[$lang];
                    }
                }
                $step->save();
            }
        }
    }

    /**
     * Placeholder items for the Space and Thoughts pages (runs once, and only while both are still empty), so it is
     * clear what each page needs. Each is marked "Contoh" / "Sample" and reuses project covers instead of new files;
     * replace or delete them in the admin.
     */
    private function sampleSpaceAndThoughts(): void
    {
        if (\App\Models\SpaceItem::exists() || \App\Models\Thought::exists()) {
            return;
        }

        $data = require database_path('data/samples_space_thoughts.php');
        $cover = fn (string $slug) => Work::where('slug', $slug)->value('cover_photo');

        foreach ($data['space'] as [$tag, $coverSlug, $order, $captionId, $captionEn]) {
            if ($photo = $cover($coverSlug)) {
                \App\Models\SpaceItem::create(['photo' => $photo, 'tag' => $tag, 'caption_id' => $captionId, 'caption_en' => $captionEn, 'is_active' => true, 'sort_order' => $order]);
            }
        }

        foreach ($data['thoughts'] as $t) {
            \App\Models\Thought::create([
                'slug' => $t['slug'],
                'title_id' => $t['title_id'], 'title_en' => $t['title_en'],
                'excerpt_id' => $t['excerpt_id'], 'excerpt_en' => $t['excerpt_en'],
                'body_id' => $t['body_id'], 'body_en' => $t['body_en'],
                'cover_photo' => $cover($t['cover']),
                'published_at' => $t['date'], 'is_published' => true,
            ]);
        }
    }

    /**
     * The Overlander is not only social media: the studio built its website too (runs once). Adds the "web" category and
     * the live link, and extends the texts, but only while they are still the ones seeded earlier and only where the
     * admin has not filled the field yet.
     */
    private function portfolioOverlanderWeb(): void
    {
        $work = Work::where('slug', 'the-overlander')->first();

        if (! $work) {
            return;
        }

        $site = Setting::get('site_name', 'WMSTUDIOS');

        $work->extra_categories = $work->extra_categories ?: 'web';
        $work->project_url = $work->project_url ?: 'https://overlander-production-b73c.up.railway.app/';

        $oldSummaryEn = 'Visual identity and social media content for a travel brand: colour palette, typography, destination carousels and reel covers.';
        $oldSummaryId = 'Identitas visual dan konten media sosial untuk brand travel: palet warna, tipografi, carousel destinasi, dan cover reels.';
        $oldDescEn = 'Visual identity and social media content for The Overlander Indonesia. The set includes the colour palette and typography, destination carousels, travel package posts and reel covers.';
        $oldDescId = 'Identitas visual dan konten media sosial untuk The Overlander Indonesia. Set ini mencakup palet warna dan tipografi, carousel destinasi, postingan paket perjalanan, dan cover reels.';

        if ($work->summary_en === $oldSummaryEn) {
            $work->summary_en = 'Visual identity, social media content and a website for a travel brand: colour palette, typography, destination carousels, reel covers and a trip-catalogue site.';
        }

        if ($work->summary_id === $oldSummaryId) {
            $work->summary_id = 'Identitas visual, konten media sosial, dan website untuk brand travel: palet warna, tipografi, carousel destinasi, cover reels, dan situs katalog perjalanan.';
        }

        if ($work->description_en === $oldDescEn) {
            $work->description_en = $oldDescEn . "\n\n{$site} also built the website: a bilingual (Indonesian and English) travel site with destinations, trip packages, visitor reviews and ratings, and booking enquiries sent to WhatsApp.";
        }

        if ($work->description_id === $oldDescId) {
            $work->description_id = $oldDescId . "\n\n{$site} juga membangun websitenya: situs travel dua bahasa (Indonesia dan Inggris) dengan destinasi, paket perjalanan, ulasan dan rating pengunjung, serta pemesanan lewat WhatsApp.";
        }

        $work->save();
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
                'title' => ['Spesialis Sosial Media', 'Social Media Specialist'],
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
                'title' => ['Desain & Pengembangan Web', 'Web Design & Development'],
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

        $idTitles = [
            'Listen & Understand' => 'Dengarkan & Pahami', 'Explore Ideas' => 'Gali Ide', 'Create & Execute' => 'Buat & Eksekusi',
            'Review & Improve' => 'Evaluasi & Perbaiki', 'Grow Together' => 'Tumbuh Bersama',
        ];

        foreach ($steps as $i => [$icon, $title, $descId, $descEn]) {
            ProcessStep::firstOrCreate(['title_en' => $title], [
                'icon' => $icon,
                'title_id' => $idTitles[$title] ?? $title,
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
                'group' => ['Sosial Media & Pembuatan Konten', 'Social Media & Content Creation'],
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
                'group' => ['Editing Video', 'Video Editing'],
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

        $idNames = [
            'Starter' => 'Pemula', 'Growth' => 'Tumbuh', 'Advance' => 'Lanjutan',
            'Simple' => 'Sederhana', 'Standard' => 'Standar', 'Complex' => 'Kompleks',
            'Short' => 'Singkat', 'Medium' => 'Menengah', 'Long' => 'Panjang',
        ];

        $order = 0;

        foreach ($groups as $g) {
            foreach ($g['items'] as $item) {
                [$name, $priceId, $priceEn, $featId, $featEn] = $item;
                Package::firstOrCreate(['name_en' => $name, 'group_en' => $g['group'][1]], [
                    'name_id' => $idNames[$name] ?? $name,
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
