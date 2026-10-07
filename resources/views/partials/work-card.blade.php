{{-- Portfolio card. $work, optional $ratio (aspect class) and $delay (reveal delay in ms). --}}
@php
    $icon = ['photo' => 'camera', 'video' => 'film', 'documentation' => 'film', 'design' => 'palette', 'web' => 'code', 'social' => 'megaphone'][$work->category] ?? 'spark';
    $cover = $work->coverUrl(true);
    $isVideo = (bool) $work->video();
@endphp
<a href="{{ route('works.show', $work) }}"
   class="reveal group relative block overflow-hidden rounded-3xl bg-soft transition-transform duration-200 active:scale-[0.98] {{ $ratio ?? 'aspect-[4/5]' }}" style="--d: {{ $delay ?? 0 }}ms">
    @if($cover)
        <img src="{{ $cover }}" alt="{{ $work->t('title') }}" loading="lazy"
             style="object-position: {{ $work->coverPosition() }}"
             class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
    @else
        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-brand-100 via-brand-50 to-white text-brand-300">
            <x-icon :name="$icon" class="h-16 w-16" stroke="1.2" />
        </div>
    @endif

    <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/10 to-transparent opacity-90 transition-opacity duration-300 group-hover:opacity-100"></div>

    <span class="absolute left-4 top-4 flex flex-wrap gap-1.5">
        @foreach($work->categoryLabels() as $label)
            <span class="chip bg-white/90 text-ink backdrop-blur">{{ $label }}</span>
        @endforeach
    </span>

    @if($isVideo)
        <span class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-brand-600 transition-transform duration-300 group-hover:scale-110">
            <x-icon name="play" class="ml-0.5 h-4 w-4" />
        </span>
    @endif

    <div class="absolute inset-x-0 bottom-0 p-5 text-white">
        @if($work->metric_value)
            <span class="mb-2 inline-flex items-baseline gap-1.5 rounded-full bg-lime px-3 py-1 text-ink">
                <span class="display text-xl leading-none">{{ $work->metric_value }}</span>
                @if($metricLabel = $work->t('metric_label'))<span class="text-[0.7rem] font-semibold">{{ $metricLabel }}</span>@endif
            </span>
        @endif
        <h3 class="display text-3xl leading-[0.95]">{{ $work->t('title') }}</h3>
        <p class="mt-1.5 flex items-center gap-2 text-xs text-white/75">
            @if($work->client)<span>{{ $work->client }}</span>@endif
            @if($work->client && $work->year)<span aria-hidden="true">·</span>@endif
            @if($work->year)<span>{{ $work->year }}</span>@endif
            <x-icon name="arrow-up-right" class="ml-auto h-5 w-5 translate-y-1 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100" />
        </p>
    </div>
</a>
