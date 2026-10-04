@extends('layouts.app')

@php
    use App\Models\Setting;

    $title = Setting::t('hero_title', __('site.hero.title'));
    $subtitle = Setting::t('hero_subtitle', __('site.hero.subtitle'));

    // Words wrapped in *asterisks* in the statement get a coloured highlight.
    $marks = ['bg-lime', 'bg-lilac', 'bg-butter', 'bg-mint'];
    $n = 0;
    $statement = preg_replace_callback('/\*(.+?)\*/', function ($m) use ($marks, &$n) {
        return '<mark class="' . $marks[$n++ % count($marks)] . ' rounded-lg px-2 text-ink [box-decoration-break:clone]">' . $m[1] . '</mark>';
    }, e(__('site.home.statement')));

    // Photo pills sit inside the headline, after roughly the 1st, 2nd and 3rd quarter of the words.
    $words = preg_split('/\s+/', trim($title));
    $count = count($words);
    $slots = array_values(array_unique(array_filter([
        max(0, (int) floor($count * 0.25) - 1),
        max(1, (int) floor($count * 0.55) - 1),
        max(2, $count - 2),
    ], fn ($i) => $i < $count - 1)));
    $pills = [
        ['photo' => Setting::image('hero_photo_1', true), 'bg' => 'bg-lilac', 'icon' => 'camera', 'rot' => '-3deg'],
        ['photo' => Setting::image('hero_photo_2', true), 'bg' => 'bg-brand-200', 'icon' => 'film', 'rot' => '2deg'],
        ['photo' => Setting::image('hero_photo_3', true), 'bg' => 'bg-mint', 'icon' => 'palette', 'rot' => '-2deg'],
    ];
@endphp

