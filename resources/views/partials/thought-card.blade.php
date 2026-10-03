{{-- Article card. $thought, optional $delay. --}}
@php $cover = $thought->coverUrl(true); @endphp
<a href="{{ route('thoughts.show', $thought) }}" class="reveal group block" style="--d: {{ $delay ?? 0 }}ms">
    <div class="relative aspect-[16/10] overflow-hidden rounded-3xl bg-soft">
        @if($cover)
            <img src="{{ $cover }}" alt="" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-100 via-brand-50 to-white text-brand-300">
                <x-icon name="pen" class="h-14 w-14" stroke="1.2" />
            </div>
        @endif
    </div>
    <p class="mt-4 text-xs font-medium text-muted">
        {{ $thought->published_at?->locale(app()->getLocale())->translatedFormat('d F Y') }}
        · {{ __('site.thoughts.min_read', ['n' => $thought->readingMinutes()]) }}
    </p>
    <h3 class="display mt-1.5 text-3xl text-ink transition-colors duration-200 group-hover:text-brand-500">{{ $thought->t('title') }}</h3>
    @if($thought->t('excerpt'))
        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-muted">{{ $thought->t('excerpt') }}</p>
    @endif
</a>
