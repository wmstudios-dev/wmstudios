<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Package;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\Setting;
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
        $this->services();
        $this->process();
        $this->packages();
        $this->faqs();
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
            ['bulb', ['Brainstorming', 'Brainstorming'], [
                'Kami duduk bareng untuk memahami tujuan, audiens, dan karakter brand-mu, lalu merumuskan ide dan arah kreatif.',
                'We sit down together to understand your goals, audience and brand character, then shape the idea and creative direction.',
            ]],
            ['camera', ['Shooting', 'Shooting'], [
                'Produksi dengan persiapan matang: moodboard, shot list, dan tim yang tahu perannya masing-masing.',
                'Production with solid preparation: moodboards, shot lists and a team that knows its role.',
            ]],
            ['film', ['Editing', 'Editing'], [
                'Editing, color grading, dan desain dipoles dengan alur revisi yang jelas dan terstruktur.',
                'Editing, color grading and design are polished through a clear, structured revision flow.',
            ]],
            ['check', ['Serah terima', 'Delivery'], [
                'Hasil akhir diserahkan dalam format yang siap pakai, lengkap dengan arahan penggunaannya.',
                'The final result is delivered in ready-to-use formats, with guidance on how to use it.',
            ]],
        ];

        foreach ($steps as $i => [$icon, $title, $desc]) {
            ProcessStep::firstOrCreate(['title_en' => $title[1]], [
                'icon' => in_array($icon, \App\Support\AdminResources::ICONS, true) ? $icon : 'spark',
                'title_id' => $title[0],
                'description_id' => $desc[0], 'description_en' => $desc[1],
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function packages(): void
    {
        $rows = [
            [
                'name' => ['Essential', 'Essential'], 'featured' => false,
                'tagline' => ['Untuk bisnis yang baru mulai tampil rapi', 'For businesses getting started'],
                'features' => [
                    "Rencana konten bulanan\nDesain feed & story\nCaption & copywriting\nLaporan bulanan singkat",
                    "Monthly content plan\nFeed & story design\nCaptions & copywriting\nShort monthly report",
                ],
            ],
            [
                'name' => ['Growth', 'Growth'], 'featured' => true,
                'tagline' => ['Untuk brand yang ingin tumbuh konsisten', 'For brands that want steady growth'],
                'features' => [
                    "Semua yang ada di Essential\nSesi produksi foto & video\nEditing reels & video pendek\nPengelolaan akun & komunitas\nRekomendasi optimasi",
                    "Everything in Essential\nPhoto & video production session\nReels & short-video editing\nAccount & community management\nOptimization recommendations",
                ],
            ],
            [
                'name' => ['Signature', 'Signature'], 'featured' => false,
                'tagline' => ['Untuk campaign dan produksi penuh', 'For campaigns and full production'],
                'features' => [
                    "Konsep & strategi campaign\nProduksi foto & video profesional\nIdentitas brand & kit desain\nWebsite atau landing page\nDokumentasi acara",
                    "Campaign concept & strategy\nProfessional photo & video production\nBrand identity & design kit\nWebsite or landing page\nEvent documentation",
                ],
            ],
        ];

        foreach ($rows as $i => $r) {
            Package::firstOrCreate(['name_en' => $r['name'][1]], [
                'name_id' => $r['name'][0],
                'tagline_id' => $r['tagline'][0], 'tagline_en' => $r['tagline'][1],
                'features_id' => $r['features'][0], 'features_en' => $r['features'][1],
                'is_featured' => $r['featured'],
                'sort_order' => $i + 1,
            ]);
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
