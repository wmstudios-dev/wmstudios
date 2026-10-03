{{-- Page title block. $eyebrow (optional), $title, $subtitle (optional). --}}
<section class="mx-auto max-w-7xl px-5 pb-10 pt-14 sm:px-8 sm:pt-20">
    @isset($eyebrow)<p class="eyebrow reveal">{{ $eyebrow }}</p>@endisset
    <h1 class="display reveal mt-3 text-[4.5rem] text-ink sm:text-[8rem]" style="--d: 60ms">{{ $title }}</h1>
    @isset($subtitle)
        <p class="reveal mt-5 max-w-2xl text-lg leading-relaxed text-muted" style="--d: 120ms">{{ $subtitle }}</p>
    @endisset
</section>
