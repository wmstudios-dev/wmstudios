{{-- Home page reviews: a heading in the middle with the review boxes (chat screenshots) placed around it. No slider:
     every box is visible. Needs $testimonials. --}}
@php
    $reviews = $testimonials->take(6)->values();
    $left = $reviews->filter(fn ($r, $i) => $i % 2 === 0)->values();
    $right = $reviews->filter(fn ($r, $i) => $i % 2 === 1)->values();
    // a little stagger and tilt so the boxes feel scattered, not lined up
    $leftStyle = ['self-start -rotate-3', 'self-end rotate-2', 'self-start -rotate-1'];
    $rightStyle = ['self-end rotate-3', 'self-start -rotate-2', 'self-end rotate-1'];
@endphp
@if($reviews->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="grid items-center gap-10 lg:grid-cols-[1fr_minmax(0,26rem)_1fr] lg:gap-8">
        {{-- Heading, in the middle on wide screens --}}
        <div class="text-center lg:order-2">
            <span class="reveal inline-flex rounded-full border border-ink/25 px-4 py-1.5 text-[0.7rem] font-semibold uppercase tracking-[0.14em] text-ink">{{ __('site.home.testimonials_eyebrow') }}</span>
            <h2 class="display reveal mt-6 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.reviews.around_title') }}</h2>
            <a href="{{ route('reviews') }}" class="btn-dark reveal mt-8" style="--d: 120ms">{{ __('site.reviews.all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
        </div>

        {{-- Phone and tablet: all boxes in a grid under the heading --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:hidden">
            @foreach($reviews as $item)
                <div class="reveal" style="--d: {{ ($loop->index % 2) * 80 }}ms">@include('partials.review-box', ['item' => $item])</div>
            @endforeach
        </div>

        {{-- Wide screens: left and right of the heading --}}
        <div class="hidden flex-col gap-8 lg:order-1 lg:flex">
            @foreach($left as $item)
                <div class="reveal w-full max-w-[17rem] {{ $leftStyle[$loop->index % 3] }}" style="--d: {{ $loop->index * 90 }}ms">@include('partials.review-box', ['item' => $item])</div>
            @endforeach
        </div>
        <div class="hidden flex-col gap-8 lg:order-3 lg:flex">
            @foreach($right as $item)
                <div class="reveal w-full max-w-[17rem] {{ $rightStyle[$loop->index % 3] }}" style="--d: {{ $loop->index * 90 + 45 }}ms">@include('partials.review-box', ['item' => $item])</div>
            @endforeach
        </div>
    </div>
</section>
@endif
