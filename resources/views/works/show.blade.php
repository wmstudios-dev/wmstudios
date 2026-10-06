@extends('layouts.app')

@php
    $video = $work->video();
    $cover = $work->coverUrl();
    $description = $work->t('description');
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
            <span class="chip reveal bg-brand-50 text-brand-600">{{ $work->categoryLabel() }}</span>
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

    {{-- Main media --}}
    <div class="reveal mt-10">
        @if($video && $video['type'] === 'link')
            {{-- unknown video host: just link out --}}
        @elseif($video)
            <button type="button" data-video="{{ $video['embed'] }}" data-type="{{ $video['type'] }}" data-vertical="{{ $video['vertical'] ? 1 : 0 }}"
                    class="group relative block aspect-video w-full overflow-hidden rounded-[2rem] bg-ink" aria-label="{{ __('site.works.watch_video') }}">
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
            <a href="{{ $cover }}" data-lightbox="main" data-caption="{{ $work->t('title') }}" class="flex justify-center overflow-hidden rounded-[2rem] bg-soft">
                <img src="{{ $cover }}" alt="{{ $work->t('title') }}" class="max-h-[80vh] w-auto max-w-full object-contain">
            </a>
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
        </div>

        <div class="flex flex-wrap items-start gap-3 lg:col-span-4 lg:justify-end">
            @if($work->project_url)
                <a href="{{ $work->project_url }}" target="_blank" rel="noopener" class="btn-primary">{{ __('site.works.visit_project') }} <x-icon name="arrow-up-right" class="h-4 w-4" /></a>
            @endif
            @if($video && $video['type'] === 'link')
                <a href="{{ $video['url'] }}" target="_blank" rel="noopener" class="btn-primary">{{ __('site.works.watch_video') }} <x-icon name="arrow-up-right" class="h-4 w-4" /></a>
            @endif
        </div>
    </div>

    {{-- Before / after --}}
    @if($work->hasBeforeAfter())
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
    @if($work->photos->isNotEmpty())
        @php
            $groups = collect(\App\Models\WorkPhoto::KINDS)
                ->map(fn ($label, $kind) => $work->photos->where('kind', $kind)->values())
                ->filter(fn ($photos) => $photos->isNotEmpty());
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
                        <div class="mt-6 grid max-w-3xl grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach($photos as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-{{ $kind }}" data-caption="{{ $photo->caption }}"
                                   class="reveal group block aspect-[9/16] overflow-hidden rounded-2xl bg-soft" style="--d: {{ ($loop->index % 5) * 60 }}ms">
                                    <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                </a>
                            @endforeach
                        </div>
                    @elseif($kind === 'feed')
                        {{-- Looks like an Instagram profile grid: three columns, hairline gaps, 4:5 tiles --}}
                        <div class="mt-6 grid max-w-3xl grid-cols-3 gap-1 rounded-3xl bg-ink p-1.5 sm:gap-1.5 sm:p-2">
                            @foreach($photos as $photo)
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
                    @else
                        <div class="mt-6 columns-1 gap-4 sm:columns-2 lg:columns-3">
                            @foreach($photos as $photo)
                                <a href="{{ $photo->url() }}" data-lightbox="gallery-{{ $kind }}" data-caption="{{ $photo->caption }}"
                                   class="reveal group mb-4 block break-inside-avoid overflow-hidden rounded-2xl bg-soft" style="--d: {{ ($loop->index % 3) * 70 }}ms">
                                    <img src="{{ $photo->url(true) }}" alt="{{ $photo->caption }}" loading="lazy" class="w-full transition-transform duration-700 group-hover:scale-105">
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
