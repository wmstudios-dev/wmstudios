{{-- A review shown as a chat. $item (Testimonial). With a screenshot it is shown in a phone frame; without one the
     chat is drawn from the review text (and our reply, when there is one). Optional $compact for smaller cards. --}}
@php
    $compact = $compact ?? false;
    $image = $item->chatImageUrl();
    $reply = $item->t('reply');
    $channel = $item->channel ?: 'WhatsApp';
    $initial = strtoupper(mb_substr($item->name, 0, 1));
@endphp
@if($image)
    <div class="mx-auto overflow-hidden rounded-[2rem] border-[6px] border-ink bg-ink shadow-xl {{ $compact ? 'max-w-[15rem]' : 'max-w-[19rem]' }}">
        <img src="{{ $image }}" alt="{{ __('site.reviews.chat_alt', ['name' => $item->name]) }}" loading="lazy" class="max-h-[32rem] w-full rounded-[1.4rem] bg-white object-cover object-top">
    </div>
@else
    <div class="mx-auto overflow-hidden rounded-[2rem] border border-line bg-[#efeae2] shadow-lg {{ $compact ? 'max-w-sm' : 'max-w-md' }}">
        <div class="flex items-center gap-3 bg-[#075e54] px-4 py-3 text-white">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20 text-sm font-bold">{{ $initial }}</span>
            <span class="min-w-0 leading-tight">
                <span class="block truncate text-sm font-semibold">{{ $item->name }}</span>
                <span class="block text-[0.7rem] text-white/70">{{ $channel }}</span>
            </span>
        </div>
        <div class="space-y-2.5 px-4 py-5 {{ $compact ? 'text-[0.82rem]' : 'text-sm' }}">
            <p class="max-w-[88%] rounded-2xl rounded-tl-sm bg-white px-3.5 py-2.5 leading-relaxed text-ink shadow-sm">
                {{ $item->t('quote') }}
                <span class="mt-1 block text-right text-[0.65rem] text-muted">&#10003;&#10003;</span>
            </p>
            @if($reply)
                <p class="ml-auto max-w-[80%] rounded-2xl rounded-tr-sm bg-[#d9fdd3] px-3.5 py-2.5 leading-relaxed text-ink shadow-sm">{{ $reply }}</p>
            @endif
        </div>
    </div>
@endif
