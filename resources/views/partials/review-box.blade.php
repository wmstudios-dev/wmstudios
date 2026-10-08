{{-- One review as a small box: a screenshot of the real chat when there is one, otherwise a chat bubble drawn from the text.
     $item (Testimonial). --}}
@php
    $compact = $compact ?? false;
    $image = $item->chatImageUrl();
    $channel = $item->channel ?: 'WhatsApp';
@endphp
@if($image)
    <a href="{{ $image }}" data-lightbox="reviews" data-caption="{{ $item->name }}" class="block overflow-hidden rounded-2xl border border-line bg-white shadow-lg shadow-ink/10 transition-transform duration-300 hover:scale-[1.03]">
        <img src="{{ $image }}" alt="{{ __('site.reviews.chat_alt', ['name' => $item->name]) }}" loading="lazy" class="w-full object-cover object-top {{ $compact ? 'max-h-44' : 'max-h-[22rem]' }}">
    </a>
@else
    <div class="overflow-hidden rounded-2xl border border-line bg-[#efeae2] shadow-lg shadow-ink/10 transition-transform duration-300 hover:scale-[1.03]">
        <div class="flex items-center gap-2.5 bg-[#075e54] px-3.5 py-2.5 text-white">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/20 text-xs font-bold">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
            <span class="min-w-0 leading-tight">
                <span class="block truncate text-[0.8rem] font-semibold">{{ $item->name }}</span>
                <span class="block text-[0.65rem] text-white/70">{{ $channel }}</span>
            </span>
        </div>
        <div class="px-3.5 py-4">
            <p class="rounded-xl rounded-tl-sm bg-white px-3 py-2.5 text-[0.82rem] leading-relaxed text-ink shadow-sm">
                <span @class(['block', 'line-clamp-4' => $compact])>{{ $item->t('quote') }}</span>
                <span class="mt-1.5 flex items-center justify-between text-[0.65rem] text-muted">
                    <span class="tracking-[0.15em] text-amber-400" role="img" aria-label="{{ __('site.reviews.stars', ['n' => $item->rating]) }}">{{ str_repeat('★', $item->rating) }}</span>
                    <span>&#10003;&#10003;</span>
                </span>
            </p>
        </div>
    </div>
@endif
