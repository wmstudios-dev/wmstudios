@extends('layouts.app')

@section('title', __('site.space.title'))
@section('meta_description', __('site.space.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.space.title'), 'subtitle' => __('site.space.subtitle')])

<section class="mx-auto max-w-7xl px-5 sm:px-8">
    @if($tags->count() > 1)
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('space') }}"
               class="rounded-full border px-5 py-2 text-sm font-semibold transition-all duration-200 {{ ! $tag ? 'border-brand-500 bg-brand-500 text-white' : 'border-line text-ink hover:border-brand-300 hover:text-brand-600' }}">{{ __('site.categories.all') }}</a>
            @foreach($tags as $t)
                <a href="{{ route('space', ['tag' => $t]) }}"
                   class="rounded-full border px-5 py-2 text-sm font-semibold transition-all duration-200 {{ $tag === $t ? 'border-brand-500 bg-brand-500 text-white' : 'border-line text-ink hover:border-brand-300 hover:text-brand-600' }}">{{ __('site.space.tags.' . $t) }}</a>
            @endforeach
        </div>
    @endif

    @if($items->isEmpty())
        <p class="py-24 text-center text-muted">{{ __('site.space.empty') }}</p>
    @else
        <div class="mt-10 columns-1 gap-4 sm:columns-2 lg:columns-3">
            @foreach($items as $item)
                <a href="{{ $item->photoUrl() }}" data-lightbox="space" data-caption="{{ $item->t('caption') }}"
                   class="reveal group relative mb-4 block break-inside-avoid overflow-hidden rounded-2xl bg-soft" style="--d: {{ ($loop->index % 3) * 70 }}ms">
                    <img src="{{ $item->photoUrl(true) }}" alt="{{ $item->t('caption') }}" loading="lazy" class="w-full transition-transform duration-700 group-hover:scale-105">
                    @if($item->t('caption'))
                        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/70 to-transparent p-4 pt-10 text-sm font-medium text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">{{ $item->t('caption') }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</section>

@include('partials.cta-band')
@endsection
