{{-- Home page reviews as text: one big quote at a time, with a timer bar and arrows. Needs $testimonials. --}}
@php $reviews = $testimonials->take(6)->values(); @endphp
@if($reviews->isNotEmpty())
<section class="mx-auto mt-28 max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow reveal">{{ __('site.home.testimonials_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.home.testimonials_title') }}</h2>
        </div>
        <a href="{{ route('reviews') }}" class="btn-ghost reveal">{{ __('site.reviews.all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
    </div>

    <div data-review-show class="mt-10">
        <div class="reveal relative overflow-hidden rounded-[2rem] bg-soft px-6 py-10 sm:px-12 sm:py-14">
            <span class="display pointer-events-none absolute right-8 top-2 select-none text-[12rem] leading-none text-brand-500/10 sm:text-[16rem]" aria-hidden="true">&rdquo;</span>

            <div class="relative grid">
                @foreach($reviews as $item)
                    <figure data-review-card="{{ $loop->index }}" data-on="{{ $loop->first ? 'true' : 'false' }}" class="col-start-1 row-start-1 flex flex-col transition-opacity duration-500 data-[on=false]:invisible data-[on=false]:opacity-0">
                        <span class="text-xl tracking-[0.2em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => $item->rating]) }}">{{ str_repeat('★', $item->rating) }}<span class="text-ink/15">{{ str_repeat('★', 5 - $item->rating) }}</span></span>
                        <blockquote class="mt-6 max-w-4xl text-2xl font-medium leading-snug tracking-tight text-ink sm:text-4xl">&ldquo;{{ $item->t('quote') }}&rdquo;</blockquote>
                        <figcaption class="mt-9 flex flex-wrap items-center gap-4">
                            @if($item->photoUrl())
                                <img src="{{ $item->photoUrl() }}" alt="" class="h-14 w-14 rounded-full object-cover">
                            @else
                                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-500 text-lg font-bold text-white">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                            @endif
                            <span class="min-w-0">
                                <span class="block font-bold text-ink">{{ $item->name }}</span>
                                @if($item->role)<span class="block text-sm text-muted">{{ $item->role }}</span>@endif
                            </span>
                            @if($item->logoUrl())
                                <img src="{{ $item->logoUrl() }}" alt="" class="h-10 max-w-[8rem] shrink-0 object-contain opacity-80">
                            @endif
                            @if($item->channel)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-ink/70 sm:ml-auto">
                                    <x-icon name="whatsapp" class="h-3.5 w-3.5 text-[#128c7e]" /> {{ __('site.reviews.via', ['channel' => $item->channel]) }}
                                </span>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>

        @if($reviews->count() > 1)
            <div class="mt-6 flex items-center gap-4">
                <div class="grid flex-1 gap-2" style="grid-template-columns: repeat({{ $reviews->count() }}, minmax(0, 1fr))">
                    @foreach($reviews as $item)
                        <button type="button" data-review-go="{{ $loop->index }}" aria-label="{{ $item->name }}" class="group py-2">
                            <span class="block h-0.5 overflow-hidden rounded-full bg-ink/15 transition-colors group-hover:bg-ink/30">
                                <span class="review-bar-fill block h-full w-full bg-ink" data-state="off"></span>
                            </span>
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <button type="button" data-review-prev aria-label="{{ __('site.reviews.prev') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition-colors hover:border-brand-500 hover:text-brand-600"><x-icon name="arrow" class="h-4 w-4 rotate-180" /></button>
                    <button type="button" data-review-next aria-label="{{ __('site.reviews.next') }}" class="flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink transition-colors hover:border-brand-500 hover:text-brand-600"><x-icon name="arrow" class="h-4 w-4" /></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        (function () {
            var root = document.currentScript.previousElementSibling;
            if (!root || !root.hasAttribute('data-review-show')) return;
            var cards = root.querySelectorAll('[data-review-card]'), segs = root.querySelectorAll('.review-bar-fill');
            var n = cards.length, i = 0, timer = null, DURATION = 9000, auto = n > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            function show(k) {
                i = (k + n) % n;
                cards.forEach(function (el, j) { el.dataset.on = String(j === i); });
                segs.forEach(function (el, j) {
                    el.dataset.state = j < i ? 'done' : (j === i ? (auto ? 'on' : 'done') : 'off');
                    if (j === i && auto) { el.style.animation = 'none'; void el.offsetWidth; el.style.animation = ''; }
                });
            }
            function schedule() { clearTimeout(timer); if (auto) timer = setTimeout(function () { show(i + 1); schedule(); }, DURATION); }
            function stop() { auto = false; clearTimeout(timer); segs.forEach(function (el, j) { if (j === i) el.dataset.state = 'done'; }); }
            root.addEventListener('click', function (e) {
                var g = e.target.closest('[data-review-go]');
                if (g) { stop(); show(Number(g.dataset.reviewGo)); }
                if (e.target.closest('[data-review-prev]')) { stop(); show(i - 1); }
                if (e.target.closest('[data-review-next]')) { stop(); show(i + 1); }
            });
            show(0);
            schedule();
            root.addEventListener('mouseenter', function () { if (auto) stop(); });
        })();
    </script>
</section>
@endif