@section('content')
{{-- Hero: one blue block that also holds the header, like the reference --}}
<section data-hero class="relative -mt-16 overflow-hidden rounded-bl-[3.5rem] rounded-br-[1.5rem] bg-gradient-to-tr from-brand-500 via-brand-500 to-brand-400 text-white sm:rounded-bl-[6rem] sm:rounded-br-[2rem]">
    <div class="pointer-events-none absolute -right-32 -top-32 h-[30rem] w-[30rem] rounded-full bg-white/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 left-1/4 h-[26rem] w-[26rem] rounded-full bg-brand-700/40 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-5 pb-10 pt-28 sm:px-8 lg:pt-32">
        <div class="grid items-start gap-6 lg:grid-cols-12">

            {{-- Left card: real numbers only --}}
            <div class="reveal hidden lg:col-span-2 lg:block">
                <div class="w-36 rounded-3xl bg-butter p-5 text-ink shadow-lg shadow-ink/10 transition-transform duration-300 hover:-translate-y-1 hover:rotate-[-2deg]">
                    <x-icon name="spark" class="h-6 w-6 text-brand-600" />
                    <p class="display mt-6 text-6xl leading-none">{{ $services->count() ?: '5' }}</p>
                    <p class="mt-1 text-[0.7rem] font-bold uppercase leading-tight tracking-wider">{{ __('site.hero.card_label') }}</p>
                </div>
            </div>

            {{-- Headline --}}
            <div class="lg:col-span-8 lg:text-center">
                <p class="reveal chip bg-white/15 text-white backdrop-blur"><span class="h-1.5 w-1.5 rounded-full bg-lime"></span>{{ __('site.hero.eyebrow') }}</p>

                <h1 class="display reveal mt-6 text-[3.4rem] text-white sm:text-[6rem] lg:text-[6.2rem] xl:text-[6.8rem]" style="--d: 60ms">
                    @foreach($words as $i => $word)
                        <span class="whitespace-nowrap @if($loop->last) text-lime @endif">@if($loop->last && $count > 2)<x-icon name="arrow" class="mr-1 inline-block h-[0.55em] w-[0.55em] -translate-y-[0.06em] text-white" stroke="2.6" />@endif{{ $word }}</span>
                        @if(($slot = array_search($i, $slots, true)) !== false)
                            @php $pill = $pills[$slot] ?? null; @endphp
                            @if($pill)
                                <span class="bob relative -mt-1 inline-block h-[0.62em] w-[1.35em] overflow-hidden rounded-full align-middle {{ $pill['bg'] }}" style="--r: {{ $pill['rot'] }}">
                                    @if($pill['photo'])
                                        <img src="{{ $pill['photo'] }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-ink/40"><x-icon :name="$pill['icon']" class="h-[0.36em] w-[0.36em]" stroke="1.6" /></span>
                                    @endif
                                </span>
                            @endif
                        @endif
                    @endforeach
                </h1>
            </div>

            {{-- Right card: a featured project --}}
            <div class="reveal hidden lg:col-span-2 lg:flex lg:justify-end lg:self-end" style="--d: 120ms">
                @php $feature = $works->first(); @endphp
                @if($feature)
                    <a href="{{ route('works.show', $feature) }}" class="group block w-44 overflow-hidden rounded-3xl bg-white shadow-xl shadow-ink/20 transition-all duration-300 hover:-translate-y-1.5 hover:rotate-2 active:scale-95">
                        <span class="block aspect-[4/5] overflow-hidden bg-brand-100">
                            @if($feature->coverUrl(true))
                                <img src="{{ $feature->coverUrl(true) }}" alt="" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <span class="flex h-full w-full items-center justify-center text-brand-300"><x-icon name="image" class="h-12 w-12" stroke="1.2" /></span>
                            @endif
                        </span>
                        <span class="flex items-center justify-between gap-2 bg-lime px-4 py-3 text-sm font-bold text-ink">
                            <span class="truncate">{{ $feature->t('title') }}</span>
                            <x-icon name="arrow-up-right" class="h-4 w-4 shrink-0 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" stroke="2.4" />
                        </span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Subtitle + call to action --}}
        <div class="mt-8 grid items-start gap-8 lg:mt-8 lg:grid-cols-12">
            <p class="reveal max-w-md text-base leading-relaxed text-white/85 lg:col-span-4 lg:text-sm" style="--d: 120ms">{{ $subtitle }}</p>

            <div class="reveal lg:col-span-5 lg:-mt-2 lg:text-center" style="--d: 180ms">
                <div class="flex flex-wrap gap-3 lg:justify-center">
                    <a href="{{ route('contact') }}" class="btn-light !px-8 !py-4 !text-base">{{ __('site.hero.cta_primary') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                    <a href="{{ route('works.index') }}" class="btn border border-white/40 text-white hover:bg-white/10 hover:-translate-y-0.5 !px-6 !py-4">{{ __('site.hero.cta_secondary') }}</a>
                </div>
                <p class="mt-4 text-xs text-white/70">{{ __('site.hero.free_note') }}</p>
                @if($rating = Setting::get('rating_value'))
                    <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-semibold text-white lg:justify-center" aria-label="{{ __('site.hero.rating_aria', ['n' => $rating]) }}">
                        <span class="flex gap-0.5 text-lime" aria-hidden="true">@for($s = 0; $s < 5; $s++)<x-icon name="star" class="h-4 w-4" />@endfor</span>
                        <span>{{ $rating }}</span>
                        @if($ratingLabel = Setting::t('rating_label'))<span class="font-normal text-white/75">{{ $ratingLabel }}</span>@endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    @if($clients->isNotEmpty())
        {{-- Brands we have worked with, drifting right to left. The list is repeated until one set is wider
             than the row, so the loop never shows a gap, however few brands there are. --}}
        @php
            $group = $clients;
            while ($group->count() < 8) {
                $group = $group->concat($clients);
            }
            $marqueeSeconds = max(24, $group->count() * 4);
        @endphp
        <div class="relative">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="flex flex-col gap-5 border-t border-white/20 py-7 sm:flex-row sm:items-center sm:gap-10">
                    <p class="max-w-[10rem] shrink-0 text-xs font-medium leading-snug text-white/70">{{ __('site.hero.trusted') }}</p>

                    <div class="marquee relative min-w-0 flex-1 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_8%,#000_92%,transparent)]">
                        <div class="marquee-track flex w-max items-center gap-14" style="animation-duration: {{ $marqueeSeconds }}s">
                            @foreach([1, 2] as $copy)
                                @foreach($group as $client)
                                    @include('partials.client-logo', ['client' => $client, 'hidden' => $copy === 2 || $loop->index >= $clients->count()])
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="h-6"></div>
    @endif
</section>

{{-- Statement --}}
<section class="mx-auto max-w-7xl px-5 py-20 sm:px-8 sm:py-28">
    <p class="reveal max-w-5xl text-3xl font-semibold leading-[1.2] tracking-tight text-ink sm:text-5xl">{!! $statement !!}</p>
</section>

{{-- Why us: big running headline, then the figures the admin filled in (real claims only) --}}
@php
    $stats = collect(range(1, 4))
        ->map(fn ($n) => ['value' => Setting::get("stat_{$n}_value"), 'label' => Setting::t("stat_{$n}_label")])
        ->filter(fn ($s) => filled($s['value']))
        ->values();
    $statStyles = [
        ['bg-lilac', 'spark'],
        ['bg-butter', 'heart'],
        ['bg-mint', 'check'],
        ['bg-lime', 'star'],
    ];
    $whyText = __('site.home.why_title', ['name' => Setting::get('site_name', config('app.name'))]);
@endphp
<section class="overflow-hidden pb-14 sm:pb-20" aria-label="{{ $whyText }}">
    <div class="marquee" aria-hidden="true">
        <div class="marquee-track flex w-max" style="animation-duration: 36s">
            @foreach([1, 2] as $copy)
                <div class="flex shrink-0 items-center gap-8 pr-8 sm:gap-12 sm:pr-12">
                    @foreach(range(1, 3) as $i)
                        <span class="display whitespace-nowrap text-[5rem] leading-none text-brand-500 sm:text-[9rem]">{{ $whyText }}</span>
                        <x-icon name="spark" class="h-10 w-10 shrink-0 text-lime sm:h-16 sm:w-16" />
                        <span class="display whitespace-nowrap text-[5rem] leading-none text-transparent [-webkit-text-stroke:2px_var(--color-brand-500)] sm:text-[9rem]">{{ $whyText }}</span>
                        <x-icon name="spark" class="h-10 w-10 shrink-0 text-lime sm:h-16 sm:w-16" />
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    @if($stats->isNotEmpty())
        <div class="mx-auto mt-10 max-w-7xl px-5 sm:mt-14 sm:px-8">
            <div class="grid gap-4 sm:grid-cols-2 {{ [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4'][min($stats->count(), 4)] }}">
                @foreach($stats as $stat)
                    @php [$bg, $icon] = $statStyles[$loop->index % count($statStyles)]; @endphp
                    <div class="reveal rounded-3xl {{ $bg }} p-6 transition-transform duration-300 hover:-translate-y-1 sm:p-7" style="--d: {{ $loop->index * 70 }}ms">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/70 text-ink"><x-icon :name="$icon" class="h-5 w-5" /></span>
                        <p class="display mt-8 text-6xl text-ink sm:text-7xl">{{ $stat['value'] }}</p>
                        @if($stat['label'])<p class="mt-2 text-sm font-semibold text-ink/80">{{ $stat['label'] }}</p>@endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>
{{-- Services: preview of the selected service on the left, the list of services on the right --}}
@if($services->isNotEmpty())
<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.services_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.services_title') }}</h2>
        </div>
        <a href="{{ route('services') }}" class="btn-ghost reveal">{{ __('site.home.services_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    <div class="mt-12 grid gap-10 lg:grid-cols-12 lg:gap-16" data-services>

        {{-- Preview card of the selected service --}}
        <div class="reveal lg:col-span-5" aria-live="polite">
            @foreach($services as $service)
                @php $cover = $service->coverUrl(); @endphp
                <article class="svc-preview {{ $loop->first ? 'active' : '' }}" data-preview="{{ $loop->index }}">
                    <div class="aspect-[16/10] overflow-hidden rounded-[2rem] rounded-tr-[2.5rem] bg-soft">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $service->t('title') }}" loading="lazy" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 via-lilac/40 to-brand-50 text-brand-400">
                                <x-icon :name="$service->icon" class="h-20 w-20" stroke="1.2" />
                            </div>
                        @endif
                    </div>
                    <h3 class="display mt-8 text-4xl text-ink">{{ $service->t('title') }}</h3>
                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink/80">{{ $service->t('summary') }}</p>
                    <a href="{{ route('services') }}#{{ $service->slug }}" class="btn-dark mt-6">{{ __('site.home.service_more') }}</a>
                </article>
            @endforeach
        </div>

        {{-- The services, large --}}
        <ul class="reveal divide-y divide-line border-b border-line lg:col-span-7" style="--d: 80ms">
            @foreach($services as $service)
                <li>
                    <a href="{{ route('services') }}#{{ $service->slug }}" data-svc="{{ $loop->index }}"
                       class="svc-item flex items-center justify-between gap-6 py-4 {{ $loop->first ? 'active' : '' }}">
                        <span class="svc-title display min-w-0 text-4xl sm:text-5xl lg:text-[3.3rem] xl:text-[3.6rem]">{{ $service->t('title') }}</span>
                        <span class="svc-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-lilac text-ink">
                            <x-icon :name="$service->icon" class="h-5 w-5" />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif

{{-- Featured works, in a blue band --}}
@if($works->isNotEmpty())
<section class="mx-auto mt-28 max-w-[88rem] px-3 sm:px-5">
    <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-tr from-brand-600 via-brand-500 to-brand-400 px-5 py-14 text-white sm:px-10 sm:py-20">
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 left-1/4 h-80 w-80 rounded-full bg-brand-700/50 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="reveal text-xs font-semibold uppercase tracking-[0.18em] text-lime">{{ __('site.home.works_eyebrow') }}</p>
                    <h2 class="display reveal mt-2 text-5xl text-white sm:text-7xl" style="--d: 60ms">{{ __('site.home.works_title') }}</h2>
                </div>
                <a href="{{ route('works.index') }}" class="btn-light reveal">{{ __('site.home.works_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($works as $work)
                    @include('partials.work-card', ['work' => $work, 'delay' => ($loop->index % 3) * 80])
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
{{-- Process --}}
@if($steps->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.process_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.process_title') }}</h2>
        </div>
        <a href="{{ route('process') }}" class="btn-ghost reveal">{{ __('site.home.process_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($steps as $step)
            <div class="reveal rounded-3xl border border-line p-7 transition-colors duration-300 hover:border-brand-300" style="--d: {{ $loop->index * 70 }}ms">
                <p class="display text-6xl text-brand-500">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                <h3 class="mt-6 text-lg font-bold text-ink">{{ $step->t('title') }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-muted">{{ $step->t('description') }}</p>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- Culture: one big sentence with photos from the Space page scattered around it --}}
@if($space->isNotEmpty())
@php
    $scatter = [
        'left-0 top-2 w-36 rotate-[-6deg]',
        'right-2 top-0 w-40 rotate-[5deg]',
        'left-[9%] bottom-0 w-32 rotate-[4deg]',
        'right-[10%] bottom-2 w-36 rotate-[-4deg]',
    ];
    $shots = $space->take(4)->values();
@endphp
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="relative py-6 lg:py-24">
        {{-- Phone: a small row of photos above the text --}}
        <div class="reveal mb-8 grid grid-cols-4 gap-2 lg:hidden">
            @foreach($shots as $item)
                <a href="{{ route('space') }}" class="aspect-square overflow-hidden rounded-2xl bg-soft {{ $loop->odd ? 'rotate-[-3deg]' : 'rotate-[3deg]' }}">
                    <img src="{{ $item->photoUrl(true) }}" alt="{{ $item->t('caption') }}" loading="lazy" class="h-full w-full object-cover">
                </a>
            @endforeach
        </div>

        {{-- Desktop: scattered around the text --}}
        @foreach($shots as $item)
            <a href="{{ route('space') }}" class="reveal group absolute z-10 hidden aspect-[3/4] overflow-hidden rounded-3xl bg-soft shadow-xl shadow-ink/10 transition-all duration-500 hover:z-20 hover:scale-105 hover:rotate-0 lg:block {{ $scatter[$loop->index] }}" style="--d: {{ $loop->index * 80 }}ms">
                <img src="{{ $item->photoUrl(true) }}" alt="{{ $item->t('caption') }}" loading="lazy" class="h-full w-full object-cover">
            </a>
        @endforeach

        <div class="relative mx-auto max-w-3xl text-center">
            <p class="eyebrow reveal">{{ __('site.home.space_eyebrow') }}</p>
            <p class="display reveal mt-4 text-4xl text-ink sm:text-6xl" style="--d: 60ms">{{ __('site.home.culture') }}</p>
            <a href="{{ route('space') }}" class="btn-dark reveal mt-8" style="--d: 120ms">{{ __('site.home.space_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
        </div>
    </div>
</section>
@endif
{{-- Testimonials --}}
@if($testimonials->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <p class="eyebrow reveal">{{ __('site.home.testimonials_eyebrow') }}</p>
    <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.testimonials_title') }}</h2>

    <div class="mt-10 grid gap-5 md:grid-cols-3">
        @foreach($testimonials->take(3) as $item)
            <figure class="reveal flex flex-col rounded-3xl bg-soft p-8" style="--d: {{ $loop->index * 80 }}ms">
                <span class="display text-6xl leading-none text-brand-500">&ldquo;</span>
                <blockquote class="mt-2 flex-1 text-lg leading-relaxed text-ink">{{ $item->t('quote') }}</blockquote>
                <figcaption class="mt-8 flex items-center gap-3">
                    @if($item->photoUrl())
                        <img src="{{ $item->photoUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover">
                    @else
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-600">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                    @endif
                    <span>
                        <span class="block text-sm font-bold text-ink">{{ $item->name }}</span>
                        @if($item->role)<span class="block text-xs text-muted">{{ $item->role }}</span>@endif
                    </span>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

{{-- Thoughts --}}
@if($thoughts->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.thoughts_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.thoughts_title') }}</h2>
        </div>
        <a href="{{ route('thoughts.index') }}" class="btn-ghost reveal">{{ __('site.home.thoughts_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    <div class="mt-10 grid gap-8 md:grid-cols-3">
        @foreach($thoughts as $thought)
            @include('partials.thought-card', ['thought' => $thought, 'delay' => $loop->index * 80])
        @endforeach
    </div>
</section>
@endif

@include('partials.faq', ['more' => true])

@include('partials.cta-band')
@endsection
