@extends('layouts.app')

@section('title', __('site.process.title'))
@section('meta_description', __('site.process.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.process.title'), 'subtitle' => __('site.process.subtitle')])

<section class="mx-auto max-w-5xl px-5 sm:px-8">
    <ol class="relative space-y-5 before:absolute before:bottom-6 before:left-[1.65rem] before:top-6 before:hidden before:w-px before:bg-line sm:before:block">
        @foreach($steps as $step)
            <li class="reveal relative flex gap-6 rounded-3xl border border-line p-6 transition-colors duration-300 hover:border-brand-300 sm:p-8" style="--d: {{ min($loop->index, 4) * 60 }}ms">
                <span class="relative z-10 flex h-[3.3rem] w-[3.3rem] shrink-0 items-center justify-center rounded-2xl bg-brand-500 text-white">
                    <x-icon :name="$step->icon" class="h-6 w-6" />
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-500">{{ __('site.process.step') }} {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                    <h2 class="display mt-1 text-4xl text-ink sm:text-5xl">{{ $step->t('title') }}</h2>
                    <p class="mt-3 max-w-2xl leading-relaxed text-muted">{{ $step->t('description') }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>

@if($behindTheScenes->isNotEmpty())
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <h2 class="display reveal text-5xl text-ink sm:text-7xl">{{ __('site.process.bts_title') }}</h2>
    <p class="reveal mt-3 max-w-xl text-muted" style="--d: 60ms">{{ __('site.process.bts_subtitle') }}</p>

    <div class="mt-10 columns-2 gap-4 lg:columns-4">
        @foreach($behindTheScenes as $item)
            <a href="{{ $item->photoUrl() }}" data-lightbox="bts" data-caption="{{ $item->t('caption') }}"
               class="reveal group mb-4 block break-inside-avoid overflow-hidden rounded-2xl bg-soft" style="--d: {{ ($loop->index % 4) * 60 }}ms">
                <img src="{{ $item->photoUrl(true) }}" alt="{{ $item->t('caption') }}" loading="lazy" class="w-full transition-transform duration-700 group-hover:scale-105">
            </a>
        @endforeach
    </div>
</section>
@endif

@include('partials.cta-band')
@endsection
