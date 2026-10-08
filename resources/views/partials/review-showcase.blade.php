{{-- Home page reviews: the author card on the left, the chat itself on the right, switch with arrows or the bar. Needs $testimonials. --}}
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
        <div class="grid gap-5 lg:grid-cols-12">
            {{-- Author card --}}
            <div class="reveal rounded-[2rem] bg-soft p-7 sm:p-9 lg:col-span-5">
                @foreach($reviews as $item)
                    @php $hasShot = (bool) $item->chatImageUrl(); @endphp
                    <figure data-review-card="{{ $loop->index }}" @class(['flex h-full flex-col', 'hidden' => ! $loop->first])>
                        <span class="text-xl tracking-[0.2em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => $item->rating]) }}">{{ str_repeat('★', $item->rating) }}<span class="text-ink/15">{{ str_repeat('★', 5 - $item->rating) }}</span></span>
                        <blockquote class="mt-5 flex-1 text-xl leading-relaxed text-ink sm:text-2xl">
                            @if($hasShot)
                                &ldquo;{{ $item->t('quote') }}&rdquo;
                            @else
                                {{ __('site.reviews.lead', ['channel' => $item->channel ?: 'WhatsApp']) }}
                            @endif
                        </blockquote>
                        <figcaption class="mt-8 flex items-center gap-3">
                            @if($item->photoUrl())
                                <img src="{{ $item->photoUrl() }}" alt="" class="h-12 w-12 rounded-full object-cover">
                            @else
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-base font-bold text-brand-600">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                            @endif
                            <span>
                                <span class="block text-sm font-bold text-ink">{{ $item->name }}</span>
                                @if($item->role)<span class="block text-xs text-muted">{{ $item->role }}</span>@endif
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>

            {{-- The chat --}}
            <div class="reveal rounded-[2rem] bg-gradient-to-br from-brand-100 via-lilac/50 to-brand-50 px-4 py-8 sm:px-8 sm:py-10 lg:col-span-7" style="--d: 80ms">
                @foreach($reviews as $item)
                    <div data-review-chat="{{ $loop->index }}" @class(['hidden' => ! $loop->first])>
                        @include('partials.chat-review', ['item' => $item])
                    </div>
                @endforeach
            </div>
        </div>

        @if($reviews->count() > 1)
            <div class="mt-6 flex items-center gap-4">
                <div class="grid flex-1 gap-1.5" style="grid-template-columns: repeat({{ $reviews->count() }}, minmax(0, 1fr))">
                    @foreach($reviews as $item)
                        <button type="button" data-review-go="{{ $loop->index }}" aria-label="{{ $item->name }}" class="group py-2">
                            <span class="block h-0.5 rounded-full bg-ink/15 transition-colors group-hover:bg-ink/40 group-data-[on=true]:bg-ink" data-bar></span>
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <button type="button" data-review-prev aria-label="{{ __('site.reviews.prev') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink transition-colors hover:border-brand-500 hover:text-brand-600"><x-icon name="arrow" class="h-4 w-4 rotate-180" /></button>
                    <button type="button" data-review-next aria-label="{{ __('site.reviews.next') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink transition-colors hover:border-brand-500 hover:text-brand-600"><x-icon name="arrow" class="h-4 w-4" /></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        (function () {
            var root = document.currentScript.previousElementSibling;
            if (!root || !root.hasAttribute('data-review-show')) return;
            var cards = root.querySelectorAll('[data-review-card]'), chats = root.querySelectorAll('[data-review-chat]'), goes = root.querySelectorAll('[data-review-go]');
            var n = cards.length, i = 0, timer = null;
            function show(k) {
                i = (k + n) % n;
                cards.forEach(function (el, j) { el.classList.toggle('hidden', j !== i); });
                chats.forEach(function (el, j) { el.classList.toggle('hidden', j !== i); });
                goes.forEach(function (el, j) { el.dataset.on = String(j <= i); });
            }
            function stop() { if (timer) { clearInterval(timer); timer = null; } }
            root.addEventListener('click', function (e) {
                var g = e.target.closest('[data-review-go]');
                if (g) { stop(); show(Number(g.dataset.reviewGo)); }
                if (e.target.closest('[data-review-prev]')) { stop(); show(i - 1); }
                if (e.target.closest('[data-review-next]')) { stop(); show(i + 1); }
            });
            show(0);
            if (n > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                timer = setInterval(function () { show(i + 1); }, 9000);
                root.addEventListener('mouseenter', stop);
            }
        })();
    </script>
</section>
@endif
