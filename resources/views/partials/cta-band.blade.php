{{-- Big blue call-to-action block with photos floating on the right. Optional $title and $text. --}}
@php
    $wa = \App\Support\WhatsApp::chatUrl();
    $pics = \App\Models\SpaceItem::active()->orderBy('sort_order')->orderByDesc('id')->take(4)->get()->map(fn ($i) => $i->photoUrl(true));
    if ($pics->filter()->count() < 4) {
        $pics = $pics->concat(\App\Models\Work::active()->ordered()->take(4)->get()->map(fn ($w) => $w->coverUrl(true)));
    }
    $pics = $pics->filter()->unique()->take(4)->values();
    $floats = [
        'right-10 top-8 w-40 aspect-[3/4] rounded-3xl rotate-6',
        'right-56 top-24 w-32 aspect-square rounded-full -rotate-6',
        'right-14 bottom-8 w-44 aspect-[4/3] rounded-3xl -rotate-3',
        'right-72 bottom-6 w-24 aspect-square rounded-full rotate-3',
    ];
@endphp
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <div class="reveal relative overflow-hidden rounded-[2rem] bg-brand-500 px-7 py-14 text-white sm:px-14 sm:py-20">
        <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-brand-400/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-brand-700/60 blur-3xl"></div>

        @foreach($pics as $pic)
            <span class="absolute hidden overflow-hidden ring-4 ring-white/25 shadow-xl shadow-ink/20 transition-transform duration-500 hover:scale-110 hover:rotate-0 xl:block {{ $floats[$loop->index] }}">
                <img src="{{ $pic }}" alt="" loading="lazy" class="h-full w-full object-cover">
            </span>
        @endforeach

        <div class="relative max-w-3xl xl:max-w-xl">
            <h2 class="display text-5xl sm:text-7xl">{{ $title ?? __('site.home.cta_title') }}</h2>
            <p class="mt-5 max-w-xl text-lg text-white/80">{{ $text ?? __('site.home.cta_text') }}</p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('contact') }}" class="btn-light">{{ __('site.hero.cta_primary') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                @if($wa)
                    <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn border border-white/30 text-white hover:bg-white/10"><x-icon name="whatsapp" class="h-4 w-4" /> WhatsApp</a>
                @endif
            </div>
        </div>
    </div>
</section>
