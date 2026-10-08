@extends('layouts.app')

@php
    $works = $service->relatedWorks(6);
    $packages = $service->relatedPackages();
    $cover = $service->coverUrl();
    $lines = $service->tLines('details');
@endphp

@section('title', $service->t('title'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($service->t('summary')), 160))
@if($cover)
    @section('og_image', $cover)
@endif

@section('content')
@include('partials.page-header', ['eyebrow' => __('site.service_page.eyebrow'), 'title' => $service->t('title'), 'subtitle' => $service->t('summary')])

{{-- What you get --}}
<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted transition-colors hover:text-brand-600">
        <x-icon name="arrow" class="h-4 w-4 rotate-180" /> {{ __('site.service_page.back') }}
    </a>

    <div class="mt-8 grid gap-10 lg:grid-cols-12 lg:gap-14">
        <div class="lg:col-span-7">
            <h2 class="display reveal text-4xl text-ink sm:text-6xl">{{ __('site.service_page.gets_title') }}</h2>
            <p class="reveal mt-3 max-w-xl text-muted" style="--d: 60ms">{{ __('site.service_page.gets_text') }}</p>

            @if($lines)
                <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                    @foreach($lines as $line)
                        <li class="reveal flex items-start gap-3 rounded-2xl bg-soft px-4 py-3.5 text-sm font-medium text-ink" style="--d: {{ ($loop->index % 4) * 50 }}ms">
                            <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" stroke="2.4" />
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('contact', ['service' => $service->slug]) }}" class="btn-dark">{{ __('site.services.ask') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                @if($wa = \App\Support\WhatsApp::chatUrl(__('site.service_page.wa_message', ['service' => $service->t('title')])))
                    <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn-ghost"><x-icon name="whatsapp" class="h-4 w-4" /> WhatsApp</a>
                @endif
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="reveal aspect-[4/5] overflow-hidden rounded-[2rem] rounded-tr-[3rem] bg-soft" style="--d: 80ms">
                @if($cover)
                    <img src="{{ $cover }}" alt="{{ $service->t('title') }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 via-lilac/40 to-brand-50 text-brand-400">
                        <x-icon :name="$service->icon" class="h-20 w-20" stroke="1.2" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Related projects --}}
@if($works->isNotEmpty())
<section class="mx-auto mt-24 max-w-[88rem] px-3 sm:px-5">
    <div class="mx-auto max-w-7xl px-2 sm:px-3">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <h2 class="display reveal text-4xl text-ink sm:text-6xl">{{ __('site.service_page.works_title') }}</h2>
            <a href="{{ route('works.index') }}" class="btn-ghost reveal">{{ __('site.service_page.works_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
        </div>
        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($works as $work)
                @include('partials.work-card', ['work' => $work, 'delay' => ($loop->index % 3) * 80])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Packages that belong to this service --}}
@if($packages->isNotEmpty())
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <h2 class="display reveal text-4xl text-ink sm:text-6xl">{{ __('site.service_page.packages_title') }}</h2>
    <p class="reveal mt-3 max-w-xl text-muted" style="--d: 60ms">{{ __('site.service_page.packages_text') }}</p>

    <div class="mt-8 grid gap-5 md:grid-cols-3">
        @foreach($packages as $package)
            @php $featured = $package->is_featured; @endphp
            <article class="reveal flex flex-col rounded-[2rem] p-7 {{ $featured ? 'bg-brand-500 text-white' : 'border border-line bg-white' }}" style="--d: {{ $loop->index * 70 }}ms">
                <h3 class="display text-4xl">{{ $package->t('name') }}</h3>
                @if($package->t('tagline'))
                    <p class="mt-2 text-sm {{ $featured ? 'text-white/80' : 'text-muted' }}">{{ $package->t('tagline') }}</p>
                @endif
                @if($package->t('price_label'))
                    <p class="display mt-5 text-3xl {{ $featured ? 'text-lime' : 'text-brand-500' }}">{{ $package->t('price_label') }}</p>
                @endif
                <ul class="mt-5 space-y-2.5 text-sm">
                    @foreach($package->tLines('features') as $line)
                        <li class="flex items-start gap-2.5">
                            <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 {{ $featured ? 'text-lime' : 'text-brand-500' }}" stroke="2.4" />
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('contact', ['service' => $package->t('group') ?: $service->slug]) }}" class="mt-8 {{ $featured ? 'btn-light' : 'btn-dark' }}">{{ __('site.services.choose') }}</a>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- Free consultation and free audit --}}
@include('partials.consult-band')

{{-- The other services --}}
@if($others->isNotEmpty())
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <h2 class="display reveal text-3xl text-ink sm:text-5xl">{{ __('site.service_page.other_title') }}</h2>
    <div class="mt-6 flex flex-wrap gap-2.5">
        @foreach($others as $other)
            <a href="{{ route('services.show', $other) }}" class="inline-flex items-center gap-2 rounded-full border border-line px-5 py-2.5 text-sm font-semibold text-ink transition-all duration-200 hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600">
                <x-icon :name="$other->icon" class="h-4 w-4" /> {{ $other->t('title') }}
            </a>
        @endforeach
    </div>
</section>
@endif

@include('partials.faq')

@include('partials.cta-band', ['title' => __('site.service_page.cta_title'), 'text' => __('site.service_page.cta_text')])
@endsection
