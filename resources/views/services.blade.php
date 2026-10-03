@extends('layouts.app')

@section('title', __('site.services.title'))
@section('meta_description', __('site.services.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.services.title'), 'subtitle' => __('site.services.subtitle')])

{{-- Services --}}
<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="divide-y divide-line border-y border-line">
        @foreach($services as $service)
            <div id="{{ $service->slug }}" class="reveal grid scroll-mt-28 gap-8 py-12 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-500">
                        <x-icon :name="$service->icon" class="h-7 w-7" />
                    </span>
                    <h2 class="display mt-6 text-5xl text-ink sm:text-6xl">{{ $service->t('title') }}</h2>
                    <p class="mt-4 max-w-md leading-relaxed text-muted">{{ $service->t('summary') }}</p>
                    <a href="{{ route('contact', ['service' => $service->slug]) }}" class="btn-ghost mt-6">{{ __('site.services.ask') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                </div>

                @if($service->tLines('details'))
                    <div class="lg:col-span-6 lg:col-start-7">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">{{ __('site.services.get') }}</p>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach($service->tLines('details') as $line)
                                <li class="flex items-start gap-3 rounded-2xl bg-soft px-4 py-3 text-sm font-medium text-ink">
                                    <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" stroke="2.4" />
                                    <span>{{ $line }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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

{{-- FAQ --}}
@if($faqs->isNotEmpty())
<section class="mx-auto mt-24 max-w-4xl px-5 sm:px-8">
    <p class="eyebrow reveal">{{ __('site.services.faq_eyebrow') }}</p>
    <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.services.faq_title') }}</h2>

    <div class="mt-10 divide-y divide-line border-y border-line">
        @foreach($faqs as $faq)
            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-lg font-semibold text-ink transition-colors hover:text-brand-600">
                    {{ $faq->t('question') }}
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-line transition-all duration-300 group-open:rotate-45 group-open:border-brand-500 group-open:bg-brand-500 group-open:text-white">
                        <x-icon name="plus" class="h-4 w-4" />
                    </span>
                </summary>
                <p class="mt-3 max-w-2xl leading-relaxed text-muted">{{ $faq->t('answer') }}</p>
            </details>
        @endforeach
    </div>
</section>
@endif

@include('partials.cta-band')
@endsection
