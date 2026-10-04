@extends('layouts.app')

@section('title', __('site.services.title'))
@section('meta_description', __('site.services.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.services.title'), 'subtitle' => __('site.services.subtitle')])

{{-- Quick jump between services --}}
@if($services->count() > 1)
<nav class="sticky top-16 z-30 -mt-4 mb-10 border-b border-line bg-white/85 backdrop-blur-md" aria-label="{{ __('site.nav.services') }}">
    <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-5 py-3 sm:px-8 [scrollbar-width:none]">
        @foreach($services as $service)
            <a href="#{{ $service->slug }}" class="inline-flex shrink-0 items-center gap-2 rounded-full border border-line px-4 py-1.5 text-sm font-semibold text-ink transition-all duration-200 hover:border-brand-500 hover:bg-brand-500 hover:text-white active:scale-95">
                <x-icon :name="$service->icon" class="h-4 w-4" /> {{ $service->t('title') }}
            </a>
        @endforeach
    </div>
</nav>
@endif

{{-- Services --}}
<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="divide-y divide-line border-y border-line">
        @foreach($services as $service)
            @php $cover = $service->coverUrl(); @endphp
            <div id="{{ $service->slug }}" class="reveal grid scroll-mt-40 gap-8 py-12 lg:grid-cols-12 lg:gap-12">
                <div class="lg:col-span-5">
                    <div class="aspect-[16/10] overflow-hidden rounded-[2rem] rounded-tr-[2.5rem] bg-soft">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $service->t('title') }}" loading="lazy" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 via-lilac/40 to-brand-50 text-brand-400">
                                <x-icon :name="$service->icon" class="h-16 w-16" stroke="1.2" />
                            </div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <p class="display text-3xl text-brand-500">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                    <h2 class="display mt-1 text-5xl text-ink sm:text-6xl">{{ $service->t('title') }}</h2>
                    <p class="mt-4 max-w-xl leading-relaxed text-muted">{{ $service->t('summary') }}</p>

                    @if($service->tLines('details'))
                        <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                            @foreach($service->tLines('details') as $line)
                                <li class="flex items-start gap-3 rounded-2xl bg-soft px-4 py-3 text-sm font-medium text-ink">
                                    <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" stroke="2.4" />
                                    <span>{{ $line }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <a href="{{ route('contact', ['service' => $service->slug]) }}" class="btn-dark mt-7">{{ __('site.services.ask') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>
            </div>
        @endforeach
    </div>
</section>
{{-- Packages --}}
@if($packages->isNotEmpty())
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <p class="eyebrow reveal">{{ __('site.services.packages_eyebrow') }}</p>
    <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.services.packages_title') }}</h2>
    <p class="reveal mt-4 max-w-xl text-muted" style="--d: 100ms">{{ __('site.services.packages_note') }}</p>

    <div class="mt-10 grid gap-5 lg:grid-cols-3">
        @foreach($packages as $package)
            @php $featured = $package->is_featured; @endphp
            <div class="reveal relative flex flex-col rounded-[2rem] p-8 {{ $featured ? 'bg-brand-500 text-white shadow-xl shadow-brand-500/25' : 'border border-line bg-white' }}" style="--d: {{ $loop->index * 80 }}ms">
                @if($featured)
                    <span class="chip absolute right-6 top-6 bg-white/15 text-white">{{ __('site.services.popular') }}</span>
                @endif
                <h3 class="display text-4xl {{ $featured ? 'text-white' : 'text-ink' }}">{{ $package->t('name') }}</h3>
                @if($package->t('tagline'))
                    <p class="mt-2 text-sm {{ $featured ? 'text-white/75' : 'text-muted' }}">{{ $package->t('tagline') }}</p>
                @endif
                <p class="mt-6 text-2xl font-bold tracking-tight">{{ $package->t('price_label') ?? __('site.services.ask_price') }}</p>

                <ul class="mt-6 flex-1 space-y-3 text-sm">
                    @foreach($package->tLines('features') as $line)
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 {{ $featured ? 'text-white' : 'text-brand-500' }}" stroke="2.4" />
                            <span class="{{ $featured ? 'text-white/90' : 'text-ink/80' }}">{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('contact') }}" class="mt-8 {{ $featured ? 'btn-light' : 'btn-dark' }}">{{ __('site.services.choose') }}</a>
            </div>
        @endforeach
    </div>
</section>
@endif

@include('partials.faq')

@include('partials.cta-band')
@endsection
