@extends('layouts.app')

@section('title', __('site.reviews.title'))
@section('meta_description', __('site.reviews.subtitle'))

@section('content')
@include('partials.page-header', ['eyebrow' => __('site.home.testimonials_eyebrow'), 'title' => __('site.reviews.title'), 'subtitle' => __('site.reviews.subtitle')])

<section class="mx-auto max-w-7xl px-5 sm:px-8">
    @if($reviews->isNotEmpty())
        {{-- Real numbers only: the average and the count come from the reviews below --}}
        @if($showSummary)
        <div class="reveal flex flex-wrap items-center gap-x-10 gap-y-4 rounded-[2rem] bg-soft px-7 py-6 sm:px-10">
            <div class="flex items-center gap-4">
                <span class="display text-6xl text-ink">{{ number_format($average, 1, ',', '.') }}</span>
                <span>
                    <span class="block text-xl tracking-[0.2em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => round($average)]) }}">{{ str_repeat('★', (int) round($average)) }}<span class="text-ink/15">{{ str_repeat('★', 5 - (int) round($average)) }}</span></span>
                    <span class="block text-sm text-muted">{{ __('site.reviews.count', ['n' => $reviews->count()]) }}</span>
                </span>
            </div>
            <p class="max-w-md text-sm text-muted">{{ __('site.reviews.note') }}</p>
        </div>
        @endif

        <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($reviews as $item)
                <article class="reveal flex flex-col rounded-[2rem] bg-soft p-6" style="--d: {{ ($loop->index % 3) * 70 }}ms">
                    <span class="text-lg tracking-[0.2em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => $item->rating]) }}">{{ str_repeat('★', $item->rating) }}<span class="text-ink/15">{{ str_repeat('★', 5 - $item->rating) }}</span></span>
                    @if($item->chatImageUrl())
                        <div class="mt-4 flex-1">@include('partials.review-box', ['item' => $item])</div>
                    @else
                        <blockquote class="mt-4 flex-1 leading-relaxed text-ink">&ldquo;{{ $item->t('quote') }}&rdquo;</blockquote>
                    @endif
                    <div class="mt-5 flex items-center gap-3">
                        @if($item->photoUrl())
                            <img src="{{ $item->photoUrl() }}" alt="" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-600">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                        @endif
                        <span>
                            <span class="block text-sm font-bold text-ink">{{ $item->name }}</span>
                            @if($item->role)<span class="block text-xs text-muted">{{ $item->role }}</span>@endif
                        </span>
                        @if($item->logoUrl())<img src="{{ $item->logoUrl() }}" alt="" class="ml-auto h-8 max-w-[6rem] shrink-0 object-contain opacity-80">@endif
                    </div>
                    @if($item->channel)
                        <p class="mt-4 inline-flex items-center gap-1.5 self-start rounded-full bg-white px-3 py-1 text-xs font-semibold text-ink/70"><x-icon name="whatsapp" class="h-3.5 w-3.5 text-[#128c7e]" /> {{ __('site.reviews.via', ['channel' => $item->channel]) }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @else
        <p class="rounded-[2rem] bg-soft px-7 py-12 text-center text-muted">{{ __('site.reviews.empty') }}</p>
    @endif
</section>

{{-- Invite a new review: sent through WhatsApp, so no review is published without being seen --}}
@php $reviewWa = \App\Support\WhatsApp::chatUrl(__('site.reviews.wa_message')); @endphp
@include('partials.cta-band', ['title' => __('site.reviews.cta_title'), 'text' => __('site.reviews.cta_text')])
@if($reviewWa)
    <p class="mx-auto mt-6 max-w-7xl px-5 text-center text-sm text-muted sm:px-8">
        <a href="{{ $reviewWa }}" target="_blank" rel="noopener" class="font-semibold text-brand-600 underline underline-offset-4">{{ __('site.reviews.send') }}</a>
    </p>
@endif
@endsection
