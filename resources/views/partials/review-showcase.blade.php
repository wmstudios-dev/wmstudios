{{-- Home page reviews. Two tabs: the chats (author card on the left, the conversation on the right, with a timer bar) and
     the same reviews as plain text. Needs $testimonials. --}}
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
        {{-- Tabs --}}
        <div class="mx-auto flex max-w-md justify-center gap-1 border-b border-line" role="tablist">
            <button type="button" role="tab" data-review-tab="chat" data-active="true" class="relative px-6 py-3 text-xs font-bold uppercase tracking-[0.14em] text-ink/40 transition-colors hover:text-ink data-[active=true]:text-ink after:absolute after:inset-x-0 after:-bottom-px after:h-0.5 after:scale-x-0 after:bg-ink after:transition-transform data-[active=true]:after:scale-x-100">{{ __('site.reviews.tab_chat') }}</button>
            <button type="button" role="tab" data-review-tab="text" data-active="false" class="relative px-6 py-3 text-xs font-bold uppercase tracking-[0.14em] text-ink/40 transition-colors hover:text-ink data-[active=true]:text-ink after:absolute after:inset-x-0 after:-bottom-px after:h-0.5 after:scale-x-0 after:bg-ink after:transition-transform data-[active=true]:after:scale-x-100">{{ __('site.reviews.tab_text') }}</button>
        </div>

        {{-- Chat tab --}}
        <div data-review-panel="chat" class="mt-8">
            <div class="grid gap-5 lg:grid-cols-12">
                {{-- Author card --}}
                <div class="reveal rounded-[2rem] bg-soft p-7 sm:p-9 lg:col-span-5">
                    @foreach($reviews as $item)
                        <figure data-review-card="{{ $loop->index }}" @class(['flex h-full flex-col', 'hidden' => ! $loop->first])>
                            <span class="text-xl tracking-[0.2em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => $item->rating]) }}">{{ str_repeat('★', $item->rating) }}<span class="text-ink/15">{{ str_repeat('★', 5 - $item->rating) }}</span></span>
                            <blockquote class="mt-5 flex-1 text-lg leading-relaxed text-ink sm:text-xl">&ldquo;{{ $item->t('quote') }}&rdquo;</blockquote>
                            <figcaption class="mt-8 flex items-center gap-3">
                                @if($item->photoUrl())
                                    <img src="{{ $item->photoUrl() }}" alt="" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-500 text-base font-bold text-white">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                                @endif
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-bold text-ink">{{ $item->name }}</span>
                                    @if($item->role)<span class="block text-xs text-muted">{{ $item->role }}</span>@endif
                                </span>
                                @if($item->logoUrl())
                                    <img src="{{ $item->logoUrl() }}" alt="" class="h-9 max-w-[7rem] shrink-0 object-contain opacity-80">
                                @endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>

                {{-- The conversation, on a blue stage --}}
                <div class="reveal relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-500 via-brand-500 to-brand-700 px-4 py-10 sm:px-8 sm:py-12 lg:col-span-7" style="--d: 80ms">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-20 -left-10 h-64 w-64 rounded-full bg-lime/20 blur-3xl"></div>

                    @foreach($reviews as $item)
                        <span data-review-chip="{{ $loop->index }}" @class(['absolute left-5 top-5 hidden items-center gap-2 rounded-full bg-white/95 px-3.5 py-1.5 text-xs font-bold text-ink shadow-lg', 'sm:inline-flex' => $loop->first])>
                            <x-icon name="whatsapp" class="h-3.5 w-3.5 text-[#128c7e]" /> {{ $item->channel ?: 'WhatsApp' }}
                        </span>
                    @endforeach

                    <div class="relative">
                        @foreach($reviews as $item)
                            <div data-review-chat="{{ $loop->index }}" @class(['hidden' => ! $loop->first])>
                                @include('partials.chat-review', ['item' => $item])
                            </div>
                        @endforeach
                    </div>

                    @if($reviews->count() > 1)
                        <button type="button" data-review-prev aria-label="{{ __('site.reviews.prev') }}" class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-ink shadow-lg transition-transform hover:scale-110 sm:left-5"><x-icon name="arrow" class="h-4 w-4 rotate-180" /></button>
                        <button type="button" data-review-next aria-label="{{ __('site.reviews.next') }}" class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white text-ink shadow-lg transition-transform hover:scale-110 sm:right-5"><x-icon name="arrow" class="h-4 w-4" /></button>
                    @endif
                </div>
            </div>

            {{-- Timer bar: one segment per review --}}
            @if($reviews->count() > 1)
                <div class="mt-6 grid gap-2" style="grid-template-columns: repeat({{ $reviews->count() }}, minmax(0, 1fr))">
                    @foreach($reviews as $item)
                        <button type="button" data-review-go="{{ $loop->index }}" aria-label="{{ $item->name }}" class="group py-2">
                            <span class="block h-0.5 overflow-hidden rounded-full bg-ink/15 transition-colors group-hover:bg-ink/30">
                                <span class="review-bar-fill block h-full w-full bg-ink" data-state="off"></span>
                            </span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Text tab --}}
        <div data-review-panel="text" class="mt-8 hidden">
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($reviews as $item)
                    <figure class="flex flex-col rounded-[2rem] bg-soft p-7">
                        <span class="text-lg tracking-[0.2em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => $item->rating]) }}">{{ str_repeat('★', $item->rating) }}<span class="text-ink/15">{{ str_repeat('★', 5 - $item->rating) }}</span></span>
                        <blockquote class="mt-4 flex-1 leading-relaxed text-ink">&ldquo;{{ $item->t('quote') }}&rdquo;</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3">
                            @if($item->photoUrl())
                                <img src="{{ $item->photoUrl() }}" alt="" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 text-sm font-bold text-white">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-bold text-ink">{{ $item->name }}</span>
                                @if($item->role)<span class="block text-xs text-muted">{{ $item->role }}</span>@endif
                            </span>
                            @if($item->logoUrl())
                                <img src="{{ $item->logoUrl() }}" alt="" class="h-8 max-w-[6rem] shrink-0 object-contain opacity-80">
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        (function () {
            var root = document.currentScript.previousElementSibling;
            if (!root || !root.hasAttribute('data-review-show')) return;
            var cards = root.querySelectorAll('[data-review-card]'), chats = root.querySelectorAll('[data-review-chat]'),
                chips = root.querySelectorAll('[data-review-chip]'), segs = root.querySelectorAll('.review-bar-fill');
            var n = cards.length, i = 0, timer = null, DURATION = 9000, auto = n > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            function show(k) {
                i = (k + n) % n;
                cards.forEach(function (el, j) { el.classList.toggle('hidden', j !== i); });
                chats.forEach(function (el, j) { el.classList.toggle('hidden', j !== i); });
                chips.forEach(function (el, j) { el.classList.toggle('sm:inline-flex', j === i); });
                segs.forEach(function (el, j) {
                    el.dataset.state = j < i ? 'done' : (j === i ? (auto ? 'on' : 'done') : 'off');
                    if (j === i && auto) { el.style.animation = 'none'; void el.offsetWidth; el.style.animation = ''; }
                });
            }
            function schedule() { clearTimeout(timer); if (auto) timer = setTimeout(function () { show(i + 1); schedule(); }, DURATION); }
            function stop() { auto = false; clearTimeout(timer); segs.forEach(function (el, j) { if (j === i) el.dataset.state = 'done'; }); }
            root.addEventListener('click', function (e) {
                var t = e.target.closest('[data-review-tab]');
                if (t) {
                    var which = t.dataset.reviewTab;
                    root.querySelectorAll('[data-review-tab]').forEach(function (b) { b.dataset.active = String(b === t); });
                    root.querySelectorAll('[data-review-panel]').forEach(function (p) { p.classList.toggle('hidden', p.dataset.reviewPanel !== which); });
                    if (which === 'text') stop(); else if (n > 1) { auto = !window.matchMedia('(prefers-reduced-motion: reduce)').matches; show(i); schedule(); }
                    return;
                }
                var g = e.target.closest('[data-review-go]');
                if (g) { stop(); show(Number(g.dataset.reviewGo)); }
                if (e.target.closest('[data-review-prev]')) { stop(); show(i - 1); }
                if (e.target.closest('[data-review-next]')) { stop(); show(i + 1); }
            });
            show(0);
            schedule();
            root.addEventListener('mouseenter', function () { if (auto) { stop(); } });
        })();
    </script>
</section>
@endif
