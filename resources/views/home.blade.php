@extends('layouts.app')

@php
    use App\Models\Setting;

    $title = Setting::t('hero_title', __('site.hero.title'));
    $subtitle = Setting::t('hero_subtitle', __('site.hero.subtitle'));

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
{{-- Hero --}}
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute -right-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-brand-100/70 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-5 pb-16 pt-12 sm:px-8 sm:pb-20 sm:pt-16">
        <p class="reveal chip bg-brand-50 text-brand-600"><span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>{{ __('site.hero.eyebrow') }}</p>

        <h1 class="display reveal mt-6 max-w-6xl text-[3.6rem] text-ink sm:text-[7rem] lg:text-[8rem]" style="--d: 60ms">
            @foreach($words as $i => $word)
                {{ $word }}
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

        <p class="reveal mt-8 max-w-2xl text-lg leading-relaxed text-muted sm:text-xl" style="--d: 120ms">{{ $subtitle }}</p>

        <div class="reveal mt-10 flex flex-wrap gap-3" style="--d: 180ms">
            <a href="{{ route('contact') }}" class="btn-primary">{{ __('site.hero.cta_primary') }} <x-icon name="arrow" class="h-4 w-4" /></a>
            <a href="{{ route('works.index') }}" class="btn-ghost">{{ __('site.hero.cta_secondary') }}</a>
        </div>
    </div>

    @if($clients->isNotEmpty())
        <div class="relative border-y border-line bg-white/60 py-7 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center gap-8 px-5 sm:px-8">
                <p class="hidden shrink-0 text-xs font-semibold uppercase tracking-[0.18em] text-muted sm:block">{{ __('site.hero.trusted') }}</p>
                <div class="marquee relative min-w-0 flex-1 overflow-hidden [mask-image:linear-gradient(90deg,transparent,#000_8%,#000_92%,transparent)]">
                    <div class="marquee-track flex w-max items-center gap-14">
                        @foreach([1, 2] as $copy)
                            @foreach($clients as $client)
                                <span class="flex shrink-0 items-center" @if($copy === 2) aria-hidden="true" @endif>
                                    @if($client->logoUrl())
                                        <img src="{{ $client->logoUrl() }}" alt="{{ $client->name }}" loading="lazy" class="h-8 w-auto opacity-60 grayscale transition hover:opacity-100 hover:grayscale-0">
                                    @else
                                        <span class="display text-2xl text-ink/45">{{ $client->name }}</span>
                                    @endif
                                </span>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>

{{-- Statement --}}
<section class="mx-auto max-w-7xl px-5 py-20 sm:px-8 sm:py-28">
    <p class="reveal max-w-5xl text-3xl font-semibold leading-[1.2] tracking-tight text-ink sm:text-5xl">{{ __('site.home.statement') }}</p>
</section>

{{-- Services --}}
@if($services->isNotEmpty())
<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.services_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.services_title') }}</h2>
        </div>
        <a href="{{ route('services') }}" class="btn-ghost reveal">{{ __('site.home.services_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    @php
        // Three cards per row; if the last row is short, its cards stretch to fill it.
        $remainder = $services->count() % 3;
    @endphp
    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        @foreach($services as $service)
            @php
                $inLastRow = $remainder > 0 && $loop->iteration > $services->count() - $remainder;
                $span = $inLastRow ? ($remainder === 2 ? 'lg:col-span-3' : 'lg:col-span-6') : 'lg:col-span-2';
            @endphp
            <a href="{{ route('services') }}#{{ $service->slug }}"
               class="reveal group flex flex-col rounded-3xl bg-soft p-7 {{ $span }} active:scale-[0.98] transition-all duration-300 hover:-translate-y-1 hover:bg-brand-500 hover:shadow-xl hover:shadow-brand-500/20"
               style="--d: {{ ($loop->index % 3) * 70 }}ms">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-brand-500 transition-colors duration-300 group-hover:bg-white/15 group-hover:text-white">
                    <x-icon :name="$service->icon" class="h-6 w-6" />
                </span>
                <h3 class="display mt-8 text-4xl text-ink transition-colors duration-300 group-hover:text-white">{{ $service->t('title') }}</h3>
                <p class="mt-3 flex-1 text-sm leading-relaxed text-muted transition-colors duration-300 group-hover:text-white/80">{{ $service->t('summary') }}</p>
                <x-icon name="arrow-up-right" class="mt-6 h-6 w-6 text-ink/30 transition-all duration-300 group-hover:translate-x-1 group-hover:text-white" />
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Featured works --}}
@if($works->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.works_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.works_title') }}</h2>
        </div>
        <a href="{{ route('works.index') }}" class="btn-ghost reveal">{{ __('site.home.works_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($works as $work)
            @include('partials.work-card', ['work' => $work, 'delay' => ($loop->index % 3) * 80])
        @endforeach
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

{{-- Space --}}
@if($space->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.space_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.space_title') }}</h2>
        </div>
        <a href="{{ route('space') }}" class="btn-ghost reveal">{{ __('site.home.space_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach($space as $item)
            <a href="{{ route('space') }}" class="reveal group relative aspect-[3/4] overflow-hidden rounded-2xl bg-soft {{ $loop->odd ? 'lg:mt-8' : '' }}" style="--d: {{ $loop->index * 60 }}ms">
                <img src="{{ $item->photoUrl(true) }}" alt="{{ $item->t('caption') }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
            </a>
        @endforeach
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

@include('partials.cta-band')
@endsection
