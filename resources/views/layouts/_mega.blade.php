{{--
    Panels that open under the header menu items (desktop). Each panel has data-panel="<key>" and is
    opened by the matching [data-mega="<key>"] link in the header (see resources/js/app.js).
    $mega comes from App\Support\MegaMenu.
--}}
@php
    $workIcons = ['photo' => 'camera', 'video' => 'film', 'design' => 'palette', 'web' => 'code', 'social' => 'megaphone'];
    $cardBase = 'group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-soft';
@endphp

<div id="mega" class="pointer-events-none absolute inset-x-0 top-full hidden md:block" aria-live="polite">
    <div class="relative mx-auto max-w-6xl px-5 sm:px-8">

        {{-- Karya --}}
        <div data-panel="works" class="mega-panel" role="region" aria-label="{{ __('site.nav.works') }}">
            <div class="grid gap-4 md:grid-cols-3">
                @foreach($mega['works'] as $work)
                    @php $cover = $work->coverUrl(true); @endphp
                    <a href="{{ route('works.show', $work) }}" class="{{ $cardBase }}">
                        @if($cover)
                            <img src="{{ $cover }}" alt="" loading="lazy" style="object-position: {{ $work->coverPosition() }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <span class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-brand-100 via-brand-50 to-white text-brand-300"><x-icon :name="$workIcons[$work->category] ?? 'spark'" class="h-14 w-14" stroke="1.2" /></span>
                        @endif
                        <span class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/15 to-transparent"></span>
                        <span class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-5 text-white">
                            <span class="min-w-0">
                                <span class="display block text-3xl leading-[0.95]">{{ $work->t('title') }}</span>
                                <span class="mt-1 block truncate text-xs text-white/75">{{ $work->categoryLabel() }}@if($work->client) · {{ $work->client }}@endif</span>
                            </span>
                            <x-icon name="arrow" class="h-6 w-6 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                        </span>
                    </a>
                @endforeach

                <a href="{{ route('works.index') }}" class="group relative flex aspect-[4/3] flex-col justify-end overflow-hidden rounded-2xl bg-brand-500 p-5 text-white">
                    <span class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-brand-400/50 blur-2xl"></span>
                    <span class="relative flex items-end justify-between gap-3">
                        <span>
                            <span class="display block text-3xl leading-[0.95]">{{ __('site.mega.works_all') }}</span>
                            @if($mega['workCount'])<span class="mt-1 block text-xs text-white/75">{{ __('site.mega.works_count', ['n' => $mega['workCount']]) }}</span>@endif
                        </span>
                        <x-icon name="arrow" class="h-6 w-6 shrink-0 transition-transform duration-300 group-hover:translate-x-1" />
                    </span>
                </a>
            </div>

            @if(count($mega['categories']))
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-line pt-4">
                    @foreach($mega['categories'] as $cat)
                        <a href="{{ route('works.index', ['c' => $cat]) }}" class="inline-flex items-center gap-1.5 rounded-full border border-line px-4 py-1.5 text-sm font-semibold text-ink transition-all duration-200 hover:border-brand-500 hover:bg-brand-500 hover:text-white active:scale-95">
                            <x-icon :name="$workIcons[$cat] ?? 'spark'" class="h-4 w-4" /> {{ __('site.categories.' . $cat) }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Layanan --}}
        <div data-panel="services" class="mega-panel" role="region" aria-label="{{ __('site.nav.services') }}">
            <div class="grid gap-5 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    <div class="grid gap-1 sm:grid-cols-2">
                        @foreach($mega['services'] as $service)
                            <a href="{{ route('services') }}#{{ $service->slug }}" class="group flex items-start gap-3 rounded-2xl p-3 transition-colors duration-200 hover:bg-brand-50 active:scale-[0.98]">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-soft text-brand-500 transition-colors duration-200 group-hover:bg-brand-500 group-hover:text-white">
                                    <x-icon :name="$service->icon" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-bold text-ink">{{ $service->t('title') }}</span>
                                    <span class="mt-0.5 line-clamp-2 block text-xs leading-relaxed text-muted">{{ $service->t('summary') }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('services') }}" class="mt-3 inline-flex items-center gap-2 px-3 text-sm font-semibold text-brand-600 hover:underline">{{ __('site.home.services_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>

                <a href="{{ route('contact') }}" class="group relative flex flex-col justify-between overflow-hidden rounded-2xl bg-gradient-to-br from-brand-500 to-brand-400 p-6 text-white lg:col-span-4">
                    <span class="pointer-events-none absolute -bottom-12 -right-12 h-44 w-44 rounded-full bg-white/10"></span>
                    <span class="relative">
                        <span class="display block text-4xl leading-[0.95]">{{ __('site.mega.cta_title') }}</span>
                        <span class="mt-3 block text-sm leading-relaxed text-white/85">{{ __('site.mega.cta_text') }}</span>
                    </span>
                    <span class="relative mt-8 flex items-center justify-between text-sm font-semibold">
                        {{ __('site.hero.cta_primary') }}
                        <x-icon name="arrow" class="h-6 w-6 transition-transform duration-300 group-hover:translate-x-1.5" />
                    </span>
                </a>
            </div>
        </div>

        {{-- Proses --}}
        <div data-panel="process" class="mega-panel" role="region" aria-label="{{ __('site.nav.process') }}">
            <div class="grid gap-3 sm:grid-cols-2 {{ $mega['steps']->count() >= 5 ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }}">
                @foreach($mega['steps'] as $step)
                    <a href="{{ route('process') }}" class="group rounded-2xl bg-soft p-5 transition-all duration-200 hover:bg-brand-500 hover:text-white active:scale-[0.98]">
                        <span class="display block text-5xl text-brand-500 transition-colors duration-200 group-hover:text-white/80">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="mt-4 block text-sm font-bold">{{ $step->t('title') }}</span>
                        <span class="mt-1 line-clamp-3 block text-xs leading-relaxed text-muted transition-colors duration-200 group-hover:text-white/80">{{ $step->t('description') }}</span>
                    </a>
                @endforeach
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-4 border-t border-line pt-4">
                <a href="{{ route('process') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:underline">{{ __('site.home.process_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                <a href="{{ route('space', ['tag' => 'bts']) }}" class="text-sm font-medium text-muted transition-colors hover:text-brand-600">{{ __('site.process.bts_title') }}</a>
            </div>
        </div>

        {{-- Space --}}
        <div data-panel="space" class="mega-panel" role="region" aria-label="{{ __('site.nav.space') }}">
            <div class="grid gap-5 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">{{ __('site.mega.browse') }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('space') }}" class="rounded-full border border-line px-4 py-1.5 text-sm font-semibold transition-all duration-200 hover:border-brand-500 hover:bg-brand-500 hover:text-white active:scale-95">{{ __('site.categories.all') }}</a>
                        @foreach($mega['spaceTags'] as $tag)
                            <a href="{{ route('space', ['tag' => $tag]) }}" class="rounded-full border border-line px-4 py-1.5 text-sm font-semibold transition-all duration-200 hover:border-brand-500 hover:bg-brand-500 hover:text-white active:scale-95">{{ __('site.space.tags.' . $tag) }}</a>
                        @endforeach
                    </div>
                    <p class="mt-5 max-w-sm text-sm leading-relaxed text-muted">{{ __('site.space.subtitle') }}</p>
                    <a href="{{ route('space') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:underline">{{ __('site.home.space_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>

                <div class="grid grid-cols-3 gap-3 lg:col-span-7">
                    @forelse($mega['spacePhotos'] as $photo)
                        <a href="{{ route('space', ['tag' => $photo->tag]) }}" class="group relative aspect-[3/4] overflow-hidden rounded-2xl bg-soft">
                            <img src="{{ $photo->photoUrl(true) }}" alt="{{ $photo->t('caption') }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                        </a>
                    @empty
                        <a href="{{ route('space') }}" class="col-span-3 flex aspect-[3/1] items-center justify-center rounded-2xl bg-soft text-brand-300"><x-icon name="image" class="h-12 w-12" stroke="1.2" /></a>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Thoughts --}}
        <div data-panel="thoughts" class="mega-panel" role="region" aria-label="{{ __('site.nav.thoughts') }}">
            @if($mega['thoughts']->isEmpty())
                <div class="flex flex-wrap items-center justify-between gap-4 py-2">
                    <p class="text-sm text-muted">{{ __('site.thoughts.empty') }}</p>
                    <a href="{{ route('thoughts.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:underline">{{ __('site.home.thoughts_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($mega['thoughts'] as $thought)
                        @php $cover = $thought->coverUrl(true); @endphp
                        <a href="{{ route('thoughts.show', $thought) }}" class="group block rounded-2xl p-2 transition-colors duration-200 hover:bg-brand-50 active:scale-[0.98]">
                            <span class="relative block aspect-[16/10] overflow-hidden rounded-xl bg-soft">
                                @if($cover)
                                    <img src="{{ $cover }}" alt="" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <span class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 via-brand-50 to-white text-brand-300"><x-icon name="pen" class="h-10 w-10" stroke="1.2" /></span>
                                @endif
                            </span>
                            <span class="mt-3 block text-xs text-muted">{{ $thought->published_at?->locale(app()->getLocale())->translatedFormat('d F Y') }}</span>
                            <span class="display mt-1 block text-2xl leading-[1] text-ink transition-colors duration-200 group-hover:text-brand-600">{{ $thought->t('title') }}</span>
                        </a>
                    @endforeach
                </div>
                <div class="mt-3 border-t border-line pt-4">
                    <a href="{{ route('thoughts.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:underline">{{ __('site.home.thoughts_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>
            @endif
        </div>

    </div>
</div>
