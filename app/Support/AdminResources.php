<?php

namespace App\Support;

use App\Models\Client;
use App\Models\Faq;
use App\Models\Package;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\SpaceItem;
use App\Models\Testimonial;
use App\Models\Thought;
use App\Models\Work;

/**
 * Describes every content type the admin can manage. One generic controller + two views
 * (list and form) read these definitions, so adding a new type is just adding an entry here.
 *
 * Field options: name, label, type (text|textarea|lines|number|select|checkbox|image|url|datetime|gallery|icon),
 * bilingual (stored as name_id / name_en), required, hint, options (select), folder (image/gallery), rows.
 */
class AdminResources
{
    public const ICONS = [
        'spark', 'camera', 'video', 'palette', 'megaphone', 'code', 'pen', 'layers',
        'users', 'chart', 'bulb', 'film', 'phone', 'globe', 'target', 'heart',
    ];

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function get(string $key): array
    {
        return self::all()[$key] ?? abort(404);
    }

    public static function categoryOptions(): array
    {
        return collect(Work::CATEGORIES)->mapWithKeys(fn ($c) => [$c => ucfirst($c)])->all();
    }

    public static function all(): array
    {
        $icons = collect(self::ICONS)->mapWithKeys(fn ($i) => [$i => ucfirst($i)])->all();

        return [
            'works' => [
                'model' => Work::class,
                'label' => 'Works',
                'singular' => 'work',
                'nav' => 'Works',
                'search' => ['title_en', 'title_id', 'client'],
                'order' => [['sort_order', 'asc'], ['id', 'desc']],
                'slug_from' => 'title_en',
                'view_route' => 'works.show',
                'columns' => [
                    ['label' => '', 'field' => 'cover_photo', 'type' => 'image'],
                    ['label' => 'Title', 'field' => 'title_id', 'type' => 'title', 'sub' => 'client'],
                    ['label' => 'Category', 'field' => 'category', 'type' => 'badge'],
                    ['label' => 'Year', 'field' => 'year', 'type' => 'text'],
                    ['label' => 'Featured', 'field' => 'is_featured', 'type' => 'bool'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                    ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => self::categoryOptions(), 'required' => true],
                    ['name' => 'client', 'label' => 'Client', 'type' => 'text'],
                    ['name' => 'year', 'label' => 'Year', 'type' => 'number'],
                    ['name' => 'summary', 'label' => 'Short summary', 'type' => 'textarea', 'rows' => 2, 'bilingual' => true, 'hint' => 'Shown on the cards.'],
                    ['name' => 'description', 'label' => 'Full description', 'type' => 'textarea', 'rows' => 6, 'bilingual' => true, 'hint' => 'Shown on the work page. Blank lines make new paragraphs.'],
                    ['name' => 'metric_value', 'label' => 'Result number (optional)', 'type' => 'text', 'hint' => 'A real headline result, e.g. 250K. Shown as a green badge on the work card. Leave empty to hide.'],
                    ['name' => 'metric_label', 'label' => 'Result caption (optional)', 'type' => 'text', 'bilingual' => true, 'hint' => 'e.g. views in 30 days'],
                    ['name' => 'cover_photo', 'label' => 'Cover photo', 'type' => 'image', 'folder' => 'works', 'hint' => 'Photos are resized and converted to WebP automatically.'],
                    ['name' => 'video_url', 'label' => 'Video link', 'type' => 'url', 'hint' => 'YouTube, Vimeo, Instagram reel, or a direct .mp4 link. For a video work, the YouTube thumbnail is used when there is no cover.'],
                    ['name' => 'project_url', 'label' => 'Live project link', 'type' => 'url', 'hint' => 'For websites: link to the live site.'],
                    ['name' => 'before_photo', 'label' => 'Before photo', 'type' => 'image', 'folder' => 'works', 'hint' => 'Fill both before and after to show a comparison slider.'],
                    ['name' => 'after_photo', 'label' => 'After photo', 'type' => 'image', 'folder' => 'works'],
                    ['name' => 'photos', 'label' => 'Gallery photos', 'type' => 'gallery', 'folder' => 'works', 'relation' => 'photos', 'model' => \App\Models\WorkPhoto::class, 'fk' => 'work_id', 'max' => 12],
                    ['name' => 'is_featured', 'label' => 'Show on the home page', 'type' => 'checkbox'],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'services' => [
                'model' => Service::class,
                'label' => 'Services',
                'singular' => 'service',
                'nav' => 'Services',
                'search' => ['title_en', 'title_id'],
                'order' => [['sort_order', 'asc'], ['id', 'asc']],
                'slug_from' => 'title_en',
                'columns' => [
                    ['label' => '', 'field' => 'cover_photo', 'type' => 'image'],
                    ['label' => 'Title', 'field' => 'title_id', 'type' => 'title', 'sub' => 'summary_id'],
                    ['label' => 'Icon', 'field' => 'icon', 'type' => 'badge'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'options' => $icons, 'required' => true],
                    ['name' => 'summary', 'label' => 'Short description', 'type' => 'textarea', 'rows' => 3, 'bilingual' => true],
                    ['name' => 'cover_photo', 'label' => 'Photo', 'type' => 'image', 'folder' => 'services', 'hint' => 'Shown in the preview card of the Services section on the home page. A landscape photo works best.'],
                    ['name' => 'details', 'label' => 'What you get', 'type' => 'lines', 'bilingual' => true, 'hint' => 'One item per line.'],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'packages' => [
                'model' => Package::class,
                'label' => 'Packages',
                'singular' => 'package',
                'nav' => 'Packages',
                'search' => ['name_en', 'name_id'],
                'order' => [['sort_order', 'asc'], ['id', 'asc']],
                'columns' => [
                    ['label' => 'Name', 'field' => 'name_id', 'type' => 'title', 'sub' => 'tagline_id'],
                    ['label' => 'Price', 'field' => 'price_label_id', 'type' => 'text'],
                    ['label' => 'Popular', 'field' => 'is_featured', 'type' => 'bool'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'bilingual' => true, 'required' => true],
                    ['name' => 'tagline', 'label' => 'Tagline', 'type' => 'text', 'bilingual' => true],
                    ['name' => 'price_label', 'label' => 'Price text', 'type' => 'text', 'bilingual' => true, 'hint' => 'Free text, e.g. "Mulai dari Rp 2,5 jt". Empty shows "Ask us".'],
                    ['name' => 'features', 'label' => 'Included', 'type' => 'lines', 'bilingual' => true, 'hint' => 'One item per line.'],
                    ['name' => 'is_featured', 'label' => 'Highlight as most popular', 'type' => 'checkbox'],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'faqs' => [
                'model' => Faq::class,
                'label' => 'FAQ',
                'singular' => 'question',
                'nav' => 'FAQ',
                'search' => ['question_en', 'question_id'],
                'order' => [['sort_order', 'asc'], ['id', 'asc']],
                'columns' => [
                    ['label' => 'Question', 'field' => 'question_id', 'type' => 'title'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'question', 'label' => 'Question', 'type' => 'text', 'bilingual' => true, 'required' => true],
                    ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'rows' => 4, 'bilingual' => true, 'required' => true],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'process' => [
                'model' => ProcessStep::class,
                'label' => 'Process steps',
                'singular' => 'step',
                'nav' => 'Process',
                'search' => ['title_en', 'title_id'],
                'order' => [['sort_order', 'asc'], ['id', 'asc']],
                'columns' => [
                    ['label' => 'Step', 'field' => 'title_id', 'type' => 'title', 'sub' => 'description_id'],
                    ['label' => 'Order', 'field' => 'sort_order', 'type' => 'text'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'bilingual' => true],
                    ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'options' => $icons, 'required' => true],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'space' => [
                'model' => SpaceItem::class,
                'label' => 'Space photos',
                'singular' => 'photo',
                'nav' => 'Space',
                'search' => ['caption_en', 'caption_id'],
                'order' => [['sort_order', 'asc'], ['id', 'desc']],
                'columns' => [
                    ['label' => '', 'field' => 'photo', 'type' => 'image'],
                    ['label' => 'Caption', 'field' => 'caption_id', 'type' => 'title'],
                    ['label' => 'Tag', 'field' => 'tag', 'type' => 'badge'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'photo', 'label' => 'Photo', 'type' => 'image', 'folder' => 'space', 'required' => true],
                    ['name' => 'tag', 'label' => 'Group', 'type' => 'select', 'options' => collect(SpaceItem::TAGS)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all(), 'required' => true],
                    ['name' => 'caption', 'label' => 'Caption', 'type' => 'text', 'bilingual' => true],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'thoughts' => [
                'model' => Thought::class,
                'label' => 'Thoughts (articles)',
                'singular' => 'article',
                'nav' => 'Thoughts',
                'search' => ['title_en', 'title_id'],
                'order' => [['published_at', 'desc'], ['id', 'desc']],
                'slug_from' => 'title_en',
                'view_route' => 'thoughts.show',
                'columns' => [
                    ['label' => '', 'field' => 'cover_photo', 'type' => 'image'],
                    ['label' => 'Title', 'field' => 'title_id', 'type' => 'title'],
                    ['label' => 'Published', 'field' => 'published_at', 'type' => 'date'],
                    ['label' => 'Live', 'field' => 'is_published', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'bilingual' => true, 'required' => true],
                    ['name' => 'excerpt', 'label' => 'Short intro', 'type' => 'textarea', 'rows' => 2, 'bilingual' => true],
                    ['name' => 'body', 'label' => 'Article', 'type' => 'textarea', 'rows' => 12, 'bilingual' => true, 'hint' => 'Blank lines make new paragraphs. Start a line with "## " for a heading.'],
                    ['name' => 'cover_photo', 'label' => 'Cover photo', 'type' => 'image', 'folder' => 'thoughts'],
                    ['name' => 'published_at', 'label' => 'Publish date', 'type' => 'datetime', 'hint' => 'Leave empty to publish now. A future date schedules it.'],
                    ['name' => 'is_published', 'label' => 'Published', 'type' => 'checkbox', 'default' => true],
                ],
            ],

            'clients' => [
                'model' => Client::class,
                'label' => 'Clients (brands you worked with)',
                'singular' => 'client',
                'nav' => 'Clients',
                'search' => ['name'],
                'order' => [['sort_order', 'asc'], ['id', 'asc']],
                'columns' => [
                    ['label' => '', 'field' => 'logo', 'type' => 'image'],
                    ['label' => 'Name', 'field' => 'name', 'type' => 'title'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Brand / client name', 'type' => 'text', 'required' => true, 'hint' => 'Shown in the "Trusted by" row on the home page (as the logo, or as text when there is no logo).'],
                    ['name' => 'logo', 'label' => 'Logo', 'type' => 'image', 'folder' => 'clients', 'hint' => 'A white or transparent-background logo looks best.'],
                    ['name' => 'url', 'label' => 'Website', 'type' => 'url'],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],

            'testimonials' => [
                'model' => Testimonial::class,
                'label' => 'Testimonials',
                'singular' => 'testimonial',
                'nav' => 'Testimonials',
                'search' => ['name', 'quote_en', 'quote_id'],
                'order' => [['sort_order', 'asc'], ['id', 'asc']],
                'columns' => [
                    ['label' => 'Person', 'field' => 'name', 'type' => 'title', 'sub' => 'role'],
                    ['label' => 'Quote', 'field' => 'quote_id', 'type' => 'text'],
                    ['label' => 'Active', 'field' => 'is_active', 'type' => 'bool'],
                ],
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['name' => 'role', 'label' => 'Role / company', 'type' => 'text'],
                    ['name' => 'quote', 'label' => 'Quote', 'type' => 'textarea', 'rows' => 4, 'bilingual' => true, 'required' => true],
                    ['name' => 'photo', 'label' => 'Photo', 'type' => 'image', 'folder' => 'testimonials'],
                    ['name' => 'is_active', 'label' => 'Visible on the site', 'type' => 'checkbox', 'default' => true],
                    ['name' => 'sort_order', 'label' => 'Order (smaller = first)', 'type' => 'number', 'default' => 0],
                ],
            ],
        ];
    }
}
