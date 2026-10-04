@extends('layouts.app')

@section('title', $page['title'])
@section('meta_description', $page['subtitle'])

@section('content')
@include('partials.page-header', ['title' => $page['title'], 'subtitle' => $page['subtitle']])

<section class="mx-auto max-w-3xl px-5 sm:px-8">
    <p class="reveal text-xs font-semibold uppercase tracking-[0.18em] text-muted">{{ __('legal.updated', ['date' => $updated]) }}</p>

    <div class="mt-8 space-y-9">
        @foreach($page['sections'] as [$heading, $body])
            <div class="reveal">
                <h2 class="text-xl font-bold text-ink">{{ $loop->iteration }}. {{ $heading }}</h2>
                <p class="mt-2 leading-relaxed text-muted">{{ $body }}</p>
            </div>
        @endforeach
    </div>

    <p class="reveal mt-12 text-sm text-muted">
        <a href="{{ route('contact') }}" class="font-semibold text-brand-600 underline underline-offset-4">{{ __('site.nav.contact') }}</a>
    </p>
</section>
@endsection
