{{-- Home page reviews: the words in the middle ("what they say") with the review boxes (chat screenshots) placed all
     around them. No slider: every box is visible. Needs $testimonials. --}}
@php
    use App\Models\Setting;

    $reviews = $testimonials->take(6)->values();
    $label = __('site.reviews.around_title');
    $headline = Setting::t('reviews_headline', __('site.reviews.headline'));
    $by = Setting::t('reviews_by', __('site.reviews.headline_by'));

    // Wide screens: where each box sits around the words, with a little tilt so it feels scattered, not lined up
    $scatter = [
        'left-[9%] top-0 w-52 -rotate-3',
        'right-[8%] top-2 w-56 rotate-3',
        'left-[6%] bottom-0 w-56 rotate-3',
        'right-[14%] bottom-0 w-52 -rotate-3',
        'left-0 top-[38%] w-52 rotate-2',
        'right-0 top-[36%] w-56 -rotate-2',
    ];
@endphp
@if($reviews->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="relative lg:flex lg:min-h-[40rem] lg:items-center lg:justify-center">
        {{-- The words in the middle --}}
        <div class="relative z-10 mx-auto max-w-2xl text-center">
            <span class="reveal inline-flex rounded-full border border-ink/25 px-4 py-1.5 text-[0.7rem] font-semibold uppercase tracking-[0.14em] text-ink">{{ $label }}</span>
            <p class="reveal mt-8 text-balance text-3xl font-light leading-[1.25] tracking-tight text-ink sm:text-[2.75rem]" style="--d: 60ms">{{ $headline }}</p>
            @if($by)
                <p class="reveal mt-6 text-sm text-muted" style="--d: 120ms">&mdash; {{ $by }}</p>
            @endif
            <a href="{{ route('reviews') }}" class="btn-ghost reveal mt-8" style="--d: 160ms">{{ __('site.reviews.all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
        </div>

        {{-- Phone and tablet: the boxes in a grid under the words --}}
        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:hidden">
            @foreach($reviews as $item)
                <div class="reveal" style="--d: {{ ($loop->index % 2) * 80 }}ms">@include('partials.review-box', ['item' => $item])</div>
            @endforeach
        </div>

        {{-- Wide screens: all around the words --}}
        @foreach($reviews as $item)
            <div class="reveal absolute z-0 hidden lg:block {{ $scatter[$loop->index] }}" style="--d: {{ $loop->index * 80 }}ms">@include('partials.review-box', ['item' => $item, 'compact' => true])</div>
        @endforeach
    </div>
</section>
@endif
