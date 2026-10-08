@extends('layouts.app')

@php
    $video = $work->video();
    $cover = $work->coverUrl();
    $description = $work->t('description');
    // Documentation projects are kept simple: the video and a short text, no gallery or extras.
    $simple = $work->category === 'documentation';
    $tall = $video && ($video['vertical'] || $work->video_vertical);
@endphp

@section('title', $work->t('title'))
@section('meta_description', $work->t('summary') ?? \Illuminate\Support\Str::limit(strip_tags((string) $description), 160))
@if($cover)
    @section('og_image', $cover)
@endif

@section('content')
<article class="mx-auto max-w-7xl px-5 pt-10 sm:px-8 sm:pt-14">
    <a href="{{ route('works.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted transition-colors hover:text-brand-600">
        <x-icon name="arrow" class="h-4 w-4 rotate-180" /> {{ __('site.works.back') }}
    </a>

    <header class="mt-6 grid gap-8 lg:grid-cols-12 lg:items-end">
        <div class="lg:col-span-8">
            <span class="reveal flex flex-wrap gap-2">
                @foreach($work->categoryLabels() as $label)
                    <span class="chip bg-brand-50 text-brand-600">{{ $label }}</span>
                @endforeach
            </span>
            <h1 class="display reveal mt-4 text-[3.6rem] text-ink sm:text-[7rem]" style="--d: 60ms">{{ $work->t('title') }}</h1>
        </div>
        <dl class="reveal grid grid-cols-2 gap-6 border-t border-line pt-6 text-sm lg:col-span-4 lg:border-t-0 lg:pt-0" style="--d: 120ms">
            @if($work->client)
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">{{ __('site.works.client') }}</dt><dd class="mt-1 font-semibold text-ink">{{ $work->client }}</dd></div>
            @endif
            @if($work->year)
                <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">{{ __('site.works.year') }}</dt><dd class="mt-1 font-semibold text-ink">{{ $work->year }}</dd></div>
            @endif
        </dl>
    </header>

    {{-- Open the live site: big, with the address, because for a web project this is the main thing to look at --}}
    @if($work->project_url)
        @php $siteHost = preg_replace('/^www\./i', '', (string) parse_url($work->project_url, PHP_URL_HOST)); @endphp
        <a href="{{ $work->project_url }}" target="_blank" rel="noopener"
           class="reveal group mt-8 inline-flex max-w-full items-center gap-4 rounded-full bg-brand-500 py-3 pl-7 pr-3 text-white shadow-xl shadow-brand-500/30 transition-all duration-300 hover:-translate-y-0.5 hover:bg-brand-600 hover:shadow-2xl hover:shadow-brand-500/40 active:scale-[0.98]">
            <span class="min-w-0 text-left">
                <span class="block text-lg font-bold leading-tight sm:text-xl">{{ __('site.works.open_site') }}</span>
                @if($siteHost)<span class="block truncate text-xs text-white/75 sm:text-sm">{{ $siteHost }}</span>@endif
            </span>
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-brand-600 transition-transform duration-300 group-hover:rotate-12 sm:h-14 sm:w-14">
                <x-icon name="arrow-up-right" class="h-6 w-6" stroke="2.2" />
            </span>
        </a>
    @endif

    {{-- Main media --}}
    <div class="reveal mt-10">
        @if($video && $video['type'] === 'link')
            {{-- unknown video host: just link out --}}
        @elseif($video)
            <button type="button" data-video="{{ $video['embed'] }}" data-type="{{ $video['type'] }}" data-vertical="{{ $tall ? 1 : 0 }}"
                    class="group relative block overflow-hidden rounded-[2rem] bg-ink {{ $tall ? 'mx-auto aspect-[9/16] max-h-[80vh]' : 'aspect-video w-full' }}" aria-label="{{ __('site.works.watch_video') }}">
                @if($cover)
                    <img src="{{ $cover }}" alt="{{ $work->t('title') }}" class="h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-105 group-hover:opacity-100">
                @endif
                <span class="absolute inset-0 flex items-center justify-center">
                    <span class="flex h-20 w-20 items-center justify-center rounded-full bg-white text-brand-600 shadow-xl transition-transform duration-300 group-hover:scale-110">
                        <x-icon name="play" class="ml-1 h-8 w-8" />
                    </span>
                </span>
            </button>
        @elseif($cover)
            @if($work->coverIsLandscape())
                <a href="{{ $cover }}" data-lightbox="main" data-caption="{{ $work->t('title') }}" class="block overflow-hidden rounded-[2rem] bg-soft">
                    <img src="{{ $cover }}" alt="{{ $work->t('title') }}" style="object-position: {{ $work->coverPosition() }}" class="aspect-[16/10] max-h-[80vh] w-full object-cover">
                </a>
            @else
                <a href="{{ $cover }}" data-lightbox="main" data-caption="{{ $work->t('title') }}" class="flex justify-center overflow-hidden rounded-[2rem] bg-soft">
                    <img src="{{ $cover }}" alt="{{ $work->t('title') }}" class="max-h-[80vh] w-auto max-w-full object-contain">
                </a>
            @endif
        @endif
    </div>

    <div class="mt-12 grid gap-10 lg:grid-cols-12">
        <div class="lg:col-span-8">
            @if($work->t('summary'))
                <p class="text-2xl font-semibold leading-snug tracking-tight text-ink sm:text-3xl">{{ $work->t('summary') }}</p>
            @endif
            @if($description)
                <div class="mt-6 whitespace-pre-line text-lg leading-relaxed text-ink/80">{{ $description }}</div>
            @endif
            @php
                $features = array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $work->t('features')))));
                $tools = array_values(array_filter(array_map('trim', explode(',', (string) $work->tech))));
            @endphp
            @if($features)
                <h2 class="display mt-10 text-3xl text-ink sm:text-4xl">{{ __('site.works.features') }}</h2>
                <ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                    @foreach($features as $feature)
                        <li class="flex items-start gap-3 text-ink/80">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"><x-icon name="check" class="h-3.5 w-3.5" /></span>
                            <span>{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if($tools)
                <h2 class="display mt-10 text-3xl text-ink sm:text-4xl">{{ __('site.works.tech') }}</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($tools as $tool)
                        <span class="chip bg-soft text-ink">{{ $tool }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-start gap-3 lg:col-span-4 lg:justify-end">
            @if($work->instagram_url)
                <a href="{{ $work->instagram_url }}" target="_blank" rel="noopener" class="btn border-0 bg-[linear-gradient(45deg,#f09433_0%,#e6683c_25%,#dc2743_50%,#cc2366_75%,#bc1888_100%)] text-white shadow-lg shadow-[#dc2743]/25 hover:-translate-y-0.5 hover:brightness-110 active:scale-95"><x-icon name="instagram" class="h-4 w-4" /> {{ __('site.works.view_instagram') }}</a>
            @endif
            @if($video && $video['type'] === 'link')
                <a href="{{ $video['url'] }}" target="_blank" rel="noopener" class="btn-primary">{{ __('site.works.watch_video') }} <x-icon name="arrow-up-right" class="h-4 w-4" /></a>
            @endif
        </div>
    </div>

    {{-- More videos --}}
    @php $moreVideos = $work->moreVideos(); @endphp
    @if($moreVideos)
        <section class="mt-16">
            <h2 class="display text-4xl text-ink sm:text-5xl">{{ __('site.works.more_videos') }}</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($moreVideos as $extra)
                    <button type="button" data-video="{{ $extra['embed'] }}" data-type="{{ $extra['type'] }}" data-vertical="{{ $extra['vertical'] ? 1 : 0 }}"
                            class="reveal group text-left" style="--d: {{ ($loop->index % 3) * 70 }}ms" aria-label="{{ __('site.works.watch_video') }}: {{ $extra['title'] }}">
                        <span class="relative block aspect-video overflow-hidden rounded-3xl bg-ink">
                            @if($extra['thumb'])
                                <img src="{{ $extra['thumb'] }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-90 transition duration-700 group-hover:scale-105 group-hover:opacity-100">
                            @else
                                <span class="absolute inset-0 bg-gradient-to-br from-brand-500 via-brand-600 to-ink"></span>
                            @endif
                            <span class="absolute inset-0 flex items-center justify-center">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-brand-600 shadow-xl transition-transform duration-300 group-hover:scale-110">
                                    <x-icon name="play" class="ml-0.5 h-6 w-6" />
                                </span>
                            </span>
                            @if($extra['vertical'])
                                <span class="absolute right-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[0.65rem] font-bold uppercase tracking-wider text-ink">9:16</span>
                            @endif
                        </span>
                        <span class="mt-3 block text-sm font-semibold text-ink">{{ $extra['title'] }}</span>
                    </button>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Before / after --}}
    @if(! $simple && $work->hasBeforeAfter())
        <section class="mt-16">
            <h2 class="display text-4xl text-ink sm:text-5xl">{{ __('site.works.before_after') }}</h2>
            <div class="ba reveal relative mt-6 aspect-[4/3] overflow-hidden rounded-[2rem] bg-soft select-none sm:aspect-video">
                <img src="{{ $work->beforeUrl() }}" alt="{{ __('site.works.before') }}" class="absolute inset-0 h-full w-full object-cover" draggable="false">
                <img src="{{ $work->afterUrl() }}" alt="{{ __('site.works.after') }}" class="ba-after absolute inset-0 h-full w-full object-cover" draggable="false">
                <span class="chip absolute left-4 top-4 bg-ink/70 text-white backdrop-blur">{{ __('site.works.before') }}</span>
                <span class="chip absolute right-4 top-4 bg-brand-500 text-white">{{ __('site.works.after') }}</span>
                <span class="ba-handle pointer-events-none absolute inset-y-0 w-0.5 -translate-x-1/2 bg-white shadow">
                    <span class="absolute left-1/2 top-1/2 flex h-11 w-11 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white text-brand-600 shadow-lg">&#8596;</span>
                </span>
                <input type="range" min="0" max="100" value="50" aria-label="{{ __('site.works.before_after') }}" class="absolute inset-0 h-full w-full opacity-0">
            </div>
        </section>
    @endif

    {{-- Gallery, one section per kind of piece (feed, story, carousel, ...) --}}
    @if(! $simple && $work->photos->isNotEmpty())
        @php
            $groups = collect(\App\Models\WorkPhoto::KINDS)
                ->map(fn ($label, $kind) => $work->photos->where('kind', $kind)->values())
                ->filter(fn ($photos) => $photos->isNotEmpty());
            // phone screenshots belong to the website showcase when there are website screenshots too
            $phones = $work->photos->where('kind', 'mobile')->values();
            if ($groups->has('web') && $phones->isNotEmpty()) {
                $groups->forget('mobile');
            }
        @endphp
        <section class="mt-16">
            @if($groups->count() > 1)
                <div class="flex flex-wrap gap-2">
                    @foreach($groups as $kind => $photos)
                        <a href="#kind-{{ $kind }}" class="inline-flex items-center gap-2 rounded-full border border-line px-4 py-1.5 text-sm font-semibold text-ink transition-all duration-200 hover:border-brand-500 hover:bg-brand-500 hover:text-white active:scale-95">
                            {{ __('site.works.kinds.' . $kind) }} <span class="text-xs opacity-60">{{ $photos->count() }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            @foreach($groups as $kind => $photos)
                <div id="kind-{{ $kind }}" class="mt-12 scroll-mt-28 first:mt-8">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <h2 class="display text-4xl text-ink sm:text-5xl">{{ __('site.works.kinds.' . $kind) }}</h2>
                        @if(__('site.works.kind_hints.' . $kind) !== '')
                            <p class="text-sm text-muted">{{ __('site.works.kind_hints.' . $kind) }}</p>
                        @endif
                    </div>

                    @if($kind === 'story' || $kind === 'reel')
                        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                            {{-- at most six stories / reels shown --}}
                            @foreach($photos->take(6) as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-{{ $kind }}" data-caption="{{ $photo->caption }}"
                                   class="reveal group block aspect-[9/16] overflow-hidden rounded-2xl bg-soft" style="--d: {{ ($loop->index % 5) * 60 }}ms">
                                    <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                </a>
                            @endforeach
                        </div>
                    @elseif($kind === 'feed')
                        {{-- Looks like an Instagram profile grid: three columns, hairline gaps, 4:5 tiles --}}
                        <div class="mt-6 grid grid-cols-3 gap-1 rounded-3xl bg-ink p-1.5 sm:gap-2 sm:p-2.5">
                            {{-- at most nine feed posts shown: a 3 x 3 profile grid --}}
                            @foreach($photos->take(9) as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-{{ $kind }}" data-caption="{{ $photo->caption }}"
                                   class="group block aspect-[4/5] overflow-hidden bg-white/10 first:rounded-tl-[1.25rem]">
                                    <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" class="h-full w-full object-cover transition-all duration-500 group-hover:scale-105 group-hover:opacity-90">
                                </a>
                            @endforeach
                        </div>
                    @elseif($kind === 'carousel')
                        @php $sets = $photos->groupBy(fn ($p) => $p->caption ?? ''); @endphp
                        @foreach($sets as $setLabel => $slides)
                            <div class="relative mt-6">
                                @if($sets->count() > 1 && $setLabel !== '')
                                    <p class="mb-3 text-sm font-semibold text-ink">{{ $setLabel }} <span class="font-normal text-muted">· {{ $slides->count() }}</span></p>
                                @endif
                                <div class="-mx-5 flex snap-x snap-mandatory gap-3 overflow-x-auto px-5 pb-3 sm:-mx-8 sm:px-8 [scrollbar-width:thin]">
                                    @foreach($slides as $photo)
                                        <a href="{{ $photo->url() }}" data-lightbox="gallery-carousel-{{ $loop->parent->index }}" data-caption="{{ $photo->caption }}"
                                           class="group relative block aspect-[4/5] w-56 shrink-0 snap-start overflow-hidden rounded-2xl bg-soft sm:w-64">
                                            <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                            <span class="absolute bottom-3 right-3 rounded-full bg-ink/70 px-2.5 py-1 text-[11px] font-semibold text-white">{{ $loop->iteration }}/{{ $loop->count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                                @if($slides->count() > 2)
                                    <p class="mt-1 flex items-center gap-1.5 text-xs font-medium text-muted"><x-icon name="arrow" class="h-3.5 w-3.5" /> {{ __('site.works.swipe') }}</p>
                                @endif
                            </div>
                        @endforeach
                    @elseif($kind === 'web')
                        {{-- Website showcase: one big device, tabs for the pages, switch between desktop and phone --}}
                        @php
                            $desktopShots = $photos->map(fn ($p) => ['url' => $p->url(), 'caption' => $p->caption])->values();
                            $phoneShots = $phones->map(fn ($p) => ['url' => $p->url(), 'caption' => $p->caption])->values();
                        @endphp
                        <div data-device-show data-desktop="{{ json_encode($desktopShots) }}" data-phone="{{ json_encode($phoneShots) }}" class="mt-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                @if($phoneShots->isNotEmpty())
                                    <div class="inline-flex rounded-full bg-soft p-1 text-sm font-semibold" role="group">
                                        <button type="button" data-mode="desktop" data-active="true" class="rounded-full px-4 py-1.5 text-ink transition-colors data-[active=true]:bg-ink data-[active=true]:text-white">Desktop</button>
                                        <button type="button" data-mode="phone" data-active="false" class="rounded-full px-4 py-1.5 text-ink transition-colors data-[active=true]:bg-ink data-[active=true]:text-white">{{ __('site.works.kinds.mobile') }}</button>
                                    </div>
                                @endif
                                @if($desktopShots->count() > 1)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($desktopShots as $i => $shot)
                                            <button type="button" data-page="{{ $i }}" data-active="{{ $i === 0 ? 'true' : 'false' }}" class="rounded-full border border-line px-4 py-1.5 text-sm font-semibold text-ink transition-colors hover:border-brand-500 data-[active=true]:border-brand-500 data-[active=true]:bg-brand-500 data-[active=true]:text-white">{{ $shot['caption'] ?: ($i + 1) }}</button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="reveal mt-6 rounded-[2rem] bg-soft px-4 py-8 sm:px-10 sm:py-12">
                                {{-- laptop --}}
                                <div data-stage="desktop" class="mx-auto max-w-4xl">
                                    <a href="{{ $desktopShots[0]['url'] }}" data-device-link data-lightbox="gallery-device" data-caption="{{ $desktopShots[0]['caption'] }}" class="block rounded-t-2xl border-[6px] border-ink bg-ink shadow-xl sm:border-[10px]">
                                        <img data-device-img src="{{ $desktopShots[0]['url'] }}" alt="{{ $desktopShots[0]['caption'] }}" class="aspect-[16/10] w-full rounded-md bg-white object-cover object-top">
                                    </a>
                                    <div class="-mx-[4%] h-2.5 rounded-b-2xl bg-gradient-to-b from-ink/80 to-ink/60 sm:h-3.5"></div>
                                </div>
                                {{-- phone --}}
                                <div data-stage="phone" class="hidden">
                                    <a href="#" data-device-link data-lightbox="gallery-device" class="mx-auto block w-56 overflow-hidden rounded-[2.25rem] border-[7px] border-ink bg-ink shadow-xl sm:w-64">
                                        <img data-device-img src="" alt="" class="aspect-[739/1600] w-full rounded-[1.6rem] bg-white object-cover object-top">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <script>
                            (function () {
                                var root = document.currentScript.previousElementSibling;
                                if (!root || !root.hasAttribute('data-device-show')) return;
                                var desktop = JSON.parse(root.dataset.desktop), phone = JSON.parse(root.dataset.phone);
                                var mode = 'desktop', page = 0;
                                function pick(list, i) {
                                    var cap = desktop[i] && desktop[i].caption;
                                    return list.filter(function (s) { return cap && s.caption === cap; })[0] || list[i] || list[0];
                                }
                                function render() {
                                    var shot = pick(mode === 'phone' ? phone : desktop, page);
                                    root.querySelectorAll('[data-stage]').forEach(function (el) { el.classList.toggle('hidden', el.dataset.stage !== mode); });
                                    var stage = root.querySelector('[data-stage="' + mode + '"]');
                                    var img = stage.querySelector('[data-device-img]'), link = stage.querySelector('[data-device-link]');
                                    img.src = shot.url; img.alt = shot.caption || ''; link.href = shot.url; link.dataset.caption = shot.caption || '';
                                    root.querySelectorAll('[data-mode]').forEach(function (b) { b.dataset.active = String(b.dataset.mode === mode); });
                                    root.querySelectorAll('[data-page]').forEach(function (b) { b.dataset.active = String(Number(b.dataset.page) === page); });
                                }
                                root.addEventListener('click', function (e) {
                                    var m = e.target.closest('[data-mode]'), p = e.target.closest('[data-page]');
                                    if (m) { mode = m.dataset.mode; render(); }
                                    if (p) { page = Number(p.dataset.page); render(); }
                                });
                                phone.forEach(function (s) { new Image().src = s.url; });
                                desktop.forEach(function (s) { new Image().src = s.url; });
                                render();
                            })();
                        </script>
                    @elseif($kind === 'mobile')
                        {{-- Phone screenshots in a phone-shaped frame --}}
                        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                            @foreach($photos as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-mobile" data-caption="{{ $photo->caption }}"
                                   class="reveal group block" style="--d: {{ ($loop->index % 5) * 60 }}ms">
                                    <span class="block overflow-hidden rounded-[1.75rem] border-[5px] border-ink bg-ink shadow-md">
                                        <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" class="aspect-[739/1600] w-full object-cover object-top transition-transform duration-700 group-hover:scale-[1.03]">
                                    </span>
                                    @if($photo->caption)
                                        <span class="mt-2 block text-center text-xs font-semibold text-muted">{{ $photo->caption }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @elseif($kind === 'documentation')
                        {{-- Event photos: tight masonry that keeps every picture's own proportions --}}
                        <div class="mt-6 columns-2 gap-2 sm:columns-3 sm:gap-3">
                            @foreach($photos as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-documentation" data-caption="{{ $photo->caption }}"
                                   class="reveal group mb-2 block break-inside-avoid overflow-hidden rounded-xl bg-soft sm:mb-3" style="--d: {{ ($loop->index % 3) * 60 }}ms">
                                    <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" {!! $photo->sizeAttrs() !!} class="h-auto w-full transition-transform duration-700 group-hover:scale-105">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-6 columns-1 gap-4 sm:columns-2 lg:columns-3">
                            @foreach($photos as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-{{ $kind }}" data-caption="{{ $photo->caption }}"
                                   class="reveal group mb-4 block break-inside-avoid overflow-hidden rounded-2xl bg-soft" style="--d: {{ ($loop->index % 3) * 70 }}ms">
                                    <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" {!! $photo->sizeAttrs() !!} class="h-auto w-full transition-transform duration-700 group-hover:scale-105">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif
</article>

@if($related->isNotEmpty())
    <section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
        <h2 class="display text-5xl text-ink sm:text-6xl">{{ __('site.works.related') }}</h2>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($related as $item)
                @include('partials.work-card', ['work' => $item, 'delay' => $loop->index * 80, 'ratio' => 'aspect-[4/3]'])
            @endforeach
        </div>
    </section>
@endif

@include('partials.cta-band', ['title' => __('site.works.cta_title')])
@endsection
