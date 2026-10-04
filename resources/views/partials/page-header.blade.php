{{-- Blue page title block that also holds the header, like the home hero. $eyebrow (optional), $title, $subtitle (optional). --}}
<section data-hero class="relative -mt-16 mb-14 overflow-hidden rounded-bl-[3.5rem] rounded-br-[1.5rem] bg-gradient-to-tr from-brand-500 via-brand-500 to-brand-400 text-white sm:mb-20">
    <div class="pointer-events-none absolute -right-32 -top-32 h-[28rem] w-[28rem] rounded-full bg-white/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 left-1/4 h-[22rem] w-[22rem] rounded-full bg-brand-700/40 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-5 pb-12 pt-32 sm:px-8 sm:pb-16 sm:pt-40">
        @isset($eyebrow)<p class="reveal text-xs font-semibold uppercase tracking-[0.18em] text-white/70">{{ $eyebrow }}</p>@endisset
        <h1 class="display reveal mt-3 text-[4.5rem] text-white sm:text-[8rem]" style="--d: 60ms">{{ $title }}</h1>
        @isset($subtitle)
            <p class="reveal mt-5 max-w-2xl text-lg leading-relaxed text-white/85" style="--d: 120ms">{{ $subtitle }}</p>
        @endisset
    </div>
</section>
