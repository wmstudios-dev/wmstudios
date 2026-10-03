@extends('layouts.app')

@php $cover = $thought->coverUrl(); @endphp

@section('title', $thought->t('title'))
@section('meta_description', $thought->t('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags((string) $thought->t('body')), 160))
@if($cover)
    @section('og_image', $cover)
@endif

@section('content')
<article class="mx-auto max-w-3xl px-5 pt-10 sm:px-8 sm:pt-14">
    <a href="{{ route('thoughts.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted transition-colors hover:text-brand-600">
        <x-icon name="arrow" class="h-4 w-4 rotate-180" /> {{ __('site.thoughts.back') }}
    </a>

    <p class="reveal mt-8 text-xs font-medium text-muted">
        {{ $thought->published_at?->locale(app()->getLocale())->translatedFormat('d F Y') }}
        · {{ __('site.thoughts.min_read', ['n' => $thought->readingMinutes()]) }}
    </p>
    <h1 class="display reveal mt-3 text-[3.4rem] text-ink sm:text-[6rem]" style="--d: 60ms">{{ $thought->t('title') }}</h1>

    @if($thought->t('excerpt'))
        <p class="reveal mt-6 text-xl leading-relaxed text-muted" style="--d: 100ms">{{ $thought->t('excerpt') }}</p>
    @endif

    @if($cover)
        <img src="{{ $cover }}" alt="" class="reveal mt-10 w-full rounded-[2rem] object-cover" style="--d: 140ms">
    @endif

    <div class="prose-site mt-10">
        {!! \Illuminate\Support\Str::markdown((string) $thought->t('body'), ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
    </div>

    <div class="mt-12 border-t border-line pt-6">
        @include('partials.share', ['title' => $thought->t('title')])
    </div>
</article>

@if($more->isNotEmpty())
    <section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
        <h2 class="display text-5xl text-ink sm:text-6xl">{{ __('site.thoughts.more') }}</h2>
        <div class="mt-8 grid gap-8 md:grid-cols-3">
            @foreach($more as $item)
                @include('partials.thought-card', ['thought' => $item, 'delay' => $loop->index * 80])
            @endforeach
        </div>
    </section>
@endif

@include('partials.cta-band')
@endsection
